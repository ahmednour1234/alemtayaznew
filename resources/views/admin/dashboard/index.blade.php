@extends('admin.layouts.app')
@section('title', 'لوحة التحكم')

@push('styles')
<style>
    :root {
        --dash-navy: #0f172a;
        --dash-text: #1e293b;
        --dash-muted: #94a3b8;
        --dash-border: #e5e7eb;
        --dash-bg: #f5f7fb;
        --dash-gold: #c9a84c;
        --dash-green: #059669;
        --dash-red: #ef4444;
        --dash-blue: #2563eb;
        --dash-orange: #f97316;
    }

    .dashboard-page {
        direction: rtl;
        font-family: Cairo, sans-serif;
        overflow-x: hidden;
        max-width: 100%;
    }

    .dash-filter-row {
        display: flex;
        justify-content: flex-start;
        margin-bottom: 18px;
    }

    .dash-filter-select {
        width: 220px;
        height: 44px;
        border: 1px solid var(--dash-border);
        border-radius: 12px;
        background: #fff;
        padding: 0 14px;
        color: var(--dash-text);
        font-size: 13px;
        font-weight: 700;
        outline: none;
        box-shadow: 0 1px 3px rgba(15, 23, 42, .05);
    }

    .dash-stat-grid {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 18px;
    }

    .stat-card {
        background: #fff;
        border: 1px solid rgba(226, 232, 240, .9);
        border-radius: 16px;
        padding: 18px;
        min-height: 130px;
        box-shadow: 0 2px 12px rgba(15, 23, 42, .05);
        text-decoration: none;
        color: inherit;
        position: relative;
        overflow: hidden;
        transition: .2s ease;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 30px rgba(15, 23, 42, .10);
    }

    .stat-card::after {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        height: 3px;
        background: var(--accent, #2563eb);
    }

    .stat-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--soft, #eff6ff);
        color: var(--accent, #2563eb);
        flex-shrink: 0;
    }

    .stat-label {
        color: var(--dash-muted);
        font-size: 12px;
        font-weight: 700;
        margin: 0 0 8px;
    }

    .stat-value {
        color: var(--dash-text);
        font-size: 28px;
        font-weight: 900;
        line-height: 1;
        margin: 0;
    }

    .stat-unit {
        color: #64748b;
        font-size: 12px;
        margin-top: 8px;
    }

    .stat-change {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        font-weight: 800;
        padding: 3px 9px;
        border-radius: 999px;
        margin-top: 14px;
    }
    .stat-change.up   { color: #059669; background: #d1fae5; }
    .stat-change.down { color: #dc2626; background: #fee2e2; }
    .stat-change.neu  { color: #0369a1; background: #e0f2fe; }

    .dash-main-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
        margin-bottom: 18px;
    }

    .table-card {
        background: #fff;
        border: 1px solid rgba(226, 232, 240, .9);
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(15, 23, 42, .05);
    }

    .table-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 16px 18px;
        border-bottom: 1px solid #eef2f7;
        background: linear-gradient(180deg, #fff, #fbfdff);
    }

    .table-title {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0;
        color: var(--dash-text);
        font-size: 16px;
        font-weight: 900;
    }

    .table-icon {
        width: 36px;
        height: 36px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--soft, #eff6ff);
        color: var(--accent, #2563eb);
        flex-shrink: 0;
    }

    .table-link {
        border: 1px solid var(--link-border, #bfdbfe);
        background: var(--link-bg, #eff6ff);
        color: var(--link-color, #2563eb);
        border-radius: 999px;
        padding: 6px 13px;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none;
        white-space: nowrap;
    }

    .dash-table-wrap {
        overflow-x: auto;
    }

    .dash-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 640px;
    }

    .dash-table.small {
        min-width: 520px;
    }

    .dash-table th {
        background: #f8fafc;
        color: #64748b;
        font-size: 12px;
        font-weight: 900;
        text-align: right;
        padding: 12px 16px;
        border-bottom: 1px solid #eef2f7;
        white-space: nowrap;
    }

    .dash-table td {
        color: #334155;
        font-size: 13px;
        padding: 13px 16px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        white-space: nowrap;
    }

    .dash-table tr:hover td {
        background: #f8fafc;
    }

    .amount-green { color: var(--dash-green); font-weight: 900; }
    .amount-red { color: var(--dash-red); font-weight: 900; }
    .amount-orange { color: var(--dash-orange); font-weight: 900; }

    .badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        border-radius: 999px;
        padding: 5px 11px;
        font-size: 11px;
        font-weight: 900;
    }

    .badge.green { color: #15803d; background: #dcfce7; }
    .badge.orange { color: #c2410c; background: #ffedd5; }
    .badge.red { color: #b91c1c; background: #fee2e2; }
    .badge.blue { color: #1d4ed8; background: #dbeafe; }

    .action-group {
        display: inline-flex;
        gap: 7px;
        align-items: center;
    }

    .btn-soft,
    .btn-approve {
        border: 0;
        border-radius: 9px;
        padding: 7px 13px;
        font-size: 12px;
        font-weight: 900;
        text-decoration: none;
        cursor: pointer;
        font-family: Cairo, sans-serif;
    }

    .btn-soft {
        background: #eef2ff;
        color: #475569;
    }

    .btn-approve {
        background: linear-gradient(135deg, #16a34a, #15803d);
        color: #fff;
    }

    .pending-card .table-card-header {
        background: linear-gradient(90deg, #fffbeb, #fff);
        border-bottom-color: #fde68a;
    }

    .pending-card .dash-table th {
        background: #fffbeb;
        color: #92400e;
        border-bottom-color: #fde68a;
    }

    /* compact cells so 8 cols fit without horizontal scroll */
    .pending-card .dash-table td,
    .pending-card .dash-table th {
        padding: 10px 11px;
    }
    .pending-card .dash-table td:nth-child(3) {
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .empty-state {
        text-align: center !important;
        padding: 44px 16px !important;
        color: #94a3b8 !important;
        font-weight: 700;
    }

    /* ── Welcome Banner ───────────────────────────────────── */
    .dash-welcome {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 55%, #1a3355 100%);
        border-radius: 18px;
        padding: 26px 32px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        position: relative;
        overflow: hidden;
    }
    .dash-welcome::before {
        content: '';
        position: absolute;
        top: -50px; left: -50px;
        width: 220px; height: 220px;
        background: radial-gradient(circle, rgba(201,168,76,.14) 0%, transparent 70%);
        pointer-events: none;
    }
    .dash-welcome::after {
        content: '';
        position: absolute;
        bottom: -70px; right: 80px;
        width: 260px; height: 260px;
        background: radial-gradient(circle, rgba(37,99,235,.10) 0%, transparent 70%);
        pointer-events: none;
    }
    .dash-welcome-text { position: relative; z-index: 1; }
    .dash-welcome-date {
        color: #c9a84c;
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: .04em;
        margin: 0 0 5px;
    }
    .dash-welcome-title {
        color: #fff;
        font-size: 22px;
        font-weight: 900;
        margin: 0 0 4px;
    }
    .dash-welcome-sub {
        color: rgba(255,255,255,.5);
        font-size: 12.5px;
        margin: 0;
    }
    .dash-banner-pills {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        position: relative;
        z-index: 1;
    }
    .dash-banner-pill {
        background: rgba(255,255,255,.08);
        border: 1px solid rgba(255,255,255,.12);
        border-radius: 14px;
        padding: 12px 18px;
        text-align: center;
        min-width: 100px;
    }
    .dash-banner-pill-val {
        color: #fff;
        font-size: 20px;
        font-weight: 900;
        display: block;
        line-height: 1;
    }
    .dash-banner-pill-lbl {
        color: rgba(255,255,255,.55);
        font-size: 11px;
        font-weight: 700;
        margin-top: 4px;
        display: block;
    }

    /* ── Charts Row ─────────────────────────────────────── */
    .dash-charts-row {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 18px;
        margin-bottom: 24px;
    }
    .chart-card {
        background: #fff;
        border: 1px solid rgba(226,232,240,.8);
        border-radius: 18px;
        box-shadow: 0 2px 12px rgba(15,23,42,.05);
        overflow: hidden;
        min-width: 0;
    }
    .chart-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 20px 0;
        gap: 12px;
    }
    .chart-card-title {
        display: flex;
        align-items: center;
        gap: 9px;
        font-size: 15px;
        font-weight: 900;
        color: #1e293b;
        margin: 0;
    }
    .chart-card-dot { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
    .chart-legend { display: flex; gap: 14px; }
    .chart-legend-item {
        display: flex; align-items: center; gap: 6px;
        font-size: 12px; color: #64748b; font-weight: 700;
    }
    .chart-legend-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
    .chart-body { padding: 16px 20px 20px; }
    .chart-body canvas { max-height: 220px; }
    .chart-donut-wrap {
        padding: 12px 20px 20px;
        display: flex; align-items: center; justify-content: center;
    }
    .chart-donut-wrap canvas { max-height: 210px; max-width: 210px; }

    /* ── Table Footer ─────────────────────────────────── */
    .table-footer {
        padding: 12px 18px;
        text-align: center;
        border-top: 1px solid #f1f5f9;
    }
    .table-footer a {
        font-size: 13px; font-weight: 800; text-decoration: none;
    }
    .table-footer a:hover { opacity: .75; }

    /* ── Responsive ────────────────────────────────────── */
    @media (max-width: 1400px) {
        .dash-stat-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }
    @media (max-width: 1100px) {
        .dash-charts-row { grid-template-columns: 1fr; }
    }
    @media (max-width: 992px) {
        .dash-main-grid { grid-template-columns: 1fr; }
        .dash-stat-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 768px) {
        .dash-welcome { flex-direction: column; align-items: flex-start; }
        .dash-banner-pills { display: none; }
    }
    @media (max-width: 576px) {
        .dash-stat-grid { grid-template-columns: 1fr; }
        .table-card-header { align-items: flex-start; flex-direction: column; }
        .dash-filter-select { width: 100%; }
    }

    /* ── Warning / Housing stats row ──────────── */
    .dash-warning-row {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }
    .warn-card {
        background: #fff;
        border-radius: 14px;
        padding: 18px 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 1px 6px rgba(0,0,0,.06);
        min-width: 0;
    }
    .warn-icon {
        width: 46px; height: 46px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .warn-label { font-size: 11.5px; color: #64748b; margin-bottom: 1px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .warn-value { font-size: 24px; font-weight: 800; line-height: 1.1; color: #0f172a; }
    .warn-sub { font-size: 11px; color: #94a3b8; margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .warn-body { min-width: 0; flex: 1; }

    /* ── Tomorrow Trips list ──────────────────── */
    .trips-list { padding: 4px 0; overflow-y: auto; max-height: 280px; }
    .trip-item {
        display: flex; align-items: center; gap: 12px;
        padding: 10px 16px;
        border-bottom: 1px solid #f8fafc;
        transition: background .15s;
    }
    .trip-item:last-child { border-bottom: none; }
    .trip-item:hover { background: #f8fafc; }
    .trip-time { font-size: 14px; font-weight: 700; color: #0f172a; min-width: 46px; }
    .trip-info { flex: 1; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .trip-type-badge {
        font-size: 11px; padding: 2px 8px;
        border-radius: 6px; font-weight: 600;
    }
    .trip-airport { font-size: 12.5px; color: #475569; }
    .trip-workers { display: flex; align-items: center; gap: 4px; font-size: 12px; color: #94a3b8; }
    @media (max-width: 992px) { .dash-warning-row { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 576px) { .dash-warning-row { grid-template-columns: 1fr 1fr; } }
</style>
@endpush

@section('content')
<div class="dashboard-page">

    {{-- لافتة الترحيب --}}
    <div class="dash-welcome">
        <div class="dash-welcome-text">
            <p class="dash-welcome-date">{{ now()->locale('ar')->isoFormat('dddd، D MMMM YYYY') }}</p>
            <h1 class="dash-welcome-title">مرحباً، {{ auth('admin')->user()->name ?? 'المدير' }}</h1>
            <p class="dash-welcome-sub">
                @if(auth('admin')->user()->isBranchAdmin() && auth('admin')->user()->branch)
                    {{ auth('admin')->user()->department_label }} — فرع {{ auth('admin')->user()->branch->name }}
                @else
                    لوحة تحكم شركة الامتياز للاستقدام
                @endif
            </p>
            @unless(auth('admin')->user()->hideFinancials())
            <a href="{{ route('admin.dashboard.import-statement') }}"
               style="display:inline-flex;align-items:center;gap:7px;margin-top:14px;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);color:#fff;border-radius:10px;padding:8px 16px;font-size:12.5px;font-weight:700;text-decoration:none;font-family:Cairo,sans-serif;transition:.2s;"
               onmouseover="this.style.background='rgba(255,255,255,.2)'"
               onmouseout="this.style.background='rgba(255,255,255,.12)'">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                استيراد إيرادات ومصروفات
            </a>
            @endunless
        </div>
        <div class="dash-banner-pills">
            <div class="dash-banner-pill">
                <span class="dash-banner-pill-val">{{ number_format($stats['active_contracts'] ?? 0) }}</span>
                <span class="dash-banner-pill-lbl">عقد نشط</span>
            </div>
            <div class="dash-banner-pill">
                <span class="dash-banner-pill-val">{{ number_format($stats['total_clients'] ?? 0) }}</span>
                <span class="dash-banner-pill-lbl">عميل</span>
            </div>
            @unless(auth('admin')->user()->hideFinancials())
            <div class="dash-banner-pill">
                <span class="dash-banner-pill-val">{{ ($stats['pending_expenses'] ?? 0) + ($stats['pending_transfers'] ?? 0) }}</span>
                <span class="dash-banner-pill-lbl">طلب معلق</span>
            </div>
            @endunless
        </div>
    </div>

    {{-- كروت الإحصائيات --}}
    <div class="dash-stat-grid">
        <a href="{{ route('admin.clients.index') }}" class="stat-card" style="--accent:#2563eb;--soft:#eff6ff;">
            <div class="stat-row">
                <div>
                    <p class="stat-label">إجمالي العملاء</p>
                    <p class="stat-value">{{ number_format($stats['total_clients'] ?? 0) }}</p>
                    <div class="stat-unit">عميل</div>
                </div>
                <div class="stat-icon">
                    <svg width="23" height="23" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
            </div>
            <div class="stat-change up">↑ 12% عن الشهر الماضي</div>
        </a>

        <a href="{{ route('admin.contracts.index') }}" class="stat-card" style="--accent:#10b981;--soft:#ecfdf5;">
            <div class="stat-row">
                <div>
                    <p class="stat-label">إجمالي العقود</p>
                    <p class="stat-value">{{ number_format($stats['active_contracts'] ?? 0) }}</p>
                    <div class="stat-unit">عقد</div>
                </div>
                <div class="stat-icon">
                    <svg width="23" height="23" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
            </div>
            <div class="stat-change up">↑ 8% عن الشهر الماضي</div>
        </a>

        @unless(auth('admin')->user()->hideFinancials())
        <a href="{{ route('admin.incomes.index') }}" class="stat-card" style="--accent:#059669;--soft:#ecfdf5;">
            <div class="stat-row">
                <div>
                    <p class="stat-label">إجمالي الإيرادات</p>
                    <p class="stat-value">{{ number_format($stats['total_income'] ?? 0) }}</p>
                    <div class="stat-unit">ريال سعودي</div>
                </div>
                <div class="stat-icon">
                    <svg width="23" height="23" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>
                </div>
            </div>
            @if(($stats['income_change'] ?? 0) >= 0)
                <div class="stat-change up">↑ {{ abs($stats['income_change'] ?? 0) }}% عن الشهر الماضي</div>
            @else
                <div class="stat-change down">↓ {{ abs($stats['income_change'] ?? 0) }}% عن الشهر الماضي</div>
            @endif
        </a>

        <a href="{{ route('admin.expenses.index') }}" class="stat-card" style="--accent:#f59e0b;--soft:#fffbeb;">
            <div class="stat-row">
                <div>
                    <p class="stat-label">إجمالي المصروفات</p>
                    <p class="stat-value">{{ number_format($stats['total_expenses'] ?? 0) }}</p>
                    <div class="stat-unit">ريال سعودي</div>
                </div>
                <div class="stat-icon">
                    <svg width="23" height="23" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M7 15h0"/><path d="M11 15h0"/></svg>
                </div>
            </div>
            <div class="stat-change down">↓ {{ abs($stats['expenses_change'] ?? 3) }}% عن الشهر الماضي</div>
        </a>

        <div class="stat-card" style="--accent:#f97316;--soft:#fff7ed;">
            <div class="stat-row">
                <div>
                    <p class="stat-label">إجمالي الأرباح</p>
                    <p class="stat-value" style="color:#f97316;">{{ number_format(abs($stats['net_profit'] ?? 0)) }}</p>
                    <div class="stat-unit">ريال سعودي</div>
                </div>
                <div class="stat-icon">
                    <svg width="23" height="23" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>
                </div>
            </div>
            <div class="stat-change up">↑ 10% عن الشهر الماضي</div>
        </div>
        @endunless

        <div class="stat-card" style="--accent:#3b82f6;--soft:#eff6ff;">
            <div class="stat-row">
                <div>
                    <p class="stat-label">متوسط مدة الإنجاز</p>
                    <p class="stat-value">{{ $stats['avg_completion_days'] ?? '4.2' }}</p>
                    <div class="stat-unit">أيام</div>
                </div>
                <div class="stat-icon">
                    <svg width="23" height="23" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
            </div>
            <div class="stat-change neu">↗ معدل الإنجاز {{ $stats['completion_rate'] ?? 0 }}٪</div>
        </div>
    </div>

    {{-- مخططات بيانية --}}
    <div class="dash-charts-row" @if(auth('admin')->user()->hideFinancials()) style="grid-template-columns:1fr;" @endif>
        @unless(auth('admin')->user()->hideFinancials())
        <div class="chart-card">
            <div class="chart-card-header">
                <h3 class="chart-card-title">
                    <span class="chart-card-dot" style="background:#2563eb;"></span>
                    الإيرادات والمصروفات الشهرية
                </h3>
                <div class="chart-legend">
                    <span class="chart-legend-item"><span class="chart-legend-dot" style="background:#059669;"></span>الإيرادات</span>
                    <span class="chart-legend-item"><span class="chart-legend-dot" style="background:#ef4444;"></span>المصروفات</span>
                </div>
            </div>
            <div class="chart-body"><canvas id="revenueChart"></canvas></div>
        </div>
        @endunless
        <div class="chart-card">
            <div class="chart-card-header">
                <h3 class="chart-card-title">
                    <span class="chart-card-dot" style="background:#7c3aed;"></span>
                    حالة العقود
                </h3>
            </div>
            <div class="chart-donut-wrap"><canvas id="statusChart"></canvas></div>
        </div>
    </div>

    {{-- نقل كفالة + عقود الاستقدام الشهرية --}}
    @unless(auth('admin')->user()->hideFinancials())
    <div class="dash-charts-row" style="grid-template-columns:minmax(0,1fr) minmax(0,1fr);">
        <div class="chart-card">
            <div class="chart-card-header">
                <h3 class="chart-card-title">
                    <span class="chart-card-dot" style="background:#8b5cf6;"></span>
                    نقل كفالة شهرياً
                </h3>
                <div class="chart-legend">
                    <span class="chart-legend-item"><span class="chart-legend-dot" style="background:#8b5cf6;"></span>إجمالي</span>
                    <span class="chart-legend-item"><span class="chart-legend-dot" style="background:#16a34a;"></span>مكتمل</span>
                </div>
            </div>
            <div class="chart-body"><canvas id="sponsorshipChart"></canvas></div>
        </div>
        <div class="chart-card">
            <div class="chart-card-header">
                <h3 class="chart-card-title">
                    <span class="chart-card-dot" style="background:#c9a84c;"></span>
                    عقود الاستقدام الشهرية
                </h3>
            </div>
            <div class="chart-body"><canvas id="contractsBarChart"></canvas></div>
        </div>
    </div>
    @endunless

    {{-- رحلات غداً + الشكاوى والمشاكل --}}
    <div class="dash-charts-row" style="grid-template-columns:minmax(0,1fr) minmax(0,1fr);">
        {{-- رحلات الغد --}}
        <div class="chart-card" style="padding:0;">
            <div class="chart-card-header" style="padding:16px 18px 14px;">
                <h3 class="chart-card-title">
                    <span class="chart-card-dot" style="background:#0891b2;"></span>
                    رحلات الغد
                </h3>
                <span style="background:#e0f2fe;color:#0369a1;border:1px solid #bae6fd;font-size:11.5px;padding:3px 10px;border-radius:8px;font-weight:700;">
                    {{ count($tomorrowTrips) }} رحلة
                </span>
            </div>
            <div class="trips-list">
                @forelse($tomorrowTrips as $trip)
                    <div class="trip-item">
                        <div class="trip-time">{{ $trip->trip_time ? \Carbon\Carbon::parse($trip->trip_time)->format('H:i') : '--:--' }}</div>
                        <div class="trip-info">
                            <span class="trip-type-badge" style="background:{{ ($trip->type_color ?? '#64748b') }}22;color:{{ $trip->type_color ?? '#64748b' }};">
                                {{ $trip->type_label }}
                            </span>
                            <span class="trip-airport">{{ $trip->airport?->name ?? '—' }}</span>
                        </div>
                        <div class="trip-workers">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            {{ $trip->workers_count }}
                        </div>
                    </div>
                @empty
                    <div style="padding:36px 16px;text-align:center;color:#94a3b8;font-size:13px;">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" style="display:block;margin:0 auto 10px;"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                        لا توجد رحلات مجدولة لغد
                    </div>
                @endforelse
            </div>
        </div>
        {{-- الشكاوى والمشاكل --}}
        <div class="chart-card">
            <div class="chart-card-header">
                <h3 class="chart-card-title">
                    <span class="chart-card-dot" style="background:#ef4444;"></span>
                    الشكاوى والمشاكل الشهرية
                </h3>
                <div class="chart-legend">
                    <span class="chart-legend-item"><span class="chart-legend-dot" style="background:#ef4444;"></span>مفتوحة</span>
                    <span class="chart-legend-item"><span class="chart-legend-dot" style="background:#16a34a;"></span>محلولة</span>
                </div>
            </div>
            <div class="chart-body"><canvas id="complaintsBarChart"></canvas></div>
        </div>
    </div>

    {{-- السكن + مساند + شكاوى عاجلة --}}
    <div class="dash-warning-row">
        <div class="warn-card">
            <div class="warn-icon" style="background:#eff6ff;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            </div>
            <div class="warn-body">
                <div class="warn-label">سعة السكن</div>
                <div class="warn-value" style="color:#2563eb;">{{ $housingStats['capacity'] }}</div>
                <div class="warn-sub">{{ $housingStats['houses'] }} مسكن نشط</div>
            </div>
        </div>
        <div class="warn-card">
            <div class="warn-icon" style="background:#f0fdf4;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="warn-body">
                <div class="warn-label">مشغول / متاح</div>
                <div class="warn-value" style="color:#16a34a;">{{ $housingStats['occupied'] }}</div>
                <div class="warn-sub"><span style="color:#16a34a;font-weight:600;">{{ $housingStats['available'] }}</span> متاح &bull; {{ $housingStats['rate'] }}% مشغول</div>
            </div>
        </div>
        <div class="warn-card">
            <div class="warn-icon" style="background:#fefce8;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <div class="warn-body">
                <div class="warn-label">مساند غير مغلقة</div>
                <div class="warn-value" style="color:#ca8a04;">{{ $stats['musaned_open'] }}</div>
                <div class="warn-sub">شكوى على مساند مفتوحة</div>
            </div>
        </div>
        <div class="warn-card">
            <div class="warn-icon" style="background:#fff1f2;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
            <div class="warn-body">
                <div class="warn-label">شكاوى بتحذير</div>
                <div class="warn-value" style="color:#dc2626;">{{ $stats['urgent_complaints'] }}</div>
                <div class="warn-sub">أولوية عالية أو عاجلة مفتوحة</div>
            </div>
        </div>
    </div>

    {{-- جدولين فوق: الإيرادات + المصروفات --}}
    @unless(auth('admin')->user()->hideFinancials())
    <div class="dash-main-grid">

        {{-- أحدث الإيرادات --}}
        <div class="table-card">
            <div class="table-card-header">
                <h3 class="table-title">
                    <span class="table-icon" style="--accent:#059669;--soft:#ecfdf5;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>
                    </span>
                    أحدث الإيرادات
                </h3>
                <a href="{{ route('admin.incomes.index') }}" class="table-link" style="--link-color:#059669;--link-bg:#ecfdf5;--link-border:#a7f3d0;">عرض الكل ←</a>
            </div>
            <div class="dash-table-wrap">
                <table class="dash-table small">
                    <thead>
                        <tr>
                            <th>المبلغ</th>
                            <th>المصدر</th>
                            <th>التاريخ</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentIncomes as $income)
                            <tr>
                                <td><span class="amount-green">{{ number_format($income->amount, 0) }}</span> ريال</td>
                                <td>{{ $income->incomeType?->name ?? $income->source ?? 'دفعة من العميل' }}</td>
                                <td>{{ $income->date?->format('d/m/Y') }}</td>
                                <td><span class="badge green">تم التحصيل</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="empty-state">لا توجد إيرادات حالياً</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="table-footer">
                <a href="{{ route('admin.incomes.index') }}" style="color:#059669;">عرض جميع الإيرادات ←</a>
            </div>
        </div>

        {{-- أحدث المصروفات --}}
        <div class="table-card">
            <div class="table-card-header">
                <h3 class="table-title">
                    <span class="table-icon" style="--accent:#ef4444;--soft:#fff1f2;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="22 17 13.5 8.5 8.5 13.5 2 7"/><polyline points="16 17 22 17 22 11"/></svg>
                    </span>
                    أحدث المصروفات
                </h3>
                <a href="{{ route('admin.expenses.index') }}" class="table-link" style="--link-color:#ef4444;--link-bg:#fff1f2;--link-border:#fecdd3;">عرض الكل ←</a>
            </div>
            <div class="dash-table-wrap">
                <table class="dash-table small">
                    <thead>
                        <tr>
                            <th>المبلغ</th>
                            <th>النوع</th>
                            <th>التاريخ</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentExpenses as $expense)
                            <tr>
                                <td><span class="amount-red">{{ number_format($expense->amount, 0) }}</span> ريال</td>
                                <td>{{ $expense->expenseType?->name ?? 'مصروفات إدارية' }}</td>
                                <td>{{ $expense->date?->format('d/m/Y') }}</td>
                                <td>
                                    @if(($expense->status ?? null) == 'paid' || ($expense->approved_at ?? false))
                                        <span class="badge green">مدفوعة</span>
                                    @else
                                        <span class="badge orange">معلقة</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="empty-state">لا توجد مصروفات حالياً</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="table-footer">
                <a href="{{ route('admin.expenses.index') }}" style="color:#ef4444;">عرض جميع المصروفات ←</a>
            </div>
        </div>
    </div>
    @endunless

    {{-- جدول كبير تحتهم: طلبات الموافقة المعلقة --}}
    @unless(auth('admin')->user()->hideFinancials())
    <div class="table-card pending-card">
        <div class="table-card-header">
            <h3 class="table-title">
                <span class="table-icon" style="--accent:#d97706;--soft:#fffbeb;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                </span>
                طلبات الموافقة المعلقة
            </h3>
        </div>
        <div class="dash-table-wrap">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>نوع الطلب</th>
                        <th>البيان</th>
                        <th>المبلغ</th>
                        <th>تاريخ الطلب</th>
                        <th>مقدم الطلب</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @php $rowNumber = 1; @endphp

                    @forelse($pendingExpenses as $expense)
                        <tr>
                            <td>{{ $rowNumber++ }}</td>
                            <td>{{ $expense->expenseType?->name ?? 'مصروف' }}</td>
                            <td title="{{ $expense->notes ?? $expense->description ?? '' }}">{{ \Illuminate\Support\Str::limit($expense->notes ?? $expense->description ?? 'مصروف يحتاج موافقة', 35) }}</td>
                            <td><span class="amount-orange">{{ number_format($expense->amount, 0) }}</span> ريال</td>
                            <td>{{ $expense->date?->format('d/m/Y') }}</td>
                            <td>{{ $expense->user?->name ?? $expense->branch?->name ?? '—' }}</td>
                            <td>
                                <div class="action-group">
                                    <form action="{{ route('admin.expenses.approve', $expense->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn-approve">موافقة</button>
                                    </form>
                                    <button type="button" class="btn-soft"
                                        style="background:#fee2e2;color:#dc2626;"
                                        onclick="openRejectModal('{{ route('admin.expenses.reject', $expense->id) }}')">رفض</button>
                                    <a href="{{ route('admin.expenses.show', $expense->id) }}" class="btn-soft">عرض</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        @if(($pendingTransfers->count() ?? 0) == 0)
                            <tr><td colspan="7" class="empty-state">لا توجد طلبات موافقة معلقة</td></tr>
                        @endif
                    @endforelse

                    @foreach($pendingTransfers as $transfer)
                        <tr>
                            <td>{{ $rowNumber++ }}</td>
                            <td>تحويل مالي</td>
                            <td title="تحويل من {{ $transfer->fromBranch?->name ?? '—' }} إلى {{ $transfer->toBranch?->name ?? '—' }}">{{ $transfer->fromBranch?->name ?? '—' }} ← {{ $transfer->toBranch?->name ?? '—' }}</td>
                            <td><span class="amount-orange">{{ number_format($transfer->amount, 0) }}</span> ريال</td>
                            <td>{{ $transfer->date?->format('d/m/Y') ?? $transfer->created_at?->format('d/m/Y') }}</td>
                            <td>{{ $transfer->user?->name ?? $transfer->fromBranch?->name ?? '—' }}</td>
                            <td>
                                <div class="action-group">
                                    <form action="{{ route('admin.transfers.approve', $transfer->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn-approve">موافقة</button>
                                    </form>
                                    <button type="button" class="btn-soft"
                                        style="background:#fee2e2;color:#dc2626;"
                                        onclick="openRejectModal('{{ route('admin.transfers.reject', $transfer->id) }}')">رفض</button>
                                    <a href="{{ route('admin.transfers.show', $transfer->id) }}" class="btn-soft">عرض</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endunless

</div>

{{-- ── Modal رفض الطلب ─────────────────────────────────────── --}}
<div id="rejectModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(15,23,42,.45);align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:18px;padding:28px 28px 22px;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(15,23,42,.2);direction:rtl;font-family:Cairo,sans-serif;">
        <h4 style="margin:0 0 6px;color:#0f172a;font-size:17px;font-weight:900;">رفض الطلب</h4>
        <p style="margin:0 0 16px;color:#64748b;font-size:13px;">يرجى كتابة سبب الرفض</p>
        <form id="rejectForm" method="POST">
            @csrf
            <input name="_method" type="hidden" value="POST">
            <textarea name="rejection_reason" rows="3" required
                placeholder="اكتب سبب الرفض هنا..."
                style="width:100%;border:1px solid #e2e8f0;border-radius:10px;padding:10px 13px;font-family:Cairo,sans-serif;font-size:13px;resize:vertical;outline:none;box-sizing:border-box;"></textarea>
            <div style="display:flex;gap:10px;margin-top:16px;justify-content:flex-end;">
                <button type="button" onclick="closeRejectModal()"
                    style="border:1px solid #e2e8f0;background:#f8fafc;color:#475569;border-radius:9px;padding:8px 18px;font-size:13px;font-weight:700;cursor:pointer;font-family:Cairo,sans-serif;">
                    إلغاء
                </button>
                <button type="submit"
                    style="background:linear-gradient(135deg,#dc2626,#b91c1c);color:#fff;border:0;border-radius:9px;padding:8px 20px;font-size:13px;font-weight:900;cursor:pointer;font-family:Cairo,sans-serif;">
                    تأكيد الرفض
                </button>
            </div>
        </form>
    </div>
</div>
<script>
    function openRejectModal(action) {
        document.getElementById('rejectForm').action = action;
        document.getElementById('rejectForm').querySelector('textarea').value = '';
        const m = document.getElementById('rejectModal');
        m.style.display = 'flex';
        setTimeout(() => m.querySelector('textarea').focus(), 50);
    }
    function closeRejectModal() {
        document.getElementById('rejectModal').style.display = 'none';
    }
    document.getElementById('rejectModal').addEventListener('click', function(e) {
        if (e.target === this) closeRejectModal();
    });
</script>

@endsection

@push('scripts')
@once
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
@endonce
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ── Revenue Line Chart ────────────────── */
    const revCtx = document.getElementById('revenueChart');
    if (revCtx) {
        new Chart(revCtx, {
            type: 'line',
            data: {
                labels: @json($chartData['months'] ?? []),
                datasets: [
                    {
                        label: 'الإيرادات',
                        data: @json($chartData['incomes'] ?? []),
                        borderColor: '#059669',
                        backgroundColor: 'rgba(5,150,105,.08)',
                        borderWidth: 2.5,
                        pointRadius: 4,
                        pointBackgroundColor: '#059669',
                        tension: 0.4,
                        fill: true,
                    },
                    {
                        label: 'المصروفات',
                        data: @json($chartData['expenses'] ?? []),
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239,68,68,.06)',
                        borderWidth: 2.5,
                        pointRadius: 4,
                        pointBackgroundColor: '#ef4444',
                        tension: 0.4,
                        fill: true,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        rtl: true,
                        bodyFont: { family: 'Cairo' },
                        callbacks: {
                            label: ctx => ' ' + ctx.dataset.label + ': ' + ctx.parsed.y.toLocaleString('ar-SA') + ' ر.س'
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Cairo', size: 11 }, color: '#94a3b8' }
                    },
                    y: {
                        grid: { color: 'rgba(226,232,240,.6)' },
                        ticks: {
                            font: { family: 'Cairo', size: 11 }, color: '#94a3b8',
                            callback: v => v >= 1000 ? (v / 1000).toFixed(0) + 'ك' : v
                        }
                    }
                }
            }
        });
    }

    /* ── Contract Status Doughnut ──────────── */
    const stCtx = document.getElementById('statusChart');
    if (stCtx) {
        new Chart(stCtx, {
            type: 'doughnut',
            data: {
                labels: @json($statusChart['labels'] ?? []),
                datasets: [{
                    data: @json($statusChart['data'] ?? []),
                    backgroundColor: @json($statusChart['colors'] ?? []),
                    borderWidth: 0,
                    hoverOffset: 8,
                }]
            },
            options: {
                responsive: true,
                cutout: '68%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        rtl: true,
                        labels: {
                            font: { family: 'Cairo', size: 11 },
                            color: '#475569',
                            padding: 12,
                            usePointStyle: true
                        }
                    },
                    tooltip: { rtl: true, bodyFont: { family: 'Cairo' } }
                }
            }
        });
    }

    /* ── Sponsorship Transfers Bar Chart ────── */
    const spCtx = document.getElementById('sponsorshipChart');
    if (spCtx) {
        new Chart(spCtx, {
            type: 'bar',
            data: {
                labels: @json($sponsorshipChart['months'] ?? []),
                datasets: [
                    {
                        label: 'إجمالي',
                        data: @json($sponsorshipChart['new'] ?? []),
                        backgroundColor: 'rgba(139,92,246,.75)',
                        borderRadius: 5,
                        borderSkipped: false,
                    },
                    {
                        label: 'مكتمل',
                        data: @json($sponsorshipChart['completed'] ?? []),
                        backgroundColor: 'rgba(22,163,74,.75)',
                        borderRadius: 5,
                        borderSkipped: false,
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false }, tooltip: { rtl: true, bodyFont: { family: 'Cairo' } } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { family: 'Cairo', size: 11 }, color: '#94a3b8' } },
                    y: { grid: { color: 'rgba(226,232,240,.6)' }, ticks: { font: { family: 'Cairo', size: 11 }, color: '#94a3b8', stepSize: 1 } }
                }
            }
        });
    }

    /* ── Contracts Monthly Bar Chart ─────────── */
    const conCtx = document.getElementById('contractsBarChart');
    if (conCtx) {
        new Chart(conCtx, {
            type: 'bar',
            data: {
                labels: @json($contractsChart['labels'] ?? []),
                datasets: [{
                    label: 'عقود جديدة',
                    data: @json($contractsChart['data'] ?? []),
                    backgroundColor: 'rgba(201,168,76,.8)',
                    borderRadius: 5,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false }, tooltip: { rtl: true, bodyFont: { family: 'Cairo' } } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { family: 'Cairo', size: 11 }, color: '#94a3b8' } },
                    y: { grid: { color: 'rgba(226,232,240,.6)' }, ticks: { font: { family: 'Cairo', size: 11 }, color: '#94a3b8', stepSize: 1 } }
                }
            }
        });
    }

    /* ── Complaints Monthly Bar Chart ────────── */
    const compCtx = document.getElementById('complaintsBarChart');
    if (compCtx) {
        new Chart(compCtx, {
            type: 'bar',
            data: {
                labels: @json($complaintsChart['months'] ?? []),
                datasets: [
                    {
                        label: 'مفتوحة',
                        data: @json($complaintsChart['open'] ?? []),
                        backgroundColor: 'rgba(239,68,68,.75)',
                        borderRadius: 5,
                        borderSkipped: false,
                    },
                    {
                        label: 'محلولة',
                        data: @json($complaintsChart['resolved'] ?? []),
                        backgroundColor: 'rgba(22,163,74,.75)',
                        borderRadius: 5,
                        borderSkipped: false,
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false }, tooltip: { rtl: true, bodyFont: { family: 'Cairo' } } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { family: 'Cairo', size: 11 }, color: '#94a3b8' } },
                    y: { grid: { color: 'rgba(226,232,240,.6)' }, ticks: { font: { family: 'Cairo', size: 11 }, color: '#94a3b8', stepSize: 1 } }
                }
            }
        });
    }

});
</script>
@endpush
