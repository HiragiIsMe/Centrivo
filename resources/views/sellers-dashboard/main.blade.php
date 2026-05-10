<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - {{ $global_settings['platform_name'] ?? 'Centrivo' }}</title>
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
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #B1C9EF; border-radius: 10px; }
        
        .sidebar-item-active {
            background-color: white;
            color: #628ECB;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        }
        .dropdown-animate {
            transform-origin: top right;
            transition: all 0.2s ease-out;
        }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-800">

    <div class="flex min-h-screen">
        
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-72 bg-color1 text-white transition-transform transform -translate-x-full lg:translate-x-0 lg:static lg:inset-0 shadow-2xl shadow-color1/20">
            <div class="flex flex-col h-full">
                <div class="p-8">
                    <span class="text-3xl font-black tracking-tighter">{{ $global_settings['platform_name'] ?? 'Centrivo' }}</span>
                    <p class="text-xs text-color3 font-bold mt-1 uppercase tracking-widest opacity-80">Workspace</p>
                </div>

                <nav class="flex-grow px-4 space-y-2">
                    {{-- Dashboard --}}
                    <a href="{{ route('sellers.dashboard') }}" 
                    class="{{ request()->is('dashboard-sellers*') ? 'sidebar-item-active' : 'text-color4 hover:bg-white/10' }} flex items-center gap-4 px-6 py-4 rounded-2xl font-bold transition-all">
                        <span class="text-xl">📊</span> Dashboard
                    </a>

                    <a href="{{ route('locations.index') }}" 
                    class="{{ request()->is('locations*') ? 'sidebar-item-active' : 'text-color4 hover:bg-white/10' }} flex items-center gap-4 px-6 py-4 rounded-2xl font-bold transition-all">
                        <span class="text-xl">🗺️</span> Locations
                    </a>

                    {{-- KYC Verification --}}
                    @php $kycStatus = Auth::user()->sellerProfile?->verification_status ?? 'unverified'; @endphp
                    <a href="{{ route('seller.kyc.show') }}"
                    class="{{ request()->routeIs('seller.kyc.*') ? 'sidebar-item-active' : 'text-color4 hover:bg-white/10' }} flex items-center justify-between px-6 py-4 rounded-2xl font-bold transition-all">
                        <div class="flex items-center gap-4">
                            <span class="text-xl">📝</span> Verifikasi Identitas
                        </div>
                        @if($kycStatus === 'unverified')
                            <span class="bg-red-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full">!</span>
                        @elseif($kycStatus === 'pending')
                            <span class="bg-yellow-400 text-white text-[10px] font-black px-2 py-0.5 rounded-full">...</span>
                        @elseif($kycStatus === 'verified')
                            <span class="bg-green-400 text-white text-[10px] font-black px-2 py-0.5 rounded-full">✓</span>
                        @endif
                    </a>

                    {{-- Services --}}
                    <a href="{{ route('services.index') }}" 
                    class="{{ request()->is('services*') || request()->is('services*') ? 'sidebar-item-active' : 'text-color4 hover:bg-white/10' }} flex items-center gap-4 px-6 py-4 rounded-2xl font-bold transition-all">
                        <span class="text-xl">🛠️</span> Services
                    </a>

                    {{-- Service Transactions --}}
                    <a href="{{ route('sellers.transactions') }}" 
                    class="{{ request()->routeIs('sellers.transactions') ? 'sidebar-item-active' : 'text-color4 hover:bg-white/10' }} flex items-center justify-between px-6 py-4 rounded-2xl font-bold transition-all relative">
                        <div class="flex items-center gap-4">
                            <span class="text-xl">🗓️</span> Service Transactions
                        </div>
                        @php
                            $unreadSellerChats = \App\Models\Message::where('sender_id', '!=', Auth::id())
                                ->where('is_read', false)
                                ->whereHas('serviceRequest', function($q) {
                                    $q->where('seller_id', Auth::id());
                                })->count();
                        @endphp
                        @if($unreadSellerChats > 0)
                        <span class="bg-red-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full shadow-md">{{ $unreadSellerChats }}</span>
                        @endif
                    </a>

                    {{-- Wallet --}}
                    <a href="{{ route('seller.wallet') }}" 
                    class="{{ request()->routeIs('seller.wallet') ? 'sidebar-item-active' : 'text-color4 hover:bg-white/10' }} flex items-center gap-4 px-6 py-4 rounded-2xl font-bold transition-all">
                        <span class="text-xl">💳</span> Withdrawals
                    </a>

                    {{-- Advertisements --}}
                    <a href="{{ route('seller.advertisements') }}" 
                    class="{{ request()->routeIs('seller.advertisements*') ? 'sidebar-item-active' : 'text-color4 hover:bg-white/10' }} flex items-center gap-4 px-6 py-4 rounded-2xl font-bold transition-all">
                        <span class="text-xl">⭐</span> Advertisements
                    </a>

                    {{-- Income Reports --}}
                    <a href="{{ route('seller.reports.index') }}" 
                    class="{{ request()->routeIs('seller.reports*') ? 'sidebar-item-active' : 'text-color4 hover:bg-white/10' }} flex items-center gap-4 px-6 py-4 rounded-2xl font-bold transition-all">
                        <span class="text-xl">📠</span> Income Reports
                    </a>
                </nav>

                <div class="p-8">
                    <p class="text-[10px] text-color3 font-bold uppercase tracking-widest opacity-50 text-center">© 2026 {{ $global_settings['platform_name'] ?? 'Centrivo' }}</p>
                </div>
            </div>
        </aside>

        <main class="flex-grow flex flex-col min-w-0 h-screen overflow-hidden">
            
            <header class="bg-white/80 backdrop-blur-md border-b border-gray-100 px-8 py-4 flex items-center justify-between sticky top-0 z-40">
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="lg:hidden p-2 bg-gray-100 rounded-xl">
                        <span>☰</span>
                    </button>
                    <h2 class="text-xl font-black text-slate-800">Overview</h2>
                </div>

                <div class="relative">
                    <button onclick="toggleDropdown()" class="flex items-center gap-3 bg-gray-50 hover:bg-gray-100 p-2 pr-4 rounded-2xl border border-gray-100 transition-all group">
                        <div class="w-10 h-10 bg-color1 rounded-xl flex items-center justify-center font-bold text-white shadow-lg shadow-color1/20">
                            AD
                        </div>
                        <div class="text-left hidden sm:block">
                            <p class="text-sm font-bold text-slate-800 leading-none">{{ Auth::user()->email }}</p>
                            <p class="text-[10px] text-slate-400 font-bold uppercase mt-1">{{ Auth::user()->role }}</p>
                        </div>
                        <span class="text-xs text-slate-400 group-hover:text-color1 transition">▼</span>
                    </button>

                    <div id="userDropdown" class="hidden absolute right-0 mt-3 w-56 bg-white rounded-3xl shadow-2xl shadow-color1/20 border border-gray-100 py-3 dropdown-animate z-50">
                        <div class="px-6 py-2 border-b border-gray-50 mb-2">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Menu Akun</p>
                        </div>
                        <a href="#" class="flex items-center gap-3 px-6 py-3 text-sm font-bold text-slate-600 hover:bg-color4/30 hover:text-color1 transition">
                            <span>👤</span> Edit Profil
                        </a>
                        <a href="#" class="flex items-center gap-3 px-6 py-3 text-sm font-bold text-slate-600 hover:bg-color4/30 hover:text-color1 transition border-b border-gray-50">
                            <span>⚙️</span> Pengaturan
                        </a>
                        <a href="{{ route('logout') }}" class="flex items-center gap-3 px-6 py-3 text-sm font-bold text-red-500 hover:bg-red-50 transition">
                            <span>🚪</span> Keluar (Logout)
                        </a>
                    </div>
                </div>
            </header>

            <section class="flex-grow overflow-y-auto p-8">
                
                {{-- KYC Warning Banner --}}
                @php $sellerKycStatus = Auth::user()->sellerProfile?->verification_status ?? 'unverified'; @endphp
                @if($sellerKycStatus !== 'verified')
                <div class="mb-6 rounded-[20px] p-4 flex items-center justify-between gap-4
                    {{ $sellerKycStatus === 'pending' ? 'bg-yellow-50 border border-yellow-200' : ($sellerKycStatus === 'rejected' ? 'bg-red-50 border border-red-200' : 'bg-blue-50 border border-blue-200') }}">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">{{ $sellerKycStatus === 'pending' ? '⏳' : ($sellerKycStatus === 'rejected' ? '❌' : '📝') }}</span>
                        <div>
                            <p class="font-bold text-sm {{ $sellerKycStatus === 'pending' ? 'text-yellow-700' : ($sellerKycStatus === 'rejected' ? 'text-red-700' : 'text-blue-700') }}">
                                @if($sellerKycStatus === 'pending')
                                    Verifikasi sedang diproses — Fitur layanan sementara dikunci.
                                @elseif($sellerKycStatus === 'rejected')
                                    Verifikasi ditolak — Perbaiki dan kirim ulang dokumen Anda.
                                @else
                                    Akun belum diverifikasi — Anda tidak bisa membuat layanan.
                                @endif
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('seller.kyc.show') }}"
                        class="px-4 py-2 font-bold text-xs rounded-xl whitespace-nowrap transition-all
                        {{ $sellerKycStatus === 'pending' ? 'bg-yellow-500 text-white hover:bg-yellow-600' : ($sellerKycStatus === 'rejected' ? 'bg-red-500 text-white hover:bg-red-600' : 'bg-blue-500 text-white hover:bg-blue-600') }}">
                        {{ $sellerKycStatus === 'pending' ? 'Lihat Status' : 'Verifikasi Sekarang' }}
                    </a>
                </div>
                @endif

                {{-- kyc_warning from middleware redirect --}}
                @if(session('kyc_warning'))
                <div class="mb-6 p-4 bg-orange-50 border border-orange-200 rounded-2xl flex items-center gap-3">
                    <span class="text-2xl">🔒</span>
                    <p class="text-orange-700 font-bold text-sm">{{ session('kyc_warning') }}</p>
                    <a href="{{ route('seller.kyc.show') }}" class="ml-auto px-4 py-2 bg-orange-500 text-white text-xs font-bold rounded-xl hover:bg-orange-600 whitespace-nowrap">Verifikasi Sekarang</a>
                </div>
                @endif

                @yield('sellers_content')

            </section>
        </main>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        }

        function toggleDropdown() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('hidden');
        }

        window.onclick = function(event) {
            if (!event.target.closest('.relative')) {
                const dropdown = document.getElementById('userDropdown');
                if (!dropdown.classList.contains('hidden')) {
                    dropdown.classList.add('hidden');
                }
            }
        }
    </script>

    {{-- First-Login KYC Onboarding Modal for Unverified Sellers --}}
    @if(Auth::user()->sellerProfile?->verification_status === 'unverified' && !session('kyc_onboarding_shown'))
    @php session(['kyc_onboarding_shown' => true]); @endphp
    <div id="kycOnboardingModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-[999] flex items-center justify-center p-4">
        <div class="bg-white rounded-[40px] max-w-lg w-full p-10 shadow-2xl text-center">
            <div class="w-24 h-24 bg-gradient-to-br from-blue-100 to-blue-200 rounded-3xl flex items-center justify-center text-5xl mx-auto mb-6 shadow-xl">
                📋
            </div>
            <h2 class="text-3xl font-black text-slate-800 tracking-tighter mb-3">Selamat Datang!</h2>
            <p class="text-slate-500 font-medium mb-2">Akun Anda sudah aktif, tapi ada satu langkah lagi.</p>
            <p class="text-slate-700 font-bold mb-8">Selesaikan <span class="text-color1">Verifikasi Identitas</span> untuk mulai membuat layanan dan berjualan di platform kami.</p>

            <div class="space-y-3 text-left mb-8">
                <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl">
                    <div class="w-10 h-10 bg-blue-100 text-color1 rounded-xl flex items-center justify-center font-black text-lg">1</div>
                    <div>
                        <p class="font-bold text-slate-800 text-sm">Upload KTP & Selfie</p>
                        <p class="text-xs text-slate-400">Foto KTP dan selfie memegang KTP</p>
                    </div>
                </div>
                <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl">
                    <div class="w-10 h-10 bg-blue-100 text-color1 rounded-xl flex items-center justify-center font-black text-lg">2</div>
                    <div>
                        <p class="font-bold text-slate-800 text-sm">Data Rekening Bank</p>
                        <p class="text-xs text-slate-400">Untuk pencairan penghasilan Anda</p>
                    </div>
                </div>
                <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl">
                    <div class="w-10 h-10 bg-green-100 text-green-600 rounded-xl flex items-center justify-center font-black text-lg">✓</div>
                    <div>
                        <p class="font-bold text-slate-800 text-sm">Diverifikasi Admin (1-2 Hari Kerja)</p>
                        <p class="text-xs text-slate-400">Setelah itu, Anda bisa langsung berjualan!</p>
                    </div>
                </div>
            </div>

            <div class="flex gap-4">
                <button onclick="document.getElementById('kycOnboardingModal').classList.add('hidden')"
                    class="flex-1 py-4 text-slate-400 font-bold rounded-2xl hover:bg-gray-50 transition text-sm">
                    Nanti Saja
                </button>
                <a href="{{ route('seller.kyc.show') }}"
                    class="flex-1 py-4 bg-color1 hover:bg-color2 text-white font-black rounded-2xl transition-all shadow-lg shadow-color1/30 text-sm">
                    Verifikasi Sekarang 🚀
                </a>
            </div>
        </div>
    </div>
    @endif

</body>
</html>