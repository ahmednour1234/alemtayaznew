@extends('admin.layouts.app')
@section('title', 'لوحة الفروع — العملاء المحتملون')

@push('styles')
<style>
    .card-glow-red    { box-shadow: 0 0 0 1px #fca5a5, 0 4px 24px rgba(239,68,68,.12); }
    .card-glow-amber  { box-shadow: 0 0 0 1px #fcd34d, 0 4px 24px rgba(245,158,11,.10); }
    .card-glow-normal { box-shadow: 0 2px 12px rgba(99,102,241,.06); }
    .stat-card { transition: transform .18s, box-shadow .18s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(99,102,241,.13); }
    .branch-card { transition: transform .2s, box-shadow .2s; }
    .branch-card:hover { transform: translateY(-3px); }
    .ring-svg { transform: rotate(-90deg); }
    .btn-assign { background: linear-gradient(135deg,#6366f1,#4f46e5); }
    .btn-assign:hover { background: linear-gradient(135deg,#4f46e5,#4338ca); }
</style>
@endpush

@section('content')

{{-- ══════════════════════════════════════════
     HERO HEADER
══════════════════════════════════════════ --}}
<div class="relative rounded-3xl overflow-hidden mb-7"
     style="background:linear-gradient(135deg,#1e1b4b 0%,#312e81 40%,#4f46e5 75%,#6366f1 100%)">
    <div class="absolute -top-10 -left-10 w-56 h-56 rounded-full opacity-10"
         style="background:radial-gradient(circle,#a5b4fc,transparent)"></div>
    <div class="absolute bottom-0 right-0 w-40 h-40 rounded-full opacity-10"
         style="background:radial-gradient(circle,#c7d2fe,transparent)"></div>
    <div class="relative flex items-center justify-between px-7 py-6 flex-wrap gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center"
                 style="background:rgba(255,255,255,.15);backdrop-filter:blur(8px)">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                    <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                    <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                    <rect x="14" y="14" width="7" height="7" rx="1.5"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold text-white">لوحة الفروع — العملاء المحتملون</h1>
                <p class="text-indigo-200 text-sm mt-0.5">توزيع الموظفين · تنبيهات التأخير · التحويل المباشر</p>
            </div>
        </div>
        <a href="{{ route('admin.marketing.leads.index') }}"
           class="flex items-center gap-2 text-sm font-medium text-white rounded-xl px-5 py-2.5 transition"
           style="background:rgba(255,255,255,.18);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.25)">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M9 5l7 7-7 7"/>
            </svg>
            قائمة العملاء المحتملين
        </a>
    </div>
</div>

{{-- ══════════════════════════════════════════
     SUMMARY STAT CARDS
══════════════════════════════════════════ --}}
@php
$stats = [
    ['label'=>'إجمالي العملاء','value'=>$summary['total'],    'top'=>'linear-gradient(90deg,#6366f1,#818cf8)','numcls'=>'text-indigo-700','bgcls'=>'bg-indigo-50','iconcls'=>'text-indigo-400','icon'=>'<path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>'],
    ['label'=>'جديد',           'value'=>$summary['new'],      'top'=>'linear-gradient(90deg,#3b82f6,#60a5fa)','numcls'=>'text-blue-700',  'bgcls'=>'bg-blue-50',  'iconcls'=>'text-blue-400',  'icon'=>'<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>'],
    ['label'=>'قيد المتابعة',  'value'=>$summary['progress'], 'top'=>'linear-gradient(90deg,#f59e0b,#fbbf24)','numcls'=>'text-amber-700', 'bgcls'=>'bg-amber-50', 'iconcls'=>'text-amber-400', 'icon'=>'<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>'],
    ['label'=>'تم التحويل',     'value'=>$summary['converted'],'top'=>'linear-gradient(90deg,#10b981,#34d399)','numcls'=>'text-emerald-700','bgcls'=>'bg-emerald-50','iconcls'=>'text-emerald-400','icon'=>'<path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>'],
    ['label'=>'تأخير 24 ساعة', 'value'=>$summary['stale'],   'top'=>'linear-gradient(90deg,#f97316,#fb923c)','numcls'=>'text-orange-700','bgcls'=>'bg-orange-50','iconcls'=>'text-orange-400','icon'=>'<path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>'],
    ['label'=>'حرج 4 أيام+',   'value'=>$summary['critical'], 'top'=>'linear-gradient(90deg,#ef4444,#f87171)','numcls'=>'text-red-700',   'bgcls'=>'bg-red-50',   'iconcls'=>'text-red-400',   'icon'=>'<polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>'],
];
@endphp
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-7">
@foreach($stats as $s)
<div class="stat-card bg-white rounded-2xl overflow-hidden border border-slate-100">
    <div class="h-1.5 w-full" style="background:{{ $s['top'] }}"></div>
    <div class="p-4">
        <div class="w-9 h-9 rounded-xl {{ $s['bgcls'] }} flex items-center justify-center mb-3">
            <svg class="{{ $s['iconcls'] }}" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $s['icon'] !!}</svg>
        </div>
        <p class="text-2xl font-extrabold {{ $s['numcls'] }} leading-none">{{ number_format($s['value']) }}</p>
        <p class="text-xs text-slate-400 mt-1 font-medium">{{ $s['label'] }}</p>
    </div>
</div>
@endforeach
</div>

{{-- Flash messages --}}
@if(session('success'))
<div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl px-5 py-3.5 text-sm mb-5 shadow-sm">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <span class="font-medium">{{ session('success') }}</span>
</div>
@endif
@if(session('error'))
<div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 rounded-2xl px-5 py-3.5 text-sm mb-5 shadow-sm">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
    <span class="font-medium">{{ session('error') }}</span>
</div>
@endif

{{-- ══════════════════════════════════════════
     BRANCHES — CITY GROUPS
══════════════════════════════════════════ --}}
@if($branches->isEmpty())
<div class="text-center py-20 text-slate-400">
    <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
        <svg class="w-9 h-9 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <rect x="2" y="7" width="20" height="14" rx="2"/>
            <path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/>
        </svg>
    </div>
    <p class="font-semibold text-slate-500">لا توجد فروع نشطة</p>
</div>
@else

@php $byCity = $branches->groupBy(fn($b) => $b->city ?: 'غير محدد'); @endphp

@foreach($byCity as $city => $cityBranches)
{{-- City header --}}
<div class="flex items-center gap-3 mb-5 {{ !$loop->first ? 'mt-10' : '' }}">
    <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
         style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
        <svg width="18" height="18" class="text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
    </div>
    <h3 class="text-lg font-bold text-slate-800">{{ $city }}</h3>
    <span class="text-xs font-bold text-indigo-600 bg-indigo-50 border border-indigo-100 rounded-full px-3 py-0.5">
        {{ $cityBranches->count() }} {{ $cityBranches->count() === 1 ? 'فرع' : 'فروع' }}
    </span>
    <div class="flex-1 h-px bg-gradient-to-l from-transparent to-slate-200"></div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 mb-2">
@foreach($cityBranches as $branch)
@php
    $hasCritical = $branch->leads_critical > 0;
    $hasStale    = $branch->leads_stale > 0;
    $accentColor = $hasCritical ? '#ef4444' : ($hasStale ? '#f59e0b' : '#6366f1');
    $glowClass   = $hasCritical ? 'card-glow-red' : ($hasStale ? 'card-glow-amber' : 'card-glow-normal');
    $total       = max($branch->leads_total, 1);
    $pctNew      = round($branch->leads_new / $total * 100);
    $pctProgress = round($branch->leads_in_progress / $total * 100);
    $pctConverted= round($branch->leads_converted / $total * 100);

    // SVG donut (r=28, circumference≈175.9)
    $circ = 2 * 3.14159 * 28;
    $dN = round($circ * $pctNew / 100, 1);
    $dP = round($circ * $pctProgress / 100, 1);
    $dC = round($circ * $pctConverted / 100, 1);
    $oN = 0; $oP = $dN; $oC = $dN + $dP;
@endphp
<div class="branch-card bg-white rounded-3xl overflow-hidden {{ $glowClass }}">
    {{-- Accent top --}}
    <div class="h-1.5" style="background:{{ $accentColor }}"></div>

    {{-- Header --}}
    <div class="px-5 pt-5 pb-4 border-b border-slate-100">
        <div class="flex items-start justify-between gap-2 mb-4">
            <div class="flex-1 min-w-0">
                <h4 class="font-bold text-slate-800 text-base leading-snug truncate">{{ $branch->name }}</h4>
                @if($branch->code)
                <span class="text-xs text-slate-400 font-mono">{{ $branch->code }}</span>
                @endif
            </div>
            @if($hasCritical)
            <span class="inline-flex items-center gap-1 text-xs font-bold bg-red-100 text-red-700 rounded-full px-2.5 py-1 border border-red-200 flex-shrink-0">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                حرج ({{ $branch->leads_critical }})
            </span>
            @elseif($hasStale)
            <span class="inline-flex items-center gap-1 text-xs font-bold bg-amber-100 text-amber-700 rounded-full px-2.5 py-1 border border-amber-200 flex-shrink-0">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
                تأخير ({{ $branch->leads_stale }})
            </span>
            @else
            <span class="inline-flex items-center gap-1.5 text-xs font-semibold bg-emerald-100 text-emerald-700 rounded-full px-2.5 py-1 border border-emerald-200 flex-shrink-0">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block animate-pulse"></span>
                نشط
            </span>
            @endif
        </div>

        {{-- Donut + stat boxes --}}
        <div class="flex items-center gap-4">
            {{-- Donut --}}
            <div class="relative flex-shrink-0 w-[68px] h-[68px]">
                <svg class="ring-svg w-full h-full" viewBox="0 0 72 72">
                    <circle cx="36" cy="36" r="28" fill="none" stroke="#f1f5f9" stroke-width="9"/>
                    @if($branch->leads_total > 0)
                    @if($dN > 0)
                    <circle cx="36" cy="36" r="28" fill="none" stroke="#3b82f6" stroke-width="9"
                            stroke-dasharray="{{ $dN }} {{ $circ - $dN }}" stroke-dashoffset="{{ -$oN }}"/>
                    @endif
                    @if($dP > 0)
                    <circle cx="36" cy="36" r="28" fill="none" stroke="#f59e0b" stroke-width="9"
                            stroke-dasharray="{{ $dP }} {{ $circ - $dP }}" stroke-dashoffset="{{ -$oP }}"/>
                    @endif
                    @if($dC > 0)
                    <circle cx="36" cy="36" r="28" fill="none" stroke="#10b981" stroke-width="9"
                            stroke-dasharray="{{ $dC }} {{ $circ - $dC }}" stroke-dashoffset="{{ -$oC }}"/>
                    @endif
                    @endif
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                    <span class="text-sm font-extrabold text-slate-700 leading-none">{{ $branch->leads_total }}</span>
                    <span class="text-[9px] text-slate-400 mt-0.5">إجمالي</span>
                </div>
            </div>

            {{-- 3 stat boxes --}}
            <div class="flex-1 grid grid-cols-3 gap-2">
                <div class="bg-blue-50 rounded-xl py-2.5 px-1 text-center border border-blue-100">
                    <p class="text-xl font-extrabold text-blue-600 leading-none">{{ $branch->leads_new }}</p>
                    <p class="text-[10px] text-blue-400 mt-0.5 font-semibold">جديد</p>
                </div>
                <div class="bg-amber-50 rounded-xl py-2.5 px-1 text-center border border-amber-100">
                    <p class="text-xl font-extrabold text-amber-600 leading-none">{{ $branch->leads_in_progress }}</p>
                    <p class="text-[10px] text-amber-400 mt-0.5 font-semibold">متابعة</p>
                </div>
                <div class="bg-emerald-50 rounded-xl py-2.5 px-1 text-center border border-emerald-100">
                    <p class="text-xl font-extrabold text-emerald-600 leading-none">{{ $branch->leads_converted }}</p>
                    <p class="text-[10px] text-emerald-400 mt-0.5 font-semibold">تحوّل</p>
                </div>
            </div>
        </div>
    </div>

    {{-- CS Staff --}}
    <div class="px-5 py-4">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-1.5">
                <svg width="14" height="14" class="text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 7a4 4 0 100 8 4 4 0 000-8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                </svg>
                <span class="text-xs font-bold text-slate-500 tracking-wide">موظفو خدمة العملاء</span>
            </div>
            @if($branch->unassigned_leads > 0)
            <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-orange-100 text-orange-600 rounded-full px-2 py-0.5 border border-orange-200">
                <span class="w-1.5 h-1.5 rounded-full bg-orange-500 inline-block animate-pulse"></span>
                {{ $branch->unassigned_leads }} غير موزّع
            </span>
            @endif
        </div>

        @if($branch->cs_staff->isEmpty())
        <div class="text-center py-5">
            <svg class="w-8 h-8 text-slate-200 mx-auto mb-1" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
            </svg>
            <p class="text-xs text-slate-400 font-medium">لا يوجد موظفون مسجّلون</p>
        </div>
        @else
        <div class="space-y-3">
            @foreach($branch->cs_staff as $staff)
            @php
                $maxLoad  = max($branch->cs_staff->max('active_leads'), 1);
                $loadPct  = min(round($staff->active_leads / $maxLoad * 100), 100);
                $barColor = $loadPct >= 80 ? '#ef4444' : ($loadPct >= 50 ? '#f59e0b' : '#10b981');
                $palette  = ['#6366f1','#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#14b8a6'];
                $avatarBg = $palette[$loop->index % count($palette)];
            @endphp
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                     style="background:{{ $avatarBg }}">
                    {{ mb_substr($staff->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-semibold text-slate-700 truncate">{{ $staff->name }}</span>
                        <div class="flex items-center gap-1 flex-shrink-0 mr-2 text-[10px] font-bold">
                            <span class="text-slate-500">{{ $staff->active_leads }} نشط</span>
                            @if($staff->converted_leads > 0)
                            <span class="text-emerald-600">· {{ $staff->converted_leads }} ✓</span>
                            @endif
                        </div>
                    </div>
                    <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500"
                             style="width:{{ $loadPct }}%;background:{{ $barColor }}"></div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Footer buttons --}}
    <div class="px-5 pb-5 flex items-center gap-2">
        <a href="{{ route('admin.marketing.leads.index', ['branch_id' => $branch->id]) }}"
           class="flex-1 flex items-center justify-center gap-1.5 text-xs font-semibold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 rounded-xl py-2.5 transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
            </svg>
            عرض الليدز
        </a>
        @if($branch->unassigned_leads > 0 && $branch->cs_staff->isNotEmpty())
        <form method="POST"
              action="{{ route('admin.marketing.leads-board.auto-assign', $branch) }}"
              onsubmit="return confirm('توزيع {{ $branch->unassigned_leads }} عميل تلقائياً على موظفي الفرع؟')">
            @csrf
            <button type="submit"
                    class="btn-assign flex items-center gap-1.5 text-xs font-bold text-white rounded-xl py-2.5 px-4 transition shadow-md shadow-indigo-200">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M12 5v14M5 12l7-7 7 7"/>
                </svg>
                توزيع تلقائي
            </button>
        </form>
        @endif
    </div>
</div>
@endforeach
</div>
@endforeach
@endif

{{-- Legend --}}
<div class="mt-8 bg-white border border-slate-100 rounded-2xl px-6 py-4 shadow-sm">
    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">دليل الألوان</p>
    <div class="flex flex-wrap gap-5 text-xs text-slate-500">
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-blue-500 inline-block flex-shrink-0"></span>
            <span><strong class="text-slate-600">جديد</strong> — بانتظار التواصل</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-amber-400 inline-block flex-shrink-0"></span>
            <span><strong class="text-slate-600">متابعة</strong> — قيد المعالجة</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block flex-shrink-0"></span>
            <span><strong class="text-slate-600">تحوّل</strong> — أصبح عميلاً فعلياً</span>
        </div>
        <div class="flex items-center gap-2 mr-auto">
            <span class="inline-flex items-center gap-1 bg-amber-100 text-amber-700 border border-amber-200 rounded-full px-2.5 py-0.5 font-bold">تأخير</span>
            <span>لم يُتواصل مع العميل &gt; 24 ساعة (تنبيه لمدير الفرع)</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1 bg-red-100 text-red-700 border border-red-200 rounded-full px-2.5 py-0.5 font-bold">حرج</span>
            <span>لم يُتواصل &gt; 4 أيام (تحذير عاجل للإدارة العليا)</span>
        </div>
    </div>
</div>

@endsection
