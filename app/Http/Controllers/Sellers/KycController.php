<?php

namespace App\Http\Controllers\Sellers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class KycController extends Controller
{
    public function show()
    {
        $sellerProfile = Auth::user()->sellerProfile;
        return view('sellers-dashboard.kyc', compact('sellerProfile'));
    }

    public function submit(Request $request)
    {
        $sellerProfile = Auth::user()->sellerProfile;

        if ($sellerProfile->verification_status === 'verified') {
            return back()->with('success', 'Akun Anda sudah terverifikasi.');
        }

        $request->validate([
            'nik'                 => 'required|string|digits:16',
            'ktp'                 => 'required|image|mimes:jpg,jpeg,png|max:5120',
            'selfie'              => 'required|image|mimes:jpg,jpeg,png|max:5120',
            'bank_name'           => 'required|string|max:100',
            'bank_account_number' => 'required|string|max:50',
            'bank_account_name'   => 'required|string|max:100',
        ], [
            'nik.digits' => 'NIK harus terdiri dari 16 digit angka.',
            'ktp.required' => 'Foto KTP wajib diunggah.',
            'selfie.required' => 'Foto selfie dengan KTP wajib diunggah.',
            'ktp.max' => 'Ukuran foto KTP maksimal 5MB.',
            'selfie.max' => 'Ukuran foto selfie maksimal 5MB.',
        ]);

        if ($sellerProfile->ktp_path) {
            Storage::disk('public')->delete($sellerProfile->ktp_path);
        }
        if ($sellerProfile->selfie_path) {
            Storage::disk('public')->delete($sellerProfile->selfie_path);
        }

        $ktpPath    = $request->file('ktp')->store('kyc/ktp', 'public');
        $selfiePath = $request->file('selfie')->store('kyc/selfie', 'public');

        $sellerProfile->update([
            'nik'                 => $request->nik,
            'ktp_path'            => $ktpPath,
            'selfie_path'         => $selfiePath,
            'bank_name'           => $request->bank_name,
            'bank_account_number' => $request->bank_account_number,
            'bank_account_name'   => $request->bank_account_name,
            'verification_status' => 'pending',
            'rejection_reason'    => null,
        ]);

        return redirect()->route('seller.kyc.show')
            ->with('success', 'Data verifikasi berhasil dikirim! Tim kami akan meninjaunya dalam 1–2 hari kerja.');
    }
}
