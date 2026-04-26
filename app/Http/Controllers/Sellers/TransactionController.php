<?php

namespace App\Http\Controllers\Sellers;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index()
    {
        $sellerId = Auth::id();

        // Tab 1: Negosiasi Aktif (ServiceRequests where seller is this user and status is open/negotiating)
        $negotiations = ServiceRequest::with(['service.images', 'buyer.userProfile'])
            ->where('seller_id', $sellerId)
            ->whereIn('status', ['open', 'negotiating'])
            ->latest()
            ->get();

        $allTransactions = Transaction::with(['serviceRequest.service.images', 'serviceRequest.buyer.userProfile'])
            ->whereHas('serviceRequest', function ($q) use ($sellerId) {
                $q->where('seller_id', $sellerId);
            })
            ->latest()
            ->get();

        // Tab 2: Berjalan (Paid but not completed)
        $activeTransactions = $allTransactions->where('payment_status', 'paid')->where('transaction_status', '!=', 'completed');

        // Tab 3: Selesai (Completed)
        $completedTransactions = $allTransactions->where('transaction_status', 'completed');

        // Tab 4: Riwayat Chat (All ServiceRequests with messages)
        $chatHistories = ServiceRequest::with(['service.images', 'buyer.userProfile', 'messages' => function($q) { 
                $q->latest(); 
            }])
            ->where('seller_id', $sellerId)
            ->where('status', 'agreed')
            ->whereHas('messages')
            ->get()
            ->sortByDesc(function($req) { 
                return $req->messages->first()->created_at ?? $req->created_at; 
            });

        return view('sellers-dashboard.transactions', compact('negotiations', 'activeTransactions', 'completedTransactions', 'chatHistories'));
    }
}
