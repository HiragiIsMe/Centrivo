<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    public function index()
    {
        $withdrawals = Withdrawal::with('seller.sellerProfile')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.withdrawals', compact('withdrawals'));
    }

    public function approve(Withdrawal $withdrawal)
    {
        if ($withdrawal->status !== 'pending') {
            return redirect()->back()->with('error', 'Withdrawal status is already processed.');
        }

        $withdrawal->update(['status' => 'approved']);

        return redirect()->back()->with('success', 'Withdrawal approved successfully. Make sure you have transferred the funds to the seller.');
    }

    public function reject(Withdrawal $withdrawal)
    {
        if ($withdrawal->status !== 'pending') {
            return redirect()->back()->with('error', 'Withdrawal status is already processed.');
        }

        DB::transaction(function () use ($withdrawal) {
            $withdrawal->update(['status' => 'rejected']);
            
            $profile = $withdrawal->seller->sellerProfile;
            $profile->balance += $withdrawal->amount;
            $profile->save();
        });

        return redirect()->back()->with('success', 'Withdrawal rejected. Funds have been returned to the seller\'s balance.');
    }
}
