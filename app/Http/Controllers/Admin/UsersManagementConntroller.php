<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class UsersManagementConntroller extends Controller
{
    public function index(Request $request)
    {
        $tab    = $request->get('tab', 'seller');
        $search = $request->get('search');

        $query = User::with(['userProfile', 'sellerProfile'])->withCount('reportsReceived');

        if ($tab === 'seller') {
            $query->where('role', 'seller');
        } else {
            $query->where('role', 'user');
        }

        if ($search) {
            $query->where(function ($q) use ($search, $tab) {
                $q->where('email', 'like', "%{$search}%");
                if ($tab === 'seller') {
                    $q->orWhereHas('sellerProfile', function ($sq) use ($search) {
                        $sq->where('brand_name', 'like', "%{$search}%");
                    });
                } else {
                    $q->orWhereHas('userProfile', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    });
                }
            });
        }

        $users = $query->paginate(10)->appends(['tab' => $tab, 'search' => $search]);

        return view('dashboard.users-management', compact('users', 'tab', 'search'));
    }

    public function reports(User $user)
    {
        $reports = Report::with(['reporter.userProfile', 'reportedService', 'relatedTransaction.serviceRequest.service'])
            ->where('reported_user_id', $user->id)
            ->latest()
            ->get();

        return response()->json([
            'user'    => $user->load('userProfile', 'sellerProfile'),
            'reports' => $reports,
        ]);
    }

    public function ban(Request $request, User $user)
    {
        $request->validate([
            'ban_reason' => 'nullable|string|max:1000',
        ]);

        $banReason   = $request->ban_reason ?? 'Melanggar Ketentuan Layanan Centrivo.';
        $reportCode  = $request->input('report_code') ?? Report::generateCode();

        // Cari semua transaksi aktif yang terdampak
        $disputedBy  = $user->role === 'user' ? 'user_ban' : 'seller_ban';
        $affectedTxs = [];

        if ($user->role === 'user') {
            // Transaksi yang sedang berjalan dimana user ini sebagai pembeli
            $affectedTxs = Transaction::with('serviceRequest')
                ->whereHas('serviceRequest', fn($q) => $q->where('user_id', $user->id))
                ->where('payment_status', 'paid')
                ->whereNotIn('transaction_status', ['completed', 'cancelled'])
                ->where('is_disputed', false)
                ->get();
        } elseif ($user->role === 'seller') {
            // Transaksi yang sedang berjalan dimana user ini sebagai seller
            $affectedTxs = Transaction::with('serviceRequest')
                ->whereHas('serviceRequest', fn($q) => $q->where('seller_id', $user->id))
                ->where('payment_status', 'paid')
                ->whereNotIn('transaction_status', ['completed', 'cancelled'])
                ->where('is_disputed', false)
                ->get();
        }

        // Tandai semua transaksi terkait sebagai disputed
        $firstAffectedTxId = null;
        foreach ($affectedTxs as $tx) {
            $tx->update([
                'is_disputed' => true,
                'disputed_at' => now(),
                'disputed_by' => $disputedBy,
            ]);
            if (!$firstAffectedTxId) {
                $firstAffectedTxId = $tx->id;
            }
        }

        // Hapus: Tidak perlu lagi membuat report untuk admin ban.

        // Update user
        $user->update([
            'is_banned'        => true,
            'banned_at'        => now(),
            'ban_report_code'  => $reportCode,
            'ban_reason'       => $banReason,
        ]);

        $affectedCount = count($affectedTxs);
        $message = "User berhasil di-ban. Kode laporan: {$reportCode}.";
        if ($affectedCount > 0) {
            $message .= " {$affectedCount} transaksi aktif ditandai sebagai bermasalah (disputed).";
        }

        return back()->with('success', $message);
    }

    public function unban(User $user)
    {
        $user->update([
            'is_banned'       => false,
            'banned_at'       => null,
            'ban_report_code' => null,
            'ban_reason'      => null,
        ]);

        // Tidak perlu lagi update status laporan karena tidak ada laporan admin

        return back()->with('success', 'User berhasil di-unban.');
    }
}
