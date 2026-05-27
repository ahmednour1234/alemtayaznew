<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم') - نظام الامتياز للاستقدام</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT: '#2563eb', light: '#eff6ff', dark: '#1d4ed8' },
                        surface: '#ffffff',
                        bg: '#f5f7fb',
                        ink: { DEFAULT: '#0f172a', muted: '#64748b', faint: '#94a3b8' },
                        border: '#e8edf5',
                    },
                    fontFamily: { sans: ['Cairo', 'sans-serif'] },
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
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" defer></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.14.1/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        :root {
            --sidebar-w: 260px;
            --topbar-h: 60px;
            --primary: #2563eb;
            --primary-light: #eff6ff;
            --surface: #ffffff;
            --bg: #f5f7fb;
            --ink: #0f172a;
            --ink-muted: #64748b;
            --ink-faint: #94a3b8;
            --border: #e8edf5;
            --radius: 12px;
        }
        body { font-family: 'Cairo', sans-serif; background: var(--bg); color: var(--ink); -webkit-font-smoothing: antialiased; }
        [x-cloak] { display: none !important; }

        /* ── Sidebar ── */
        #sidebar {
            position: fixed; top: 0; right: 0;
            width: var(--sidebar-w); height: 100vh;
            background: #0f172a;
            display: flex; flex-direction: column;
            z-index: 50;
            transition: transform .25s cubic-bezier(.4,0,.2,1);
            overflow: hidden;
        }
        #sidebar.collapsed { transform: translateX(100%); }

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
            background: rgba(37,99,235,.18);
            color: #93c5fd;
            font-weight: 600;
        }
        .nav-link.active svg { color: #60a5fa; }
        .nav-link svg { flex-shrink: 0; width: 16px; height: 16px; transition: color .15s; }

        /* ── Main wrapper ── */
        #main-wrap {
            margin-right: var(--sidebar-w);
            display: flex; flex-direction: column; min-height: 100vh;
            transition: margin-right .25s cubic-bezier(.4,0,.2,1);
        }
        #main-wrap.expanded { margin-right: 0; }

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
            font-size: 13px; color: var(--ink); font-family: 'Cairo', sans-serif;
            transition: border-color .15s, background .15s;
            outline: none;
        }
        .search-input:focus { background: #fff; border-color: var(--primary); }
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
        .data-table tbody tr:hover td { background: #fafbff; }
    </style>

    @stack('styles')
</head>
<body>

<div x-data="{ open: true }">

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
</div>

@stack('scripts')
</body>
</html>

