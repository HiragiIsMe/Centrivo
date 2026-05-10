<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;

use App\Models\Service;
use App\Models\Category;
use App\Models\Billboard;
use Illuminate\Http\Request;

class MarketController extends Controller
{
    public function main(Request $request)
    {
        $categoryId = $request->get('category');
        $search = $request->get('search');

        $categories = Category::all();
        $billboards = Billboard::where('is_active', true)->orderBy('order')->get();

        $query = Service::with(['seller.sellerProfile', 'category', 'images', 'reviews', 'activeAdvertisement', 'location'])
                        ->where('status', 'active')
                        ->where('is_banned', false);

        $userProfile = auth()->user()->userProfile;
        if ($userProfile && $userProfile->latitude && $userProfile->longitude) {
            $userLat = $userProfile->latitude;
            $userLng = $userProfile->longitude;
            $radius = 50; // 50 km

            $query->whereHas('location', function ($q) use ($userLat, $userLng, $radius) {
                $q->whereRaw("
                    (6371 * acos(
                        cos(radians(?)) * cos(radians(latitude)) * 
                        cos(radians(longitude) - radians(?)) + 
                        sin(radians(?)) * sin(radians(latitude))
                    )) <= ?
                ", [$userLat, $userLng, $userLat, $radius]);
            });
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($search) {
            $query->where('service_name', 'like', "%{$search}%");
        }

        $services = $query->latest()->paginate(12)->appends([
            'category' => $categoryId,
            'search' => $search
        ]);

        return view('market.index', compact('services', 'categories', 'billboards', 'categoryId', 'search'));
    }

    public function detailmain(Service $service)
    {
        if ($service->status !== 'active' || $service->is_banned) {
            abort(404);
        }

        $service->load(['seller.sellerProfile', 'category', 'images', 'reviews', 'location', 'activeAdvertisement']);

        return view('market.detail', compact('service'));
    }

    public function checkoutUI(\App\Models\Message $message)
    {
        if (\Illuminate\Support\Facades\Auth::id() !== $message->serviceRequest->user_id) {
            abort(403);
        }

        $message->load('serviceRequest.service.images', 'serviceRequest.seller.sellerProfile');

        return view('market.checkout', compact('message'));
    }
}
