@extends('dashboard.main')

@section('admin_content')
<div class="mb-6 flex flex-col md:flex-row items-center justify-between gap-4">
    <div>
        <h2 class="text-3xl font-black text-slate-800">Service Transactions</h2>
        <p class="text-slate-400 font-medium">Pantau dan kelola seluruh transaksi layanan yang sedang berlangsung maupun telah selesai.</p>
    </div>
</div>

<div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden mb-6">
    <!-- Filter & Search -->
    <div class="p-6 border-b border-gray-100 bg-gray-50/50">
        <form method="GET" action="{{ route('admin.service.transactions') }}" class="flex flex-col sm:flex-row gap-3">
            <select name="status" class="rounded-2xl border border-gray-200 px-4 py-3 focus:outline-none focus:border-color1 focus:ring-2 focus:ring-color1/20 transition-all font-medium text-slate-600 bg-white min-w-[150px]">
                <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>Semua Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Accepted</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            
            <select name="payment" class="rounded-2xl border border-gray-200 px-4 py-3 focus:outline-none focus:border-color1 focus:ring-2 focus:ring-color1/20 transition-all font-medium text-slate-600 bg-white min-w-[150px]">
                <option value="all" {{ request('payment') === 'all' ? 'selected' : '' }}>Semua Pembayaran</option>
                <option value="pending" {{ request('payment') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="paid" {{ request('payment') === 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="failed" {{ request('payment') === 'failed' ? 'selected' : '' }}>Failed</option>
            </select>

            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama layanan..." 
                   class="flex-1 rounded-2xl border border-gray-200 px-4 py-3 focus:outline-none focus:border-color1 focus:ring-2 focus:ring-color1/20 transition-all">
            
            <button type="submit" class="bg-color1 hover:bg-color2 text-white px-6 py-3 rounded-2xl font-bold transition-all shadow-lg shadow-color1/20 whitespace-nowrap">
                Filter Data
            </button>
            <a href="{{ route('admin.service.transactions') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-6 py-3 rounded-2xl font-bold transition-all whitespace-nowrap text-center">
                Reset
            </a>
        </form>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[1000px]">
            <thead>
                <tr class="bg-gray-50 text-slate-500 text-sm uppercase tracking-wider">
                    <th class="p-4 font-bold border-b border-gray-100">ID / Date</th>
                    <th class="p-4 font-bold border-b border-gray-100">Service Info</th>
                    <th class="p-4 font-bold border-b border-gray-100">Buyer & Seller</th>
                    <th class="p-4 font-bold border-b border-gray-100">Amount & Method</th>
                    <th class="p-4 font-bold border-b border-gray-100">Tx Status</th>
                    <th class="p-4 font-bold border-b border-gray-100">Payment Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($transactions as $tx)
                <tr class="hover:bg-gray-50 transition-all">
                    <td class="p-4">
                        <p class="font-black text-slate-800">#{{ $tx->id }}</p>
                        <p class="text-xs text-slate-500 font-medium mt-1">{{ $tx->created_at->format('d M Y H:i') }}</p>
                    </td>
                    <td class="p-4">
                        <p class="font-bold text-slate-700">{{ $tx->serviceRequest->service->service_name ?? 'N/A' }}</p>
                    </td>
                    <td class="p-4">
                        <p class="text-sm font-medium text-slate-800"><span class="text-xs text-slate-400">B:</span> {{ $tx->serviceRequest->buyer->userProfile->name ?? 'N/A' }}</p>
                        <p class="text-sm font-medium text-slate-800 mt-1"><span class="text-xs text-slate-400">S:</span> {{ $tx->serviceRequest->seller->sellerProfile->brand_name ?? 'N/A' }}</p>
                    </td>
                    <td class="p-4">
                        <p class="font-bold text-slate-800 text-lg">Rp {{ number_format($tx->final_price, 0, ',', '.') }}</p>
                        <p class="text-xs font-bold text-color1 uppercase tracking-widest mt-1">{{ $tx->payment_method }}</p>
                    </td>
                    <td class="p-4">
                        @php
                            $txStatusColors = [
                                'pending' => 'bg-orange-100 text-orange-600',
                                'accepted' => 'bg-blue-100 text-blue-600',
                                'completed' => 'bg-green-100 text-green-600',
                                'cancelled' => 'bg-red-100 text-red-600',
                            ];
                            $txColor = $txStatusColors[$tx->transaction_status] ?? 'bg-gray-100 text-gray-600';
                        @endphp
                        <span class="px-3 py-1 {{ $txColor }} rounded-full text-xs font-bold uppercase tracking-wider">
                            {{ $tx->transaction_status }}
                        </span>
                    </td>
                    <td class="p-4">
                        @php
                            $pyStatusColors = [
                                'pending' => 'bg-orange-100 text-orange-600',
                                'paid' => 'bg-green-100 text-green-600',
                                'failed' => 'bg-red-100 text-red-600',
                            ];
                            $pyColor = $pyStatusColors[$tx->payment_status] ?? 'bg-gray-100 text-gray-600';
                        @endphp
                        <span class="px-3 py-1 {{ $pyColor }} rounded-full text-xs font-bold uppercase tracking-wider">
                            {{ $tx->payment_status }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-8 text-center text-slate-400 font-bold">
                        Tidak ada transaksi yang ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="p-6 border-t border-gray-100">
        {{ $transactions->links() }}
    </div>
</div>
@endsection
