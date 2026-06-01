@extends('admin.layouts.app')
@section('title', 'تقرير أداء الموظفين - التسويق')
@section('content')
@php
    $periodLabels = [
        'this_month' => 'هذا الشهر',
        'last_month' => 'الشهر الماضي',
        'last_3'     => 'آخر 3 أشهر',
        'last_6'     => 'آخر 6 أشهر',
        'all'        => 'كل الوقت',
    ];
    $tierColors = [
        'green'  => ['bg' => 'bg-green-100',  'text' => 'text-green-700',  'ring' => 'ring-green-300',  'bar' => 'bg-green-500'],
        'amber'  => ['bg' => 'bg-amber-100',  'text' => 'text-amber-700',  'ring' => 'ring-amber-300',  'bar' => 'bg-amber-500'],
        'orange' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'ring' => 'ring-orange-300', 'bar' => 'bg-orange-500'],
        'red'    => ['bg' => 'bg-red-100',    'text' => 'text-red-700',    'ring' => 'ring-red-300',    'bar' => 'bg-red-400'],
    ];
@endphp

{{-- ═══════════════ HEADER ═══════════════ --}}
<div class="flex items-center justify-between mb-6 flex-wrap gap-3">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center"
             style="background:linear-gradient(135deg,#6366f1,#8b5cf6)">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
            </svg>
        </div>
        <div>
            <h2 class="text-xl font-bold text-slate-800">تقرير أداء الموظفين</h2>
            <p class="text-slate-400 text-xs mt-0.5">
                {{ $periodLabels[$period] }} &mdash;
                {{ $dateStart->format('d/m/Y') }} إلى {{ $dateEnd->format('d/m/Y') }}
            </p>
        </div>
    </div>
    <a href="{{ route('admin.marketing.reports') }}"
       class="flex items-center gap-2 text-sm text-slate-500 hover:text-slate-700 bg-white border border-slate-200 rounded-xl px-4 py-2 shadow-sm transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
        تقارير التسويق
    </a>
</div>

{{-- ═══════════════ FILTERS ═══════════════ --}}
<form method="GET" action="{{ route('admin.marketing.staff-performance') }}"
      class="bg-white border border-slate-200 rounded-2xl shadow-sm p-4 mb-6">
    <div class="flex flex-wrap gap-3 items-end">

        {{-- Period --}}
        <div class="flex-1 min-w-40">
            <label class="block text-xs font-medium text-slate-500 mb-1">الفترة الزمنية</label>
            <select name="period" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                @foreach($periodLabels as $val => $lbl)
                    <option value="{{ $val }}" {{ $period === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                @endforeach
            </select>
        </div>

        {{-- Branch (super-admin only) --}}
        @if($me->isSuperAdmin())
        <div class="flex-1 min-w-40">
            <label class="block text-xs font-medium text-slate-500 mb-1">الفرع</label>
            <select name="branch_id" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                <option value="">كل الفروع</option>
                @foreach($branches as $b)
                    <option value="{{ $b->id }}" {{ $branchId == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                @endforeach
            </select>
        </div>
        @endif

        <button type="submit"
                class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl px-5 py-2 transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
            </svg>
            عرض
        </button>
    </div>
</form>

{{-- ═══════════════ SUMMARY CARDS ═══════════════ --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
    @php
        $cards = [
            ['label'=>'إجمالي الموظفين',  'value'=> $summary['total_staff'],     'icon'=>'M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2 M9 7a4 4 0 100 8 4 4 0 000-8z', 'color'=>'indigo'],
            ['label'=>'اللييدز المعينة',   'value'=> $summary['total_leads'],     'icon'=>'M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2 M22 11h-6 M19 8v6',             'color'=>'blue'],
            ['label'=>'تحولوا لعملاء',     'value'=> $summary['total_converted'], 'icon'=>'M20 6L9 17l-5-5',                                                         'color'=>'green'],
            ['label'=>'إجمالي الاتصالات', 'value'=> $summary['total_calls'],     'icon'=>'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z', 'color'=>'violet'],
            ['label'=>'لم تتم متابعتهم', 'value'=> $summary['never_called'],   'icon'=>'M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21', 'color'=>'red'],
            ['label'=>'متوسط النقاط',      'value'=> $summary['avg_score'] . '/100', 'icon'=>'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z', 'color'=>'amber'],
        ];
        $cardColors = [
            'indigo' => ['bg'=>'bg-indigo-50','text'=>'text-indigo-700','icon'=>'text-indigo-500'],
            'blue'   => ['bg'=>'bg-blue-50',  'text'=>'text-blue-700',  'icon'=>'text-blue-500'],
            'green'  => ['bg'=>'bg-green-50', 'text'=>'text-green-700', 'icon'=>'text-green-500'],
            'violet' => ['bg'=>'bg-violet-50','text'=>'text-violet-700','icon'=>'text-violet-500'],
            'red'    => ['bg'=>'bg-red-50',   'text'=>'text-red-700',   'icon'=>'text-red-500'],
            'amber'  => ['bg'=>'bg-amber-50', 'text'=>'text-amber-700', 'icon'=>'text-amber-500'],
        ];
    @endphp
    @foreach($cards as $c)
    @php $cc = $cardColors[$c['color']]; @endphp
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-4 flex flex-col gap-1">
        <div class="flex items-center gap-2 mb-1">
            <div class="w-7 h-7 rounded-lg {{ $cc['bg'] }} flex items-center justify-center">
                <svg class="w-3.5 h-3.5 {{ $cc['icon'] }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="{{ $c['icon'] }}"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold {{ $cc['text'] }}">{{ $c['value'] }}</p>
        <p class="text-xs text-slate-500">{{ $c['label'] }}</p>
    </div>
    @endforeach
</div>

{{-- ═══════════════ TABLE ═══════════════ --}}
@if($rows->isEmpty())
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-16 text-center">
    <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
        <circle cx="9" cy="7" r="4"/>
    </svg>
    <p class="text-slate-400 text-sm">لا توجد بيانات موظفين للفترة المحددة</p>
</div>
@else
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

    {{-- Legend --}}
    <div class="px-5 py-3 border-b border-slate-100 flex flex-wrap gap-4 text-xs text-slate-500">
        <span class="font-semibold text-slate-700">مقياس الأداء:</span>
        @foreach(['green'=>'ممتاز (70+)', 'amber'=>'جيد (45–69)', 'orange'=>'مقبول (20–44)', 'red'=>'ضعيف (<20)'] as $c => $lbl)
        <span class="flex items-center gap-1.5">
            <span class="inline-block w-2.5 h-2.5 rounded-full {{ $tierColors[$c]['bar'] }}"></span>
            {{ $lbl }}
        </span>
        @endforeach
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 text-slate-600 text-xs font-semibold">
                    <th class="px-4 py-3 text-right">#</th>
                    <th class="px-4 py-3 text-right">الموظف</th>
                    @if($me->isSuperAdmin() && !$branchId)
                    <th class="px-4 py-3 text-right">الفرع</th>
                    @endif
                    <th class="px-4 py-3 text-center">اللييدز<br><span class="font-normal text-slate-400">المعينة</span></th>
                    <th class="px-4 py-3 text-center">اتصل بهم<br><span class="font-normal text-slate-400">من المعينين</span></th>
                    <th class="px-4 py-3 text-center">لم يُتابَعوا<br><span class="font-normal text-red-400">⚠</span></th>
                    <th class="px-4 py-3 text-center">إجمالي<br><span class="font-normal text-slate-400">الاتصالات</span></th>
                    <th class="px-4 py-3 text-center">تحولوا<br><span class="font-normal text-slate-400">لعميل</span></th>
                    <th class="px-4 py-3 text-center" title="نسبة الاتصالات التي تمت في نفس يوم إنشاء الليد">نفس اليوم<br><span class="font-normal text-slate-400">%</span></th>
                    <th class="px-4 py-3 text-center">معدل<br><span class="font-normal text-slate-400">الاتصال %</span></th>
                    <th class="px-4 py-3 text-center">معدل<br><span class="font-normal text-slate-400">التحويل %</span></th>
                    <th class="px-4 py-3 text-center">النقاط</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($rows as $i => $row)
                @php
                    $tc = $tierColors[$row['tier']['color']];
                    $admin = $row['admin'];
                @endphp
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-4 py-3 text-slate-400 text-xs font-mono">{{ $i + 1 }}</td>

                    {{-- Employee --}}
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                 style="background:linear-gradient(135deg,{{ ['#6366f1','#8b5cf6','#ec4899','#06b6d4','#10b981','#f59e0b'][($i % 6)]}},{{ ['#8b5cf6','#a78bfa','#f472b6','#22d3ee','#34d399','#fbbf24'][($i % 6)]}})">
                                {{ mb_substr($admin->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-medium text-slate-800 text-sm">{{ $admin->name }}</p>
                                <p class="text-xs text-slate-400">{{ $admin->departmentLabel }}</p>
                            </div>
                        </div>
                    </td>

                    {{-- Branch (super-admin, no filter) --}}
                    @if($me->isSuperAdmin() && !$branchId)
                    <td class="px-4 py-3 text-slate-600 text-xs">{{ $admin->branch?->name ?? '—' }}</td>
                    @endif

                    {{-- Total leads --}}
                    <td class="px-4 py-3 text-center">
                        <span class="font-semibold text-slate-700">{{ $row['total_leads'] }}</span>
                        @if($row['total_leads'] > 0)
                        <div class="text-[10px] text-slate-400 mt-0.5">
                            <span class="text-blue-500">{{ $row['new_leads'] }} جديد</span>
                            · <span class="text-amber-500">{{ $row['in_progress'] }} متابعة</span>
                        </div>
                        @endif
                    </td>

                    {{-- Called leads --}}
                    <td class="px-4 py-3 text-center">
                        <span class="font-semibold text-indigo-600">{{ $row['called_leads'] }}</span>
                        <span class="text-slate-400 text-xs"> / {{ $row['total_leads'] }}</span>
                    </td>

                    {{-- Never called --}}
                    <td class="px-4 py-3 text-center">
                        @if($row['never_called'] > 0)
                        <span class="inline-flex items-center gap-1 bg-red-50 text-red-600 text-xs font-semibold px-2 py-0.5 rounded-full">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 9v4m0 4h.01M12 3a9 9 0 100 18A9 9 0 0012 3z"/></svg>
                            {{ $row['never_called'] }}
                        </span>
                        @else
                        <span class="text-green-600 text-xs font-medium">✓ الكل</span>
                        @endif
                    </td>

                    {{-- Total calls --}}
                    <td class="px-4 py-3 text-center font-semibold text-violet-600">{{ $row['total_calls'] }}</td>

                    {{-- Converted --}}
                    <td class="px-4 py-3 text-center">
                        @if($row['converted'] > 0)
                        <span class="inline-flex items-center gap-1 bg-green-50 text-green-700 text-xs font-semibold px-2 py-0.5 rounded-full">
                            ✓ {{ $row['converted'] }}
                        </span>
                        @else
                        <span class="text-slate-300 text-xs">—</span>
                        @endif
                    </td>

                    {{-- Same-day rate --}}
                    <td class="px-4 py-3 text-center">
                        @php $sdr = $row['same_day_rate']; @endphp
                        <div class="flex items-center justify-center gap-1.5">
                            <div class="w-14 h-1.5 rounded-full bg-slate-100 overflow-hidden">
                                <div class="h-full rounded-full {{ $sdr >= 60 ? 'bg-green-500' : ($sdr >= 30 ? 'bg-amber-400' : 'bg-red-400') }}"
                                     style="width:{{ min($sdr, 100) }}%"></div>
                            </div>
                            <span class="text-xs font-medium {{ $sdr >= 60 ? 'text-green-600' : ($sdr >= 30 ? 'text-amber-600' : 'text-red-500') }}">
                                {{ $sdr }}%
                            </span>
                        </div>
                    </td>

                    {{-- Call rate --}}
                    <td class="px-4 py-3 text-center">
                        @php $cr = $row['call_rate']; @endphp
                        <div class="flex items-center justify-center gap-1.5">
                            <div class="w-14 h-1.5 rounded-full bg-slate-100 overflow-hidden">
                                <div class="h-full rounded-full {{ $cr >= 80 ? 'bg-green-500' : ($cr >= 50 ? 'bg-amber-400' : 'bg-red-400') }}"
                                     style="width:{{ min($cr, 100) }}%"></div>
                            </div>
                            <span class="text-xs font-medium {{ $cr >= 80 ? 'text-green-600' : ($cr >= 50 ? 'text-amber-600' : 'text-red-500') }}">
                                {{ $cr }}%
                            </span>
                        </div>
                    </td>

                    {{-- Conversion rate --}}
                    <td class="px-4 py-3 text-center">
                        @php $cvr = $row['conversion_rate']; @endphp
                        <div class="flex items-center justify-center gap-1.5">
                            <div class="w-14 h-1.5 rounded-full bg-slate-100 overflow-hidden">
                                <div class="h-full rounded-full bg-indigo-500" style="width:{{ min($cvr, 100) }}%"></div>
                            </div>
                            <span class="text-xs font-medium text-indigo-700">{{ $cvr }}%</span>
                        </div>
                    </td>

                    {{-- Score --}}
                    <td class="px-4 py-3 text-center">
                        <div class="inline-flex flex-col items-center gap-1">
                            <span class="text-lg font-bold {{ $tc['text'] }}">{{ $row['score'] }}</span>
                            <span class="text-[10px] {{ $tc['bg'] }} {{ $tc['text'] }} px-2 py-0.5 rounded-full font-medium ring-1 {{ $tc['ring'] }}">
                                {{ $row['tier']['label'] }}
                            </span>
                        </div>
                    </td>
                </tr>

                {{-- Expandable detail row --}}
                <tr class="bg-slate-50/60 border-b border-slate-100">
                    <td colspan="{{ $me->isSuperAdmin() && !$branchId ? 12 : 11 }}" class="px-6 pb-3 pt-1">
                        <div class="flex flex-wrap gap-4 text-xs text-slate-500">
                            <span>
                                <span class="font-medium text-slate-700">المؤرشفة:</span>
                                {{ $row['archived'] }}
                            </span>
                            <span>
                                <span class="font-medium text-slate-700">اتصالات نفس اليوم:</span>
                                {{ $row['same_day_calls'] }} من {{ $row['total_calls'] }}
                            </span>
                            <span>
                                <span class="font-medium text-slate-700">اتصالات متأخرة:</span>
                                @if($row['delayed_calls'] > 0)
                                    <span class="text-red-500">{{ $row['delayed_calls'] }}</span>
                                @else
                                    <span class="text-green-600">لا يوجد</span>
                                @endif
                            </span>
                            <span>
                                <span class="font-medium text-slate-700">متوسط الاتصالات / ليد:</span>
                                {{ $row['total_leads'] > 0 ? round($row['total_calls'] / $row['total_leads'], 1) : 0 }}
                            </span>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Score Explanation --}}
    <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/60 text-xs text-slate-500 flex flex-wrap gap-x-6 gap-y-1">
        <span class="font-semibold text-slate-600">طريقة حساب النقاط:</span>
        <span>معدل الاتصال <strong>× 30%</strong></span>
        <span>معدل التحويل <strong>× 40%</strong></span>
        <span>نسبة نفس اليوم <strong>× 20%</strong></span>
        <span>النشاط (الاتصالات) <strong>× 10%</strong></span>
    </div>
</div>
@endif

@endsection
