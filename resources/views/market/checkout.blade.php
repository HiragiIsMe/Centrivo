<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Layanan - Centrivo</title>
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
</head>
<body class="bg-slate-50 font-sans text-slate-800">

    <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('negotiation.show', $message->request_id) }}" class="text-xl font-black text-slate-400 hover:text-color1 flex items-center gap-2 transition-colors">
                <span>←</span> Kembali ke Negosiasi
            </a>
            <h1 class="text-xl font-black text-slate-800 tracking-tighter">Checkout Pembayaran</h1>
            <div class="w-24"></div> <!-- Spacer for center alignment -->
        </div>
    </nav>

    <main class="max-w-5xl mx-auto px-6 py-10">
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-600 rounded-2xl font-bold text-sm">
                {{ session('error') }}
            </div>
        @endif
        <form action="{{ route('checkout.process', $message->id) }}" method="POST" class="grid lg:grid-cols-3 gap-8">
            @csrf
            
            <!-- Left Column: Order Details -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Service Summary -->
                <div class="bg-white p-8 rounded-[32px] border border-gray-100 shadow-sm">
                    <h2 class="text-lg font-black mb-6 flex items-center gap-2">
                        <span class="w-1.5 h-6 bg-color1 rounded-full"></span> Ringkasan Layanan
                    </h2>
                    
                    <div class="flex gap-4 items-start">
                        <div class="w-24 h-24 bg-slate-100 rounded-2xl flex-shrink-0 overflow-hidden shadow-sm">
                            @if($message->serviceRequest->service->images->count() > 0)
                                <img src="{{ asset('storage/' . $message->serviceRequest->service->images->first()->image_path) }}" class="w-full h-full object-cover" alt="Service Image">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-xs text-slate-400 font-bold">No Image</div>
                            @endif
                        </div>
                        <div class="flex-grow">
                            <h3 class="font-bold text-slate-800 text-lg leading-tight mb-2">{{ $message->serviceRequest->service->service_name }}</h3>
                            <p class="text-sm font-medium text-slate-500 mb-2">Penyedia: <span class="font-bold text-color1">{{ $message->serviceRequest->seller->sellerProfile->brand_name ?? 'Mitra Centrivo' }}</span></p>
                            
                            <div class="bg-blue-50 border border-blue-100 rounded-xl p-3 inline-block">
                                <p class="text-xs font-bold text-blue-600 uppercase tracking-widest mb-1">Jadwal Disepakati</p>
                                <p class="text-sm font-black text-blue-800">🗓️ {{ \Carbon\Carbon::parse($message->scheduled_date)->format('l, d M Y - H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Service Type & Location -->
                <div class="bg-white p-8 rounded-[32px] border border-gray-100 shadow-sm">
                    <h2 class="text-lg font-black mb-6 flex items-center gap-2">
                        <span class="w-1.5 h-6 bg-color1 rounded-full"></span> Tipe Layanan & Lokasi
                    </h2>
                    
                    <div class="grid md:grid-cols-2 gap-4 mb-8">
                        <label class="relative flex flex-col p-5 border-2 border-color1 bg-color4/10 rounded-2xl cursor-pointer group transition-all">
                            <input type="radio" name="service_type" value="home_service" checked class="absolute top-4 right-4 w-5 h-5 accent-color1">
                            <span class="text-2xl mb-2">🏠</span>
                            <span class="font-bold text-slate-800 text-sm">Home Service</span>
                            <span class="text-[10px] text-slate-400 font-medium leading-tight mt-1">Penyedia jasa akan datang ke alamat Anda.</span>
                        </label>

                        <label class="relative flex flex-col p-5 border border-gray-100 hover:border-color1 rounded-2xl cursor-pointer group transition-all">
                            <input type="radio" name="service_type" value="on_site" class="absolute top-4 right-4 w-5 h-5 accent-color1">
                            <span class="text-2xl mb-2">🏢</span>
                            <span class="font-bold text-slate-800 text-sm">Datang ke Lokasi</span>
                            <span class="text-[10px] text-slate-400 font-medium leading-tight mt-1">Anda datang ke tempat penyedia jasa.</span>
                        </label>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Alamat Konfirmasi</h4>
                            <a href="{{ route('user.settings') }}" class="text-[10px] font-black text-color1 hover:underline">Ubah di Pengaturan</a>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center flex-shrink-0 text-lg">📍</div>
                            <div>
                                <p class="text-sm font-bold text-slate-700 leading-relaxed">{{ Auth::user()->userProfile->address ?? 'Alamat belum diatur' }}</p>
                                <p class="text-[10px] text-slate-400 font-medium mt-1">Latitude: {{ Auth::user()->userProfile->latitude ?? '-' }}, Longitude: {{ Auth::user()->userProfile->longitude ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="bg-white p-8 rounded-[32px] border border-gray-100 shadow-sm">
                    <h2 class="text-lg font-black mb-6 flex items-center gap-2">
                        <span class="w-1.5 h-6 bg-color1 rounded-full"></span> Metode Pembayaran
                    </h2>
                    
                    <div class="space-y-4">
                        <!-- Option 1 -->
                        <label class="flex items-center gap-4 p-4 border-2 border-color1 bg-color4/10 rounded-2xl cursor-pointer transition-all">
                            <input type="radio" name="payment_method" checked class="w-5 h-5 accent-color1">
                            <div class="flex-grow flex items-center justify-between">
                                <div>
                                    <h4 class="font-bold text-slate-800 text-sm">Virtual Account / Transfer Bank</h4>
                                    <p class="text-xs text-slate-400 mt-1">BCA, Mandiri, BNI, BRI, dll.</p>
                                </div>
                                <div class="text-2xl">🏦</div>
                            </div>
                        </label>
                        
                        <!-- Option 2 -->
                        <label class="flex items-center gap-4 p-4 border border-gray-100 hover:border-gray-300 rounded-2xl cursor-pointer transition-all grayscale opacity-50">
                            <input type="radio" name="payment_method" disabled class="w-5 h-5">
                            <div class="flex-grow flex items-center justify-between">
                                <div>
                                    <h4 class="font-bold text-slate-800 text-sm">E-Wallet (Segera Hadir)</h4>
                                    <p class="text-xs text-slate-400 mt-1">GoPay, OVO, Dana, ShopeePay</p>
                                </div>
                                <div class="text-2xl">📱</div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Right Column: Payment Summary -->
            <div class="lg:col-start-3">
                <div class="bg-white p-8 rounded-[32px] border border-gray-100 shadow-2xl shadow-color1/10 sticky top-24">
                    <h2 class="text-lg font-black mb-6">Detail Tagihan</h2>
                    
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500 font-medium">Harga Layanan (Nego)</span>
                            <span class="font-bold text-slate-700">Rp {{ number_format($message->offered_price, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500 font-medium">Biaya Layanan Aplikasi</span>
                            <span class="font-bold text-slate-700">Rp 2.500</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500 font-medium">Pajak (PPN 11%)</span>
                            @php $tax = $message->offered_price * 0.11; @endphp
                            <span class="font-bold text-slate-700">Rp {{ number_format($tax, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    
                    <div class="border-t border-dashed border-gray-200 pt-4 mb-8">
                        <div class="flex justify-between items-end">
                            <span class="text-sm font-bold text-slate-800 uppercase tracking-widest">Total Bayar</span>
                            @php $total = $message->offered_price + 2500 + $tax; @endphp
                            <span class="text-2xl font-black text-color1">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-color1 hover:bg-color2 text-white py-4 rounded-2xl font-bold text-lg hover:shadow-xl hover:shadow-color1/30 transition-all transform hover:-translate-y-1">
                        Bayar Sekarang
                    </button>
                    <p class="text-center text-xs text-slate-400 mt-4">
                        Transaksi di Centrivo aman. Dana hanya diteruskan ke Mitra jika pekerjaan telah selesai.
                    </p>
                </div>
            </div>

        </form>
    </main>

</body>
</html>