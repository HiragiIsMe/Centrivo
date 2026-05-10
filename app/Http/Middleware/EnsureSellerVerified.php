<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSellerVerified
{
    /**
     * Block unverified sellers from creating or editing services.
     * They are redirected back with a flash message.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $seller = $request->user();

        if (!$seller || !$seller->sellerProfile) {
            return redirect()->route('sellers.dashboard')
                ->with('kyc_warning', 'Profil seller tidak ditemukan.');
        }

        if (!$seller->sellerProfile->canCreateService()) {
            return redirect()->route('sellers.dashboard')
                ->with('kyc_warning', 'Anda harus menyelesaikan verifikasi identitas terlebih dahulu sebelum bisa membuat atau mengelola layanan.');
        }

        return $next($request);
    }
}
