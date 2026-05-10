<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi Saya - Centrivo</title>
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
        .star-rating input { display: none; }
        .star-rating label { font-size: 2rem; color: #cbd5e1; cursor: pointer; transition: color 0.2s; }
        .star-rating input:checked ~ label { color: #cbd5e1; }
        .star-rating label:hover,
        .star-rating label:hover ~ label,
        .star-rating input:checked + label,
        .star-rating input:checked + label ~ label { color: #f59e0b; }
        .star-rating { display: flex; flex-direction: row-reverse; justify-content: center; }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-800">

    <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100">
        <div class="max-w-4xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('market') }}" class="p-2 hover:bg-gray-100 rounded-xl transition-colors">
                    <span class="text-xl">←</span>
                </a>
                <h1 class="text-2xl font-black text-slate-800 tracking-tighter">Transaksi Saya</h1>
            </div>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto px-6 py-10">
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

        <!-- Tabs -->
        <div class="flex gap-4 mb-6 border-b border-gray-100 pb-2 overflow-x-auto no-scrollbar">
            <button onclick="switchTab('pending')" id="tab-pending" class="px-6 py-3 rounded-xl font-bold text-sm transition-all bg-color1 text-white shadow-lg shadow-color1/20 whitespace-nowrap">
                Belum Dibayar ({{ $pending->count() }})
            </button>
            <button onclick="switchTab('active')" id="tab-active" class="px-6 py-3 rounded-xl font-bold text-sm transition-all text-slate-500 hover:bg-gray-100 whitespace-nowrap">
                Berjalan ({{ $active->count() }})
            </button>
            <button onclick="switchTab('completed')" id="tab-completed" class="px-6 py-3 rounded-xl font-bold text-sm transition-all text-slate-500 hover:bg-gray-100 whitespace-nowrap">
                Riwayat ({{ $completed->count() }})
            </button>
        </div>

        <!-- Tab: Pending -->
        <div id="content-pending" class="space-y-4">
            @forelse($pending as $tx)
                @include('market.components.transaction-card', ['tx' => $tx, 'type' => 'pending'])
            @empty
                <div class="bg-white rounded-[32px] p-12 border border-gray-100 text-center">
                    <span class="text-6xl opacity-50 block mb-4">🛒</span>
                    <h3 class="font-bold text-slate-700 text-xl">Belum Ada Tagihan</h3>
                    <p class="text-slate-400 mt-2">Tidak ada transaksi yang menunggu pembayaran.</p>
                </div>
            @endforelse
        </div>

        <!-- Tab: Active -->
        <div id="content-active" class="space-y-4 hidden">
            @forelse($active as $tx)
                @include('market.components.transaction-card', ['tx' => $tx, 'type' => 'active'])
            @empty
                <div class="bg-white rounded-[32px] p-12 border border-gray-100 text-center">
                    <span class="text-6xl opacity-50 block mb-4">🏃</span>
                    <h3 class="font-bold text-slate-700 text-xl">Tidak Ada Pesanan Aktif</h3>
                    <p class="text-slate-400 mt-2">Belum ada jasa yang sedang berjalan atau akan dikerjakan.</p>
                </div>
            @endforelse
        </div>

        <!-- Tab: Completed -->
        <div id="content-completed" class="space-y-4 hidden">
            @forelse($completed as $tx)
                @include('market.components.transaction-card', ['tx' => $tx, 'type' => 'completed'])
            @empty
                <div class="bg-white rounded-[32px] p-12 border border-gray-100 text-center">
                    <span class="text-6xl opacity-50 block mb-4">📚</span>
                    <h3 class="font-bold text-slate-700 text-xl">Belum Ada Riwayat</h3>
                    <p class="text-slate-400 mt-2">Anda belum memiliki transaksi yang selesai atau dibatalkan.</p>
                </div>
            @endforelse
        </div>
    </main>

    <!-- Review Modal -->
    <div id="reviewModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-[32px] p-8 w-full max-w-md shadow-2xl transform scale-95 transition-transform duration-300" id="reviewModalBox">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-black text-slate-800">Beri Nilai Jasa</h3>
                <button onclick="closeReviewModal()" class="text-slate-400 hover:text-red-500 font-bold text-xl">&times;</button>
            </div>
            <p class="text-sm text-slate-500 mb-6 font-medium">Bagaimana hasil kerja <span id="modalSellerName" class="font-bold text-color1"></span>?</p>
            
            <form id="reviewForm" method="POST" action="">
                @csrf
                <div class="mb-6">
                    <div class="star-rating">
                        <input type="radio" id="star5" name="rating" value="5" required />
                        <label for="star5" title="5 Bintang">★</label>
                        <input type="radio" id="star4" name="rating" value="4" />
                        <label for="star4" title="4 Bintang">★</label>
                        <input type="radio" id="star3" name="rating" value="3" />
                        <label for="star3" title="3 Bintang">★</label>
                        <input type="radio" id="star2" name="rating" value="2" />
                        <label for="star2" title="2 Bintang">★</label>
                        <input type="radio" id="star1" name="rating" value="1" />
                        <label for="star1" title="1 Bintang">★</label>
                    </div>
                </div>
                
                <div class="mb-6">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Ulasan (Opsional)</label>
                    <textarea name="comment" rows="3" class="w-full bg-gray-50 border border-gray-100 rounded-2xl p-4 text-sm font-medium outline-none focus:ring-2 focus:ring-color1/20 transition-all resize-none" placeholder="Tuliskan pengalaman Anda..."></textarea>
                </div>

                <button type="submit" class="w-full bg-color1 hover:bg-color2 text-white font-bold py-4 rounded-2xl transition-all shadow-lg shadow-color1/20">
                    Kirim &amp; Selesaikan Pesanan
                </button>
            </form>

            <!-- Link laporan dari dalam review modal -->
            <div class="mt-5 pt-4 border-t border-gray-100 text-center">
                <p class="text-xs text-slate-400">Ada masalah dengan pesanan ini? 
                    <button id="reviewReportLink" onclick="switchToReport()" class="text-red-500 font-bold hover:underline">Laporkan</button>
                </p>
            </div>
        </div>
    </div>

    <!-- Report Modal -->
    <div id="reportModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-[32px] p-8 w-full max-w-md shadow-2xl transform scale-95 transition-transform duration-300" id="reportModalBox">
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">🚩</span>
                    <h3 class="text-xl font-black text-slate-800">Buat Laporan</h3>
                </div>
                <button onclick="closeReportModal()" class="text-slate-400 hover:text-red-500 font-bold text-xl">&times;</button>
            </div>
            <p class="text-sm text-slate-500 mb-6 font-medium">Laporan Anda akan ditinjau oleh tim Centrivo dan ditindaklanjuti sesuai kebijakan platform.</p>

            <form id="reportForm" method="POST" action="{{ route('user.report.store') }}">
                @csrf
                <input type="hidden" name="reported_user_id" id="reportUserId">
                <input type="hidden" name="reported_service_id" id="reportServiceId">

                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Kategori Masalah</label>
                    <select name="reason" required class="w-full bg-gray-50 border border-gray-100 rounded-2xl p-4 text-sm font-medium outline-none focus:ring-2 focus:ring-red-200 transition-all">
                        <option value="" disabled selected>Pilih kategori...</option>
                        <option value="Penipuan">Penipuan / Tidak sesuai deskripsi</option>
                        <option value="Konten tidak pantas">Konten tidak pantas</option>
                        <option value="Harga tidak wajar">Harga tidak wajar / Manipulatif</option>
                        <option value="Tidak profesional">Perilaku tidak profesional</option>
                        <option value="Kualitas buruk">Kualitas layanan sangat buruk</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Detail Laporan (Opsional)</label>
                    <textarea name="description" rows="4" class="w-full bg-gray-50 border border-gray-100 rounded-2xl p-4 text-sm font-medium outline-none focus:ring-2 focus:ring-red-200 transition-all resize-none" placeholder="Ceritakan apa yang terjadi..."></textarea>
                </div>

                <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-4 rounded-2xl transition-all shadow-lg shadow-red-500/20">
                    Kirim Laporan
                </button>
            </form>
        </div>
    </div>

    <script>
        function switchTab(tab) {
            const tabs = ['pending', 'active', 'completed'];
            tabs.forEach(t => {
                const btn = document.getElementById('tab-' + t);
                const content = document.getElementById('content-' + t);
                if (t === tab) {
                    btn.className = "px-6 py-3 rounded-xl font-bold text-sm transition-all bg-color1 text-white shadow-lg shadow-color1/20 whitespace-nowrap";
                    content.classList.remove('hidden');
                } else {
                    btn.className = "px-6 py-3 rounded-xl font-bold text-sm transition-all text-slate-500 hover:bg-gray-100 whitespace-nowrap";
                    content.classList.add('hidden');
                }
            });
        }

        // Simpan data sementara untuk switch dari review ke report
        let _currentSellerId = null;
        let _currentServiceId = null;

        function openReviewModal(transactionId, sellerName, serviceId) {
            const modal = document.getElementById('reviewModal');
            const box = document.getElementById('reviewModalBox');
            document.getElementById('modalSellerName').innerText = sellerName;
            _currentSellerId = null; // akan diisi dari card
            _currentServiceId = serviceId;
            
            // Set form action dynamically
            document.getElementById('reviewForm').action = `/user/transactions/${transactionId}/complete`;
            
            modal.classList.remove('hidden');
            void modal.offsetWidth;
            modal.classList.remove('opacity-0');
            box.classList.remove('scale-95');
        }

        function closeReviewModal() {
            const modal = document.getElementById('reviewModal');
            const box = document.getElementById('reviewModalBox');
            modal.classList.add('opacity-0');
            box.classList.add('scale-95');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }

        function openReportModal(userId, serviceId, fromReview) {
            closeReviewModal();
            const modal = document.getElementById('reportModal');
            const box = document.getElementById('reportModalBox');

            // Set hidden inputs
            document.getElementById('reportUserId').value = userId || '';
            document.getElementById('reportServiceId').value = serviceId || '';

            modal.classList.remove('hidden');
            void modal.offsetWidth;
            modal.classList.remove('opacity-0');
            box.classList.remove('scale-95');
        }

        function closeReportModal() {
            const modal = document.getElementById('reportModal');
            const box = document.getElementById('reportModalBox');
            modal.classList.add('opacity-0');
            box.classList.add('scale-95');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }

        // Dipanggil dari link "Ada masalah? Laporkan" dalam Review Modal
        function switchToReport() {
            openReportModal(_currentSellerId, _currentServiceId, true);
        }
    </script>
</body>
</html>
