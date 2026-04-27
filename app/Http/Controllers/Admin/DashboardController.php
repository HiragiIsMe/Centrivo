<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Withdrawal;
use App\Models\AdvertisementTransaction;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::where('role', 'user')->count();
        $totalSellers = User::where('role', 'seller')->count();
        
        $completedTransactions = Transaction::where('transaction_status', 'completed')->count();
        
        $adminFee = Setting::where('key', 'admin_fee')->first()->value ?? 2500;
        
        $serviceFeeRevenue = $completedTransactions * $adminFee;
        $adRevenue = AdvertisementTransaction::where('payment_status', 'paid')->sum('amount');
        
        $totalRevenue = $serviceFeeRevenue + $adRevenue;

        $recentWithdrawals = Withdrawal::with('seller.sellerProfile')
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        $months = [];
        $monthlyRevenueData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M');
            
            $monthlyServiceCount = Transaction::where('transaction_status', 'completed')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $monthlyServiceRev = $monthlyServiceCount * $adminFee;
            
            $monthlyAdRev = AdvertisementTransaction::where('payment_status', 'paid')
                ->whereYear('paid_at', $date->year)
                ->whereMonth('paid_at', $date->month)
                ->sum('amount');
                
            $monthlyRevenueData[] = $monthlyServiceRev + $monthlyAdRev;
        }

        $userGrowth = [];
        $sellerGrowth = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $userGrowth[] = User::where('role', 'user')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $sellerGrowth[] = User::where('role', 'seller')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        return view('admin.dashboard', compact(
            'totalUsers', 
            'totalSellers', 
            'completedTransactions', 
            'totalRevenue', 
            'serviceFeeRevenue',
            'adRevenue',
            'recentWithdrawals',
            'months',
            'monthlyRevenueData',
            'userGrowth',
            'sellerGrowth'
        ));
    }
}
