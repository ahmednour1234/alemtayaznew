<aside class="fixed right-0 top-0 h-full w-64 flex flex-col z-50 select-none"
       :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full'"
       style="background:#0a1628; transition: transform .25s cubic-bezier(.4,0,.2,1)">

    <!-- Logo -->
    <div class="flex items-center gap-3 px-5 py-[18px]" style="border-bottom:1px solid rgba(255,255,255,.07)">
        <div class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center" style="background:linear-gradient(135deg,#2563eb,#1d4ed8)">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                <path d="M12 2L3 7V17L12 22L21 17V7L12 2Z" stroke="white" stroke-width="1.8" stroke-linejoin="round"/>
                <path d="M12 2V22M3 7L12 12M21 7L12 12" stroke="white" stroke-width="1.8"/>
            </svg>
        </div>
        <div>
            <p class="text-white font-bold text-[13px] leading-tight">Ù†Ø¸Ø§Ù… Ø§Ù„Ø§Ù…ØªÙŠØ§Ø²</p>
            <p class="text-[11px] leading-tight mt-0.5" style="color:#4a7ab5">Ù„Ù„Ø§Ø³ØªÙ‚Ø¯Ø§Ù…</p>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-3 py-3 overflow-y-auto space-y-0.5">
        @php $admin = auth('admin')->user(); @endphp

        <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <svg class="w-[17px] h-[17px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/>
                <rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>
            </svg>
            Ù„ÙˆØ­Ø© Ø§Ù„ØªØ­ÙƒÙ…
        </a>

        <a href="{{ route('admin.incomes.index') }}" class="sidebar-link {{ request()->routeIs('admin.incomes.*') ? 'active' : '' }}">
            <svg class="w-[17px] h-[17px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/>
                <polyline points="16 7 22 7 22 13"/>
            </svg>
            Ø§Ù„Ø¥ÙŠØ±Ø§Ø¯Ø§Øª
        </a>

        <a href="{{ route('admin.expenses.index') }}" class="sidebar-link {{ request()->routeIs('admin.expenses.*') ? 'active' : '' }}">
            <svg class="w-[17px] h-[17px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <rect x="2" y="5" width="20" height="14" rx="2"/>
                <line x1="2" y1="10" x2="22" y2="10"/>
            </svg>
            Ø§Ù„Ù…ØµØ±ÙˆÙØ§Øª
        </a>

        <a href="{{ route('admin.transfers.index') }}" class="sidebar-link {{ request()->routeIs('admin.transfers.*') ? 'active' : '' }}">
            <svg class="w-[17px] h-[17px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path d="M7 16l-4-4 4-4M17 8l4 4-4 4M14 4l-4 16"/>
            </svg>
            Ø§Ù„ØªØ­ÙˆÙŠÙ„Ø§Øª Ø¨ÙŠÙ† Ø§Ù„ÙØ±ÙˆØ¹
        </a>

        <a href="{{ route('admin.reports.branch-statement') }}" class="sidebar-link {{ request()->routeIs('admin.reports.branch-statement') ? 'active' : '' }}">
            <svg class="w-[17px] h-[17px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                <polyline points="10 9 9 9 8 9"/>
            </svg>
            ÙƒØ´Ù Ø­Ø³Ø§Ø¨ Ø§Ù„ÙØ±Ø¹
        </a>

        <a href="{{ route('admin.reports.income-statement') }}" class="sidebar-link {{ request()->routeIs('admin.reports.income-statement') ? 'active' : '' }}">
            <svg class="w-[17px] h-[17px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <line x1="2" y1="12" x2="22" y2="12"/>
                <path d="M12 2a15.3 15.3 0 010 20M12 2a15.3 15.3 0 000 20"/>
            </svg>
            Ù‚Ø§Ø¦Ù…Ø© Ø¯Ø®Ù„ Ø¨ÙŠÙ† Ø§Ù„ÙØ±ÙˆØ¹
        </a>

        <a href="{{ route('admin.reports.branch-statement') }}" class="sidebar-link">
            <svg class="w-[17px] h-[17px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/>
                <line x1="6" y1="20" x2="6" y2="14"/>
            </svg>
            Ø§Ù„ØªÙ‚Ø§Ø±ÙŠØ±
        </a>

        <!-- Settings Section -->
        <div class="pt-4 pb-1.5 px-2">
            <p class="text-[11px] font-bold uppercase tracking-widest" style="color:#3d5c80">Ø§Ù„Ø¥Ø¹Ø¯Ø§Ø¯Ø§Øª</p>
        </div>

        <a href="{{ route('admin.branches.index') }}" class="sidebar-link {{ request()->routeIs('admin.branches.*') ? 'active' : '' }}">
            <svg class="w-[17px] h-[17px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path d="M3 21h18M3 7l9-4 9 4M4 7v14M20 7v14M9 21V11h6v10"/>
            </svg>
            Ø§Ù„ÙØ±ÙˆØ¹
        </a>

        <a href="{{ route('admin.income-types.index') }}" class="sidebar-link {{ request()->routeIs('admin.income-types.*') ? 'active' : '' }}">
            <svg class="w-[17px] h-[17px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>
            </svg>
            Ø£Ù†ÙˆØ§Ø¹ Ø§Ù„Ø¥ÙŠØ±Ø§Ø¯Ø§Øª
        </a>

        <a href="{{ route('admin.expense-types.index') }}" class="sidebar-link {{ request()->routeIs('admin.expense-types.*') ? 'active' : '' }}">
            <svg class="w-[17px] h-[17px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <line x1="8" y1="12" x2="16" y2="12"/>
            </svg>
            Ø£Ù†ÙˆØ§Ø¹ Ø§Ù„Ù…ØµØ±ÙˆÙØ§Øª
        </a>

        <a href="{{ route('admin.cities.index') }}" class="sidebar-link {{ request()->routeIs('admin.cities.*') ? 'active' : '' }}">
            <svg class="w-[17px] h-[17px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                <circle cx="12" cy="9" r="2.5"/>
            </svg>
            Ø§Ù„Ù…Ø¯Ù†
        </a>

        <a href="{{ route('admin.settings.roles.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings.roles.*') || request()->routeIs('admin.settings.permissions.*') ? 'active' : '' }}">
            <svg class="w-[17px] h-[17px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
            Ø§Ù„Ø£Ø¯ÙˆØ§Ø± ÙˆØ§Ù„ØµÙ„Ø§Ø­ÙŠØ§Øª
        </a>

        <a href="{{ route('admin.settings.admins.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings.admins.*') ? 'active' : '' }}">
            <svg class="w-[17px] h-[17px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
            </svg>
            Ø§Ù„Ù…Ø¯ÙŠØ±ÙŠÙ† <span class="text-[11px] opacity-50 mr-1">(Admins)</span>
        </a>

        <a href="{{ route('admin.settings.permissions.index') }}" class="sidebar-link">
            <svg class="w-[17px] h-[17px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="3"/>
                <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/>
            </svg>
            Ø§Ù„Ø¥Ø¹Ø¯Ø§Ø¯Ø§Øª Ø§Ù„Ø¹Ø§Ù…Ø©
        </a>
    </nav>

    <!-- Logout -->
    <div class="px-3 py-3" style="border-top:1px solid rgba(255,255,255,.07)">
        <form action="{{ route('admin.logout') }}" method="POST">
            @csrf
            <button type="submit" class="sidebar-link w-full" style="color:#e87575" onmouseover="this.style.background='rgba(239,68,68,.12)'" onmouseout="this.style.background=''">
                <svg class="w-[17px] h-[17px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M17 16l4-4m0 0l-4-4m4 4H7"/>
                    <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                </svg>
                ØªØ³Ø¬ÙŠÙ„ Ø®Ø±ÙˆØ¬
            </button>
        </form>
    </div>
</aside>

