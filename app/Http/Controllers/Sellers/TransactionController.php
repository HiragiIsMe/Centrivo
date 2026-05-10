<?php

namespace App\Http\Controllers\Sellers;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\ServiceRequest;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index()
    {
        $sellerId = Auth::id();

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

        $activeTransactions = $allTransactions->where('payment_status', 'paid')
                                              ->whereNotIn('transaction_status', ['completed', 'cancelled']);

        $completedTransactions = $allTransactions->whereIn('transaction_status', ['completed', 'cancelled']);

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

    public function reportUser(Request $request)
    {
        $request->validate([
            'reported_user_id' => 'required|exists:users,id',
            'reason'           => 'required|string|max:255',
            'description'      => 'nullable|string|max:2000',
        ]);

        // Cegah laporan duplikat dalam 24 jam
        $existing = Report::where('reporter_id', Auth::id())
            ->where('reported_user_id', $request->reported_user_id)
            ->where('created_at', '>=', now()->subDay())
            ->exists();

        if ($existing) {
            return back()->with('error', 'Anda sudah pernah mengirimkan laporan untuk pelanggan ini dalam 24 jam terakhir.');
        }

        Report::create([
            'reporter_id'      => Auth::id(),
            'reported_user_id' => $request->reported_user_id,
            'reason'           => $request->reason,
            'description'      => $request->description,
        ]);

        return back()->with('success', 'Laporan pelanggan berhasil dikirim. Tim kami akan segera meninjau.');
    }
}
