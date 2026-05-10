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
        Riwayat Pesanan ({{ $completedTransactions->count() }})
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
                <div class="flex items-center gap-2 mb-1">
                    <p class="text-[10px] font-bold text-blue-600 uppercase tracking-widest">ID Transaksi: #{{ $tx->id }}</p>
                    @if($tx->is_disputed)
                        <span class="text-[10px] font-black bg-red-100 text-red-500 px-2 py-0.5 rounded-lg border border-red-200 uppercase">⚠️ Bermasalah</span>
                    @endif
                </div>
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
            @if($tx->is_disputed)
                @php
                    $waMessage = urlencode("Halo admin, transaksi saya #" . $tx->id . " bermasalah (Disputed). Mohon bantuannya.");
                    $waUrl = "https://wa.me/" . ($global_settings['admin_whatsapp'] ?? '628123456789') . "?text=" . $waMessage;
                @endphp
                <a href="{{ $waUrl }}" target="_blank" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-bold rounded-xl text-xs transition-colors shadow-sm text-center">
                    Hubungi Admin
                </a>
                <p class="text-[10px] font-bold text-red-500">Menunggu Review Admin</p>
            @else
                <a href="{{ route('negotiation.show', $tx->serviceRequest->id) }}" class="text-sm font-bold text-color1 hover:underline">Chat Pelanggan</a>
                <button onclick="openReportUserModal({{ $tx->serviceRequest->buyer->id }}, '{{ addslashes($tx->serviceRequest->buyer->userProfile->name ?? 'User') }}')" 
                        class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-500 font-bold rounded-xl text-xs transition-colors border border-red-100">
                    🚩 Laporkan Pelanggan
                </button>
            @endif
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
            @if($tx->transaction_status == 'cancelled')
                <div class="w-16 h-16 bg-red-100 text-red-500 rounded-2xl flex-shrink-0 flex items-center justify-center text-2xl font-bold">
                    ✕
                </div>
                <div>
                    <p class="text-[10px] font-bold text-red-600 uppercase tracking-widest mb-1">ID Transaksi: #{{ $tx->id }} (DIBATALKAN)</p>
            @else
                <div class="w-16 h-16 bg-green-100 text-green-500 rounded-2xl flex-shrink-0 flex items-center justify-center text-2xl font-bold">
                    ✓
                </div>
                <div>
                    <p class="text-[10px] font-bold text-green-600 uppercase tracking-widest mb-1">ID Transaksi: #{{ $tx->id }}</p>
            @endif
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
        <span class="text-4xl opacity-50 block mb-3">📚</span>
        <h3 class="font-bold text-slate-700 text-lg">Belum Ada Riwayat Pesanan</h3>
        <p class="text-slate-400 mt-2 text-sm">Anda belum memiliki transaksi yang selesai atau dibatalkan.</p>
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

<!-- Report User Modal -->
<div id="reportUserModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-[32px] p-8 w-full max-w-md shadow-2xl transform scale-95 transition-transform duration-300" id="reportUserModalBox">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center gap-3">
                <span class="text-2xl">🚩</span>
                <h3 class="text-xl font-black text-slate-800">Laporkan Pelanggan</h3>
            </div>
            <button onclick="closeReportUserModal()" class="text-slate-400 hover:text-red-500 font-bold text-xl">&times;</button>
        </div>
        <p class="text-sm text-slate-500 mb-1 font-medium">Melaporkan: <span id="reportUserName" class="font-black text-slate-800"></span></p>
        <p class="text-xs text-slate-400 mb-6">Laporan akan ditinjau oleh tim Centrivo dalam waktu dekat.</p>

        <form method="POST" action="{{ route('seller.report.user') }}">
            @csrf
            <input type="hidden" name="reported_user_id" id="reportUserId">

            <div class="mb-5">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Kategori Masalah</label>
                <select name="reason" required class="w-full bg-gray-50 border border-gray-100 rounded-2xl p-4 text-sm font-medium outline-none focus:ring-2 focus:ring-red-200 transition-all">
                    <option value="" disabled selected>Pilih kategori...</option>
                    <option value="Tidak kooperatif">Tidak kooperatif / Tidak datang</option>
                    <option value="Pembayaran bermasalah">Masalah pembayaran</option>
                    <option value="Perilaku tidak sopan">Perilaku tidak sopan / Kasar</option>
                    <option value="Penipuan">Indikasi penipuan</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Detail Laporan (Opsional)</label>
                <textarea name="description" rows="4" class="w-full bg-gray-50 border border-gray-100 rounded-2xl p-4 text-sm font-medium outline-none focus:ring-2 focus:ring-red-200 transition-all resize-none" placeholder="Ceritakan apa yang terjadi..."></textarea>
            </div>

            <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-4 rounded-2xl transition-all shadow-lg shadow-red-500/20">
                Kirim Laporan
            </button>
        </form>
    </div>
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

    function openReportUserModal(userId, userName) {
        document.getElementById('reportUserId').value = userId;
        document.getElementById('reportUserName').innerText = userName;
        const modal = document.getElementById('reportUserModal');
        const box = document.getElementById('reportUserModalBox');
        modal.classList.remove('hidden');
        void modal.offsetWidth;
        modal.classList.remove('opacity-0');
        box.classList.remove('scale-95');
    }

    function closeReportUserModal() {
        const modal = document.getElementById('reportUserModal');
        const box = document.getElementById('reportUserModalBox');
        modal.classList.add('opacity-0');
        box.classList.add('scale-95');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }
</script>
@endsection

