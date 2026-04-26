<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Saya - Centrivo</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                <a href="{{ route('market') }}" class="p-2 hover:bg-gray-100 rounded-xl transition-colors">
                    <span class="text-xl">←</span>
                </a>
                <h1 class="text-2xl font-black text-slate-800 tracking-tighter">💬 Chat Saya</h1>
            </div>
            <p class="text-sm font-bold text-slate-400">{{ $conversations->count() }} Percakapan</p>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto px-6 py-8">

        @forelse($conversations as $conv)
            @php
                $lastMsg = $conv->messages->first();
                $preview = $lastMsg ? ($lastMsg->offered_price ? '💰 Penawaran Rp ' . number_format($lastMsg->offered_price, 0, ',', '.') : \Illuminate\Support\Str::limit($lastMsg->message, 50)) : 'Belum ada pesan';
                $time = $lastMsg ? $lastMsg->created_at->diffForHumans() : '';
            @endphp
            <div class="bg-white rounded-[24px] border border-gray-100 p-5 mb-4 shadow-sm hover:shadow-lg hover:shadow-color1/10 transition-all group relative" id="conv-{{ $conv->id }}">
                <a href="{{ route('negotiation.show', $conv->id) }}" class="flex gap-4 items-center">
                    {{-- Service Image --}}
                    <div class="w-16 h-16 bg-slate-100 rounded-2xl flex-shrink-0 overflow-hidden">
                        @if($conv->service->images->count() > 0)
                            <img src="{{ asset('storage/' . $conv->service->images->first()->image_path) }}" class="w-full h-full object-cover" alt="Service">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-[10px] text-slate-400 font-bold">No Img</div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="flex-grow min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <h3 class="font-bold text-slate-800 text-sm line-clamp-1 group-hover:text-color1 transition-colors">
                                {{ $conv->seller->sellerProfile->brand_name ?? 'Mitra Centrivo' }}
                            </h3>
                            <span class="text-[10px] text-slate-400 font-medium flex-shrink-0 ml-2">{{ $time }}</span>
                        </div>
                        <p class="text-[11px] font-bold text-color1 line-clamp-1 mb-1">🛠️ {{ $conv->service->service_name }}</p>
                        <p class="text-xs text-slate-400 line-clamp-1">{{ $preview }}</p>
                    </div>
                </a>

                {{-- Delete Button --}}
                <button onclick="deleteConversation({{ $conv->id }})" class="absolute top-3 right-3 p-2 text-slate-300 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all opacity-0 group-hover:opacity-100" title="Hapus Percakapan">
                    🗑️
                </button>
            </div>
        @empty
            <div class="text-center py-20">
                <span class="text-6xl block mb-4 opacity-50">💬</span>
                <h3 class="text-xl font-bold text-slate-700">Belum Ada Percakapan</h3>
                <p class="text-slate-400 mt-2">Mulai negosiasi jasa di marketplace untuk memulai chat.</p>
                <a href="{{ route('market') }}" class="mt-6 inline-block bg-color1 text-white px-6 py-3 rounded-xl font-bold hover:scale-105 transition-transform shadow-lg shadow-color1/20">Jelajahi Jasa</a>
            </div>
        @endforelse
    </main>

    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;

        async function deleteConversation(id) {
            if (!confirm('Hapus seluruh percakapan ini? Tindakan ini tidak bisa dibatalkan.')) return;

            try {
                const res = await fetch(`/chats/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                });
                if (res.ok) {
                    const el = document.getElementById(`conv-${id}`);
                    el.style.opacity = '0';
                    el.style.transform = 'translateX(40px)';
                    el.style.transition = 'all 0.3s ease';
                    setTimeout(() => el.remove(), 300);
                }
            } catch (err) {
                console.error(err);
            }
        }
    </script>
</body>
</html>
