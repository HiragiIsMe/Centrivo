<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ServiceTransactionController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $payment = $request->get('payment');
        $search = $request->get('search');

        $query = Transaction::with([
            'serviceRequest.service', 
            'serviceRequest.buyer.userProfile', 
            'serviceRequest.seller.sellerProfile'
        ]);

        if ($status && $status !== 'all') {
            $query->where('transaction_status', $status);
        }

        if ($payment && $payment !== 'all') {
            $query->where('payment_status', $payment);
        }

        if ($search) {
            $query->whereHas('serviceRequest', function($q) use ($search) {
                $q->whereHas('service', function($sq) use ($search) {
                    $sq->where('service_name', 'like', "%{$search}%");
                });
            });
        }

        $transactions = $query->latest()->paginate(10)->appends([
            'status' => $status,
            'payment' => $payment,
            'search' => $search
        ]);

        return view('dashboard.service-transactions', compact('transactions', 'status', 'payment', 'search'));
    }
}
