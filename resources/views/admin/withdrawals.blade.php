@extends('dashboard.main')

@section('admin_content')
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tighter">Withdrawal Requests</h1>
        <p class="text-slate-400 mt-1 font-medium">Manage seller balance withdrawals.</p>
    </div>
</div>

<div class="bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-xs font-bold text-slate-400 uppercase tracking-widest border-b border-gray-100">
                    <th class="p-6">Seller Info</th>
                    <th class="p-6">Amount</th>
                    <th class="p-6">Bank Details</th>
                    <th class="p-6">Status</th>
                    <th class="p-6 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-sm font-medium text-slate-600">
                @forelse($withdrawals as $w)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="p-6">
                        <p class="font-bold text-slate-800 text-base">{{ $w->seller->sellerProfile->brand_name ?? 'Unknown' }}</p>
                        <p class="text-xs text-slate-400">{{ $w->seller->email }}</p>
                        <p class="text-[10px] text-slate-400 mt-1">{{ $w->created_at->format('d M Y, H:i') }}</p>
                    </td>
                    <td class="p-6 font-bold text-slate-800 text-base">
                        Rp {{ number_format($w->amount, 0, ',', '.') }}
                    </td>
                    <td class="p-6">
                        <p class="font-bold text-slate-800">{{ $w->bank_name }}</p>
                        <p class="text-slate-500">{{ $w->account_number }}</p>
                        <p class="text-xs text-slate-400 mt-1">A.n {{ $w->account_name }}</p>
                    </td>
                    <td class="p-6">
                        <span class="inline-block px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-widest
                            {{ $w->status == 'pending' ? 'bg-yellow-50 text-yellow-600' : ($w->status == 'approved' ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600') }}">
                            {{ $w->status }}
                        </span>
                    </td>
                    <td class="p-6 text-right">
                        @if($w->status == 'pending')
                            <div class="flex items-center justify-end gap-2">
                                <form action="{{ route('admin.withdrawals.approve', $w->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Apakah Anda yakin sudah mentransfer uang ke seller ini?')" class="bg-green-500 hover:bg-green-600 text-white font-bold px-4 py-2 rounded-xl text-xs transition-colors shadow-sm">
                                        Approve
                                    </button>
                                </form>
                                <form action="{{ route('admin.withdrawals.reject', $w->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Tolak dan kembalikan saldo ke seller?')" class="bg-red-50 hover:bg-red-100 text-red-600 font-bold px-4 py-2 rounded-xl text-xs transition-colors">
                                        Reject
                                    </button>
                                </form>
                            </div>
                        @else
                            <span class="text-xs text-slate-400 font-bold">PROCESSED</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-12 text-center text-slate-400">
                        <span class="text-4xl block mb-2 opacity-30">📂</span>
                        <p>No withdrawal records found.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">
    {{ $withdrawals->links() }}
</div>
@endsection
