@extends('admin.layouts.app')
@section('title', 'تقارير الشكاوي')
@section('content')
@php
    $problemLabels = \App\Models\Complaint::problemTypes();
    $priorityLabels = \App\Models\Complaint::priorities();
@endphp
<div class="w-full">
    <div class="flex items-center justify-between gap-3 mb-6">
        <h2 class="text-2xl font-bold text-slate-800">تقارير الشكاوي</h2>
        <a href="{{ route('admin.complaints.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm px-4 py-2.5 rounded-lg">عودة للشكاوي</a>
    </div>

    {{-- Filters --}}
    <form method="GET" class="bg-white rounded-xl shadow-sm p-4 mb-6 grid grid-cols-1 md:grid-cols-4 gap-3">
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">من تاريخ</label>
            <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">إلى تاريخ</label>
            <input type="date" name="date_to" value="{{ $dateTo }}" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
        </div>
        @unless(auth('admin')->user()->isBranchAdmin())
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">الفرع</label>
            <select name="branch_id" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
                <option value="">كل الفروع</option>
                @foreach($branches as $b)
                <option value="{{ $b->id }}" {{ $branchId == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                @endforeach
            </select>
        </div>
        @endunless
        <div class="flex items-end">
            <button class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-6 py-2 rounded-lg w-full">تطبيق</button>
        </div>
    </form>

    {{-- Summary cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
        @php
        $cards = [
            ['label' => 'إجمالي', 'value' => $summary['total'], 'color' => 'bg-blue-50 text-blue-700'],
            ['label' => 'جديدة', 'value' => $summary['new'], 'color' => 'bg-sky-50 text-sky-700'],
            ['label' => 'قيد المعالجة', 'value' => $summary['in_progress'], 'color' => 'bg-amber-50 text-amber-700'],
            ['label' => 'تم الحل', 'value' => $summary['resolved'], 'color' => 'bg-emerald-50 text-emerald-700'],
            ['label' => 'مغلقة', 'value' => $summary['closed'], 'color' => 'bg-slate-50 text-slate-700'],
            ['label' => 'مصعّدة', 'value' => $summary['escalated'], 'color' => 'bg-red-50 text-red-700'],
            ['label' => 'على مساند', 'value' => $summary['on_musaned'], 'color' => 'bg-purple-50 text-purple-700'],
            ['label' => 'متأخرة (>7 أيام)', 'value' => $summary['stale'], 'color' => 'bg-rose-50 text-rose-700'],
        ];
        @endphp
        @foreach($cards as $card)
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-xs font-semibold {{ $card['color'] }} inline-block px-2 py-0.5 rounded">{{ $card['label'] }}</p>
            <p class="text-3xl font-bold text-slate-800 mt-3">{{ $card['value'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-sm font-bold text-slate-700 mb-4">حسب نوع المشكلة</h3>
            <canvas id="problemChart" height="160"></canvas>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-sm font-bold text-slate-700 mb-4">حسب الأولوية</h3>
            <canvas id="priorityChart" height="160"></canvas>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 lg:col-span-2">
            <h3 class="text-sm font-bold text-slate-700 mb-4">تطور عدد الشكاوي اليومي</h3>
            <canvas id="trendChart" height="100"></canvas>
        </div>
    </div>

    {{-- Branch performance --}}
    @unless(auth('admin')->user()->isBranchAdmin())
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-800">أداء الفروع في الشكاوي</h3>
            <p class="text-xs text-slate-500 mt-1">مقياس استجابة كل فرع — متوسط أيام الحل، نسبة الحل، الشكاوي المتأخرة</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-right">الفرع</th>
                        <th class="px-4 py-3 text-center">الإجمالي</th>
                        <th class="px-4 py-3 text-center">مفتوحة</th>
                        <th class="px-4 py-3 text-center">تم الحل</th>
                        <th class="px-4 py-3 text-center">مغلقة</th>
                        <th class="px-4 py-3 text-center">متأخرة</th>
                        <th class="px-4 py-3 text-center">نسبة الحل</th>
                        <th class="px-4 py-3 text-center">متوسط أيام الحل</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($branchPerformance as $row)
                    @php
                        $rate = $row->total ? round((($row->resolved + $row->closed) / $row->total) * 100, 1) : 0;
                        $rateColor = $rate >= 80 ? 'text-emerald-600' : ($rate >= 50 ? 'text-amber-600' : 'text-red-600');
                    @endphp
                    <tr>
                        <td class="px-4 py-3 font-semibold">{{ $row->branch->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-center">{{ $row->total }}</td>
                        <td class="px-4 py-3 text-center">{{ $row->open }}</td>
                        <td class="px-4 py-3 text-center text-emerald-700 font-semibold">{{ $row->resolved }}</td>
                        <td class="px-4 py-3 text-center">{{ $row->closed }}</td>
                        <td class="px-4 py-3 text-center {{ $row->stale > 0 ? 'text-red-600 font-semibold' : '' }}">{{ $row->stale }}</td>
                        <td class="px-4 py-3 text-center {{ $rateColor }} font-bold">{{ $rate }}%</td>
                        <td class="px-4 py-3 text-center">{{ $row->avg_resolution_days ? number_format($row->avg_resolution_days, 1) . ' يوم' : '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-4 py-10 text-center text-slate-400">لا توجد بيانات في النطاق المحدد</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endunless
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
    const problemData  = @json($byProblem);
    const priorityData = @json($byPriority);
    const trendData    = @json($trend);
    const problemLabels = @json($problemLabels);
    const priorityLabels = @json($priorityLabels);

    if (document.getElementById('problemChart')) {
        new Chart(document.getElementById('problemChart'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(problemData).map(k => problemLabels[k] || k),
                datasets: [{
                    data: Object.values(problemData),
                    backgroundColor: ['#3b82f6','#f59e0b','#ef4444','#10b981','#8b5cf6','#ec4899','#64748b'],
                }],
            },
            options: { plugins: { legend: { position: 'bottom' } } },
        });
    }
    if (document.getElementById('priorityChart')) {
        new Chart(document.getElementById('priorityChart'), {
            type: 'bar',
            data: {
                labels: Object.keys(priorityData).map(k => priorityLabels[k] || k),
                datasets: [{
                    label: 'العدد',
                    data: Object.values(priorityData),
                    backgroundColor: ['#94a3b8','#3b82f6','#f97316','#ef4444'],
                }],
            },
            options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } },
        });
    }
    if (document.getElementById('trendChart')) {
        new Chart(document.getElementById('trendChart'), {
            type: 'line',
            data: {
                labels: Object.keys(trendData),
                datasets: [{
                    label: 'شكاوي',
                    data: Object.values(trendData),
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.1)',
                    fill: true,
                    tension: 0.3,
                }],
            },
            options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } },
        });
    }
})();
</script>
@endsection
