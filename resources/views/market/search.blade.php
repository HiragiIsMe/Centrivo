<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pencarian - Centrivo</title>
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
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between gap-6">
            <a href="{{ route('market') }}" class="text-3xl font-black tracking-tighter text-color1 flex-shrink-0">Centrivo</a>

            <form action="{{ route('market.search') }}" method="GET" class="flex-grow max-w-2xl relative">
                <input type="text" name="q" value="{{ $query }}" placeholder="Cari jasa berdasarkan nama, deskripsi, rating..." 
                    class="w-full pl-12 pr-4 py-4 bg-white border border-gray-100 rounded-2xl shadow-sm focus:ring-2 focus:ring-color1/20 focus:border-color1 outline-none transition-all">
                <button type="submit" class="absolute left-4 top-1/2 -translate-y-1/2 opacity-40 hover:opacity-100">🔍</button>
            </form>

            <a href="{{ route('market') }}" class="text-sm font-bold text-slate-400 hover:text-color1 transition-colors flex-shrink-0 hidden md:block">← Kembali</a>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-6 py-8">

        <div class="mb-8">
            <h1 class="text-2xl md:text-4xl font-black text-slate-800 tracking-tighter">
                Hasil Pencarian untuk <span class="text-color1 italic">"{{ $query }}"</span>
            </h1>
            <p class="text-slate-400 mt-2 font-medium">Ditemukan <span class="text-slate-800 font-bold">{{ $services->count() }}</span> jasa relevan.</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @forelse($services as $service)
            <a href="{{ route('detail-market', $service->id) }}" class="bg-white rounded-[32px] border border-gray-100 overflow-hidden hover:shadow-2xl hover:shadow-color1/10 transition-all group flex flex-col">
                <div class="aspect-[4/3] bg-slate-100 relative overflow-hidden shrink-0">
                     @if($service->images->count() > 0)
                        <img src="{{ asset('storage/' . $service->images->first()->image_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="{{ $service->service_name }}">
                     @else
                        <div class="absolute inset-0 flex items-center justify-center text-slate-300 font-bold group-hover:scale-110 transition-transform duration-500">No Image</div>
                     @endif
                     <div class="absolute top-4 right-4 bg-white/80 backdrop-blur px-2 py-1 rounded-xl shadow-sm flex items-center gap-1">
                        <span class="text-xs font-bold text-color1">⭐ {{ number_format($service->reviews->avg('rating') ?? 0, 1) }}</span>
                     </div>
                     <div class="absolute bottom-4 left-4 bg-black/50 backdrop-blur-sm px-3 py-1 rounded-full">
                        <span class="text-[10px] font-bold text-white tracking-widest uppercase">{{ $service->category->name ?? 'Uncategorized' }}</span>
                     </div>
                </div>
                <div class="p-5 flex flex-col flex-1">
                    <h3 class="font-bold text-slate-800 line-clamp-2 mb-2 group-hover:text-color1 transition-colors leading-tight">{{ $service->service_name }}</h3>
                    <p class="text-[11px] text-slate-400 font-medium mb-4 line-clamp-1">{{ $service->seller->sellerProfile->brand_name ?? 'Mitra Centrivo' }}</p>
                    <div class="flex items-center justify-between mt-auto pt-2">
                        <p class="text-sm font-black text-slate-900 italic">Start From Rp {{ number_format($service->start_price, 0, ',', '.') }}</p>
                        <button class="px-3 py-1.5 bg-color4 text-color1 rounded-xl group-hover:bg-color1 group-hover:text-white transition-all text-xs font-bold shadow-sm">Detail</button>
                    </div>
                </div>
            </a>
            @empty
            <div class="col-span-full py-20 text-center">
                <span class="text-6xl mb-4 block opacity-50">🔍</span>
                <p class="text-slate-500 font-bold text-xl">Tidak ada jasa yang cocok.</p>
                <p class="text-slate-400 mt-2">Coba gunakan kata kunci lain atau lebih spesifik.</p>
                <a href="{{ route('market') }}" class="mt-6 inline-block bg-color1 text-white px-6 py-3 rounded-xl font-bold hover:scale-105 transition-transform shadow-lg shadow-color1/20">Kembali ke Market</a>
            </div>
            @endforelse
        </div>

    </main>

</body>
</html>
