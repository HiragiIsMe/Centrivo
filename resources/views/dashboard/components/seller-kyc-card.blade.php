<div class="bg-white rounded-[24px] border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-6 flex flex-col lg:flex-row gap-6">

        {{-- Info Seller --}}
        <div class="flex-1 space-y-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-color1 rounded-2xl flex items-center justify-center font-black text-xl text-white">
                    {{ strtoupper(substr($sp->brand_name, 0, 2)) }}
                </div>
                <div>
                    <h3 class="font-black text-slate-800 text-lg">{{ $sp->brand_name }}</h3>
                    <p class="text-sm text-slate-400">{{ $sp->user->email }}</p>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-xs font-bold uppercase px-2 py-0.5 rounded-lg {{ $sp->verification_status_color }}">
                            {{ $sp->verification_status_label }}
                        </span>
                        @if($sp->verified_at)
                            <span class="text-xs text-slate-400">Diverifikasi {{ \Carbon\Carbon::parse($sp->verified_at)->format('d M Y') }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-3 text-sm">
                <div class="bg-gray-50 rounded-2xl p-4">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">NIK</p>
                    <p class="font-bold text-slate-700 tracking-widest">{{ $sp->nik ?? '—' }}</p>
                </div>
                <div class="bg-gray-50 rounded-2xl p-4">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Rekening</p>
                    <p class="font-bold text-slate-700">{{ $sp->bank_name ?? '—' }}</p>
                    <p class="text-slate-500 text-xs">{{ $sp->bank_account_number ?? '' }} · {{ $sp->bank_account_name ?? '' }}</p>
                </div>
            </div>

            @if($sp->rejection_reason)
                <div class="bg-red-50 border border-red-100 rounded-2xl p-4">
                    <p class="text-[10px] font-bold text-red-400 uppercase tracking-widest mb-1">Alasan Penolakan</p>
                    <p class="text-sm text-red-700">{{ $sp->rejection_reason }}</p>
                </div>
            @endif
        </div>

        {{-- Foto Dokumen --}}
        <div class="flex gap-4">
            @if($sp->ktp_path)
            <div class="text-center">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Foto KTP</p>
                <a href="{{ asset('storage/' . $sp->ktp_path) }}" target="_blank">
                    <img src="{{ asset('storage/' . $sp->ktp_path) }}" class="w-36 h-24 object-cover rounded-2xl border-2 border-gray-100 hover:border-color1 transition" alt="KTP">
                </a>
            </div>
            @endif
            @if($sp->selfie_path)
            <div class="text-center">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Foto Selfie</p>
                <a href="{{ asset('storage/' . $sp->selfie_path) }}" target="_blank">
                    <img src="{{ asset('storage/' . $sp->selfie_path) }}" class="w-36 h-24 object-cover rounded-2xl border-2 border-gray-100 hover:border-color1 transition" alt="Selfie">
                </a>
            </div>
            @endif
        </div>

        {{-- Action Buttons --}}
        @if($showActions)
        <div class="flex flex-col gap-3 min-w-[180px]">
            {{-- Approve --}}
            <form action="{{ route('admin.seller-verifications.approve', $sp) }}" method="POST">
                @csrf
                <button type="submit"
                    onclick="return confirm('Verifikasi seller {{ $sp->brand_name }}?')"
                    class="w-full py-3 bg-green-500 hover:bg-green-600 text-white font-bold rounded-2xl text-sm transition-all shadow-sm">
                    ✅ Verifikasi
                </button>
            </form>

            {{-- Reject --}}
            <button onclick="document.getElementById('rejectModal-{{ $sp->id }}').classList.remove('hidden')"
                class="w-full py-3 bg-red-50 hover:bg-red-100 text-red-600 font-bold rounded-2xl text-sm transition-all border border-red-100">
                ❌ Tolak
            </button>

            {{-- Reject Modal --}}
            <div id="rejectModal-{{ $sp->id }}" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-[32px] max-w-md w-full p-8 shadow-2xl relative">
                    <button onclick="document.getElementById('rejectModal-{{ $sp->id }}').classList.add('hidden')" class="absolute top-6 right-6 text-slate-400 hover:text-slate-600 text-xl font-bold">✕</button>
                    <h2 class="text-xl font-black text-slate-800 mb-2">Tolak Verifikasi</h2>
                    <p class="text-slate-400 text-sm mb-6">Berikan alasan penolakan yang jelas agar seller dapat memperbaiki pengajuannya.</p>
                    <form action="{{ route('admin.seller-verifications.reject', $sp) }}" method="POST">
                        @csrf
                        <div class="mb-6">
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Alasan Penolakan</label>
                            <textarea name="rejection_reason" required rows="4"
                                class="w-full bg-gray-50 border border-gray-100 rounded-2xl p-4 text-sm font-medium outline-none focus:ring-2 focus:ring-red-500/20 transition-all resize-none"
                                placeholder="Contoh: Foto KTP tidak jelas / NIK tidak sesuai / ..."></textarea>
                        </div>
                        <div class="flex gap-4">
                            <button type="button" onclick="document.getElementById('rejectModal-{{ $sp->id }}').classList.add('hidden')" class="flex-1 py-4 text-slate-500 font-bold rounded-2xl text-sm hover:bg-gray-50">Batal</button>
                            <button type="submit" class="flex-1 py-4 bg-red-500 hover:bg-red-600 text-white font-bold rounded-2xl text-sm shadow-lg shadow-red-500/20">Tolak</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>
