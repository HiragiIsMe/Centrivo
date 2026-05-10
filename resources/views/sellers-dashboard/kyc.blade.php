@extends('sellers-dashboard.main')

@section('sellers_content')
<div class="mb-8">
    <h1 class="text-3xl font-black text-slate-800 tracking-tighter">Verifikasi Identitas</h1>
    <p class="text-slate-400 mt-1 font-medium">Lengkapi data diri Anda untuk mulai berjualan di platform kami.</p>
</div>

{{-- Flash Messages --}}
@if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-700 rounded-2xl font-bold text-sm">
        ✅ {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-700 rounded-2xl text-sm">
        <p class="font-bold mb-2">Terdapat beberapa kesalahan:</p>
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Status Card --}}
@php
    $status = $sellerProfile->verification_status;
    $statusLabel = $sellerProfile->verification_status_label;
    $statusColor = $sellerProfile->verification_status_color;
@endphp

<div class="mb-8 p-6 rounded-[24px] border-2
    @if($status === 'verified') border-green-200 bg-green-50
    @elseif($status === 'pending') border-yellow-200 bg-yellow-50
    @elseif($status === 'rejected') border-red-200 bg-red-50
    @else border-slate-200 bg-slate-50 @endif">
    <div class="flex items-center gap-4">
        <div class="text-4xl">
            @if($status === 'verified') ✅
            @elseif($status === 'pending') ⏳
            @elseif($status === 'rejected') ❌
            @else 📋 @endif
        </div>
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Status Verifikasi</p>
            <span class="font-black text-xl {{ $statusColor }} px-3 py-1 rounded-xl">{{ $statusLabel }}</span>
            @if($status === 'verified')
                <p class="text-sm text-green-700 mt-2 font-medium">🎉 Selamat! Akun Anda sudah terverifikasi. Anda bisa mulai membuat dan mengelola layanan.</p>
            @elseif($status === 'pending')
                <p class="text-sm text-yellow-700 mt-2 font-medium">Data Anda sedang dalam proses peninjauan oleh tim kami. Mohon tunggu 1–2 hari kerja.</p>
            @elseif($status === 'rejected')
                <p class="text-sm text-red-700 mt-2 font-medium">Pengajuan Anda ditolak. Silakan perbaiki dan kirim ulang.</p>
                @if($sellerProfile->rejection_reason)
                    <div class="mt-3 p-4 bg-red-100 rounded-xl border border-red-200">
                        <p class="text-xs font-bold text-red-400 uppercase tracking-widest mb-1">Alasan Penolakan</p>
                        <p class="text-sm text-red-700">{{ $sellerProfile->rejection_reason }}</p>
                    </div>
                @endif
            @else
                <p class="text-sm text-slate-600 mt-2 font-medium">Lengkapi form di bawah untuk mengajukan verifikasi identitas.</p>
            @endif
        </div>
    </div>
</div>

@if($status !== 'verified' && $status !== 'pending')
{{-- KYC Form --}}
<form action="{{ route('seller.kyc.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf

    {{-- Step 1: Identity --}}
    <div class="bg-white rounded-[24px] p-8 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-color1 text-white rounded-2xl flex items-center justify-center font-black text-lg">1</div>
            <div>
                <h2 class="text-lg font-black text-slate-800">Data Identitas</h2>
                <p class="text-xs text-slate-400 font-medium">Masukkan Nomor Induk Kependudukan (NIK) sesuai KTP</p>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">NIK (Nomor Induk Kependudukan) <span class="text-red-500">*</span></label>
                <input type="text" name="nik" value="{{ old('nik', $sellerProfile->nik) }}" maxlength="16"
                    placeholder="Masukkan 16 digit NIK"
                    class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-5 py-3.5 text-sm font-medium outline-none focus:ring-2 focus:ring-color1/20 transition-all tracking-widest @error('nik') border-red-300 @enderror">
                @error('nik')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Foto KTP <span class="text-red-500">*</span></label>
                <div class="border-2 border-dashed border-gray-200 rounded-2xl p-6 text-center hover:border-color1/50 transition-all cursor-pointer" onclick="document.getElementById('ktp_input').click()">
                    <input type="file" id="ktp_input" name="ktp" accept="image/*" class="hidden" onchange="previewImage(this, 'ktp_preview')">
                    <div id="ktp_preview" class="hidden mb-3">
                        <img id="ktp_preview_img" src="" class="max-h-40 mx-auto rounded-xl object-cover" alt="Preview KTP">
                    </div>
                    <span class="text-3xl block mb-2">🪪</span>
                    <p class="text-sm font-bold text-slate-500">Klik untuk upload foto KTP</p>
                    <p class="text-xs text-slate-400 mt-1">JPG/PNG, Maks 5MB</p>
                    @if($sellerProfile->ktp_path)
                        <p class="text-xs text-green-600 font-bold mt-2">✓ File sudah ada (upload baru untuk mengganti)</p>
                    @endif
                </div>
                @error('ktp')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Selfie dengan KTP <span class="text-red-500">*</span></label>
                <div class="border-2 border-dashed border-gray-200 rounded-2xl p-6 text-center hover:border-color1/50 transition-all cursor-pointer" onclick="document.getElementById('selfie_input').click()">
                    <input type="file" id="selfie_input" name="selfie" accept="image/*" class="hidden" onchange="previewImage(this, 'selfie_preview')">
                    <div id="selfie_preview" class="hidden mb-3">
                        <img id="selfie_preview_img" src="" class="max-h-40 mx-auto rounded-xl object-cover" alt="Preview Selfie">
                    </div>
                    <span class="text-3xl block mb-2">🤳</span>
                    <p class="text-sm font-bold text-slate-500">Klik untuk upload foto selfie + KTP</p>
                    <p class="text-xs text-slate-400 mt-1">Pastikan wajah & KTP terlihat jelas</p>
                    @if($sellerProfile->selfie_path)
                        <p class="text-xs text-green-600 font-bold mt-2">✓ File sudah ada (upload baru untuk mengganti)</p>
                    @endif
                </div>
                @error('selfie')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    {{-- Step 2: Bank Info --}}
    <div class="bg-white rounded-[24px] p-8 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-color1 text-white rounded-2xl flex items-center justify-center font-black text-lg">2</div>
            <div>
                <h2 class="text-lg font-black text-slate-800">Informasi Rekening Bank</h2>
                <p class="text-xs text-slate-400 font-medium">Digunakan untuk proses pencairan (withdrawal) penghasilan Anda</p>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Nama Bank <span class="text-red-500">*</span></label>
                <select name="bank_name" class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-5 py-3.5 text-sm font-medium outline-none focus:ring-2 focus:ring-color1/20 @error('bank_name') border-red-300 @enderror">
                    <option value="">-- Pilih Bank --</option>
                    @php
                        $banks = ['BCA', 'BNI', 'BRI', 'Mandiri', 'CIMB Niaga', 'Permata Bank', 'Danamon', 'BTN', 'Bank Jago', 'Jenius (SMBC)', 'Allo Bank', 'SeaBank', 'BSI', 'Bank Mega', 'OCBC NISP'];
                    @endphp
                    @foreach($banks as $bank)
                        <option value="{{ $bank }}" {{ old('bank_name', $sellerProfile->bank_name) === $bank ? 'selected' : '' }}>{{ $bank }}</option>
                    @endforeach
                </select>
                @error('bank_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Nomor Rekening <span class="text-red-500">*</span></label>
                <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $sellerProfile->bank_account_number) }}"
                    placeholder="Contoh: 1234567890"
                    class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-5 py-3.5 text-sm font-medium outline-none focus:ring-2 focus:ring-color1/20 tracking-widest @error('bank_account_number') border-red-300 @enderror">
                @error('bank_account_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Nama Pemilik Rekening <span class="text-red-500">*</span></label>
                <input type="text" name="bank_account_name" value="{{ old('bank_account_name', $sellerProfile->bank_account_name) }}"
                    placeholder="Sesuai nama di buku tabungan"
                    class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-5 py-3.5 text-sm font-medium outline-none focus:ring-2 focus:ring-color1/20 @error('bank_account_name') border-red-300 @enderror">
                @error('bank_account_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    {{-- Disclaimer --}}
    <div class="bg-blue-50 border border-blue-100 rounded-[24px] p-6">
        <p class="text-sm font-bold text-blue-700 mb-2">📋 Ketentuan Verifikasi</p>
        <ul class="text-xs text-blue-600 space-y-1 list-disc list-inside">
            <li>Data yang Anda masukkan akan digunakan untuk keperluan verifikasi identitas.</li>
            <li>Proses verifikasi membutuhkan waktu 1–2 hari kerja.</li>
            <li>Pastikan foto KTP dan selfie jelas dan tidak buram.</li>
            <li>Data rekening digunakan untuk proses pencairan penghasilan.</li>
        </ul>
    </div>

    <button type="submit" class="w-full py-4 bg-color1 hover:bg-color2 text-white font-black rounded-[24px] text-base transition-all shadow-lg shadow-color1/30">
        🚀 Kirim Pengajuan Verifikasi
    </button>
</form>
@elseif($status === 'pending')
    <div class="text-center py-20 bg-white rounded-[24px] border border-gray-100">
        <span class="text-7xl block mb-4">⏳</span>
        <h2 class="text-2xl font-black text-slate-700">Sedang Diproses</h2>
        <p class="text-slate-400 mt-2 max-w-sm mx-auto">Tim kami sedang meninjau dokumen Anda. Mohon bersabar 1–2 hari kerja.</p>
    </div>
@endif

<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    const previewImg = document.getElementById(previewId + '_img');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            previewImg.src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
