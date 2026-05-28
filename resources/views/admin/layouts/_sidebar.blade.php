@php
    $admin = auth('admin')->user();
    $openSettings   = request()->routeIs(['admin.branches.*', 'admin.cities.*', 'admin.settings.*', 'admin.nationalities.*', 'admin.airports.*']);
    $openFinance    = request()->routeIs(['admin.income-types.*','admin.expense-types.*','admin.incomes.*','admin.expenses.*','admin.transfers.*','admin.reports.*']);
    $openAccounting = request()->routeIs(['admin.income-types.*', 'admin.expense-types.*']);
    $openMoney      = request()->routeIs(['admin.incomes.*', 'admin.expenses.*', 'admin.transfers.*']);
    $openReports    = request()->routeIs(['admin.reports.*']);
    $openPeople     = request()->routeIs(['admin.clients.*', 'admin.agents.*']);
    $openWorkers    = request()->routeIs(['admin.workers.*']);
    $openContracts  = request()->routeIs(['admin.contracts.*', 'admin.reports.contracts-*']);
    $openCReps      = request()->routeIs(['admin.reports.contracts-*']);
    $openMarketing  = request()->routeIs(['admin.marketing.*']);
@endphp

<div x-data="{
        s: {{ $openSettings   ? 'true' : 'false' }},
        f: {{ $openFinance    ? 'true' : 'false' }},
        a: {{ $openAccounting ? 'true' : 'false' }},
        m: {{ $openMoney      ? 'true' : 'false' }},
        r: {{ $openReports    ? 'true' : 'false' }},
        p: {{ $openPeople     ? 'true' : 'false' }},
        w: {{ $openWorkers    ? 'true' : 'false' }},
        c: {{ $openContracts  ? 'true' : 'false' }},
        cr: {{ $openCReps     ? 'true' : 'false' }},
        mk: {{ $openMarketing ? 'true' : 'false' }}
     }"
     style="display:flex;flex-direction:column;height:100%;overflow:hidden;">

    {{-- Logo --}}
    <div style="padding:0 16px;height:64px;display:flex;align-items:center;gap:12px;
                border-bottom:1px solid rgba(255,255,255,.06);flex-shrink:0;">
        <div style="width:36px;height:36px;border-radius:9px;
                    background:linear-gradient(135deg,#2563eb,#1d4ed8);
                    display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <path d="M12 2L3 7V17L12 22L21 17V7L12 2Z" stroke="white" stroke-width="2" stroke-linejoin="round"/>
                <path d="M12 2V22M3 7L12 12M21 7L12 12" stroke="white" stroke-width="1.6"/>
            </svg>
        </div>
        <div>
            <p style="color:#f1f5f9;font-size:13px;font-weight:700;line-height:1.2;margin:0;">نظام الامتياز</p>
            <p style="color:#475569;font-size:10px;line-height:1.3;margin:0;">للاستقدام</p>
        </div>
    </div>

    {{-- Scrollable nav --}}
    <nav style="flex:1;overflow-y:auto;overflow-x:hidden;padding:10px 8px 8px;
                scrollbar-width:thin;scrollbar-color:#1e293b transparent;">

        {{-- Dashboard --}}
        <a href="{{ route('admin.dashboard') }}"
           style="display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:8px;
                  text-decoration:none;font-size:13px;font-weight:500;margin-bottom:6px;
                  {{ request()->routeIs('admin.dashboard') ? 'color:#fff;background:#2563eb;' : 'color:#94a3b8;background:transparent;' }}"
           onmouseover="if(!this.dataset.active){this.style.background='rgba(255,255,255,.06)';this.style.color='#e2e8f0';}"
           onmouseout="if(!this.dataset.active){this.style.background='transparent';this.style.color='#94a3b8';}"
           {{ request()->routeIs('admin.dashboard') ? 'data-active=1' : '' }}>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;">
                <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                <rect x="14" y="14" width="7" height="7" rx="1.5"/>
            </svg>
            لوحة التحكم
        </a>

        {{-- Section label: الإعدادات --}}
        <div style="padding:10px 10px 4px;">
            <p style="font-size:10px;font-weight:700;letter-spacing:.06em;
                      text-transform:uppercase;color:#334155;margin:0;">الإعدادات</p>
        </div>

        {{-- ── GROUP 1: الإعدادات العامة ── --}}
        <div style="margin-bottom:1px;">
            <button @click="s=!s"
                    style="width:100%;display:flex;align-items:center;gap:10px;padding:9px 12px;
                           border-radius:8px;border:none;cursor:pointer;text-align:right;
                           font-family:Cairo,sans-serif;font-size:13px;font-weight:600;
                           transition:background .15s,color .15s;"
                    :style="{ color: s ? '#e2e8f0' : '#64748b', background: s ? 'rgba(255,255,255,.05)' : 'transparent' }"
                    @mouseenter="$el.style.background='rgba(255,255,255,.06)';$el.style.color='#e2e8f0';"
                    @mouseleave="$el.style.background=s?'rgba(255,255,255,.05)':'transparent';$el.style.color=s?'#e2e8f0':'#64748b';">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;">
                    <circle cx="12" cy="8" r="4"/><path d="M6 20v-2a4 4 0 014-4h4a4 4 0 014 4v2"/>
                </svg>
                <span style="flex:1;">الإعدادات العامة</span>
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                     style="flex-shrink:0;transition:transform .25s;" :style="{ transform: s ? 'rotate(180deg)' : 'none' }">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </button>
            <div x-show="s" x-collapse style="overflow:hidden;padding:2px 0 2px 6px;">
                @php $items = [
                    ['r'=>'admin.branches.index',        'p'=>'admin.branches.*',        'l'=>'الفروع',              'd'=>'M3 21h18M3 7l9-4 9 4M4 7v14M20 7v14M9 21V11h6v10'],
                    ['r'=>'admin.cities.index',          'p'=>'admin.cities.*',          'l'=>'المدن',               'd'=>'M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z'],
                    ['r'=>'admin.nationalities.index',   'p'=>'admin.nationalities.*',   'l'=>'الجنسيات',            'd'=>'M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6H10.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9'],
                    ['r'=>'admin.airports.index',        'p'=>'admin.airports.*',        'l'=>'المطارات',            'd'=>'M2.5 19h19M6.5 12.5L4 19 M17.5 12.5L20 19 M12 3L6.5 12.5h11L12 3z'],
                    ['r'=>'admin.settings.roles.index',  'p'=>'admin.settings.roles.*',  'l'=>'الأدوار والصلاحيات', 'd'=>'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z'],
                    ['r'=>'admin.settings.admins.index', 'p'=>'admin.settings.admins.*', 'l'=>'المديرين',            'd'=>'M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2 M9 7a4 4 0 100 8 4 4 0 000-8z'],
                ] @endphp
                @foreach($items as $it)
                    @php $on = request()->routeIs($it['p']); @endphp
                    <a href="{{ route($it['r']) }}"
                       style="display:flex;align-items:center;gap:9px;padding:7px 10px;margin:1px 0;
                              border-radius:7px;text-decoration:none;font-size:12.5px;
                              {{ $on ? 'color:#60a5fa;background:rgba(96,165,250,.1);border-right:2px solid #2563eb;' : 'color:#64748b;background:transparent;border-right:2px solid transparent;' }}"
                       onmouseover="if(!this.dataset.on){this.style.background='rgba(255,255,255,.05)';this.style.color='#cbd5e1';}"
                       onmouseout="if(!this.dataset.on){this.style.background='transparent';this.style.color='#64748b';}"
                       {{ $on ? 'data-on=1' : '' }}>
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;">
                            <path d="{{ $it['d'] }}"/>
                        </svg>
                        {{ $it['l'] }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Section label: المالية --}}
        <div style="padding:10px 10px 4px;margin-top:4px;">
            <p style="font-size:10px;font-weight:700;letter-spacing:.06em;
                      text-transform:uppercase;color:#334155;margin:0;">المالية</p>
        </div>

        {{-- ── OUTER: القسم المالي ── --}}
        <div style="margin-bottom:1px;">
            <button @click="f=!f"
                    style="width:100%;display:flex;align-items:center;gap:10px;padding:9px 12px;
                           border-radius:8px;border:none;cursor:pointer;text-align:right;
                           font-family:Cairo,sans-serif;font-size:13px;font-weight:700;
                           transition:background .15s,color .15s;"
                    :style="{ color: f ? '#e2e8f0' : '#64748b', background: f ? 'rgba(255,255,255,.07)' : 'transparent' }"
                    @mouseenter="$el.style.background='rgba(255,255,255,.07)';$el.style.color='#e2e8f0';"
                    @mouseleave="$el.style.background=f?'rgba(255,255,255,.07)':'transparent';$el.style.color=f?'#e2e8f0':'#64748b';">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;">
                    <rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/>
                </svg>
                <span style="flex:1;">القسم المالي</span>
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                     style="flex-shrink:0;transition:transform .25s;" :style="{ transform: f ? 'rotate(180deg)' : 'none' }">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </button>

            <div x-show="f" x-collapse style="overflow:hidden;">
                <div style="padding:4px 0 4px 10px;border-right:2px solid rgba(255,255,255,.06);margin-right:12px;">

                    {{-- SUB 1: إعدادات المحاسبة --}}
                    <div style="margin-bottom:1px;">
                        <button @click="a=!a"
                                style="width:100%;display:flex;align-items:center;gap:9px;padding:7px 10px;
                                       border-radius:7px;border:none;cursor:pointer;text-align:right;
                                       font-family:Cairo,sans-serif;font-size:12.5px;font-weight:600;
                                       transition:background .15s,color .15s;"
                                :style="{ color: a ? '#cbd5e1' : '#64748b', background: a ? 'rgba(255,255,255,.04)' : 'transparent' }"
                                @mouseenter="$el.style.background='rgba(255,255,255,.05)';$el.style.color='#cbd5e1';"
                                @mouseleave="$el.style.background=a?'rgba(255,255,255,.04)':'transparent';$el.style.color=a?'#cbd5e1':'#64748b';">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;">
                                <rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/>
                            </svg>
                            <span style="flex:1;">إعدادات المحاسبة</span>
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                 style="flex-shrink:0;transition:transform .25s;" :style="{ transform: a ? 'rotate(180deg)' : 'none' }">
                                <polyline points="6 9 12 15 18 9"/>
                            </svg>
                        </button>
                        <div x-show="a" x-collapse style="overflow:hidden;padding:2px 0 2px 6px;">
                            @php $acctItems = [
                                ['r'=>'admin.income-types.index',  'p'=>'admin.income-types.*',  'l'=>'أنواع الإيرادات',  'd'=>'M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6'],
                                ['r'=>'admin.expense-types.index', 'p'=>'admin.expense-types.*', 'l'=>'أنواع المصروفات', 'd'=>'M3 6h18M3 12h18M3 18h18'],
                            ] @endphp
                            @foreach($acctItems as $it)
                                @php $on = request()->routeIs($it['p']); @endphp
                                <a href="{{ route($it['r']) }}"
                                   style="display:flex;align-items:center;gap:8px;padding:6px 10px;margin:1px 0;
                                          border-radius:6px;text-decoration:none;font-size:12px;
                                          {{ $on ? 'color:#60a5fa;background:rgba(96,165,250,.1);border-right:2px solid #2563eb;' : 'color:#64748b;background:transparent;border-right:2px solid transparent;' }}"
                                   onmouseover="if(!this.dataset.on){this.style.background='rgba(255,255,255,.05)';this.style.color='#cbd5e1';}"
                                   onmouseout="if(!this.dataset.on){this.style.background='transparent';this.style.color='#64748b';}"
                                   {{ $on ? 'data-on=1' : '' }}>
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;"><path d="{{ $it['d'] }}"/></svg>
                                    {{ $it['l'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- SUB 2: المالية --}}
                    <div style="margin-bottom:1px;">
                        <button @click="m=!m"
                                style="width:100%;display:flex;align-items:center;gap:9px;padding:7px 10px;
                                       border-radius:7px;border:none;cursor:pointer;text-align:right;
                                       font-family:Cairo,sans-serif;font-size:12.5px;font-weight:600;
                                       transition:background .15s,color .15s;"
                                :style="{ color: m ? '#cbd5e1' : '#64748b', background: m ? 'rgba(255,255,255,.04)' : 'transparent' }"
                                @mouseenter="$el.style.background='rgba(255,255,255,.05)';$el.style.color='#cbd5e1';"
                                @mouseleave="$el.style.background=m?'rgba(255,255,255,.04)':'transparent';$el.style.color=m?'#cbd5e1':'#64748b';">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;">
                                <rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/>
                            </svg>
                            <span style="flex:1;">المالية</span>
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                 style="flex-shrink:0;transition:transform .25s;" :style="{ transform: m ? 'rotate(180deg)' : 'none' }">
                                <polyline points="6 9 12 15 18 9"/>
                            </svg>
                        </button>
                        <div x-show="m" x-collapse style="overflow:hidden;padding:2px 0 2px 6px;">
                            @php $finItems = [
                                ['r'=>'admin.incomes.index',   'p'=>'admin.incomes.*',   'l'=>'الإيرادات',             'd'=>'M22 7L13.5 15.5L8.5 10.5L2 17 M16 7h6v6'],
                                ['r'=>'admin.expenses.index',  'p'=>'admin.expenses.*',  'l'=>'المصروفات',             'd'=>'M22 17L13.5 8.5L8.5 13.5L2 7 M16 17h6v-6'],
                                ['r'=>'admin.transfers.index', 'p'=>'admin.transfers.*', 'l'=>'التحويلات بين الفروع', 'd'=>'M7 16l-4-4 4-4 M17 8l4 4-4 4 M14 4l-4 16'],
                            ] @endphp
                            @foreach($finItems as $it)
                                @php $on = request()->routeIs($it['p']); @endphp
                                <a href="{{ route($it['r']) }}"
                                   style="display:flex;align-items:center;gap:8px;padding:6px 10px;margin:1px 0;
                                          border-radius:6px;text-decoration:none;font-size:12px;
                                          {{ $on ? 'color:#60a5fa;background:rgba(96,165,250,.1);border-right:2px solid #2563eb;' : 'color:#64748b;background:transparent;border-right:2px solid transparent;' }}"
                                   onmouseover="if(!this.dataset.on){this.style.background='rgba(255,255,255,.05)';this.style.color='#cbd5e1';}"
                                   onmouseout="if(!this.dataset.on){this.style.background='transparent';this.style.color='#64748b';}"
                                   {{ $on ? 'data-on=1' : '' }}>
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;"><path d="{{ $it['d'] }}"/></svg>
                                    {{ $it['l'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- SUB 3: التقارير --}}
                    <div style="margin-bottom:1px;">
                        <button @click="r=!r"
                                style="width:100%;display:flex;align-items:center;gap:9px;padding:7px 10px;
                                       border-radius:7px;border:none;cursor:pointer;text-align:right;
                                       font-family:Cairo,sans-serif;font-size:12.5px;font-weight:600;
                                       transition:background .15s,color .15s;"
                                :style="{ color: r ? '#cbd5e1' : '#64748b', background: r ? 'rgba(255,255,255,.04)' : 'transparent' }"
                                @mouseenter="$el.style.background='rgba(255,255,255,.05)';$el.style.color='#cbd5e1';"
                                @mouseleave="$el.style.background=r?'rgba(255,255,255,.04)':'transparent';$el.style.color=r?'#cbd5e1':'#64748b';">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;">
                                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                            </svg>
                            <span style="flex:1;">التقارير</span>
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                 style="flex-shrink:0;transition:transform .25s;" :style="{ transform: r ? 'rotate(180deg)' : 'none' }">
                                <polyline points="6 9 12 15 18 9"/>
                            </svg>
                        </button>
                        <div x-show="r" x-collapse style="overflow:hidden;padding:2px 0 2px 6px;">
                            @php $repItems = [
                                ['r'=>'admin.reports.branch-statement',   'p'=>'admin.reports.branch-statement',   'l'=>'كشف حساب الفرع',       'd'=>'M9 17v-2m3 2v-4m3 4v-6M5 21h14a2 2 0 002-2V8l-5-5H7a2 2 0 00-2 2v14a2 2 0 002 2z'],
                                ['r'=>'admin.reports.income-statement',   'p'=>'admin.reports.income-statement',   'l'=>'قائمة دخل بين الفروع', 'd'=>'M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z'],
                            ] @endphp
                            @foreach($repItems as $it)
                                @php $on = request()->routeIs($it['p']); @endphp
                                <a href="{{ route($it['r']) }}"
                                   style="display:flex;align-items:center;gap:8px;padding:6px 10px;margin:1px 0;
                                          border-radius:6px;text-decoration:none;font-size:12px;
                                          {{ $on ? 'color:#60a5fa;background:rgba(96,165,250,.1);border-right:2px solid #2563eb;' : 'color:#64748b;background:transparent;border-right:2px solid transparent;' }}"
                                   onmouseover="if(!this.dataset.on){this.style.background='rgba(255,255,255,.05)';this.style.color='#cbd5e1';}"
                                   onmouseout="if(!this.dataset.on){this.style.background='transparent';this.style.color='#64748b';}"
                                   {{ $on ? 'data-on=1' : '' }}>
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;"><path d="{{ $it['d'] }}"/></svg>
                                    {{ $it['l'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Section label: العملاء والوكلاء --}}
        <div style="padding:10px 10px 4px;margin-top:4px;">
            <p style="font-size:10px;font-weight:700;letter-spacing:.06em;
                      text-transform:uppercase;color:#334155;margin:0;">العملاء والوكلاء</p>
        </div>

        {{-- ── GROUP: العملاء والوكلاء ── --}}
        <div style="margin-bottom:1px;">
            <button @click="p=!p"
                    style="width:100%;display:flex;align-items:center;gap:10px;padding:9px 12px;
                           border-radius:8px;border:none;cursor:pointer;text-align:right;
                           font-family:Cairo,sans-serif;font-size:13px;font-weight:600;
                           transition:background .15s,color .15s;"
                    :style="{ color: p ? '#e2e8f0' : '#64748b', background: p ? 'rgba(255,255,255,.05)' : 'transparent' }"
                    @mouseenter="$el.style.background='rgba(255,255,255,.06)';$el.style.color='#e2e8f0';"
                    @mouseleave="$el.style.background=p?'rgba(255,255,255,.05)':'transparent';$el.style.color=p?'#e2e8f0':'#64748b';">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>
                </svg>
                <span style="flex:1;">العملاء والوكلاء</span>
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                     style="flex-shrink:0;transition:transform .25s;" :style="{ transform: p ? 'rotate(180deg)' : 'none' }">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </button>
            <div x-show="p" x-collapse style="overflow:hidden;padding:2px 0 2px 6px;">
                @php $peopleItems = [
                    ['r'=>'admin.clients.index', 'p'=>'admin.clients.*', 'l'=>'العملاء',
                     'd'=>'M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2 M12 7a4 4 0 100 8 4 4 0 000-8z'],
                    ['r'=>'admin.agents.index',  'p'=>'admin.agents.*',  'l'=>'الوكلاء',
                     'd'=>'M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2 M9 7a4 4 0 100 8 4 4 0 000-8z M23 21v-2a4 4 0 00-3-3.87 M16 3.13a4 4 0 010 7.75'],
                ] @endphp
                @foreach($peopleItems as $it)
                    @php $on = request()->routeIs($it['p']); @endphp
                    <a href="{{ route($it['r']) }}"
                       style="display:flex;align-items:center;gap:9px;padding:7px 10px;margin:1px 0;
                              border-radius:7px;text-decoration:none;font-size:12.5px;
                              {{ $on ? 'color:#60a5fa;background:rgba(96,165,250,.1);border-right:2px solid #2563eb;' : 'color:#64748b;background:transparent;border-right:2px solid transparent;' }}"
                       onmouseover="if(!this.dataset.on){this.style.background='rgba(255,255,255,.05)';this.style.color='#cbd5e1';}"
                       onmouseout="if(!this.dataset.on){this.style.background='transparent';this.style.color='#64748b';}"
                       {{ $on ? 'data-on=1' : '' }}>
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;">
                            <path d="{{ $it['d'] }}"/>
                        </svg>
                        {{ $it['l'] }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Section label: التسويق --}}
        <div style="padding:10px 10px 4px;margin-top:4px;">
            <p style="font-size:10px;font-weight:700;letter-spacing:.06em;
                      text-transform:uppercase;color:#334155;margin:0;">التسويق</p>
        </div>

        {{-- ── GROUP: التسويق ── --}}
        <div style="margin-bottom:1px;">
            <button @click="mk=!mk"
                    style="width:100%;display:flex;align-items:center;gap:10px;padding:9px 12px;
                           border-radius:8px;border:none;cursor:pointer;text-align:right;
                           font-family:Cairo,sans-serif;font-size:13px;font-weight:600;
                           transition:background .15s,color .15s;"
                    :style="{ color: mk ? '#e2e8f0' : '#64748b', background: mk ? 'rgba(255,255,255,.05)' : 'transparent' }"
                    @mouseenter="$el.style.background='rgba(255,255,255,.06)';$el.style.color='#e2e8f0';"
                    @mouseleave="$el.style.background=mk?'rgba(255,255,255,.05)':'transparent';$el.style.color=mk?'#e2e8f0':'#64748b';">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;">
                    <path d="M3 11l18-8-8 18-2-8-8-2z"/>
                </svg>
                <span style="flex:1;">التسويق</span>
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                     style="flex-shrink:0;transition:transform .25s;" :style="{ transform: mk ? 'rotate(180deg)' : 'none' }">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </button>
            <div x-show="mk" x-collapse style="overflow:hidden;padding:2px 0 2px 6px;">
                @php $mkItems = [
                    ['r'=>'admin.marketing.campaigns.index', 'p'=>'admin.marketing.campaigns.*', 'l'=>'الحملات',
                     'd'=>'M3 11l18-8-8 18-2-8-8-2z'],
                    ['r'=>'admin.marketing.leads.index',     'p'=>'admin.marketing.leads.*',     'l'=>'العملاء المحتملون',
                     'd'=>'M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2 M9 7a4 4 0 100 8 4 4 0 000-8z M22 11h-6 M19 8v6'],
                    ['r'=>'admin.marketing.reports',         'p'=>'admin.marketing.reports',     'l'=>'تقارير التسويق',
                     'd'=>'M3 3v18h18 M7 14l4-4 4 4 5-5'],
                ] @endphp
                @foreach($mkItems as $it)
                    @php $on = request()->routeIs($it['p']); @endphp
                    <a href="{{ route($it['r']) }}"
                       style="display:flex;align-items:center;gap:9px;padding:7px 10px;margin:1px 0;
                              border-radius:7px;text-decoration:none;font-size:12.5px;
                              {{ $on ? 'color:#60a5fa;background:rgba(96,165,250,.1);border-right:2px solid #2563eb;' : 'color:#64748b;background:transparent;border-right:2px solid transparent;' }}"
                       onmouseover="if(!this.dataset.on){this.style.background='rgba(255,255,255,.05)';this.style.color='#cbd5e1';}"
                       onmouseout="if(!this.dataset.on){this.style.background='transparent';this.style.color='#64748b';}"
                       {{ $on ? 'data-on=1' : '' }}>
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;">
                            <path d="{{ $it['d'] }}"/>
                        </svg>
                        {{ $it['l'] }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Section label: العاملات --}}
        <div style="padding:10px 10px 4px;margin-top:4px;">
            <p style="font-size:10px;font-weight:700;letter-spacing:.06em;
                      text-transform:uppercase;color:#334155;margin:0;">العاملات (CVs)</p>
        </div>

        {{-- ── GROUP: العاملات ── --}}
        <div style="margin-bottom:1px;">
            <button @click="w=!w"
                    style="width:100%;display:flex;align-items:center;gap:10px;padding:9px 12px;
                           border-radius:8px;border:none;cursor:pointer;text-align:right;
                           font-family:Cairo,sans-serif;font-size:13px;font-weight:600;
                           transition:background .15s,color .15s;"
                    :style="{ color: w ? '#e2e8f0' : '#64748b', background: w ? 'rgba(255,255,255,.05)' : 'transparent' }"
                    @mouseenter="$el.style.background='rgba(255,255,255,.06)';$el.style.color='#e2e8f0';"
                    @mouseleave="$el.style.background=w?'rgba(255,255,255,.05)':'transparent';$el.style.color=w?'#e2e8f0':'#64748b';">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;">
                    <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span style="flex:1;">العاملات</span>
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                     style="flex-shrink:0;transition:transform .25s;" :style="{ transform: w ? 'rotate(180deg)' : 'none' }">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </button>
            <div x-show="w" x-collapse style="overflow:hidden;padding:2px 0 2px 6px;">
                @php $workerItems = [
                    ['r'=>'admin.workers.index', 'p'=>'admin.workers.index', 'l'=>'قائمة العاملات',
                     'd'=>'M4 6h16M4 10h16M4 14h8'],
                    ['r'=>'admin.workers.create','p'=>'admin.workers.create','l'=>'إضافة عاملة',
                     'd'=>'M12 5v14M5 12h14'],
                    ['r'=>'admin.workers.bulk',  'p'=>'admin.workers.bulk',  'l'=>'رفع CVs متعددة',
                     'd'=>'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12'],
                ] @endphp
                @foreach($workerItems as $it)
                    @php $on = request()->routeIs($it['p']); @endphp
                    <a href="{{ route($it['r']) }}"
                       style="display:flex;align-items:center;gap:9px;padding:7px 10px;margin:1px 0;
                              border-radius:7px;text-decoration:none;font-size:12.5px;
                              {{ $on ? 'color:#60a5fa;background:rgba(96,165,250,.1);border-right:2px solid #2563eb;' : 'color:#64748b;background:transparent;border-right:2px solid transparent;' }}"
                       onmouseover="if(!this.dataset.on){this.style.background='rgba(255,255,255,.05)';this.style.color='#cbd5e1';}"
                       onmouseout="if(!this.dataset.on){this.style.background='transparent';this.style.color='#64748b';}"
                       {{ $on ? 'data-on=1' : '' }}>
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;">
                            <path d="{{ $it['d'] }}"/>
                        </svg>
                        {{ $it['l'] }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Section label: عقود الاستقدام --}}
        <div style="padding:10px 10px 4px;margin-top:4px;">
            <p style="font-size:10px;font-weight:700;letter-spacing:.06em;
                      text-transform:uppercase;color:#334155;margin:0;">عقود الاستقدام</p>
        </div>

        {{-- ── GROUP: Contracts ── --}}
        <div style="margin-bottom:1px;">
            <button @click="c=!c"
                    style="width:100%;display:flex;align-items:center;gap:10px;padding:9px 12px;
                           border-radius:8px;border:none;cursor:pointer;text-align:right;
                           font-family:Cairo,sans-serif;font-size:13px;font-weight:600;
                           transition:background .15s,color .15s;"
                    :style="{ color: c ? '#e2e8f0' : '#64748b', background: c ? 'rgba(255,255,255,.05)' : 'transparent' }"
                    @mouseenter="$el.style.background='rgba(255,255,255,.06)';$el.style.color='#e2e8f0';"
                    @mouseleave="$el.style.background=c?'rgba(255,255,255,.05)':'transparent';$el.style.color=c?'#e2e8f0':'#64748b';">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;">
                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/>
                </svg>
                <span style="flex:1;">عقود الاستقدام</span>
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                     style="flex-shrink:0;transition:transform .25s;" :style="{ transform: c ? 'rotate(180deg)' : 'none' }">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </button>
            <div x-show="c" x-collapse style="overflow:hidden;padding:2px 0 2px 6px;">
                @php
                    $_su   = Auth::guard('admin')->user();
                    $_sdept = $_su->department;
                    $sidebarCanCreate = ($_su->isSuperAdmin() || $_su->hasPermission('contracts.create'))
                                     && ! in_array($_sdept, ['accounts', 'accountant', 'coordination']);
                    $contractItems = array_filter([
                        ['r'=>'admin.contracts.index',  'p'=>'admin.contracts.index',  'l'=>'قائمة العقود',    'd'=>'M4 6h16M4 10h16M4 14h8'],
                        $sidebarCanCreate ? ['r'=>'admin.contracts.create', 'p'=>'admin.contracts.create', 'l'=>'إضافة عقد جديد', 'd'=>'M12 5v14M5 12h14'] : null,
                    ]);
                @endphp
                @foreach($contractItems as $it)
                    @php $on = request()->routeIs($it['p']); @endphp
                    <a href="{{ route($it['r']) }}"
                       style="display:flex;align-items:center;gap:9px;padding:7px 10px;margin:1px 0;
                              border-radius:7px;text-decoration:none;font-size:12.5px;
                              {{ $on ? 'color:#60a5fa;background:rgba(96,165,250,.1);border-right:2px solid #2563eb;' : 'color:#64748b;background:transparent;border-right:2px solid transparent;' }}"
                       onmouseover="if(!this.dataset.on){this.style.background='rgba(255,255,255,.05)';this.style.color='#cbd5e1';}"
                       onmouseout="if(!this.dataset.on){this.style.background='transparent';this.style.color='#64748b';}"
                       {{ $on ? 'data-on=1' : '' }}>
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;">
                            <path d="{{ $it['d'] }}"/>
                        </svg>
                        {{ $it['l'] }}
                    </a>
                @endforeach

                {{-- ── قسم التقارير (sub-dropdown) — permission-filtered ── --}}
                @php
                    $_crAll = [
                        ['r'=>'admin.reports.contracts-stats',    'perm'=>'reports.contracts-stats',    'l'=>'إحصائيات العقود', 'd'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                        ['r'=>'admin.reports.contracts-received', 'perm'=>'reports.contracts-received', 'l'=>'العمالة المستلمة',  'd'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['r'=>'admin.reports.contracts-delayed',  'perm'=>'reports.contracts-delayed',  'l'=>'العقود المتأخرة',   'd'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ];
                    $_crVisible = array_filter($_crAll, fn($it) => $_su->isSuperAdmin() || $_su->hasPermission($it['perm']));
                @endphp
                @if(count($_crVisible))
                <div style="margin-top:4px;">
                    <button @click="cr=!cr"
                            style="width:100%;display:flex;align-items:center;gap:9px;padding:7px 10px;
                                   border-radius:7px;border:none;cursor:pointer;text-align:right;
                                   font-family:Cairo,sans-serif;font-size:12.5px;font-weight:600;
                                   transition:background .15s,color .15s;"
                            :style="{ color: cr ? '#93c5fd' : '#64748b', background: cr ? 'rgba(96,165,250,.08)' : 'transparent' }"
                            @mouseenter="$el.style.background='rgba(255,255,255,.05)';$el.style.color='#cbd5e1';"
                            @mouseleave="$el.style.background=cr?'rgba(96,165,250,.08)':'transparent';$el.style.color=cr?'#93c5fd':'#64748b';">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;">
                            <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span style="flex:1;">قسم التقارير</span>
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                             style="flex-shrink:0;transition:transform .25s;" :style="{ transform: cr ? 'rotate(180deg)' : 'none' }">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </button>
                    <div x-show="cr" x-collapse style="overflow:hidden;padding:2px 0 2px 6px;">
                        @foreach($_crVisible as $it)
                            @php $on = request()->routeIs($it['r']); @endphp
                            <a href="{{ route($it['r']) }}"
                               style="display:flex;align-items:center;gap:8px;padding:6px 10px;margin:1px 0;
                                      border-radius:6px;text-decoration:none;font-size:12px;
                                      {{ $on ? 'color:#60a5fa;background:rgba(96,165,250,.1);border-right:2px solid #2563eb;' : 'color:#64748b;background:transparent;border-right:2px solid transparent;' }}"
                               onmouseover="if(!this.dataset.on){this.style.background='rgba(255,255,255,.05)';this.style.color='#cbd5e1';}"
                               onmouseout="if(!this.dataset.on){this.style.background='transparent';this.style.color='#64748b';}"
                               {{ $on ? 'data-on=1' : '' }}>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;"><path d="{{ $it['d'] }}"/></svg>
                                {{ $it['l'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

    </nav>

    {{-- User & Logout --}}
    <div style="padding:10px;border-top:1px solid rgba(255,255,255,.06);flex-shrink:0;">
        <div style="display:flex;align-items:center;gap:10px;padding:8px 10px;
                    border-radius:8px;margin-bottom:6px;background:rgba(255,255,255,.04);">
            <div style="width:32px;height:32px;border-radius:50%;
                        background:linear-gradient(135deg,#2563eb,#7c3aed);
                        display:flex;align-items:center;justify-content:center;
                        color:#fff;font-size:13px;font-weight:700;flex-shrink:0;">
                {{ mb_substr($admin?->name ?? 'A', 0, 1) }}
            </div>
            <div style="min-width:0;flex:1;">
                <p style="color:#e2e8f0;font-size:12px;font-weight:600;line-height:1.3;
                          overflow:hidden;text-overflow:ellipsis;white-space:nowrap;margin:0;">
                    {{ $admin?->name }}
                </p>
                <p style="color:#475569;font-size:10px;line-height:1.2;margin:0;">
                    {{ $admin?->roles->first()?->name ?? 'مدير عام' }}
                </p>
            </div>
        </div>
        <form action="{{ route('admin.logout') }}" method="POST" style="margin:0;">
            @csrf
            <button type="submit"
                    style="width:100%;display:flex;align-items:center;gap:9px;padding:8px 12px;
                           border-radius:7px;font-size:12.5px;color:#f87171;border:none;
                           background:transparent;cursor:pointer;font-family:Cairo,sans-serif;
                           transition:background .15s;"
                    onmouseover="this.style.background='rgba(248,113,113,.08)'"
                    onmouseout="this.style.background='transparent'">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M17 16l4-4m0 0l-4-4m4 4H7"/><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                </svg>
                تسجيل خروج
            </button>
        </form>
    </div>

</div>

