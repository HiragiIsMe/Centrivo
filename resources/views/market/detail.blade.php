<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Jasa - Centrivo</title>
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
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map { height: 300px; width: 100%; border-radius: 2rem; z-index: 10; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-800">

    <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('market') }}" class="text-2xl font-black tracking-tighter text-color1 flex items-center gap-2">
                <span>←</span> Centrivo
            </a>
            <div class="flex items-center gap-2">

                <button class="p-2.5 text-slate-400 hover:text-color1 hover:bg-gray-50 rounded-xl transition-all">
                    <span class="text-xl">🛒</span>
                </button>
                
                <div class="relative">
                    <button onclick="toggleDropdown()" class="flex items-center gap-3 bg-gray-50 hover:bg-gray-100 p-1.5 pr-4 rounded-2xl border border-gray-100 transition-all group">
                        <div class="w-9 h-9 bg-color1 rounded-xl flex items-center justify-center font-bold text-white shadow-lg shadow-color1/20">
                            {{ substr(Auth::user()->userProfile->name ?? Auth::user()->email, 0, 2) }}
                        </div>
                        <div class="text-left hidden sm:block">
                            <p class="text-[11px] font-black text-slate-800 leading-none">{{ Auth::user()->userProfile->name ?? Auth::user()->email }}</p>
                            <p class="text-[9px] text-color1 font-bold uppercase mt-1">{{ Auth::user()->role }}</p>
                        </div>
                        <span class="text-[10px] text-slate-400 group-hover:text-color1 transition">▼</span>
                    </button>

                    <div id="userDropdown" class="hidden absolute right-0 mt-3 w-52 bg-white rounded-3xl shadow-2xl shadow-color1/20 border border-gray-100 py-3 z-[100] animate-in fade-in zoom-in duration-200">
                        <div class="px-6 py-2 border-b border-gray-50 mb-2">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Menu Akun</p>
                        </div>
                        <a href="#" class="flex items-center gap-3 px-6 py-3 text-sm font-bold text-slate-600 hover:bg-color4/30 hover:text-color1 transition">
                            <span>👤</span> Edit Profil
                        </a>
                        <a href="#" class="flex items-center gap-3 px-6 py-3 text-sm font-bold text-slate-600 hover:bg-color4/30 hover:text-color1 transition border-b border-gray-50">
                            <span>⚙️</span> Pengaturan
                        </a>
                        <a href="{{ route('logout') }}" class="flex items-center gap-3 px-6 py-3 text-sm font-bold text-red-500 hover:bg-red-50 transition">
                            <span>🚪</span> Keluar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-6 py-10">
        <div class="grid lg:grid-cols-3 gap-10">
            
            <div class="lg:col-span-2 space-y-8">
                
                <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-widest">
                    <a href="{{ route('market') }}" class="hover:text-color1 transition-colors">Home</a> <span>/</span> 
                    <a href="{{ route('market', ['category' => $service->category_id]) }}" class="hover:text-color1 transition-colors">{{ $service->category->name ?? 'Kategori' }}</a> <span>/</span> 
                    <span class="text-color1 line-clamp-1">{{ $service->service_name }}</span>
                </div>

                <div>
                    <h1 class="text-3xl md:text-4xl font-black text-slate-800 leading-tight mb-4">{{ $service->service_name }}</h1>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-1 bg-yellow-100 px-3 py-1 rounded-full text-yellow-700 font-bold text-sm">
                            ⭐ {{ number_format($service->reviews->avg('rating') ?? 0, 1) }} <span class="text-xs font-medium opacity-70">({{ $service->reviews->count() }} Ulasan)</span>
                        </div>
                        <div class="text-slate-400 font-medium text-sm">•</div>
                        <div class="text-slate-600 font-bold text-sm">Terjual {{ rand(10, 500) }}+ Jasa</div>
                    </div>
                </div>

                @if($service->images->count() > 0)
                <div class="grid grid-cols-4 gap-4 aspect-video md:aspect-[21/9]">
                    <div class="col-span-3 bg-slate-100 rounded-[40px] flex items-center justify-center text-slate-400 font-bold overflow-hidden shadow-lg group">
                        <img src="{{ asset('storage/' . $service->images->first()->image_path) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="Main Image">
                    </div>
                    <div class="flex flex-col gap-4">
                        @foreach($service->images->skip(1)->take(3) as $index => $image)
                            @if($index === 2 && $service->images->count() > 4)
                            <div class="flex-grow bg-slate-100 rounded-[25px] overflow-hidden relative group cursor-pointer">
                                <img src="{{ asset('storage/' . $image->image_path) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="Thumbnail">
                                <span class="absolute inset-0 bg-black/60 rounded-[25px] flex items-center justify-center text-white font-bold text-lg">+{{ $service->images->count() - 3 }}</span>
                            </div>
                            @else
                            <div class="flex-grow bg-slate-100 rounded-[25px] overflow-hidden group">
                                <img src="{{ asset('storage/' . $image->image_path) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="Thumbnail">
                            </div>
                            @endif
                        @endforeach
                        
                        {{-- Fill empty slots if less than 4 images --}}
                        @for($i = $service->images->count(); $i < 4; $i++)
                            <div class="flex-grow bg-slate-50 rounded-[25px] flex items-center justify-center text-xs font-bold text-slate-300 border border-dashed border-gray-200">No Image</div>
                        @endfor
                    </div>
                </div>
                @else
                <div class="aspect-video md:aspect-[21/9] bg-slate-100 rounded-[40px] flex items-center justify-center text-slate-400 font-bold shadow-sm border border-dashed border-gray-200">
                    <div class="text-center">
                        <span class="text-4xl block mb-2 opacity-50">📷</span>
                        Belum ada foto layanan
                    </div>
                </div>
                @endif

                <div class="bg-white p-8 rounded-[40px] border border-gray-100 shadow-sm">
                    <h2 class="text-xl font-black mb-4 flex items-center gap-2">
                        <span class="w-1.5 h-6 bg-color1 rounded-full"></span> Deskripsi Jasa
                    </h2>
                    <div class="text-slate-600 leading-relaxed whitespace-pre-wrap">{!! nl2br(e($service->description)) !!}</div>
                </div>

                @if($service->location)
                <div class="bg-white p-8 rounded-[40px] border border-gray-100 shadow-sm">
                    <h2 class="text-xl font-black mb-4">Lokasi Penyedia Jasa</h2>
                    <p class="text-sm text-slate-500 mb-4 font-medium italic">📍 Alamat: {{ $service->location->address }}, {{ $service->location->district }}, {{ $service->location->city }}</p>
                    <div id="map"></div>
                </div>
                @endif
            </div>

            <div class="lg:col-start-3">
                <div class="sticky top-24 space-y-6">
                    <div class="bg-white p-8 rounded-[40px] border border-gray-100 shadow-2xl shadow-color1/10">
                        <p class="text-slate-400 font-bold text-xs uppercase tracking-widest mb-2">Harga Mulai Dari</p>
                        <div class="flex items-end gap-2 mb-6">
                            <h3 class="text-4xl font-black text-color1 tracking-tighter">Rp {{ number_format($service->start_price, 0, ',', '.') }}</h3>
                            <span class="text-slate-400 text-sm font-medium mb-1 line-through opacity-0">/ unit</span>
                        </div>

                        <div class="space-y-3 mb-8">
                            <div class="flex items-center gap-3 text-sm font-bold text-slate-600">
                                <span class="w-6 h-6 rounded-full bg-green-100 text-green-500 flex items-center justify-center text-xs">✔</span> Respon Cepat
                            </div>
                            <div class="flex items-center gap-3 text-sm font-bold text-slate-600">
                                <span class="w-6 h-6 rounded-full bg-green-100 text-green-500 flex items-center justify-center text-xs">✔</span> Terpercaya
                            </div>
                            <div class="flex items-center gap-3 text-sm font-bold text-slate-600">
                                <span class="w-6 h-6 rounded-full bg-green-100 text-green-500 flex items-center justify-center text-xs">✔</span> Aman & Mudah
                            </div>
                        </div>

                        <div class="space-y-3">
                            @if(Auth::id() !== $service->seller_id)
                            <form action="{{ route('negotiation.initiate', $service->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full block text-center bg-color1 text-white py-4 rounded-2xl font-bold text-lg hover:shadow-xl hover:shadow-color1/30 hover:bg-color2 transition-all transform hover:-translate-y-1">
                                    Nego Sekarang
                                </button>
                            </form>
                            @else
                            <button disabled class="w-full block text-center bg-gray-300 text-gray-500 py-4 rounded-2xl font-bold text-lg cursor-not-allowed">
                                Ini Layanan Anda
                            </button>
                            @endif
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-[30px] border border-gray-100 flex items-center gap-4 hover:shadow-lg transition-shadow cursor-pointer">
                        <div class="w-14 h-14 bg-gradient-to-br from-color1 to-color2 rounded-full flex items-center justify-center text-white font-bold text-xl shadow-md">
                            {{ substr($service->seller->sellerProfile->brand_name ?? 'Mitra', 0, 2) }}
                        </div>
                        <div>
                            <p class="font-black text-slate-800">{{ $service->seller->sellerProfile->brand_name ?? 'Mitra Centrivo' }}</p>
                            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mt-0.5">Mitra Sejak {{ $service->seller->created_at->format('Y') }}</p>
                            <span class="text-xs text-color1 font-bold mt-1 inline-block">Lihat Profil Lengkap &rarr;</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    @if($service->location)
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const lat = {{ $service->location->latitude }};
        const lng = {{ $service->location->longitude }};
        const brandName = "{{ $service->seller->sellerProfile->brand_name ?? 'Penyedia Jasa' }}";
        
        const map = L.map('map').setView([lat, lng], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        
        const marker = L.marker([lat, lng]).addTo(map)
            .bindPopup(`<b>${brandName}</b><br>Lokasi Layanan.`)
            .openPopup();
    </script>
    @endif

    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('hidden');
        }

        window.addEventListener('click', function(e) {
            const dropdown = document.getElementById('userDropdown');
            const button = dropdown.previousElementSibling;
            
            if (!dropdown.contains(e.target) && !button.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
</body>
</html>