<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Aktif - Centrivo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { 'color1': '#628ECB', 'color2': '#8AAEE0', 'color3': '#B1C9EF', 'color4': '#D5DEEF' }, fontFamily: { sans: ['Inter', 'sans-serif'] } } } }
    </script>
    <style> .bg-blob { filter: blur(80px); opacity: 0.4; z-index: -1; } </style>
</head>
<body class="bg-color4 min-h-screen flex items-center justify-center p-6 relative">
    <div class="absolute top-0 right-0 w-[400px] h-[400px] bg-color1 bg-blob rounded-full -mr-20 -mt-20"></div>
    <div class="absolute bottom-0 left-0 w-[300px] h-[300px] bg-color2 bg-blob rounded-full -ml-10 -mb-10"></div>

    <div class="w-full max-w-md relative z-10 text-center">
        <div class="mb-8">
            <h1 class="text-4xl font-black tracking-tighter text-color1">Centrivo</h1>
        </div>

        <div class="bg-white p-10 rounded-[40px] shadow-2xl shadow-color1/10 border border-white/50">
            <div class="w-24 h-24 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="text-5xl">✨</span>
            </div>

            <h2 class="text-2xl font-black text-gray-800 mb-3">Akun Berhasil Aktif!</h2>
            <p class="text-gray-500 mb-8 font-medium leading-relaxed">
                Selamat datang! Akun kamu sudah siap digunakan. Sekarang kamu bisa masuk dan mulai menggunakan layanan kami.
            </p>

            <a href="{{ route('login') }}" class="block w-full bg-color1 text-white py-4 rounded-2xl font-bold text-lg hover:shadow-lg hover:shadow-color1/30 transition-all transform hover:-translate-y-1">
                Masuk ke Aplikasi
            </a>
        </div>
        
        <p class="mt-8 text-sm text-gray-500 font-medium">
            Ada kendala saat masuk? <a href="#" class="text-color1 font-bold hover:underline">Hubungi Pusat Bantuan</a>
        </p>
    </div>
</body>
</html>