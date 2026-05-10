<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class ReportCenterController extends Controller
{
    public function index(Request $request)
    {
        $activeReports = Report::with([
            'reporter.userProfile',
            'reporter.sellerProfile',
            'reportedUser.userProfile',
            'reportedUser.sellerProfile',
            'reportedService'
        ])->whereIn('status', ['pending', 'reviewed'])->latest()->paginate(15, ['*'], 'active_page');

        $resolvedReports = Report::with([
            'reporter.userProfile',
            'reporter.sellerProfile',
            'reportedUser.userProfile',
            'reportedUser.sellerProfile',
            'reportedService'
        ])->where('status', 'resolved')->latest()->paginate(15, ['*'], 'resolved_page');

        $disputedTransactions = Transaction::with([
            'serviceRequest.service',
            'serviceRequest.buyer.userProfile',
            'serviceRequest.seller.sellerProfile'
        ])->where('is_disputed', true)->latest()->paginate(15, ['*'], 'disputed_page');

        $pendingCount = $activeReports->total();
        $disputedCount = $disputedTransactions->total();

        return view('dashboard.report-center', compact(
            'activeReports', 'resolvedReports', 'disputedTransactions', 
            'pendingCount', 'disputedCount'
        ));
    }

    public function show(Report $report)
    {
        $report->load([
            'reporter.userProfile',
            'reporter.sellerProfile',
            'reportedUser.userProfile',
            'reportedUser.sellerProfile',
            'reportedService.images',
            'relatedTransaction.serviceRequest.service',
            'relatedTransaction.serviceRequest.buyer.userProfile',
            'relatedTransaction.serviceRequest.seller.sellerProfile',
        ]);

        return view('dashboard.report-center-detail', compact('report'));
    }

    public function updateStatus(Request $request, Report $report)
    {
        $request->validate([
            'status'      => 'required|in:pending,reviewed,resolved',
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $report->update([
            'status'      => $request->status,
            'admin_notes' => $request->admin_notes,
        ]);

        return back()->with('success', 'Status laporan berhasil diperbarui.');
    }

    public function markResolved(Report $report)
    {
        $report->update(['status' => 'resolved']);

        // Jika ada transaksi terkait yang disputed, tawarkan untuk un-dispute
        if ($report->relatedTransaction && $report->relatedTransaction->is_disputed) {
            $report->relatedTransaction->update([
                'is_disputed'  => false,
                'disputed_at'  => null,
                'disputed_by'  => null,
            ]);
        }

        return back()->with('success', 'Laporan ditandai sebagai resolved. Transaksi terkait (jika ada) telah dipulihkan.');
    }

    public function resolveTransaction(Request $request, Transaction $transaction)
    {
        $request->validate([
            'action' => 'required|in:resume,cancel',
            'notes' => 'nullable|string|max:500'
        ]);

        if ($request->action === 'resume') {
            $transaction->update([
                'is_disputed' => false,
                'disputed_at' => null,
                'disputed_by' => null,
            ]);
            $message = 'Transaksi berhasil dilanjutkan (Resume).';
        } else {
            $transaction->update([
                'transaction_status' => 'cancelled',
                'is_disputed' => false,
                'disputed_at' => null,
                'disputed_by' => null,
            ]);
            $message = 'Transaksi berhasil dibatalkan. Menunggu proses refund.';
        }

        // Simpan admin_notes di transaksi jika diperlukan nanti, 
        // untuk sekarang kembalikan dengan pesan sukses.

        return redirect()->back()->with('success', $message);
    }
}
