<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Transaction;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Midtrans\Config;
use Midtrans\Snap;

class TransactionController extends Controller
{
    public function __construct()
    {
        // Set konfigurasi midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function processCheckout(Request $request, Message $message)
    {
        $user = Auth::user();

        // Validasi akses dan pesan
        if ($user->id !== $message->serviceRequest->user_id) {
            abort(403);
        }

        if ($message->is_checkout) {
            return redirect()->back()->with('error', 'Penawaran ini sudah Anda checkout sebelumnya.');
        }

        $request->validate([
            'service_type' => 'required|in:home_service,on_site'
        ]);

        // Tandai pesan sudah di checkout
        $message->update(['is_checkout' => true]);

        // Update service request dengan service type
        $message->serviceRequest->update([
            'service_type' => $request->service_type,
            'status' => 'agreed' // Optional: mengubah status negosiasi
        ]);

        $tax = $message->offered_price * 0.11;
        $adminFee = 2500;
        $finalPrice = $message->offered_price + $tax + $adminFee;

        // Buat transaksi
        $transaction = Transaction::create([
            'request_id' => $message->serviceRequest->id,
            'base_price' => $message->offered_price,
            'tax_amount' => $tax,
            'admin_fee' => $adminFee,
            'final_price' => $finalPrice,
            'payment_method' => 'transfer', // Default untuk Midtrans
            'transaction_status' => 'pending',
            'payment_status' => 'pending',
            'scheduled_date' => $message->scheduled_date,
        ]);

        // Buat parameter untuk Midtrans Snap
        $params = array(
            'transaction_details' => array(
                'order_id' => 'SRV-' . $transaction->id . '-' . time(), // Unique order id
                'gross_amount' => $finalPrice,
            ),
            'customer_details' => array(
                'first_name' => $user->userProfile->name ?? $user->email,
                'email' => $user->email,
                'phone' => $user->userProfile->phone ?? '',
            ),
            'item_details' => array(
                [
                    'id' => 'SRV-' . $message->serviceRequest->service_id,
                    'price' => $message->offered_price,
                    'quantity' => 1,
                    'name' => 'Layanan: ' . substr($message->serviceRequest->service->service_name, 0, 30)
                ],
                [
                    'id' => 'TAX',
                    'price' => $tax,
                    'quantity' => 1,
                    'name' => 'Pajak (PPN 11%)'
                ],
                [
                    'id' => 'FEE',
                    'price' => $adminFee,
                    'quantity' => 1,
                    'name' => 'Biaya Layanan Aplikasi'
                ]
            )
        );

        try {
            $snapToken = Snap::getSnapToken($params);
            
            // Simpan snap token ke database
            $transaction->update(['snap_token' => $snapToken]);

            return redirect()->route('user.payment', $transaction->id);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses pembayaran: ' . $e->getMessage());
        }
    }

    public function paymentPage(Transaction $transaction)
    {
        $user = Auth::user();

        if ($user->id !== $transaction->serviceRequest->user_id) {
            abort(403);
        }

        $transaction->load('serviceRequest.service.images', 'serviceRequest.seller.sellerProfile');

        return view('market.payment', compact('transaction'));
    }

    public function index()
    {
        $userId = Auth::id();

        $allTransactions = Transaction::with(['serviceRequest.service.images', 'serviceRequest.seller.sellerProfile'])
            ->whereHas('serviceRequest', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->latest()
            ->get();

        $pending = $allTransactions->where('payment_status', 'pending');
        $active = $allTransactions->where('payment_status', 'paid')->where('transaction_status', '!=', 'completed');
        $completed = $allTransactions->where('transaction_status', 'completed');

        return view('market.transactions', compact('pending', 'active', 'completed'));
    }


    public function complete(Request $request, Transaction $transaction)
    {
        $user = Auth::user();

        if ($user->id !== $transaction->serviceRequest->user_id) {
            abort(403);
        }

        if ($transaction->transaction_status === 'completed') {
            return redirect()->back()->with('error', 'Transaksi sudah diselesaikan sebelumnya.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        if ($transaction->scheduled_date && \Carbon\Carbon::parse($transaction->scheduled_date)->lt(now())) {
            return redirect()->back()->with('error', 'Transaksi belum bisa diselesaikan sebelum tanggal pelaksanaan yang dijadwalkan.');
        }

        // Update status transaksi
        $transaction->update([
            'transaction_status' => 'completed',
            'completed_at' => now(),
        ]);

        // Tambahkan saldo ke seller (mengambil harga penawaran asli sebelum pajak/fee)
        $sellerProfile = $transaction->serviceRequest->seller->sellerProfile;
        $sellerProfile->balance += $transaction->base_price;
        $sellerProfile->save();

        // Create Review
        Review::create([
            'user_id' => $user->id,
            'service_id' => $transaction->serviceRequest->service_id,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return redirect()->back()->with('success', 'Terima kasih! Ulasan Anda telah disimpan.');
    }
}
