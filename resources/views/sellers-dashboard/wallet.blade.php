@extends('sellers-dashboard.main')

@section('sellers_content')
<div class="mb-8">
    <h1 class="text-3xl font-black text-slate-800 tracking-tighter">Dompet & Saldo</h1>
    <p class="text-slate-400 mt-1 font-medium">Kelola pendapatan Anda dan tarik tunai ke rekening bank.</p>
</div>

@if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-600 rounded-2xl font-bold text-sm">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-600 rounded-2xl font-bold text-sm">
        {{ session('error') }}
    </div>
@endif
@if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-600 rounded-2xl font-bold text-sm">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Kartu Saldo -->
    <div class="bg-gradient-to-br from-color1 to-color2 rounded-[32px] p-8 text-white shadow-xl shadow-color1/20 lg:col-span-1">
        <p class="text-white/80 font-bold uppercase tracking-widest text-xs mb-2">Saldo Aktif</p>
        <h2 class="text-4xl font-black mb-8">Rp {{ number_format($balance, 0, ',', '.') }}</h2>
        
        <form action="{{ route('seller.wallet.withdraw') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-white/90 uppercase tracking-widest mb-1">Nominal Tarik</label>
                <input type="number" name="amount" value="{{ old('amount') }}" min="10000" max="{{ $balance }}" class="w-full bg-white/20 border border-white/30 rounded-xl p-3 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-white" placeholder="Min Rp 10.000" required>
                @error('amount')<p class="text-red-200 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-bold text-white/90 uppercase tracking-widest mb-1">Nama Bank</label>
                <input type="text" name="bank_name" value="{{ old('bank_name') }}" class="w-full bg-white/20 border border-white/30 rounded-xl p-3 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-white" placeholder="Contoh: BCA / Mandiri / GoPay" required>
                @error('bank_name')<p class="text-red-200 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-bold text-white/90 uppercase tracking-widest mb-1">Nomor Rekening</label>
                <input type="text" name="account_number" value="{{ old('account_number') }}" class="w-full bg-white/20 border border-white/30 rounded-xl p-3 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-white" placeholder="1234567890" required>
                @error('account_number')<p class="text-red-200 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-bold text-white/90 uppercase tracking-widest mb-1">Atas Nama</label>
                <input type="text" name="account_name" value="{{ old('account_name') }}" class="w-full bg-white/20 border border-white/30 rounded-xl p-3 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-white" placeholder="Nama Pemilik Rekening" required>
                @error('account_name')<p class="text-red-200 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="w-full bg-white text-color1 hover:bg-slate-50 font-black py-4 rounded-xl transition-colors shadow-lg mt-2">
                Tarik Tunai
            </button>
        </form>
    </div>

    <!-- Riwayat Penarikan -->
    <div class="bg-white rounded-[32px] p-8 border border-gray-100 shadow-sm lg:col-span-2">
        <h3 class="text-xl font-bold text-slate-800 mb-6">Riwayat Penarikan</h3>
        
        <div class="space-y-4">
            @forelse($withdrawals as $w)
            <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 p-4 border border-gray-100 rounded-2xl">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center font-bold text-xl flex-shrink-0
                        {{ $w->status == 'pending' ? 'bg-yellow-100 text-yellow-600' : ($w->status == 'approved' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600') }}">
                        {{ $w->status == 'pending' ? '⏳' : ($w->status == 'approved' ? '✓' : '✗') }}
                    </div>
                    <div>
                        <p class="font-bold text-slate-800">Rp {{ number_format($w->amount, 0, ',', '.') }}</p>
                        <p class="text-xs font-medium text-slate-500">{{ $w->bank_name }} - {{ $w->account_number }}</p>
                        <p class="text-[10px] text-slate-400 mt-1">{{ $w->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-block px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-widest
                        {{ $w->status == 'pending' ? 'bg-yellow-50 text-yellow-600' : ($w->status == 'approved' ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600') }}">
                        {{ $w->status }}
                    </span>
                </div>
            </div>
            @empty
            <div class="text-center py-10">
                <span class="text-4xl block mb-2 opacity-30">💸</span>
                <p class="text-slate-500 font-medium">Belum ada riwayat penarikan dana.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
