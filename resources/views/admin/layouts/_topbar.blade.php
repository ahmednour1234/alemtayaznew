@php
    $authAdmin        = auth('admin')->user();
    $unreadNotifCount = $authAdmin
        ? \App\Models\AdminNotification::where('admin_id', $authAdmin->id)->whereNull('read_at')->count()
        : 0;
    $recentNotifs     = $authAdmin
        ? \App\Models\AdminNotification::where('admin_id', $authAdmin->id)->latest()->limit(8)->get()
        : collect();
    $initials = strtoupper(mb_substr($authAdmin?->name ?? 'A', 0, 1));
@endphp
<style>
/* ── Topbar Redesign ── */
.tb-toggle {
    width: 38px; height: 38px; border-radius: 10px;
    background: #f1f5f9; border: 1px solid #e8edf5;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; transition: all .18s ease; flex-shrink: 0; color: #475569;
}
.tb-toggle:hover { background: #0f172a; color: #fff; border-color: #0f172a; }
.tb-breadcrumb {
    display: flex; align-items: center; gap: 4px;
    font-size: 11px; color: #94a3b8; margin: 3px 0 0; font-weight: 500;
}
.tb-breadcrumb a { color: #94a3b8; text-decoration: none; transition: color .15s; }
.tb-breadcrumb a:hover { color: #c9a84c; }
.tb-breadcrumb .crumb-sep { opacity: .5; font-size: 10px; }
.tb-breadcrumb .crumb-cur { color: #c9a84c; font-weight: 600; }
.tb-search-input {
    width: 100%; background: #f8fafc;
    border: 1.5px solid #e8edf5; border-radius: 12px;
    padding: 9px 38px 9px 14px; font-size: 13px; color: #0f172a;
    font-family: var(--app-font); transition: all .18s ease; outline: none;
}
.tb-search-input::placeholder { color: #b0bec5; }
.tb-search-input:focus { background:#fff; border-color:#c9a84c; box-shadow:0 0 0 3px rgba(201,168,76,.13); }
/* ── Global Search Dropdown ── */
.gs-dropdown {
    position:absolute; top:calc(100% + 6px); inset-inline:0;
    background:#fff; border-radius:14px; border:1.5px solid #e8edf5;
    box-shadow:0 20px 60px rgba(15,23,42,.16),0 4px 16px rgba(15,23,42,.08);
    overflow:hidden; z-index:9999; max-height:420px; overflow-y:auto;
}
.gs-group-label {
    font-size:10px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.06em;
    padding:10px 14px 4px; background:#f8fafc; border-bottom:1px solid #f1f5f9;
}
.gs-item {
    display:flex; align-items:center; gap:10px; padding:10px 14px;
    text-decoration:none; transition:background .1s; border-bottom:1px solid #f8fafc;
    cursor:pointer;
}
.gs-item:hover, .gs-item:focus { background:#f8fafc; outline:none; }
.gs-item-icon {
    width:34px; height:34px; border-radius:10px; flex-shrink:0;
    display:flex; align-items:center; justify-content:center; font-size:16px;
}
.gs-item-title { font-size:13px; font-weight:600; color:#1e293b; line-height:1.2; }
.gs-item-sub   { font-size:11px; color:#94a3b8; margin-top:1px; }
.gs-badge      { font-size:10px; font-weight:700; padding:2px 7px; border-radius:20px; margin-inline-start:auto; }
.gs-empty      { padding:28px 14px; text-align:center; color:#94a3b8; font-size:13px; }
.gs-spinner    { width:16px; height:16px; border:2px solid #e2e8f0; border-top-color:#c9a84c; border-radius:50%; animation:gsSpin .6s linear infinite; }
@keyframes gsSpin { to { transform:rotate(360deg); } }
.tb-icon-btn {
    position: relative; width: 38px; height: 38px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    background: transparent; border: none; cursor: pointer; color: #64748b; transition: all .18s ease;
}
.tb-icon-btn:hover { background: #f1f5f9; color: #0f172a; }
.tb-icon-btn:focus { outline: none; }
.tb-notif-badge {
    position: absolute; top: 4px; inset-inline-start: 4px;
    min-width: 16px; height: 16px; border-radius: 10px;
    background: linear-gradient(135deg,#ef4444,#f97316);
    color: #fff; font-size: 9px; font-weight: 800;
    display: flex; align-items: center; justify-content: center;
    padding: 0 3px; border: 2px solid #fff;
    animation: tbBadgePulse 2s infinite;
}
@keyframes tbBadgePulse {
    0%,100% { box-shadow: 0 0 0 0 rgba(239,68,68,.35); }
    60%     { box-shadow: 0 0 0 5px rgba(239,68,68,.0); }
}
.tb-user-btn {
    display: flex; align-items: center; gap: 9px;
    padding: 5px 12px 5px 8px; border-radius: 12px;
    border: 1.5px solid #e8edf5; outline: none;
    background: #fff; cursor: pointer; transition: all .18s ease; direction: ltr;
}
.tb-user-btn:hover { border-color:#c9a84c; box-shadow:0 2px 14px rgba(201,168,76,.18); background:#fffdf5; }
.tb-avatar {
    width: 34px; height: 34px; border-radius: 50%;
    background: linear-gradient(135deg,#1a2744 0%,#c9a84c 100%);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 13px; font-weight: 800; flex-shrink: 0;
    box-shadow: 0 0 0 2px #fff, 0 0 0 3.5px rgba(201,168,76,.4);
}
.tb-notif-panel {
    position: absolute; top: calc(100% + 10px); inset-inline-start: 0;
    width: 360px; background: #fff; border-radius: 16px;
    box-shadow: 0 20px 60px rgba(15,23,42,.16),0 4px 16px rgba(15,23,42,.08);
    border: 1px solid #e8edf5; z-index: 9999; overflow: hidden;
    transform-origin: top start;
}
.tb-drop-panel {
    position: absolute; inset-inline-start: 0; top: calc(100% + 8px);
    width: 230px; direction: var(--app-dir); background: #fff; border-radius: 14px;
    box-shadow: 0 20px 60px rgba(15,23,42,.16),0 4px 16px rgba(15,23,42,.08);
    border: 1px solid #e8edf5; padding: 6px; z-index: 200;
}
.tb-drop-item {
    display: flex; align-items: center; gap: 9px;
    padding: 9px 11px; border-radius: 8px; font-size: 13px; color: #475569;
    text-decoration: none; transition: background .12s; cursor: pointer;
    border: none; background: transparent; width: 100%; font-family: var(--app-font);
}
.tb-drop-item:hover { background: #f8fafc; }
.tb-drop-item.tb-danger { color: #ef4444; }
.tb-drop-item.tb-danger:hover { background: #fff1f2; }
@media (max-width: 767px) {
    .tb-search-wrap { display: none !important; }
    .tb-fullscreen  { display: none !important; }
    .tb-user-info-text { display: none !important; }
    .tb-chevron     { display: none !important; }
    .tb-user-btn    { padding: 5px; border: 1.5px solid #e8edf5; }
}
</style>

{{-- ── Topbar HTML ── --}}
<div style="display:flex;align-items:center;width:100%;gap:12px;overflow:visible;">

    {{-- ─── 1 · RIGHT: toggle + title ─── --}}
    <div style="display:flex;align-items:center;gap:10px;flex-shrink:0;">

        <button @click="open = !open" class="tb-toggle" title="{{ __('topbar.sidebar_toggle') }}">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <line x1="3" y1="6"  x2="21" y2="6"/>
                <line x1="3" y1="12" x2="21" y2="12"/>
                <line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
        </button>

        <div style="min-width:0;padding-inline-start:10px;border-inline-start:3px solid #c9a84c;">
            <p style="font-size:15px;font-weight:800;color:#0f172a;margin:0;line-height:1.2;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                @yield('title', __('nav.dashboard'))
            </p>
            <div class="tb-breadcrumb">
                <a href="{{ route('admin.dashboard') }}">
                    <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;margin-inline-end:2px;">
                        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>{{ __('common.home') }}
                </a>
                <span class="crumb-sep">›</span>
                <span class="crumb-cur">@yield('title', __('nav.dashboard'))</span>
            </div>
        </div>
    </div>

    {{-- ─── 2 · CENTER: global search ─── --}}
    <div class="topbar-search-wrap tb-search-wrap" style="flex:1;display:flex;align-items:center;justify-content:center;min-width:0;padding:0 8px;">
        <div style="position:relative;width:100%;max-width:480px;"
             x-data="{
                query: '',
                results: [],
                loading: false,
                open: false,
                debounce: null,
                searchUrl: '{{ route('admin.search') }}',
                async doSearch() {
                    if (this.query.length < 2) { this.results = []; this.open = false; return; }
                    this.loading = true; this.open = true;
                    try {
                        const abort = new AbortController();
                        const timer = setTimeout(() => abort.abort(), 6000);
                        const res = await fetch(this.searchUrl + '?q=' + encodeURIComponent(this.query), {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' },
                            signal: abort.signal
                        });
                        clearTimeout(timer);
                        if (!res.ok) throw new Error('HTTP ' + res.status);
                        const data = await res.json();
                        this.results = data.results ?? [];
                    } catch(e) {
                        this.results = [];
                    } finally {
                        this.loading = false;
                    }
                },
                onInput() {
                    clearTimeout(this.debounce);
                    this.debounce = setTimeout(() => this.doSearch(), 320);
                },
                icons: {
                    worker:   { bg:'#fdf4ff', icon:'👩' },
                    client:   { bg:'#f0fdf4', icon:'🧑' },
                    contract: { bg:'#faf5ff', icon:'📄' },
                    agent:    { bg:'#fff7ed', icon:'🤝' },
                },
                labelColors: {
                    worker:   { bg:'#f3e8ff', color:'#9333ea' },
                    client:   { bg:'#dcfce7', color:'#16a34a' },
                    contract: { bg:'#f3e8ff', color:'#9333ea' },
                    agent:    { bg:'#fed7aa', color:'#c2410c' },
                }
             }"
             @click.outside="open = false"
             @keydown.escape="open = false">

            {{-- Icon + spinner --}}
            <div style="position:absolute;right:12px;top:50%;transform:translateY(-50%);pointer-events:none;z-index:1;">
                <div x-show="loading" class="gs-spinner"></div>
                <svg x-show="!loading" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2">
                    <circle cx="11" cy="11" r="8" fill="none"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
            </div>

            {{-- Clear button --}}
            <button x-show="query.length > 0" @click="query='';results=[];open=false;"
                    style="position:absolute;left:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8;line-height:1;font-size:16px;padding:0;z-index:1;"
                    title="{{ __('common.actions.clear') }}">×</button>

            <input type="text"
                   x-model="query"
                   @input="onInput()"
                   @focus="if(results.length) open=true"
                   @keydown.arrow-down.prevent="$refs.dropdown?.querySelector('a')?.focus()"
                   placeholder="{{ __('topbar.search.placeholder') }}"
                   class="tb-search-input"
                   autocomplete="off">

            {{-- Dropdown --}}
            <div x-show="open" x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 transform scale-y-95 origin-top"
                 x-transition:enter-end="opacity-100 transform scale-y-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="gs-dropdown" x-ref="dropdown">

                {{-- Loading state --}}
                <div x-show="loading" style="padding:20px 14px;display:flex;align-items:center;gap:10px;color:#64748b;font-size:13px;">
                    <div class="gs-spinner"></div> {{ __('topbar.search.searching') }}
                </div>

                {{-- No results --}}
                <div x-show="!loading && results.length === 0" class="gs-empty">
                    <svg width="32" height="32" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 8px;display:block;">
                        <circle cx="11" cy="11" r="8" fill="none"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    {{ __('topbar.search.no_results') }} "<span x-text="query" style="color:#334155;font-weight:600;"></span>"
                </div>

                {{-- Results --}}
                <template x-if="!loading && results.length > 0">
                    <div>
                        <template x-for="(item, i) in results" :key="i">
                            <a :href="item.url" class="gs-item" @keydown.arrow-down.prevent="$el.nextElementSibling?.focus()" @keydown.arrow-up.prevent="$el.previousElementSibling?.focus() || $refs.dropdown?.previousElementSibling?.focus()">
                                <div class="gs-item-icon" :style="`background:${icons[item.type]?.bg ?? '#f1f5f9'}`">
                                    <span x-text="icons[item.type]?.icon ?? '🔍'"></span>
                                </div>
                                <div style="flex:1;min-width:0;">
                                    <p class="gs-item-title" x-text="item.title"></p>
                                    <p class="gs-item-sub" x-text="item.subtitle" x-show="item.subtitle"></p>
                                </div>
                                <span class="gs-badge"
                                      :style="`background:${labelColors[item.type]?.bg ?? '#f1f5f9'};color:${labelColors[item.type]?.color ?? '#475569'}`"
                                      x-text="item.label"></span>
                            </a>
                        </template>
                        <div style="padding:8px 14px;font-size:11px;color:#94a3b8;text-align:center;border-top:1px solid #f1f5f9;">
                            <span x-text="results.length"></span> {{ __('topbar.search.result') }} — {{ __('topbar.search.press') }} Enter {{ __('topbar.search.full_search') }}
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- ─── 3 · LEFT: icons + user ─── --}}
    <div style="display:flex;align-items:center;gap:4px;flex-shrink:0;direction:ltr;">

        {{-- Fullscreen --}}
        <button class="tb-icon-btn tb-fullscreen topbar-fullscreen"
                onclick="document.fullscreenElement ? document.exitFullscreen() : document.documentElement.requestFullscreen()"
                title="{{ __('topbar.fullscreen') }}">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                <path d="M8 3H5a2 2 0 00-2 2v3m18 0V5a2 2 0 00-2-2h-3m0 18h3a2 2 0 002-2v-3M3 16v3a2 2 0 002 2h3"/>
            </svg>
        </button>

        {{-- Notifications --}}
        <div x-data="{ notifOpen: false }" style="position:relative;">
            <button @click="notifOpen = !notifOpen" class="tb-icon-btn" title="{{ __('topbar.notifications.title') }}">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                    <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 01-3.46 0"/>
                </svg>
                @if($unreadNotifCount > 0)
                    <span class="tb-notif-badge">{{ $unreadNotifCount > 99 ? '99+' : $unreadNotifCount }}</span>
                @endif
            </button>

            <div x-show="notifOpen"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 @click.outside="notifOpen = false"
                 class="tb-notif-panel"
                 x-cloak>

                <div style="display:flex;align-items:center;justify-content:space-between;
                            padding:14px 16px 12px;
                            background:linear-gradient(to bottom,#fafafa,#fff);
                            border-bottom:1px solid #f1f5f9;">
                    <div style="display:flex;align-items:center;gap:9px;">
                        <div style="width:32px;height:32px;border-radius:9px;
                                    background:linear-gradient(135deg,#1a2744,#c9a84c);
                                    display:flex;align-items:center;justify-content:center;">
                            <svg width="14" height="14" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                                <path d="M13.73 21a2 2 0 01-3.46 0"/>
                            </svg>
                        </div>
                        <div>
                            <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0;line-height:1.2;">{{ __('topbar.notifications.title') }}</p>
                            @if($unreadNotifCount > 0)
                            <p style="font-size:11px;color:#94a3b8;margin:0;">{{ $unreadNotifCount }} {{ __('topbar.notifications.unread') }}</p>
                            @endif
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:6px;">
                        @if($unreadNotifCount > 0)
                        <form method="POST" action="{{ route('admin.notifications.read-all') }}" style="margin:0;">
                            @csrf
                            <button type="submit"
                                    style="font-size:11px;color:#c9a84c;background:#fdf8e8;
                                           border:1px solid #fde68a;border-radius:6px;
                                           cursor:pointer;padding:3px 8px;font-family:Cairo,sans-serif;font-weight:600;"
                                    onmouseover="this.style.background='#fef3c7'"
                                    onmouseout="this.style.background='#fdf8e8'">✓ {{ __('topbar.notifications.mark_all') }}</button>
                        </form>
                        @endif
                        <a href="{{ route('admin.notifications.index') }}"
                           style="font-size:11px;color:#64748b;text-decoration:none;
                                  padding:3px 8px;border-radius:6px;background:#f1f5f9;font-weight:500;"
                           onmouseover="this.style.background='#e2e8f0'"
                           onmouseout="this.style.background='#f1f5f9'">{{ __('topbar.notifications.view_all') }}</a>
                    </div>
                </div>

                <div style="max-height:360px;overflow-y:auto;">
                    @forelse($recentNotifs as $notif)
                    <a href="{{ route('admin.notifications.read', $notif->id) }}"
                       style="display:flex;align-items:flex-start;gap:12px;padding:12px 16px;
                              text-decoration:none;border-bottom:1px solid #f8fafc;
                              background:{{ $notif->isRead() ? '#fff' : '#f5f9ff' }};transition:background .15s;"
                       onmouseover="this.style.background='#f8fafc'"
                       onmouseout="this.style.background='{{ $notif->isRead() ? '#fff' : '#f5f9ff' }}'">
                        <div style="width:38px;height:38px;border-radius:10px;flex-shrink:0;
                                    background:{{ $notif->icon_bg }};color:{{ $notif->icon_color }};
                                    display:flex;align-items:center;justify-content:center;">
                            {!! $notif->icon_svg !!}
                        </div>
                        <div style="flex:1;min-width:0;">
                            <p style="font-size:13px;font-weight:{{ $notif->isRead() ? '500' : '700' }};
                                      color:#0f172a;margin:0 0 2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                {{ $notif->title }}
                            </p>
                            <p style="font-size:11.5px;color:#64748b;margin:0 0 4px;
                                      white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                {{ $notif->body }}
                            </p>
                            <p style="font-size:10.5px;color:#94a3b8;margin:0;">{{ $notif->created_at->diffForHumans() }}</p>
                        </div>
                        @if(!$notif->isRead())
                        <div style="width:8px;height:8px;border-radius:50%;
                                    background:linear-gradient(135deg,#c9a84c,#f59e0b);
                                    flex-shrink:0;margin-top:6px;
                                    box-shadow:0 0 0 3px rgba(201,168,76,.2);"></div>
                        @endif
                    </a>
                    @empty
                    <div style="padding:40px 16px;text-align:center;">
                        <div style="width:52px;height:52px;border-radius:14px;background:#f8fafc;
                                    margin:0 auto 12px;display:flex;align-items:center;justify-content:center;">
                            <svg width="22" height="22" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24">
                                <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                                <path d="M13.73 21a2 2 0 01-3.46 0"/>
                            </svg>
                        </div>
                        <p style="font-size:13px;font-weight:600;color:#cbd5e1;margin:0;">{{ __('topbar.notifications.empty') }}</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Divider --}}
        <div style="width:1px;height:24px;background:#e8edf5;flex-shrink:0;margin:0 2px;"></div>

        {{-- User dropdown --}}
        <div x-data="{ dropOpen: false }" style="position:relative;direction:rtl;">

            <button @click="dropOpen = !dropOpen"
                    class="tb-user-btn"
                    :style="dropOpen ? 'border-color:#c9a84c;box-shadow:0 2px 14px rgba(201,168,76,.2);background:#fffdf5;' : ''">
                <div class="tb-avatar">{{ $initials }}</div>
                <div class="tb-user-info-text" style="text-align:right;line-height:1.3;flex-shrink:0;">
                    <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0;white-space:nowrap;">
                        {{ $authAdmin?->name ?? __('topbar.manager') }}
                    </p>
                    <p style="font-size:11px;color:#94a3b8;margin:0;white-space:nowrap;">
                        {{ $authAdmin?->roles->first()?->name ?? __('nav.user.super_admin') }}
                    </p>
                </div>
                <svg class="tb-chevron" width="11" height="11" fill="none"
                     stroke="#94a3b8" stroke-width="2.5" viewBox="0 0 24 24"
                     :style="dropOpen ? 'transform:rotate(180deg)' : 'transform:rotate(0deg)'"
                     style="flex-shrink:0;transition:transform .2s ease;">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </button>

            <div x-show="dropOpen"
                 @click.outside="dropOpen = false"
                 x-cloak
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="tb-drop-panel">

                {{-- User header card --}}
                <div style="padding:12px 14px;border-radius:10px;margin:2px 2px 6px;
                            background:linear-gradient(135deg,#1a2744 0%,#2d3f6b 100%);">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:40px;height:40px;border-radius:12px;
                                    background:linear-gradient(135deg,#c9a84c,#f0c060);
                                    display:flex;align-items:center;justify-content:center;
                                    color:#fff;font-size:16px;font-weight:800;flex-shrink:0;">
                            {{ $initials }}
                        </div>
                        <div>
                            <p style="font-size:13px;font-weight:700;color:#fff;margin:0 0 2px;">{{ $authAdmin?->name }}</p>
                            <p style="font-size:11px;color:#94a3b8;margin:0;">{{ $authAdmin?->roles->first()?->name ?? __('topbar.system_admin') }}</p>
                        </div>
                    </div>
                </div>

                <a href="{{ route('admin.settings.admins.index') }}" class="tb-drop-item">
                    <div style="width:28px;height:28px;border-radius:8px;background:#f1f5f9;
                                display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="13" height="13" fill="none" stroke="#475569" stroke-width="1.8" viewBox="0 0 24 24">
                            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
                    {{ __('nav.user.profile') }}
                </a>

                <div style="height:1px;background:#f1f5f9;margin:3px 0;"></div>

                {{-- مبدّل اللغة --}}
                <div style="padding:6px 10px 2px;font-size:11px;color:#94a3b8;font-weight:600;">
                    {{ __('common.language') }}
                </div>
                @foreach(config('locales.supported') as $code => $loc)
                <a href="{{ route('locale.switch', $code) }}" class="tb-drop-item"
                   style="{{ app()->getLocale() === $code ? 'background:#fdf8e8;color:#a88830;font-weight:600;' : '' }}">
                    <div style="width:28px;height:28px;border-radius:8px;background:#f8fafc;
                                display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:14px;">
                        {{ $loc['flag'] }}
                    </div>
                    {{ $loc['native'] }}
                    @if(app()->getLocale() === $code)
                        <span style="margin-inline-start:auto;">✓</span>
                    @endif
                </a>
                @endforeach

                <div style="height:1px;background:#f1f5f9;margin:3px 0;"></div>

                <form action="{{ route('admin.logout') }}" method="POST" style="margin:0;">
                    @csrf
                    <button type="submit" class="tb-drop-item tb-danger">
                        <div style="width:28px;height:28px;border-radius:8px;background:#fff1f2;
                                    display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="13" height="13" fill="none" stroke="#ef4444" stroke-width="1.8" viewBox="0 0 24 24">
                                <path d="M17 16l4-4m0 0l-4-4m4 4H7"/>
                                <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                            </svg>
                        </div>
                        {{ __('nav.user.logout') }}
                    </button>
                </form>
            </div>
        </div>{{-- /user dropdown --}}

    </div>{{-- /section 3 --}}
</div>{{-- /topbar --}}
