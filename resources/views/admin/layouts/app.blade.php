@php
    // احتياطي كامل: لو لم يُحمَّل config/locales.php بعد (كاش قديم) نعود للعربية
    // بدل أن ينقلب اتجاه الواجهة كلها إلى LTR.
    $fallbackConf = [
        'native'      => 'العربية',
        'flag'        => '🇸🇦',
        'dir'         => 'rtl',
        'font_stack'  => ['Cairo', 'sans-serif'],
        'google_font' => 'Cairo:wght@300;400;500;600;700;800',
    ];

    $locales    = config('locales.supported') ?: ['ar' => $fallbackConf];
    $currentLoc = app()->getLocale();

    // اللغة الحالية غير مدعومة (مثلاً APP_LOCALE=en الافتراضي في Laravel)
    if (! isset($locales[$currentLoc])) {
        $currentLoc = config('locales.default', 'ar');
    }

    $locConf = $locales[$currentLoc] ?? $fallbackConf;
    $dir     = $locConf['dir'] ?? 'rtl';
@endphp
<!DOCTYPE html>
<html lang="{{ $currentLoc }}" dir="{{ $dir }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('common.dashboard')) - {{ __('common.system') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT: '#c9a84c', light: '#fdf8e8', dark: '#a88830' },
                        surface: '#ffffff',
                        bg: '#f5f7fb',
                        ink: { DEFAULT: '#0f172a', muted: '#64748b', faint: '#94a3b8' },
                        border: '#e8edf5',
                    },
                    fontFamily: { sans: @json($locConf['font_stack']) },
                    boxShadow: {
                        card: '0 1px 3px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04)',
                        'card-hover': '0 4px 16px rgba(15,23,42,.10), 0 2px 4px rgba(15,23,42,.06)',
                        topbar: '0 1px 0 #e8edf5',
                    }
                }
            }
        }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family={{ $locConf['google_font'] }}&display=swap" rel="stylesheet">
    <!-- Tom Select -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.default.min.css">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" defer></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.14.1/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        html, body { overflow-x: hidden; max-width: 100%; }
        :root {
            --sidebar-w: 260px;
            --topbar-h: 60px;
            --primary: #c9a84c;
            --primary-light: #fdf8e8;
            --surface: #ffffff;
            --bg: #f5f7fb;
            --ink: #0f172a;
            --ink-muted: #64748b;
            --ink-faint: #94a3b8;
            --border: #e8edf5;
            --radius: 12px;
        }
        /* الخط والاتجاه يتغيّران حسب اللغة المختارة */
        :root { --app-font: {{ implode(', ', array_map(fn($f) => str_contains($f, ' ') ? "'$f'" : $f, $locConf['font_stack'])) }}; --app-dir: {{ $dir }}; }
        body { font-family: var(--app-font); background: var(--bg); color: var(--ink); -webkit-font-smoothing: antialiased; }
        [x-cloak] { display: none !important; }

        /* ── Sidebar ── */
        #sidebar {
            position: fixed; top: 0;
            width: var(--sidebar-w); height: 100vh;
            background: #0f172a;
            display: flex; flex-direction: column;
            z-index: 50;
            transition: transform .25s cubic-bezier(.4,0,.2,1);
            overflow: hidden;
        }
        /* تثبيت صريح لكل اتجاه — أوثق من الخصائص المنطقية مع تعدد المتصفحات */
        [dir="rtl"] #sidebar { right: 0; left: auto; }
        [dir="ltr"] #sidebar { left: 0; right: auto; }
        /* الإخفاء نحو الحافة الخارجية — يعكس اتجاهه حسب اللغة */
        [dir="rtl"] #sidebar.collapsed { transform: translateX(100%); }
        [dir="ltr"] #sidebar.collapsed { transform: translateX(-100%); }

        /* Sidebar nav link */
        .nav-link {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 12px; border-radius: 8px;
            color: #94a3b8; font-size: 13px; font-weight: 500;
            transition: all .15s ease; white-space: nowrap;
            cursor: pointer; text-decoration: none;
        }
        .nav-link:hover { background: rgba(255,255,255,.06); color: #cbd5e1; }
        .nav-link.active {
            background: rgba(201,168,76,.18);
            color: #c9a84c;
            font-weight: 600;
        }
        .nav-link.active svg { color: #c9a84c; }
        .nav-link svg { flex-shrink: 0; width: 16px; height: 16px; transition: color .15s; }

        /* ── Main wrapper ── */
        #main-wrap {
            display: flex; flex-direction: column; min-height: 100vh;
            transition: margin .25s cubic-bezier(.4,0,.2,1);
            min-width: 0; overflow-x: hidden;
        }
        [dir="rtl"] #main-wrap { margin-right: var(--sidebar-w); margin-left: 0; }
        [dir="ltr"] #main-wrap { margin-left: var(--sidebar-w); margin-right: 0; }
        [dir="rtl"] #main-wrap.expanded { margin-right: 0; margin-left: 0; }
        [dir="ltr"] #main-wrap.expanded { margin-left: 0; margin-right: 0; }
        main { min-width: 0; overflow-x: hidden; }

        /* ── Topbar ── */
        #topbar {
            position: sticky; top: 0; z-index: 40;
            height: 72px;
            background: rgba(255,255,255,.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center;
            padding: 0 20px;
            gap: 0;
        }

        /* ── Cards ── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: 0 1px 3px rgba(15,23,42,.05);
            transition: box-shadow .2s ease, transform .2s ease;
        }
        .card:hover { box-shadow: 0 4px 20px rgba(15,23,42,.09); transform: translateY(-1px); }

        /* ── Stat cards ── */
        .stat-icon {
            width: 44px; height: 44px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        /* ── Flash alerts ── */
        .alert {
            display: flex; align-items: center; justify-content: space-between;
            padding: 12px 16px; border-radius: 10px; margin-bottom: 16px;
            font-size: 14px; font-weight: 500; border: 1px solid;
            animation: slideDown .25s ease;
        }
        .alert-success { background: #f0fdf4; border-color: #bbf7d0; color: #15803d; }
        .alert-error   { background: #fff1f2; border-color: #fecdd3; color: #be123c; }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Scrollbar ── */
        #sidebar nav::-webkit-scrollbar { width: 4px; }
        #sidebar nav::-webkit-scrollbar-track { background: transparent; }
        #sidebar nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.1); border-radius: 4px; }

        /* ── Search ── */
        .search-input {
            width: 100%; background: #f1f5f9;
            border: 1.5px solid transparent; border-radius: 10px;
            padding: 8px 36px 8px 14px;
            font-size: 13px; color: var(--ink); font-family: var(--app-font);
            transition: border-color .15s, background .15s;
            outline: none;
        }
        .search-input:focus { background: #fff; border-color: #c9a84c; box-shadow: 0 0 0 3px rgba(201,168,76,.12); }
        .search-input::placeholder { color: var(--ink-faint); }

        /* ── Icon button ── */
        .icon-btn {
            width: 34px; height: 34px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: var(--ink-faint); transition: background .15s, color .15s;
            cursor: pointer; border: none; background: transparent;
            outline: none; -webkit-appearance: none;
        }
        .icon-btn:hover { background: #f1f5f9; color: var(--ink-muted); }
        .icon-btn:focus { outline: none; }

        /* ── Badge ── */
        .badge {
            position: absolute; top: -3px; left: -3px;
            min-width: 16px; height: 16px; border-radius: 8px;
            background: #ef4444; color: #fff;
            font-size: 9px; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            padding: 0 3px; border: 2px solid #fff;
        }

        /* ── Table ── */
        .data-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .data-table th {
            padding: 10px 14px; text-align: right;
            font-size: 11px; font-weight: 600; letter-spacing: .03em;
            color: var(--ink-faint); background: #f8fafc;
            border-bottom: 1px solid var(--border);
        }
        .data-table td { padding: 11px 14px; border-bottom: 1px solid #f1f5f9; color: var(--ink-muted); }
        .data-table tbody tr:last-child td { border-bottom: none; }
        .data-table tbody tr:hover td { background: #fdfbf3; }

        /* ── Responsive ── */
        #sidebar-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,.55);
            z-index: 48;
            opacity: 0; pointer-events: none;
            transition: opacity .25s;
        }
        #sidebar-overlay.active { opacity: 1; pointer-events: all; }

        #bottom-nav { display: none; }

        /* ── Bottom nav items (shared, only visible on mobile) ── */
        .bnav-item {
            flex: 1;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            gap: 3px;
            color: #64748b; font-size: 10px; font-weight: 600;
            text-decoration: none;
            font-family: var(--app-font);
            transition: color .15s, background .15s;
            padding: 4px 2px;
        }
        .bnav-item.active { color: #c9a84c; }
        .bnav-item:active, .bnav-item:hover { color: #e2e8f0; background: rgba(255,255,255,.05); }

        @media (max-width: 767px) {
            [dir="rtl"] #sidebar { box-shadow: -6px 0 32px rgba(0,0,0,.45); }
            [dir="ltr"] #sidebar { box-shadow:  6px 0 32px rgba(0,0,0,.45); }
            #main-wrap,
            #main-wrap.expanded { margin-right: 0 !important; margin-left: 0 !important; }
            #topbar { height: 58px !important; padding: 0 10px !important; }
            .topbar-search-wrap { display: none !important; }
            .topbar-fullscreen  { display: none !important; }
            main { padding: 10px 10px 74px !important; }
            #bottom-nav {
                display: flex;
                position: fixed; bottom: 0; left: 0; right: 0;
                height: 62px;
                background: #0f172a;
                border-top: 1px solid rgba(201,168,76,.2);
                z-index: 55;
                align-items: stretch;
            }
        }

        @media (min-width: 768px) and (max-width: 1023px) {
            :root { --sidebar-w: 220px; }
            #topbar { padding: 0 14px !important; }
            main { padding: 14px !important; }
        }
    </style>

    <!-- Tom Select RTL Theme -->
    <style>
        .ts-wrapper { direction: var(--app-dir); font-family: var(--app-font); }
        .ts-wrapper .ts-control {
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            padding: 6px 10px 6px 32px;
            font-family: var(--app-font);
            font-size: 13px;
            background: #fff;
            min-height: 38px;
            cursor: pointer;
            transition: border-color .15s, box-shadow .15s;
            gap: 4px;
        }
        .ts-wrapper.focus .ts-control {
            border-color: #c9a84c !important;
            box-shadow: 0 0 0 3px rgba(201,168,76,.15);
            outline: none;
        }
        .ts-wrapper .ts-control .item { font-size: 13px; }
        .ts-wrapper .ts-control::after {
            content: '';
            position: absolute;
            left: 10px; top: 50%;
            transform: translateY(-50%);
            width: 0; height: 0;
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-top: 5px solid #94a3b8;
            pointer-events: none;
        }
        .ts-wrapper.open .ts-control::after { border-top: none; border-bottom: 5px solid #c9a84c; }
        .ts-wrapper .ts-dropdown {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            box-shadow: 0 8px 30px rgba(15,23,42,.13);
            font-family: var(--app-font);
            font-size: 13px;
            direction: var(--app-dir);
            z-index: 9999;
            margin-top: 4px;
            overflow: hidden;
        }
        .ts-wrapper .ts-dropdown .ts-dropdown-content { max-height: 220px; }
        .ts-wrapper .ts-dropdown .option {
            padding: 9px 14px;
            color: #334155;
            cursor: pointer;
            transition: background .12s;
        }
        .ts-wrapper .ts-dropdown .option:hover,
        .ts-wrapper .ts-dropdown .option.active   { background: #fdf8e8; color: #92720e; }
        .ts-wrapper .ts-dropdown .option.selected { background: #fef9e7; color: #c9a84c; font-weight: 600; }
        .ts-dropdown input.dropdown-input {
            border: none;
            border-bottom: 1.5px solid #e8edf5;
            padding: 8px 12px;
            font-family: var(--app-font);
            font-size: 13px;
            width: 100%;
            outline: none;
            direction: var(--app-dir);
            background: #fafafa;
        }
        .ts-dropdown input.dropdown-input:focus { border-bottom-color: #c9a84c; background: #fff; }
        .ts-dropdown .no-results { padding: 10px 14px; color: #94a3b8; font-size: 13px; }
        /* hide default arrow from original select */
        select { display: none; }
    </style>

    @stack('styles')
</head>
<body>

<div x-data="{
    open: window.innerWidth >= 768,
    mob: window.innerWidth < 768,
    init() {
        window.addEventListener('resize', () => {
            const isMob = window.innerWidth < 768;
            if (!isMob && !this.open) this.open = true;
            if (isMob && this.open) this.open = false;
            this.mob = isMob;
        });
    }
}">

    <!-- Mobile sidebar overlay backdrop -->
    <div id="sidebar-overlay"
         :class="open && mob ? 'active' : ''"
         @click="open = false"
         x-cloak></div>

    <!-- Sidebar -->
    <div id="sidebar" :class="open ? '' : 'collapsed'">
        @include('admin.layouts._sidebar')
    </div>

    <!-- Main -->
    <div id="main-wrap" :class="open ? '' : 'expanded'">

        <!-- Topbar -->
        <div id="topbar">
            @include('admin.layouts._topbar')
        </div>

        <!-- Content -->
        <main class="flex-1 p-6">

            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4500)"
                     class="alert alert-success" x-cloak>
                    <span>{{ session('success') }}</span>
                    <button @click="show = false" class="text-lg leading-none opacity-60 hover:opacity-100 ml-2">×</button>
                </div>
            @endif
            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" class="alert alert-error" x-cloak>
                    <span>{{ session('error') }}</span>
                    <button @click="show = false" class="text-lg leading-none opacity-60 hover:opacity-100 ml-2">×</button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-error">
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Bottom navigation bar (mobile only) -->
    <nav id="bottom-nav">
        <a href="{{ route('admin.dashboard') }}"
           class="bnav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <svg width="21" height="21" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                <rect x="14" y="14" width="7" height="7" rx="1.5"/>
            </svg>
            {{ __('common.home') }}
        </a>
        <a href="{{ route('admin.contracts.index') }}"
           class="bnav-item {{ request()->routeIs('admin.contracts.*') ? 'active' : '' }}">
            <svg width="21" height="21" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
            </svg>
            {{ __('nav.contracts.group') }}
        </a>
        <a href="{{ route('admin.incomes.index') }}"
           class="bnav-item {{ request()->routeIs('admin.incomes.*','admin.expenses.*','admin.transfers.*','admin.reports.*') ? 'active' : '' }}">
            <svg width="21" height="21" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                <rect x="2" y="5" width="20" height="14" rx="2"/>
                <path d="M2 10h20"/>
            </svg>
            {{ __('nav.finance.group') }}
        </a>
        <a href="{{ route('admin.clients.index') }}"
           class="bnav-item {{ request()->routeIs('admin.clients.*','admin.agents.*','admin.workers.*') ? 'active' : '' }}">
            <svg width="21" height="21" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
            </svg>
            {{ __('nav.people.clients') }}
        </a>
        <a href="#" @click.prevent="open = !open"
           class="bnav-item" :class="open ? 'active' : ''">
            <svg width="21" height="21" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                <line x1="3" y1="6"  x2="21" y2="6"/>
                <line x1="3" y1="12" x2="21" y2="12"/>
                <line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
            {{ __('nav.menu') }}
        </a>
    </nav>
</div>

@stack('scripts')
<script>
// Global Tom Select init — runs after Alpine and DOM are ready
function initTomSelects(root) {
    root = root || document;
    root.querySelectorAll('select:not([data-ts-ignore]):not([data-ts-done])').forEach(function(el) {
        if (el._tomSelect) return;
        el.setAttribute('data-ts-done', '1');
        var opts = {
            allowEmptyOption: true,
            plugins: [],
            searchField: ['text'],
            placeholder: el.options[0] ? el.options[0].text : @json(__('nav.choose_ph')),
            render: {
                no_results: function() {
                    return '<div class="no-results">' + @json(__('nav.no_result')) + '</div>';
                }
            }
        };
        // Disable search for small selects (≤5 options)
        if (el.options.length <= 5) { opts.controlInput = null; }
        try { new TomSelect(el, opts); } catch(e) {}
    });
}
document.addEventListener('DOMContentLoaded', function() { initTomSelects(); });
// Re-init after Alpine finishes (for any dynamically shown elements)
document.addEventListener('alpine:initialized', function() { initTomSelects(); });
</script>
</body>
</html>

