@extends('admin.layouts.app')
@section('title', 'لوحة التحكم')

@section('content')

{{-- ── Stat Cards ─────────────────────────────────────────────────────── --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:24px;">

    {{-- Total Income --}}
    <div class="card" style="padding:20px;display:flex;align-items:center;gap:16px;">
        <div class="stat-icon" style="background:#f0fdf4;">
            <svg width="20" height="20" fill="none" stroke="#16a34a" stroke-width="1.8" viewBox="0 0 24 24">
                <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/>
                <polyline points="16 7 22 7 22 13"/>
            </svg>
        </div>
        <div>
            <p style="font-size:11px;color:#94a3b8;font-weight:500;margin-bottom:4px;">إجمالي الدخل</p>
            <p style="font-size:22px;font-weight:800;color:#16a34a;line-height:1;">{{ number_format($stats['total_income'], 2) }}</p>
            <p style="font-size:11px;color:#cbd5e1;margin-top:3px;">ريال سعودي</p>
        </div>
    </div>

    {{-- Total Expenses --}}
    <div class="card" style="padding:20px;display:flex;align-items:center;gap:16px;">
        <div class="stat-icon" style="background:#fff1f2;">
            <svg width="20" height="20" fill="none" stroke="#ef4444" stroke-width="1.8" viewBox="0 0 24 24">
                <rect x="2" y="5" width="20" height="14" rx="2"/>
                <line x1="2" y1="10" x2="22" y2="10"/>
            </svg>
        </div>
        <div>
            <p style="font-size:11px;color:#94a3b8;font-weight:500;margin-bottom:4px;">إجمالي المصاريف</p>
            <p style="font-size:22px;font-weight:800;color:#ef4444;line-height:1;">{{ number_format($stats['total_expenses'], 2) }}</p>
            <p style="font-size:11px;color:#cbd5e1;margin-top:3px;">المعتمدة فقط</p>
        </div>
    </div>

    {{-- Net Profit --}}
    <div class="card" style="padding:20px;display:flex;align-items:center;gap:16px;">
        <div class="stat-icon" style="background:#eff6ff;">
            <svg width="20" height="20" fill="none" stroke="#2563eb" stroke-width="1.8" viewBox="0 0 24 24">
                <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
            </svg>
        </div>
        <div>
            <p style="font-size:11px;color:#94a3b8;font-weight:500;margin-bottom:4px;">صافي الربح</p>
            <p style="font-size:22px;font-weight:800;line-height:1;color:{{ $stats['net_profit'] >= 0 ? '#2563eb' : '#ef4444' }};">
                {{ number_format($stats['net_profit'], 2) }}
            </p>
            <p style="font-size:11px;color:#cbd5e1;margin-top:3px;">ريال سعودي</p>
        </div>
    </div>

    {{-- Active Branches --}}
    <div class="card" style="padding:20px;display:flex;align-items:center;gap:16px;">
        <div class="stat-icon" style="background:#faf5ff;">
            <svg width="20" height="20" fill="none" stroke="#9333ea" stroke-width="1.8" viewBox="0 0 24 24">
                <path d="M3 21h18M3 7l9-4 9 4M4 7v14M20 7v14M9 21V11h6v10"/>
            </svg>
        </div>
        <div>
            <p style="font-size:11px;color:#94a3b8;font-weight:500;margin-bottom:4px;">الفروع النشطة</p>
            <p style="font-size:22px;font-weight:800;color:#9333ea;line-height:1;">{{ $stats['branch_count'] }}</p>
            <p style="font-size:11px;color:#cbd5e1;margin-top:3px;">فرع</p>
        </div>
    </div>

    {{-- Pending Expenses --}}
    <div class="card" style="padding:20px;display:flex;align-items:center;gap:16px;">
        <div class="stat-icon" style="background:#fff7ed;">
            <svg width="20" height="20" fill="none" stroke="#f97316" stroke-width="1.8" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
        </div>
        <div>
            <p style="font-size:11px;color:#94a3b8;font-weight:500;margin-bottom:4px;">مصاريف معلقة</p>
            <p style="font-size:22px;font-weight:800;color:#f97316;line-height:1;">{{ $stats['pending_expenses'] }}</p>
            <p style="font-size:11px;color:#cbd5e1;margin-top:3px;">تنتظر الموافقة</p>
        </div>
    </div>

    {{-- Pending Transfers --}}
    <div class="card" style="padding:20px;display:flex;align-items:center;gap:16px;">
        <div class="stat-icon" style="background:#fefce8;">
            <svg width="20" height="20" fill="none" stroke="#ca8a04" stroke-width="1.8" viewBox="0 0 24 24">
                <path d="M7 16l-4-4 4-4M17 8l4 4-4 4M14 4l-4 16"/>
            </svg>
        </div>
        <div>
            <p style="font-size:11px;color:#94a3b8;font-weight:500;margin-bottom:4px;">تحويلات معلقة</p>
            <p style="font-size:22px;font-weight:800;color:#ca8a04;line-height:1;">{{ $stats['pending_transfers'] }}</p>
            <p style="font-size:11px;color:#cbd5e1;margin-top:3px;">تنتظر الموافقة</p>
        </div>
    </div>
</div>

{{-- ── Charts ──────────────────────────────────────────────────────────── --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">
    <div class="card" style="padding:20px;">
        <p style="font-size:13px;font-weight:700;color:#0f172a;margin-bottom:16px;">الدخل مقابل المصاريف ({{ now()->year }})</p>
        <canvas id="incomeExpenseChart" height="180"></canvas>
    </div>
    <div class="card" style="padding:20px;">
        <p style="font-size:13px;font-weight:700;color:#0f172a;margin-bottom:16px;">مقارنة الفروع ({{ now()->year }})</p>
        <canvas id="branchChart" height="180"></canvas>
    </div>
</div>

{{-- ── Recent Tables ────────────────────────────────────────────────────── --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">

    {{-- Recent Incomes --}}
    <div class="card" style="overflow:hidden;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #f1f5f9;">
            <p style="font-size:13px;font-weight:700;color:#0f172a;">أحدث الإيرادات</p>
            <a href="{{ route('admin.incomes.index') }}" style="font-size:12px;color:#2563eb;text-decoration:none;font-weight:500;">عرض الكل ←</a>
        </div>
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>الفرع</th><th>النوع</th><th>المبلغ</th><th>التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentIncomes as $income)
                    <tr>
                        <td style="font-size:12px;color:#334155;">{{ $income->branch?->name ?? '—' }}</td>
                        <td style="font-size:12px;color:#64748b;">{{ $income->incomeType?->name ?? '—' }}</td>
                        <td style="font-size:13px;font-weight:600;color:#16a34a;">{{ number_format($income->amount, 2) }}</td>
                        <td style="font-size:11px;color:#94a3b8;">{{ $income->date?->format('Y-m-d') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="padding:32px;text-align:center;color:#cbd5e1;font-size:13px;">لا توجد إيرادات</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent Expenses --}}
    <div class="card" style="overflow:hidden;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #f1f5f9;">
            <p style="font-size:13px;font-weight:700;color:#0f172a;">أحدث المصاريف</p>
            <a href="{{ route('admin.expenses.index') }}" style="font-size:12px;color:#2563eb;text-decoration:none;font-weight:500;">عرض الكل ←</a>
        </div>
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>الفرع</th><th>النوع</th><th>المبلغ</th><th>الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentExpenses as $expense)
                    <tr>
                        <td style="font-size:12px;color:#334155;">{{ $expense->branch?->name ?? '—' }}</td>
                        <td style="font-size:12px;color:#64748b;">{{ $expense->expenseType?->name ?? '—' }}</td>
                        <td style="font-size:13px;font-weight:600;color:#ef4444;">{{ number_format($expense->amount, 2) }}</td>
                        <td>
                            @if($expense->status === 'approved')
                                <span style="display:inline-flex;align-items:center;padding:2px 8px;background:#f0fdf4;color:#16a34a;border-radius:20px;font-size:11px;font-weight:600;">معتمد</span>
                            @elseif($expense->status === 'pending')
                                <span style="display:inline-flex;align-items:center;padding:2px 8px;background:#fff7ed;color:#f97316;border-radius:20px;font-size:11px;font-weight:600;">معلق</span>
                            @else
                                <span style="display:inline-flex;align-items:center;padding:2px 8px;background:#fff1f2;color:#ef4444;border-radius:20px;font-size:11px;font-weight:600;">مرفوض</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="padding:32px;text-align:center;color:#cbd5e1;font-size:13px;">لا توجد مصاريف</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ── Pending Approvals ────────────────────────────────────────────────── --}}
@if($pendingExpenses->count() || $pendingTransfers->count())
<div class="card" style="overflow:hidden;margin-bottom:24px;">
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
Chart.defaults.font.size   = 12;
Chart.defaults.color       = '#94a3b8';

new Chart(document.getElementById('incomeExpenseChart'), {
    type: 'line',
    data: {
        labels: chartData.months,
        datasets: [
            {
                label: 'الدخل', data: chartData.incomes,
                borderColor: '#16a34a', backgroundColor: 'rgba(22,163,74,.08)',
                borderWidth: 2.5, tension: 0.4, fill: true,
                pointRadius: 4, pointHoverRadius: 6,
                pointBackgroundColor: '#16a34a',
            },
            {
                label: 'المصاريف', data: chartData.expenses,
                borderColor: '#ef4444', backgroundColor: 'rgba(239,68,68,.06)',
                borderWidth: 2.5, tension: 0.4, fill: true,
                pointRadius: 4, pointHoverRadius: 6,
                pointBackgroundColor: '#ef4444',
            }
        ]
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: { legend: { position: 'top', labels: { usePointStyle: true, pointStyleWidth: 8, padding: 16 } } },
        scales: {
            x: { grid: { display: false } },
            y: { grid: { color: '#f1f5f9' }, border: { display: false }, beginAtZero: true }
        }
    }
});

new Chart(document.getElementById('branchChart'), {
    type: 'line',
    data: {
        labels: branchData.labels,
        datasets: branchData.datasets,
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: { legend: { position: 'top', labels: { usePointStyle: true, pointStyleWidth: 8, padding: 16 } } },
        scales: {
            x: { grid: { display: false } },
            y: { grid: { color: '#f1f5f9' }, border: { display: false }, beginAtZero: true }
        }
    }
});
});
</script>
@endpush
