<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Centrivo</title>
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
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-800">

    <div class="flex min-h-screen">
        
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 text-white transition-transform transform -translate-x-full lg:translate-x-0 lg:static lg:inset-0 shadow-2xl">
            <div class="flex flex-col h-full">
                <div class="p-8">
                    <span class="text-3xl font-black tracking-tighter text-white">Centrivo <span class="text-color1">HQ</span></span>
                    <p class="text-xs text-slate-400 font-bold mt-1 uppercase tracking-widest">Admin Control Panel</p>
                </div>

                <nav class="flex-grow px-4 space-y-2">
                    <a href="{{ route('admin.dashboard') }}" 
                    class="{{ request()->routeIs('admin.dashboard') ? 'bg-color1 text-white font-bold' : 'text-slate-400 hover:bg-white/10' }} flex items-center gap-4 px-6 py-4 rounded-2xl transition-all">
                        <span class="text-xl">📊</span> Overview
                    </a>

                    <a href="{{ route('admin.withdrawals') }}" 
                    class="{{ request()->routeIs('admin.withdrawals') ? 'bg-color1 text-white font-bold' : 'text-slate-400 hover:bg-white/10' }} flex items-center gap-4 px-6 py-4 rounded-2xl transition-all">
                        <span class="text-xl">💸</span> Withdrawals
                    </a>
                </nav>

                <div class="p-8 mt-auto">
                    <form action="/logout" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 bg-slate-800 hover:bg-red-500 text-white px-4 py-3 rounded-xl transition-colors font-bold text-sm">
                            <span>🚪</span> Logout Admin
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <main class="flex-grow flex flex-col min-w-0 h-screen overflow-hidden">
            
            <header class="bg-white/80 backdrop-blur-md border-b border-gray-100 px-8 py-4 flex items-center justify-between sticky top-0 z-40">
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="lg:hidden p-2 bg-gray-100 rounded-xl">
                        <span>☰</span>
                    </button>
                    <h2 class="text-xl font-black text-slate-800">Hello, Superadmin</h2>
                </div>
            </header>

            <section class="flex-grow overflow-y-auto p-8">
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-600 rounded-2xl font-bold text-sm">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-600 rounded-2xl font-bold text-sm">
                        {{ session('error') }}
                    </div>
                @endif
                
                @yield('admin_content')

            </section>
        </main>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        }
    </script>
</body>
</html>
