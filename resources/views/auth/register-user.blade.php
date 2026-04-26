<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar User - Centrivo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { 'color1': '#628ECB', 'color2': '#8AAEE0', 'color3': '#B1C9EF', 'color4': '#D5DEEF' }, fontFamily: { sans: ['Inter', 'sans-serif'] } } } }
    </script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style> .bg-blob { filter: blur(80px); opacity: 0.4; z-index: -1; } #map { height: 250px; width: 100%; border-radius: 1rem; } </style>
</head>
<body class="bg-color4 min-h-screen flex items-center justify-center p-6 relative">
    <div class="absolute top-0 right-0 w-[400px] h-[400px] bg-color1 bg-blob rounded-full -mr-20 -mt-20"></div>
    <div class="absolute bottom-0 left-0 w-[300px] h-[300px] bg-color2 bg-blob rounded-full -ml-10 -mb-10"></div>

    <div class="w-full max-w-xl relative z-10">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-black tracking-tighter text-color1">Centrivo</h1>
            <p class="text-gray-500 mt-2 font-medium">Daftar sebagai User untuk cari jasa</p>
        </div>

        <div class="bg-white p-10 rounded-[40px] shadow-2xl shadow-color1/10 border border-white/50">
            <form action="{{ route('register.user.post') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" id="lat" name="latitude" value="-8.1724">
                <input type="hidden" id="lng" name="longitude" value="113.7005">
                
                <input type="text" name="name" placeholder="Nama Lengkap" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-color1/20 outline-none" required>
                <input type="email" name="email" placeholder="Email" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-color1/20 outline-none" required>
                <input type="password" name="password" placeholder="Kata Sandi (Min. 8 Karakter)" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-color1/20 outline-none" required>
                <input type="text" name="phone" placeholder="No. WhatsApp" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-color1/20 outline-none">
                <input type="text" name="address" placeholder="Alamat Lengkap" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-color1/20 outline-none" required>
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 ml-1">Titik Lokasi</label>
                    <div id="map"></div>
                </div>

                <button type="submit" class="w-full bg-color1 text-white py-4 rounded-2xl font-bold text-lg hover:shadow-xl transition-all">Daftar Sekarang</button>
            </form>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const map = L.map('map').setView([-8.1724, 113.7005], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        let marker = L.marker([-8.1724, 113.7005], {draggable: true}).addTo(map);
        marker.on('dragend', (e) => {
            document.getElementById('lat').value = e.target.getLatLng().lat;
            document.getElementById('lng').value = e.target.getLatLng().lng;
        });
    </script>
</body>
</html>