@php
    // نفس منطق لوحة الإدارة: لغة غير مدعومة تُرجَع للافتراضية بدل قلب الاتجاه.
    $fallbackConf = [
        'dir' => 'rtl', 'font_stack' => ['Cairo', 'sans-serif'],
        'google_font' => 'Cairo:wght@300;400;500;600;700;800',
    ];
    $locales    = config('locales.supported') ?: ['ar' => $fallbackConf];
    $currentLoc = app()->getLocale();
    if (! isset($locales[$currentLoc])) {
        $currentLoc = config('locales.default', 'ar');
    }
    $locConf = $locales[$currentLoc] ?? $fallbackConf;
    $dir     = $locConf['dir'] ?? 'rtl';

    $me     = Auth::guard('admin')->user();
    $canMan = $me->isSuperAdmin() || in_array($me->department, ['branch_manager', 'chairman'], true);

    $unreadCount = \App\Http\Controllers\CvPanel\NotificationController::scope($me)
        ->whereNull('read_at')->count();

    $nav = [
        ['route' => 'cv-panel.dashboard', 'label' => __('cv-panel.dashboard'),
         'icon'  => 'M3 12l9-9 9 9M5 10v10h14V10'],
        ['route' => 'cv-panel.cvs.index', 'label' => __('cv-panel.cvs'),
         'icon'  => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        ['route' => 'cv-panel.upload', 'label' => __('cv-panel.upload.title'),
         'icon'  => 'M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12'],
    ];

    if ($canMan) {
        $nav[] = ['route' => 'cv-panel.coordinators.index', 'label' => __('cv-panel.coordinators_page.nav'),
                  'icon'  => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'];
    }
@endphp
<!DOCTYPE html>
<html lang="{{ $currentLoc }}" dir="{{ $dir }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('cv-panel.title')) - {{ __('common.system') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: {
                colors: {
                    primary: { DEFAULT: '#c9a84c', light: '#fdf8e8', dark: '#a88830' },
                    navy:    { DEFAULT: '#0f2547', dark: '#081729', light: '#1c3b6e' },
                    ink:     { DEFAULT: '#0f172a', muted: '#64748b' },
                },
                fontFamily: { sans: @json($locConf['font_stack']) },
            }}
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family={{ $locConf['google_font'] }}&display=swap" rel="stylesheet">
    <style>
        [x-cloak]{display:none!important}
        /* شريط تمرير القائمة العلوية مخفي — التمرير باللمس على الجوال */
        .no-scrollbar::-webkit-scrollbar{display:none}
        .no-scrollbar{-ms-overflow-style:none;scrollbar-width:none}
    </style>
    @stack('styles')
</head>
<body class="bg-slate-100 font-sans text-ink antialiased min-h-screen flex flex-col">

<header class="bg-navy text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="flex items-center justify-between h-16">
            <a href="{{ route('cv-panel.dashboard') }}" class="flex items-center gap-2.5 font-extrabold">
                <span class="w-9 h-9 rounded-xl bg-primary/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </span>
                <span class="text-sm sm:text-base">{{ __('cv-panel.title') }}</span>
            </a>

            <div class="flex items-center gap-2 sm:gap-3 text-xs">
                <span class="hidden sm:inline text-white/70 max-w-[10rem] truncate">{{ $me->name }}</span>

                {{-- الإشعارات داخل اللوحة — لا تعتمد على شريط لوحة الإدارة --}}
                <a href="{{ route('cv-panel.notifications.index') }}"
                   class="relative w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors"
                   title="{{ __('cv-panel.notifications.title') }}" aria-label="{{ __('cv-panel.notifications.title') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
                    @if($unreadCount > 0)
                    <span class="absolute -top-1 -end-1 min-w-[1.05rem] h-[1.05rem] px-1 rounded-full bg-red-500 text-white
                                 text-[10px] font-bold flex items-center justify-center">
                        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                    </span>
                    @endif
                </a>
                <form method="POST" action="{{ route('cv-panel.logout') }}">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 font-bold transition-colors whitespace-nowrap">
                        {{ __('cv-panel.logout') }}
                    </button>
                </form>
            </div>
        </div>

        <nav class="flex items-center gap-1 overflow-x-auto -mb-px no-scrollbar">
            @foreach($nav as $item)
                @php
                    $isActive = request()->routeIs($item['route']);
                @endphp
                <a href="{{ route($item['route']) }}"
                   class="inline-flex items-center gap-2 px-3 sm:px-4 py-3 text-xs sm:text-sm font-bold whitespace-nowrap border-b-2 transition-colors
                          {{ $isActive ? 'border-primary text-primary' : 'border-transparent text-white/70 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/></svg>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>
    </div>
</header>

<main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 py-6 sm:py-8">
    @if(session('success'))
    <div class="mb-5 rounded-xl bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm font-semibold">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mb-5 rounded-xl bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm font-semibold">
        {{ session('error') }}
    </div>
    @endif

    @if($errors->any())
    <div class="mb-5 rounded-xl bg-red-50 border border-red-200 px-4 py-3">
        <ul class="text-sm text-red-800 space-y-1 list-disc list-inside">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @yield('content')
</main>

@stack('scripts')
</body>
</html>
