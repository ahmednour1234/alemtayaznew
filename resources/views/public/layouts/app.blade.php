@php
    $fallbackConf = [
        'dir'         => 'rtl',
        'font_stack'  => ['Cairo', 'sans-serif'],
        'google_font' => 'Cairo:wght@300;400;500;600;700;800',
    ];
    $locales    = config('locales.supported') ?: ['ar' => $fallbackConf];
    $currentLoc = app()->getLocale();
    if (! isset($locales[$currentLoc])) {
        $currentLoc = config('locales.default', 'ar');
    }
    $locConf = $locales[$currentLoc] ?? $fallbackConf;
    $dir     = $locConf['dir'] ?? 'rtl';
    $font    = $locConf['google_font'] ?? $fallbackConf['google_font'];
    $stack   = implode(', ', array_map(fn($f) => str_contains($f, ' ') ? "'$f'" : $f, $locConf['font_stack'] ?? $fallbackConf['font_stack']));

    $S = fn(string $k) => \App\Models\SiteSetting::value($k);
@endphp
<!DOCTYPE html>
<html lang="{{ $currentLoc }}" dir="{{ $dir }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $S('company_name'))</title>
    <meta name="description" content="@yield('meta_description', $S('company_name') . ' — ' . $S('tagline'))">

    <link rel="icon" type="image/png" href="{{ asset('08_alemtyaz_logo_original.png') }}">

    {{-- Open Graph — تظهر عند مشاركة الرابط في واتساب وتويتر --}}
    <meta property="og:title" content="@yield('title', $S('company_name'))">
    <meta property="og:description" content="@yield('meta_description', $S('company_name') . ' — ' . $S('tagline'))">
    <meta property="og:image" content="{{ asset('09_hero_background.jpg') }}">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family={{ $font }}&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy:  { DEFAULT: '#1e3a6d', dark: '#16294d', light: '#2b4d8c' },
                        gold:  { DEFAULT: '#c9a84c', dark: '#ab8d38', light: '#e0c674' },
                    },
                    fontFamily: { sans: [{!! json_encode(explode(', ', str_replace("'", '', $stack))) !!}] },
                },
            },
        };
    </script>
    <style>
        body { font-family: {{ $stack }}; }
        .hero-grad { background: linear-gradient(135deg, #1e3a6d 0%, #2b4d8c 55%, #16294d 100%); }

        /* صورة الواجهة على الشاشات الكبيرة: ملتصقة بحافة الصفحة بلا حشو،
           تشغل نصف العرض بالكامل. في RTL تقع يميناً والنص يساراً،
           والقوس على حافتها الداخلية (جهة النص). كل الخصائص منطقية
           فتنعكس تلقائياً عند تبديل اتجاه الصفحة. */
        @media (min-width: 1024px) {
            .hero-photo {
                position: absolute;
                inset-block: 0;
                inset-inline-start: 0;
                width: 52%;
                z-index: 1;              /* فوق الخلفية الزخرفية، وتحت النص */
                overflow: hidden;
                border-start-end-radius: 16rem;
                border-end-end-radius: 16rem;
                border-inline-end: 5px solid #c9a84c;
            }
        }

        /* ── حركة دخول الواجهة ─────────────────────────────────────────
           عناصر النص تدخل متتابعة، والصورة تتكشّف مع تكبير خفيف.
           التأخير يُضبط عبر --d على كل عنصر. */
        @keyframes heroRise {
            from { opacity: 0; transform: translateY(22px); }
            to   { opacity: 1; transform: none; }
        }

        @keyframes heroReveal {
            from { opacity: 0; transform: scale(1.06); }
            to   { opacity: 1; transform: scale(1); }
        }

        @keyframes heroLine {
            from { transform: scaleX(0); }
            to   { transform: scaleX(1); }
        }

        .hero-rise {
            opacity: 0;
            animation: heroRise .7s cubic-bezier(.22,.61,.36,1) forwards;
            animation-delay: var(--d, 0s);
        }

        .hero-reveal {
            opacity: 0;
            animation: heroReveal 1s cubic-bezier(.22,.61,.36,1) forwards;
            animation-delay: var(--d, 0s);
        }

        .hero-line {
            transform-origin: right center;
            animation: heroLine .6s cubic-bezier(.22,.61,.36,1) forwards;
            animation-delay: var(--d, 0s);
        }
        [dir="ltr"] .hero-line { transform-origin: left center; }

        /* صورة الواجهة تكبر ببطء شديد بعد اكتمال ظهورها — إحساس بالحياة بلا إلهاء.
           التأخير يساوي زمن heroReveal حتى لا يتزاحم التحويلان. */
        .hero-photo img { animation: heroZoom 18s ease-out 1.1s forwards; }
        @keyframes heroZoom {
            from { transform: scale(1); }
            to   { transform: scale(1.07); }
        }

        /* احترام تفضيل تقليل الحركة في نظام المستخدم */
        @media (prefers-reduced-motion: reduce) {
            .hero-rise, .hero-reveal, .hero-line, .hero-photo img {
                animation: none !important;
                opacity: 1 !important;
                transform: none !important;
            }
        }

        [x-cloak] { display: none !important; }
    </style>
    @stack('head')
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

@include('public.partials.header')

<main>
    @yield('content')
</main>

@include('public.partials.footer')

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@stack('scripts')
</body>
</html>
