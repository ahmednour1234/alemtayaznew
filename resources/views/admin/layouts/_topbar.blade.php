@php
    $authAdmin        = auth('admin')->user();
    $unreadNotifCount = $authAdmin
        ? \App\Models\AdminNotification::where('admin_id', $authAdmin->id)->whereNull('read_at')->count()
        : 0;
    $recentNotifs     = $authAdmin
        ? \App\Models\AdminNotification::where('admin_id', $authAdmin->id)->latest()->limit(8)->get()
        : collect();
@endphp

{{--
  Topbar: single flex row — width:100%; height:72px; align-items:center
  RTL page: right = first element, left = last element
  Sections:
    1. RIGHT  — sidebar toggle + page title/breadcrumb   (flex-shrink:0)
    2. CENTER — search box                               (flex:1, centered)
    3. LEFT   — fullscreen, notifications, user dropdown (flex-shrink:0, direction:ltr)
--}}
<div style="display:flex;align-items:center;width:100%;height:72px;gap:12px;overflow:visible;">

    {{-- ── SECTION 1 · RIGHT (toggle + title) ── --}}
    <div style="display:flex;align-items:center;gap:10px;flex-shrink:0;">

        {{-- Sidebar toggle --}}
        <button @click="open = !open"
                class="icon-btn"
                style="flex-shrink:0;"
                title="القائمة الجانبية">
            <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <line x1="3" y1="6"  x2="21" y2="6"/>
                <line x1="3" y1="12" x2="21" y2="12"/>
                <line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
        </button>

        {{-- Title + breadcrumb --}}
        <div style="min-width:0;">
            <p style="font-size:14px;font-weight:700;color:#0f172a;
                      line-height:1.25;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin:0;">
                @yield('title', 'لوحة التحكم')
            </p>
            <p style="font-size:11px;color:#94a3b8;
                      line-height:1;margin:2px 0 0;white-space:nowrap;">
                الرئيسية / @yield('title', 'لوحة التحكم')
            </p>
        </div>
    </div>

    {{-- ── SECTION 2 · CENTER (search) ── --}}
    <div style="flex:1;display:flex;align-items:center;justify-content:center;min-width:0;padding:0 8px;">
        <div style="position:relative;width:100%;max-width:420px;">
            <svg style="position:absolute;right:10px;top:50%;transform:translateY(-50%);pointer-events:none;"
                 width="14" height="14" viewBox="0 0 24 24" fill="none"
                 stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8" fill="none" stroke="#94a3b8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text"
                   placeholder="ابحث هنا..."
                   class="search-input"
                   style="padding-right:36px;padding-left:12px;">
        </div>
    </div>

    {{-- ── SECTION 3 · LEFT (icons + user) — direction:ltr keeps order predictable ── --}}
    <div style="display:flex;align-items:center;gap:4px;flex-shrink:0;direction:ltr;">

        {{-- Fullscreen --}}
        <button onclick="document.fullscreenElement
                    ? document.exitFullscreen()
                    : document.documentElement.requestFullscreen()"
                class="icon-btn"
                title="ملء الشاشة">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path d="M8 3H5a2 2 0 00-2 2v3m18 0V5a2 2 0 00-2-2h-3m0 18h3a2 2 0 002-2v-3M3 16v3a2 2 0 002 2h3"/>
            </svg>
        </button>

        {{-- Notifications bell + dropdown --}}
        <div x-data="{ notifOpen: false }" style="position:relative;">

            <button @click="notifOpen = !notifOpen"
                    class="icon-btn"
                    style="position:relative;"
                    title="الإشعارات">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 01-3.46 0"/>
                </svg>
                @if($unreadNotifCount > 0)
                    <span style="position:absolute;top:2px;left:2px;
                                 min-width:14px;height:14px;border-radius:8px;
                                 background:#ef4444;color:#fff;
                                 font-size:9px;font-weight:700;line-height:1;
                                 display:flex;align-items:center;justify-content:center;
                                 padding:0 3px;border:2px solid #fff;">
                        {{ $unreadNotifCount > 99 ? '99+' : $unreadNotifCount }}
                    </span>
                @endif
            </button>

            {{-- Dropdown panel --}}
            <div x-show="notifOpen"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 @click.outside="notifOpen = false"
                 style="position:absolute;top:calc(100% + 10px);left:0;
                        width:360px;background:#fff;border-radius:14px;
                        box-shadow:0 8px 32px rgba(15,23,42,.14);
                        border:1px solid #e8edf5;z-index:9999;overflow:hidden;
                        transform-origin:top left;"
                 x-cloak>

                {{-- Header --}}
                <div style="display:flex;align-items:center;justify-content:space-between;
                            padding:14px 16px 10px;border-bottom:1px solid #f1f5f9;">
                    <span style="font-size:14px;font-weight:700;color:#0f172a;">الإشعارات</span>
                    <div style="display:flex;align-items:center;gap:8px;">
                        @if($unreadNotifCount > 0)
                            <form method="POST" action="{{ route('admin.notifications.read-all') }}" style="margin:0;">
                                @csrf
                                <button type="submit"
                                        style="font-size:11px;color:#2563eb;background:none;border:none;
                                               cursor:pointer;padding:2px 6px;border-radius:6px;"
                                        onmouseover="this.style.background='#eff6ff'"
                                        onmouseout="this.style.background='none'">
                                    تحديد الكل كمقروء
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('admin.notifications.index') }}"
                           style="font-size:11px;color:#64748b;text-decoration:none;
                                  padding:2px 6px;border-radius:6px;"
                           onmouseover="this.style.background='#f1f5f9'"
                           onmouseout="this.style.background='none'">
                            عرض الكل
                        </a>
                    </div>
                </div>

                {{-- List --}}
                <div style="max-height:360px;overflow-y:auto;">
                    @forelse($recentNotifs as $notif)
                        <a href="{{ route('admin.notifications.read', $notif->id) }}"
                           style="display:flex;align-items:flex-start;gap:12px;
                                  padding:12px 16px;text-decoration:none;
                                  border-bottom:1px solid #f8fafc;
                                  background:{{ $notif->isRead() ? '#fff' : '#f0f7ff' }};
                                  transition:background .15s;"
                           onmouseover="this.style.background='#f8fafc'"
                           onmouseout="this.style.background='{{ $notif->isRead() ? '#fff' : '#f0f7ff' }}'">

                            {{-- Icon --}}
                            <div style="width:38px;height:38px;border-radius:10px;flex-shrink:0;
                                        background:{{ $notif->icon_bg }};color:{{ $notif->icon_color }};
                                        display:flex;align-items:center;justify-content:center;">
                                {!! $notif->icon_svg !!}
                            </div>

                            {{-- Text --}}
                            <div style="flex:1;min-width:0;">
                                <p style="font-size:13px;font-weight:{{ $notif->isRead() ? '500' : '700' }};
                                          color:#0f172a;margin:0 0 2px;
                                          white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    {{ $notif->title }}
                                </p>
                                <p style="font-size:11.5px;color:#64748b;margin:0 0 4px;
                                          white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    {{ $notif->body }}
                                </p>
                                <p style="font-size:10.5px;color:#94a3b8;margin:0;">
                                    {{ $notif->created_at->diffForHumans() }}
                                </p>
                            </div>

                            {{-- Unread dot --}}
                            @if(! $notif->isRead())
                                <div style="width:8px;height:8px;border-radius:50%;
                                            background:#2563eb;flex-shrink:0;margin-top:5px;"></div>
                            @endif
                        </a>
                    @empty
                        <div style="padding:32px 16px;text-align:center;color:#94a3b8;">
                            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5"
                                 viewBox="0 0 24 24" style="margin:0 auto 8px;display:block;opacity:.4;">
                                <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                                <path d="M13.73 21a2 2 0 01-3.46 0"/>
                            </svg>
                            <p style="font-size:13px;margin:0;">لا توجد إشعارات</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Divider --}}
        <div style="width:1px;height:22px;background:#e2e8f0;margin:0 6px;flex-shrink:0;"></div>

        {{-- User dropdown — uses 'dropOpen' (NOT 'open') to avoid conflict with sidebar --}}
        <div x-data="{ dropOpen: false }" style="position:relative;direction:rtl;">

            <button @click="dropOpen = !dropOpen"
                    style="display:flex;align-items:center;gap:8px;
                           padding:5px 10px 5px 8px;border-radius:10px;
                           border:none;outline:none;-webkit-appearance:none;
                           background:transparent;cursor:pointer;
                           transition:background .15s;direction:ltr;"
                    :style="{ background: dropOpen ? '#f1f5f9' : 'transparent' }"
                    @mouseenter="$el.style.background='#f1f5f9'"
                    @mouseleave="if(!dropOpen) $el.style.background='transparent'">

                {{-- Avatar --}}
                <div style="width:34px;height:34px;border-radius:50%;
                            background:linear-gradient(135deg,#2563eb,#7c3aed);
                            display:flex;align-items:center;justify-content:center;
                            color:#fff;font-size:13px;font-weight:700;flex-shrink:0;">
                    {{ mb_substr($authAdmin?->name ?? 'A', 0, 1) }}
                </div>

                {{-- Name + Role (always visible) --}}
                <div style="text-align:right;line-height:1.3;flex-shrink:0;">
                    <p style="font-size:13px;font-weight:700;color:#0f172a;
                              margin:0;white-space:nowrap;">
                        {{ $authAdmin?->name ?? 'المدير' }}
                    </p>
                    <p style="font-size:11px;color:#94a3b8;margin:0;white-space:nowrap;">
                        {{ $authAdmin?->roles->first()?->name ?? 'مدير عام' }}
                    </p>
                </div>

                {{-- Chevron --}}
                <svg width="11" height="11" fill="none" stroke="#94a3b8" stroke-width="2.5" viewBox="0 0 24 24"
                     style="flex-shrink:0;transition:transform .2s;"
                     :style="dropOpen ? 'transform:rotate(180deg)' : 'transform:rotate(0deg)'">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </button>

            {{-- Dropdown panel --}}
            <div x-show="dropOpen"
                 @click.outside="dropOpen = false"
                 x-cloak
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 style="position:absolute;left:0;top:calc(100% + 8px);
                        width:220px;direction:rtl;
                        background:#fff;border-radius:12px;
                        box-shadow:0 12px 40px rgba(15,23,42,.14),0 2px 8px rgba(15,23,42,.06);
                        border:1px solid #e8edf5;padding:6px;z-index:200;">

                {{-- User info header --}}
                <div style="padding:10px 12px;margin-bottom:2px;border-bottom:1px solid #f1f5f9;">
                    <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 2px;">
                        {{ $authAdmin?->name }}
                    </p>
                    <p style="font-size:11px;color:#94a3b8;margin:0;">
                        {{ $authAdmin?->roles->first()?->name ?? 'مدير النظام' }}
                    </p>
                </div>

                {{-- Profile --}}
                <a href="{{ route('admin.settings.admins.index') }}"
                   style="display:flex;align-items:center;gap:8px;padding:8px 10px;
                          border-radius:8px;font-size:13px;color:#475569;
                          text-decoration:none;transition:background .12s;"
                   onmouseover="this.style.background='#f8fafc'"
                   onmouseout="this.style.background=''">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    الملف الشخصي
                </a>

                {{-- Logout --}}
                <form action="{{ route('admin.logout') }}" method="POST" style="margin:0;">
                    @csrf
                    <button type="submit"
                            style="width:100%;display:flex;align-items:center;gap:8px;
                                   padding:8px 10px;border-radius:8px;
                                   font-size:13px;color:#ef4444;border:none;
                                   background:transparent;cursor:pointer;
                                   font-family:Cairo,sans-serif;transition:background .12s;"
                            onmouseover="this.style.background='#fff1f2'"
                            onmouseout="this.style.background=''">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path d="M17 16l4-4m0 0l-4-4m4 4H7"/>
                            <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                        </svg>
                        تسجيل خروج
                    </button>
                </form>
            </div>
        </div>{{-- /user dropdown --}}
    </div>{{-- /section 3 --}}

</div>{{-- /topbar flex row --}}
