@extends('dashboard.main')

@section('admin_content')

<div class="mb-6 flex flex-col md:flex-row items-center justify-between gap-4">
    <div>
        <h2 class="text-3xl font-black text-slate-800">Services & Categories</h2>
        <p class="text-slate-400 font-medium">Pantau semua layanan dari seller dan kelola kategori.</p>
    </div>
</div>

@if(session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
    <span class="block sm:inline">{{ session('success') }}</span>
</div>
@endif

@if($errors->any())
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
    <ul class="list-disc pl-5">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<!-- Tabs -->
<div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden mb-6">
    <div class="flex border-b border-gray-100 flex-wrap">
        <a href="{{ route('admin.services.index', ['tab' => 'services', 'search' => $search]) }}" 
           class="flex-1 min-w-[150px] py-4 px-6 text-center font-bold transition-all {{ $tab === 'services' ? 'text-color1 border-b-2 border-color1 bg-blue-50/50' : 'text-slate-400 hover:text-slate-600 hover:bg-gray-50' }}">
            Services
        </a>
        <a href="{{ route('admin.services.index', ['tab' => 'categories']) }}" 
           class="flex-1 min-w-[150px] py-4 px-6 text-center font-bold transition-all {{ $tab === 'categories' ? 'text-color1 border-b-2 border-color1 bg-blue-50/50' : 'text-slate-400 hover:text-slate-600 hover:bg-gray-50' }}">
            Categories
        </a>
    </div>

    @if($tab === 'services')
    <!-- Search Bar for Services -->
    <div class="p-6 border-b border-gray-100 bg-gray-50/50">
        <form method="GET" action="{{ route('admin.services.index') }}" class="flex flex-col sm:flex-row gap-2">
            <input type="hidden" name="tab" value="services">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama layanan..." 
                   class="flex-1 rounded-2xl border border-gray-200 px-4 py-3 focus:outline-none focus:border-color1 focus:ring-2 focus:ring-color1/20 transition-all">
            <button type="submit" class="bg-color1 hover:bg-color2 text-white px-6 py-3 rounded-2xl font-bold transition-all shadow-lg shadow-color1/20 whitespace-nowrap">
                Cari Layanan
            </button>
        </form>
    </div>

    <!-- Table Services -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[900px]">
            <thead>
                <tr class="bg-gray-50 text-slate-500 text-sm uppercase tracking-wider">
                    <th class="p-4 font-bold border-b border-gray-100">Service Name</th>
                    <th class="p-4 font-bold border-b border-gray-100">Seller / Brand</th>
                    <th class="p-4 font-bold border-b border-gray-100">Category</th>
                    <th class="p-4 font-bold border-b border-gray-100">Price</th>
                    <th class="p-4 font-bold border-b border-gray-100">Reports</th>
                    <th class="p-4 font-bold border-b border-gray-100">Status</th>
                    <th class="p-4 font-bold border-b border-gray-100 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($services as $service)
                <tr class="hover:bg-gray-50 transition-all">
                    <td class="p-4">
                        <p class="font-bold text-slate-800">{{ $service->service_name }}</p>
                    </td>
                    <td class="p-4">
                        <p class="font-medium text-slate-700">{{ $service->seller->sellerProfile->brand_name ?? 'N/A' }}</p>
                        <p class="text-xs text-slate-500">{{ $service->seller->email }}</p>
                    </td>
                    <td class="p-4">
                        <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-xs font-bold">{{ $service->category->name ?? 'Uncategorized' }}</span>
                    </td>
                    <td class="p-4 font-bold text-slate-700">Rp {{ number_format($service->start_price, 0, ',', '.') }}</td>
                    <td class="p-4">
                        @if($service->reports_received_count > 0)
                            <span class="px-3 py-1 bg-orange-100 text-orange-600 rounded-full text-xs font-bold">
                                {{ $service->reports_received_count }} Reports
                            </span>
                        @else
                            <span class="px-3 py-1 bg-gray-100 text-gray-500 rounded-full text-xs font-bold">0 Reports</span>
                        @endif
                    </td>
                    <td class="p-4">
                        @if($service->is_banned)
                            <span class="px-3 py-1 bg-red-100 text-red-600 rounded-full text-xs font-bold">Banned</span>
                        @else
                            <span class="px-3 py-1 bg-green-100 text-green-600 rounded-full text-xs font-bold">Active</span>
                        @endif
                    </td>
                    <td class="p-4 flex gap-2 justify-end">
                        <button onclick="viewReports({{ $service->id }})" class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-2 rounded-xl text-sm font-bold transition-all whitespace-nowrap">
                            See Reports
                        </button>
                        @if($service->is_banned)
                            <form action="{{ route('admin.services.unban', $service->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="bg-green-100 hover:bg-green-200 text-green-700 px-4 py-2 rounded-xl text-sm font-bold transition-all whitespace-nowrap" onclick="return confirm('Apakah Anda yakin ingin membuka ban layanan ini?')">
                                    Unban
                                </button>
                            </form>
                        @else
                            <button type="button" onclick="openBanServiceModal({{ $service->id }}, '{{ addslashes($service->service_name) }}')"
                                    class="bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded-xl text-sm font-bold transition-all whitespace-nowrap">
                                Ban Service
                            </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="p-8 text-center text-slate-400 font-bold">
                        Tidak ada layanan yang ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-6 border-t border-gray-100">
        {{ $services->links() }}
    </div>

    @else
    <!-- Categories Tab -->
    <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
        <h3 class="font-bold text-slate-700 text-lg">Kelola Kategori</h3>
        <button onclick="openCategoryModal()" class="bg-color1 hover:bg-color2 text-white px-6 py-3 rounded-2xl font-bold transition-all shadow-lg shadow-color1/20 whitespace-nowrap">
            + Tambah Kategori
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[600px]">
            <thead>
                <tr class="bg-gray-50 text-slate-500 text-sm uppercase tracking-wider">
                    <th class="p-4 font-bold border-b border-gray-100 w-16">ID</th>
                    <th class="p-4 font-bold border-b border-gray-100">Category Name</th>
                    <th class="p-4 font-bold border-b border-gray-100">Created At</th>
                    <th class="p-4 font-bold border-b border-gray-100 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($categories as $category)
                <tr class="hover:bg-gray-50 transition-all">
                    <td class="p-4 text-slate-500 font-bold">#{{ $category->id }}</td>
                    <td class="p-4">
                        <p class="font-bold text-slate-800">{{ $category->name }}</p>
                    </td>
                    <td class="p-4 text-slate-500 text-sm">
                        {{ $category->created_at->format('d M Y') }}
                    </td>
                    <td class="p-4 flex gap-2 justify-end">
                        <button onclick="editCategory({{ $category->id }}, '{{ addslashes($category->name) }}')" class="bg-yellow-100 hover:bg-yellow-200 text-yellow-700 px-4 py-2 rounded-xl text-sm font-bold transition-all whitespace-nowrap">
                            Edit
                        </button>
                        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded-xl text-sm font-bold transition-all whitespace-nowrap" onclick="return confirm('Hapus kategori ini? Aksi ini mungkin mempengaruhi layanan yang terkait.')">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-8 text-center text-slate-400 font-bold">
                        Belum ada kategori.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-6 border-t border-gray-100">
        {{ $categories->links() }}
    </div>
    @endif
</div>

@if($tab === 'services')
<!-- Modal Ban Service -->
<div id="banServiceModal" class="fixed inset-0 z-[110] hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center transition-opacity opacity-0 p-4">
    <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl transform scale-95 transition-transform duration-300" id="banServiceModalContent">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-3xl">
            <h3 class="text-xl font-black text-slate-800">Konfirmasi Ban Layanan</h3>
            <button onclick="closeBanServiceModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-200 hover:bg-gray-300 text-slate-600 transition-all font-bold">✕</button>
        </div>
        <form id="banServiceForm" method="POST">
            @csrf
            <div class="p-6">
                <p class="text-sm text-slate-500 mb-4 font-medium">Anda akan menonaktifkan layanan <span id="banServiceName" class="font-bold text-slate-800"></span>.</p>
                
                <div class="bg-yellow-50 border border-yellow-100 rounded-2xl p-4 mb-6">
                    <p class="text-xs text-yellow-700 font-bold mb-1">💡 INFO:</p>
                    <p class="text-[10px] text-yellow-600 font-medium leading-relaxed">
                        Transaksi aktif yang menggunakan layanan ini akan otomatis menjadi <b>"Disputed"</b> (Bermasalah) dan dana akan dibekukan sementara.
                    </p>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Alasan Penonaktifan</label>
                    <textarea name="ban_reason" required rows="4" 
                              class="w-full bg-slate-50 border border-gray-100 rounded-2xl p-4 text-sm font-medium outline-none focus:ring-2 focus:ring-red-200 transition-all resize-none"
                              placeholder="Contoh: Layanan terdeteksi melanggar hak cipta / penipuan..."></textarea>
                </div>
            </div>
            <div class="p-6 border-t border-gray-100 bg-gray-50 rounded-b-3xl flex gap-3">
                <button type="button" onclick="closeBanServiceModal()" class="flex-1 px-6 py-3 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-2xl font-bold transition-all text-sm">Batal</button>
                <button type="submit" class="flex-1 px-6 py-3 bg-red-500 hover:bg-red-600 text-white rounded-2xl font-bold transition-all shadow-lg shadow-red-500/20 text-sm">Ya, Ban Layanan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Reports -->
<div id="reportsModal" class="fixed inset-0 z-[100] hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center transition-opacity opacity-0 p-4">
    <div class="bg-white rounded-3xl w-full max-w-3xl max-h-[90vh] flex flex-col shadow-2xl transform scale-95 transition-transform duration-300" id="reportsModalContent">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-3xl">
            <h3 class="text-xl font-black text-slate-800">Detail Laporan Layanan</h3>
            <button onclick="closeReportsModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-200 hover:bg-gray-300 text-slate-600 transition-all font-bold">
                ✕
            </button>
        </div>
        <div class="p-6 overflow-y-auto flex-grow" id="reportsModalBody">
            <div class="flex justify-center py-10">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-color1"></div>
            </div>
        </div>
        <div class="p-6 border-t border-gray-100 bg-gray-50 rounded-b-3xl flex justify-end">
            <button onclick="closeReportsModal()" class="px-6 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl font-bold transition-all">Tutup</button>
        </div>
    </div>
</div>
@endif

@if($tab === 'categories')
<!-- Modal Category (Add/Edit) -->
<div id="categoryModal" class="fixed inset-0 z-[100] hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center transition-opacity opacity-0 p-4">
    <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl transform scale-95 transition-transform duration-300" id="categoryModalContent">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-3xl">
            <h3 class="text-xl font-black text-slate-800" id="categoryModalTitle">Tambah Kategori</h3>
            <button onclick="closeCategoryModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-200 hover:bg-gray-300 text-slate-600 transition-all font-bold">
                ✕
            </button>
        </div>
        <form id="categoryForm" action="{{ route('admin.categories.store') }}" method="POST">
            @csrf
            <input type="hidden" name="_method" id="categoryMethod" value="POST">
            <div class="p-6">
                <div class="mb-4">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Nama Kategori</label>
                    <input type="text" name="name" id="categoryName" required class="w-full rounded-2xl border border-gray-200 px-4 py-3 focus:outline-none focus:border-color1 focus:ring-2 focus:ring-color1/20 transition-all" placeholder="Masukkan nama kategori">
                </div>
            </div>
            <div class="p-6 border-t border-gray-100 bg-gray-50 rounded-b-3xl flex justify-end gap-2">
                <button type="button" onclick="closeCategoryModal()" class="px-6 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl font-bold transition-all">Batal</button>
                <button type="submit" class="px-6 py-2 bg-color1 hover:bg-color2 text-white rounded-xl font-bold transition-all shadow-lg shadow-color1/20" id="categorySubmitBtn">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
    // Scripts for Services Reports Modal
    @if($tab === 'services')
    function viewReports(serviceId) {
        const modal = document.getElementById('reportsModal');
        const modalContent = document.getElementById('reportsModalContent');
        const modalBody = document.getElementById('reportsModalBody');
        
        modal.classList.remove('hidden');
        void modal.offsetWidth; // Force reflow
        modal.classList.remove('opacity-0');
        modalContent.classList.remove('scale-95');

        modalBody.innerHTML = `
            <div class="flex justify-center py-10">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-color1"></div>
            </div>
        `;

        fetch(`/services-categories/${serviceId}/reports`)
            .then(response => response.json())
            .then(data => {
                const service = data.service;
                const reports = data.reports;
                
                let sellerName = service.seller && service.seller.seller_profile ? service.seller.seller_profile.brand_name : 'N/A';
                let price = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(service.start_price);

                let html = `
                    <div class="mb-6 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                        <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">Informasi Layanan</p>
                        <p class="text-xl font-black text-slate-800">${service.service_name}</p>
                        <div class="mt-2 flex flex-wrap gap-3">
                            <span class="px-3 py-1 bg-white border border-gray-200 rounded-lg text-xs font-bold text-slate-600">👤 ${sellerName}</span>
                            <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-bold">🏷️ ${service.category ? service.category.name : 'N/A'}</span>
                            <span class="px-3 py-1 bg-green-50 text-green-600 rounded-lg text-xs font-bold">💵 ${price}</span>
                        </div>
                    </div>
                `;

                if (reports.length === 0) {
                    html += `
                        <div class="text-center py-8">
                            <span class="text-4xl mb-3 block opacity-50">📋</span>
                            <p class="text-slate-500 font-bold text-lg">Belum ada laporan</p>
                            <p class="text-slate-400 text-sm">Layanan ini bersih dari laporan.</p>
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

    function closeReportsModal() {
        const modal = document.getElementById('reportsModal');
        const modalContent = document.getElementById('reportsModalContent');
        
        modal.classList.add('opacity-0');
        modalContent.classList.add('scale-95');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    document.getElementById('reportsModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeReportsModal();
        }
    });

    function openBanServiceModal(serviceId, serviceName) {
        const modal = document.getElementById('banServiceModal');
        const modalContent = document.getElementById('banServiceModalContent');
        const form = document.getElementById('banServiceForm');
        
        document.getElementById('banServiceName').innerText = serviceName;
        form.action = `/services-categories/${serviceId}/ban`;
        
        modal.classList.remove('hidden');
        void modal.offsetWidth;
        modal.classList.remove('opacity-0');
        modalContent.classList.remove('scale-95');
    }

    function closeBanServiceModal() {
        const modal = document.getElementById('banServiceModal');
        const modalContent = document.getElementById('banServiceModalContent');
        
        modal.classList.add('opacity-0');
        modalContent.classList.add('scale-95');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    document.getElementById('banServiceModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeBanServiceModal();
        }
    });
    @endif

    // Scripts for Categories Modal
    @if($tab === 'categories')
    function openCategoryModal() {
        const modal = document.getElementById('categoryModal');
        const modalContent = document.getElementById('categoryModalContent');
        
        document.getElementById('categoryModalTitle').innerText = 'Tambah Kategori';
        document.getElementById('categoryForm').action = "{{ route('admin.categories.store') }}";
        document.getElementById('categoryMethod').value = 'POST';
        document.getElementById('categoryName').value = '';
        document.getElementById('categorySubmitBtn').innerText = 'Simpan Baru';

        modal.classList.remove('hidden');
        void modal.offsetWidth;
        modal.classList.remove('opacity-0');
        modalContent.classList.remove('scale-95');
    }

    function editCategory(id, name) {
        const modal = document.getElementById('categoryModal');
        const modalContent = document.getElementById('categoryModalContent');
        
        document.getElementById('categoryModalTitle').innerText = 'Edit Kategori';
        document.getElementById('categoryForm').action = `/categories/${id}`;
        document.getElementById('categoryMethod').value = 'PUT';
        document.getElementById('categoryName').value = name;
        document.getElementById('categorySubmitBtn').innerText = 'Simpan Perubahan';

        modal.classList.remove('hidden');
        void modal.offsetWidth;
        modal.classList.remove('opacity-0');
        modalContent.classList.remove('scale-95');
    }

    function closeCategoryModal() {
        const modal = document.getElementById('categoryModal');
        const modalContent = document.getElementById('categoryModalContent');
        
        modal.classList.add('opacity-0');
        modalContent.classList.add('scale-95');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    document.getElementById('categoryModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeCategoryModal();
        }
    });
    @endif

</script>

@endsection
