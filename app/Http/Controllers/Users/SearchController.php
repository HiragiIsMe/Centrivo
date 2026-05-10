<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Category;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = trim($request->get('q', ''));
        $categories = Category::all();

        if (empty($query)) {
            return redirect()->route('market');
        }

        $queryBuilder = Service::with(['seller.sellerProfile', 'category', 'images', 'reviews', 'activeAdvertisement', 'location'])
            ->where('status', 'active')
            ->where('is_banned', false);

        $userProfile = auth()->user()->userProfile;
        if ($userProfile && $userProfile->latitude && $userProfile->longitude) {
            $userLat = $userProfile->latitude;
            $userLng = $userProfile->longitude;
            $radius = 50; // 50 km

            $queryBuilder->whereHas('location', function ($q) use ($userLat, $userLng, $radius) {
                $q->whereRaw("
                    (6371 * acos(
                        cos(radians(?)) * cos(radians(latitude)) * 
                        cos(radians(longitude) - radians(?)) + 
                        sin(radians(?)) * sin(radians(latitude))
                    )) <= ?
                ", [$userLat, $userLng, $userLat, $radius]);
            });
        }

        $services = $queryBuilder->get();

        $queryTerms = $this->tokenize($query);

        // Jika tokenize kosong (misal query terlalu pendek atau hanya stopwords), 
        // fallback ke simple LIKE search (substring match).
        if (empty($queryTerms) && !empty($query)) {
            $rankedServices = $services->filter(function($svc) use ($query) {
                return stripos($svc->service_name, $query) !== false || stripos($svc->description, $query) !== false;
            })->map(function($svc) {
                $svc->relevance_score = 1;
                return $svc;
            });
            return view('market.search', [
                'services' => $rankedServices,
                'query' => $query,
                'categories' => $categories,
            ]);
        }

        $corpus = [];
        foreach ($services as $service) {
            $nameTokens = $this->tokenize($service->service_name);
            $descTokens = $this->tokenize($service->description);
            $corpus[$service->id] = [
                'name_tokens' => $nameTokens,
                'desc_tokens' => $descTokens,
                'all_tokens' => array_merge(
                    $nameTokens, $nameTokens, $nameTokens,
                    $descTokens
                ),
            ];
        }

        $totalDocs = count($corpus);

        $idf = [];
        foreach ($queryTerms as $term) {
            $docCount = 0;
            foreach ($corpus as $doc) {
                // Partial match inside TF-IDF for more robust search
                $found = false;
                foreach ($doc['all_tokens'] as $dt) {
                    if (str_contains($dt, $term)) {
                        $found = true;
                        break;
                    }
                }
                if ($found) $docCount++;
            }
            $idf[$term] = $docCount > 0 ? log(($totalDocs + 1) / ($docCount + 1)) + 1 : 0;
        }

        $scores = [];
        foreach ($corpus as $serviceId => $doc) {
            $score = 0;
            $totalTokens = count($doc['all_tokens']);
            if ($totalTokens === 0) continue;

            foreach ($queryTerms as $term) {
                $tf = 0;
                foreach ($doc['all_tokens'] as $dt) {
                    if (str_contains($dt, $term)) {
                        $tf++; // Partial match frequency
                    }
                }
                $tfNormalized = $tf / $totalTokens;

                $score += $tfNormalized * ($idf[$term] ?? 0);
            }

            $service = $services->firstWhere('id', $serviceId);
            $avgRating = $service->reviews->avg('rating') ?? 0;
            $ratingBonus = ($avgRating / 5) * 0.15 * $score;
            $adBoost = $service->activeAdvertisement ? 1000 : 0;

            $scores[$serviceId] = $score + $ratingBonus + $adBoost;
        }

        arsort($scores);
        $scores = array_filter($scores, fn($s) => $s > 0);

        // Jika algoritma TF-IDF gagal mendapat skor (0), fallback tambahkan substring match
        if (empty($scores)) {
            foreach ($services as $svc) {
                if (stripos($svc->service_name, $query) !== false || stripos($svc->description, $query) !== false) {
                    $scores[$svc->id] = 0.5; // low score
                }
            }
            arsort($scores);
        }

        $rankedServices = collect();
        foreach ($scores as $serviceId => $score) {
            $service = $services->firstWhere('id', $serviceId);
            if ($service) {
                $service->relevance_score = round($score, 6);
                $rankedServices->push($service);
            }
        }

        return view('market.search', [
            'services' => $rankedServices,
            'query' => $query,
            'categories' => $categories,
        ]);
    }

    /**
     * Tokenize a string into lowercase words, removing stopwords and short words.
     */
    private function tokenize(string $text): array
    {
        $text = mb_strtolower($text);
        $text = preg_replace('/[^\w\s]/u', ' ', $text);
        $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);

        $stopwords = [
            'dan', 'di', 'ke', 'dari', 'yang', 'untuk', 'dengan', 'adalah', 'ini',
            'itu', 'pada', 'ada', 'tidak', 'juga', 'akan', 'atau', 'bisa', 'lebih',
            'sudah', 'saya', 'anda', 'kami', 'kita', 'mereka', 'dia', 'ia', 'oleh',
            'sebagai', 'dalam', 'telah', 'jika', 'maka', 'nya', 'lah', 'pun',
            'the', 'a', 'an', 'and', 'or', 'is', 'in', 'of', 'to', 'for',
        ];

        return array_values(array_filter($words, function ($w) use ($stopwords) {
            return !empty($w) && !in_array($w, $stopwords);
        }));
    }
}
