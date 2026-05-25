<header class="bg-white sticky top-0 z-40 flex items-center justify-between px-5 py-3" style="box-shadow:0 1px 4px rgba(0,0,0,.06)">

    <!-- Right: sidebar toggle + page title + breadcrumb -->
    <div class="flex items-center gap-4">
        <button @click="sidebarOpen = !sidebarOpen"
                class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
        </button>
        <div>
            <h1 class="text-[15px] font-bold text-slate-800 leading-tight">@yield('title', 'Ù„ÙˆØ­Ø© Ø§Ù„ØªØ­ÙƒÙ…')</h1>
            <p class="text-[11px] text-slate-400 leading-tight mt-0.5">
                Ø§Ù„Ø±Ø¦ÙŠØ³ÙŠØ©
                @hasSection('breadcrumb-extra')
                <span class="mx-1">/</span>@yield('breadcrumb-extra')
                @endif
                <span class="mx-1">/</span> @yield('title', 'Ù„ÙˆØ­Ø© Ø§Ù„ØªØ­ÙƒÙ…')
            </p>
        </div>
    </div>

    <!-- Center: search -->
    <div class="flex-1 max-w-md mx-6">
        <div class="relative">
            <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" placeholder="Ø§Ø¨Ø­Ø« Ù‡Ù†Ø§..."
                   class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2 pr-9 pl-4 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-transparent">
        </div>
    </div>

    <!-- Left: action icons + user -->
    <div class="flex items-center gap-1.5">

        @php
            $pendingExpenses = \App\Models\Expense::where('status','pending')->count();
            $pendingTransfers = \App\Models\FinancialTransfer::where('status','pending')->count();
        @endphp

        <!-- Fullscreen -->
        <button onclick="document.fullscreenElement ? document.exitFullscreen() : document.documentElement.requestFullscreen()"
                class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path d="M8 3H5a2 2 0 00-2 2v3m18 0V5a2 2 0 00-2-2h-3m0 18h3a2 2 0 002-2v-3M3 16v3a2 2 0 002 2h3"/>
            </svg>
        </button>

        <!-- Dark mode toggle (UI only) -->
        <button class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
            </svg>
        </button>

        <!-- Mail / Transfers bell -->
        <a href="{{ route('admin.transfers.index', ['status' => 'pending']) }}"
           class="relative w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-blue-600 hover:bg-slate-100 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                <polyline points="22 6 12 13 2 6"/>
            </svg>
            @if($pendingTransfers > 0)
            <span class="absolute -top-0.5 -left-0.5 min-w-[16px] h-4 bg-blue-600 text-white text-[9px] font-bold rounded-full flex items-center justify-center px-0.5">{{ $pendingTransfers }}</span>
            @endif
        </a>

        <!-- Notifications / Expenses bell -->
        <a href="{{ route('admin.expenses.index', ['status' => 'pending']) }}"
           class="relative w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-blue-600 hover:bg-slate-100 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                <path d="M13.73 21a2 2 0 01-3.46 0"/>
            </svg>
            @if($pendingExpenses > 0)
            <span class="absolute -top-0.5 -left-0.5 min-w-[16px] h-4 bg-blue-600 text-white text-[9px] font-bold rounded-full flex items-center justify-center px-0.5">{{ $pendingExpenses }}</span>
            @endif
        </a>

        <!-- Divider -->
        <div class="w-px h-6 bg-slate-200 mx-1"></div>

        <!-- User info dropdown -->
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="flex items-center gap-2.5 px-2 py-1.5 rounded-xl hover:bg-slate-100 transition">
                @php $authAdmin = auth('admin')->user(); @endphp
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                     style="background:linear-gradient(135deg,#2563eb,#7c3aed)">
                    {{ mb_substr($authAdmin?->name ?? 'A', 0, 1) }}
                </div>
                <div class="text-right hidden sm:block">
                    <p class="text-xs font-semibold text-slate-700 leading-tight">{{ $authAdmin?->name }}</p>
                    <p class="text-[10px] text-slate-400 leading-tight">{{ $authAdmin?->roles->first()?->name ?? 'Ù…Ø¯ÙŠØ± Ø§Ù„Ù†Ø¸Ø§Ù…' }}</p>
                </div>
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </button>
            <div x-show="open" @click.outside="open = false" x-cloak
                 class="absolute left-0 top-full mt-1.5 w-44 bg-white rounded-xl shadow-lg border border-slate-100 py-1 z-50">
                <a href="{{ route('admin.settings.admins.index') }}"
                   class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    Ø§Ù„Ù…Ù„Ù Ø§Ù„Ø´Ø®ØµÙŠ
                </a>
                <hr class="my-1 border-slate-100">
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path d="M17 16l4-4m0 0l-4-4m4 4H7"/><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                        </svg>
                        ØªØ³Ø¬ÙŠÙ„ Ø®Ø±ÙˆØ¬
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

