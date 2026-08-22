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
    <meta property="og:image" content="{{ asset('01_hero_family_worker.jpg') }}">
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

        /* تعتيم صورة الواجهة: داكن كلياً على الجوال ليبقى النص مقروءاً،
           ومتدرّج على الشاشات الكبيرة فتظهر الصورة من الجهة المقابلة للنص. */
        .hero-overlay { background: linear-gradient(to bottom, rgba(22,41,77,.82), rgba(22,41,77,.78)); }

        @media (min-width: 1024px) {
            /* RTL: النص يمين ← التعتيم يبدأ من اليمين */
            [dir="rtl"] .hero-overlay {
                background: linear-gradient(to left, rgba(22,41,77,.94) 0%, rgba(22,41,77,.86) 35%, rgba(22,41,77,.45) 65%, rgba(22,41,77,.15) 100%);
            }
            [dir="ltr"] .hero-overlay {
                background: linear-gradient(to right, rgba(22,41,77,.94) 0%, rgba(22,41,77,.86) 35%, rgba(22,41,77,.45) 65%, rgba(22,41,77,.15) 100%);
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
