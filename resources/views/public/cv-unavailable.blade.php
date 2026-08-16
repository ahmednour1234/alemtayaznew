@php
    // reason: 'reserved' = حُجزت/عُيّنت لعميل آخر | 'unavailable' = محذوفة أو الملف مفقود
    $isReserved = ($reason ?? 'unavailable') === 'reserved';
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $isReserved ? 'العاملة محجوزة' : 'الملف غير متاح' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>* { font-family: 'Cairo', sans-serif; }</style>
</head>
<body class="bg-gradient-to-br from-slate-50 to-blue-50/30 min-h-screen flex items-center justify-center p-5">

    <div class="bg-white rounded-2xl shadow-lg border border-slate-100 max-w-md w-full p-8 text-center">

        {{-- أيقونة --}}
        <div class="w-20 h-20 mx-auto mb-5 rounded-full flex items-center justify-center
                    {{ $isReserved ? 'bg-amber-50' : 'bg-slate-100' }}">
            @if($isReserved)
                <svg class="w-10 h-10 text-amber-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            @else
                <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    <line x1="4" y1="20" x2="20" y2="4" stroke-linecap="round"/>
                </svg>
            @endif
        </div>

        {{-- العنوان --}}
        <h1 class="text-xl font-bold text-slate-800 mb-2">
            {{ $isReserved ? 'تم حجز هذه العاملة' : 'هذا الـ CV أصبح غير متاح حالياً' }}
        </h1>

        {{-- الشرح --}}
        <p class="text-sm text-slate-500 leading-relaxed mb-6">
            @if($isReserved)
                عذراً، تم حجز هذه العاملة لعميل آخر ولم تعد متاحة.
                يسعدنا مساعدتك في اختيار عاملة أخرى مناسبة.
            @else
                عذراً، لم يعد ملف السيرة الذاتية لهذه العاملة متاحاً للعرض.
                قد تكون البيانات حُدِّثت أو أُزيلت من النظام.
            @endif
        </p>

        {{-- اسم العاملة إن توفر --}}
        @if(!empty($worker?->name))
        <div class="bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 mb-6">
            <p class="text-xs text-slate-400 mb-0.5">العاملة</p>
            <p class="text-sm font-semibold text-slate-700">{{ $worker->name }}</p>
        </div>
        @endif

        {{-- تواصل --}}
        <p class="text-xs text-slate-400">
            للاستفسار يرجى التواصل مع خدمة العملاء
        </p>

        <div class="mt-6 pt-5 border-t border-slate-100">
            <p class="text-xs font-semibold text-slate-400">شركة الامتياز للاستقدام</p>
        </div>
    </div>

</body>
</html>
