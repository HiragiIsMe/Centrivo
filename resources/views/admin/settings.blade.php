@extends('dashboard.main')

@section('admin_content')
<div class="mb-8">
    <h1 class="text-3xl font-black text-slate-800 tracking-tighter">Platform Settings</h1>
    <p class="text-slate-400 mt-1 font-medium">Kelola konfigurasi platform dan tampilan billboard utama.</p>
</div>

@if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-600 rounded-2xl font-bold text-sm">{{ session('success') }}</div>
@endif

@if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-600 rounded-2xl font-bold text-sm">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
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
                    <input type="text" name="title" value="{{ old('title') }}" required class="w-full bg-slate-50 border border-slate-100 rounded-xl p-3 text-sm font-bold outline-none focus:ring-2 focus:ring-color1/20">
                    @error('title')<p class="text-red-500 text-xs mt-1 px-1">{{ $message }}</p>@enderror
                </div>
                <div class="col-span-2">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Sub-judul (Opsional)</label>
                    <input type="text" name="subtitle" class="w-full bg-slate-50 border border-slate-100 rounded-xl p-3 text-sm font-bold outline-none focus:ring-2 focus:ring-color1/20">
                </div>

                <!-- Gradient From with Opacity -->
                <div class="col-span-2">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Warna Gradient Awal</label>
                    <div class="flex items-center gap-3 bg-slate-50 border border-slate-100 rounded-xl p-3">
                        <input type="color" id="addFromColor" value="#628ECB"
                               class="w-10 h-10 rounded-lg border-0 cursor-pointer bg-transparent flex-shrink-0"
                               oninput="updateGradientValue('add', 'from')">
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[10px] font-bold text-slate-400">Opacity</span>
                                <span id="addFromOpacityLabel" class="text-[10px] font-black text-slate-700">100%</span>
                            </div>
                            <input type="range" id="addFromOpacity" min="0" max="100" value="100"
                                   class="w-full h-1.5 rounded-full accent-color1 cursor-pointer"
                                   oninput="updateGradientValue('add', 'from')">
                        </div>
                        <div id="addFromPreview" class="w-8 h-8 rounded-lg border border-slate-200 flex-shrink-0" style="background-color: #628ECB"></div>
                    </div>
                    <input type="hidden" id="addFromHidden" name="gradient_from" value="#628ECB">
                </div>

                <!-- Gradient To with Opacity -->
                <div class="col-span-2">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Warna Gradient Akhir</label>
                    <div class="flex items-center gap-3 bg-slate-50 border border-slate-100 rounded-xl p-3">
                        <input type="color" id="addToColor" value="#8AAEE0"
                               class="w-10 h-10 rounded-lg border-0 cursor-pointer bg-transparent flex-shrink-0"
                               oninput="updateGradientValue('add', 'to')">
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[10px] font-bold text-slate-400">Opacity</span>
                                <span id="addToOpacityLabel" class="text-[10px] font-black text-slate-700">100%</span>
                            </div>
                            <input type="range" id="addToOpacity" min="0" max="100" value="100"
                                   class="w-full h-1.5 rounded-full accent-color1 cursor-pointer"
                                   oninput="updateGradientValue('add', 'to')">
                        </div>
                        <div id="addToPreview" class="w-8 h-8 rounded-lg border border-slate-200 flex-shrink-0" style="background-color: #8AAEE0"></div>
                    </div>
                    <input type="hidden" id="addToHidden" name="gradient_to" value="#8AAEE0">
                </div>

                <!-- Live Preview -->
                <div class="col-span-2">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Preview Gradient</label>
                    <div id="addGradientPreview" class="w-full h-16 rounded-xl border border-slate-100" style="background: linear-gradient(135deg, #628ECB, #8AAEE0)"></div>
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

<!-- Edit Modal -->
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

                <!-- Gradient From with Opacity -->
                <div class="col-span-2">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Warna Gradient Awal</label>
                    <div class="flex items-center gap-3 bg-slate-50 border border-slate-100 rounded-xl p-3">
                        <input type="color" id="editFromColor" value="#628ECB"
                               class="w-10 h-10 rounded-lg border-0 cursor-pointer bg-transparent flex-shrink-0"
                               oninput="updateGradientValue('edit', 'from')">
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[10px] font-bold text-slate-400">Opacity</span>
                                <span id="editFromOpacityLabel" class="text-[10px] font-black text-slate-700">100%</span>
                            </div>
                            <input type="range" id="editFromOpacity" min="0" max="100" value="100"
                                   class="w-full h-1.5 rounded-full accent-color1 cursor-pointer"
                                   oninput="updateGradientValue('edit', 'from')">
                        </div>
                        <div id="editFromPreview" class="w-8 h-8 rounded-lg border border-slate-200 flex-shrink-0" style="background-color: #628ECB"></div>
                    </div>
                    <input type="hidden" id="editFromHidden" name="gradient_from" value="#628ECB">
                </div>

                <!-- Gradient To with Opacity -->
                <div class="col-span-2">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Warna Gradient Akhir</label>
                    <div class="flex items-center gap-3 bg-slate-50 border border-slate-100 rounded-xl p-3">
                        <input type="color" id="editToColor" value="#8AAEE0"
                               class="w-10 h-10 rounded-lg border-0 cursor-pointer bg-transparent flex-shrink-0"
                               oninput="updateGradientValue('edit', 'to')">
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[10px] font-bold text-slate-400">Opacity</span>
                                <span id="editToOpacityLabel" class="text-[10px] font-black text-slate-700">100%</span>
                            </div>
                            <input type="range" id="editToOpacity" min="0" max="100" value="100"
                                   class="w-full h-1.5 rounded-full accent-color1 cursor-pointer"
                                   oninput="updateGradientValue('edit', 'to')">
                        </div>
                        <div id="editToPreview" class="w-8 h-8 rounded-lg border border-slate-200 flex-shrink-0" style="background-color: #8AAEE0"></div>
                    </div>
                    <input type="hidden" id="editToHidden" name="gradient_to" value="#8AAEE0">
                </div>

                <!-- Live Preview -->
                <div class="col-span-2">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Preview Gradient</label>
                    <div id="editGradientPreview" class="w-full h-16 rounded-xl border border-slate-100" style="background: linear-gradient(135deg, #628ECB, #8AAEE0)"></div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Order</label>
                    <input type="number" name="order" id="editOrder" class="w-full bg-slate-50 border border-slate-100 rounded-xl p-3 text-sm font-bold">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Ganti Image (Opsional)</label>
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
    /**
     * Parse sebuah hex color (6 atau 8 karakter) menjadi {hex, opacity}
     * Contoh: '#628ecb80' => { hex: '#628ecb', opacity: 50 }
     *         '#628ecb'   => { hex: '#628ecb', opacity: 100 }
     */
    function parseHexColor(hexRaw) {
        const hex = hexRaw ? hexRaw.trim() : '#000000';
        if (hex.length === 9) { // #RRGGBBAA
            const alpha = parseInt(hex.slice(7, 9), 16);
            return { hex: hex.slice(0, 7), opacity: Math.round((alpha / 255) * 100) };
        }
        return { hex: hex.length >= 7 ? hex.slice(0, 7) : '#000000', opacity: 100 };
    }

    /**
     * Konversi hex color + opacity (0-100) menjadi 8-char hex (#RRGGBBAA)
     */
    function buildHexWithAlpha(hex, opacity) {
        if (opacity >= 100) return hex; // simpan sebagai 6-char jika fully opaque
        const alpha = Math.round((opacity / 100) * 255);
        return hex + alpha.toString(16).padStart(2, '0');
    }

    /**
     * Update hidden input, preview box, label, dan gradient preview.
     * @param {string} context - 'add' atau 'edit'
     * @param {string} side    - 'from' atau 'to'
     */
    function updateGradientValue(context, side) {
        const prefix = context.charAt(0).toUpperCase() + context.slice(1); // 'Add' | 'Edit'
        const sideCap = side.charAt(0).toUpperCase() + side.slice(1);      // 'From' | 'To'

        const colorEl   = document.getElementById(`${context}${sideCap}Color`);
        const opacityEl = document.getElementById(`${context}${sideCap}Opacity`);
        const labelEl   = document.getElementById(`${context}${sideCap}OpacityLabel`);
        const previewEl = document.getElementById(`${context}${sideCap}Preview`);
        const hiddenEl  = document.getElementById(`${context}${sideCap}Hidden`);

        const hex = colorEl.value;
        const opacity = parseInt(opacityEl.value);
        const finalHex = buildHexWithAlpha(hex, opacity);

        labelEl.textContent  = opacity + '%';
        previewEl.style.backgroundColor = hexToRgba(hex, opacity);
        hiddenEl.value = finalHex;

        // Update gradient preview box
        const fromHidden = document.getElementById(`${context}FromHidden`).value;
        const toHidden   = document.getElementById(`${context}ToHidden`).value;
        const gradientEl = document.getElementById(`${context}GradientPreview`);
        gradientEl.style.background = `linear-gradient(135deg, ${fromHidden}, ${toHidden})`;
    }

    /**
     * Helper: hex + opacity% => rgba string (untuk preview box bg)
     */
    function hexToRgba(hex, opacity) {
        const r = parseInt(hex.slice(1, 3), 16);
        const g = parseInt(hex.slice(3, 5), 16);
        const b = parseInt(hex.slice(5, 7), 16);
        return `rgba(${r}, ${g}, ${b}, ${opacity / 100})`;
    }

    /**
     * Set nilai semua input di satu sisi (from/to) berdasarkan hex mentah dari DB.
     */
    function setColorInputs(context, side, rawHex) {
        const sideCap = side.charAt(0).toUpperCase() + side.slice(1);
        const { hex, opacity } = parseHexColor(rawHex);

        document.getElementById(`${context}${sideCap}Color`).value  = hex;
        document.getElementById(`${context}${sideCap}Opacity`).value = opacity;
        document.getElementById(`${context}${sideCap}OpacityLabel`).textContent = opacity + '%';
        document.getElementById(`${context}${sideCap}Preview`).style.backgroundColor = hexToRgba(hex, opacity);
        document.getElementById(`${context}${sideCap}Hidden`).value = rawHex ?? hex;
    }

    function editBillboard(bb) {
        const modal = document.getElementById('editBillboardModal');
        const form  = document.getElementById('editForm');
        form.action = `/admin/billboards/${bb.id}`;

        document.getElementById('editTitle').value    = bb.title;
        document.getElementById('editSubtitle').value = bb.subtitle || '';
        document.getElementById('editOrder').value    = bb.order;

        setColorInputs('edit', 'from', bb.gradient_from);
        setColorInputs('edit', 'to',   bb.gradient_to);

        // Refresh gradient preview
        document.getElementById('editGradientPreview').style.background =
            `linear-gradient(135deg, ${bb.gradient_from}, ${bb.gradient_to})`;

        modal.classList.remove('hidden');
    }

    @if($errors->any())
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('addBillboardModal').classList.remove('hidden');
        });
    @endif
</script>
@endsection
