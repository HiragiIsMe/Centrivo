@extends('sellers-dashboard.main')

@section('sellers_content')
<div class="mb-8">
    <h1 class="text-3xl font-black text-slate-800 tracking-tighter">Iklankan Jasa Anda</h1>
    <p class="text-slate-400 mt-1 font-medium">Pilih jasa dan paket iklan untuk menjangkau lebih banyak pelanggan.</p>
</div>

@if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-600 rounded-2xl font-bold text-sm">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-600 rounded-2xl font-bold text-sm">{{ session('error') }}</div>
@endif
@if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-600 rounded-2xl font-bold text-sm">
        <ul class="list-disc pl-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <!-- Form Beli Iklan -->
    <div class="lg:col-span-1">
        <div class="bg-gradient-to-br from-orange-400 to-orange-500 rounded-[32px] p-8 text-white shadow-xl shadow-orange-400/20">
            <p class="text-white/80 font-bold uppercase tracking-widest text-xs mb-2">🚀 Boost Jasa Anda</p>
            <h2 class="text-2xl font-black mb-6">Beli Paket Iklan</h2>

            <form action="{{ route('seller.advertisements.checkout') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Pilih Jasa -->
                <div>
                    <label class="block text-xs font-bold text-white/90 uppercase tracking-widest mb-2">Pilih Jasa</label>
                    <select name="service_id" required class="w-full bg-white/20 border border-white/30 rounded-xl p-3 text-white focus:outline-none focus:ring-2 focus:ring-white appearance-none">
                        <option value="" disabled selected class="text-slate-800">— Pilih jasa —</option>
                        @foreach($myServices as $svc)
                            <option value="{{ $svc->id }}" class="text-slate-800" {{ $svc->activeAdvertisement ? 'disabled' : '' }}>
                                {{ $svc->service_name }} {{ $svc->activeAdvertisement ? '(Sudah Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Pilih Paket -->
                <div>
                    <label class="block text-xs font-bold text-white/90 uppercase tracking-widest mb-2">Pilih Paket</label>
                    <div class="space-y-3">
                        @forelse($packages as $pkg)
                        <label class="flex items-center gap-4 p-4 bg-white/15 border border-white/20 rounded-xl cursor-pointer hover:bg-white/25 transition-all">
                            <input type="radio" name="ad_package_id" value="{{ $pkg->id }}" class="accent-white w-4 h-4" required>
                            <div class="flex-grow">
                                <p class="font-bold text-sm">{{ $pkg->name }}</p>
                                <p class="text-[10px] text-white/70">{{ $pkg->duration_days }} Hari</p>
                            </div>
                            <span class="font-black text-sm">Rp {{ number_format($pkg->price, 0, ',', '.') }}</span>
                        </label>
                        @empty
                        <p class="text-white/60 text-sm text-center py-4">Admin belum menyediakan paket iklan.</p>
                        @endforelse
                    </div>
                </div>

                @if($packages->count() > 0 && $myServices->count() > 0)
                <button type="submit" class="w-full bg-white text-orange-500 hover:bg-orange-50 font-black py-4 rounded-xl transition-colors shadow-lg mt-2">
                    Bayar & Aktifkan Iklan
                </button>
                @endif
            </form>
        </div>
    </div>

    <!-- Riwayat Iklan -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-[32px] p-8 border border-gray-100 shadow-sm">
            <h3 class="text-xl font-bold text-slate-800 mb-6">Riwayat Iklan Saya</h3>

            <div class="space-y-4">
                @forelse($myAdHistory as $ad)
                <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 p-5 border border-gray-100 rounded-2xl">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center font-bold text-xl flex-shrink-0
                            {{ $ad->payment_status == 'paid' ? ($ad->advertisement->isCurrentlyActive() ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400') : ($ad->payment_status == 'pending' ? 'bg-yellow-100 text-yellow-600' : 'bg-red-100 text-red-600') }}">
                            {{ $ad->payment_status == 'paid' ? ($ad->advertisement->isCurrentlyActive() ? '📣' : '⏰') : ($ad->payment_status == 'pending' ? '⏳' : '✗') }}
                        </div>
                        <div>
                            <p class="font-bold text-slate-800">{{ $ad->advertisement->service->service_name ?? 'Jasa' }}</p>
                            <p class="text-xs font-medium text-slate-500">{{ $ad->adPackage->name ?? $ad->duration_days . ' Hari' }} — Rp {{ number_format($ad->amount, 0, ',', '.') }}</p>
                            <p class="text-[10px] text-slate-400 mt-1">{{ $ad->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                    <div class="text-right flex flex-col items-end gap-2">
                        @if($ad->payment_status == 'paid' && $ad->advertisement->isCurrentlyActive())
                            <span class="bg-green-50 text-green-600 px-3 py-1 rounded-lg text-[10px] font-bold uppercase tracking-widest">Aktif s/d {{ $ad->advertisement->end_date->format('d M Y') }}</span>
                        @elseif($ad->payment_status == 'paid')
                            <span class="bg-gray-100 text-gray-400 px-3 py-1 rounded-lg text-[10px] font-bold uppercase tracking-widest">Expired</span>
                        @elseif($ad->payment_status == 'pending')
                            <a href="{{ route('seller.advertisements.pay', $ad->id) }}" class="px-5 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-bold rounded-xl text-xs transition-colors shadow-sm">Lanjutkan Bayar</a>
                        @else
                            <span class="bg-red-50 text-red-500 px-3 py-1 rounded-lg text-[10px] font-bold uppercase tracking-widest">Gagal</span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center py-10">
                    <span class="text-4xl block mb-2 opacity-30">📭</span>
                    <p class="text-slate-500 font-medium">Anda belum pernah membeli iklan.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
