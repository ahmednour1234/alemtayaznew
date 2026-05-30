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
    $openComplaints = request()->routeIs(['admin.complaints.*']);
    $openST         = request()->routeIs(['admin.sponsorship-transfers.*']);
    $openOperations = request()->routeIs(['admin.housing-assignments.*', 'admin.trips.*', 'admin.calendar.*']);
    // Permission helper (arrow fn auto-captures $admin)
    $can = fn(string $perm) => $admin->isSuperAdmin() || $admin->hasPermission($perm);
    // Section group visibility
    $showSettingsGroup   = $can('branches.view')||$can('nationalities.view')||$can('airports.view')||$can('housings.view')||$can('roles.manage')||$can('admins.manage')||$can('income-types.view')||$can('expense-types.view');
    $showFinanceGroup    = $can('incomes.view')||$can('expenses.view')||$can('transfers.view')||$can('reports.view')||$can('income-types.view')||$can('expense-types.view')||$can('reports.branch-statement')||$can('reports.income-statement');
    $showPeopleGroup     = $can('clients.view')||$can('agents.view');
    $showMarketingGroup  = $can('campaigns.view')||$can('leads.view')||$can('marketing.reports.view')||$can('calendar.view');
    $showComplaintsGroup = $can('complaints.view');
    $showOpsGroup        = $can('trips.view')||$can('housing-assignments.view')||$can('calendar.view');
    $showSTGroup         = $can('sponsorship-transfers.view');
    $showWorkersGroup    = $can('workers.view')||$can('workers.create');
    $showContractsGroup  = $can('contracts.view');
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
        mk: {{ $openMarketing ? 'true' : 'false' }},
        cp: {{ $openComplaints ? 'true' : 'false' }},
        op: {{ $openOperations ? 'true' : 'false' }},
        st: {{ $openST ? 'true' : 'false' }}
     }"
     style="display:flex;flex-direction:column;height:100%;overflow:hidden;">

    {{-- Logo --}}
    <div style="flex-shrink:0;display:flex;align-items:center;justify-content:center;
                padding:12px 16px;border-bottom:1px solid rgba(255,255,255,.08);">
        <img src="{{ asset('1759760768-33.png') }}" alt="شركة الامتياز للاستقدام"
             style="height:46px;max-width:190px;object-fit:contain;">
    </div>

    {{-- Scrollable nav --}}
    <nav style="flex:1;overflow-y:auto;overflow-x:hidden;padding:10px 8px 8px;
                scrollbar-width:thin;scrollbar-color:#1e293b transparent;">

        {{-- Dashboard --}}
        <a href="{{ route('admin.dashboard') }}"
           style="display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:8px;
                  text-decoration:none;font-size:13px;font-weight:500;margin-bottom:6px;
                  {{ request()->routeIs('admin.dashboard') ? 'color:#1a2744;background:#c9a84c;' : 'color:#c8d4e3;background:transparent;' }}"
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

        @if($showContractsGroup)
        {{-- Section label: عقود الاستقدام --}}
        <div style="padding:10px 10px 4px;margin-top:4px;">
            <p style="font-size:10px;font-weight:700;letter-spacing:.06em;
                      text-transform:uppercase;color:#8fa3c0;margin:0;">عقود الاستقدام</p>
        </div>

        {{-- -- GROUP: Contracts -- --}}
        <div style="margin-bottom:1px;">
            <button @click="c=!c"
                    style="width:100%;display:flex;align-items:center;gap:10px;padding:9px 12px;
                           border-radius:8px;border:none;cursor:pointer;text-align:right;
                           font-family:Cairo,sans-serif;font-size:13px;font-weight:600;
                           transition:background .15s,color .15s;"
                    :style="{ color: c ? '#e2e8f0' : '#c8d4e3', background: c ? 'rgba(255,255,255,.05)' : 'transparent' }"
                    @mouseenter="$el.style.background='rgba(255,255,255,.06)';$el.style.color='#e2e8f0';"
                    @mouseleave="$el.style.background=c?'rgba(255,255,255,.05)':'transparent';$el.style.color=c?'#e2e8f0':'#c8d4e3';">
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
                              {{ $on ? 'color:#c9a84c;background:rgba(201,168,76,.12);border-right:2px solid #c9a84c;' : 'color:#c8d4e3;background:transparent;border-right:2px solid transparent;' }}"
                       onmouseover="if(!this.dataset.on){this.style.background='rgba(255,255,255,.05)';this.style.color='#cbd5e1';}"
                       onmouseout="if(!this.dataset.on){this.style.background='transparent';this.style.color='#c8d4e3';}"
                       {{ $on ? 'data-on=1' : '' }}>
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;">
                            <path d="{{ $it['d'] }}"/>
                        </svg>
                        {{ $it['l'] }}
                    </a>
                @endforeach

                {{-- -- تقارير العقود (sub-dropdown) – permission-filtered -- --}}
                @php
                    $_crAll = [
                        ['r'=>'admin.reports.contracts-stats',    'perm'=>'reports.contracts-stats',    'l'=>'إحصائيات العقود', 'd'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                        ['r'=>'admin.reports.contracts-received', 'perm'=>'reports.contracts-received', 'l'=>'العقود المستلمة',  'd'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
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
                            :style="{ color: cr ? '#c9a84c' : '#c8d4e3', background: cr ? 'rgba(96,165,250,.08)' : 'transparent' }"
                            @mouseenter="$el.style.background='rgba(255,255,255,.05)';$el.style.color='#cbd5e1';"
                            @mouseleave="$el.style.background=cr?'rgba(201,168,76,.12)':'transparent';$el.style.color=cr?'#c9a84c':'#c8d4e3';">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;">
                            <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span style="flex:1;">تقارير العقود</span>
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
                                      {{ $on ? 'color:#c9a84c;background:rgba(201,168,76,.12);border-right:2px solid #c9a84c;' : 'color:#c8d4e3;background:transparent;border-right:2px solid transparent;' }}"
                               onmouseover="if(!this.dataset.on){this.style.background='rgba(255,255,255,.05)';this.style.color='#cbd5e1';}"
                               onmouseout="if(!this.dataset.on){this.style.background='transparent';this.style.color='#c8d4e3';}"
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
        @endif

        @if($showSTGroup)
        {{-- Section label: نقل الكفالة --}}
        <div style="padding:10px 10px 4px;margin-top:4px;">
            <p style="font-size:10px;font-weight:700;letter-spacing:.06em;
                      text-transform:uppercase;color:#8fa3c0;margin:0;">نقل الكفالة</p>
        </div>

        {{-- -- GROUP: نقل الكفالة -- --}}
        <div style="margin-bottom:1px;">
            <button @click="st=!st"
                    style="width:100%;display:flex;align-items:center;gap:10px;padding:9px 12px;
                           border-radius:8px;border:none;cursor:pointer;text-align:right;
                           font-family:Cairo,sans-serif;font-size:13px;font-weight:600;
                           transition:background .15s,color .15s;"
                    :style="{ color: st ? '#e2e8f0' : '#c8d4e3', background: st ? 'rgba(255,255,255,.05)' : 'transparent' }"
                    @mouseenter="$el.style.background='rgba(255,255,255,.06)';$el.style.color='#e2e8f0';"
                    @mouseleave="$el.style.background=st?'rgba(255,255,255,.05)':'transparent';$el.style.color=st?'#e2e8f0':'#c8d4e3';">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;">
                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span style="flex:1;">نقل الكفالة</span>
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                     style="flex-shrink:0;transition:transform .25s;" :style="{ transform: st ? 'rotate(180deg)' : 'none' }">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </button>
            <div x-show="st" x-collapse style="overflow:hidden;padding:2px 0 2px 6px;">
                @php $stItems = [
                    ['r'=>'admin.sponsorship-transfers.index',   'p'=>'admin.sponsorship-transfers.index',   'l'=>'قائمة نقل الكفالة',    'perm'=>'sponsorship-transfers.view',
                     'd'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ['r'=>'admin.sponsorship-transfers.reports', 'p'=>'admin.sponsorship-transfers.reports', 'l'=>'تقارير نقل الكفالة', 'perm'=>'sponsorship-transfers.view',
                     'd'=>'M3 3v18h18 M7 14l4-4 4 4 5-5'],
                ] @endphp
                @foreach($stItems as $it)
                    @if($can($it['perm']))
                    @php $on = request()->routeIs($it['p']); @endphp
                    <a href="{{ route($it['r']) }}"
                       style="display:flex;align-items:center;gap:9px;padding:7px 10px;margin:1px 0;
                              border-radius:7px;text-decoration:none;font-size:12.5px;
                              {{ $on ? 'color:#c9a84c;background:rgba(201,168,76,.12);border-right:2px solid #c9a84c;' : 'color:#c8d4e3;background:transparent;border-right:2px solid transparent;' }}"
                       onmouseover="if(!this.dataset.on){this.style.background='rgba(255,255,255,.05)';this.style.color='#cbd5e1';}"
                       onmouseout="if(!this.dataset.on){this.style.background='transparent';this.style.color='#c8d4e3';}"
                       {{ $on ? 'data-on=1' : '' }}>
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;">
                            <path d="{{ $it['d'] }}"/>
                        </svg>
                        {{ $it['l'] }}
                    </a>
                    @endif
                @endforeach
            </div>
        </div>
        @endif

        @if($showOpsGroup)
        {{-- Section label: السكن والرحلات --}}
        <div style="padding:10px 10px 4px;margin-top:4px;">
            <p style="font-size:10px;font-weight:700;letter-spacing:.06em;
                      text-transform:uppercase;color:#8fa3c0;margin:0;">السكن والرحلات</p>
        </div>

        {{-- -- GROUP: السكن والرحلات -- --}}
        <div style="margin-bottom:1px;">
            <button @click="op=!op"
                    style="width:100%;display:flex;align-items:center;gap:10px;padding:9px 12px;
                           border-radius:8px;border:none;cursor:pointer;text-align:right;
                           font-family:Cairo,sans-serif;font-size:13px;font-weight:600;
                           transition:background .15s,color .15s;"
                    :style="{ color: op ? '#e2e8f0' : '#c8d4e3', background: op ? 'rgba(255,255,255,.05)' : 'transparent' }"
                    @mouseenter="$el.style.background='rgba(255,255,255,.06)';$el.style.color='#e2e8f0';"
                    @mouseleave="$el.style.background=op?'rgba(255,255,255,.05)':'transparent';$el.style.color=op?'#e2e8f0':'#c8d4e3';">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"/>
                </svg>
                <span style="flex:1;">السكن والرحلات</span>
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                     style="flex-shrink:0;transition:transform .25s;" :style="{ transform: op ? 'rotate(180deg)' : 'none' }">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </button>
            <div x-show="op" x-collapse style="overflow:hidden;padding:2px 0 2px 6px;">
                @php $opItems = [
                    ['r'=>'admin.calendar.index',             'p'=>'admin.calendar.*',             'l'=>'التقويم',          'perm'=>'calendar.view',
                     'd'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['r'=>'admin.trips.index',                'p'=>'admin.trips.*',                'l'=>'الرحلات الدولية',   'perm'=>'trips.view',
                     'd'=>'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064'],
                    ['r'=>'admin.housing-assignments.index',  'p'=>'admin.housing-assignments.*',  'l'=>'الإسكان والتوزيع', 'perm'=>'housing-assignments.view',
                     'd'=>'M3 21h18M3 7l9-4 9 4M4 7v14h16V7M9 21V11h6v10'],
                ] @endphp
                @foreach($opItems as $it)
                    @if($can($it['perm']))
                    @php $on = request()->routeIs($it['p']); @endphp
                    <a href="{{ route($it['r']) }}"
                       style="display:flex;align-items:center;gap:9px;padding:7px 10px;margin:1px 0;
                              border-radius:7px;text-decoration:none;font-size:12.5px;
                              {{ $on ? 'color:#c9a84c;background:rgba(201,168,76,.12);border-right:2px solid #c9a84c;' : 'color:#c8d4e3;background:transparent;border-right:2px solid transparent;' }}"
                       onmouseover="if(!this.dataset.on){this.style.background='rgba(255,255,255,.05)';this.style.color='#cbd5e1';}"
                       onmouseout="if(!this.dataset.on){this.style.background='transparent';this.style.color='#c8d4e3';}"
                       {{ $on ? 'data-on=1' : '' }}>
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;">
                            <path d="{{ $it['d'] }}"/>
                        </svg>
                        {{ $it['l'] }}
                    </a>
                    @endif
                @endforeach
            </div>
        </div>
        @endif

        @if($showFinanceGroup)
        {{-- Section label: المالية --}}
        <div style="padding:10px 10px 4px;margin-top:4px;">
            <p style="font-size:10px;font-weight:700;letter-spacing:.06em;
                      text-transform:uppercase;color:#8fa3c0;margin:0;">المالية</p>
        </div>

        {{-- -- OUTER: الإدارة المالية -- --}}
        <div style="margin-bottom:1px;">
            <button @click="f=!f"
                    style="width:100%;display:flex;align-items:center;gap:10px;padding:9px 12px;
                           border-radius:8px;border:none;cursor:pointer;text-align:right;
                           font-family:Cairo,sans-serif;font-size:13px;font-weight:700;
                           transition:background .15s,color .15s;"
                    :style="{ color: f ? '#e2e8f0' : '#c8d4e3', background: f ? 'rgba(255,255,255,.07)' : 'transparent' }"
                    @mouseenter="$el.style.background='rgba(255,255,255,.07)';$el.style.color='#e2e8f0';"
                    @mouseleave="$el.style.background=f?'rgba(255,255,255,.07)':'transparent';$el.style.color=f?'#e2e8f0':'#c8d4e3';">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;">
                    <rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/>
                </svg>
                <span style="flex:1;">الإدارة المالية</span>
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                     style="flex-shrink:0;transition:transform .25s;" :style="{ transform: f ? 'rotate(180deg)' : 'none' }">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </button>

            <div x-show="f" x-collapse style="overflow:hidden;">
                <div style="padding:4px 0 4px 10px;border-right:2px solid rgba(255,255,255,.06);margin-right:12px;">

                    @if($can('income-types.view') || $can('expense-types.view'))
                    {{-- SUB 1: الإعدادات المحاسبية --}}
                    <div style="margin-bottom:1px;">
                        <button @click="a=!a"
                                style="width:100%;display:flex;align-items:center;gap:9px;padding:7px 10px;
                                       border-radius:7px;border:none;cursor:pointer;text-align:right;
                                       font-family:Cairo,sans-serif;font-size:12.5px;font-weight:600;
                                       transition:background .15s,color .15s;"
                                :style="{ color: a ? '#cbd5e1' : '#c8d4e3', background: a ? 'rgba(255,255,255,.04)' : 'transparent' }"
                                @mouseenter="$el.style.background='rgba(255,255,255,.05)';$el.style.color='#cbd5e1';"
                                @mouseleave="$el.style.background=a?'rgba(255,255,255,.04)':'transparent';$el.style.color=a?'#cbd5e1':'#c8d4e3';">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;">
                                <rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/>
                            </svg>
                            <span style="flex:1;">الإعدادات المحاسبية</span>
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                 style="flex-shrink:0;transition:transform .25s;" :style="{ transform: a ? 'rotate(180deg)' : 'none' }">
                                <polyline points="6 9 12 15 18 9"/>
                            </svg>
                        </button>
                        <div x-show="a" x-collapse style="overflow:hidden;padding:2px 0 2px 6px;">
                            @php $acctItems = [
                                ['r'=>'admin.income-types.index',  'p'=>'admin.income-types.*',  'l'=>'أنواع الإيرادات',  'd'=>'M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6',  'perm'=>'income-types.view'],
                                ['r'=>'admin.expense-types.index', 'p'=>'admin.expense-types.*', 'l'=>'أنواع المصروفات', 'd'=>'M3 6h18M3 12h18M3 18h18',                                    'perm'=>'expense-types.view'],
                            ] @endphp
                            @foreach($acctItems as $it)
                                @if($can($it['perm']))
                                @php $on = request()->routeIs($it['p']); @endphp
                                <a href="{{ route($it['r']) }}"
                                   style="display:flex;align-items:center;gap:8px;padding:6px 10px;margin:1px 0;
                                          border-radius:6px;text-decoration:none;font-size:12px;
                                          {{ $on ? 'color:#c9a84c;background:rgba(201,168,76,.12);border-right:2px solid #c9a84c;' : 'color:#c8d4e3;background:transparent;border-right:2px solid transparent;' }}"
                                   onmouseover="if(!this.dataset.on){this.style.background='rgba(255,255,255,.05)';this.style.color='#cbd5e1';}"
                                   onmouseout="if(!this.dataset.on){this.style.background='transparent';this.style.color='#c8d4e3';}"
                                   {{ $on ? 'data-on=1' : '' }}>
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;"><path d="{{ $it['d'] }}"/></svg>
                                    {{ $it['l'] }}
                                </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($can('incomes.view') || $can('expenses.view') || $can('transfers.view'))
                    {{-- SUB 2: المعاملات --}}
                    <div style="margin-bottom:1px;">
                        <button @click="m=!m"
                                style="width:100%;display:flex;align-items:center;gap:9px;padding:7px 10px;
                                       border-radius:7px;border:none;cursor:pointer;text-align:right;
                                       font-family:Cairo,sans-serif;font-size:12.5px;font-weight:600;
                                       transition:background .15s,color .15s;"
                                :style="{ color: m ? '#cbd5e1' : '#c8d4e3', background: m ? 'rgba(255,255,255,.04)' : 'transparent' }"
                                @mouseenter="$el.style.background='rgba(255,255,255,.05)';$el.style.color='#cbd5e1';"
                                @mouseleave="$el.style.background=m?'rgba(255,255,255,.04)':'transparent';$el.style.color=m?'#cbd5e1':'#c8d4e3';">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;">
                                <rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/>
                            </svg>
                            <span style="flex:1;">المعاملات</span>
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                 style="flex-shrink:0;transition:transform .25s;" :style="{ transform: m ? 'rotate(180deg)' : 'none' }">
                                <polyline points="6 9 12 15 18 9"/>
                            </svg>
                        </button>
                        <div x-show="m" x-collapse style="overflow:hidden;padding:2px 0 2px 6px;">
                            @php $finItems = [
                                ['r'=>'admin.incomes.index',   'p'=>'admin.incomes.*',   'l'=>'الإيرادات',             'd'=>'M22 7L13.5 15.5L8.5 10.5L2 17 M16 7h6v6',      'perm'=>'incomes.view'],
                                ['r'=>'admin.expenses.index',  'p'=>'admin.expenses.*',  'l'=>'المصروفات',             'd'=>'M22 17L13.5 8.5L8.5 13.5L2 7 M16 17h6v-6',     'perm'=>'expenses.view'],
                                ['r'=>'admin.transfers.index', 'p'=>'admin.transfers.*', 'l'=>'التحويلات بين الفروع', 'd'=>'M7 16l-4-4 4-4 M17 8l4 4-4 4 M14 4l-4 16',    'perm'=>'transfers.view'],
                            ] @endphp
                            @foreach($finItems as $it)
                                @if($can($it['perm']))
                                @php $on = request()->routeIs($it['p']); @endphp
                                <a href="{{ route($it['r']) }}"
                                   style="display:flex;align-items:center;gap:8px;padding:6px 10px;margin:1px 0;
                                          border-radius:6px;text-decoration:none;font-size:12px;
                                          {{ $on ? 'color:#c9a84c;background:rgba(201,168,76,.12);border-right:2px solid #c9a84c;' : 'color:#c8d4e3;background:transparent;border-right:2px solid transparent;' }}"
                                   onmouseover="if(!this.dataset.on){this.style.background='rgba(255,255,255,.05)';this.style.color='#cbd5e1';}"
                                   onmouseout="if(!this.dataset.on){this.style.background='transparent';this.style.color='#c8d4e3';}"
                                   {{ $on ? 'data-on=1' : '' }}>
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;"><path d="{{ $it['d'] }}"/></svg>
                                    {{ $it['l'] }}
                                </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($can('reports.branch-statement') || $can('reports.income-statement') || $can('reports.view'))
                    {{-- SUB 3: التقارير --}}
                    <div style="margin-bottom:1px;">
                        <button @click="r=!r"
                                style="width:100%;display:flex;align-items:center;gap:9px;padding:7px 10px;
                                       border-radius:7px;border:none;cursor:pointer;text-align:right;
                                       font-family:Cairo,sans-serif;font-size:12.5px;font-weight:600;
                                       transition:background .15s,color .15s;"
                                :style="{ color: r ? '#cbd5e1' : '#c8d4e3', background: r ? 'rgba(255,255,255,.04)' : 'transparent' }"
                                @mouseenter="$el.style.background='rgba(255,255,255,.05)';$el.style.color='#cbd5e1';"
                                @mouseleave="$el.style.background=r?'rgba(255,255,255,.04)':'transparent';$el.style.color=r?'#cbd5e1':'#c8d4e3';">
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
                                ['r'=>'admin.reports.branch-statement',   'p'=>'admin.reports.branch-statement',   'l'=>'كشف حساب الفرع',       'd'=>'M9 17v-2m3 2v-4m3 4v-6M5 21h14a2 2 0 002-2V8l-5-5H7a2 2 0 00-2 2v14a2 2 0 002 2z', 'perm'=>'reports.branch-statement'],
                                ['r'=>'admin.reports.income-statement',   'p'=>'admin.reports.income-statement',   'l'=>'قائمة دخل كل الفروع', 'd'=>'M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z',                                      'perm'=>'reports.income-statement'],
                            ] @endphp
                            @foreach($repItems as $it)
                                @if($can($it['perm']))
                                @php $on = request()->routeIs($it['p']); @endphp
                                <a href="{{ route($it['r']) }}"
                                   style="display:flex;align-items:center;gap:8px;padding:6px 10px;margin:1px 0;
                                          border-radius:6px;text-decoration:none;font-size:12px;
                                          {{ $on ? 'color:#c9a84c;background:rgba(201,168,76,.12);border-right:2px solid #c9a84c;' : 'color:#c8d4e3;background:transparent;border-right:2px solid transparent;' }}"
                                   onmouseover="if(!this.dataset.on){this.style.background='rgba(255,255,255,.05)';this.style.color='#cbd5e1';}"
                                   onmouseout="if(!this.dataset.on){this.style.background='transparent';this.style.color='#c8d4e3';}"
                                   {{ $on ? 'data-on=1' : '' }}>
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;"><path d="{{ $it['d'] }}"/></svg>
                                    {{ $it['l'] }}
                                </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>
        @endif

        @if($showMarketingGroup)
        {{-- Section label: التسويق --}}
        <div style="padding:10px 10px 4px;margin-top:4px;">
            <p style="font-size:10px;font-weight:700;letter-spacing:.06em;
                      text-transform:uppercase;color:#8fa3c0;margin:0;">التسويق</p>
        </div>

        {{-- -- GROUP: التسويق -- --}}
        <div style="margin-bottom:1px;">
            <button @click="mk=!mk"
                    style="width:100%;display:flex;align-items:center;gap:10px;padding:9px 12px;
                           border-radius:8px;border:none;cursor:pointer;text-align:right;
                           font-family:Cairo,sans-serif;font-size:13px;font-weight:600;
                           transition:background .15s,color .15s;"
                    :style="{ color: mk ? '#e2e8f0' : '#c8d4e3', background: mk ? 'rgba(255,255,255,.05)' : 'transparent' }"
                    @mouseenter="$el.style.background='rgba(255,255,255,.06)';$el.style.color='#e2e8f0';"
                    @mouseleave="$el.style.background=mk?'rgba(255,255,255,.05)':'transparent';$el.style.color=mk?'#e2e8f0':'#c8d4e3';">
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
                    ['r'=>'admin.marketing.campaigns.index', 'p'=>'admin.marketing.campaigns.*', 'l'=>'الحملات',            'perm'=>'campaigns.view',
                     'd'=>'M3 11l18-8-8 18-2-8-8-2z'],
                    ['r'=>'admin.marketing.leads.index',     'p'=>'admin.marketing.leads.*',     'l'=>'العملاء المحتملون', 'perm'=>'leads.view',
                     'd'=>'M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2 M9 7a4 4 0 100 8 4 4 0 000-8z M22 11h-6 M19 8v6'],
                    ['r'=>'admin.marketing.reports',         'p'=>'admin.marketing.reports',     'l'=>'تقارير التسويق',   'perm'=>'marketing.reports.view',
                     'd'=>'M3 3v18h18 M7 14l4-4 4 4 5-5'],
                ] @endphp
                @foreach($mkItems as $it)
                    @if($can($it['perm']))
                    @php $on = request()->routeIs($it['p']); @endphp
                    <a href="{{ route($it['r']) }}"
                       style="display:flex;align-items:center;gap:9px;padding:7px 10px;margin:1px 0;
                              border-radius:7px;text-decoration:none;font-size:12.5px;
                              {{ $on ? 'color:#c9a84c;background:rgba(201,168,76,.12);border-right:2px solid #c9a84c;' : 'color:#c8d4e3;background:transparent;border-right:2px solid transparent;' }}"
                       onmouseover="if(!this.dataset.on){this.style.background='rgba(255,255,255,.05)';this.style.color='#cbd5e1';}"
                       onmouseout="if(!this.dataset.on){this.style.background='transparent';this.style.color='#c8d4e3';}"
                       {{ $on ? 'data-on=1' : '' }}>
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;">
                            <path d="{{ $it['d'] }}"/>
                        </svg>
                        {{ $it['l'] }}
                    </a>
                    @endif
                @endforeach
            </div>
        </div>
        @endif

        @if($showComplaintsGroup)
        {{-- Section label: إدارة الشكاوي --}}
        <div style="padding:10px 10px 4px;margin-top:4px;">
            <p style="font-size:10px;font-weight:700;letter-spacing:.06em;
                      text-transform:uppercase;color:#8fa3c0;margin:0;">إدارة الشكاوي</p>
        </div>

        {{-- -- GROUP: الشكاوي -- --}}
        <div style="margin-bottom:1px;">
            <button @click="cp=!cp"
                    style="width:100%;display:flex;align-items:center;gap:10px;padding:9px 12px;
                           border-radius:8px;border:none;cursor:pointer;text-align:right;
                           font-family:Cairo,sans-serif;font-size:13px;font-weight:600;
                           transition:background .15s,color .15s;"
                    :style="{ color: cp ? '#e2e8f0' : '#c8d4e3', background: cp ? 'rgba(255,255,255,.05)' : 'transparent' }"
                    @mouseenter="$el.style.background='rgba(255,255,255,.06)';$el.style.color='#e2e8f0';"
                    @mouseleave="$el.style.background=cp?'rgba(255,255,255,.05)':'transparent';$el.style.color=cp?'#e2e8f0':'#c8d4e3';">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;">
                    <path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/>
                </svg>
                <span style="flex:1;">الشكاوي</span>
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                     style="flex-shrink:0;transition:transform .25s;" :style="{ transform: cp ? 'rotate(180deg)' : 'none' }">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </button>
            <div x-show="cp" x-collapse style="overflow:hidden;padding:2px 0 2px 6px;">
                @php $cpItems = [
                    ['r'=>'admin.complaints.index',   'p'=>'admin.complaints.index',   'l'=>'كل الشكاوي',      'perm'=>'complaints.view',
                     'd'=>'M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z'],
                    ['r'=>'admin.complaints.create',  'p'=>'admin.complaints.create',  'l'=>'إضافة شكوى',     'perm'=>'complaints.create',
                     'd'=>'M12 4v16m8-8H4'],
                    ['r'=>'admin.complaints.reports', 'p'=>'admin.complaints.reports', 'l'=>'تقارير الشكاوي', 'perm'=>'complaints.reports',
                     'd'=>'M3 3v18h18 M7 14l4-4 4 4 5-5'],
                ] @endphp
                @foreach($cpItems as $it)
                    @if($can($it['perm']))
                    @php $on = request()->routeIs($it['p']); @endphp
                    <a href="{{ route($it['r']) }}"
                       style="display:flex;align-items:center;gap:9px;padding:7px 10px;margin:1px 0;
                              border-radius:7px;text-decoration:none;font-size:12.5px;
                              {{ $on ? 'color:#c9a84c;background:rgba(201,168,76,.12);border-right:2px solid #c9a84c;' : 'color:#c8d4e3;background:transparent;border-right:2px solid transparent;' }}"
                       onmouseover="if(!this.dataset.on){this.style.background='rgba(255,255,255,.05)';this.style.color='#cbd5e1';}"
                       onmouseout="if(!this.dataset.on){this.style.background='transparent';this.style.color='#c8d4e3';}"
                       {{ $on ? 'data-on=1' : '' }}>
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;">
                            <path d="{{ $it['d'] }}"/>
                        </svg>
                        {{ $it['l'] }}
                    </a>
                    @endif
                @endforeach
            </div>
        </div>
        @endif

        @if($showWorkersGroup)
        {{-- Section label: العمالة (CVs) --}}
        <div style="padding:10px 10px 4px;margin-top:4px;">
            <p style="font-size:10px;font-weight:700;letter-spacing:.06em;
                      text-transform:uppercase;color:#8fa3c0;margin:0;">العمالة (CVs)</p>
        </div>

        {{-- -- GROUP: العمالة -- --}}
        <div style="margin-bottom:1px;">
            <button @click="w=!w"
                    style="width:100%;display:flex;align-items:center;gap:10px;padding:9px 12px;
                           border-radius:8px;border:none;cursor:pointer;text-align:right;
                           font-family:Cairo,sans-serif;font-size:13px;font-weight:600;
                           transition:background .15s,color .15s;"
                    :style="{ color: w ? '#e2e8f0' : '#c8d4e3', background: w ? 'rgba(255,255,255,.05)' : 'transparent' }"
                    @mouseenter="$el.style.background='rgba(255,255,255,.06)';$el.style.color='#e2e8f0';"
                    @mouseleave="$el.style.background=w?'rgba(255,255,255,.05)':'transparent';$el.style.color=w?'#e2e8f0':'#c8d4e3';">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;">
                    <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span style="flex:1;">العمالة</span>
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                     style="flex-shrink:0;transition:transform .25s;" :style="{ transform: w ? 'rotate(180deg)' : 'none' }">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </button>
            <div x-show="w" x-collapse style="overflow:hidden;padding:2px 0 2px 6px;">
                @php $workerItems = [
                    ['r'=>'admin.workers.index', 'p'=>'admin.workers.index', 'l'=>'قائمة العمالة', 'perm'=>'workers.view',
                     'd'=>'M4 6h16M4 10h16M4 14h8'],
                    ['r'=>'admin.workers.create','p'=>'admin.workers.create','l'=>'إضافة عامل',  'perm'=>'workers.create',
                     'd'=>'M12 5v14M5 12h14'],
                    ['r'=>'admin.workers.bulk',  'p'=>'admin.workers.bulk',  'l'=>'رفع CVs جماعي', 'perm'=>'workers.create',
                     'd'=>'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12'],
                ] @endphp
                @foreach($workerItems as $it)
                    @if($can($it['perm']))
                    @php $on = request()->routeIs($it['p']); @endphp
                    <a href="{{ route($it['r']) }}"
                       style="display:flex;align-items:center;gap:9px;padding:7px 10px;margin:1px 0;
                              border-radius:7px;text-decoration:none;font-size:12.5px;
                              {{ $on ? 'color:#c9a84c;background:rgba(201,168,76,.12);border-right:2px solid #c9a84c;' : 'color:#c8d4e3;background:transparent;border-right:2px solid transparent;' }}"
                       onmouseover="if(!this.dataset.on){this.style.background='rgba(255,255,255,.05)';this.style.color='#cbd5e1';}"
                       onmouseout="if(!this.dataset.on){this.style.background='transparent';this.style.color='#c8d4e3';}"
                       {{ $on ? 'data-on=1' : '' }}>
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;">
                            <path d="{{ $it['d'] }}"/>
                        </svg>
                        {{ $it['l'] }}
                    </a>
                    @endif
                @endforeach
            </div>
        </div>
        @endif

        @if($showPeopleGroup)
        {{-- Section label: الأشخاص والعملاء --}}
        <div style="padding:10px 10px 4px;margin-top:4px;">
            <p style="font-size:10px;font-weight:700;letter-spacing:.06em;
                      text-transform:uppercase;color:#8fa3c0;margin:0;">الأشخاص والعملاء</p>
        </div>

        {{-- -- GROUP: الأشخاص والعملاء -- --}}
        <div style="margin-bottom:1px;">
            <button @click="p=!p"
                    style="width:100%;display:flex;align-items:center;gap:10px;padding:9px 12px;
                           border-radius:8px;border:none;cursor:pointer;text-align:right;
                           font-family:Cairo,sans-serif;font-size:13px;font-weight:600;
                           transition:background .15s,color .15s;"
                    :style="{ color: p ? '#e2e8f0' : '#c8d4e3', background: p ? 'rgba(255,255,255,.05)' : 'transparent' }"
                    @mouseenter="$el.style.background='rgba(255,255,255,.06)';$el.style.color='#e2e8f0';"
                    @mouseleave="$el.style.background=p?'rgba(255,255,255,.05)':'transparent';$el.style.color=p?'#e2e8f0':'#c8d4e3';">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>
                </svg>
                <span style="flex:1;">الأشخاص والعملاء</span>
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                     style="flex-shrink:0;transition:transform .25s;" :style="{ transform: p ? 'rotate(180deg)' : 'none' }">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </button>
            <div x-show="p" x-collapse style="overflow:hidden;padding:2px 0 2px 6px;">
                @php $peopleItems = [
                    ['r'=>'admin.clients.index', 'p'=>'admin.clients.*', 'l'=>'العملاء', 'perm'=>'clients.view',
                     'd'=>'M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2 M12 7a4 4 0 100 8 4 4 0 000-8z'],
                    ['r'=>'admin.agents.index',  'p'=>'admin.agents.*',  'l'=>'الوسطاء',  'perm'=>'agents.view',
                     'd'=>'M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2 M9 7a4 4 0 100 8 4 4 0 000-8z M23 21v-2a4 4 0 00-3-3.87 M16 3.13a4 4 0 010 7.75'],
                ] @endphp
                @foreach($peopleItems as $it)
                    @if($can($it['perm']))
                    @php $on = request()->routeIs($it['p']); @endphp
                    <a href="{{ route($it['r']) }}"
                       style="display:flex;align-items:center;gap:9px;padding:7px 10px;margin:1px 0;
                              border-radius:7px;text-decoration:none;font-size:12.5px;
                              {{ $on ? 'color:#c9a84c;background:rgba(201,168,76,.12);border-right:2px solid #c9a84c;' : 'color:#c8d4e3;background:transparent;border-right:2px solid transparent;' }}"
                       onmouseover="if(!this.dataset.on){this.style.background='rgba(255,255,255,.05)';this.style.color='#cbd5e1';}"
                       onmouseout="if(!this.dataset.on){this.style.background='transparent';this.style.color='#c8d4e3';}"
                       {{ $on ? 'data-on=1' : '' }}>
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;">
                            <path d="{{ $it['d'] }}"/>
                        </svg>
                        {{ $it['l'] }}
                    </a>
                    @endif
                @endforeach
            </div>
        </div>
        @endif

        @if($showSettingsGroup)
        {{-- Section label: الإعدادات --}}
        <div style="padding:10px 10px 4px;">
            <p style="font-size:10px;font-weight:700;letter-spacing:.06em;
                      text-transform:uppercase;color:#8fa3c0;margin:0;">الإعدادات</p>
        </div>

        {{-- -- GROUP 1: الإعدادات العامة -- --}}
        <div style="margin-bottom:1px;">
            <button @click="s=!s"
                    style="width:100%;display:flex;align-items:center;gap:10px;padding:9px 12px;
                           border-radius:8px;border:none;cursor:pointer;text-align:right;
                           font-family:Cairo,sans-serif;font-size:13px;font-weight:600;
                           transition:background .15s,color .15s;"
                    :style="{ color: s ? '#e2e8f0' : '#c8d4e3', background: s ? 'rgba(255,255,255,.05)' : 'transparent' }"
                    @mouseenter="$el.style.background='rgba(255,255,255,.06)';$el.style.color='#e2e8f0';"
                    @mouseleave="$el.style.background=s?'rgba(255,255,255,.05)':'transparent';$el.style.color=s?'#e2e8f0':'#c8d4e3';">
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
                    ['r'=>'admin.branches.index',        'p'=>'admin.branches.*',        'l'=>'الفروع',              'd'=>'M3 21h18M3 7l9-4 9 4M4 7v14M20 7v14M9 21V11h6v10',                                          'perm'=>'branches.view'],
                    ['r'=>'admin.cities.index',          'p'=>'admin.cities.*',          'l'=>'المدن',               'd'=>'M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z',                  'perm'=>null],
                    ['r'=>'admin.nationalities.index',   'p'=>'admin.nationalities.*',   'l'=>'الجنسيات',            'd'=>'M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6H10.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9',       'perm'=>'nationalities.view'],
                    ['r'=>'admin.airports.index',        'p'=>'admin.airports.*',        'l'=>'المطارات',            'd'=>'M2.5 19h19M6.5 12.5L4 19 M17.5 12.5L20 19 M12 3L6.5 12.5h11L12 3z',                       'perm'=>'airports.view'],
                    ['r'=>'admin.housings.index',        'p'=>'admin.housings.*',        'l'=>'السكن',               'd'=>'M3 21h18M3 7l9-4 9 4M4 7v14h16V7M9 21V11h6v10',                                            'perm'=>'housings.view'],
                    ['r'=>'admin.settings.roles.index',  'p'=>'admin.settings.roles.*',  'l'=>'الأدوار والصلاحيات', 'd'=>'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z',                                             'perm'=>'roles.manage'],
                    ['r'=>'admin.settings.admins.index', 'p'=>'admin.settings.admins.*', 'l'=>'المسؤولون',            'd'=>'M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2 M9 7a4 4 0 100 8 4 4 0 000-8z',                 'perm'=>'admins.manage'],
                ] @endphp
                @foreach($items as $it)
                    @if($it['perm'] ? $can($it['perm']) : $admin->isSuperAdmin())
                    @php $on = request()->routeIs($it['p']); @endphp
                    <a href="{{ route($it['r']) }}"
                       style="display:flex;align-items:center;gap:9px;padding:7px 10px;margin:1px 0;
                              border-radius:7px;text-decoration:none;font-size:12.5px;
                              {{ $on ? 'color:#c9a84c;background:rgba(201,168,76,.12);border-right:2px solid #c9a84c;' : 'color:#c8d4e3;background:transparent;border-right:2px solid transparent;' }}"
                       onmouseover="if(!this.dataset.on){this.style.background='rgba(255,255,255,.05)';this.style.color='#cbd5e1';}"
                       onmouseout="if(!this.dataset.on){this.style.background='transparent';this.style.color='#c8d4e3';}"
                       {{ $on ? 'data-on=1' : '' }}>
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;">
                            <path d="{{ $it['d'] }}"/>
                        </svg>
                        {{ $it['l'] }}
                    </a>
                    @endif
                @endforeach
            </div>
        </div>
        @endif

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
                تسجيل الخروج
            </button>
        </form>
    </div>

</div>
