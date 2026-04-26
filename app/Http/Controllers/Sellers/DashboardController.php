<?php

namespace App\Http\Controllers\Sellers;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $seller = Auth::user();
        $sellerProfile = $seller->sellerProfile;

        // Basic Stats
        $totalRevenue = Transaction::whereHas('serviceRequest.service', function ($query) use ($seller) {
            $query->where('seller_id', $seller->id);
        })->where('transaction_status', 'completed')->sum('base_price');

        $completedTransactions = Transaction::whereHas('serviceRequest.service', function ($query) use ($seller) {
            $query->where('seller_id', $seller->id);
        })->where('transaction_status', 'completed')->count();

        $activeServices = Service::where('seller_id', $seller->id)
            ->where('status', 'active')
            ->count();

        $balance = $sellerProfile->balance ?? 0;

        $averageRating = Review::whereHas('service', function ($query) use ($seller) {
            $query->where('seller_id', $seller->id);
        })->avg('rating') ?? 0;

        // Chart Data: Monthly Revenue (Last 6 Months)
        $months = [];
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M');
            
            $revenue = Transaction::whereHas('serviceRequest.service', function ($query) use ($seller) {
                $query->where('seller_id', $seller->id);
            })
            ->where('transaction_status', 'completed')
            ->whereYear('completed_at', $date->year)
            ->whereMonth('completed_at', $date->month)
            ->sum('base_price');
            
            $monthlyRevenue[] = $revenue;
        }

        // Chart Data: Transaction Status Distribution
        $statusCounts = Transaction::whereHas('serviceRequest.service', function ($query) use ($seller) {
            $query->where('seller_id', $seller->id);
        })
        ->select('transaction_status', DB::raw('count(*) as count'))
        ->groupBy('transaction_status')
        ->pluck('count', 'transaction_status')
        ->toArray();

        $statuses = ['pending', 'accepted', 'ongoing', 'completed', 'cancelled'];
        $statusData = [];
        foreach ($statuses as $status) {
            $statusData[] = $statusCounts[$status] ?? 0;
        }

        return view('sellers-dashboard.dashboard', compact(
            'totalRevenue',
            'completedTransactions',
            'activeServices',
            'balance',
            'averageRating',
            'months',
            'monthlyRevenue',
            'statuses',
            'statusData'
        ));
    }
}
