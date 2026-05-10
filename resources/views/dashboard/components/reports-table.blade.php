<table class="w-full text-sm">
    <thead>
        <tr class="border-b border-gray-100">
            <th class="text-left px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Kode</th>
            <th class="text-left px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Pelapor</th>
            <th class="text-left px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Dilaporkan</th>
            <th class="text-left px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Alasan</th>
            <th class="text-left px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Status</th>
            <th class="text-left px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Tanggal</th>
            <th class="px-6 py-4"></th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-50">
        @forelse($reports as $report)
        <tr class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4">
                <code class="text-xs font-bold text-color1 bg-color1/10 px-2 py-1 rounded-lg">
                    {{ $report->report_code ?? 'N/A' }}
                </code>
            </td>
            <td class="px-6 py-4">
                @if($report->reporter)
                    <p class="font-bold text-slate-700 text-xs">
                        {{ $report->reporter->userProfile->name ?? $report->reporter->sellerProfile->brand_name ?? $report->reporter->email }}
                    </p>
                    <p class="text-slate-400 text-[10px] uppercase font-bold">{{ $report->reporter->role }}</p>
                @else
                    <span class="text-slate-400 text-xs italic">Admin (System)</span>
                @endif
            </td>
            <td class="px-6 py-4">
                @if($report->reportedUser)
                    <p class="font-bold text-slate-700 text-xs">
                        {{ $report->reportedUser->userProfile->name ?? $report->reportedUser->sellerProfile->brand_name ?? $report->reportedUser->email }}
                    </p>
                    <p class="text-slate-400 text-[10px] uppercase font-bold">{{ $report->reportedUser->role }}</p>
                @endif
                @if($report->reportedService)
                    <p class="font-bold text-slate-700 text-xs mt-1">📦 {{ $report->reportedService->service_name }}</p>
                @endif
            </td>
            <td class="px-6 py-4">
                <p class="text-xs font-bold text-slate-700 max-w-[140px] truncate" title="{{ $report->reason }}">{{ $report->reason }}</p>
            </td>
            <td class="px-6 py-4">
                @php
                    $statusColor = match($report->status) {
                        'pending'  => 'bg-yellow-100 text-yellow-600',
                        'reviewed' => 'bg-blue-100 text-blue-600',
                        'resolved' => 'bg-green-100 text-green-600',
                        default    => 'bg-gray-100 text-gray-500',
                    };
                @endphp
                <span class="text-[10px] font-bold uppercase px-2 py-1 rounded-lg {{ $statusColor }}">
                    {{ $report->status }}
                </span>
            </td>
            <td class="px-6 py-4">
                <p class="text-xs text-slate-400 font-medium whitespace-nowrap">{{ $report->created_at->format('d M Y') }}</p>
                <p class="text-[10px] text-slate-300 font-medium">{{ $report->created_at->format('H:i') }}</p>
            </td>
            <td class="px-6 py-4">
                <a href="{{ route('admin.report-center.show', $report) }}"
                   class="px-4 py-2 bg-color1 hover:bg-color2 text-white font-bold rounded-xl text-xs transition-all shadow-sm whitespace-nowrap">
                    Detail →
                </a>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="px-6 py-16 text-center">
                <span class="text-4xl block mb-3 opacity-30">🚩</span>
                <p class="font-bold text-slate-400">{{ $emptyMessage ?? 'Tidak ada laporan ditemukan' }}</p>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
