@extends('admin.layouts.app')
@section('title', 'لوحة التحكم')

@push('styles')
<style>
/* ── Grid helpers ─────────────────────────────── */
.dash-stat-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(170px,1fr)); gap:16px; margin-bottom:24px; }
.dash-2col      { display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:24px; }
.dash-3col      { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; }
.dash-mini-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); gap:14px; margin-bottom:24px; }
/* ── Stat Card ────────────────────────────────── */
.stat-card  { background:#fff; border-radius:14px; padding:18px 20px; box-shadow:0 1px 4px rgba(0,0,0,.07); display:flex; align-items:center; gap:14px; }
.stat-icon  { width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.stat-change { display:inline-flex; align-items:center; gap:3px; font-size:11px; font-weight:600; padding:2px 6px; border-radius:20px; }
.stat-change.up   { background:#f0fdf4; color:#16a34a; }
.stat-change.down { background:#fff1f2; color:#ef4444; }
/* ── Quick Action ─────────────────────────────── */
.qa-card { background:#fff; border-radius:12px; padding:18px 14px; box-shadow:0 1px 4px rgba(0,0,0,.07);
           display:flex; flex-direction:column; align-items:center; gap:10px; text-decoration:none;
           transition:box-shadow .15s,transform .15s; }
.qa-card:hover { box-shadow:0 4px 16px rgba(0,0,0,.1); transform:translateY(-2px); }
.qa-icon { width:46px; height:46px; border-radius:13px; display:flex; align-items:center; justify-content:center; }
/* ── Mini stat & table ────────────────────────── */
.mini-stat { background:#fff; border-radius:12px; padding:14px 16px; box-shadow:0 1px 4px rgba(0,0,0,.07); }
.tbl-card  { background:#fff; border-radius:14px; box-shadow:0 1px 4px rgba(0,0,0,.07); overflow:hidden; }
@media(max-width:900px){ .dash-2col{ grid-template-columns:1fr !important; } .dash-3col{ grid-template-columns:repeat(2,1fr) !important; } .dash-charts-row{ grid-template-columns:1fr !important; } }
@media(max-width:600px){ .dash-stat-grid{ grid-template-columns:repeat(2,1fr) !important; gap:10px !important; } .dash-3col{ grid-template-columns:1fr !important; } .dash-mini-grid{ grid-template-columns:repeat(2,1fr) !important; } }
</style>
@endpush

@section('content')

{{-- ── KPI Stat Cards ──────────────────────────────────────────────────── --}}
<div class="dash-stat-grid">

    {{-- Total Workers --}}
    <a href="{{ route('admin.workers.index') }}" class="stat-card" style="text-decoration:none;cursor:pointer;transition:box-shadow .15s,transform .15s;" onmouseover="this.style.boxShadow='0 4px 20px rgba(37,99,235,.15)';this.style.transform='translateY(-2px)'" onmouseout="this.style.boxShadow='';this.style.transform=''">
        <div class="stat-icon" style="background:#eff6ff;">
            <svg width="22" height="22" fill="none" stroke="#2563eb" stroke-width="1.8" viewBox="0 0 24 24">
                <circle cx="9" cy="7" r="4"/><path d="M3 21v-2a4 4 0 014-4h4a4 4 0 014 4v2"/>
                <path d="M16 3.13a4 4 0 010 7.75M21 21v-2a4 4 0 00-3-3.87"/>
            </svg>
        </div>
        <div>
            <p style="font-size:11px;color:#94a3b8;font-weight:500;margin:0 0 2px;">إجمالي الموظفين</p>
            <p style="font-size:24px;font-weight:800;color:#1e293b;line-height:1;margin:0;">{{ number_format($stats['total_workers']) }}</p>
            <p style="font-size:11px;color:#94a3b8;margin:2px 0 0;">موظف نشط</p>
        </div>
    </a>

    {{-- Active Contracts --}}
    <a href="{{ route('admin.contracts.index') }}" class="stat-card" style="text-decoration:none;cursor:pointer;transition:box-shadow .15s,transform .15s;" onmouseover="this.style.boxShadow='0 4px 20px rgba(22,163,74,.15)';this.style.transform='translateY(-2px)'" onmouseout="this.style.boxShadow='';this.style.transform=''">
        <div class="stat-icon" style="background:#f0fdf4;">
            <svg width="22" height="22" fill="none" stroke="#16a34a" stroke-width="1.8" viewBox="0 0 24 24">
                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
            </svg>
        </div>
        <div>
            <p style="font-size:11px;color:#94a3b8;font-weight:500;margin:0 0 2px;">العقود النشطة</p>
            <p style="font-size:24px;font-weight:800;color:#16a34a;line-height:1;margin:0;">{{ number_format($stats['active_contracts']) }}</p>
            <p style="font-size:11px;color:#94a3b8;margin:2px 0 0;">عقد نشط</p>
        </div>
    </a>

    {{-- Pending Contracts --}}
    <a href="{{ route('admin.contracts.index') }}?status=1" class="stat-card" style="text-decoration:none;cursor:pointer;transition:box-shadow .15s,transform .15s;" onmouseover="this.style.boxShadow='0 4px 20px rgba(249,115,22,.15)';this.style.transform='translateY(-2px)'" onmouseout="this.style.boxShadow='';this.style.transform=''">
        <div class="stat-icon" style="background:#fff7ed;">
            <svg width="22" height="22" fill="none" stroke="#f97316" stroke-width="1.8" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
        </div>
        <div>
            <p style="font-size:11px;color:#94a3b8;font-weight:500;margin:0 0 2px;">قيد المعالجة</p>
            <p style="font-size:24px;font-weight:800;color:#f97316;line-height:1;margin:0;">{{ number_format($stats['pending_contracts']) }}</p>
            <p style="font-size:11px;color:#94a3b8;margin:2px 0 0;">طلب</p>
        </div>
    </a>

    {{-- Total Income --}}
    <a href="{{ route('admin.incomes.index') }}" class="stat-card" style="text-decoration:none;cursor:pointer;transition:box-shadow .15s,transform .15s;" onmouseover="this.style.boxShadow='0 4px 20px rgba(5,150,105,.15)';this.style.transform='translateY(-2px)'" onmouseout="this.style.boxShadow='';this.style.transform=''">
        <div class="stat-icon" style="background:#ecfdf5;">
            <svg width="22" height="22" fill="none" stroke="#059669" stroke-width="1.8" viewBox="0 0 24 24">
                <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/>
            </svg>
        </div>
        <div>
            <p style="font-size:11px;color:#94a3b8;font-weight:500;margin:0 0 2px;">إجمالي الإيرادات</p>
            <p style="font-size:22px;font-weight:800;color:#059669;line-height:1;margin:0;">{{ number_format($stats['total_income'], 0) }}</p>
            <div style="display:flex;align-items:center;gap:6px;margin-top:3px;">
                <span style="font-size:10px;color:#94a3b8;">ريال سعودي</span>
                @if($stats['income_change'] != 0)
                <span class="stat-change {{ $stats['income_change'] >= 0 ? 'up' : 'down' }}">{{ $stats['income_change'] >= 0 ? '↑' : '↓' }}{{ abs($stats['income_change']) }}%</span>
                @endif
            </div>
        </div>
    </a>

    {{-- Total Expenses --}}
    <a href="{{ route('admin.expenses.index') }}" class="stat-card" style="text-decoration:none;cursor:pointer;transition:box-shadow .15s,transform .15s;" onmouseover="this.style.boxShadow='0 4px 20px rgba(239,68,68,.15)';this.style.transform='translateY(-2px)'" onmouseout="this.style.boxShadow='';this.style.transform=''">
        <div class="stat-icon" style="background:#fff1f2;">
            <svg width="22" height="22" fill="none" stroke="#ef4444" stroke-width="1.8" viewBox="0 0 24 24">
                <rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/>
            </svg>
        </div>
        <div>
            <p style="font-size:11px;color:#94a3b8;font-weight:500;margin:0 0 2px;">إجمالي المصروفات</p>
            <p style="font-size:22px;font-weight:800;color:#ef4444;line-height:1;margin:0;">{{ number_format($stats['total_expenses'], 0) }}</p>
            <div style="display:flex;align-items:center;gap:6px;margin-top:3px;">
                <span style="font-size:10px;color:#94a3b8;">ريال سعودي</span>
                @if($stats['expenses_change'] != 0)
                <span class="stat-change {{ $stats['expenses_change'] <= 0 ? 'up' : 'down' }}">{{ $stats['expenses_change'] >= 0 ? '↑' : '↓' }}{{ abs($stats['expenses_change']) }}%</span>
                @endif
            </div>
        </div>
    </a>

    {{-- Net Profit --}}
    <div class="stat-card">
        <div class="stat-icon" style="background:{{ $stats['net_profit'] >= 0 ? '#eff6ff' : '#fff1f2' }};">
            <svg width="22" height="22" fill="none" stroke="{{ $stats['net_profit'] >= 0 ? '#2563eb' : '#ef4444' }}" stroke-width="1.8" viewBox="0 0 24 24">
                <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/>
            </svg>
        </div>
        <div>
            <p style="font-size:11px;color:#94a3b8;font-weight:500;margin:0 0 2px;">إجمالي الأرباح</p>
            <p style="font-size:22px;font-weight:800;color:{{ $stats['net_profit'] >= 0 ? '#2563eb' : '#ef4444' }};line-height:1;margin:0;">{{ number_format(abs($stats['net_profit']), 0) }}</p>
            <p style="font-size:10px;color:#94a3b8;margin:2px 0 0;">ريال سعودي</p>
        </div>
    </div>

</div>

{{-- ── Charts + Quick Actions (same row) ───────────────────────────────── --}}
<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;margin-bottom:24px;" class="dash-charts-row">

    {{-- Quick Actions --}}
    <div style="background:#fff;border-radius:14px;padding:20px;box-shadow:0 1px 4px rgba(0,0,0,.07);">
        <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 14px;">إجراءات سريعة</p>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
        <a href="{{ route('admin.contracts.create') }}" class="qa-card">
            <div class="qa-icon" style="background:#eff6ff;">
                <svg width="22" height="22" fill="none" stroke="#2563eb" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>
                    <line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/>
                </svg>
            </div>
            <span style="font-size:12.5px;font-weight:600;color:#334155;">عقد جديد</span>
        </a>
        <a href="{{ route('admin.clients.create') }}" class="qa-card">
            <div class="qa-icon" style="background:#f0fdf4;">
                <svg width="22" height="22" fill="none" stroke="#16a34a" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
                    <line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/>
                </svg>
            </div>
            <span style="font-size:12.5px;font-weight:600;color:#334155;">إضافة عميل</span>
        </a>
        <a href="{{ route('admin.sponsorship-transfers.create') }}" class="qa-card">
            <div class="qa-icon" style="background:#faf5ff;">
                <svg width="22" height="22" fill="none" stroke="#9333ea" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M7 16l-4-4 4-4"/><path d="M3 12h18"/><path d="M17 8l4 4-4 4"/>
                </svg>
            </div>
            <span style="font-size:12.5px;font-weight:600;color:#334155;">نقل كفالة</span>
        </a>
        <a href="{{ route('admin.contracts.create') }}" class="qa-card">
            <div class="qa-icon" style="background:#fff7ed;">
                <svg width="22" height="22" fill="none" stroke="#f97316" stroke-width="1.8" viewBox="0 0 24 24">
                    <rect x="2" y="7" width="20" height="14" rx="2"/>
                    <path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/>
                    <line x1="12" y1="12" x2="12" y2="16"/><line x1="10" y1="14" x2="14" y2="14"/>
                </svg>
            </div>
            <span style="font-size:12.5px;font-weight:600;color:#334155;">عقد استقدام</span>
        </a>
        <a href="{{ route('admin.reports.branch-statement') }}" class="qa-card">
            <div class="qa-icon" style="background:#ecfdf5;">
                <svg width="22" height="22" fill="none" stroke="#059669" stroke-width="1.8" viewBox="0 0 24 24">
                    <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/>
                    <line x1="6" y1="20" x2="6" y2="14"/><line x1="2" y1="20" x2="22" y2="20"/>
                </svg>
            </div>
            <span style="font-size:12.5px;font-weight:600;color:#334155;">تقرير مالي</span>
        </a>
        <a href="{{ route('admin.workers.create') }}" class="qa-card">
            <div class="qa-icon" style="background:#fef9c3;">
                <svg width="22" height="22" fill="none" stroke="#ca8a04" stroke-width="1.8" viewBox="0 0 24 24">
                    <circle cx="9" cy="7" r="4"/><path d="M3 21v-2a4 4 0 014-4h4a4 4 0 014 4v2"/>
                    <line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/>
                </svg>
            </div>
            <span style="font-size:12.5px;font-weight:600;color:#334155;">إضافة موظف</span>
        </a>
        </div>
    </div>

    {{-- Income vs Expenses (bar) --}}
    <div style="background:#fff;border-radius:14px;padding:20px;box-shadow:0 1px 4px rgba(0,0,0,.07);">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0;">الدخل مقابل المصاريف ({{ now()->year }})</p>
            <div style="display:flex;align-items:center;gap:10px;">
                <span style="display:flex;align-items:center;gap:4px;font-size:11px;color:#64748b;"><span style="width:10px;height:10px;border-radius:3px;background:#16a34a;display:inline-block;"></span>الدخل</span>
                <span style="display:flex;align-items:center;gap:4px;font-size:11px;color:#64748b;"><span style="width:10px;height:10px;border-radius:3px;background:#ef4444;display:inline-block;"></span>المصروفات</span>
            </div>
        </div>
        <div style="position:relative;width:100%;"><canvas id="incomeExpenseChart" height="200" style="max-width:100%;"></canvas></div>
    </div>

    {{-- Branch Comparison (line) --}}
    <div style="background:#fff;border-radius:14px;padding:20px;box-shadow:0 1px 4px rgba(0,0,0,.07);">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0;">مقارنة الفروع ({{ now()->year }})</p>
            <span style="font-size:11px;color:#64748b;background:#f8fafc;padding:3px 10px;border-radius:20px;border:1px solid #e2e8f0;">سنة {{ now()->year }}</span>
        </div>
        <div style="position:relative;width:100%;"><canvas id="branchChart" height="200" style="max-width:100%;"></canvas></div>
    </div>

</div>

{{-- ── Charts Row 2: Contracts monthly · Status donut · Campaigns ───────── --}}
<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;margin-bottom:24px;" class="dash-charts-row">

    {{-- Contracts Monthly Bar --}}
    <div style="background:#fff;border-radius:14px;padding:20px;box-shadow:0 1px 4px rgba(0,0,0,.07);">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0;">عقود الاستقدام الشهرية</p>
            <a href="{{ route('admin.contracts.index') }}" style="font-size:11px;color:#2563eb;text-decoration:none;font-weight:500;">عرض الكل ←</a>
        </div>
        <canvas id="contractsChart" height="200" style="max-width:100%;"></canvas>
    </div>

    {{-- Contracts by Status Donut --}}
    <div style="background:#fff;border-radius:14px;padding:20px;box-shadow:0 1px 4px rgba(0,0,0,.07);">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0;">مقارنة حالات العقود</p>
            <a href="{{ route('admin.contracts.index') }}" style="font-size:11px;color:#9333ea;text-decoration:none;font-weight:500;">عرض الكل ←</a>
        </div>
        <canvas id="statusChart" height="200" style="max-width:100%;"></canvas>
    </div>

    {{-- Campaigns Comparison --}}
    <div style="background:#fff;border-radius:14px;padding:20px;box-shadow:0 1px 4px rgba(0,0,0,.07);">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0;">حملات التسويق</p>
            <a href="{{ route('admin.marketing.campaigns.index') }}" style="font-size:11px;color:#f97316;text-decoration:none;font-weight:500;">عرض الكل ←</a>
        </div>
        <canvas id="campaignsChart" height="200" style="max-width:100%;"></canvas>
    </div>

</div>

{{-- ── Secondary Mini Stats ─────────────────────────────────────────────── --}}
<div class="dash-mini-grid">
    <div class="mini-stat">
        <p style="font-size:11px;color:#94a3b8;margin:0 0 4px;font-weight:500;">إجراءات العقود</p>
        <p style="font-size:22px;font-weight:800;color:#1e293b;margin:0;line-height:1;">{{ $stats['contracts_this_month'] }}</p>
        <div style="display:flex;align-items:center;justify-content:space-between;margin-top:5px;">
            <span style="font-size:10px;color:#94a3b8;">هذا الشهر</span>
            @if($stats['contracts_change'] != 0)
            <span class="stat-change {{ $stats['contracts_change'] >= 0 ? 'up' : 'down' }}">{{ $stats['contracts_change'] >= 0 ? '↑' : '↓' }}{{ abs($stats['contracts_change']) }}%</span>
            @endif
        </div>
    </div>
    <div class="mini-stat">
        <p style="font-size:11px;color:#94a3b8;margin:0 0 4px;font-weight:500;">طلبات المعالجة</p>
        <p style="font-size:22px;font-weight:800;color:#1e293b;margin:0;line-height:1;">{{ $stats['pending_expenses'] }}</p>
        <div style="margin-top:5px;"><span style="font-size:10px;color:#94a3b8;">تنتظر الموافقة</span></div>
    </div>
    <div class="mini-stat">
        <p style="font-size:11px;color:#94a3b8;margin:0 0 4px;font-weight:500;">التحويلات المعلقة</p>
        <p style="font-size:22px;font-weight:800;color:#1e293b;margin:0;line-height:1;">{{ $stats['pending_transfers'] }}</p>
        <div style="margin-top:5px;"><span style="font-size:10px;color:#94a3b8;">تنتظر الموافقة</span></div>
    </div>
    <div class="mini-stat">
        <p style="font-size:11px;color:#94a3b8;margin:0 0 4px;font-weight:500;">الشكاوي</p>
        <p style="font-size:22px;font-weight:800;color:#1e293b;margin:0;line-height:1;">{{ $stats['complaints_this_month'] }}</p>
        <div style="display:flex;align-items:center;justify-content:space-between;margin-top:5px;">
            <span style="font-size:10px;color:#94a3b8;">هذا الشهر</span>
            @if($stats['complaints_change'] != 0)
            <span class="stat-change {{ $stats['complaints_change'] <= 0 ? 'up' : 'down' }}">{{ $stats['complaints_change'] >= 0 ? '↑' : '↓' }}{{ abs($stats['complaints_change']) }}%</span>
            @endif
        </div>
    </div>
    <div class="mini-stat">
        <p style="font-size:11px;color:#94a3b8;margin:0 0 4px;font-weight:500;">الفروع النشطة</p>
        <p style="font-size:22px;font-weight:800;color:#1e293b;margin:0;line-height:1;">{{ $stats['branch_count'] }}</p>
        <div style="margin-top:5px;"><span style="font-size:10px;color:#94a3b8;">فرع</span></div>
    </div>
    <div class="mini-stat">
        <p style="font-size:11px;color:#94a3b8;margin:0 0 4px;font-weight:500;">نسبة الإنجاز</p>
        <p style="font-size:22px;font-weight:800;color:#2563eb;margin:0;line-height:1;">{{ $stats['completion_rate'] }}%</p>
        <div style="margin-top:5px;"><span style="font-size:10px;color:#94a3b8;">من إجمالي المهام</span></div>
    </div>
</div>
<div class="dash-2col">

{{-- ── Recent Tables ────────────────────────────────────────────────────── --}}
<div class="dash-2col">

    {{-- Recent Incomes --}}
    <div class="tbl-card">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #f1f5f9;">
            <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0;">أحدث الإيرادات</p>
            <a href="{{ route('admin.incomes.index') }}" style="font-size:12px;color:#2563eb;text-decoration:none;font-weight:500;">عرض الكل ←</a>
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:600;color:#94a3b8;">النوع</th>
                        <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:600;color:#94a3b8;">المبلغ</th>
                        <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:600;color:#94a3b8;">التاريخ</th>
                        <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:600;color:#94a3b8;">الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentIncomes as $income)
                    <tr style="border-bottom:1px solid #f8fafc;" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
                        <td style="padding:10px 16px;font-size:12.5px;color:#334155;">{{ $income->incomeType?->name ?? '—' }}</td>
                        <td style="padding:10px 16px;font-size:12.5px;font-weight:700;color:#059669;">{{ number_format($income->amount, 0) }} <span style="font-size:10px;font-weight:400;color:#94a3b8;">ر.س</span></td>
                        <td style="padding:10px 16px;font-size:11px;color:#94a3b8;">{{ $income->date?->format('Y-m-d') }}</td>
                        <td style="padding:10px 16px;"><span style="display:inline-flex;align-items:center;padding:3px 9px;background:#f0fdf4;color:#16a34a;border-radius:20px;font-size:11px;font-weight:600;">تم التحصيل</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="padding:32px;text-align:center;color:#cbd5e1;font-size:13px;">لا توجد إيرادات</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent Expenses --}}
    <div class="tbl-card">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #f1f5f9;">
            <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0;">أحدث المصروفات</p>
            <a href="{{ route('admin.expenses.index') }}" style="font-size:12px;color:#2563eb;text-decoration:none;font-weight:500;">عرض الكل ←</a>
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:600;color:#94a3b8;">النوع</th>
                        <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:600;color:#94a3b8;">المبلغ</th>
                        <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:600;color:#94a3b8;">التاريخ</th>
                        <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:600;color:#94a3b8;">الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentExpenses as $expense)
                    <tr style="border-bottom:1px solid #f8fafc;" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
                        <td style="padding:10px 16px;font-size:12.5px;color:#334155;">{{ $expense->expenseType?->name ?? '—' }}</td>
                        <td style="padding:10px 16px;font-size:12.5px;font-weight:700;color:#ef4444;">{{ number_format($expense->amount, 0) }} <span style="font-size:10px;font-weight:400;color:#94a3b8;">ر.س</span></td>
                        <td style="padding:10px 16px;font-size:11px;color:#94a3b8;">{{ $expense->date?->format('Y-m-d') }}</td>
                        <td style="padding:10px 16px;">
                            @if($expense->status === 'approved')
                                <span style="display:inline-flex;align-items:center;padding:3px 9px;background:#f0fdf4;color:#16a34a;border-radius:20px;font-size:11px;font-weight:600;">مدفوعة</span>
                            @elseif($expense->status === 'pending')
                                <span style="display:inline-flex;align-items:center;padding:3px 9px;background:#fff7ed;color:#f97316;border-radius:20px;font-size:11px;font-weight:600;">قيد المعالجة</span>
                            @else
                                <span style="display:inline-flex;align-items:center;padding:3px 9px;background:#fff1f2;color:#ef4444;border-radius:20px;font-size:11px;font-weight:600;">مرفوض</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="padding:32px;text-align:center;color:#cbd5e1;font-size:13px;">لا توجد مصاريف</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ── Pending Approvals ────────────────────────────────────────────────── --}}
@if($pendingExpenses->count() || $pendingTransfers->count())
<div style="background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.07);margin-bottom:8px;">
    <div style="display:flex;align-items:center;gap:10px;padding:14px 20px;border-bottom:1px solid #fde68a;background:#fffbeb;">
        <div style="width:32px;height:32px;border-radius:8px;background:#fef3c7;display:flex;align-items:center;justify-content:center;">
            <svg width="16" height="16" fill="none" stroke="#f59e0b" stroke-width="2" viewBox="0 0 24 24">
                <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
        </div>
        <p style="font-size:13px;font-weight:700;color:#92400e;">طلبات الموافقة المعلقة ({{ $pendingExpenses->count() + $pendingTransfers->count() }})</p>
    </div>
    <div style="padding:12px 16px;display:flex;flex-direction:column;gap:8px;">
        @foreach($pendingExpenses->take(5) as $expense)
        <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;background:#fffbeb;border:1px solid #fde68a;border-radius:10px;">
            <div>
                <p style="font-size:13px;color:#334155;font-weight:500;">مصروف: <span style="font-weight:700;">{{ $expense->branch?->name }}</span></p>
                <p style="font-size:12px;color:#f97316;font-weight:600;">{{ number_format($expense->amount, 2) }} ريال</p>
            </div>
            <div style="display:flex;gap:6px;">
                <form action="{{ route('admin.expenses.approve', $expense->id) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" style="padding:5px 12px;background:#16a34a;color:#fff;border-radius:7px;border:none;font-size:12px;font-weight:600;cursor:pointer;font-family:Cairo,sans-serif;">موافقة</button>
                </form>
                <a href="{{ route('admin.expenses.show', $expense->id) }}" style="padding:5px 12px;background:#f1f5f9;color:#475569;border-radius:7px;font-size:12px;font-weight:600;text-decoration:none;">عرض</a>
            </div>
        </div>
        @endforeach

        @foreach($pendingTransfers->take(5) as $transfer)
        <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;background:#fefce8;border:1px solid #fef08a;border-radius:10px;">
            <div>
                <p style="font-size:13px;color:#334155;font-weight:500;">تحويل: <span style="font-weight:700;">{{ $transfer->fromBranch?->name }}</span> → {{ $transfer->toBranch?->name }}</p>
                <p style="font-size:12px;color:#ca8a04;font-weight:600;">{{ number_format($transfer->amount, 2) }} ريال</p>
            </div>
            <div style="display:flex;gap:6px;">
                <form action="{{ route('admin.transfers.approve', $transfer->id) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" style="padding:5px 12px;background:#16a34a;color:#fff;border-radius:7px;border:none;font-size:12px;font-weight:600;cursor:pointer;font-family:Cairo,sans-serif;">موافقة</button>
                </form>
                <a href="{{ route('admin.transfers.show', $transfer->id) }}" style="padding:5px 12px;background:#f1f5f9;color:#475569;border-radius:7px;font-size:12px;font-weight:600;text-decoration:none;">عرض</a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const chartData  = @json($chartData);
    const branchData = @json($branchChart);

    Chart.defaults.font.family = 'Cairo, sans-serif';
    Chart.defaults.font.size   = 11;
    Chart.defaults.color       = '#94a3b8';

    // ── Branch Comparison — LINE ──────────────────────────────────────
    new Chart(document.getElementById('branchChart'), {
        type: 'line',
        data: { labels: branchData.labels, datasets: branchData.datasets },
        options: {
            responsive: true, maintainAspectRatio: true,
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, pointStyleWidth: 8, padding: 12, font: { size: 11 } } } },
            scales: {
                x: { grid: { display: false }, border: { display: false } },
                y: { grid: { color: '#f1f5f9' }, border: { display: false }, beginAtZero: true,
                     ticks: { callback: v => v >= 1000 ? (v/1000).toFixed(0)+'k' : v } }
            }
        }
    });

    // ── Income vs Expenses — BAR ──────────────────────────────────────
    new Chart(document.getElementById('incomeExpenseChart'), {
        type: 'bar',
        data: {
            labels: chartData.months,
            datasets: [
                { label: 'الدخل',      data: chartData.incomes,  backgroundColor: 'rgba(22,163,74,.75)',  borderRadius: 4, borderSkipped: false },
                { label: 'المصاريف',   data: chartData.expenses, backgroundColor: 'rgba(239,68,68,.65)',  borderRadius: 4, borderSkipped: false }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: true,
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, border: { display: false } },
                y: { grid: { color: '#f1f5f9' }, border: { display: false }, beginAtZero: true,
                     ticks: { callback: v => v >= 1000 ? (v/1000).toFixed(0)+'k' : v } }
            }
        }
    });

    const contractsData = @json($contractsChart);
    const statusData    = @json($statusChart);
    const campaignsData = @json($campaignsChart);

    // ── Contracts Monthly — BAR ────────────────────────────────────────
    new Chart(document.getElementById('contractsChart'), {
        type: 'bar',
        data: {
            labels: contractsData.labels,
            datasets: [{
                label: 'عقود جديدة',
                data: contractsData.data,
                backgroundColor: 'rgba(37,99,235,.72)',
                borderRadius: 5, borderSkipped: false,
                hoverBackgroundColor: 'rgba(37,99,235,.9)'
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: true,
            plugins: { legend: { display: false },
                tooltip: { callbacks: { label: ctx => `${ctx.parsed.y} عقد` } }
            },
            scales: {
                x: { grid: { display: false }, border: { display: false } },
                y: { grid: { color: '#f1f5f9' }, border: { display: false }, beginAtZero: true, ticks: { precision: 0 } }
            }
        }
    });

    // ── Status Distribution — DOUGHNUT ────────────────────────────────
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: statusData.labels,
            datasets: [{
                data: statusData.data,
                backgroundColor: statusData.colors,
                borderWidth: 2, borderColor: '#fff', hoverOffset: 6
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: true,
            cutout: '60%',
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, pointStyleWidth: 8, padding: 10, font: { size: 11 } } },
                tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed} عقد` } }
            }
        }
    });

    // ── Campaigns — HORIZONTAL BAR ────────────────────────────────────
    new Chart(document.getElementById('campaignsChart'), {
        type: 'bar',
        data: {
            labels: campaignsData.labels.length ? campaignsData.labels : ['لا توجد حملات'],
            datasets: [
                { label: 'إجمالي العملاء المحتملين', data: campaignsData.leads,     backgroundColor: 'rgba(249,115,22,.72)', borderRadius: 4, borderSkipped: false },
                { label: 'تم التحويل',                data: campaignsData.converted, backgroundColor: 'rgba(22,163,74,.72)',  borderRadius: 4, borderSkipped: false }
            ]
        },
        options: {
            indexAxis: 'y',
            responsive: true, maintainAspectRatio: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, pointStyleWidth: 8, padding: 10, font: { size: 11 } } }
            },
            scales: {
                x: { grid: { color: '#f1f5f9' }, border: { display: false }, beginAtZero: true, ticks: { precision: 0 } },
                y: { grid: { display: false }, border: { display: false }, ticks: { font: { size: 11 } } }
            }
        }
    });
});
</script>
@endpush
