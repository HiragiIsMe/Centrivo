@extends('sellers-dashboard.main')

@section('sellers_content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="mb-8">
    <h1 class="text-3xl font-black text-slate-800 tracking-tighter">Manajemen Transaksi</h1>
    <p class="text-slate-400 mt-1 font-medium">Balas negosiasi masuk dan pantau transaksi yang sudah dibayar.</p>
</div>

<!-- Tabs -->
<div class="flex gap-4 mb-6 border-b border-gray-100 pb-2 overflow-x-auto no-scrollbar">
    <button onclick="switchTab('negosiasi')" id="tab-negosiasi" class="px-6 py-3 rounded-xl font-bold text-sm transition-all bg-color1 text-white shadow-lg shadow-color1/20 whitespace-nowrap">
        Negosiasi Aktif ({{ $negotiations->count() }})
    </button>
    <button onclick="switchTab('berjalan')" id="tab-berjalan" class="px-6 py-3 rounded-xl font-bold text-sm transition-all text-slate-500 hover:bg-gray-100 whitespace-nowrap">
        Berjalan ({{ $activeTransactions->count() }})
    </button>
    <button onclick="switchTab('selesai')" id="tab-selesai" class="px-6 py-3 rounded-xl font-bold text-sm transition-all text-slate-500 hover:bg-gray-100 whitespace-nowrap">
        Selesai ({{ $completedTransactions->count() }})
    </button>
    <button onclick="switchTab('riwayat')" id="tab-riwayat" class="px-6 py-3 rounded-xl font-bold text-sm transition-all text-slate-500 hover:bg-gray-100 whitespace-nowrap relative">
        Riwayat Chat ({{ $chatHistories->count() }})
        @php
            $unreadHistory = $chatHistories->filter(function($req) {
                return $req->messages->where('sender_id', '!=', Auth::id())->where('is_read', false)->count() > 0;
            })->count();
        @endphp
        @if($unreadHistory > 0)
        <span class="absolute top-1 right-2 bg-red-500 text-white text-[10px] font-black px-1.5 py-0.5 rounded-full">{{ $unreadHistory }}</span>
        @endif
    </button>
</div>

<!-- Tab Content: Negosiasi -->
<div id="content-negosiasi" class="space-y-4">
    @forelse($negotiations as $req)
    <div class="bg-white rounded-[24px] p-6 border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-sm hover:shadow-md transition-shadow" id="seller-conv-{{ $req->id }}">
        <div class="flex gap-4 items-center">
            <div class="w-16 h-16 bg-slate-100 rounded-2xl flex-shrink-0 overflow-hidden">
                @if($req->service->images->count() > 0)
                    <img src="{{ asset('storage/' . $req->service->images->first()->image_path) }}" class="w-full h-full object-cover" alt="Service">
                @else
                    <div class="w-full h-full flex items-center justify-center text-[10px] text-slate-400 font-bold">No Image</div>
                @endif
            </div>
            <div>
                <p class="text-[10px] font-bold text-color1 uppercase tracking-widest mb-1">{{ $req->created_at->format('d M Y, H:i') }}</p>
                <h3 class="font-bold text-slate-800 line-clamp-1 text-lg leading-tight">{{ $req->service->service_name }}</h3>
                <p class="text-sm font-medium text-slate-500 mt-1">Calon Pelanggan: <span class="font-bold text-slate-700">{{ $req->buyer->userProfile->name ?? 'User' }}</span></p>
            </div>
        </div>
        <div class="flex-shrink-0 flex items-center gap-2">
            <a href="{{ route('negotiation.show', $req->id) }}" class="inline-block bg-color1 hover:bg-color2 text-white font-bold px-6 py-3 rounded-xl transition-colors shadow-sm text-sm text-center">
                Buka Ruang Chat
            </a>
            <button onclick="deleteSellerConv({{ $req->id }})" class="p-3 text-slate-300 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all" title="Hapus Percakapan">
                🗑️
            </button>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-[24px] p-10 border border-gray-100 text-center">
        <span class="text-4xl opacity-50 block mb-3">💬</span>
        <h3 class="font-bold text-slate-700 text-lg">Belum Ada Negosiasi Aktif</h3>
        <p class="text-slate-400 mt-1 text-sm">Pesanan yang masih dinegosiasikan akan muncul di sini.</p>
    </div>
    @endforelse
</div>

<!-- Tab Content: Berjalan -->
<div id="content-berjalan" class="space-y-4 hidden">
    @forelse($activeTransactions as $tx)
    <div class="bg-white rounded-[24px] p-6 border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex gap-4 items-center">
            <div class="w-16 h-16 bg-blue-100 text-blue-500 rounded-2xl flex-shrink-0 flex items-center justify-center text-2xl font-bold">
                🏃
            </div>
            <div>
                <p class="text-[10px] font-bold text-blue-600 uppercase tracking-widest mb-1">ID Transaksi: #{{ $tx->id }}</p>
                <h3 class="font-bold text-slate-800 line-clamp-1 text-lg leading-tight">{{ $tx->serviceRequest->service->service_name }}</h3>
                <p class="text-sm font-medium text-slate-500 mt-1">Pelanggan: <span class="font-bold text-slate-700">{{ $tx->serviceRequest->buyer->userProfile->name ?? 'User' }}</span></p>
                
                <div class="mt-3 p-3 bg-gray-50 rounded-xl border border-gray-100">
                    <div class="flex gap-4 text-xs font-bold text-slate-600 mb-2">
                        <span>🗓️ {{ \Carbon\Carbon::parse($tx->scheduled_date)->format('d M Y, H:i') }}</span>
                        <span>💰 Rp {{ number_format($tx->final_price, 0, ',', '.') }}</span>
                        <span class="text-color1">{{ $tx->serviceRequest->service_type == 'home_service' ? '🏠 Home Service' : '🏢 On Site' }}</span>
                    </div>
                    @if($tx->serviceRequest->service_type == 'home_service')
                        <p class="text-xs text-slate-500 font-medium break-all">📍 Lokasi: {{ $tx->serviceRequest->buyer->userProfile->address ?? 'Alamat tidak tersedia' }}</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex-shrink-0 flex flex-col items-end gap-2">
            <a href="{{ route('negotiation.show', $tx->serviceRequest->id) }}" class="text-sm font-bold text-color1 hover:underline">Chat Pelanggan</a>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-[24px] p-10 border border-gray-100 text-center">
        <span class="text-4xl opacity-50 block mb-3">🏃</span>
        <h3 class="font-bold text-slate-700 text-lg">Tidak Ada Pesanan Berjalan</h3>
        <p class="text-slate-400 mt-1 text-sm">Pesanan yang sudah dibayar dan menunggu dikerjakan akan muncul di sini.</p>
    </div>
    @endforelse
</div>

<!-- Tab Content: Selesai -->
<div id="content-selesai" class="space-y-4 hidden">
    @forelse($completedTransactions as $tx)
    <div class="bg-white rounded-[24px] p-6 border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex gap-4 items-center">
            <div class="w-16 h-16 bg-green-100 text-green-500 rounded-2xl flex-shrink-0 flex items-center justify-center text-2xl font-bold">
                ✓
            </div>
            <div>
                <p class="text-[10px] font-bold text-green-600 uppercase tracking-widest mb-1">ID Transaksi: #{{ $tx->id }}</p>
                <h3 class="font-bold text-slate-800 line-clamp-1 text-lg leading-tight">{{ $tx->serviceRequest->service->service_name }}</h3>
                <p class="text-sm font-medium text-slate-500 mt-1">Pelanggan: <span class="font-bold text-slate-700">{{ $tx->serviceRequest->buyer->userProfile->name ?? 'User' }}</span></p>
                <div class="mt-2 flex gap-3 text-xs font-bold text-slate-600">
                    <span class="bg-gray-100 px-2 py-1 rounded-lg">Rp {{ number_format($tx->base_price, 0, ',', '.') }}</span>
                </div>
                
                @php
                    $review = \App\Models\Review::where('user_id', $tx->serviceRequest->user_id)
                        ->where('service_id', $tx->serviceRequest->service_id)
                        ->latest()
                        ->first();
                @endphp
                @if($review)
                <div class="mt-3 p-3 bg-gray-50 rounded-xl border border-gray-100">
                    <div class="flex gap-1 text-yellow-400 text-xs mb-1">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $review->rating)
                                ★
                            @else
                                <span class="text-gray-300">★</span>
                            @endif
                        @endfor
                    </div>
                    <p class="text-xs text-slate-600 italic">"{{ $review->comment ?? 'Tidak ada komentar' }}"</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-[24px] p-10 border border-gray-100 text-center">
        <span class="text-4xl opacity-50 block mb-3">✅</span>
        <h3 class="font-bold text-slate-700 text-lg">Belum Ada Transaksi Selesai</h3>
    </div>
    @endforelse
</div>

<!-- Tab Content: Riwayat Chat -->
<div id="content-riwayat" class="space-y-4 hidden">
    @forelse($chatHistories as $req)
    <div class="bg-white rounded-[24px] p-6 border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex gap-4 items-center">
            <div class="w-16 h-16 bg-slate-100 rounded-2xl flex-shrink-0 overflow-hidden relative">
                @if($req->service->images->count() > 0)
                    <img src="{{ asset('storage/' . $req->service->images->first()->image_path) }}" class="w-full h-full object-cover" alt="Service">
                @else
                    <div class="w-full h-full flex items-center justify-center text-[10px] text-slate-400 font-bold">No Image</div>
                @endif
                @php
                    $unreadCount = $req->messages->where('sender_id', '!=', Auth::id())->where('is_read', false)->count();
                @endphp
                @if($unreadCount > 0)
                <div class="absolute inset-0 border-2 border-red-500 rounded-2xl"></div>
                @endif
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">
                    {{ $req->messages->first() ? $req->messages->first()->created_at->format('d M Y, H:i') : $req->created_at->format('d M Y, H:i') }}
                </p>
                <h3 class="font-bold text-slate-800 line-clamp-1 text-lg leading-tight flex items-center gap-2">
                    {{ $req->service->service_name }}
                    @if($unreadCount > 0)
                    <span class="bg-red-500 text-white text-[10px] px-2 py-0.5 rounded-full">Baru</span>
                    @endif
                </h3>
                <p class="text-sm font-medium text-slate-500 mt-1">Pelanggan: <span class="font-bold text-slate-700">{{ $req->buyer->userProfile->name ?? 'User' }}</span></p>
                
                @if($req->messages->first())
                <p class="text-xs text-slate-500 mt-2 line-clamp-1 italic">
                    "{{ $req->messages->first()->message ?? 'Menawarkan harga Rp ' . number_format($req->messages->first()->offered_price, 0, ',', '.') }}"
                </p>
                @endif
            </div>
        </div>
        <div class="flex-shrink-0 flex items-center gap-2">
            <a href="{{ route('negotiation.show', $req->id) }}" class="inline-block bg-white border border-gray-200 text-slate-600 hover:border-color1 hover:text-color1 font-bold px-6 py-3 rounded-xl transition-colors shadow-sm text-sm text-center">
                Buka Riwayat
            </a>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-[24px] p-10 border border-gray-100 text-center">
        <span class="text-4xl opacity-50 block mb-3">💬</span>
        <h3 class="font-bold text-slate-700 text-lg">Belum Ada Riwayat Chat</h3>
    </div>
    @endforelse
</div>

<script>
    function switchTab(tab) {
        const tabs = ['negosiasi', 'berjalan', 'selesai', 'riwayat'];
        tabs.forEach(t => {
            const btn = document.getElementById('tab-' + t);
            const content = document.getElementById('content-' + t);
            if (t === tab) {
                btn.className = "px-6 py-3 rounded-xl font-bold text-sm transition-all bg-color1 text-white shadow-lg shadow-color1/20 whitespace-nowrap";
                content.classList.remove('hidden');
            } else {
                btn.className = "px-6 py-3 rounded-xl font-bold text-sm transition-all text-slate-500 hover:bg-gray-100 whitespace-nowrap";
                content.classList.add('hidden');
            }
        });
    }

    async function deleteSellerConv(id) {
        if (!confirm('Hapus seluruh percakapan ini? Tindakan ini tidak bisa dibatalkan.')) return;
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        try {
            const res = await fetch(`/negotiation/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            });
            if (res.ok) {
                const el = document.getElementById(`seller-conv-${id}`);
                el.style.opacity = '0';
                el.style.transform = 'translateX(40px)';
                el.style.transition = 'all 0.3s ease';
                setTimeout(() => el.remove(), 300);
            }
        } catch (err) { console.error(err); }
    }
</script>
@endsection

