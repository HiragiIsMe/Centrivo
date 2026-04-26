<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Email Anda - Centrivo</title>

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

    <style>
        .bg-blob { filter: blur(80px); opacity: 0.4; z-index: -1; }
    </style>
</head>

<body class="bg-color4 min-h-screen flex items-center justify-center p-6 relative">

    <!-- Background -->
    <div class="absolute top-0 right-0 w-[400px] h-[400px] bg-color1 bg-blob rounded-full -mr-20 -mt-20"></div>
    <div class="absolute bottom-0 left-0 w-[300px] h-[300px] bg-color2 bg-blob rounded-full -ml-10 -mb-10"></div>

    <div class="w-full max-w-md relative z-10 text-center">

        <!-- Logo -->
        <div class="mb-8">
            <h1 class="text-4xl font-black tracking-tighter text-color1">Centrivo</h1>
        </div>

        <div class="bg-white p-10 rounded-[40px] shadow-2xl shadow-color1/10 border border-white/50">

            <!-- Icon -->
            <div class="w-20 h-20 bg-color4/50 rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="text-4xl">📧</span>
            </div>

            <h2 class="text-2xl font-black text-gray-800 mb-3">Cek Email Anda</h2>

            <p class="text-gray-500 mb-3 font-medium">
                Kami telah mengirimkan link aktivasi ke:
            </p>

            <p class="text-color1 font-bold mb-6 break-all">
                {{ $email }}
            </p>

            <p class="text-gray-400 text-sm mb-6">
                Silakan cek inbox atau folder spam Anda.
            </p>

            <!-- SUCCESS -->
            @if(session('message'))
                <div class="bg-green-50 text-green-600 p-4 rounded-2xl mb-4 font-bold text-sm">
                    {{ session('message') }}
                </div>
            @endif

            <!-- ERROR -->
            @if(session('error'))
                <div class="bg-red-50 text-red-600 p-4 rounded-2xl mb-4 font-bold text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <!-- RESEND -->
            <form method="POST" action="{{ route('activation.resend', $email) }}" class="mb-6">
                @csrf
                <button type="submit"
                    class="w-full bg-color1 text-white py-4 rounded-2xl font-bold hover:shadow-lg hover:shadow-color1/30 transition-all">
                    Kirim Ulang Email
                </button>
            </form>

            <!-- BACK -->
            <a href="{{ route('login') }}"
               class="text-gray-400 font-bold hover:text-color1 transition-all">
                &larr; Kembali ke Login
            </a>

        </div>
    </div>

</body>
</html>