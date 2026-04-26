@extends('dashboard.main')

@section('admin_content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tighter">Dashboard Overview</h1>
        <p class="text-slate-400 mt-1 font-medium">Platform performance and statistics.</p>
    </div>
    <a href="{{ route('admin.reports.export') }}" class="bg-color1 hover:bg-color2 text-white font-bold px-6 py-3 rounded-2xl flex items-center gap-2 transition-all shadow-lg shadow-color1/20">
        <span class="text-xl">📊</span> Platform Report (Excel)
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col justify-between group hover:border-color1/30 transition-all">
        <div class="w-12 h-12 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition-transform">👥</div>
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Users</p>
            <h2 class="text-2xl font-black text-slate-800">{{ $totalUsers }}</h2>
        </div>
    </div>
    
    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col justify-between group hover:border-color1/30 transition-all">
        <div class="w-12 h-12 bg-purple-50 text-purple-500 rounded-2xl flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition-transform">🏪</div>
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Sellers</p>
            <h2 class="text-2xl font-black text-slate-800">{{ $totalSellers }}</h2>
        </div>
    </div>

    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col justify-between group hover:border-color1/30 transition-all">
        <div class="w-12 h-12 bg-green-50 text-green-500 rounded-2xl flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition-transform">✓</div>
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Completed Jobs</p>
            <h2 class="text-2xl font-black text-slate-800">{{ $completedTransactions }}</h2>
        </div>
    </div>

    <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-3xl p-6 text-white shadow-xl flex flex-col justify-between group hover:scale-[1.02] transition-all">
        <div class="w-12 h-12 bg-white/10 text-white rounded-2xl flex items-center justify-center text-xl mb-4">💰</div>
        <div>
            <p class="text-[10px] font-bold text-white/50 uppercase tracking-widest mb-1">Total Platform Revenue</p>
            <h2 class="text-2xl font-black text-white">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h2>
            <div class="mt-1 flex flex-col gap-0.5">
                <span class="text-[9px] text-white/40">Fees: Rp {{ number_format($serviceFeeRevenue, 0, ',', '.') }}</span>
                <span class="text-[9px] text-white/40">Ads: Rp {{ number_format($adRevenue, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Revenue Chart -->
    <div class="lg:col-span-2 bg-white rounded-[40px] p-8 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-lg font-black text-slate-800 tracking-tight">Platform Revenue Trend</h3>
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Combined (Fees + Ads)</span>
        </div>
        <div class="h-72">
            <canvas id="adminRevenueChart"></canvas>
        </div>
    </div>

    <!-- Growth Chart -->
    <div class="bg-white rounded-[40px] p-8 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-lg font-black text-slate-800 tracking-tight">User Growth</h3>
        </div>
        <div class="h-72">
            <canvas id="growthChart"></canvas>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="bg-white rounded-[40px] p-8 border border-gray-100 shadow-sm lg:col-span-3">
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-black text-lg text-slate-800 tracking-tight">Recent Pending Withdrawals</h3>
            <a href="{{ route('admin.withdrawals') }}" class="text-xs font-bold text-color1 hover:underline">View All Processed</a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($recentWithdrawals as $w)
            <div class="flex items-center justify-between p-5 border border-gray-50 rounded-3xl bg-slate-50/50 group hover:bg-white hover:border-color1/20 transition-all">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-yellow-100 text-yellow-600 rounded-2xl flex items-center justify-center text-xl">⏳</div>
                    <div>
                        <p class="font-bold text-slate-800">{{ $w->seller->sellerProfile->brand_name ?? $w->seller->email }}</p>
                        <p class="text-xs font-bold text-color1">Rp {{ number_format($w->amount, 0, ',', '.') }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.withdrawals') }}" class="px-6 py-2.5 bg-white border border-gray-200 text-[10px] font-black uppercase tracking-widest text-slate-600 rounded-2xl hover:bg-slate-800 hover:text-white transition-all shadow-sm">Process</a>
            </div>
            @empty
            <div class="col-span-2 py-10 text-center">
                <span class="text-4xl block mb-2 opacity-20">📭</span>
                <p class="text-sm text-slate-400 font-bold">No pending withdrawals at the moment.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Revenue Line Chart
    const revCtx = document.getElementById('adminRevenueChart').getContext('2d');
    new Chart(revCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($months) !!},
            datasets: [{
                label: 'Revenue (Rp)',
                data: {!! json_encode($monthlyRevenueData) !!},
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

    // Growth Bar Chart
    const growthCtx = document.getElementById('growthChart').getContext('2d');
    new Chart(growthCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($months) !!},
            datasets: [
                {
                    label: 'Users',
                    data: {!! json_encode($userGrowth) !!},
                    backgroundColor: '#628ECB',
                    borderRadius: 8,
                },
                {
                    label: 'Sellers',
                    data: {!! json_encode($sellerGrowth) !!},
                    backgroundColor: '#A78BFA',
                    borderRadius: 8,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        font: { weight: 'bold', size: 10 }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.03)' },
                    ticks: { font: { weight: 'bold', size: 10 } }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { weight: 'bold', size: 10 } }
                }
            }
        }
    });
</script>
@endsection
