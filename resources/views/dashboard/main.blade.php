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
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->is('dashboard*') ? 'sidebar-item-active' : 'text-color4 hover:bg-white/10' }} flex items-center gap-4 px-6 py-4 rounded-2xl font-bold transition-all">
                        <span class="text-xl">📊</span> Dashboard
                    </a>
                    <a href="{{ route('users.management') }}" class="{{ request()->is('users*') ? 'sidebar-item-active' : 'text-color4 hover:bg-white/10' }} flex items-center gap-4 px-6 py-4 rounded-2xl font-bold transition-all">
                        <span class="text-xl">🧑</span> Users Management
                    </a>
                    <a href="{{ route('admin.seller-verifications.index') }}" class="{{ request()->routeIs('admin.seller-verifications.*') ? 'sidebar-item-active' : 'text-color4 hover:bg-white/10' }} flex items-center justify-between px-6 py-4 rounded-2xl font-bold transition-all">
                        <div class="flex items-center gap-4">
                            <span class="text-xl">📝</span> Verifikasi Seller
                        </div>
                        @php
                            $pendingKycCount = \App\Models\SellerProfile::where('verification_status', 'pending')->count();
                        @endphp
                        @if($pendingKycCount > 0)
                            <span class="bg-yellow-400 text-white text-[10px] font-black px-2 py-0.5 rounded-full shadow-md">{{ $pendingKycCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.services.index') }}" class="{{ request()->is('services-categories*') ? 'sidebar-item-active' : 'text-color4 hover:bg-white/10' }} flex items-center gap-4 px-6 py-4 rounded-2xl font-bold transition-all">
                        <span class="text-xl">🎟️</span> Service & Categories
                    </a>
                    <a href="{{ route('admin.report-center.index') }}" class="{{ request()->routeIs('admin.report-center.*') ? 'sidebar-item-active' : 'text-color4 hover:bg-white/10' }} flex items-center justify-between px-6 py-4 rounded-2xl font-bold transition-all">
                        <div class="flex items-center gap-4">
                            <span class="text-xl">🚨</span> Report Center
                        </div>
                        @php
                            $pendingReportsCount = \App\Models\Report::whereIn('status', ['pending', 'reviewed'])->count();
                            $disputedCount = \App\Models\Transaction::where('is_disputed', true)->count();
                            $totalAlerts = $pendingReportsCount + $disputedCount;
                        @endphp
                        @if($totalAlerts > 0)
                            <span class="bg-red-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full shadow-md">{{ $totalAlerts }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.service.transactions') }}" class="{{ request()->is('service-transactions*') ? 'sidebar-item-active' : 'text-color4 hover:bg-white/10' }} flex items-center gap-4 px-6 py-4 rounded-2xl font-bold transition-all">
                        <span class="text-xl">🗓️</span> Service Transactions
                    </a>
                    <a href="{{ route('admin.withdrawals') }}" class="{{ request()->routeIs('admin.withdrawals') ? 'sidebar-item-active' : 'text-color4 hover:bg-white/10' }} flex items-center gap-4 px-6 py-4 rounded-2xl font-bold transition-all">
                        <span class="text-xl">💸</span> Withdrawals
                    </a>
                    <a href="{{ route('admin.ads.index') }}" class="{{ request()->routeIs('admin.ads*') ? 'sidebar-item-active' : 'text-color4 hover:bg-white/10' }} flex items-center gap-4 px-6 py-4 rounded-2xl font-bold transition-all">
                        <span class="text-xl">⭐</span> Advertisements
                    </a>
                    <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings*') ? 'sidebar-item-active' : 'text-color4 hover:bg-white/10' }} flex items-center gap-4 px-6 py-4 rounded-2xl font-bold transition-all">
                        <span class="text-xl">⚙️</span> Settings
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
                            <p class="text-sm font-bold text-slate-800 leading-none">Admin Name</p>
                            <p class="text-[10px] text-slate-400 font-bold uppercase mt-1">Administrator</p>
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
                
                @yield('admin_content')

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

        // Close dropdown when clicking outside
        window.onclick = function(event) {
            if (!event.target.closest('.relative')) {
                const dropdown = document.getElementById('userDropdown');
                if (!dropdown.classList.contains('hidden')) {
                    dropdown.classList.add('hidden');
                }
            }
        }
    </script>
</body>
</html>