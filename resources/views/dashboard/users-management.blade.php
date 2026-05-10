@extends('dashboard.main')

@section('admin_content')

<div class="mb-6 flex flex-col md:flex-row items-center justify-between gap-4">
    <div>
        <h2 class="text-3xl font-black text-slate-800">Users Management</h2>
        <p class="text-slate-400 font-medium">Kelola semua seller dan user serta laporan mereka.</p>
    </div>
</div>

@if(session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
    <span class="block sm:inline">{{ session('success') }}</span>
</div>
@endif

<!-- Tabs -->
<div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden mb-6">
    <div class="flex border-b border-gray-100 flex-wrap">
        <a href="{{ route('users.management', ['tab' => 'seller', 'search' => $search]) }}" 
           class="flex-1 min-w-[150px] py-4 px-6 text-center font-bold transition-all {{ $tab === 'seller' ? 'text-color1 border-b-2 border-color1 bg-blue-50/50' : 'text-slate-400 hover:text-slate-600 hover:bg-gray-50' }}">
            Sellers
        </a>
        <a href="{{ route('users.management', ['tab' => 'user', 'search' => $search]) }}" 
           class="flex-1 min-w-[150px] py-4 px-6 text-center font-bold transition-all {{ $tab === 'user' ? 'text-color1 border-b-2 border-color1 bg-blue-50/50' : 'text-slate-400 hover:text-slate-600 hover:bg-gray-50' }}">
            Users
        </a>
    </div>

    <!-- Search Bar -->
    <div class="p-6 border-b border-gray-100 bg-gray-50/50">
        <form method="GET" action="{{ route('users.management') }}" class="flex flex-col sm:flex-row gap-2">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari email atau nama..." 
                   class="flex-1 rounded-2xl border border-gray-200 px-4 py-3 focus:outline-none focus:border-color1 focus:ring-2 focus:ring-color1/20 transition-all">
            <button type="submit" class="bg-color1 hover:bg-color2 text-white px-6 py-3 rounded-2xl font-bold transition-all shadow-lg shadow-color1/20 whitespace-nowrap">
                Cari Data
            </button>
        </form>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[700px]">
            <thead>
                <tr class="bg-gray-50 text-slate-500 text-sm uppercase tracking-wider">
                    <th class="p-4 font-bold border-b border-gray-100">User / Brand</th>
                    <th class="p-4 font-bold border-b border-gray-100">Email</th>
                    <th class="p-4 font-bold border-b border-gray-100">Reports</th>
                    <th class="p-4 font-bold border-b border-gray-100">Status</th>
                    <th class="p-4 font-bold border-b border-gray-100 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50 transition-all">
                    <td class="p-4">
                        <p class="font-bold text-slate-800">
                            @if($user->role === 'seller')
                                {{ $user->sellerProfile->brand_name ?? 'N/A' }}
                            @else
                                {{ $user->userProfile->name ?? 'N/A' }}
                            @endif
                        </p>
                    </td>
                    <td class="p-4 text-slate-600">{{ $user->email }}</td>
                    <td class="p-4">
                        @if($user->reports_received_count > 0)
                            <span class="px-3 py-1 bg-orange-100 text-orange-600 rounded-full text-xs font-bold">
                                {{ $user->reports_received_count }} Reports
                            </span>
                        @else
                            <span class="px-3 py-1 bg-gray-100 text-gray-500 rounded-full text-xs font-bold">
                                0 Reports
                            </span>
                        @endif
                    </td>
                    <td class="p-4">
                        @if($user->is_banned)
                            <span class="px-3 py-1 bg-red-100 text-red-600 rounded-full text-xs font-bold">Banned</span>
                        @else
                            <span class="px-3 py-1 bg-green-100 text-green-600 rounded-full text-xs font-bold">Active</span>
                        @endif
                    </td>
                    <td class="p-4 flex gap-2 justify-end">
                        <button onclick="viewReports({{ $user->id }})" class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-2 rounded-xl text-sm font-bold transition-all whitespace-nowrap">
                            See Reports
                        </button>
                        @if($user->is_banned)
                            <form action="{{ route('users.unban', $user->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="bg-green-100 hover:bg-green-200 text-green-700 px-4 py-2 rounded-xl text-sm font-bold transition-all whitespace-nowrap" onclick="return confirm('Apakah Anda yakin ingin membuka ban user ini?')">
                                    Unban
                                </button>
                            </form>
                        @else
                            <button type="button" onclick="openBanModal({{ $user->id }}, '{{ addslashes($user->role === 'seller' ? ($user->sellerProfile->brand_name ?? $user->email) : ($user->userProfile->name ?? $user->email)) }}')" 
                                    class="bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded-xl text-sm font-bold transition-all whitespace-nowrap">
                                Ban User
                            </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-8 text-center text-slate-400 font-bold">
                        Tidak ada data yang ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="p-6 border-t border-gray-100">
        {{ $users->links() }}
    </div>
</div>

</div>

<!-- Modal Ban Reason -->
<div id="banModal" class="fixed inset-0 z-[110] hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center transition-opacity opacity-0 p-4">
    <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl transform scale-95 transition-transform duration-300" id="banModalContent">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-3xl">
            <h3 class="text-xl font-black text-slate-800">Konfirmasi Ban</h3>
            <button onclick="closeBanModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-200 hover:bg-gray-300 text-slate-600 transition-all font-bold">✕</button>
        </div>
        <form id="banForm" method="POST">
            @csrf
            <div class="p-6">
                <p class="text-sm text-slate-500 mb-4 font-medium">Anda akan menonaktifkan akun <span id="banUserName" class="font-bold text-slate-800"></span>.</p>
                
                <div class="bg-yellow-50 border border-yellow-100 rounded-2xl p-4 mb-6">
                    <p class="text-xs text-yellow-700 font-bold mb-1">💡 INFO:</p>
                    <p class="text-[10px] text-yellow-600 font-medium leading-relaxed">
                        Jika user ini memiliki transaksi berjalan, status transaksi akan otomatis menjadi <b>"Disputed"</b> (Bermasalah) dan dana akan dibekukan sementara.
                    </p>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Alasan Penonaktifan</label>
                    <textarea name="ban_reason" required rows="4" 
                              class="w-full bg-slate-50 border border-gray-100 rounded-2xl p-4 text-sm font-medium outline-none focus:ring-2 focus:ring-red-200 transition-all resize-none"
                              placeholder="Contoh: Terdeteksi melakukan penipuan pada transaksi #123..."></textarea>
                    <p class="text-[10px] text-slate-400 mt-2">Alasan ini akan ditampilkan di layar user yang bersangkutan.</p>
                </div>
            </div>
            <div class="p-6 border-t border-gray-100 bg-gray-50 rounded-b-3xl flex gap-3">
                <button type="button" onclick="closeBanModal()" class="flex-1 px-6 py-3 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-2xl font-bold transition-all text-sm">Batal</button>
                <button type="submit" class="flex-1 px-6 py-3 bg-red-500 hover:bg-red-600 text-white rounded-2xl font-bold transition-all shadow-lg shadow-red-500/20 text-sm">Ya, Ban User</button>
            </div>
        </form>
    </div>
</div>
<div id="reportsModal" class="fixed inset-0 z-[100] hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center transition-opacity opacity-0 p-4">
    <div class="bg-white rounded-3xl w-full max-w-3xl max-h-[90vh] flex flex-col shadow-2xl transform scale-95 transition-transform duration-300" id="reportsModalContent">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-3xl">
            <h3 class="text-xl font-black text-slate-800">Detail Reports</h3>
            <button onclick="closeModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-200 hover:bg-gray-300 text-slate-600 transition-all font-bold">
                ✕
            </button>
        </div>
        <div class="p-6 overflow-y-auto flex-grow" id="reportsModalBody">
            <div class="flex justify-center py-10">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-color1"></div>
            </div>
        </div>
        <div class="p-6 border-t border-gray-100 bg-gray-50 rounded-b-3xl flex justify-end">
            <button onclick="closeModal()" class="px-6 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl font-bold transition-all">Tutup</button>
        </div>
    </div>
</div>

<script>
    function viewReports(userId) {
        const modal = document.getElementById('reportsModal');
        const modalContent = document.getElementById('reportsModalContent');
        const modalBody = document.getElementById('reportsModalBody');
        
        modal.classList.remove('hidden');
        // Force reflow
        void modal.offsetWidth;
        modal.classList.remove('opacity-0');
        modalContent.classList.remove('scale-95');

        modalBody.innerHTML = `
            <div class="flex justify-center py-10">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-color1"></div>
            </div>
        `;

        fetch(`/users-management/${userId}/reports`)
            .then(response => response.json())
            .then(data => {
                const user = data.user;
                const reports = data.reports;
                
                let name = user.role === 'seller' 
                    ? (user.seller_profile ? user.seller_profile.brand_name : 'N/A') 
                    : (user.user_profile ? user.user_profile.name : 'N/A');

                let html = `
                    <div class="mb-6 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                        <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">Informasi User</p>
                        <p class="text-lg font-black text-slate-800">${name}</p>
                        <p class="text-sm font-medium text-slate-500">${user.email}</p>
                    </div>
                `;

                if (reports.length === 0) {
                    html += `
                        <div class="text-center py-8">
                            <span class="text-4xl mb-3 block opacity-50">📋</span>
                            <p class="text-slate-500 font-bold text-lg">Belum ada laporan</p>
                            <p class="text-slate-400 text-sm">User ini bersih dari laporan.</p>
                        </div>
                    `;
                } else {
                    html += `<div class="space-y-4">
                                <h4 class="font-bold text-slate-700 text-sm uppercase tracking-widest border-b border-gray-100 pb-2">Daftar Laporan (${reports.length})</h4>`;
                    reports.forEach(report => {
                        let reporterName = report.reporter && report.reporter.user_profile ? report.reporter.user_profile.name : 'Unknown';
                        let statusColor = report.status === 'pending' ? 'bg-orange-100 text-orange-600' : 
                                          (report.status === 'reviewed' ? 'bg-blue-100 text-blue-600' : 'bg-green-100 text-green-600');
                        
                        html += `
                            <div class="p-5 border border-red-100 bg-red-50/30 hover:bg-red-50 transition-colors rounded-2xl">
                                <div class="flex flex-wrap justify-between items-start mb-3 gap-2">
                                    <div class="flex gap-2 items-center">
                                        <span class="px-3 py-1 bg-red-100 text-red-600 rounded-lg text-xs font-bold uppercase tracking-wider">
                                            ${report.reason}
                                        </span>
                                        <span class="px-3 py-1 ${statusColor} rounded-lg text-xs font-bold uppercase tracking-wider">
                                            ${report.status}
                                        </span>
                                    </div>
                                    <span class="text-xs text-slate-400 font-bold">${new Date(report.created_at).toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'})}</span>
                                </div>
                                <div class="bg-white p-4 rounded-xl border border-red-50 mb-3 shadow-sm">
                                    <p class="text-slate-700 text-sm font-medium">${report.description || 'Tidak ada deskripsi rinci.'}</p>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                                    <span>Dilaporkan oleh:</span>
                                    <span class="font-bold text-slate-800 bg-white px-2 py-1 rounded-md border border-gray-100">${reporterName}</span>
                                </div>
                            </div>
                        `;
                    });
                    html += `</div>`;
                }

                modalBody.innerHTML = html;
            })
            .catch(error => {
                modalBody.innerHTML = `
                    <div class="text-center py-10 bg-red-50 rounded-2xl border border-red-100">
                        <span class="text-4xl mb-3 block">⚠️</span>
                        <p class="text-red-600 font-bold mb-1">Gagal memuat laporan</p>
                        <p class="text-red-400 text-sm">Terjadi kesalahan pada server. Silakan coba lagi.</p>
                    </div>
                `;
            });
    }

    function closeModal() {
        const modal = document.getElementById('reportsModal');
        const modalContent = document.getElementById('reportsModalContent');
        
        modal.classList.add('opacity-0');
        modalContent.classList.add('scale-95');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    function openBanModal(userId, userName) {
        const modal = document.getElementById('banModal');
        const modalContent = document.getElementById('banModalContent');
        const form = document.getElementById('banForm');
        
        document.getElementById('banUserName').innerText = userName;
        form.action = `/users-management/${userId}/ban`;
        
        modal.classList.remove('hidden');
        void modal.offsetWidth;
        modal.classList.remove('opacity-0');
        modalContent.classList.remove('scale-95');
    }

    function closeBanModal() {
        const modal = document.getElementById('banModal');
        const modalContent = document.getElementById('banModalContent');
        
        modal.classList.add('opacity-0');
        modalContent.classList.add('scale-95');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    document.getElementById('reportsModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    document.getElementById('banModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeBanModal();
        }
    });
</script>

@endsection
