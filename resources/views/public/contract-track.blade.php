<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تتبع عقد الاستقدام</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Cairo', sans-serif; }
        .step-connector { flex: 1; height: 2px; background: #e2e8f0; }
        .step-connector.done { background: #22c55e; }
        .step-connector.active { background: linear-gradient(to left, #e2e8f0, #3b82f6); }
        @keyframes pulse-ring { 0%{transform:scale(1);opacity:.8} 70%{transform:scale(1.4);opacity:0} 100%{transform:scale(1.4);opacity:0} }
        .pulse-ring::before { content:''; position:absolute; inset:-6px; border-radius:50%; border:2px solid #3b82f6; animation:pulse-ring 1.5s ease-out infinite; }
        .card-hover { transition: transform .2s, box-shadow .2s; }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,.08); }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 to-blue-50/30 min-h-screen">

@if(!$contract)
{{-- ══════════ NO CONTRACT STATE ══════════ --}}
<div class="min-h-screen flex flex-col items-center justify-center px-4">
    <div class="w-20 h-20 bg-blue-600 rounded-3xl flex items-center justify-center mb-6 shadow-xl shadow-blue-200">
        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/>
        </svg>
    </div>
    <h1 class="text-3xl font-bold text-slate-800 mb-2">تتبع عقد الاستقدام</h1>
    <p class="text-slate-400 mb-8">أدخل رقم عقد مساند في الرابط لمتابعة حالة طلبك</p>
    @if($musanedNum)
    <div class="bg-red-50 border border-red-200 text-red-600 rounded-2xl px-6 py-4 text-sm text-center max-w-sm">
        <svg class="w-5 h-5 inline-block ml-1 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        لم يُعثر على عقد بالرقم <strong>{{ $musanedNum }}</strong>
    </div>
    @else
    <div class="bg-amber-50 border border-amber-200 text-amber-700 rounded-2xl px-6 py-4 text-sm text-center max-w-sm">
        أضف <code class="bg-amber-100 px-1 rounded">?musaned_number=XXXXX</code> إلى الرابط
    </div>
    @endif
    <p class="text-xs text-slate-400 mt-10">الامتياز للاستقدام &copy; {{ date('Y') }}</p>
</div>
@else
{{-- ══════════ CONTRACT FOUND ══════════ --}}

@php
    $pipeline = [1,2,3,4,5,6,7,8,10,11,12,13]; // main flow steps
    $current  = $contract->current_status;
    $isComplete = $current === 13;
    $isCancelled = in_array($current, [9,15]);
    $isReturn   = $current === 14;
    $lastLog    = $contract->activityLogs->first();
    $latestDone = $contract->statusHistories->sortByDesc('status_date')->first();
    $stepsIcons = [
        1  => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/>',
        2  => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        3  => '<path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>',
        4  => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        5  => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        6  => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>',
        7  => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>',
        8  => '<path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>',
        10 => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>',
        11 => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>',
        12 => '<path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/>',
        13 => '<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>',
    ];

    // Determine current pipeline step index
    $pipelinePos = array_search($current, $pipeline);
    if ($pipelinePos === false) $pipelinePos = 0;
@endphp

{{-- ── TOP NAV ── --}}
<header class="bg-white/80 backdrop-blur-sm border-b border-slate-100 sticky top-0 z-50">
    <div class="max-w-5xl mx-auto px-5 h-16 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center shadow">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-slate-400 leading-none">الامتياز للاستقدام</p>
                <p class="text-sm font-bold text-slate-700 leading-tight">ALEMTAYAZ RECRUITMENT</p>
            </div>
        </div>
        <div class="text-left">
            <p class="text-xs text-slate-400">تتبع عقد الاستقدام</p>
            <p class="text-sm font-bold text-blue-600">نحن نعمل على طلبك خطوة بخطوة</p>
        </div>
    </div>
</header>

<div class="max-w-5xl mx-auto px-4 py-8 space-y-5">

    {{-- ── HERO STATUS BANNER ── --}}
    @if($isComplete)
    <div class="rounded-2xl bg-gradient-to-r from-green-600 to-emerald-500 text-white p-7 shadow-lg shadow-green-200 flex items-center gap-5">
    @elseif($isCancelled)
    <div class="rounded-2xl bg-gradient-to-r from-red-600 to-rose-500 text-white p-7 shadow-lg shadow-red-200 flex items-center gap-5">
    @elseif($isReturn)
    <div class="rounded-2xl bg-gradient-to-r from-orange-500 to-amber-500 text-white p-7 shadow-lg shadow-orange-200 flex items-center gap-5">
    @else
    <div class="rounded-2xl bg-gradient-to-r from-blue-600 to-blue-700 text-white p-7 shadow-lg shadow-blue-200 flex items-center gap-5">
    @endif
        <div class="flex-1">
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2.5 h-2.5 rounded-full {{ $isComplete ? 'bg-green-200' : ($isCancelled||$isReturn ? 'bg-red-200' : 'bg-blue-200') }} animate-pulse inline-block"></span>
                <span class="text-sm font-medium opacity-90">
                    @if($isComplete) تم الاستلام بنجاح
                    @elseif($isCancelled) {{ $contract->status_label }}
                    @elseif($isReturn) رجيع خلال فترة الضمان
                    @else طلبك قيد المعالجة
                    @endif
                </span>
            </div>
            <h2 class="text-2xl font-bold mb-1">{{ $contract->status_label }}</h2>
            @if(!$isComplete && !$isCancelled && !$isReturn)
            <p class="text-sm opacity-80">فريقنا يعمل حالياً على إنهاء الإجراءات الخاصة بطلبك.<br>سنقوم بإعلامك فور حدوث أي تحديث.</p>
            @if(isset($statuses[$current]['days']) && $statuses[$current]['days'])
            <div class="mt-3 inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm rounded-full px-4 py-1.5 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                تقدير الوقت: {{ $statuses[$current]['days'] }} - {{ $statuses[$current]['days'] + 3 }} عمل
            </div>
            @endif
            @endif
        </div>
        <div class="hidden sm:flex w-28 h-28 shrink-0 items-center justify-center">
            @if($isComplete)
            <svg class="w-24 h-24 text-white/30" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            @else
            <svg class="w-24 h-24 text-white/20" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/></svg>
            @endif
        </div>
    </div>

    {{-- ── DATA GRID ── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

        {{-- Contract Data --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 card-hover">
            <div class="flex items-center gap-2 mb-5">
                <div class="w-7 h-7 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-slate-700">بيانات العقد</h3>
            </div>
            <dl class="space-y-3.5">
                <div class="flex justify-between items-center text-sm">
                    <dt class="text-slate-400 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                        رقم العقد
                    </dt>
                    <dd class="font-mono font-bold text-slate-800 text-xs bg-slate-50 px-2 py-1 rounded-lg">{{ $contract->contract_number }}</dd>
                </div>
                <div class="flex justify-between items-center text-sm border-t border-slate-50 pt-3">
                    <dt class="text-slate-400 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        تاريخ الطلب
                    </dt>
                    <dd class="font-medium text-slate-700">{{ $contract->created_at->format('Y/m/d') }}</dd>
                </div>
                <div class="flex justify-between items-center text-sm border-t border-slate-50 pt-3">
                    <dt class="text-slate-400 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        العميل
                    </dt>
                    <dd class="font-medium text-slate-700">{{ $contract->client?->name ?? '—' }}</dd>
                </div>
                <div class="flex justify-between items-center text-sm border-t border-slate-50 pt-3">
                    <dt class="text-slate-400 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                        الفرع
                    </dt>
                    <dd class="font-medium text-slate-700">{{ $contract->branch?->name ?? '—' }}</dd>
                </div>
                <div class="flex justify-between items-center text-sm border-t border-slate-50 pt-3">
                    <dt class="text-slate-400 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        أنشئ بواسطة
                    </dt>
                    <dd class="font-medium text-slate-700">{{ $contract->admin?->name ?? '—' }}</dd>
                </div>
            </dl>
        </div>

        {{-- Visa Data --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 card-hover">
            <div class="flex items-center gap-2 mb-5">
                <div class="w-7 h-7 bg-emerald-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-slate-700">بيانات التأشيرة</h3>
            </div>
            <dl class="space-y-3.5">
                <div class="flex justify-between items-center text-sm">
                    <dt class="text-slate-400">نوع التأشيرة <span class="text-red-400">*</span></dt>
                    <dd class="font-medium text-slate-700">{{ $contract->visa_type_label ?? '—' }}</dd>
                </div>
                <div class="flex justify-between items-center text-sm border-t border-slate-50 pt-3">
                    <dt class="text-slate-400">رقم التأشيرة</dt>
                    <dd class="font-mono text-slate-700">{{ $contract->visa_number ?? '—' }}</dd>
                </div>
                @if($contract->musaned_number)
                <div class="flex justify-between items-center text-sm border-t border-slate-50 pt-3">
                    <dt class="text-slate-400">رقم مساند</dt>
                    <dd class="font-mono text-slate-700">{{ $contract->musaned_number }}</dd>
                </div>
                @endif
                @if($contract->arrivalAirport)
                <div class="flex justify-between items-center text-sm border-t border-slate-50 pt-3">
                    <dt class="text-slate-400 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2"/></svg>
                        محطة التميز
                    </dt>
                    <dd class="font-medium text-slate-700 text-left">{{ $contract->arrivalAirport->name }}</dd>
                </div>
                @endif
                @if($contract->deliveryCity)
                <div class="flex justify-between items-center text-sm border-t border-slate-50 pt-3">
                    <dt class="text-slate-400 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                        محطة الاستلام
                    </dt>
                    <dd class="font-medium text-slate-700">{{ $contract->deliveryCity->name }}</dd>
                </div>
                @endif
                @if($contract->visa_image)
                <div class="border-t border-slate-50 pt-3">
                    <a href="{{ file_url($contract->visa_image) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 text-sm text-blue-600 hover:text-blue-700 font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        عرض الملف
                    </a>
                </div>
                @endif
            </dl>
        </div>
    </div>

    {{-- ── STEP PROGRESS ── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <h3 class="font-bold text-slate-700 mb-6 flex items-center gap-2">
            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            مراحل العقد
        </h3>

        {{-- Horizontal Stepper --}}
        <div class="overflow-x-auto pb-2">
            <div class="flex items-start min-w-max mx-auto px-2" style="gap:0">
                @foreach($pipeline as $i => $stepNum)
                @php
                    $stepPos      = $i;
                    $isDone       = $stepPos < $pipelinePos;
                    $isCurStep    = $stepPos === $pipelinePos;
                    $isFuture     = $stepPos > $pipelinePos;
                    $h            = $historyMap->get($stepNum);
                    $stepDate     = $h ? optional($h->status_date)->format('Y/m/d') : null;
                    $shortLabel   = mb_substr($statuses[$stepNum]['label'] ?? '', 0, 10);
                @endphp

                {{-- Step circle --}}
                <div class="flex flex-col items-center" style="min-width:64px">
                    <div class="relative flex items-center justify-center
                        @if($isDone) w-9 h-9 rounded-full bg-green-500 text-white shadow-md shadow-green-200
                        @elseif($isCurStep) w-10 h-10 rounded-full bg-blue-600 text-white shadow-lg shadow-blue-300 pulse-ring
                        @else w-9 h-9 rounded-full bg-slate-100 text-slate-400
                        @endif">
                        @if($isDone)
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        @else
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $stepsIcons[$stepNum] ?? '' !!}</svg>
                        @endif
                    </div>
                    <p class="text-center mt-2 leading-tight" style="max-width:60px; font-size:10px; color:{{ $isDone ? '#16a34a' : ($isCurStep ? '#2563eb' : '#94a3b8') }}; font-weight:{{ $isCurStep ? '700' : '400' }}">{{ $shortLabel }}</p>
                    @if($stepDate)
                    <p class="text-center mt-0.5 text-slate-400" style="font-size:9px">{{ $stepDate }}</p>
                    @elseif($isCurStep)
                    <p class="text-center mt-0.5 text-blue-400" style="font-size:9px">الحالي</p>
                    @endif
                </div>

                {{-- Connector (not after last) --}}
                @if($i < count($pipeline) - 1)
                <div class="h-0.5 mt-4.5 flex-1" style="min-width:16px; background:{{ $isDone ? '#22c55e' : '#e2e8f0' }}; margin-top:18px"></div>
                @endif
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── STATUS LOG + NOTES ── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        {{-- Last update --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <h3 class="font-bold text-slate-700 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                آخر تحديث
            </h3>
            @if($lastLog)
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-700">{{ $lastLog->admin?->name ?? 'النظام' }}</p>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $lastLog->action ?? $contract->status_label }}</p>
                    <p class="text-xs text-slate-400 mt-1">{{ $lastLog->created_at->format('Y/m/d — H:i') }}</p>
                </div>
            </div>
            @elseif($latestDone)
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-700">تم تحديث الطلب وتحديد البيانات الأساسية</p>
                    <p class="text-xs text-slate-400 mt-1">{{ $latestDone->status_date?->format('Y/m/d') ?? $contract->updated_at->format('Y/m/d') }} — {{ $contract->updated_at->format('H:i') }} صباحاً</p>
                </div>
            </div>
            @else
            <p class="text-sm text-slate-400">لا توجد سجلات تحديث بعد</p>
            @endif
        </div>

        {{-- Notes --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <h3 class="font-bold text-slate-700 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                ملاحظات
            </h3>
            <div class="bg-slate-50 rounded-xl p-4">
                <p class="text-sm text-slate-600 leading-relaxed">
                    نشكركم على ثقتكم بنا. فريقنا يتابع طلبك وسنبقيك على جديد كل تحديث.
                </p>
            </div>
            @if($contract->worker)
            <div class="mt-3 flex items-center gap-2 text-sm">
                <div class="w-7 h-7 rounded-full bg-purple-100 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <span class="text-slate-500">العاملة:</span>
                <span class="font-medium text-slate-700">{{ $contract->worker->name }}</span>
            </div>
            @endif
        </div>
    </div>

    {{-- ── FEATURE FOOTER CARDS ── --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach([
            ['icon'=>'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z','color'=>'blue','label'=>'دعم العملاء','sub'=>'تواصل معنا في أي وقت'],
            ['icon'=>'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9','color'=>'amber','label'=>'تحديثات فورية','sub'=>'ستصلك إشعارات بكل جديد'],
            ['icon'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z','color'=>'green','label'=>'أمان بياناتك','sub'=>'بياناتك آمنة ومحمية لدينا'],
            ['icon'=>'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z','color'=>'purple','label'=>'خبرة موثوقة','sub'=>'سنوات خبرة في الاستقدام'],
        ] as $feat)
        @php $colors=['blue'=>['bg'=>'bg-blue-50','text'=>'text-blue-600'],'amber'=>['bg'=>'bg-amber-50','text'=>'text-amber-600'],'green'=>['bg'=>'bg-green-50','text'=>'text-green-600'],'purple'=>['bg'=>'bg-purple-50','text'=>'text-purple-600']]; $c=$colors[$feat['color']]; @endphp
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 text-center card-hover">
            <div class="w-10 h-10 {{ $c['bg'] }} rounded-xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-5 h-5 {{ $c['text'] }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $feat['icon'] }}"/>
                </svg>
            </div>
            <p class="text-sm font-bold text-slate-700">{{ $feat['label'] }}</p>
            <p class="text-xs text-slate-400 mt-0.5">{{ $feat['sub'] }}</p>
        </div>
        @endforeach
    </div>

    <p class="text-center text-xs text-slate-400 pb-4">الامتياز للاستقدام &copy; {{ date('Y') }} — رقم العقد {{ $contract->contract_number }}</p>

</div>
@endif

</body>
</html>
