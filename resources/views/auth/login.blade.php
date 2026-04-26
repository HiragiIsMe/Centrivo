<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Centrivo</title>
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
        .bg-blob {
            filter: blur(80px);
            opacity: 0.4;
            z-index: -1;
        }
    </style>
</head>
<body class="bg-color4 min-h-screen flex items-center justify-center p-6 relative overflow-hidden">

    <div class="absolute top-0 right-0 w-[400px] h-[400px] bg-color1 bg-blob rounded-full -mr-20 -mt-20"></div>
    <div class="absolute bottom-0 left-0 w-[300px] h-[300px] bg-color2 bg-blob rounded-full -ml-10 -mb-10"></div>

    <div class="w-full max-w-md relative z-10">
        <div class="text-center mb-10">
            <a href="index.html" class="text-4xl font-black tracking-tighter text-color1">Centrivo</a>
            <p class="text-gray-500 mt-2 font-medium">Selamat datang kembali! Silakan masuk.</p>
        </div>

        <div class="bg-white p-10 rounded-[40px] shadow-2xl shadow-color1/10 border border-white/50">
            <form action="{{ route('login.post') }}" method="POST" class="space-y-6">
                @csrf 

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 ml-1">Alamat Email</label>
                    <input type="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        placeholder="nama@email.com" 
                        class="w-full px-6 py-4 bg-gray-50 border @error('email') border-red-500 @else border-gray-100 @enderror rounded-2xl focus:outline-none focus:ring-2 focus:ring-color1/20 focus:border-color1 transition-all" 
                        required>
                    
                    @error('email')
                        <p class="text-red-500 text-xs mt-2 ml-1 font-semibold italic">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="flex justify-between mb-2 ml-1">
                        <label class="text-sm font-bold text-gray-700">Kata Sandi</label>
                        <a href="#" class="text-xs font-bold text-color1 hover:underline">Lupa Password?</a>
                    </div>
                    <input type="password" 
                        name="password" 
                        placeholder="••••••••" 
                        class="w-full px-6 py-4 bg-gray-50 border @error('password') border-red-500 @else border-gray-100 @enderror rounded-2xl focus:outline-none focus:ring-2 focus:ring-color1/20 focus:border-color1 transition-all" 
                        required>
                    
                    @error('password')
                        <p class="text-red-500 text-xs mt-2 ml-1 font-semibold italic">{{ $message }}</p>
                    @enderror
                    
                    @if(session('error'))
                        <div class="bg-red-50 text-red-600 p-4 rounded-2xl text-sm font-bold border border-red-100 mb-4">
                            {{ session('error') }}
                        </div>
                    @endif
                </div>

                <div class="flex items-center ml-1">
                    <input type="checkbox" name="remember" id="remember" class="w-4 h-4 text-color1 border-gray-300 rounded focus:ring-color1">
                    <label for="remember" class="ml-2 text-sm text-gray-500 font-medium cursor-pointer">Ingat saya di perangkat ini</label>
                </div>

                <button type="submit" class="w-full bg-color1 text-white py-4 rounded-2xl font-bold text-lg hover:shadow-xl hover:shadow-color1/30 transition-all transform hover:-translate-y-1">
                    Masuk Sekarang
                </button>
            </form>

            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-100"></div>
                </div>
            </div>
        </div>

        <p class="text-center mt-8 text-gray-500 font-medium">
            Belum punya akun? <a href="{{ route('register.user') }}" class="text-color1 font-bold hover:underline">Daftar Gratis</a>
        </p>
    </div>

</body>
</html>