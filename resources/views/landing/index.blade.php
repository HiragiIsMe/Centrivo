<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centrivo - Solusi Jasa Terdekat</title>
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
        html { scroll-behavior: smooth; }
        .text-gradient {
            background: linear-gradient(135deg, #628ECB, #B1C9EF);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .bg-blob {
            filter: blur(80px);
            opacity: 0.3;
            z-index: -1;
        }
    </style>
</head>
<body class="bg-color4 text-gray-900 font-sans">

    <header class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100">
        <nav class="container mx-auto px-6 py-5 flex items-center justify-between">
            <div class="flex items-center">
                <span class="text-3xl font-black tracking-tighter text-color1">Centrivo</span>
            </div>
            <div class="hidden md:flex items-center gap-10 text-sm font-bold text-gray-500">
                <a href="#solusi" class="hover:text-color1 transition">Solusi</a>
                <a href="#keunggulan" class="hover:text-color1 transition">Keunggulan</a>
                <a href="#testimoni" class="hover:text-color1 transition">Kata Mereka</a>
                <a href="#mitra" class="hover:text-color1 transition">Jadi Mitra</a>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('login') }}" class="text-sm font-bold text-gray-400 hover:text-color1">Masuk</a>
                <a href="{{ route('register.user') }}" class="bg-color1 text-white px-7 py-3 rounded-full text-sm font-bold hover:shadow-lg hover:shadow-color1/30 transition-all transform hover:-translate-y-0.5">Coba Gratis</a>
            </div>
        </nav>
    </header>

    <section class="relative min-h-[85vh] flex items-center overflow-hidden bg-white">
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-color2 bg-blob rounded-full -mr-24 -mt-24 hidden md:block"></div>
        <div class="absolute bottom-0 left-0 w-[300px] h-[300px] bg-color3 bg-blob rounded-full -ml-12 -mb-12 hidden md:block"></div>

        <div class="container mx-auto px-6 py-20 flex flex-col items-center text-center relative z-10">
            <div class="inline-flex items-center bg-color1/5 border border-color1/10 px-4 py-2 rounded-full mb-8">
                <span class="flex h-2 w-2 rounded-full bg-color1 mr-2 animate-pulse"></span>
                <span class="text-xs font-bold uppercase tracking-widest text-color1">Solusi Jasa Terpercaya</span>
            </div>

            <h1 class="text-5xl md:text-8xl font-black leading-[1.05] mb-10 max-w-5xl tracking-tight">
                Urusan Beres, <br><span class="text-gradient">Hati pun Tenang.</span>
            </h1>

            <p class="text-lg md:text-2xl text-gray-500 mb-12 max-w-2xl leading-relaxed">
                Temukan ahli profesional di sekitar Anda dalam hitungan detik. Dari servis rumah hingga kebutuhan kreatif, Centrivo siap membantu.
            </p>
            
            <div class="w-full max-w-2xl bg-white p-2 rounded-3xl md:rounded-full shadow-2xl flex flex-col md:flex-row items-center gap-2 border border-gray-100">
                <div class="flex-grow flex items-center px-6 w-full">
                    <svg class="w-6 h-6 text-gray-400 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" placeholder="Mau cari jasa apa hari ini?" class="w-full py-4 text-gray-700 bg-transparent focus:outline-none text-lg">
                </div>
                <button class="w-full md:w-auto bg-color1 text-white px-10 py-4 rounded-2xl md:rounded-full text-lg font-bold hover:bg-opacity-90 transition shadow-lg shadow-color1/20">
                    Cari Sekarang
                </button>
            </div>
        </div>
    </section>

    <section id="solusi" class="py-40 bg-white border-t border-gray-50">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-2 gap-20 items-center">
                
                <div class="relative flex justify-center items-center">
                    <div class="w-[300px] h-[300px] bg-gray-100 rounded-[50px] shadow-sm flex items-end justify-center overflow-hidden border border-gray-100">
                        <span class="text-9xl mb-[-10px]">👩</span> </div>

                    <div class="absolute -bottom-10 md:-right-10 bg-white p-7 rounded-[30px] shadow-2xl border border-gray-100 max-w-[320px] relative">
                        <p class="text-gray-800 font-bold text-lg leading-snug">"Oalah, gampang jebule! Pesan jasa service AC jam 10 pagi, jam 11 sudah adem maneh!"</p>
                        <p class="text-sm text-gray-400 mt-3 font-semibold">— Ibu Maya, Jember</p>
                        
                        <div class="absolute bottom-[-15px] left-10 w-0 h-0 border-l-[15px] border-l-transparent border-t-[20px] border-t-white border-r-[15px] border-r-transparent"></div>
                    </div>
                </div>

                <div>
                    <h2 class="text-4xl md:text-6xl font-black mb-8 leading-tight">Gak Perlu Bingung <br><span class="text-color1">Cari Bantuan.</span></h2>
                    <p class="text-gray-500 text-xl leading-relaxed mb-10">
                        Seringkali kita butuh bantuan cepat tapi bingung hubungi siapa. Centrivo hadir menghubungkan Anda dengan tetangga berbakat yang lokasinya cuma beberapa menit dari Anda.
                    </p>
                    <div class="space-y-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 text-green-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <p class="text-lg font-bold text-gray-700">Respon kilat dari mitra terdekat</p>
                        </div>

                        <div class="flex gap-4 items-center">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 text-green-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <p class="text-lg font-bold text-gray-700">Hemat biaya transportasi & waktu</p>
                        </div>

                        <div class="flex gap-4 items-center">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 text-green-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <p class="text-lg font-bold text-gray-700">Kualitas terjamin dengan mitra terverifikasi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="keunggulan" class="py-40 bg-color4">
        <div class="container mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-20">
                <h2 class="text-4xl md:text-6xl font-black mb-6">Kenapa Centrivo?</h2>
                <p class="text-gray-500 text-xl">Kami menghadirkan standar baru dalam mencari bantuan jasa yang aman dan praktis.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white p-12 rounded-[45px] shadow-sm hover:shadow-2xl transition-all duration-500 group border border-gray-50">
                    <div class="w-20 h-20 bg-color3/20 rounded-3xl flex items-center justify-center mb-10 text-4xl group-hover:scale-110 transition duration-500">📍</div>
                    <h3 class="text-2xl font-black mb-4">Paling Dekat</h3>
                    <p class="text-gray-500 leading-relaxed text-lg">Secara otomatis menyaring penyedia jasa yang lokasinya paling dekat dengan posisi Anda.</p>
                </div>
                <div class="bg-white p-12 rounded-[45px] shadow-sm hover:shadow-2xl transition-all duration-500 group border border-gray-50">
                    <div class="w-20 h-20 bg-color2/20 rounded-3xl flex items-center justify-center mb-10 text-4xl group-hover:scale-110 transition duration-500">⚡</div>
                    <h3 class="text-2xl font-black mb-4">Sangat Akurat</h3>
                    <p class="text-gray-500 leading-relaxed text-lg">Sistem cerdas kami memastikan keahlian mitra sesuai dengan apa yang Anda cari.</p>
                </div>
                <div class="bg-white p-12 rounded-[45px] shadow-sm hover:shadow-2xl transition-all duration-500 group border border-gray-50">
                    <div class="w-20 h-20 bg-color1/10 rounded-3xl flex items-center justify-center mb-10 text-4xl group-hover:scale-110 transition duration-500">🛡️</div>
                    <h3 class="text-2xl font-black mb-4">Pasti Aman</h3>
                    <p class="text-gray-500 leading-relaxed text-lg">Seluruh mitra telah diverifikasi dan dipantau melalui sistem ulasan yang transparan.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="testimoni" class="py-40 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-20">
                <h2 class="text-4xl md:text-6xl font-black mb-6">Kata Mereka</h2>
                <p class="text-gray-500 text-xl italic">Kisah nyata dari mereka yang sudah mencoba kemudahan Centrivo.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-10">
                <div class="bg-color4/40 p-10 rounded-[40px] border border-gray-100 flex flex-col justify-between relative">
                    <p class="text-gray-600 text-lg leading-relaxed italic mb-10 relative z-10">"Gak nyangka dapet tukang service AC yang cuma berjarak 5 menit dari rumah. Sangat praktis dan profesional!"</p>
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-color2 rounded-2xl flex items-center justify-center text-white text-xl font-bold">A</div>
                        <div>
                            <h5 class="font-black text-lg">Andi Pratama</h5>
                            <p class="text-gray-400 text-sm">Wiraswasta</p>
                        </div>
                    </div>
                </div>
                <div class="bg-color1 text-white p-10 rounded-[40px] shadow-xl shadow-color1/20 flex flex-col justify-between transform md:scale-105 relative">
                    <p class="text-white/90 text-lg leading-relaxed italic mb-10 relative z-10">"Centrivo bantu promosi jasa desain saya ke tetangga sekitar. Sekarang gak perlu lagi sebar brosur di jalan!"</p>
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center text-white text-xl font-bold">S</div>
                        <div>
                            <h5 class="font-black text-lg">Siti Aisyah</h5>
                            <p class="text-white/60 text-sm">Mitra Desainer</p>
                        </div>
                    </div>
                </div>
                <div class="bg-color4/40 p-10 rounded-[40px] border border-gray-100 flex flex-col justify-between relative">
                    <p class="text-gray-600 text-lg leading-relaxed italic mb-10 relative z-10">"Aplikasi paling simpel yang pernah saya coba. Tinggal ketik apa yang dicari, langsung muncul ahlinya."</p>
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-color1 rounded-2xl flex items-center justify-center text-white text-xl font-bold">B</div>
                        <div>
                            <h5 class="font-black text-lg">Budi Santoso</h5>
                            <p class="text-gray-400 text-sm">Karyawan Swasta</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="mitra" class="py-40 bg-color4">
        <div class="container mx-auto px-6">
            <div class="bg-gradient-to-br from-color1 to-color2 rounded-[60px] p-12 md:p-32 text-white text-center shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-80 h-80 bg-white/10 rounded-full blur-3xl -mr-40 -mt-40"></div>
                <div class="relative z-10">
                    <h2 class="text-5xl md:text-8xl font-black mb-8 tracking-tight leading-tight">Punya Keahlian? <br><span class="opacity-80">Jadikan Penghasilan.</span></h2>
                    <p class="text-xl md:text-2xl text-white/80 max-w-2xl mx-auto mb-16 leading-relaxed">
                        Jangan biarkan bakat Anda tersembunyi. Bergabunglah dengan ribuan mitra di Jember dan mulai layani pelanggan di sekitar Anda hari ini.
                    </p>
                    <div class="flex flex-col md:flex-row items-center justify-center gap-8">
                        <a href="{{ route('register.seller') }}" class="bg-white text-color1 px-14 py-5 rounded-full font-black text-xl hover:scale-105 transition-all shadow-xl">Daftar Jadi Mitra</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-white pt-32 pb-12 border-t border-gray-100">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-4 gap-20 mb-24">
                <div class="col-span-2">
                    <span class="text-4xl font-black text-color1 tracking-tighter">Centrivo</span>
                    <p class="mt-8 text-gray-500 text-lg leading-relaxed max-w-sm">Menghubungkan keahlian lokal dengan kebutuhan Anda secara instan, aman, dan mudah. Kebanggaan masyarakat Jember.</p>
                </div>
                <div>
                    <h5 class="font-black text-lg mb-8">Eksplorasi</h5>
                    <ul class="space-y-4 text-gray-500 font-medium">
                        <li><a href="#solusi" class="hover:text-color1 transition">Solusi</a></li>
                        <li><a href="#keunggulan" class="hover:text-color1 transition">Keunggulan</a></li>
                        <li><a href="#testimoni" class="hover:text-color1 transition">Testimoni</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="font-black text-lg mb-8">Dukungan</h5>
                    <ul class="space-y-4 text-gray-500 font-medium">
                        <li class="flex items-center gap-2"><span>📧</span> halo@centrivo.id</li>
                        <li class="flex items-center gap-2"><span>📍</span> Jember, Jawa Timur</li>
                        <li><a href="#" class="hover:text-color1 transition">Kebijakan Privasi</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-50 pt-12 flex flex-col md:flex-row justify-between items-center text-gray-400 text-sm">
                <p>&copy; 2026 Centrivo Jember. Semua hak dilindungi.</p>
                <div class="flex gap-8 mt-6 md:mt-0 font-semibold">
                    <a href="#" class="hover:text-color1 transition">Instagram</a>
                    <a href="#" class="hover:text-color1 transition">Twitter</a>
                    <a href="#" class="hover:text-color1 transition">LinkedIn</a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>