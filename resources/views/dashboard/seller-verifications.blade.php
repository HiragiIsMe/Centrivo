@extends('dashboard.main')

@section('admin_content')
<div class="mb-8 flex items-center justify-between flex-wrap gap-4">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tighter">Verifikasi Seller</h1>
        <p class="text-slate-400 mt-1 font-medium">Tinjau dan kelola pengajuan verifikasi identitas dari para seller.</p>
    </div>
</div>

{{-- Flash --}}
@if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-700 rounded-2xl font-bold text-sm">
        ✅ {{ session('success') }}
    </div>
@endif

{{-- Stats --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <div class="bg-white rounded-[24px] p-6 border border-yellow-100 shadow-sm">
        <p class="text-xs font-bold text-yellow-500 uppercase tracking-widest mb-1">⏳ Menunggu Review</p>
        <p class="text-4xl font-black text-yellow-600">{{ $pendingVerifications->count() }}</p>
    </div>
    <div class="bg-white rounded-[24px] p-6 border border-green-100 shadow-sm">
        <p class="text-xs font-bold text-green-500 uppercase tracking-widest mb-1">✅ Terverifikasi</p>
        <p class="text-4xl font-black text-green-600">{{ $verifiedSellers->count() }}</p>
    </div>
    <div class="bg-white rounded-[24px] p-6 border border-red-100 shadow-sm">
        <p class="text-xs font-bold text-red-500 uppercase tracking-widest mb-1">❌ Ditolak</p>
        <p class="text-4xl font-black text-red-600">{{ $rejectedSellers->count() }}</p>
    </div>
</div>

{{-- Tabs --}}
<div class="flex gap-4 mb-6 border-b border-gray-100 pb-2 overflow-x-auto no-scrollbar">
    <button onclick="switchVerifTab('pending')" id="tab-pending" class="px-6 py-3 rounded-xl font-bold text-sm transition-all bg-color1 text-white shadow-lg shadow-color1/20 whitespace-nowrap">
        Menunggu Review ({{ $pendingVerifications->count() }})
    </button>
    <button onclick="switchVerifTab('verified')" id="tab-verified" class="px-6 py-3 rounded-xl font-bold text-sm transition-all text-slate-500 hover:bg-gray-100 whitespace-nowrap">
        Terverifikasi ({{ $verifiedSellers->count() }})
    </button>
    <button onclick="switchVerifTab('rejected')" id="tab-rejected" class="px-6 py-3 rounded-xl font-bold text-sm transition-all text-slate-500 hover:bg-gray-100 whitespace-nowrap">
        Ditolak ({{ $rejectedSellers->count() }})
    </button>
</div>

{{-- TAB: Pending --}}
<div id="content-pending" class="space-y-6">
    @forelse($pendingVerifications as $sp)
    @include('dashboard.components.seller-kyc-card', ['sp' => $sp, 'showActions' => true])
    @empty
    <div class="bg-white rounded-[24px] p-16 border border-gray-100 text-center">
        <span class="text-5xl block mb-4 opacity-30">🎉</span>
        <p class="font-bold text-slate-400 text-lg">Tidak ada pengajuan yang menunggu review.</p>
    </div>
    @endforelse
</div>

{{-- TAB: Verified --}}
<div id="content-verified" class="space-y-6 hidden">
    @forelse($verifiedSellers as $sp)
    @include('dashboard.components.seller-kyc-card', ['sp' => $sp, 'showActions' => false])
    @empty
    <div class="bg-white rounded-[24px] p-16 border border-gray-100 text-center">
        <span class="text-5xl block mb-4 opacity-30">✅</span>
        <p class="font-bold text-slate-400 text-lg">Belum ada seller yang terverifikasi.</p>
    </div>
    @endforelse
</div>

{{-- TAB: Rejected --}}
<div id="content-rejected" class="space-y-6 hidden">
    @forelse($rejectedSellers as $sp)
    @include('dashboard.components.seller-kyc-card', ['sp' => $sp, 'showActions' => false])
    @empty
    <div class="bg-white rounded-[24px] p-16 border border-gray-100 text-center">
        <span class="text-5xl block mb-4 opacity-30">✨</span>
        <p class="font-bold text-slate-400 text-lg">Tidak ada pengajuan yang ditolak.</p>
    </div>
    @endforelse
</div>

<script>
function switchVerifTab(tab) {
    ['pending','verified','rejected'].forEach(t => {
        document.getElementById('content-' + t).classList.add('hidden');
        document.getElementById('tab-' + t).className = 'px-6 py-3 rounded-xl font-bold text-sm transition-all text-slate-500 hover:bg-gray-100 whitespace-nowrap';
    });
    document.getElementById('content-' + tab).classList.remove('hidden');
    document.getElementById('tab-' + tab).className = 'px-6 py-3 rounded-xl font-bold text-sm transition-all bg-color1 text-white shadow-lg shadow-color1/20 whitespace-nowrap';
}
</script>
@endsection
