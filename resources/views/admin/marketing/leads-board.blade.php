@extends('admin.layouts.app')
@section('title', 'لوحة الفروع — العملاء المحتملون')
@section('content')

{{-- ═══════════════ HEADER ═══════════════ --}}
<div class="flex items-center justify-between mb-6 flex-wrap gap-3">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center"
             style="background:linear-gradient(135deg,#3b82f6,#6366f1)">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="3" y="3" width="7" height="7" rx="1"/>
                <rect x="14" y="3" width="7" height="7" rx="1"/>
                <rect x="3" y="14" width="7" height="7" rx="1"/>
                <rect x="14" y="14" width="7" height="7" rx="1"/>
            </svg>
        </div>
        <div>
            <h2 class="text-xl font-bold text-slate-800">لوحة الفروع — العملاء المحتملون</h2>
            <p class="text-slate-400 text-xs mt-0.5">عرض مجمّع حسب الفرع مع توزيع الموظفين وتنبيهات التأخير</p>
        </div>
    </div>
    <a href="{{ route('admin.marketing.leads.index') }}"
       class="flex items-center gap-2 text-sm text-slate-500 hover:text-slate-700 bg-white border border-slate-200 rounded-xl px-4 py-2 shadow-sm transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M9 5l7 7-7 7"/>
        </svg>
        قائمة العملاء المحتملين
    </a>
</div>

{{-- ═══════════════ SUMMARY CARDS ═══════════════ --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
    @php
        $summaryCards = [
            ['label' => 'إجمالي العملاء',   'value' => $summary['total'],    'color' => 'indigo', 'icon' => 'M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 7a4 4 0 100 8 4 4 0 000-8z'],
            ['label' => 'جديد',              'value' => $summary['new'],      'color' => 'blue',   'icon' => 'M12 9v3m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'قيد المتابعة',     'value' => $summary['progress'], 'color' => 'amber',  'icon' => 'M12 8v4l3 3'],
            ['label' => 'تم التحويل',        'value' => $summary['converted'],'color' => 'green',  'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'تأخير (24 ساعة)',  'value' => $summary['stale'],   'color' => 'orange', 'icon' => 'M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z'],
            ['label' => 'حرج (4 أيام+)',    'value' => $summary['critical'], 'color' => 'red',    'icon' => 'M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z'],
        ];
    @endphp

    @foreach($summaryCards as $card)
    @php
        $colorMap = [
            'indigo' => ['bg'=>'bg-indigo-50','text'=>'text-indigo-700','icon'=>'text-indigo-500','border'=>'border-indigo-100'],
            'blue'   => ['bg'=>'bg-blue-50',  'text'=>'text-blue-700',  'icon'=>'text-blue-500',  'border'=>'border-blue-100'],
            'amber'  => ['bg'=>'bg-amber-50', 'text'=>'text-amber-700', 'icon'=>'text-amber-500', 'border'=>'border-amber-100'],
            'green'  => ['bg'=>'bg-green-50', 'text'=>'text-green-700', 'icon'=>'text-green-500', 'border'=>'border-green-100'],
            'orange' => ['bg'=>'bg-orange-50','text'=>'text-orange-700','icon'=>'text-orange-500','border'=>'border-orange-100'],
            'red'    => ['bg'=>'bg-red-50',   'text'=>'text-red-700',   'icon'=>'text-red-500',   'border'=>'border-red-100'],
        ];
        $c = $colorMap[$card['color']];
    @endphp
    <div class="bg-white border {{ $c['border'] }} rounded-2xl p-4 shadow-sm flex flex-col gap-1">
        <div class="flex items-center justify-between">
            <span class="text-xs text-slate-500 font-medium leading-tight">{{ $card['label'] }}</span>
            <div class="w-7 h-7 rounded-lg {{ $c['bg'] }} flex items-center justify-center">
                <svg class="w-4 h-4 {{ $c['icon'] }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold {{ $c['text'] }}">{{ number_format($card['value']) }}</p>
    </div>
    @endforeach
</div>

{{-- ═══════════════ SESSION MESSAGES ═══════════════ --}}
@if(session('success'))
<div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 rounded-2xl px-4 py-3 text-sm mb-4">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 rounded-2xl px-4 py-3 text-sm mb-4">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
    {{ session('error') }}
</div>
@endif

{{-- ═══════════════ BRANCHES GRID ═══════════════ --}}
@if($branches->isEmpty())
<div class="text-center py-16 text-slate-400">
    <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <path d="M3 5a2 2 0 012-2h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5z"/>
    </svg>
    <p class="font-medium">لا توجد فروع نشطة</p>
</div>
@else

{{-- Group branches by city --}}
@php
    $byCity = $branches->groupBy(fn($b) => $b->city ?: 'غير محدد');
@endphp

@foreach($byCity as $city => $cityBranches)
{{-- City Section --}}
<div class="mb-8">
    {{-- City Header --}}
    <div class="flex items-center gap-3 mb-4">
        <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <h3 class="text-base font-bold text-slate-700">{{ $city }}</h3>
        <span class="text-xs text-slate-400 bg-slate-100 rounded-full px-2 py-0.5">
            {{ $cityBranches->count() }} {{ $cityBranches->count() === 1 ? 'فرع' : 'فروع' }}
        </span>
        <div class="flex-1 h-px bg-slate-200 mr-1"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
    @foreach($cityBranches as $branch)
    @php
        $hasCritical = $branch->leads_critical > 0;
        $hasStale    = $branch->leads_stale > 0;
        $cardBorder  = $hasCritical ? 'border-red-300 shadow-red-100' : ($hasStale ? 'border-amber-300 shadow-amber-100' : 'border-slate-200');
        $totalActive = $branch->leads_new + $branch->leads_in_progress;
        $pctNew      = $totalActive > 0 ? round($branch->leads_new / ($branch->leads_total ?: 1) * 100) : 0;
        $pctProgress = $totalActive > 0 ? round($branch->leads_in_progress / ($branch->leads_total ?: 1) * 100) : 0;
        $pctConverted = $branch->leads_total > 0 ? round($branch->leads_converted / $branch->leads_total * 100) : 0;
    @endphp

    <div class="bg-white border {{ $cardBorder }} rounded-2xl shadow-sm overflow-hidden">
        {{-- Card Header --}}
        <div class="p-4 border-b border-slate-100">
            <div class="flex items-start justify-between gap-2">
                <div>
                    <h4 class="font-bold text-slate-800 text-sm leading-tight">{{ $branch->name }}</h4>
                    @if($branch->code)
                    <span class="text-xs text-slate-400">{{ $branch->code }}</span>
                    @endif
                </div>
                <div class="flex items-center gap-1 flex-shrink-0">
                    @if($hasCritical)
                    <span class="inline-flex items-center gap-1 text-xs font-semibold bg-red-100 text-red-700 rounded-full px-2 py-0.5">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                        حرج ({{ $branch->leads_critical }})
                    </span>
                    @elseif($hasStale)
                    <span class="inline-flex items-center gap-1 text-xs font-semibold bg-amber-100 text-amber-700 rounded-full px-2 py-0.5">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        تأخير ({{ $branch->leads_stale }})
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1 text-xs font-medium bg-green-100 text-green-700 rounded-full px-2 py-0.5">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                        نشط
                    </span>
                    @endif
                </div>
            </div>

            {{-- Lead counts row --}}
            <div class="flex items-center gap-3 mt-3 text-xs">
                <div class="flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-blue-500 inline-block"></span>
                    <span class="text-slate-500">جديد:</span>
                    <span class="font-semibold text-slate-700">{{ $branch->leads_new }}</span>
                </div>
                <div class="flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-amber-400 inline-block"></span>
                    <span class="text-slate-500">متابعة:</span>
                    <span class="font-semibold text-slate-700">{{ $branch->leads_in_progress }}</span>
                </div>
                <div class="flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
                    <span class="text-slate-500">تحوّل:</span>
                    <span class="font-semibold text-slate-700">{{ $branch->leads_converted }}</span>
                </div>
                <div class="mr-auto font-bold text-slate-700">{{ $branch->leads_total }} إجمالي</div>
            </div>

            {{-- Mini progress bar --}}
            @if($branch->leads_total > 0)
            <div class="mt-2 flex h-1.5 rounded-full overflow-hidden bg-slate-100 gap-px">
                @if($pctNew > 0)
                <div class="bg-blue-500 transition-all" style="width:{{ $pctNew }}%"></div>
                @endif
                @if($pctProgress > 0)
                <div class="bg-amber-400 transition-all" style="width:{{ $pctProgress }}%"></div>
                @endif
                @if($pctConverted > 0)
                <div class="bg-green-500 transition-all" style="width:{{ $pctConverted }}%"></div>
                @endif
            </div>
            @endif
        </div>

        {{-- CS Staff Distribution --}}
        <div class="p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">موظفو خدمة العملاء</span>
                @if($branch->unassigned_leads > 0)
                <span class="text-xs text-orange-600 font-medium">
                    {{ $branch->unassigned_leads }} غير موزّع
                </span>
                @endif
            </div>

            @if($branch->cs_staff->isEmpty())
            <p class="text-xs text-slate-400 italic py-2 text-center">لا يوجد موظفون في هذا الفرع</p>
            @else
            <div class="space-y-2">
                @foreach($branch->cs_staff as $staff)
                @php
                    $maxLoad = max($branch->cs_staff->max('active_leads'), 1);
                    $loadPct = min(round($staff->active_leads / $maxLoad * 100), 100);
                    $loadColor = $loadPct >= 80 ? 'bg-red-400' : ($loadPct >= 50 ? 'bg-amber-400' : 'bg-green-400');
                @endphp
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs flex-shrink-0">
                        {{ mb_substr($staff->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-0.5">
                            <span class="text-xs text-slate-700 truncate font-medium">{{ $staff->name }}</span>
                            <span class="text-xs text-slate-500 flex-shrink-0 mr-2">
                                {{ $staff->active_leads }} نشط
                                @if($staff->converted_leads > 0)
                                <span class="text-green-600">· {{ $staff->converted_leads }} ✓</span>
                                @endif
                            </span>
                        </div>
                        <div class="h-1 bg-slate-100 rounded-full overflow-hidden">
                            <div class="{{ $loadColor }} h-full rounded-full transition-all" style="width:{{ $loadPct }}%"></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Card Footer --}}
        <div class="px-4 pb-4 flex items-center gap-2">
            {{-- View leads for this branch --}}
            <a href="{{ route('admin.marketing.leads.index', ['branch_id' => $branch->id]) }}"
               class="flex-1 flex items-center justify-center gap-1.5 text-xs font-medium text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 rounded-xl py-2 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                </svg>
                عرض اللييدز
            </a>

            {{-- Auto-assign button (only if unassigned exist + CS staff exist) --}}
            @if($branch->unassigned_leads > 0 && $branch->cs_staff->isNotEmpty())
            <form method="POST"
                  action="{{ route('admin.marketing.leads-board.auto-assign', $branch) }}"
                  onsubmit="return confirm('توزيع {{ $branch->unassigned_leads }} عميل محتمل تلقائياً على موظفي الفرع؟')">
                @csrf
                <button type="submit"
                        class="flex items-center gap-1.5 text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl py-2 px-3 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M16 4h2a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2"/>
                        <path d="M15 2H9a1 1 0 00-1 1v2a1 1 0 001 1h6a1 1 0 001-1V3a1 1 0 00-1-1z"/>
                    </svg>
                    توزيع تلقائي
                </button>
            </form>
            @endif
        </div>
    </div>
    @endforeach
    </div>
</div>
@endforeach

@endif

{{-- ═══════════════ LEGEND ═══════════════ --}}
<div class="mt-6 flex flex-wrap gap-4 text-xs text-slate-500 bg-white border border-slate-100 rounded-2xl p-4">
    <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-blue-500 inline-block"></span>جديد = انتظر التواصل</div>
    <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-amber-400 inline-block"></span>متابعة = قيد المعالجة</div>
    <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-green-500 inline-block"></span>تحوّل = أصبح عميلاً فعلياً</div>
    <div class="flex items-center gap-2 mr-auto">
        <span class="inline-flex items-center gap-1 bg-amber-100 text-amber-700 rounded-full px-2 py-0.5 font-medium">تأخير</span>
        = لم يتم التواصل &gt; 24 ساعة
    </div>
    <div class="flex items-center gap-2">
        <span class="inline-flex items-center gap-1 bg-red-100 text-red-700 rounded-full px-2 py-0.5 font-medium">حرج</span>
        = لم يتم التواصل &gt; 4 أيام (يُرسل تحذير للإدارة العليا)
    </div>
</div>

@endsection
