@extends('admin.layouts.app')
@section('title', 'إحصائيات عقود الاستقدام')
@section('content')

@php
    $me = Auth::guard('admin')->user();
    $isSA = $me->isSuperAdmin();
    $baseUrl = route('admin.contracts.index');
@endphp

{{-- ── Header ──────────────────────────────────────────────────────────────── --}}
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <div>
        <h2 class="text-xl font-bold text-slate-800">إحصائيات عقود الاستقدام</h2>
        <p class="text-sm text-slate-400 mt-0.5">نظرة شاملة على أداء العقود والمراحل والفروع</p>
    </div>
    <div class="flex gap-2 items-center">
        @if($isSA)
        <form method="GET" class="flex gap-2 items-center">
            <select name="branch_id" onchange="this.form.submit()"
                    class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">— كل الفروع —</option>
                @foreach($branches as $br)
                <option value="{{ $br->id }}" {{ $branchId == $br->id ? 'selected' : '' }}>{{ $br->name }}</option>
                @endforeach
            </select>
        </form>
        @endif
        <a href="{{ route('admin.contracts.index') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg">عرض كل العقود →</a>
    </div>
</div>

{{-- ── Top Stat Cards ───────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">

    {{-- Total --}}
    <a href="{{ $baseUrl }}" class="group bg-white rounded-2xl border border-slate-100 shadow-sm p-4 hover:shadow-md hover:border-blue-200 transition-all cursor-pointer block">
        <div class="flex items-center justify-between mb-2">
            <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center">
                <svg width="18" height="18" fill="none" stroke="#2563eb" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 12h6M9 16h6M13 4H6a2 2 0 00-2 2v14a2 2 0 002 2h12a2 2 0 002-2V9z"/><polyline points="13 4 13 9 18 9"/></svg>
            </div>
            <span class="text-xs text-slate-400 group-hover:text-blue-500 transition">↗</span>
        </div>
        <p class="text-2xl font-black text-blue-600">{{ number_format($stats['total']) }}</p>
        <p class="text-xs text-slate-500 mt-0.5">إجمالي العقود</p>
    </a>

    {{-- Active --}}
    <a href="{{ $baseUrl }}" class="group bg-white rounded-2xl border border-slate-100 shadow-sm p-4 hover:shadow-md hover:border-indigo-200 transition-all cursor-pointer block">
        <div class="flex items-center justify-between mb-2">
            <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center">
                <svg width="18" height="18" fill="none" stroke="#6366f1" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <span class="text-xs text-slate-400 group-hover:text-indigo-500 transition">↗</span>
        </div>
        <p class="text-2xl font-black text-indigo-600">{{ number_format($stats['active']) }}</p>
        <p class="text-xs text-slate-500 mt-0.5">جارية / نشطة</p>
    </a>

    {{-- Received --}}
    <a href="{{ route('admin.reports.contracts-received') }}" class="group bg-white rounded-2xl border border-slate-100 shadow-sm p-4 hover:shadow-md hover:border-green-200 transition-all cursor-pointer block">
        <div class="flex items-center justify-between mb-2">
            <div class="w-9 h-9 rounded-xl bg-green-50 flex items-center justify-center">
                <svg width="18" height="18" fill="none" stroke="#16a34a" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <span class="text-xs text-slate-400 group-hover:text-green-500 transition">↗</span>
        </div>
        <p class="text-2xl font-black text-green-600">{{ number_format($stats['received']) }}</p>
        <p class="text-xs text-slate-500 mt-0.5">تم الاستلام</p>
    </a>

    {{-- Delayed --}}
    <a href="{{ route('admin.reports.contracts-delayed') }}" class="group bg-white rounded-2xl border border-slate-100 shadow-sm p-4 hover:shadow-md hover:border-orange-200 transition-all cursor-pointer block">
        <div class="flex items-center justify-between mb-2">
            <div class="w-9 h-9 rounded-xl bg-orange-50 flex items-center justify-center">
                <svg width="18" height="18" fill="none" stroke="#f97316" stroke-width="1.8" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <span class="text-xs text-slate-400 group-hover:text-orange-500 transition">↗</span>
        </div>
        <p class="text-2xl font-black text-orange-500">{{ number_format($stats['delayed']) }}</p>
        <p class="text-xs text-slate-500 mt-0.5">متأخرة</p>
    </a>

    {{-- Returned --}}
    <a href="{{ $baseUrl }}?status=14" class="group bg-white rounded-2xl border border-slate-100 shadow-sm p-4 hover:shadow-md hover:border-yellow-200 transition-all cursor-pointer block">
        <div class="flex items-center justify-between mb-2">
            <div class="w-9 h-9 rounded-xl bg-yellow-50 flex items-center justify-center">
                <svg width="18" height="18" fill="none" stroke="#ca8a04" stroke-width="1.8" viewBox="0 0 24 24"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 102.13-9.36L1 10"/></svg>
            </div>
            <span class="text-xs text-slate-400 group-hover:text-yellow-600 transition">↗</span>
        </div>
        <p class="text-2xl font-black text-yellow-600">{{ number_format($stats['returned']) }}</p>
        <p class="text-xs text-slate-500 mt-0.5">رجيع ضمان</p>
    </a>

    {{-- Escaped --}}
    <a href="{{ $baseUrl }}?status=15" class="group bg-white rounded-2xl border border-slate-100 shadow-sm p-4 hover:shadow-md hover:border-red-200 transition-all cursor-pointer block">
        <div class="flex items-center justify-between mb-2">
            <div class="w-9 h-9 rounded-xl bg-red-50 flex items-center justify-center">
                <svg width="18" height="18" fill="none" stroke="#ef4444" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
            </div>
            <span class="text-xs text-slate-400 group-hover:text-red-500 transition">↗</span>
        </div>
        <p class="text-2xl font-black text-red-500">{{ number_format($stats['escaped']) }}</p>
        <p class="text-xs text-slate-500 mt-0.5">هروب</p>
    </a>
</div>

{{-- ── 15 Status Cards ──────────────────────────────────────────────────────── --}}
@php
$statusCards = [
    1  => ['label'=>'جديد',                              'bg'=>'bg-blue-50',    'text'=>'text-blue-600',    'border'=>'hover:border-blue-300',   'dot'=>'bg-blue-500'],
    2  => ['label'=>'موافقة السفارة الأجنبية',            'bg'=>'bg-sky-50',     'text'=>'text-sky-600',     'border'=>'hover:border-sky-300',    'dot'=>'bg-sky-500'],
    3  => ['label'=>'بانتظار موافقة المكتب الخارجي',     'bg'=>'bg-cyan-50',    'text'=>'text-cyan-600',    'border'=>'hover:border-cyan-300',   'dot'=>'bg-cyan-500'],
    4  => ['label'=>'تم قبول مكتب العمل الخارجي',        'bg'=>'bg-teal-50',    'text'=>'text-teal-600',    'border'=>'hover:border-teal-300',   'dot'=>'bg-teal-500'],
    5  => ['label'=>'انتظار الابروف',                     'bg'=>'bg-emerald-50', 'text'=>'text-emerald-600', 'border'=>'hover:border-emerald-300','dot'=>'bg-emerald-500'],
    6  => ['label'=>'قبول العقد من مكتب العمل الخارجي',  'bg'=>'bg-green-50',   'text'=>'text-green-600',   'border'=>'hover:border-green-300',  'dot'=>'bg-green-500'],
    7  => ['label'=>'إرسال التأشيرة إلى السفارة السعودية','bg'=>'bg-lime-50',   'text'=>'text-lime-700',    'border'=>'hover:border-lime-300',   'dot'=>'bg-lime-500'],
    8  => ['label'=>'تم التأشير',                         'bg'=>'bg-green-50',   'text'=>'text-green-700',   'border'=>'hover:border-green-400',  'dot'=>'bg-green-600'],
    9  => ['label'=>'إلغاء التأشير',                      'bg'=>'bg-red-50',     'text'=>'text-red-600',     'border'=>'hover:border-red-300',    'dot'=>'bg-red-500'],
    10 => ['label'=>'تصريح سفر بعد تم التأشير',           'bg'=>'bg-indigo-50',  'text'=>'text-indigo-600',  'border'=>'hover:border-indigo-300', 'dot'=>'bg-indigo-500'],
    11 => ['label'=>'انتظار حجز تذكرة الطيران',           'bg'=>'bg-violet-50',  'text'=>'text-violet-600',  'border'=>'hover:border-violet-300', 'dot'=>'bg-violet-500'],
    12 => ['label'=>'معاد الوصول',                        'bg'=>'bg-purple-50',  'text'=>'text-purple-600',  'border'=>'hover:border-purple-300', 'dot'=>'bg-purple-500'],
    13 => ['label'=>'تم الاستلام',                        'bg'=>'bg-green-50',   'text'=>'text-green-700',   'border'=>'hover:border-green-400',  'dot'=>'bg-green-600'],
    14 => ['label'=>'رجيع خلال فترة الضمان',              'bg'=>'bg-amber-50',   'text'=>'text-amber-600',   'border'=>'hover:border-amber-300',  'dot'=>'bg-amber-500'],
    15 => ['label'=>'هروب',                               'bg'=>'bg-red-50',     'text'=>'text-red-600',     'border'=>'hover:border-red-300',    'dot'=>'bg-red-600'],
];
@endphp
<div class="mb-6">
    <h3 class="text-sm font-bold text-slate-600 mb-3 flex items-center gap-2">
        <span class="w-1 h-4 bg-slate-400 rounded-full inline-block"></span>
        توزيع العقود على الحالات الـ 15
    </h3>
    <div class="grid grid-cols-3 md:grid-cols-5 gap-3">
        @foreach($statusCards as $num => $sc)
        <a href="{{ $baseUrl }}?status={{ $num }}"
           class="group bg-white rounded-xl border border-slate-100 shadow-sm p-3 {{ $sc['border'] }} hover:shadow-md transition-all block">
            <div class="flex items-center justify-between mb-2">
                <span class="w-6 h-6 rounded-lg {{ $sc['bg'] }} flex items-center justify-center text-xs font-black {{ $sc['text'] }}">{{ $num }}</span>
                <span class="w-2 h-2 rounded-full {{ $sc['dot'] }} opacity-70"></span>
            </div>
            <p class="text-xl font-black {{ $sc['text'] }} leading-none">
                {{ number_format($stats['by_status'][$num] ?? 0) }}
            </p>
            <p class="text-xs text-slate-500 mt-1 leading-tight line-clamp-2">{{ $sc['label'] }}</p>
        </a>
        @endforeach
    </div>
</div>

{{-- ── Department Cards ─────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    @php
        $depts = [
            'customer_service' => ['label' => 'خدمة العملاء', 'color' => 'blue',   'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0'],
            'accounts'         => ['label' => 'الحسابات',     'color' => 'purple', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            'coordination'     => ['label' => 'التنسيق',      'color' => 'teal',   'icon' => 'M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z'],
        ];
        $deptColors = ['blue' => ['bg'=>'bg-blue-50','text'=>'text-blue-600','stroke'=>'#2563eb','border'=>'border-blue-200'], 'purple' => ['bg'=>'bg-purple-50','text'=>'text-purple-600','stroke'=>'#9333ea','border'=>'border-purple-200'], 'teal' => ['bg'=>'bg-teal-50','text'=>'text-teal-600','stroke'=>'#0d9488','border'=>'border-teal-200']];
    @endphp
    @foreach($depts as $key => $dept)
    @php $clr = $deptColors[$dept['color']]; @endphp
    <a href="{{ $baseUrl }}?department={{ $key }}"
       class="group bg-white rounded-2xl border border-slate-100 shadow-sm p-5 hover:shadow-md hover:{{ $clr['border'] }} transition-all block">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl {{ $clr['bg'] }} flex items-center justify-center flex-shrink-0">
                <svg width="22" height="22" fill="none" stroke="{{ $clr['stroke'] }}" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="{{ $dept['icon'] }}"/>
                </svg>
            </div>
            <div class="flex-1">
                <p class="text-xs text-slate-400 font-medium">{{ $dept['label'] }}</p>
                <p class="text-3xl font-black {{ $clr['text'] }} leading-none mt-0.5">
                    {{ number_format($stats['by_dept'][$key] ?? 0) }}
                </p>
            </div>
            @if($stats['total'] > 0)
            <div class="text-right">
                <p class="text-lg font-bold {{ $clr['text'] }}">
                    {{ round(($stats['by_dept'][$key] ?? 0) / $stats['total'] * 100) }}%
                </p>
                <p class="text-xs text-slate-400">من الإجمالي</p>
            </div>
            @endif
        </div>
        @if($stats['total'] > 0)
        <div class="mt-3 h-1.5 bg-slate-100 rounded-full overflow-hidden">
            <div class="h-full {{ $clr['bg'] }} rounded-full" style="width:{{ round(($stats['by_dept'][$key] ?? 0) / $stats['total'] * 100) }}%;background:{{ $clr['stroke'] }};"></div>
        </div>
        @endif
    </a>
    @endforeach
</div>

{{-- ── Payment Status Cards ─────────────────────────────────────────────────── --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    @php
        $pays = [
            'pending' => ['label' => 'معلق',  'color' => '#94a3b8', 'bg' => 'bg-slate-50'],
            'partial' => ['label' => 'جزئي',  'color' => '#f59e0b', 'bg' => 'bg-yellow-50'],
            'full'    => ['label' => 'كامل',  'color' => '#16a34a', 'bg' => 'bg-green-50'],
        ];
    @endphp
    @foreach($pays as $key => $pay)
    <a href="{{ $baseUrl }}?payment_status={{ $key }}"
       class="group bg-white rounded-xl border border-slate-100 shadow-sm p-4 hover:shadow-md transition-all flex items-center gap-3 block">
        <div class="w-3 h-10 rounded-full flex-shrink-0" style="background:{{ $pay['color'] }}"></div>
        <div>
            <p class="text-xs text-slate-400">{{ $pay['label'] }}</p>
            <p class="text-2xl font-black" style="color:{{ $pay['color'] }}">{{ number_format($stats['by_payment'][$key] ?? 0) }}</p>
        </div>
        <div class="flex-1 text-left">
            @if($stats['total'] > 0)
            <p class="text-sm font-semibold text-slate-400">{{ round(($stats['by_payment'][$key] ?? 0) / $stats['total'] * 100) }}%</p>
            @endif
        </div>
    </a>
    @endforeach
</div>

{{-- ── Charts Row 1 ────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">

    {{-- Monthly Trend --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-slate-700">حركة العقود الشهرية</h3>
            <span class="text-xs text-slate-400">آخر 6 أشهر</span>
        </div>
        <div style="height:220px;">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>

    {{-- Status Distribution Donut --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-slate-700">توزيع الحالات</h3>
            <span class="text-xs text-slate-400">{{ $stats['total'] }} عقد</span>
        </div>
        <div style="height:220px;display:flex;align-items:center;justify-content:center;">
            <canvas id="statusDonut"></canvas>
        </div>
    </div>
</div>

{{-- ── Branch Comparison Chart (super admin only) ──────────────────────────── --}}
@if($isSA && $stats['branch_monthly'])
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 mb-5">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-slate-700">مقارنة العقود بين الفروع</h3>
        <span class="text-xs text-slate-400">آخر 6 أشهر</span>
    </div>
    <div style="height:260px;">
        <canvas id="branchChart"></canvas>
    </div>
</div>
@endif

{{-- ── Branch Comparison Table (super admin only) ──────────────────────────── --}}
@if($isSA && count($stats['branch_table']) > 0)
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-5">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
        <h3 class="font-bold text-slate-700">مقارنة الفروع التفصيلية</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-right">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-4 py-3 font-semibold text-slate-600">الفرع</th>
                    <th class="px-4 py-3 font-semibold text-slate-600 text-center">الإجمالي</th>
                    <th class="px-4 py-3 font-semibold text-slate-600 text-center">نشطة</th>
                    <th class="px-4 py-3 font-semibold text-blue-600 text-center">خدمة عملاء</th>
                    <th class="px-4 py-3 font-semibold text-purple-600 text-center">حسابات</th>
                    <th class="px-4 py-3 font-semibold text-teal-600 text-center">تنسيق</th>
                    <th class="px-4 py-3 font-semibold text-green-600 text-center">مستلمة</th>
                    <th class="px-4 py-3 font-semibold text-yellow-600 text-center">رجيع</th>
                    <th class="px-4 py-3 font-semibold text-red-600 text-center">هروب</th>
                    <th class="px-4 py-3 font-semibold text-slate-500 text-center">إجراء</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($stats['branch_table'] as $row)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-4 py-3 font-semibold text-slate-700">{{ $row['name'] }}</td>
                    <td class="px-4 py-3 text-center font-bold text-blue-600">{{ $row['total'] }}</td>
                    <td class="px-4 py-3 text-center text-indigo-600">{{ $row['active'] }}</td>
                    <td class="px-4 py-3 text-center text-blue-500">{{ $row['cs'] }}</td>
                    <td class="px-4 py-3 text-center text-purple-500">{{ $row['acc'] }}</td>
                    <td class="px-4 py-3 text-center text-teal-500">{{ $row['coord'] }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-block px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-xs font-semibold">{{ $row['received'] }}</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-block px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-700 text-xs font-semibold">{{ $row['returned'] }}</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-block px-2 py-0.5 rounded-full bg-red-100 text-red-600 text-xs font-semibold">{{ $row['escaped'] }}</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('admin.contracts.index') }}?branch_id={{ $row['id'] }}"
                           class="text-blue-600 hover:text-blue-800 text-xs">عرض العقود</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
            {{-- Totals Row --}}
            <tfoot class="bg-slate-50 border-t-2 border-slate-200">
                <tr>
                    <td class="px-4 py-3 font-bold text-slate-700">الإجمالي</td>
                    <td class="px-4 py-3 text-center font-black text-blue-700">{{ collect($stats['branch_table'])->sum('total') }}</td>
                    <td class="px-4 py-3 text-center font-bold text-indigo-600">{{ collect($stats['branch_table'])->sum('active') }}</td>
                    <td class="px-4 py-3 text-center font-bold text-blue-500">{{ collect($stats['branch_table'])->sum('cs') }}</td>
                    <td class="px-4 py-3 text-center font-bold text-purple-500">{{ collect($stats['branch_table'])->sum('acc') }}</td>
                    <td class="px-4 py-3 text-center font-bold text-teal-500">{{ collect($stats['branch_table'])->sum('coord') }}</td>
                    <td class="px-4 py-3 text-center font-bold text-green-600">{{ collect($stats['branch_table'])->sum('received') }}</td>
                    <td class="px-4 py-3 text-center font-bold text-yellow-600">{{ collect($stats['branch_table'])->sum('returned') }}</td>
                    <td class="px-4 py-3 text-center font-bold text-red-600">{{ collect($stats['branch_table'])->sum('escaped') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    Chart.defaults.font.family = 'Cairo, sans-serif';
    Chart.defaults.font.size   = 12;
    Chart.defaults.color       = '#94a3b8';

    const monthLabels  = @json($stats['month_labels']);
    const monthCounts  = @json($stats['month_counts']);
    const statusLabels = @json($stats['status_labels']);
    const statusData   = @json($stats['status_data']);
    const statusColors = @json($stats['status_colors']);

    // ── Monthly Bar Chart ──────────────────────────────────────────────────
    new Chart(document.getElementById('monthlyChart'), {
        type: 'bar',
        data: {
            labels: monthLabels,
            datasets: [{
                label: 'عدد العقود',
                data: monthCounts,
                backgroundColor: '#2563ebcc',
                borderColor: '#2563eb',
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: ctx => ` ${ctx.parsed.y} عقد` } }
            },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f1f5f9' } },
                x: { grid: { display: false } }
            }
        }
    });

    // ── Status Donut ───────────────────────────────────────────────────────
    if (statusData.length > 0) {
        new Chart(document.getElementById('statusDonut'), {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusData,
                    backgroundColor: statusColors,
                    borderWidth: 2,
                    borderColor: '#fff',
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'left',
                        align: 'center',
                        labels: { boxWidth: 10, padding: 10, font: { size: 10 } }
                    },
                    tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed} عقد` } }
                }
            }
        });
    }

    // ── Branch Comparison Chart ────────────────────────────────────────────
    @if($isSA && $stats['branch_monthly'])
    const branchMonthly = @json($stats['branch_monthly']);
    new Chart(document.getElementById('branchChart'), {
        type: 'bar',
        data: {
            labels: branchMonthly.labels,
            datasets: branchMonthly.datasets,
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12, padding: 16 } },
                tooltip: { mode: 'index', intersect: false }
            },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f1f5f9' } },
                x: { grid: { display: false } }
            }
        }
    });
    @endif
});
</script>
@endpush
