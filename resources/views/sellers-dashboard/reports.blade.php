@extends('sellers-dashboard.main')

@section('sellers_content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tighter">Laporan Pendapatan</h1>
        <p class="text-slate-400 mt-1 font-medium">Analisis pendapatan dan download laporan formal Anda.</p>
    </div>
    <a href="{{ route('seller.reports.export') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold px-6 py-3 rounded-2xl flex items-center gap-2 transition-all shadow-lg shadow-green-600/20">
        <span class="text-xl">📊</span> Download Excel
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-[32px] p-8 border border-gray-100 shadow-sm">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Total Seluruh Pendapatan</p>
        <h2 class="text-3xl font-black text-slate-800 tracking-tight">Rp {{ number_format($totalEarned, 0, ',', '.') }}</h2>
        <div class="mt-4 flex items-center gap-2 text-green-500 font-bold text-xs">
            <span class="bg-green-50 px-2 py-1 rounded-lg">↑ Aktif</span>
            <span>Dari transaksi selesai</span>
        </div>
    </div>
    <div class="bg-white rounded-[32px] p-8 border border-gray-100 shadow-sm">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Pendapatan Bulan Ini</p>
        <h2 class="text-3xl font-black text-color1 tracking-tight">Rp {{ number_format($thisMonthEarned, 0, ',', '.') }}</h2>
        <p class="text-slate-400 text-xs font-medium mt-4">Performa bulan {{ now()->format('F Y') }}</p>
    </div>
</div>

<div class="bg-white rounded-[40px] p-8 border border-gray-100 shadow-sm">
    <h3 class="text-lg font-black text-slate-800 mb-6 tracking-tight">Riwayat Transaksi Selesai</h3>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-gray-100">
                    <th class="py-4 px-4">No</th>
                    <th class="py-4 px-4">Layanan</th>
                    <th class="py-4 px-4">Pelanggan</th>
                    <th class="py-4 px-4">Tanggal Selesai</th>
                    <th class="py-4 px-4 text-right">Pendapatan (Rp)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 font-medium text-slate-600">
                @php $i = ($transactions->currentPage() - 1) * $transactions->perPage() + 1; @endphp
                @forelse($transactions as $tx)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="py-4 px-4 text-slate-400">{{ $i++ }}</td>
                    <td class="py-4 px-4 font-bold text-slate-800">{{ $tx->serviceRequest->service->service_name }}</td>
                    <td class="py-4 px-4">
                        <div class="flex flex-col">
                            <span>{{ $tx->serviceRequest->buyer->userProfile->name ?? '-' }}</span>
                            <span class="text-[10px] text-slate-400 font-normal">{{ $tx->serviceRequest->buyer->email }}</span>
                        </div>
                    </td>
                    <td class="py-4 px-4 text-xs">{{ $tx->completed_at ? \Carbon\Carbon::parse($tx->completed_at)->format('d M Y') : '-' }}</td>
                    <td class="py-4 px-4 text-right font-black text-slate-800">Rp {{ number_format($tx->base_price, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-10 text-center text-slate-400 font-medium">Belum ada transaksi yang selesai.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-8">
        {{ $transactions->links() }}
    </div>
</div>
@endsection
