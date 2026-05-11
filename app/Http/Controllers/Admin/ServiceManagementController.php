<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Category;
use Illuminate\Http\Request;

class ServiceManagementController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'services');
        $search = $request->get('search');

        $services = collect();
        $categories = collect();

        if ($tab === 'services') {
            $query = Service::with(['seller.sellerProfile', 'category'])->withCount('reportsReceived');
            
            if ($search) {
                $query->where('service_name', 'like', "%{$search}%");
            }

            $services = $query->latest()->paginate(10)->appends(['tab' => $tab, 'search' => $search]);
        } else {
            $categories = Category::latest()->paginate(10)->appends(['tab' => $tab]);
        }

        return view('dashboard.servicencategories', compact('services', 'categories', 'tab', 'search'));
    }

    public function reports(Service $service)
    {
        $reports = \App\Models\Report::with(['reporter.userProfile'])
                         ->where('reported_service_id', $service->id)
                         ->latest()
                         ->get();
                         
        return response()->json([
            'service' => $service->load('seller.sellerProfile', 'category'),
            'reports' => $reports
        ]);
    }

    public function ban(Request $request, Service $service)
    {
        $request->validate([
            'ban_reason' => 'nullable|string|max:1000',
        ]);

        $banReason = $request->ban_reason ?? 'Layanan ini melanggar kebijakan platform.';
        $reportCode = $request->input('report_code') ?? \App\Models\Report::generateCode();

        $affectedTxs = \App\Models\Transaction::with('serviceRequest')
            ->whereHas('serviceRequest', fn($q) => $q->where('service_id', $service->id))
            ->where('payment_status', 'paid')
            ->whereNotIn('transaction_status', ['completed', 'cancelled'])
            ->where('is_disputed', false)
            ->get();

        $firstAffectedTxId = null;
        foreach ($affectedTxs as $tx) {
            $tx->update([
                'is_disputed' => true,
                'disputed_at' => now(),
                'disputed_by' => 'service_ban',
            ]);
            if (!$firstAffectedTxId) $firstAffectedTxId = $tx->id;
        }

        $service->update([
            'is_banned' => true,
            'ban_report_code' => $reportCode,
            'ban_reason' => $banReason,
        ]);

        $msg = "Layanan berhasil di-ban. Kode: {$reportCode}.";
        if (count($affectedTxs) > 0) $msg .= " " . count($affectedTxs) . " transaksi aktif ditandai disputed.";

        return back()->with('success', $msg);
    }

    public function unban(Service $service)
    {
        $service->update([
            'is_banned' => false,
            'ban_report_code' => null,
            'ban_reason' => null,
        ]);
        return back()->with('success', 'Service has been unbanned successfully.');
    }
}
