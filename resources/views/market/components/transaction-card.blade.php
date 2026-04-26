<div class="bg-white rounded-[24px] p-6 border border-gray-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6 hover:shadow-md transition-shadow">
    <div class="flex gap-4 items-center flex-grow">
        <!-- Icon / Thumbnail -->
        <div class="w-16 h-16 rounded-2xl flex-shrink-0 overflow-hidden {{ $type == 'completed' ? 'bg-green-50' : 'bg-slate-100' }}">
            @if($type == 'completed')
                <div class="w-full h-full flex items-center justify-center text-green-500 text-2xl">★</div>
            @elseif($tx->serviceRequest->service->images->count() > 0)
                <img src="{{ asset('storage/' . $tx->serviceRequest->service->images->first()->image_path) }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center text-xs text-slate-400 font-bold">No Img</div>
            @endif
        </div>
        
        <!-- Details -->
        <div>
            <div class="flex gap-2 items-center mb-1">
                <span class="text-[10px] font-bold uppercase tracking-widest px-2 py-0.5 rounded-lg 
                    {{ $type == 'pending' ? 'bg-yellow-100 text-yellow-600' : ($type == 'active' ? 'bg-blue-100 text-blue-600' : 'bg-green-100 text-green-600') }}">
                    {{ $type == 'pending' ? 'MENUNGGU PEMBAYARAN' : ($type == 'active' ? 'BERJALAN' : 'SELESAI') }}
                </span>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">#{{ $tx->id }}</span>
            </div>
            <h3 class="font-bold text-slate-800 text-lg leading-tight">{{ $tx->serviceRequest->service->service_name }}</h3>
            <p class="text-sm font-medium text-slate-500 mt-1">Penyedia: <span class="font-bold text-color1">{{ $tx->serviceRequest->seller->sellerProfile->brand_name ?? 'Mitra' }}</span></p>
            <div class="mt-2 flex flex-wrap gap-2 text-[10px] font-bold text-slate-500">
                <span class="bg-gray-100 px-2 py-1 rounded-md">Tipe: {{ $tx->serviceRequest->service_type == 'home_service' ? 'Home Service' : 'On Site' }}</span>
                <span class="bg-gray-100 px-2 py-1 rounded-md">🗓️ {{ \Carbon\Carbon::parse($tx->scheduled_date)->format('d M, H:i') }}</span>
            </div>
        </div>
    </div>

    <!-- Right Side -->
    <div class="flex-shrink-0 flex flex-col items-end gap-3">
        <span class="text-xl font-black text-slate-800">Rp {{ number_format($tx->final_price, 0, ',', '.') }}</span>
        
        @if($type == 'pending')
            <a href="{{ route('user.payment', $tx->id) }}" class="px-6 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-bold rounded-xl text-sm transition-colors shadow-sm">
                Lanjutkan Pembayaran
            </a>
        @elseif($type == 'active')
            <button onclick="openReviewModal({{ $tx->id }}, '{{ addslashes($tx->serviceRequest->seller->sellerProfile->brand_name ?? 'Mitra') }}')" class="px-6 py-2 bg-green-500 hover:bg-green-600 text-white font-bold rounded-xl text-sm transition-colors shadow-sm">
                Konfirmasi Selesai & Nilai
            </button>
            <a href="{{ route('negotiation.show', $tx->serviceRequest->id) }}" class="text-xs font-bold text-color1 hover:underline mt-1">
                Buka Chat
            </a>
        @elseif($type == 'completed')
            <a href="{{ route('detail-market', $tx->serviceRequest->service_id) }}" class="px-6 py-2 bg-color1 hover:bg-color2 text-white font-bold rounded-xl text-sm transition-colors shadow-sm">
                Pesan Lagi
            </a>
        @endif
    </div>
</div>
