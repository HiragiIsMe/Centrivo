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
        
        // Admin Fee from Settings (fallback to 2500)
        $adminFee = Setting::where('key', 'admin_fee')->first()->value ?? 2500;
        
        // Platform Revenue = (Admin Fee * Completed Transactions) + Paid Ad Transactions
        $serviceFeeRevenue = $completedTransactions * $adminFee;
        $adRevenue = AdvertisementTransaction::where('payment_status', 'paid')->sum('amount');
        
        $totalRevenue = $serviceFeeRevenue + $adRevenue;

        $recentWithdrawals = Withdrawal::with('seller.sellerProfile')
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        // Chart Data: Monthly Platform Revenue (Last 6 Months)
        $months = [];
        $monthlyRevenueData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M');
            
            // Monthly Service Fee Revenue
            $monthlyServiceCount = Transaction::where('transaction_status', 'completed')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $monthlyServiceRev = $monthlyServiceCount * $adminFee;
            
            // Monthly Ad Revenue
            $monthlyAdRev = AdvertisementTransaction::where('payment_status', 'paid')
                ->whereYear('paid_at', $date->year)
                ->whereMonth('paid_at', $date->month)
                ->sum('amount');
                
            $monthlyRevenueData[] = $monthlyServiceRev + $monthlyAdRev;
        }

        // Chart Data: User & Seller Growth (Last 6 Months)
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
