<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan - Centrivo</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
        #map { height: 400px; width: 100%; border-radius: 24px; }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-800">

    <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100">
        <div class="max-w-4xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('user.profile') }}" class="p-2 hover:bg-gray-100 rounded-xl transition-colors">
                    <span class="text-xl">←</span>
                </a>
                <h1 class="text-2xl font-black text-slate-800 tracking-tighter">Pengaturan</h1>
            </div>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto px-6 py-10">
        
        <!-- Location Settings -->
        <div class="bg-white rounded-[32px] p-8 shadow-sm border border-gray-100">
            <h2 class="text-xl font-black mb-2 flex items-center gap-2">
                <span class="w-1.5 h-6 bg-color1 rounded-full"></span> Pengaturan Lokasi
            </h2>
            <p class="text-sm text-slate-400 mb-6 font-medium">Atur lokasi Anda agar penyedia jasa dapat menemukan alamat Anda dengan akurat.</p>
            
            <div id="map" class="mb-6 shadow-inner border border-gray-100"></div>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest px-1">Latitude</label>
                    <input type="text" id="lat" readonly value="{{ $profile->latitude }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-2xl outline-none font-bold text-slate-500 text-sm">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest px-1">Longitude</label>
                    <input type="text" id="lng" readonly value="{{ $profile->longitude }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-2xl outline-none font-bold text-slate-500 text-sm">
                </div>
                <div class="md:col-span-2 space-y-2">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest px-1">Alamat Lengkap (Confirm Location)</label>
                    <textarea id="address" rows="2" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-2xl outline-none focus:ring-2 focus:ring-color1/20 focus:border-color1 transition-all font-bold text-slate-700 text-sm resize-none">{{ $profile->address }}</textarea>
                </div>
            </div>

            <div class="mt-8">
                <button onclick="saveLocation()" id="saveBtn" class="w-full md:w-auto px-10 py-4 bg-color1 hover:bg-color2 text-white font-bold rounded-2xl shadow-lg shadow-color1/20 transition-all transform hover:-translate-y-1">
                    Update Lokasi Saya
                </button>
            </div>
        </div>

        <div class="mt-8 bg-white rounded-[32px] p-8 shadow-sm border border-gray-100">
            <h2 class="text-xl font-black mb-6 flex items-center gap-2 text-red-500">
                <span class="w-1.5 h-6 bg-red-500 rounded-full"></span> Zona Berbahaya
            </h2>
            <div class="p-6 bg-red-50 rounded-2xl border border-red-100 flex items-center justify-between">
                <div>
                    <h4 class="font-bold text-red-600">Hapus Akun</h4>
                    <p class="text-xs text-red-400 font-medium">Semua data transaksi dan profil akan dihapus permanen.</p>
                </div>
                <button class="px-6 py-2 bg-red-500 text-white font-bold rounded-xl text-sm hover:bg-red-600 transition-colors">Hapus</button>
            </div>
        </div>

    </main>

    <script>
        // Default to Jakarta if no coords
        var lat = {{ $profile->latitude ?? -6.2088 }};
        var lng = {{ $profile->longitude ?? 106.8456 }};
        
        var map = L.map('map').setView([lat, lng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        var marker = L.marker([lat, lng], {draggable: true}).addTo(map);

        marker.on('dragend', function (e) {
            var position = marker.getLatLng();
            updateCoords(position.lat, position.lng);
        });

        map.on('click', function (e) {
            marker.setLatLng(e.latlng);
            updateCoords(e.latlng.lat, e.latlng.lng);
        });

        function updateCoords(lat, lng) {
            document.getElementById('lat').value = lat.toFixed(8);
            document.getElementById('lng').value = lng.toFixed(8);
        }

        async function saveLocation() {
            const btn = document.getElementById('saveBtn');
            const originalText = btn.innerText;
            btn.disabled = true;
            btn.innerText = 'Menyimpan...';

            const data = {
                latitude: document.getElementById('lat').value,
                longitude: document.getElementById('lng').value,
                address: document.getElementById('address').value
            };

            try {
                const response = await fetch('{{ route("user.profile.location.update") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                });

                const res = await response.json();
                if (res.success) {
                    alert('Lokasi berhasil diperbarui!');
                }
            } catch (error) {
                console.error(error);
                alert('Gagal memperbarui lokasi.');
            } finally {
                btn.disabled = false;
                btn.innerText = originalText;
            }
        }
    </script>
</body>
</html>
