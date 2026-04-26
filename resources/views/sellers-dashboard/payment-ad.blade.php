<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Iklan - Centrivo</title>
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
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
</head>
<body class="bg-slate-50 font-sans text-slate-800 min-h-screen flex items-center justify-center p-6">

    <div class="bg-white rounded-[32px] p-10 border border-gray-100 shadow-xl max-w-md w-full text-center">
        <div class="w-20 h-20 bg-orange-100 text-orange-500 rounded-3xl mx-auto flex items-center justify-center text-4xl mb-6">📣</div>

        <h1 class="text-2xl font-black text-slate-800 mb-2">Pembayaran Iklan</h1>
        <p class="text-slate-400 font-medium text-sm mb-6">{{ $adTx->advertisement->service->service_name ?? 'Jasa' }}</p>

        <div class="bg-gray-50 rounded-2xl p-6 mb-8 text-left space-y-3">
            <div class="flex justify-between text-sm">
                <span class="text-slate-500 font-medium">Paket</span>
                <span class="font-bold text-slate-800">{{ $adTx->adPackage->name ?? $adTx->duration_days . ' Hari' }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-slate-500 font-medium">Durasi</span>
                <span class="font-bold text-slate-800">{{ $adTx->duration_days }} Hari</span>
            </div>
            <div class="border-t border-gray-200 pt-3 flex justify-between">
                <span class="text-slate-500 font-bold">Total</span>
                <span class="text-xl font-black text-orange-500">Rp {{ number_format($adTx->amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <button id="pay-button" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-black py-4 rounded-2xl transition-all shadow-lg shadow-orange-500/20 text-lg">
            Bayar Sekarang
        </button>

        <a href="{{ route('seller.advertisements') }}" class="inline-block mt-4 text-sm font-bold text-slate-400 hover:text-color1 transition-colors">← Kembali ke Iklan</a>
    </div>

    <script type="text/javascript">
        document.getElementById('pay-button').onclick = function(){
            snap.pay('{{ $adTx->snap_token }}', {
                onSuccess: function(result){
                    window.location.href = "{{ route('seller.advertisements') }}?status=success";
                },
                onPending: function(result){
                    window.location.href = "{{ route('seller.advertisements') }}?status=pending";
                },
                onError: function(result){
                    window.location.href = "{{ route('seller.advertisements') }}?status=error";
                },
                onClose: function(){
                    alert('Anda menutup popup sebelum menyelesaikan pembayaran.');
                }
            });
        };
    </script>
</body>
</html>
