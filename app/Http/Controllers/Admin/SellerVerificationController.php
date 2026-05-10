<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SellerProfile;
use Illuminate\Http\Request;

class SellerVerificationController extends Controller
{
    public function index()
    {
        $pendingVerifications = SellerProfile::with('user')
            ->where('verification_status', 'pending')
            ->latest()
            ->get();

        $verifiedSellers = SellerProfile::with('user')
            ->where('verification_status', 'verified')
            ->latest()
            ->get();

        $rejectedSellers = SellerProfile::with('user')
            ->where('verification_status', 'rejected')
            ->latest()
            ->get();

        $pendingCount = $pendingVerifications->count();

        return view('dashboard.seller-verifications', compact(
            'pendingVerifications',
            'verifiedSellers',
            'rejectedSellers',
            'pendingCount'
        ));
    }

    public function approve(SellerProfile $sellerProfile)
    {
        $sellerProfile->update([
            'verification_status' => 'verified',
            'rejection_reason'    => null,
            'verified_at'         => now(),
        ]);

        return back()->with('success', "Seller \"{$sellerProfile->brand_name}\" berhasil diverifikasi.");
    }

    public function reject(Request $request, SellerProfile $sellerProfile)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $sellerProfile->update([
            'verification_status' => 'rejected',
            'rejection_reason'    => $request->rejection_reason,
            'verified_at'         => null,
        ]);

        return back()->with('success', "Pengajuan verifikasi dari \"{$sellerProfile->brand_name}\" telah ditolak.");
    }
}
