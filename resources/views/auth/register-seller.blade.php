<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Mitra - Centrivo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { 'color1': '#628ECB', 'color2': '#8AAEE0', 'color3': '#B1C9EF', 'color4': '#D5DEEF' }, fontFamily: { sans: ['Inter', 'sans-serif'] } } } }
    </script>
    <style> .bg-blob { filter: blur(80px); opacity: 0.4; z-index: -1; } </style>
</head>
<body class="bg-color4 min-h-screen flex items-center justify-center p-6 relative">
    <div class="absolute top-0 right-0 w-[400px] h-[400px] bg-color1 bg-blob rounded-full -mr-20 -mt-20"></div>

    <div class="w-full max-w-md relative z-10">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-black tracking-tighter text-color1">Centrivo</h1>
            <p class="text-gray-500 mt-2 font-medium">Daftar sebagai Mitra Usaha</p>
        </div>

        <div class="bg-white p-10 rounded-[40px] shadow-2xl border border-white/50">
            <form action="{{ route('register.seller.post') }}" method="POST" class="space-y-5">
                @csrf
                <input type="text" name="business_name" value="{{ old('business_name') }}" placeholder="Nama Brand / Usaha" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-color1/20 outline-none" required>
                @error('business_name')<span class="text-red-500 text-sm ml-2">{{ $message }}</span>@enderror
                
                <input type="email" name="email" value="{{ old('email') }}" placeholder="Email Bisnis" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-color1/20 outline-none" required>
                @error('email')<span class="text-red-500 text-sm ml-2">{{ $message }}</span>@enderror
                
                <input type="password" name="password" placeholder="Kata Sandi (Min. 8 Karakter)" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-color1/20 outline-none" required>
                @error('password')<span class="text-red-500 text-sm ml-2">{{ $message }}</span>@enderror
                
                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="No. WhatsApp Bisnis" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-color1/20 outline-none">
                @error('phone')<span class="text-red-500 text-sm ml-2">{{ $message }}</span>@enderror

                <button type="submit" class="w-full bg-color1 text-white py-4 rounded-2xl font-bold text-lg hover:shadow-xl transition-all">Daftar Mitra</button>
            </form>
        </div>
    </div>
</body>
</html>