@extends('dashboard.main')

@section('admin_content')
<div class="mb-8">
    <h1 class="text-3xl font-black text-slate-800 tracking-tighter">Advertisement Management</h1>
    <p class="text-slate-400 mt-1 font-medium">Kelola paket iklan dan pantau seller yang berlangganan.</p>
</div>

@if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-600 rounded-2xl font-bold text-sm">
        {{ session('success') }}
    </div>
@endif

<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm flex items-center gap-6">
        <div class="w-14 h-14 bg-orange-50 text-orange-500 rounded-2xl flex items-center justify-center text-2xl">📣</div>
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Iklan Aktif Saat Ini</p>
            <h2 class="text-3xl font-black text-slate-800">{{ $totalActiveAds }}</h2>
        </div>
    </div>
    <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-3xl p-6 text-white shadow-xl flex items-center gap-6">
        <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center text-2xl">💰</div>
        <div>
            <p class="text-xs font-bold text-white/50 uppercase tracking-widest mb-1">Total Revenue Iklan</p>
            <h2 class="text-3xl font-black">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h2>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <!-- Paket Iklan -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-[32px] p-8 border border-gray-100 shadow-sm mb-6">
            <h3 class="text-lg font-bold text-slate-800 mb-6">Paket Iklan</h3>

            <div class="space-y-3 mb-6">
                @forelse($packages as $pkg)
                <div class="flex items-center justify-between p-4 border border-gray-100 rounded-2xl {{ $pkg->is_active ? 'bg-white' : 'bg-gray-50 opacity-60' }}">
                    <div>
                        <p class="font-bold text-slate-800">{{ $pkg->name }}</p>
                        <p class="text-xs text-slate-400">{{ $pkg->duration_days }} Hari — Rp {{ number_format($pkg->price, 0, ',', '.') }}</p>
                    </div>
                    <div class="flex gap-2">
                        <form action="{{ route('admin.ads.toggle', $pkg->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-xs font-bold px-3 py-1 rounded-lg {{ $pkg->is_active ? 'bg-green-50 text-green-600' : 'bg-gray-100 text-gray-500' }}">
                                {{ $pkg->is_active ? 'ON' : 'OFF' }}
                            </button>
                        </form>
                        <form action="{{ route('admin.ads.destroy', $pkg->id) }}" method="POST" onsubmit="return confirm('Hapus paket ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-bold px-3 py-1 rounded-lg bg-red-50 text-red-500 hover:bg-red-100">✗</button>
                        </form>
                    </div>
                </div>
                @empty
                <p class="text-sm text-slate-400 text-center py-4">Belum ada paket.</p>
                @endforelse
            </div>

            <!-- Form Tambah Paket -->
            <form action="{{ route('admin.ads.store') }}" method="POST" class="space-y-4 pt-4 border-t border-gray-100">
                @csrf
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Tambah Paket Baru</h4>
                <input type="text" name="name" placeholder="Nama paket (cth: Paket 7 Hari)" class="w-full bg-gray-50 border border-gray-100 rounded-xl p-3 text-sm font-medium outline-none focus:ring-2 focus:ring-color1/20" required>
                <div class="grid grid-cols-2 gap-3">
                    <input type="number" name="duration_days" min="1" max="365" placeholder="Durasi (hari)" class="bg-gray-50 border border-gray-100 rounded-xl p-3 text-sm font-medium outline-none focus:ring-2 focus:ring-color1/20" required>
                    <input type="number" name="price" min="1000" step="1000" placeholder="Harga (Rp)" class="bg-gray-50 border border-gray-100 rounded-xl p-3 text-sm font-medium outline-none focus:ring-2 focus:ring-color1/20" required>
                </div>
                <button type="submit" class="w-full bg-color1 hover:bg-color2 text-white font-bold py-3 rounded-xl text-sm transition-colors shadow-sm">
                    + Tambah Paket
                </button>
            </form>
        </div>
    </div>

    <!-- Daftar Subscriber -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-[32px] p-8 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-slate-800">Daftar Subscriber Iklan</h3>
            </div>

            <!-- Search -->
            <form method="GET" action="{{ route('admin.ads.index') }}" class="mb-6">
                <div class="flex gap-3">
                    <input type="text" name="search" value="{{ $searchQuery }}" placeholder="Cari nama brand, email, atau jasa..." class="flex-grow bg-gray-50 border border-gray-100 rounded-xl p-3 text-sm font-medium outline-none focus:ring-2 focus:ring-color1/20">
                    <button type="submit" class="bg-color1 hover:bg-color2 text-white font-bold px-6 py-3 rounded-xl text-sm transition-colors">Cari</button>
                </div>
            </form>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-gray-100">
                            <th class="py-4 pr-4">Seller</th>
                            <th class="py-4 pr-4">Jasa</th>
                            <th class="py-4 pr-4">Paket</th>
                            <th class="py-4 pr-4">Berakhir</th>
                            <th class="py-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 font-medium text-slate-600">
                        @forelse($subscribers as $sub)
                        <tr>
                            <td class="py-4 pr-4">
                                <p class="font-bold text-slate-800">{{ $sub->seller->sellerProfile->brand_name ?? '-' }}</p>
                                <p class="text-[10px] text-slate-400">{{ $sub->seller->email }}</p>
                            </td>
                            <td class="py-4 pr-4">{{ $sub->advertisement->service->service_name ?? '-' }}</td>
                            <td class="py-4 pr-4">
                                <span class="bg-color4 px-2 py-1 rounded-lg text-[10px] font-bold text-color1">{{ $sub->adPackage->name ?? $sub->duration_days . ' Hari' }}</span>
                            </td>
                            <td class="py-4 pr-4 text-xs">
                                @if($sub->advertisement->end_date)
                                    {{ \Carbon\Carbon::parse($sub->advertisement->end_date)->format('d M Y, H:i') }}
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                            <td class="py-4">
                                @if($sub->advertisement->isCurrentlyActive())
                                    <span class="bg-green-50 text-green-600 px-2 py-1 rounded-lg text-[10px] font-bold uppercase tracking-widest">Active</span>
                                @else
                                    <span class="bg-gray-100 text-gray-400 px-2 py-1 rounded-lg text-[10px] font-bold uppercase tracking-widest">Expired</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-slate-400">
                                <span class="text-3xl block mb-2 opacity-30">📭</span>
                                {{ $searchQuery ? 'Tidak ada hasil untuk "' . $searchQuery . '"' : 'Belum ada subscriber.' }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $subscribers->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
