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

    /*
     * لوحة ألوان الموقع.
     *
     * وضع اليوم الوطني يستبدل الكحلي بالأخضر السعودي والذهبي بأخضر أفتح،
     * فتتبدّل هوية الموقع كلّها بمفتاح واحد في إعدادات الموقع. نعرّف الألوان
     * هنا مرّة واحدة ونشتقّ منها إعداد Tailwind وقواعد CSS معاً، فلا يبقى
     * لون مكتوب يدوياً يتخلّف عن التبديل.
     */
    // يصل من View::composer في AppServiceProvider، والاحتياطي لأي عرض مباشر
    $nationalDay = $nationalDay ?? (bool) $S('national_day_mode');

    $C = $nationalDay
        ? [
            'primary'       => '#046A38',  // أخضر العلم السعودي
            'primary_dark'  => '#02502A',
            'primary_light' => '#0A8F4D',
            'accent'        => '#1DB954',
            'accent_dark'   => '#149944',
            'accent_light'  => '#5FD98A',
            'hero'          => ['#046A38', '#0A8F4D', '#02502A'],
            'band'          => ['#05743D', '#0A8F4D', '#046A38'],
            'glow'          => '29,185,84',
            'primary_rgb'   => '4,106,56',
        ]
        : [
            'primary'       => '#1e3a6d',
            'primary_dark'  => '#16294d',
            'primary_light' => '#2b4d8c',
            'accent'        => '#c9a84c',
            'accent_dark'   => '#ab8d38',
            'accent_light'  => '#e0c674',
            'hero'          => ['#1e3a6d', '#2b4d8c', '#16294d'],
            'band'          => ['#24457f', '#2f5596', '#1e3a6d'],
            'glow'          => '201,168,76',
            'primary_rgb'   => '30,58,109',
        ];
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

    {{-- تحقّق ملكية النطاق — يجب بقاؤه ما دام التحقّق مطلوباً --}}
    <meta name="domain-verification" content="189c737f2be9e854cce20a01b46dfa8fc34a68342f8a61e3c1cb715d50bfa9a8">

    {{-- وسوم جوجل (gtag.js) — الإعلانات والتحليلات.
         يُحمَّل السكربت مرة واحدة لكليهما (gtag يدعم أكثر من معرّف عبر config
         متكرّر)، ولا يُحقن أصلاً ما لم يُضبط معرّف واحد على الأقل في إعدادات
         الموقع. ويوضع أعلى الرأس ما أمكن ليلتقط الزيارة قبل أي تنقّل. --}}
    @php($gtagIds = array_values(array_filter([$S('google_ads_id'), $S('google_analytics_id')])))
    @if($gtagIds)
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gtagIds[0] }}"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      @foreach($gtagIds as $gtagId)
      gtag('config', @json($gtagId));
      @endforeach
    </script>
    @endif

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
                        {{-- الأسماء ثابتة (navy/gold) والقيم تتبدّل، فلا يحتاج أي قالب للتعديل --}}
                        navy:  { DEFAULT: '{{ $C['primary'] }}', dark: '{{ $C['primary_dark'] }}', light: '{{ $C['primary_light'] }}' },
                        gold:  { DEFAULT: '{{ $C['accent'] }}', dark: '{{ $C['accent_dark'] }}', light: '{{ $C['accent_light'] }}' },
                    },
                    fontFamily: { sans: [{!! json_encode(explode(', ', str_replace("'", '', $stack))) !!}] },
                },
            },
        };
    </script>
    <style>
        body { font-family: {{ $stack }}; }
        .hero-grad { background: linear-gradient(135deg, {{ $C['hero'][0] }} 0%, {{ $C['hero'][1] }} 55%, {{ $C['hero'][2] }} 100%); }

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
                border-inline-end: 5px solid {{ $C['accent'] }};
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

        /* ── حركة الظهور عند التمرير ────────────────────────────────────
           العنصر يبدأ خفياً ومزاحاً، ويظهر حين يدخل الشاشة عبر IntersectionObserver
           الذي يضيف الصنف .in. التأخير المتتابع يُضبط بالمتغيّر --d. */
        .reveal {
            opacity: 0;
            transform: translateY(26px);
            transition: opacity .7s cubic-bezier(.22,.61,.36,1),
                        transform .7s cubic-bezier(.22,.61,.36,1);
            transition-delay: var(--d, 0s);
            will-change: opacity, transform;
        }
        .reveal.in { opacity: 1; transform: none; }

        /* تنويعات الاتجاه */
        .reveal-start { transform: translateX(-26px); }
        [dir="rtl"] .reveal-start { transform: translateX(26px); }
        .reveal-scale { transform: scale(.94); }

        .reveal-start.in,
        .reveal-scale.in { transform: none; }

@if($nationalDay)
        /* ══════════ وضع اليوم الوطني ══════════
           زخارف مرسومة بالـ CSS لا صوراً، فتتبع لون الوضع ولا تحتاج رفع ملفات. */

        /* ── شاشة التحميل: وسيط يرفعه المستخدم (فيديو أو صورة متحرّكة) ── */
        .nd-loader {
            position: fixed; inset: 0; z-index: 200;
            display: flex; align-items: center; justify-content: center;
            background: #000;
            /* تنزاح تلقائياً حتى لو تعطّل الجافاسكربت فلا يعلق الزائر خلف ستار */
            animation: ndLoaderOut .6s ease-in-out 6s forwards;
        }
        /* يضيفها الجافاسكربت عند انتهاء الوسيط أو اكتمال التحميل */
        .nd-loader.is-done { animation: ndLoaderOut .5s ease-in-out forwards; }

        @keyframes ndLoaderOut {
            to { opacity: 0; visibility: hidden; }
        }

        /* الوسيط يملأ الشاشة كاملة بلا تشويه، والقصّ مقبول على الأطراف */
        .nd-loader__media {
            width: 100%; height: 100%; object-fit: cover; display: block;
        }

        /* زرّ التخطّي — لا نحبس الزائر أمام وسيط طويل */
        .nd-loader__skip {
            position: absolute; top: 1rem; inset-inline-end: 1rem; z-index: 2;
            background: rgba(0,0,0,.45); color: #fff; border: 1px solid rgba(255,255,255,.35);
            border-radius: 99px; padding: .45rem 1.1rem;
            font: 600 .8rem/1 inherit; cursor: pointer;
            backdrop-filter: blur(4px);
        }
        .nd-loader__skip:hover { background: rgba(0,0,0,.7); }

        /* لا شاشة تحميل لمن يفضّل تقليل الحركة — تُخفى فوراً */
        @media (prefers-reduced-motion: reduce) {
            .nd-loader { animation: none; opacity: 0; visibility: hidden; }
        }


        /* شريط العلم أعلى الصفحة */
        .nd-ribbon {
            height: 4px;
            background: repeating-linear-gradient(90deg,
                {{ $C['primary'] }} 0 60px, {{ $C['accent'] }} 60px 120px);
            background-size: 240px 100%;
            animation: ndRibbon 18s linear infinite;
        }
        @keyframes ndRibbon { to { background-position: 240px 0; } }

        /* هالات خضراء ناعمة تحلّ محلّ الخلفية الزخرفية الزرقاء */
        .nd-glow {
            position: absolute; inset: 0; pointer-events: none; overflow: hidden;
        }
        .nd-glow::before,
        .nd-glow::after {
            content: ''; position: absolute; border-radius: 50%;
            filter: blur(70px); opacity: .4;
        }
        .nd-glow::before {
            width: 30rem; height: 30rem; inset-inline-start: -8rem; bottom: -10rem;
            background: radial-gradient(circle, {{ $C['accent'] }} 0%, transparent 70%);
            animation: ndFloat 14s ease-in-out infinite;
        }
        .nd-glow::after {
            width: 22rem; height: 22rem; inset-inline-start: 22%; top: -8rem;
            background: radial-gradient(circle, {{ $C['primary_light'] }} 0%, transparent 70%);
            animation: ndFloat 18s ease-in-out infinite reverse;
        }
        @keyframes ndFloat {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50%      { transform: translate(2rem, -1.5rem) scale(1.12); }
        }

        /* سعف نخيل صغيرة تتمايل في زوايا الأقسام */
        .nd-palm {
            position: absolute; pointer-events: none; z-index: 1;
            color: {{ $C['primary'] }}; opacity: .5;
            transform-origin: bottom center;
            animation: ndSway 6s ease-in-out infinite;
        }
        @keyframes ndSway {
            0%, 100% { transform: rotate(-4deg); }
            50%      { transform: rotate(4deg); }
        }

        /* شارة «اليوم الوطني» النابضة */
        .nd-badge {
            animation: ndBadge 2.6s ease-in-out infinite;
        }
        @keyframes ndBadge {
            0%, 100% { box-shadow: 0 0 0 0 rgba({{ $C['glow'] }}, .55); }
            70%      { box-shadow: 0 0 0 14px rgba({{ $C['glow'] }}, 0); }
        }

        @media (prefers-reduced-motion: reduce) {
            .nd-ribbon, .nd-glow::before, .nd-glow::after, .nd-palm, .nd-badge {
                animation: none !important;
            }
        }
@endif

        /* احترام تفضيل تقليل الحركة في نظام المستخدم */
        @media (prefers-reduced-motion: reduce) {
            .hero-rise, .hero-reveal, .hero-line, .hero-photo img {
                animation: none !important;
                opacity: 1 !important;
                transform: none !important;
            }
            .reveal, .reveal-start, .reveal-scale {
                opacity: 1 !important;
                transform: none !important;
                transition: none !important;
            }
        }

        /* إعادة تلوين الأيقونات حسب الخلفية.
           ملفات SVG تستخدم الكحلي #2F6798 والذهبي #D8BC4B، ونستهدفهما بالسمة. */

        /* فوق خلفية داكنة: الكحلي يصبح أبيض */
        .stat-icon [stroke="#2F6798"] { stroke: #ffffff; }
        .stat-icon [fill="#2F6798"]   { fill: #ffffff; }

        /* أيقونات ذهبية بالكامل (قوائم التواصل) */
        .gold-icon [stroke="#2F6798"] { stroke: {{ $C['accent'] }}; }
        .gold-icon [fill="#2F6798"]   { fill: {{ $C['accent'] }}; }

        /* بطاقة الدعوة قبل التذييل — أزرق أفتح قليلاً من hero-grad لتبرز عن الصفحة */
        .cta-band { background: linear-gradient(120deg, {{ $C['band'][0] }} 0%, {{ $C['band'][1] }} 50%, {{ $C['band'][2] }} 100%); }

        /* هالة مضيئة حول البطاقة تنبض ببطء لتلفت النظر دون إزعاج */
        .cta-shell {
            box-shadow:
                0 0 0 1px rgba(255,255,255,.08),
                0 18px 40px -12px rgba({{ $C['primary_rgb'] }},.45),
                0 0 60px -12px rgba({{ $C['glow'] }},.35);
            animation: ctaPulse 4s ease-in-out infinite;
        }

        @keyframes ctaPulse {
            0%, 100% {
                box-shadow:
                    0 0 0 1px rgba(255,255,255,.08),
                    0 18px 40px -12px rgba({{ $C['primary_rgb'] }},.45),
                    0 0 60px -12px rgba({{ $C['glow'] }},.30);
            }
            50% {
                box-shadow:
                    0 0 0 1px rgba(255,255,255,.14),
                    0 18px 46px -12px rgba({{ $C['primary_rgb'] }},.5),
                    0 0 90px -8px rgba({{ $C['glow'] }},.55);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .cta-shell { animation: none; }
        }

        /* توهّج ناعم حول الزرّ الذهبي عند المرور.
           نستخدم box-shadow لا عنصراً زائفاً بـ z-index سالب، إذ يختفي الأخير
           خلف خلفية القسم عندما تُنشئ الأخيرة سياق تراصّ خاصاً بها. */
        .btn-glow { box-shadow: 0 10px 25px -10px rgba({{ $C['glow'] }},.5); }
        .btn-glow:hover {
            box-shadow: 0 0 0 4px rgba({{ $C['glow'] }},.25),
                        0 12px 32px -8px rgba({{ $C['glow'] }},.75);
        }

        /* إخفاء شريط التمرير في السلايدر مع إبقاء التمرير باللمس فعّالاً */
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .no-scrollbar::-webkit-scrollbar { display: none; }

        [x-cloak] { display: none !important; }
    </style>
    @stack('head')
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

@php
    // وسيط شاشة التحميل يرفعه المستخدم باسم national_day_loader.
    // لا شاشة أصلاً إن لم يُرفع شيء، فلا يُحجب الموقع خلف ستار فارغ.
    $ndLoaderMedia = null;
    $ndLoaderType  = null;

    foreach (['mp4' => 'video', 'webm' => 'video', 'gif' => 'image', 'webp' => 'image'] as $ext => $kind) {
        if (file_exists(public_path('national_day_loader.' . $ext))) {
            $ndLoaderMedia = 'national_day_loader.' . $ext;
            $ndLoaderType  = $kind;
            break;
        }
    }
@endphp

@if($nationalDay && ($ndLoaderMedia ?? null))
{{--
    شاشة ترحيب باليوم الوطني تعرض الوسيط ثم تنزاح.

    الانزياح مضمون بأنيميشن CSS له مدّة محدّدة لا بالجافاسكربت وحده، فلو تعطّل
    السكربت أو حُجب انزاحت الشاشة في موعدها ولم يعلق الزائر خلفها. ومعها زرّ
    تخطٍّ لمن لا يريد انتظار الوسيط كاملاً.
--}}
<div id="nd-loader" class="nd-loader" role="status" aria-live="polite">
    <button type="button" class="nd-loader__skip" data-nd-skip>تخطٍّ</button>

    @if($ndLoaderType === 'video')
    {{-- muted + playsinline شرطا التشغيل التلقائي في متصفّحات الجوال --}}
    <video class="nd-loader__media" autoplay muted playsinline
           src="{{ asset($ndLoaderMedia) }}" aria-hidden="true"></video>
    @else
    <img class="nd-loader__media" src="{{ asset($ndLoaderMedia) }}"
         alt="تهنئة اليوم الوطني السعودي">
    @endif
</div>

<script>
/**
 * يُخفي شاشة الترحيب.
 *
 * الانزياح مضمون بالـ CSS أصلاً (أنيميشن له مدّة محدّدة)، وهذا السكربت يتيح
 * إخفاءها أبكر: عند انتهاء الفيديو، أو بضغط زرّ التخطّي، أو إن تعذّر تشغيل
 * الفيديو أصلاً (بعض المتصفّحات تمنع التشغيل التلقائي).
 */
(function () {
    var el = document.getElementById('nd-loader');
    if (! el) return;

    var done = false;

    function dismiss() {
        if (done) return;
        done = true;
        el.classList.add('is-done');
    }

    var skip = el.querySelector('[data-nd-skip]');
    if (skip) skip.addEventListener('click', dismiss);

    var video = el.querySelector('video');

    if (video) {
        video.addEventListener('ended', dismiss);
        video.addEventListener('error', dismiss);

        // التشغيل التلقائي قد يُرفض؛ عندها لا معنى لستار ساكن
        var play = video.play();
        if (play && typeof play.catch === 'function') {
            play.catch(dismiss);
        }
    } else {
        // صورة متحرّكة: لا حدث انتهاء لها، فنترك مؤقّت الـ CSS يتولّى الأمر
        var img = el.querySelector('img');
        if (img) img.addEventListener('error', dismiss);
    }
})();
</script>

{{-- شريط بألوان العلم أعلى الصفحة كلّها --}}
<div class="nd-ribbon" aria-hidden="true"></div>
@endif

@include('public.partials.header')

<main>
    @yield('content')
</main>

@include('public.partials.footer')

@if($nationalDay)
{{-- تهنئة اليوم الوطني — تسبق نافذة الطلب فلا تتزاحمان --}}
@include('public.partials.national-day-popup')
@endif

{{-- نافذة الطلب السريع — تُستثنى صفحة «اطلب الآن» لأن النموذج معروض فيها --}}
@unless(request()->routeIs('site.order'))
    @include('public.partials.lead-popup')
@endunless

<script>
/**
 * سلايدر أفقي عام — يعتمد على تمرير العنصر نفسه (scroll) لا على transform،
 * فيبقى السحب باللمس على الجوال يعمل تلقائياً ومعه snap.
 *
 * ملاحظة RTL: قيمة scrollLeft تكون سالبة في المتصفحات الحديثة عند dir="rtl"،
 * لذا نتعامل مع قيمتها المطلقة في كل الحسابات ونعكس اتجاه الإزاحة.
 *
 * الاستخدام: x-data="hSlider(4000)" حيث الوسيط هو مهلة الدوران بالمللي ثانية.
 */
function hSlider(delay = 3500) {
    return {
        scrollable: false,
        timer: null,

        init() {
            this.$nextTick(() => { this.sync(); this.resume(); });
            window.addEventListener('resize', () => this.sync(), { passive: true });

            // نوقف الحركة إذا غادر الزائر التبويب، ونستأنفها عند عودته
            document.addEventListener('visibilitychange', () => {
                document.hidden ? this.pause() : this.resume();
            });
        },

        /** مقدار الإزاحة = عرض بطاقة واحدة + الفجوة بينها وبين التالية */
        step() {
            const t = this.$refs.track;
            const card = t.firstElementChild;
            if (!card) return t.clientWidth;

            const gap = parseFloat(getComputedStyle(t).columnGap || '0') || 0;
            return card.offsetWidth + gap;
        },

        /** يحدّث حالة إمكانية التمرير بعد أي تغيّر */
        sync() {
            const t = this.$refs.track;
            const max = t.scrollWidth - t.clientWidth;

            const was = this.scrollable;
            this.scrollable = max > 4;      // هامش صغير لتفادي أخطاء التقريب

            if (!was && this.scrollable) this.resume();
            if (was && !this.scrollable) this.pause();
        },

        /** هل بلغنا نهاية الشريط؟ */
        atEnd() {
            const t = this.$refs.track;
            return Math.abs(t.scrollLeft) >= (t.scrollWidth - t.clientWidth) - 4;
        },

        /** يبدأ الدوران، ما لم يفضّل المستخدم تقليل الحركة */
        resume() {
            this.pause();
            if (!this.scrollable) return;
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

            this.timer = setInterval(() => this.advance(), delay);
        },

        pause() {
            if (this.timer) { clearInterval(this.timer); this.timer = null; }
        },

        /** يتقدّم بطاقة، ويعود إلى البداية عند بلوغ النهاية */
        advance() {
            this.atEnd()
                ? this.$refs.track.scrollTo({ left: 0, behavior: 'smooth' })
                : this.next();
        },

        /** الاتجاه المنطقي: في RTL يقلّ scrollLeft كلما تقدّمنا */
        move(dir) {
            const t = this.$refs.track;
            const rtl = getComputedStyle(t).direction === 'rtl';
            t.scrollBy({ left: this.step() * dir * (rtl ? -1 : 1), behavior: 'smooth' });
        },

        next() { this.move(1); },
        prev() { this.move(-1); },
    };
}
</script>
<script>
/**
 * إظهار العناصر عند دخولها الشاشة.
 *
 * كل عنصر يحمل الصنف .reveal يبقى خفياً حتى يظهر ثلثه تقريباً، فيُضاف .in
 * ثم نتوقف عن مراقبته — الحركة لمرة واحدة فلا تتكرر مع كل تمرير.
 *
 * العناصر داخل مجموعة واحدة (data-reveal-group) تظهر متتابعة بفارق بسيط.
 */
(function () {
    const start = () => {
        const items = document.querySelectorAll('.reveal, .reveal-start, .reveal-scale');
        if (!items.length) return;

        // متصفح قديم بلا IntersectionObserver → نُظهر كل شيء فوراً
        if (!('IntersectionObserver' in window)) {
            items.forEach(el => el.classList.add('in'));
            return;
        }

        // تأخير متتابع لأبناء كل مجموعة
        document.querySelectorAll('[data-reveal-group]').forEach(group => {
            [...group.children].forEach((child, i) => {
                const target = child.matches('.reveal, .reveal-start, .reveal-scale')
                    ? child
                    : child.querySelector('.reveal, .reveal-start, .reveal-scale');
                if (target && !target.style.getPropertyValue('--d')) {
                    target.style.setProperty('--d', Math.min(i * 0.08, 0.5) + 's');
                }
            });
        });

        const io = new IntersectionObserver((entries, obs) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('in');
                obs.unobserve(entry.target);   // الحركة لمرة واحدة
            });
        }, { threshold: 0.15, rootMargin: '0px 0px -60px 0px' });

        items.forEach(el => io.observe(el));
    };

    document.readyState === 'loading'
        ? document.addEventListener('DOMContentLoaded', start)
        : start();
})();
</script>
<script>
/**
 * نافذة الطلب السريع.
 *
 * تظهر مرة واحدة لكل زائر: نخزّن علامة في localStorage عند الإغلاق أو الإرسال.
 * الوصول إلى localStorage قد يفشل (وضع التصفّح الخاص، حظر التخزين)،
 * لذا نغلّف كل قراءة وكتابة بـ try/catch ونعتبر الفشل «لم تُعرض بعد».
 */
function leadPopup() {
    const KEY   = 'lead_popup_seen';
    const DELAY = 6000;   // مهلة قبل الظهور — تكفي لإلقاء نظرة على الصفحة

    return {
        open: false,
        done: false,
        sending: false,
        errors: [],
        form: { name: '', phone: '', city: '', nationality_id: '', service: '', notes: '', website: '' },

        init() {
            if (this.seen()) return;
            setTimeout(() => { this.open = true; }, DELAY);
        },

        seen() {
            try { return localStorage.getItem(KEY) === '1'; } catch { return false; }
        },

        remember() {
            try { localStorage.setItem(KEY, '1'); } catch { /* التخزين محظور — نتجاهل */ }
        },

        close() {
            this.open = false;
            this.remember();
        },

        async submit() {
            this.sending = true;
            this.errors  = [];

            try {
                const res = await fetch('{{ route('site.lead.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                    },
                    body: JSON.stringify(this.form),
                });

                const data = await res.json().catch(() => ({}));

                if (res.ok) {
                    this.done = true;
                    this.remember();
                    return;
                }

                // 422 من التحقّق: نعرض كل الرسائل؛ غير ذلك رسالة عامة
                this.errors = data.errors
                    ? Object.values(data.errors).flat()
                    : [data.message || 'تعذّر إرسال الطلب. حاول مرة أخرى.'];
            } catch {
                this.errors = ['تعذّر الاتصال. تحقّق من الشبكة وحاول مجدداً.'];
            } finally {
                this.sending = false;
            }
        },
    };
}
</script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@stack('scripts')
</body>
</html>
