<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - Centrivo</title>
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
    <!-- Midtrans Snap.js -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
</head>
<body class="bg-slate-50 font-sans text-slate-800">

    <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100">
        <div class="max-w-3xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('user.transactions') }}" class="text-xl font-black text-slate-400 hover:text-color1 flex items-center gap-2 transition-colors">
                <span>←</span> Kembali ke Transaksi
            </a>
            <h1 class="text-xl font-black text-slate-800 tracking-tighter">Selesaikan Pembayaran</h1>
            <div class="w-24"></div>
        </div>
    </nav>

    <main class="max-w-3xl mx-auto px-6 py-10">
        <div class="bg-white rounded-[32px] p-8 border border-gray-100 shadow-xl shadow-color1/5 text-center">
            
            <div class="w-24 h-24 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center text-4xl mx-auto mb-6">
                💳
            </div>

            <h2 class="text-2xl font-black text-slate-800 mb-2">Menunggu Pembayaran</h2>
            <p class="text-slate-500 font-medium mb-8">Selesaikan pembayaran untuk pesanan <span class="font-bold text-color1">{{ $transaction->serviceRequest->service->service_name }}</span></p>

            <div class="bg-gray-50 rounded-2xl p-6 text-left mb-8 max-w-md mx-auto border border-gray-100">
                <div class="flex justify-between items-center py-2 border-b border-gray-200 border-dashed">
                    <span class="text-sm font-medium text-slate-500">ID Transaksi</span>
                    <span class="text-sm font-bold text-slate-800">#{{ $transaction->id }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-200 border-dashed">
                    <span class="text-sm font-medium text-slate-500">Penyedia Jasa</span>
                    <span class="text-sm font-bold text-slate-800">{{ $transaction->serviceRequest->seller->sellerProfile->brand_name ?? 'Mitra Centrivo' }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-200 border-dashed">
                    <span class="text-sm font-medium text-slate-500">Tipe Layanan</span>
                    <span class="text-sm font-bold text-slate-800">{{ $transaction->serviceRequest->service_type == 'home_service' ? 'Home Service' : 'Datang ke Lokasi' }}</span>
                </div>
                <div class="flex justify-between items-center py-4 mt-2">
                    <span class="text-sm font-black text-slate-800 uppercase tracking-widest">Total Tagihan</span>
                    <span class="text-2xl font-black text-color1">Rp {{ number_format($transaction->final_price, 0, ',', '.') }}</span>
                </div>
            </div>

            <button id="pay-button" class="bg-color1 hover:bg-color2 text-white font-bold py-4 px-12 rounded-2xl text-lg hover:shadow-xl hover:shadow-color1/30 transition-all transform hover:-translate-y-1 inline-flex items-center gap-2">
                Bayar Sekarang ➔
            </button>
            <p class="text-xs text-slate-400 mt-4 font-medium">Pembayaran aman dengan Midtrans.</p>

        </div>
    </main>

    <script type="text/javascript">
        document.getElementById('pay-button').onclick = function(){
            snap.pay('{{ $transaction->snap_token }}', {
                onSuccess: function(result){
                    window.location.href = "{{ route('user.transactions') }}?status=success";
                },
                onPending: function(result){
                    window.location.href = "{{ route('user.transactions') }}?status=pending";
                },
                onError: function(result){
                    window.location.href = "{{ route('user.transactions') }}?status=error";
                },
                onClose: function(){
                    alert('Anda menutup popup sebelum menyelesaikan pembayaran.');
                }
            });
        };
    </script>
</body>
</html>
