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

        /* إعادة تلوين الأيقونات حسب الخلفية.
           ملفات SVG تستخدم الكحلي #2F6798 والذهبي #D8BC4B، ونستهدفهما بالسمة. */

        /* فوق خلفية داكنة: الكحلي يصبح أبيض */
        .stat-icon [stroke="#2F6798"] { stroke: #ffffff; }
        .stat-icon [fill="#2F6798"]   { fill: #ffffff; }

        /* أيقونات ذهبية بالكامل (قوائم التواصل) */
        .gold-icon [stroke="#2F6798"] { stroke: #c9a84c; }
        .gold-icon [fill="#2F6798"]   { fill: #c9a84c; }

        /* بطاقة الدعوة قبل التذييل — أزرق أفتح قليلاً من hero-grad لتبرز عن الصفحة */
        .cta-band { background: linear-gradient(120deg, #24457f 0%, #2f5596 50%, #1e3a6d 100%); }

        /* هالة مضيئة حول البطاقة تنبض ببطء لتلفت النظر دون إزعاج */
        .cta-shell {
            box-shadow:
                0 0 0 1px rgba(255,255,255,.08),
                0 18px 40px -12px rgba(30,58,109,.45),
                0 0 60px -12px rgba(201,168,76,.35);
            animation: ctaPulse 4s ease-in-out infinite;
        }

        @keyframes ctaPulse {
            0%, 100% {
                box-shadow:
                    0 0 0 1px rgba(255,255,255,.08),
                    0 18px 40px -12px rgba(30,58,109,.45),
                    0 0 60px -12px rgba(201,168,76,.30);
            }
            50% {
                box-shadow:
                    0 0 0 1px rgba(255,255,255,.14),
                    0 18px 46px -12px rgba(30,58,109,.5),
                    0 0 90px -8px rgba(201,168,76,.55);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .cta-shell { animation: none; }
        }

        /* توهّج ناعم حول الزرّ الذهبي عند المرور.
           نستخدم box-shadow لا عنصراً زائفاً بـ z-index سالب، إذ يختفي الأخير
           خلف خلفية القسم عندما تُنشئ الأخيرة سياق تراصّ خاصاً بها. */
        .btn-glow { box-shadow: 0 10px 25px -10px rgba(201,168,76,.5); }
        .btn-glow:hover {
            box-shadow: 0 0 0 4px rgba(201,168,76,.25),
                        0 12px 32px -8px rgba(201,168,76,.75);
        }

        /* إخفاء شريط التمرير في السلايدر مع إبقاء التمرير باللمس فعّالاً */
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .no-scrollbar::-webkit-scrollbar { display: none; }

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
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@stack('scripts')
</body>
</html>
