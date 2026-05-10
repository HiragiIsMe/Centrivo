@extends('dashboard.main')

@section('admin_content')
<div class="mb-6">
    <a href="{{ route('admin.report-center.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-400 hover:text-color1 transition-colors mb-4">
        ← Kembali ke Report Center
    </a>
    <div class="flex items-start justify-between flex-wrap gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <h1 class="text-3xl font-black text-slate-800 tracking-tighter">Detail Laporan</h1>
                @if($report->report_code)
                    <code class="text-sm font-black text-color1 bg-color1/10 px-3 py-1.5 rounded-xl">{{ $report->report_code }}</code>
                @endif
                @if($report->isBanReport())
                    <span class="text-xs font-bold bg-red-100 text-red-500 px-3 py-1.5 rounded-xl">🚫 BAN REPORT</span>
                @endif
            </div>
            <p class="text-slate-400 font-medium">Diterima: {{ $report->created_at->format('d M Y, H:i') }}</p>
        </div>

        {{-- Status Badge --}}
        @php
            $statusColor = match($report->status) {
                'pending'  => 'bg-yellow-100 text-yellow-600 border-yellow-200',
                'reviewed' => 'bg-blue-100 text-blue-600 border-blue-200',
                'resolved' => 'bg-green-100 text-green-600 border-green-200',
                default    => 'bg-gray-100 text-gray-500 border-gray-200',
            };
        @endphp
        <span class="text-sm font-black uppercase px-4 py-2 rounded-xl border {{ $statusColor }}">
            {{ $report->status }}
        </span>
    </div>
</div>

@if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-700 rounded-2xl font-bold text-sm">
        {{ session('success') }}
    </div>
@endif

<div class="grid lg:grid-cols-3 gap-6">

    {{-- Kolom Kiri: Info Laporan --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Pelapor & Target --}}
        <div class="bg-white rounded-[24px] p-6 border border-gray-100 shadow-sm">
            <h2 class="text-sm font-black text-slate-400 uppercase tracking-widest mb-5">Pihak yang Terlibat</h2>

            <div class="grid sm:grid-cols-2 gap-4">
                {{-- Pelapor --}}
                <div class="bg-gray-50 rounded-2xl p-4">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Pelapor</p>
                    @if($report->reporter)
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-color1 rounded-xl flex items-center justify-center text-white font-black text-sm">
                            {{ substr($report->reporter->userProfile->name ?? $report->reporter->sellerProfile->brand_name ?? 'A', 0, 2) }}
                        </div>
                        <div>
                            <p class="font-bold text-slate-800 text-sm">
                                {{ $report->reporter->userProfile->name ?? $report->reporter->sellerProfile->brand_name ?? $report->reporter->email }}
                            </p>
                            <p class="text-[10px] text-slate-400 font-bold uppercase">{{ $report->reporter->role }}</p>
                            <p class="text-xs text-slate-400">{{ $report->reporter->email }}</p>
                        </div>
                    </div>
                    @else
                        <p class="text-sm font-bold text-slate-500 italic">🛡️ Administrator (System)</p>
                    @endif
                </div>

                {{-- Yang Dilaporkan --}}
                <div class="bg-red-50 rounded-2xl p-4 border border-red-100">
                    <p class="text-[10px] font-bold text-red-400 uppercase tracking-widest mb-3">Dilaporkan</p>
                    @if($report->reportedUser)
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-red-200 rounded-xl flex items-center justify-center text-red-600 font-black text-sm">
                            {{ substr($report->reportedUser->userProfile->name ?? $report->reportedUser->sellerProfile->brand_name ?? 'U', 0, 2) }}
                        </div>
                        <div>
                            <p class="font-bold text-slate-800 text-sm">
                                {{ $report->reportedUser->userProfile->name ?? $report->reportedUser->sellerProfile->brand_name ?? $report->reportedUser->email }}
                            </p>
                            <p class="text-[10px] text-slate-400 font-bold uppercase">{{ $report->reportedUser->role }}</p>
                            <p class="text-xs text-slate-400">{{ $report->reportedUser->email }}</p>
                            @if($report->reportedUser->is_banned)
                                <span class="text-[10px] font-bold bg-red-100 text-red-500 px-2 py-0.5 rounded-lg">Sudah Di-ban</span>
                            @endif
                        </div>
                    </div>
                    @endif
                    @if($report->reportedService)
                    <div class="flex items-center gap-2 bg-white rounded-xl p-3">
                        <span class="text-lg">📦</span>
                        <div>
                            <p class="font-bold text-slate-800 text-xs">{{ $report->reportedService->service_name }}</p>
                            <p class="text-[10px] text-slate-400">Service ID #{{ $report->reportedService->id }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Isi Laporan --}}
        <div class="bg-white rounded-[24px] p-6 border border-gray-100 shadow-sm">
            <h2 class="text-sm font-black text-slate-400 uppercase tracking-widest mb-5">Isi Laporan</h2>

            <div class="space-y-4">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Kategori Alasan</p>
                    <p class="font-bold text-slate-800 text-base">{{ $report->reason }}</p>
                </div>

                @if($report->description)
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Deskripsi</p>
                    <div class="bg-gray-50 rounded-2xl p-4 text-sm text-slate-600 leading-relaxed whitespace-pre-wrap">{{ $report->description }}</div>
                </div>
                @endif

                @if($report->ban_reason)
                <div>
                    <p class="text-xs font-bold text-red-400 uppercase tracking-widest mb-1">Alasan Ban (oleh Admin)</p>
                    <div class="bg-red-50 border border-red-100 rounded-2xl p-4 text-sm text-slate-600 leading-relaxed">{{ $report->ban_reason }}</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Transaksi Terkait --}}
        @if($report->relatedTransaction)
        <div class="bg-white rounded-[24px] p-6 border {{ $report->relatedTransaction->is_disputed ? 'border-red-200' : 'border-gray-100' }} shadow-sm">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-sm font-black text-slate-400 uppercase tracking-widest">Transaksi Terkait</h2>
                @if($report->relatedTransaction->is_disputed)
                    <span class="text-xs font-bold bg-red-100 text-red-500 px-3 py-1 rounded-xl">⚠️ Disputed</span>
                @else
                    <span class="text-xs font-bold bg-green-100 text-green-600 px-3 py-1 rounded-xl">✓ Normal</span>
                @endif
            </div>

            @php $tx = $report->relatedTransaction; @endphp
            <div class="grid sm:grid-cols-2 gap-4 text-sm">
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">ID Transaksi</p>
                    <p class="font-bold text-slate-800">#{{ $tx->id }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Status Transaksi</p>
                    <p class="font-bold text-slate-800">{{ ucfirst($tx->transaction_status) }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Status Pembayaran</p>
                    <p class="font-bold text-slate-800">{{ ucfirst($tx->payment_status) }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Total Nilai</p>
                    <p class="font-bold text-slate-800">Rp {{ number_format($tx->final_price, 0, ',', '.') }}</p>
                </div>

                @if($tx->serviceRequest)
                <div class="bg-gray-50 rounded-xl p-3 sm:col-span-2">
                    <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Jasa</p>
                    <p class="font-bold text-slate-800">{{ $tx->serviceRequest->service->service_name ?? '-' }}</p>
                    <div class="flex gap-4 mt-2 text-xs text-slate-500">
                        <span>Pembeli: <b>{{ $tx->serviceRequest->buyer->userProfile->name ?? '-' }}</b></span>
                        <span>Seller: <b>{{ $tx->serviceRequest->seller->sellerProfile->brand_name ?? '-' }}</b></span>
                    </div>
                </div>
                @endif

                @if($tx->is_disputed && $tx->disputed_at)
                <div class="bg-red-50 border border-red-100 rounded-xl p-3 sm:col-span-2">
                    <p class="text-[10px] font-bold text-red-400 uppercase mb-1">Ditandai Disputed</p>
                    <p class="text-xs text-slate-600">{{ \Carbon\Carbon::parse($tx->disputed_at)->format('d M Y, H:i') }}
                    — karena <b>{{ $tx->disputed_by === 'user_ban' ? 'User di-ban' : ($tx->disputed_by === 'seller_ban' ? 'Seller di-ban' : $tx->disputed_by) }}</b></p>
                </div>
                @endif
            </div>

            @if($report->status !== 'resolved' && $tx->is_disputed)
            <form method="POST" action="{{ route('admin.report-center.resolve', $report) }}" class="mt-4">
                @csrf
                <button type="submit"
                        onclick="return confirm('Ini akan menandai laporan sebagai resolved dan memulihkan status transaksi. Lanjutkan?')"
                        class="w-full py-3 bg-green-500 hover:bg-green-600 text-white font-bold rounded-2xl text-sm transition-all shadow-sm">
                    ✓ Resolve & Pulihkan Transaksi
                </button>
            </form>
            @endif
        </div>
        @endif

    </div>

    {{-- Kolom Kanan: Aksi Admin --}}
    <div class="space-y-6">

        {{-- Update Status --}}
        @if($report->status !== 'resolved')
        <div class="bg-white rounded-[24px] p-6 border border-gray-100 shadow-sm">
            <h2 class="text-sm font-black text-slate-400 uppercase tracking-widest mb-5">Update Status</h2>
            <form method="POST" action="{{ route('admin.report-center.status', $report) }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Status Laporan</label>
                    <select name="status" class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-4 py-3 text-sm font-medium outline-none focus:ring-2 focus:ring-color1/20">
                        <option value="pending"  {{ $report->status === 'pending'  ? 'selected' : '' }}>Pending</option>
                        <option value="reviewed" {{ $report->status === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                        <option value="resolved" {{ $report->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Catatan Admin</label>
                    <textarea name="admin_notes" rows="4"
                              class="w-full bg-gray-50 border border-gray-100 rounded-2xl p-4 text-sm font-medium outline-none focus:ring-2 focus:ring-color1/20 transition-all resize-none"
                              placeholder="Tambahkan catatan internal...">{{ $report->admin_notes }}</textarea>
                </div>
                <button type="submit" class="w-full py-3 bg-color1 hover:bg-color2 text-white font-bold rounded-2xl text-sm transition-all shadow-sm">
                    Simpan Perubahan
                </button>
            </form>
        </div>
        @else
        <div class="bg-green-50 rounded-[24px] p-6 border border-green-100">
            <p class="text-sm font-bold text-green-600 text-center">✅ Laporan ini sudah diselesaikan</p>
            @if($report->admin_notes)
            <div class="mt-3 pt-3 border-t border-green-100">
                <p class="text-xs font-bold text-green-400 uppercase tracking-widest mb-1">Catatan Admin</p>
                <p class="text-sm text-slate-600">{{ $report->admin_notes }}</p>
            </div>
            @endif
        </div>
        @endif

        {{-- Aksi Cepat User --}}
        @if($report->reportedUser)
        <div class="bg-white rounded-[24px] p-6 border border-gray-100 shadow-sm">
            <h2 class="text-sm font-black text-slate-400 uppercase tracking-widest mb-5">Aksi User</h2>
            @if(!$report->reportedUser->is_banned)
                <button onclick="document.getElementById('banModal').classList.remove('hidden')" 
                   class="block w-full text-center py-3 bg-red-50 hover:bg-red-100 text-red-500 font-bold rounded-2xl text-sm transition-all border border-red-100">
                    🔒 Ban User Ini
                </button>

                {{-- Modal Ban --}}
                <div id="banModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                    <div class="bg-white rounded-[32px] max-w-md w-full p-8 shadow-2xl relative">
                        <button onclick="document.getElementById('banModal').classList.add('hidden')" class="absolute top-6 right-6 text-slate-400 hover:text-slate-600 text-xl font-bold">✕</button>
                        
                        <div class="w-16 h-16 bg-red-100 rounded-2xl flex items-center justify-center text-3xl mb-6">
                            🔒
                        </div>
                        <h2 class="text-2xl font-black text-slate-800 mb-2">Ban Pengguna</h2>
                        <p class="text-slate-500 text-sm mb-6">Tindakan ini akan memblokir pengguna secara permanen dan menangguhkan seluruh transaksi aktif mereka (menjadi Disputed).</p>
                        
                        <form method="POST" action="{{ route('users.ban', $report->reportedUser) }}">
                            @csrf
                            <input type="hidden" name="report_code" value="{{ $report->report_code }}">
                            <div class="mb-6">
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Alasan Ban</label>
                                <textarea name="ban_reason" required rows="3"
                                        class="w-full bg-gray-50 border border-gray-100 rounded-2xl p-4 text-sm font-medium outline-none focus:ring-2 focus:ring-red-500/20 transition-all resize-none"
                                        placeholder="Tuliskan alasan pemblokiran pengguna secara detail..."></textarea>
                            </div>
                            
                            <div class="flex gap-4">
                                <button type="button" onclick="document.getElementById('banModal').classList.add('hidden')" class="flex-1 py-4 text-slate-500 font-bold rounded-2xl text-sm hover:bg-gray-50 transition-colors">
                                    Batal
                                </button>
                                <button type="submit" class="flex-1 py-4 bg-red-500 hover:bg-red-600 text-white font-bold rounded-2xl text-sm shadow-lg shadow-red-500/20 transition-all">
                                    Ya, Ban User
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            @else
                <div class="text-center">
                    <p class="text-sm font-bold text-red-500 mb-3">🚫 User ini sudah di-ban</p>
                    <a href="{{ route('admin.report-center.index', ['disputed_page' => 1]) }}" class="block w-full py-3 mb-3 bg-red-50 hover:bg-red-100 text-red-600 font-bold rounded-2xl text-sm transition-all border border-red-100">
                        ⚠️ Lihat Transaksi Disputed
                    </a>
                    <form method="POST" action="{{ route('users.unban', $report->reportedUser) }}">
                        @csrf
                        <button type="submit" class="w-full py-3 bg-green-50 hover:bg-green-100 text-green-600 font-bold rounded-2xl text-sm transition-all border border-green-100">
                            ✓ Unban User
                        </button>
                    </form>
                </div>
            @endif
        </div>
        @endif

        {{-- Aksi Cepat Service --}}
        @if($report->reportedService)
        <div class="bg-white rounded-[24px] p-6 border border-gray-100 shadow-sm mt-6">
            <h2 class="text-sm font-black text-slate-400 uppercase tracking-widest mb-5">Aksi Layanan</h2>
            @if(!$report->reportedService->is_banned)
                <button onclick="document.getElementById('banServiceModal').classList.remove('hidden')" 
                   class="block w-full text-center py-3 bg-red-50 hover:bg-red-100 text-red-500 font-bold rounded-2xl text-sm transition-all border border-red-100">
                    🔒 Ban Layanan Ini
                </button>

                {{-- Modal Ban Service --}}
                <div id="banServiceModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                    <div class="bg-white rounded-[32px] max-w-md w-full p-8 shadow-2xl relative">
                        <button onclick="document.getElementById('banServiceModal').classList.add('hidden')" class="absolute top-6 right-6 text-slate-400 hover:text-slate-600 text-xl font-bold">✕</button>
                        
                        <div class="w-16 h-16 bg-red-100 rounded-2xl flex items-center justify-center text-3xl mb-6">
                            📦
                        </div>
                        <h2 class="text-2xl font-black text-slate-800 mb-2">Ban Layanan</h2>
                        <p class="text-slate-500 text-sm mb-6">Tindakan ini akan menyembunyikan layanan dari marketplace dan menangguhkan seluruh pesanan terkait (menjadi Disputed).</p>
                        
                        <form method="POST" action="{{ route('admin.services.ban', $report->reportedService) }}">
                            @csrf
                            <input type="hidden" name="report_code" value="{{ $report->report_code }}">
                            <div class="mb-6">
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Alasan Ban</label>
                                <textarea name="ban_reason" required rows="3"
                                        class="w-full bg-gray-50 border border-gray-100 rounded-2xl p-4 text-sm font-medium outline-none focus:ring-2 focus:ring-red-500/20 transition-all resize-none"
                                        placeholder="Tuliskan alasan pemblokiran layanan..."></textarea>
                            </div>
                            
                            <div class="flex gap-4">
                                <button type="button" onclick="document.getElementById('banServiceModal').classList.add('hidden')" class="flex-1 py-4 text-slate-500 font-bold rounded-2xl text-sm hover:bg-gray-50 transition-colors">
                                    Batal
                                </button>
                                <button type="submit" class="flex-1 py-4 bg-red-500 hover:bg-red-600 text-white font-bold rounded-2xl text-sm shadow-lg shadow-red-500/20 transition-all">
                                    Ya, Ban Layanan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            @else
                <div class="text-center">
                    <p class="text-sm font-bold text-red-500 mb-3">🚫 Layanan ini sudah di-ban</p>
                    <a href="{{ route('admin.report-center.index', ['disputed_page' => 1]) }}" class="block w-full py-3 mb-3 bg-red-50 hover:bg-red-100 text-red-600 font-bold rounded-2xl text-sm transition-all border border-red-100">
                        ⚠️ Lihat Transaksi Disputed
                    </a>
                    <form method="POST" action="{{ route('admin.services.unban', $report->reportedService) }}">
                        @csrf
                        <button type="submit" class="w-full py-3 bg-green-50 hover:bg-green-100 text-green-600 font-bold rounded-2xl text-sm transition-all border border-green-100">
                            ✓ Unban Layanan
                        </button>
                    </form>
                </div>
            @endif
        </div>
        @endif

        {{-- Kode Laporan (copy) --}}
        @if($report->report_code)
        <div class="bg-slate-50 rounded-[24px] p-6 border border-gray-100">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Kode Laporan</p>
            <div class="flex items-center justify-between bg-white rounded-2xl px-4 py-3 border border-gray-100">
                <code class="font-black text-color1 text-sm tracking-wider">{{ $report->report_code }}</code>
                <button onclick="navigator.clipboard.writeText('{{ $report->report_code }}')" class="text-slate-400 hover:text-color1 transition">📋</button>
            </div>
            <p class="text-xs text-slate-400 mt-2">Kode ini diberikan kepada user untuk follow-up via WA admin.</p>
        </div>
        @endif

    </div>
</div>
@endsection
