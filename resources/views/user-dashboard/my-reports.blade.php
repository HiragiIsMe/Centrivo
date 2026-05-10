<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Laporan - Centrivo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'color1': '#628ECB',
                        'color2': '#8AAEE0',
                        'color3': '#B1C9EF',
                        'color4': '#D5DEEF',
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-50 font-sans text-slate-800">

    <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100">
        <div class="max-w-4xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('user.profile') }}" class="p-2 hover:bg-gray-100 rounded-xl transition-colors">
                    <span class="text-xl">←</span>
                </a>
                <h1 class="text-2xl font-black text-slate-800 tracking-tighter">Riwayat Laporan</h1>
            </div>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto px-6 py-10">
        <div class="mb-8">
            <p class="text-slate-400 font-medium">Pantau status laporan yang Anda kirimkan kepada tim Centrivo.</p>
        </div>

        <div class="bg-white rounded-[24px] border border-gray-100 shadow-sm overflow-hidden p-6">
            @forelse($reports as $report)
            <div class="border border-gray-100 rounded-2xl p-5 mb-4 hover:shadow-md transition-all bg-gray-50/50">
                <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                    <div class="flex items-center gap-3">
                        <span class="bg-slate-200 text-slate-700 px-3 py-1 rounded-full text-xs font-bold tracking-widest font-mono">
                            {{ $report->report_code }}
                        </span>
                        <span class="text-xs text-slate-400 font-medium">
                            {{ $report->created_at->format('d M Y, H:i') }}
                        </span>
                    </div>
                    <div>
                        @if($report->status === 'pending')
                            <span class="bg-yellow-100 text-yellow-700 px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest border border-yellow-200">
                                ⏳ Menunggu
                            </span>
                        @elseif($report->status === 'reviewed')
                            <span class="bg-blue-100 text-blue-700 px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest border border-blue-200">
                                🔍 Sedang Ditinjau
                            </span>
                        @else
                            <span class="bg-green-100 text-green-700 px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest border border-green-200">
                                ✅ Selesai
                            </span>
                        @endif
                    </div>
                </div>

                <div class="mb-4">
                    <h3 class="text-sm font-bold text-slate-800 mb-1">
                        Melaporkan: 
                        @if($report->reportedService)
                            Layanan "{{ $report->reportedService->service_name }}"
                        @elseif($report->reportedUser)
                            Pengguna "{{ $report->reportedUser->userProfile->name ?? $report->reportedUser->sellerProfile->brand_name ?? 'N/A' }}"
                        @endif
                    </h3>
                    <p class="text-xs text-slate-500 font-medium bg-white p-3 rounded-xl border border-gray-100">
                        <strong class="text-slate-700">Alasan:</strong> {{ $report->reason }}<br>
                        <span class="mt-1 block text-slate-400">{{ $report->description }}</span>
                    </p>
                </div>

                @if($report->admin_notes)
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                    <p class="text-[10px] font-black text-blue-500 uppercase tracking-widest mb-1 flex items-center gap-1">
                        <span>💬</span> Catatan Admin
                    </p>
                    <p class="text-sm text-blue-800 font-medium">{{ $report->admin_notes }}</p>
                </div>
                @elseif($report->status === 'resolved')
                <div class="bg-green-50 border border-green-100 rounded-xl p-4">
                     <p class="text-[10px] font-black text-green-600 uppercase tracking-widest mb-1">
                        Tindakan Diambil
                    </p>
                    <p class="text-sm text-green-800 font-medium">Laporan ini telah diselesaikan oleh admin. Terima kasih atas laporan Anda.</p>
                </div>
                @endif
            </div>
            @empty
            <div class="text-center py-16">
                <span class="text-4xl block mb-3 opacity-30">✨</span>
                <p class="font-bold text-slate-400 text-lg">Belum ada laporan</p>
                <p class="text-slate-400 text-sm">Anda belum pernah mengirimkan laporan apapun.</p>
            </div>
            @endforelse

            @if($reports->hasPages())
            <div class="mt-6">
                {{ $reports->links() }}
            </div>
            @endif
        </div>
    </main>
</body>
</html>
