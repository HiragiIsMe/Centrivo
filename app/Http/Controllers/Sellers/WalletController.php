<?php

namespace App\Http\Controllers\Sellers;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function index()
    {
        $seller = Auth::user();
        $balance = $seller->sellerProfile->balance;
        $withdrawals = Withdrawal::where('seller_id', $seller->id)->latest()->get();

        return view('sellers-dashboard.wallet', compact('balance', 'withdrawals'));
    }

    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
            'account_name' => 'required|string|max:100',
        ]);

        $seller = Auth::user();
        $profile = $seller->sellerProfile;

        if ($request->amount > $profile->balance) {
            return redirect()->back()->with('error', 'Saldo tidak mencukupi untuk penarikan sebesar Rp ' . number_format($request->amount, 0, ',', '.'));
        }

        $pending = Withdrawal::where('seller_id', $seller->id)->where('status', 'pending')->exists();
        if ($pending) {
            return redirect()->back()->with('error', 'Anda masih memiliki permintaan penarikan yang sedang diproses.');
        }

        $profile->balance -= $request->amount;
        $profile->save();

        Withdrawal::create([
            'seller_id' => $seller->id,
            'amount' => $request->amount,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
            'status' => 'pending'
        ]);

        return redirect()->back()->with('success', 'Permintaan penarikan dana berhasil diajukan dan sedang diproses oleh admin.');
    }
}
