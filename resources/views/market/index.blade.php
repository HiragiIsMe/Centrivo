<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eksplorasi Jasa - {{ $global_settings['platform_name'] ?? 'Centrivo' }}</title>
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
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .banner-container { scroll-behavior: smooth; transition: all 0.5s ease; }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-800">

    <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-8">
                <a href="{{ route('landing') }}" class="text-3xl font-black tracking-tighter text-color1">{{ $global_settings['platform_name'] ?? 'Centrivo' }}</a>
                <div class="hidden md:flex items-center gap-2 bg-gray-50 px-4 py-2 rounded-full border border-gray-100">
                    <span class="text-xs">📍</span>
                    <span class="text-xs font-bold text-slate-500 line-clamp-1 max-w-[200px]">{{ Auth::user()->userProfile->address ?? 'Alamat belum diatur' }}</span>
                    <a href="{{ route('user.settings') }}" class="text-[10px] text-color1 font-black cursor-pointer hover:underline ml-1">Ubah</a>
                </div>
            </div>

            <div class="flex items-center gap-2">

                <a href="{{ route('user.chats') }}" class="p-2.5 text-slate-400 hover:text-color1 hover:bg-gray-50 rounded-xl transition-all relative">
                    <span class="text-xl">💬</span>
                    @php
                        $unreadChats = \App\Models\Message::where('sender_id', '!=', Auth::id())
                            ->where('is_read', false)
                            ->whereHas('serviceRequest', function($q) {
                                $q->where('user_id', Auth::id())->orWhere('seller_id', Auth::id());
                            })->count();
                    @endphp
                    @if($unreadChats > 0)
                    <span class="absolute top-1 right-1 w-3 h-3 bg-red-500 border-2 border-white rounded-full"></span>
                    @endif
                </a>
                
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
                        <a href="{{ route('user.transactions') }}" class="flex items-center gap-3 px-6 py-3 text-sm font-bold text-slate-600 hover:bg-color4/30 hover:text-color1 transition">
                            <span>🛒</span> Transaksi Saya
                        </a>
                        <a href="{{ route('user.profile') }}" class="flex items-center gap-3 px-6 py-3 text-sm font-bold text-slate-600 hover:bg-color4/30 hover:text-color1 transition">
                            <span>👤</span> Profil Saya
                        </a>
                        <a href="{{ route('user.settings') }}" class="flex items-center gap-3 px-6 py-3 text-sm font-bold text-slate-600 hover:bg-color4/30 hover:text-color1 transition border-b border-gray-50">
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

    <main class="max-w-7xl mx-auto px-6 py-8">
        
        <div class="mb-10">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div>
                    <h1 class="text-3xl md:text-5xl font-black text-slate-800 tracking-tighter">
                        Selamat Pagi, <span class="text-color1 italic">{{ Auth::user()->userProfile->name ?? explode('@', Auth::user()->email)[0] }}!</span> 👋
                    </h1>
                    <p class="text-slate-400 mt-2 font-medium">Temukan lebih dari <span class="text-slate-800 font-bold">1,200+</span> jasa profesional di sekitarmu.</p>
                </div>
                <div class="w-full md:w-1/3 relative group">
                    <form action="{{ route('market.search') }}" method="GET">
                        <input type="text" name="q" value="" placeholder="Cari jasa (TF-IDF)..." 
                            class="w-full pl-12 pr-4 py-4 bg-white border border-gray-100 rounded-2xl shadow-sm focus:ring-2 focus:ring-color1/20 focus:border-color1 outline-none transition-all">
                        <button type="submit" class="absolute left-4 top-1/2 -translate-y-1/2 opacity-40 hover:opacity-100">🔍</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="relative overflow-hidden rounded-[40px] mb-12 shadow-2xl shadow-color1/10 group">
            <div id="bannerContainer" class="flex banner-container overflow-hidden">
                @forelse($billboards as $bb)
                <div class="min-w-full aspect-[21/8] flex flex-col items-center justify-center text-white p-10 relative overflow-hidden" 
                     style="background: linear-gradient(135deg, {{ $bb->gradient_from }}, {{ $bb->gradient_to }})">
                    @if($bb->image_path)
                        <img src="{{ asset('storage/' . $bb->image_path) }}" class="absolute inset-0 w-full h-full object-cover opacity-20 mix-blend-overlay">
                    @endif
                    <div class="relative z-10 text-center">
                        <h2 class="text-4xl md:text-6xl font-black mb-4 tracking-tighter">{{ $bb->title }}</h2>
                        <p class="text-white/80 font-medium text-lg">{{ $bb->subtitle }}</p>
                    </div>
                </div>
                @empty
                <div class="min-w-full aspect-[21/8] bg-gradient-to-br from-color1 to-color2 flex flex-col items-center justify-center text-white p-10">
                    <h2 class="text-4xl md:text-6xl font-black mb-4">Selamat Datang di {{ $global_settings['platform_name'] ?? 'Centrivo' }}</h2>
                    <p class="text-color4 font-medium">Solusi jasa profesional untuk semua kebutuhan Anda.</p>
                </div>
                @endforelse
            </div>

            @if($billboards->count() > 1)
            <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-2">
                @foreach($billboards as $index => $bb)
                <div class="w-2 h-2 rounded-full bg-white {{ $index === 0 ? 'opacity-100' : 'opacity-40' }}" id="dot{{ $index }}"></div>
                @endforeach
            </div>
            @endif
        </div>

        <div class="mb-12">
            <h2 class="text-xl font-black mb-6 flex items-center gap-2">
                <span class="w-2 h-6 bg-color1 rounded-full"></span> Kategori Jasa
            </h2>
            <div class="flex gap-4 overflow-x-auto no-scrollbar pb-2">
                <a href="{{ route('market') }}" class="flex-shrink-0 px-8 py-4 {{ !request('category') ? 'bg-color1 text-white' : 'bg-white text-slate-800 hover:bg-color1 hover:text-white' }} border border-gray-100 rounded-2xl font-bold transition-all shadow-sm">Semua Kategori</a>
                @foreach($categories as $category)
                <a href="{{ route('market', ['category' => $category->id, 'search' => request('search')]) }}" class="flex-shrink-0 px-8 py-4 {{ request('category') == $category->id ? 'bg-color1 text-white' : 'bg-white text-slate-800 hover:bg-color1 hover:text-white' }} border border-gray-100 rounded-2xl font-bold transition-all shadow-sm">
                    {{ $category->name }}
                </a>
                @endforeach
            </div>
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
                <p class="text-slate-500 font-bold text-xl">Tidak ada jasa yang ditemukan.</p>
                <p class="text-slate-400 mt-2">Coba gunakan kata kunci lain atau hapus filter kategori.</p>
                @if(request('category') || request('search'))
                    <a href="{{ route('market') }}" class="mt-6 inline-block bg-color1 text-white px-6 py-3 rounded-xl font-bold hover:scale-105 transition-transform shadow-lg shadow-color1/20">Reset Pencarian</a>
                @endif
            </div>
            @endforelse
        </div>

        <div class="mt-16 flex justify-center gap-2">
            {{ $services->links() }}
        </div>

    </main>

    <script>
        @if($billboards->count() > 1)
        // Auto Scroll Banner Logic
        const container = document.getElementById('bannerContainer');
        const totalBanners = {{ $billboards->count() }};
        let currentBanner = 0;

        function scrollBanner() {
            currentBanner = (currentBanner + 1) % totalBanners;
            container.scrollTo({
                left: container.offsetWidth * currentBanner,
                behavior: 'smooth'
            });

            // Update Dots
            for(let i=0; i<totalBanners; i++) {
                document.getElementById('dot'+i).style.opacity = (i === currentBanner) ? "1" : "0.4";
            }
        }

        setInterval(scrollBanner, 5000);
        @endif

        // Dropdown Toggle Logic
        function toggleDropdown() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('hidden');
        }

        // Close dropdown when clicking outside
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