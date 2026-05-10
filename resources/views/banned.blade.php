<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Dinonaktifkan - {{ $platform }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'color1': '#628ECB',
                        'color2': '#8AAEE0',
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-12px); }
        }
        .float-anim { animation: float 3s ease-in-out infinite; }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-up { animation: fadeUp 0.6s ease-out forwards; }
        .fade-up-delay-1 { animation: fadeUp 0.6s ease-out 0.1s forwards; opacity: 0; }
        .fade-up-delay-2 { animation: fadeUp 0.6s ease-out 0.2s forwards; opacity: 0; }
        .fade-up-delay-3 { animation: fadeUp 0.6s ease-out 0.35s forwards; opacity: 0; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 min-h-screen flex items-center justify-center p-6 font-sans">

    <!-- Background subtle decoration -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-red-500/5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-color1/5 rounded-full blur-3xl"></div>
    </div>

    <div class="relative w-full max-w-lg">

        <!-- Card Utama -->
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-[40px] p-10 text-center shadow-2xl fade-up">

            <!-- Ikon -->
            <div class="float-anim mb-8">
                <div class="w-28 h-28 mx-auto bg-red-500/10 rounded-full flex items-center justify-center border border-red-500/20">
                    <span class="text-6xl">🚫</span>
                </div>
            </div>

            <!-- Judul -->
            <h1 class="text-3xl font-black text-white mb-3 tracking-tight fade-up-delay-1">Akun Dinonaktifkan</h1>
            <p class="text-slate-400 font-medium mb-8 leading-relaxed fade-up-delay-1">
                Akun Anda telah dinonaktifkan oleh administrator <span class="text-white font-bold">{{ $platform }}</span>.
                Hubungi kami untuk informasi lebih lanjut.
            </p>

            @if($reportCode)
            <!-- Kode Laporan -->
            <div class="bg-white/5 border border-white/10 rounded-2xl p-5 mb-6 fade-up-delay-2">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Kode Laporan Anda</p>
                <div class="flex items-center justify-center gap-3">
                    <code class="text-2xl font-black text-color2 tracking-widest">{{ $reportCode }}</code>
                    <button onclick="copyCode('{{ $reportCode }}')" class="p-2 bg-white/10 hover:bg-white/20 rounded-xl transition-all text-slate-400 hover:text-white" title="Salin kode">
                        📋
                    </button>
                </div>
                <p id="copySuccess" class="text-xs text-green-400 font-bold mt-2 hidden">✓ Kode disalin!</p>
                <p class="text-xs text-slate-500 mt-3 font-medium">Lampirkan kode ini saat menghubungi admin</p>
            </div>
            @endif

            @if($bannedEntity && $bannedEntity->ban_reason)
            <!-- Alasan Ban -->
            <div class="bg-red-500/10 border border-red-500/20 rounded-2xl p-4 mb-6 text-left fade-up-delay-2">
                <p class="text-xs font-bold text-red-400 uppercase tracking-widest mb-2">Alasan Penonaktifan</p>
                <p class="text-sm text-slate-300 font-medium leading-relaxed">{{ $bannedEntity->ban_reason }}</p>
            </div>
            @endif

            @php
                // Cari transaksi terkait ban ini
                $relatedTx = \App\Models\Transaction::with('serviceRequest.service')
                    ->where('is_disputed', true)
                    ->whereHas('serviceRequest', function($q) use ($bannedEntity) {
                        if ($bannedEntity instanceof \App\Models\User) {
                            $q->where('user_id', $bannedEntity->id)->orWhere('seller_id', $bannedEntity->id);
                        } else {
                            $q->where('service_id', $bannedEntity->id);
                        }
                    })->first();
            @endphp
            @if($relatedTx)
            <!-- Info Transaksi Terdampak -->
            <div class="bg-yellow-500/10 border border-yellow-500/20 rounded-2xl p-4 mb-6 text-left fade-up-delay-2">
                <p class="text-xs font-bold text-yellow-400 uppercase tracking-widest mb-2">⚠️ Transaksi Terdampak</p>
                <p class="text-sm text-slate-300 font-medium">
                    Ada transaksi aktif yang terdampak dari penonaktifan ini.
                    Tim admin akan menangani transaksi tersebut. Gunakan kode laporan saat menghubungi admin.
                </p>
                <p class="text-xs text-yellow-300 font-bold mt-2">
                    Jasa: {{ $relatedTx->serviceRequest->service->service_name ?? '-' }}
                </p>
            </div>
            @endif

            <!-- Langkah Selanjutnya -->
            <div class="bg-white/5 border border-white/10 rounded-2xl p-5 mb-8 text-left fade-up-delay-2">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Langkah Selanjutnya</p>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-color1/20 text-color2 flex items-center justify-center text-xs font-black flex-shrink-0 mt-0.5">1</span>
                        <p class="text-sm text-slate-300 font-medium">Salin kode laporan di atas</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-color1/20 text-color2 flex items-center justify-center text-xs font-black flex-shrink-0 mt-0.5">2</span>
                        <p class="text-sm text-slate-300 font-medium">Hubungi admin via WhatsApp dengan melampirkan kode laporan</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-color1/20 text-color2 flex items-center justify-center text-xs font-black flex-shrink-0 mt-0.5">3</span>
                        <p class="text-sm text-slate-300 font-medium">Admin akan meninjau dan memberikan keputusan dalam 1×24 jam</p>
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="space-y-3 fade-up-delay-3">
                @php
                    $waMessage = urlencode("Halo admin, saya ingin menanyakan mengenai penonaktifan akun saya di " . ($platform ?? 'Centrivo') . ". Kode Laporan: " . ($reportCode ?? 'tidak tersedia'));
                    $waUrl = "https://wa.me/{$adminWa}?text={$waMessage}";
                @endphp

                <a href="{{ $waUrl }}" target="_blank"
                   class="w-full flex items-center justify-center gap-3 bg-green-500 hover:bg-green-600 text-white font-bold py-4 rounded-2xl transition-all shadow-lg shadow-green-500/20 hover:shadow-green-500/40 hover:-translate-y-0.5">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                        <path d="M12 0C5.373 0 0 5.373 0 12c0 2.127.557 4.126 1.528 5.858L0 24l6.335-1.528A11.937 11.937 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.006-1.371l-.36-.213-3.728.9.924-3.636-.234-.374A9.818 9.818 0 012.182 12C2.182 6.56 6.56 2.182 12 2.182c5.44 0 9.818 4.378 9.818 9.818 0 5.44-4.378 9.818-9.818 9.818z"/>
                    </svg>
                    Hubungi Admin via WhatsApp
                </a>

                <a href="{{ route('login') }}"
                   class="w-full flex items-center justify-center gap-2 text-slate-400 hover:text-white font-bold py-3 rounded-2xl transition-all border border-white/10 hover:border-white/20 text-sm">
                    ← Kembali ke Halaman Login
                </a>
            </div>

            <!-- Footer -->
            <p class="text-xs text-slate-600 mt-8 font-medium">
                {{ $platform }} — Hanya menerima permintaan bantuan melalui WhatsApp resmi
            </p>

        </div>
    </div>

    <script>
        function copyCode(code) {
            navigator.clipboard.writeText(code).then(() => {
                const el = document.getElementById('copySuccess');
                el.classList.remove('hidden');
                setTimeout(() => el.classList.add('hidden'), 2000);
            });
        }
    </script>
</body>
</html>
