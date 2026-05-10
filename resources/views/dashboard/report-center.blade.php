@extends('dashboard.main')

@section('admin_content')
<div class="mb-8 flex items-center justify-between flex-wrap gap-4">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tighter">Report Center</h1>
        <p class="text-slate-400 mt-1 font-medium">Kelola semua laporan masuk dan transaksi bermasalah di satu tempat.</p>
    </div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-700 rounded-2xl font-bold text-sm">
        {{ session('success') }}
    </div>
@endif

{{-- Stats Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <div class="bg-white rounded-[24px] p-6 border border-yellow-100 shadow-sm">
        <p class="text-xs font-bold text-yellow-500 uppercase tracking-widest mb-1">⏳ Menunggu Tindakan</p>
        <p class="text-4xl font-black text-yellow-600">{{ $pendingCount }}</p>
    </div>
    <div class="bg-white rounded-[24px] p-6 border border-red-100 shadow-sm">
        <p class="text-xs font-bold text-red-500 uppercase tracking-widest mb-1">⚠️ Transaksi Disputed</p>
        <p class="text-4xl font-black text-red-600">{{ $disputedCount }}</p>
    </div>
    <div class="bg-white rounded-[24px] p-6 border border-green-100 shadow-sm">
        <p class="text-xs font-bold text-green-500 uppercase tracking-widest mb-1">✅ Laporan Diselesaikan</p>
        <p class="text-4xl font-black text-green-600">{{ $resolvedReports->total() }}</p>
    </div>
</div>

{{-- Tabs --}}
<div class="flex gap-4 mb-6 border-b border-gray-100 pb-2 overflow-x-auto no-scrollbar">
    <button onclick="switchReportTab('active')" id="tab-active" class="px-6 py-3 rounded-xl font-bold text-sm transition-all bg-color1 text-white shadow-lg shadow-color1/20 whitespace-nowrap">
        Menunggu Tindakan ({{ $activeReports->total() }})
    </button>
    <button onclick="switchReportTab('disputed')" id="tab-disputed" class="px-6 py-3 rounded-xl font-bold text-sm transition-all text-slate-500 hover:bg-gray-100 whitespace-nowrap">
        Transaksi Bermasalah ({{ $disputedTransactions->total() }})
    </button>
    <button onclick="switchReportTab('resolved')" id="tab-resolved" class="px-6 py-3 rounded-xl font-bold text-sm transition-all text-slate-500 hover:bg-gray-100 whitespace-nowrap">
        Riwayat Laporan ({{ $resolvedReports->total() }})
    </button>
</div>

{{-- Tab Content: Active Reports --}}
<div id="content-active" class="space-y-4">
    <div class="bg-white rounded-[24px] border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            @include('dashboard.components.reports-table', ['reports' => $activeReports, 'emptyMessage' => 'Tidak ada laporan yang menunggu tindakan'])
        </div>
        @if($activeReports->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $activeReports->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Tab Content: Disputed Transactions --}}
<div id="content-disputed" class="space-y-4 hidden">
    <div class="bg-white rounded-[24px] border border-gray-100 shadow-sm overflow-hidden p-6">
        <h2 class="text-xl font-black text-slate-800 tracking-tighter mb-4">Transaksi Bermasalah (Disputed)</h2>
        <p class="text-sm text-slate-500 mb-6">Transaksi di bawah ini otomatis terhenti karena salah satu pihak (seller/buyer) sedang diblokir atau ada masalah lain. Tinjau dan ambil keputusan.</p>

        @forelse($disputedTransactions as $tx)
        <div class="border border-red-100 rounded-2xl p-5 mb-4 hover:shadow-md transition-all bg-red-50/20">
            <div class="flex flex-col md:flex-row justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest">
                            ID: #{{ $tx->id }}
                        </span>
                        <span class="text-xs text-slate-400 font-medium">
                            Sejak {{ $tx->disputed_at ? \Carbon\Carbon::parse($tx->disputed_at)->format('d M Y, H:i') : $tx->updated_at->format('d M Y, H:i') }}
                        </span>
                    </div>
                    <h3 class="font-bold text-slate-800 text-lg leading-tight">{{ $tx->serviceRequest->service->service_name }}</h3>
                    <p class="text-sm font-medium text-slate-500 mt-1 flex gap-4">
                        <span>Pembeli: <strong class="text-slate-700">{{ $tx->serviceRequest->buyer->userProfile->name ?? 'User' }}</strong></span>
                        <span>Penjual: <strong class="text-color1">{{ $tx->serviceRequest->seller->sellerProfile->brand_name ?? 'Seller' }}</strong></span>
                    </p>
                    <p class="text-xl font-black text-slate-800 mt-3">Rp {{ number_format($tx->final_price, 0, ',', '.') }}</p>
                </div>

                <div class="flex flex-col gap-2 min-w-[200px]">
                    <form action="{{ route('admin.disputed-transactions.resolve', $tx->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="action" value="resume">
                        <button type="submit" class="w-full px-4 py-2 bg-green-500 hover:bg-green-600 text-white font-bold rounded-xl text-sm transition-all shadow-sm" onclick="return confirm('Lanjutkan transaksi ini secara normal?')">
                            Lanjutkan (Resume)
                        </button>
                    </form>
                    <form action="{{ route('admin.disputed-transactions.resolve', $tx->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="action" value="cancel">
                        <button type="submit" class="w-full px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 font-bold rounded-xl text-sm transition-all border border-red-100" onclick="return confirm('Batalkan transaksi ini untuk direfund?')">
                            Batalkan (Refund)
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-16">
            <span class="text-4xl block mb-3 opacity-30">✨</span>
            <p class="font-bold text-slate-400 text-lg">Semua Aman</p>
            <p class="text-slate-400 text-sm">Tidak ada transaksi yang bermasalah saat ini.</p>
        </div>
        @endforelse

        @if($disputedTransactions->hasPages())
        <div class="mt-6">
            {{ $disputedTransactions->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Tab Content: Resolved Reports --}}
<div id="content-resolved" class="space-y-4 hidden">
    <div class="bg-white rounded-[24px] border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            @include('dashboard.components.reports-table', ['reports' => $resolvedReports, 'emptyMessage' => 'Belum ada riwayat laporan yang terselesaikan'])
        </div>
        @if($resolvedReports->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $resolvedReports->links() }}
        </div>
        @endif
    </div>
</div>

<script>
    function switchReportTab(tab) {
        // Hide all contents
        document.getElementById('content-active').classList.add('hidden');
        document.getElementById('content-disputed').classList.add('hidden');
        document.getElementById('content-resolved').classList.add('hidden');
        
        // Reset tab styles
        const tabs = ['tab-active', 'tab-disputed', 'tab-resolved'];
        tabs.forEach(t => {
            document.getElementById(t).className = 'px-6 py-3 rounded-xl font-bold text-sm transition-all text-slate-500 hover:bg-gray-100 whitespace-nowrap';
        });

        // Show selected content
        document.getElementById('content-' + tab).classList.remove('hidden');
        
        // Active tab style
        document.getElementById('tab-' + tab).className = 'px-6 py-3 rounded-xl font-bold text-sm transition-all bg-color1 text-white shadow-lg shadow-color1/20 whitespace-nowrap';
    }

    // Check URL parameters for pagination to keep tab active
    const urlParams = new URLSearchParams(window.location.search);
    if(urlParams.has('disputed_page')) switchReportTab('disputed');
    if(urlParams.has('resolved_page')) switchReportTab('resolved');
</script>
@endsection
