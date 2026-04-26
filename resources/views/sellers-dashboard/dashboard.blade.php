@extends('sellers-dashboard.main')

@section('sellers_content')
<div class="mb-8">
    <h1 class="text-3xl font-black text-slate-800 tracking-tighter">Seller Dashboard</h1>
    <p class="text-slate-400 mt-1 font-medium">Pantau performa bisnis dan pendapatan Anda di Centrivo.</p>
</div>

<!-- Stats Overview -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col justify-between group hover:border-color1/30 transition-all">
        <div class="w-12 h-12 bg-blue-50 text-color1 rounded-2xl flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition-transform">💰</div>
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Pendapatan</p>
            <h2 class="text-2xl font-black text-slate-800">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h2>
        </div>
    </div>
    
    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col justify-between group hover:border-color1/30 transition-all">
        <div class="w-12 h-12 bg-purple-50 text-purple-500 rounded-2xl flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition-transform">✅</div>
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Transaksi Selesai</p>
            <h2 class="text-2xl font-black text-slate-800">{{ $completedTransactions }}</h2>
        </div>
    </div>

    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col justify-between group hover:border-color1/30 transition-all">
        <div class="w-12 h-12 bg-green-50 text-green-500 rounded-2xl flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition-transform">⭐</div>
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Rating Rata-rata</p>
            <h2 class="text-2xl font-black text-slate-800">{{ number_format($averageRating, 1) }}</h2>
        </div>
    </div>

    <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-3xl p-6 text-white shadow-xl flex flex-col justify-between group hover:scale-[1.02] transition-all">
        <div class="w-12 h-12 bg-white/10 text-white rounded-2xl flex items-center justify-center text-xl mb-4">💳</div>
        <div>
            <p class="text-[10px] font-bold text-white/50 uppercase tracking-widest mb-1">Saldo Dompet</p>
            <h2 class="text-2xl font-black text-white">Rp {{ number_format($balance, 0, ',', '.') }}</h2>
            <p class="text-[10px] text-white/30 mt-1">Siap ditarik kapan saja</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Revenue Chart -->
    <div class="lg:col-span-2 bg-white rounded-[40px] p-8 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-lg font-black text-slate-800 tracking-tight">Tren Pendapatan</h3>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">6 Bulan Terakhir</span>
        </div>
        <div class="h-64">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- Status Distribution -->
    <div class="bg-white rounded-[40px] p-8 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-lg font-black text-slate-800 tracking-tight">Status Transaksi</h3>
        </div>
        <div class="h-64 flex items-center justify-center">
            <canvas id="statusChart"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Revenue Line Chart
    const revCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($months) !!},
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: {!! json_encode($monthlyRevenue) !!},
                borderColor: '#628ECB',
                backgroundColor: 'rgba(98, 142, 203, 0.1)',
                borderWidth: 4,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#628ECB',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.03)' },
                    ticks: {
                        font: { weight: 'bold', size: 10 },
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString();
                        }
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { weight: 'bold', size: 10 } }
                }
            }
        }
    });

    // Status Doughnut Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($statuses) !!},
            datasets: [{
                data: {!! json_encode($statusData) !!},
                backgroundColor: [
                    '#FCD34D', // pending (yellow)
                    '#60A5FA', // accepted (blue)
                    '#A78BFA', // ongoing (purple)
                    '#34D399', // completed (green)
                    '#F87171'  // cancelled (red)
                ],
                borderWidth: 0,
                cutout: '70%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: { weight: 'bold', size: 10 }
                    }
                }
            }
        }
    });
</script>
@endsection