<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Ù„ÙˆØ­Ø© Ø§Ù„ØªØ­ÙƒÙ…') - Ù†Ø¸Ø§Ù… Ø´Ø±ÙƒØ© Ø§Ù„Ø§Ù…ØªÙŠØ§Ø² Ù„Ù„Ø§Ø³ØªÙ‚Ø¯Ø§Ù…</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: {
                            600: '#1e3a5f',
                            700: '#162d4a',
                            800: '#0e2038',
                            900: '#091628',
                            950: '#060f1c',
                        },
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts: Noto Sans Arabic -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" defer></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Noto Sans Arabic', sans-serif; background-color: #f0f4fa; }
        .sidebar-link {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 14px; border-radius: 8px;
            color: #94a3b8; font-size: 13.5px; font-weight: 500;
            transition: background .15s, color .15s; white-space: nowrap;
        }
        .sidebar-link:hover { background: rgba(255,255,255,.07); color: #e2e8f0; }
        .sidebar-link.active { background: #2563eb; color: #fff; }
        .stat-card { @apply bg-white rounded-2xl p-5 flex items-center gap-4 shadow-sm; }
        [x-cloak] { display:none !important; }
    </style>

    @stack('styles')
</head>
<body class="bg-slate-100 text-slate-800">

<div class="flex min-h-screen" x-data="{ sidebarOpen: true }">

    <!-- Sidebar -->
    @include('admin.layouts._sidebar')

    <!-- Main Content -->
    <div class="flex-1 flex flex-col" :class="sidebarOpen ? 'mr-64' : 'mr-0'" style="transition: margin .2s">

        <!-- Topbar -->
        @include('admin.layouts._topbar')

        <!-- Page Content -->
        <main class="flex-1 p-6">

            <!-- Flash messages -->
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                     class="mb-4 bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded-lg flex justify-between">
                    <span>{{ session('success') }}</span>
                    <button @click="show = false" class="text-green-600 font-bold">Ã—</button>
                </div>
            @endif
            @if(session('error'))
                <div x-data="{ show: true }" x-show="show"
                     class="mb-4 bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded-lg flex justify-between">
                    <span>{{ session('error') }}</span>
                    <button @click="show = false" class="text-red-600 font-bold">Ã—</button>
                </div>
            @endif
            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded-lg">
                    <ul class="list-disc list-inside space-y-1">
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

