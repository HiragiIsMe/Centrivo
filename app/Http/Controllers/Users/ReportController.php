<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::where('reporter_id', Auth::id())
            ->with(['reportedUser.userProfile', 'reportedUser.sellerProfile', 'reportedService'])
            ->latest()
            ->paginate(10);

        return view('user-dashboard.my-reports', compact('reports'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'reported_service_id' => 'nullable|exists:services,id',
            'reported_user_id'    => 'nullable|exists:users,id',
            'reason'              => 'required|string|max:255',
            'description'        => 'nullable|string|max:2000',
        ]);

        if (!$request->reported_service_id && !$request->reported_user_id) {
            return back()->withErrors(['reason' => 'Target laporan tidak valid.']);
        }

        if ($request->reported_user_id && $request->reported_user_id == Auth::id()) {
            return back()->withErrors(['reason' => 'Anda tidak dapat melaporkan diri sendiri.']);
        }

        $existing = Report::where('reporter_id', Auth::id())
            ->when($request->reported_service_id, fn($q) => $q->where('reported_service_id', $request->reported_service_id))
            ->when($request->reported_user_id, fn($q) => $q->where('reported_user_id', $request->reported_user_id))
            ->where('created_at', '>=', now()->subDay())
            ->exists();

        if ($existing) {
            return back()->with('error', 'Anda sudah pernah mengirimkan laporan untuk ini dalam 24 jam terakhir.');
        }

        Report::create([
            'reporter_id'         => Auth::id(),
            'reported_user_id'    => $request->reported_user_id,
            'reported_service_id' => $request->reported_service_id,
            'reason'              => $request->reason,
            'description'         => $request->description,
        ]);

        return back()->with('success', 'Laporan Anda telah berhasil dikirim. Tim kami akan meninjau dalam waktu dekat.');
    }
}
