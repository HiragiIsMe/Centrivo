@extends('dashboard.main')

@section('admin_content')
<div class="mb-8">
    <h1 class="text-3xl font-black text-slate-800 tracking-tighter">Platform Settings</h1>
    <p class="text-slate-400 mt-1 font-medium">Kelola konfigurasi platform dan tampilan billboard utama.</p>
</div>

@if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-600 rounded-2xl font-bold text-sm">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- General Settings -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-[32px] p-8 border border-gray-100 shadow-sm sticky top-8">
            <h3 class="text-lg font-black text-slate-800 mb-6 tracking-tight">Konfigurasi Umum</h3>
            
            <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-5">
                @csrf
                @foreach($settings as $setting)
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">{{ $setting->label }}</label>
                    @if($setting->type == 'number')
                        <input type="number" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" class="w-full bg-slate-50 border border-slate-100 rounded-xl p-3 text-sm font-bold outline-none focus:ring-2 focus:ring-color1/20">
                    @else
                        <input type="text" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" class="w-full bg-slate-50 border border-slate-100 rounded-xl p-3 text-sm font-bold outline-none focus:ring-2 focus:ring-color1/20">
                    @endif
                </div>
                @endforeach
                
                <button type="submit" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-bold py-4 rounded-xl transition-all shadow-lg shadow-slate-800/10">
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>

    <!-- Billboard Management -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-[32px] p-8 border border-gray-100 shadow-sm mb-8">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-lg font-black text-slate-800 tracking-tight">Manajemen Billboard (Market)</h3>
                <button onclick="document.getElementById('addBillboardModal').classList.remove('hidden')" class="bg-color1 hover:bg-color2 text-white font-bold px-5 py-2 rounded-xl text-xs transition-all shadow-sm">
                    + Tambah Slide
                </button>
            </div>

            <div class="space-y-4">
                @forelse($billboards as $bb)
                <div class="p-6 border border-slate-50 rounded-[28px] bg-slate-50/30 group relative">
                    <div class="flex flex-col md:flex-row gap-6">
                        <!-- Preview Box -->
                        <div class="w-full md:w-48 aspect-[21/8] rounded-2xl flex flex-col items-center justify-center text-white p-4 relative overflow-hidden" 
                             style="background: linear-gradient(135deg, {{ $bb->gradient_from }}, {{ $bb->gradient_to }})">
                            @if($bb->image_path)
                                <img src="{{ asset('storage/' . $bb->image_path) }}" class="absolute inset-0 w-full h-full object-cover opacity-30">
                            @endif
                            <p class="relative z-10 text-[10px] font-black line-clamp-1 uppercase text-center">{{ $bb->title }}</p>
                        </div>
                        
                        <div class="flex-grow">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="bg-slate-200 text-slate-600 px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest">Order: {{ $bb->order }}</span>
                                @if($bb->is_active)
                                    <span class="bg-green-100 text-green-600 px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest">Aktif</span>
                                @else
                                    <span class="bg-red-100 text-red-600 px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest">Draft</span>
                                @endif
                            </div>
                            <h4 class="font-black text-slate-800">{{ $bb->title }}</h4>
                            <p class="text-xs text-slate-400 font-medium">{{ $bb->subtitle }}</p>
                            
                            <div class="mt-4 flex gap-2">
                                <button onclick="editBillboard({{ json_encode($bb) }})" class="text-[10px] font-black uppercase text-color1 hover:underline">Edit</button>
                                <form action="{{ route('admin.billboards.toggle', $bb->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-[10px] font-black uppercase text-slate-400 hover:text-slate-600">Toggle Status</button>
                                </form>
                                <form action="{{ route('admin.billboards.destroy', $bb->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus slide ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-[10px] font-black uppercase text-red-400 hover:text-red-600">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-12 text-slate-400">Belum ada slide billboard.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div id="addBillboardModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-6">
    <div class="bg-white rounded-[40px] p-10 w-full max-w-xl shadow-2xl">
        <h3 class="text-2xl font-black text-slate-800 mb-8 tracking-tighter">Tambah Slide Billboard</h3>
        <form action="{{ route('admin.billboards.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div class="grid grid-cols-2 gap-6">
                <div class="col-span-2">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Judul Utama</label>
                    <input type="text" name="title" required class="w-full bg-slate-50 border border-slate-100 rounded-xl p-3 text-sm font-bold outline-none focus:ring-2 focus:ring-color1/20">
                </div>
                <div class="col-span-2">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Sub-judul (Opsional)</label>
                    <input type="text" name="subtitle" class="w-full bg-slate-50 border border-slate-100 rounded-xl p-3 text-sm font-bold outline-none focus:ring-2 focus:ring-color1/20">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Warna Gradient Dari</label>
                    <input type="color" name="gradient_from" value="#628ECB" class="w-full h-12 bg-slate-50 border border-slate-100 rounded-xl p-1 outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Warna Gradient Ke</label>
                    <input type="color" name="gradient_to" value="#8AAEE0" class="w-full h-12 bg-slate-50 border border-slate-100 rounded-xl p-1 outline-none">
                </div>
                <div class="col-span-2">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Background Image (Opsional)</label>
                    <input type="file" name="image" class="w-full bg-slate-50 border border-slate-100 rounded-xl p-3 text-xs font-bold outline-none">
                </div>
            </div>
            
            <div class="flex gap-4 pt-4">
                <button type="button" onclick="document.getElementById('addBillboardModal').classList.add('hidden')" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-4 rounded-2xl transition-all">Batal</button>
                <button type="submit" class="flex-1 bg-color1 hover:bg-color2 text-white font-bold py-4 rounded-2xl transition-all shadow-lg shadow-color1/20">Tambah Slide</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal (Simplified logic for now) -->
<div id="editBillboardModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-6">
    <div class="bg-white rounded-[40px] p-10 w-full max-w-xl shadow-2xl">
        <h3 class="text-2xl font-black text-slate-800 mb-8 tracking-tighter">Edit Billboard</h3>
        <form id="editForm" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-6">
                <div class="col-span-2">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Judul Utama</label>
                    <input type="text" name="title" id="editTitle" required class="w-full bg-slate-50 border border-slate-100 rounded-xl p-3 text-sm font-bold outline-none focus:ring-2 focus:ring-color1/20">
                </div>
                <div class="col-span-2">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Sub-judul</label>
                    <input type="text" name="subtitle" id="editSubtitle" class="w-full bg-slate-50 border border-slate-100 rounded-xl p-3 text-sm font-bold outline-none focus:ring-2 focus:ring-color1/20">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Warna Gradient Dari</label>
                    <input type="color" name="gradient_from" id="editFrom" class="w-full h-12 bg-slate-50 border border-slate-100 rounded-xl p-1">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Warna Gradient Ke</label>
                    <input type="color" name="gradient_to" id="editTo" class="w-full h-12 bg-slate-50 border border-slate-100 rounded-xl p-1">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Order</label>
                    <input type="number" name="order" id="editOrder" class="w-full bg-slate-50 border border-slate-100 rounded-xl p-3 text-sm font-bold">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Image</label>
                    <input type="file" name="image" class="w-full bg-slate-50 border border-slate-100 rounded-xl p-3 text-[10px] font-bold">
                </div>
            </div>
            
            <div class="flex gap-4 pt-4">
                <button type="button" onclick="document.getElementById('editBillboardModal').classList.add('hidden')" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-4 rounded-2xl transition-all">Batal</button>
                <button type="submit" class="flex-1 bg-slate-800 hover:bg-slate-900 text-white font-bold py-4 rounded-2xl transition-all shadow-lg">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function editBillboard(bb) {
        const modal = document.getElementById('editBillboardModal');
        const form = document.getElementById('editForm');
        form.action = `/admin/billboards/${bb.id}`;
        
        document.getElementById('editTitle').value = bb.title;
        document.getElementById('editSubtitle').value = bb.subtitle || '';
        document.getElementById('editFrom').value = bb.gradient_from;
        document.getElementById('editTo').value = bb.gradient_to;
        document.getElementById('editOrder').value = bb.order;
        
        modal.classList.remove('hidden');
    }
</script>
@endsection
