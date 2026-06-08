<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Expense;
use App\Models\FinancialTransfer;
use App\Models\Income;
use App\Models\RecruitmentContract;
use Carbon\Carbon;

class ReportService
{
    public function getBranchStatement(int $branchId, ?string $dateFrom, ?string $dateTo): array
    {
        $incomes = Income::with(['incomeType', 'admin'])
            ->where('branch_id', $branchId)
            ->when($dateFrom, fn($q) => $q->whereDate('date', '>=', $dateFrom))
            ->when($dateTo,   fn($q) => $q->whereDate('date', '<=', $dateTo))
            ->orderBy('date')
            ->get();

        $expenses = Expense::with(['expenseType', 'admin'])
            ->where('branch_id', $branchId)
            ->where('status', 'approved')
            ->when($dateFrom, fn($q) => $q->whereDate('date', '>=', $dateFrom))
            ->when($dateTo,   fn($q) => $q->whereDate('date', '<=', $dateTo))
            ->orderBy('date')
            ->get();

        $transfersOut = FinancialTransfer::with(['toBranch'])
            ->where('from_branch_id', $branchId)
            ->where('status', 'approved')
            ->when($dateFrom, fn($q) => $q->whereDate('date', '>=', $dateFrom))
            ->when($dateTo,   fn($q) => $q->whereDate('date', '<=', $dateTo))
            ->orderBy('date')
            ->get();

        $transfersIn = FinancialTransfer::with(['fromBranch'])
            ->where('to_branch_id', $branchId)
            ->where('status', 'approved')
            ->when($dateFrom, fn($q) => $q->whereDate('date', '>=', $dateFrom))
            ->when($dateTo,   fn($q) => $q->whereDate('date', '<=', $dateTo))
            ->orderBy('date')
            ->get();

        $totalIncome      = $incomes->sum('amount') + $transfersIn->sum('amount');
        $totalExpenses    = $expenses->sum('amount') + $transfersOut->sum('amount');

        return [
            'branch'         => Branch::findOrFail($branchId),
            'incomes'        => $incomes,
            'expenses'       => $expenses,
            'transfers_out'  => $transfersOut,
            'transfers_in'   => $transfersIn,
            'total_income'   => $totalIncome,
            'total_expenses' => $totalExpenses,
            'net_balance'    => $totalIncome - $totalExpenses,
            'date_from'      => $dateFrom,
            'date_to'        => $dateTo,
        ];
    }

    public function getIncomeStatement(array $branchIds, ?string $dateFrom, ?string $dateTo): array
    {
        $query = Branch::query();
        if (!empty($branchIds)) {
            $query->whereIn('id', $branchIds);
        }
        $branches = $query->where('active', true)->get();

        $rows = $branches->map(function ($branch) use ($dateFrom, $dateTo) {
            $totalIncome = Income::where('branch_id', $branch->id)
                ->when($dateFrom, fn($q) => $q->whereDate('date', '>=', $dateFrom))
                ->when($dateTo,   fn($q) => $q->whereDate('date', '<=', $dateTo))
                ->sum('amount');

            $totalApprovedExpenses = Expense::where('branch_id', $branch->id)
                ->where('status', 'approved')
                ->when($dateFrom, fn($q) => $q->whereDate('date', '>=', $dateFrom))
                ->when($dateTo,   fn($q) => $q->whereDate('date', '<=', $dateTo))
                ->sum('amount');

            $pendingExpenses = Expense::where('branch_id', $branch->id)
                ->where('status', 'pending')
                ->when($dateFrom, fn($q) => $q->whereDate('date', '>=', $dateFrom))
                ->when($dateTo,   fn($q) => $q->whereDate('date', '<=', $dateTo))
                ->sum('amount');

            $transfersIn = \App\Models\FinancialTransfer::where('to_branch_id', $branch->id)
                ->when($dateFrom, fn($q) => $q->whereDate('date', '>=', $dateFrom))
                ->when($dateTo,   fn($q) => $q->whereDate('date', '<=', $dateTo))
                ->sum('amount');

            $transfersOut = \App\Models\FinancialTransfer::where('from_branch_id', $branch->id)
                ->when($dateFrom, fn($q) => $q->whereDate('date', '>=', $dateFrom))
                ->when($dateTo,   fn($q) => $q->whereDate('date', '<=', $dateTo))
                ->sum('amount');

            return [
                'branch'            => $branch,
                'total_income'      => $totalIncome,
                'total_expenses'    => $totalApprovedExpenses,
                'net_profit'        => $totalIncome - $totalApprovedExpenses,
                'pending_expenses'  => $pendingExpenses,
                'transfers_in'      => $transfersIn,
                'transfers_out'     => $transfersOut,
            ];
        });

        return [
            'rows'      => $rows,
            'date_from' => $dateFrom,
            'date_to'   => $dateTo,
            'totals'    => [
                'income'        => $rows->sum('total_income'),
                'expenses'      => $rows->sum('total_expenses'),
                'transfers_in'  => $rows->sum('transfers_in'),
                'transfers_out' => $rows->sum('transfers_out'),
                'net'           => $rows->sum('net_profit'),
            ],
        ];
    }

    // ── تقرير الإيرادات والمصروفات حسب نوع البند ─────────────────────────────
    public function getTypeBreakdown(array $branchIds, ?string $dateFrom, ?string $dateTo): array
    {
        $applyFilters = function ($q) use ($branchIds, $dateFrom, $dateTo) {
            if (!empty($branchIds)) {
                $q->whereIn('branch_id', $branchIds);
            }
            $q->when($dateFrom, fn($q) => $q->whereDate('date', '>=', $dateFrom))
              ->when($dateTo,   fn($q) => $q->whereDate('date', '<=', $dateTo));
            return $q;
        };

        // الإيرادات حسب النوع
        $incomeRows = Income::query()
            ->selectRaw('income_type_id, COUNT(*) as cnt, SUM(amount) as total')
            ->tap($applyFilters)
            ->groupBy('income_type_id')
            ->with('incomeType')
            ->get()
            ->map(fn($r) => [
                'name'  => optional($r->incomeType)->name ?? 'غير محدد',
                'count' => (int) $r->cnt,
                'total' => (float) $r->total,
            ]);

        // المصروفات المعتمدة حسب النوع
        $expenseRows = Expense::query()
            ->selectRaw('expense_type_id, COUNT(*) as cnt, SUM(amount) as total')
            ->where('status', 'approved')
            ->tap($applyFilters)
            ->groupBy('expense_type_id')
            ->with('expenseType')
            ->get()
            ->map(fn($r) => [
                'name'  => optional($r->expenseType)->name ?? 'غير محدد',
                'count' => (int) $r->cnt,
                'total' => (float) $r->total,
            ]);

        $incomeTotal  = $incomeRows->sum('total');
        $expenseTotal = $expenseRows->sum('total');

        return [
            'income_rows'   => $incomeRows->sortByDesc('total')->values(),
            'expense_rows'  => $expenseRows->sortByDesc('total')->values(),
            'income_total'  => $incomeTotal,
            'expense_total' => $expenseTotal,
            'net'           => $incomeTotal - $expenseTotal,
            'date_from'     => $dateFrom,
            'date_to'       => $dateTo,
        ];
    }

    // ── إحصائيات العقود الشاملة ──────────────────────────────────────────────
    public function getContractStats(?int $branchId): array
    {
        $statuses = RecruitmentContract::statuses();
        $palette  = ['#2563eb','#16a34a','#f97316','#9333ea','#ef4444','#ca8a04','#0891b2','#db2777'];

        $base = fn() => RecruitmentContract::when($branchId, fn($q) => $q->where('branch_id', $branchId));

        // Status counts
        $bySt     = $base()->selectRaw('current_status, count(*) as cnt')->groupBy('current_status')->pluck('cnt', 'current_status');
        $total    = (int) $bySt->sum();
        $received = (int) ($bySt[13] ?? 0);
        $returned = (int) ($bySt[14] ?? 0);
        $escaped  = (int) ($bySt[15] ?? 0);
        $active   = (int) $bySt->filter(fn($v, $k) => ! in_array($k, [13, 14, 15]))->sum();

        // Dept & payment counts
        $byDept = $base()->selectRaw('current_department, count(*) as cnt')->groupBy('current_department')->pluck('cnt', 'current_department');
        $byPay  = $base()->selectRaw('payment_status, count(*) as cnt')->groupBy('payment_status')->pluck('cnt', 'payment_status');

        // Delayed count
        $delayed = $this->getDelayedContracts($branchId)->count();

        // Monthly trend — last 6 months
        $monthLabels = [];
        $monthCounts = [];
        for ($i = 5; $i >= 0; $i--) {
            $dt = Carbon::now()->subMonths($i);
            $monthLabels[] = $dt->translatedFormat('M Y');
            $monthCounts[] = $base()->whereYear('created_at', $dt->year)->whereMonth('created_at', $dt->month)->count();
        }

        // Status donut — only statuses with > 0
        $stColors = ['#3b82f6','#60a5fa','#93c5fd','#2563eb','#1d4ed8','#7c3aed','#a855f7','#c084fc','#ef4444','#22c55e','#f97316','#10b981','#16a34a','#f59e0b','#dc2626'];
        $statusLabels = [];
        $statusData   = [];
        $statusColors = [];
        foreach ($statuses as $num => $st) {
            if (($bySt[$num] ?? 0) > 0) {
                $statusLabels[] = $st['label'];
                $statusData[]   = (int) ($bySt[$num] ?? 0);
                $statusColors[] = $stColors[$num - 1] ?? '#94a3b8';
            }
        }

        // Branch monthly chart + branch table (super admin only)
        $branchMonthly = null;
        $branchTable   = [];
        if (! $branchId) {
            $branches       = Branch::where('active', true)->get();
            $bChartDatasets = [];
            foreach ($branches as $i => $branch) {
                $md = [];
                for ($j = 5; $j >= 0; $j--) {
                    $dt  = Carbon::now()->subMonths($j);
                    $md[] = RecruitmentContract::where('branch_id', $branch->id)
                        ->whereYear('created_at', $dt->year)
                        ->whereMonth('created_at', $dt->month)
                        ->count();
                }
                $c = $palette[$i % count($palette)];
                $bChartDatasets[] = [
                    'label'           => $branch->name,
                    'data'            => $md,
                    'backgroundColor' => $c . 'cc',
                    'borderColor'     => $c,
                    'borderWidth'     => 2,
                    'borderRadius'    => 6,
                ];

                $branchTable[] = [
                    'id'       => $branch->id,
                    'name'     => $branch->name,
                    'total'    => RecruitmentContract::where('branch_id', $branch->id)->count(),
                    'active'   => RecruitmentContract::where('branch_id', $branch->id)->whereNotIn('current_status', [13, 14, 15])->count(),
                    'received' => RecruitmentContract::where('branch_id', $branch->id)->where('current_status', 13)->count(),
                    'returned' => RecruitmentContract::where('branch_id', $branch->id)->where('current_status', 14)->count(),
                    'escaped'  => RecruitmentContract::where('branch_id', $branch->id)->where('current_status', 15)->count(),
                    'cs'       => RecruitmentContract::where('branch_id', $branch->id)->where('current_department', 'customer_service')->count(),
                    'acc'      => RecruitmentContract::where('branch_id', $branch->id)->where('current_department', 'accounts')->count(),
                    'coord'    => RecruitmentContract::where('branch_id', $branch->id)->where('current_department', 'coordination')->count(),
                ];
            }
            $branchMonthly = ['labels' => $monthLabels, 'datasets' => $bChartDatasets];
        }

        return [
            'total'         => $total,
            'active'        => $active,
            'received'      => $received,
            'returned'      => $returned,
            'escaped'       => $escaped,
            'delayed'       => $delayed,
            'by_status'     => $bySt->toArray(),
            'by_dept'       => $byDept,
            'by_payment'    => $byPay,
            'month_labels'  => $monthLabels,
            'month_counts'  => $monthCounts,
            'status_labels' => $statusLabels,
            'status_data'   => $statusData,
            'status_colors' => $statusColors,
            'branch_monthly'=> $branchMonthly,
            'branch_table'  => $branchTable,
        ];
    }
    public function getReceivedContracts(?string $dateFrom, ?string $dateTo, ?int $branchId): \Illuminate\Support\Collection
    {
        return RecruitmentContract::with(['client', 'worker.nationality', 'branch', 'statusHistories'])
            ->where('current_status', 13)
            ->whereNotNull('arrival_date')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($dateFrom, fn($q) => $q->whereDate('arrival_date', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('arrival_date', '<=', $dateTo))
            ->orderByDesc('arrival_date')
            ->get();
    }

    // ── تقرير العقود المتأخرة ─────────────────────────────────────────────────
    public function getDelayedContracts(?int $branchId): \Illuminate\Support\Collection
    {
        $statuses = RecruitmentContract::statuses();
        $today    = Carbon::today();

        $contracts = RecruitmentContract::with(['client', 'worker.nationality', 'branch', 'statusHistories'])
            ->whereNotIn('current_status', [9, 13, 14, 15])
            ->where('active', true)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->get();

        return $contracts->filter(function ($contract) use ($statuses, $today) {
            $currentStatus = $contract->current_status;
            $expectedDays  = $statuses[$currentStatus]['days'] ?? null;

            if (! $expectedDays) return false;

            $history = $contract->statusHistories->firstWhere('status', $currentStatus);
            if (! $history || ! $history->status_date) return false;

            $daysInStatus = $history->status_date->diffInDays($today);
            if ($daysInStatus < $expectedDays + 2) return false;

            // Attach computed delay info as dynamic properties
            $contract->delay_days     = $daysInStatus - $expectedDays;
            $contract->days_in_status = $daysInStatus;
            $contract->expected_days  = $expectedDays;

            return true;
        })->sortByDesc('delay_days')->values();
    }
}
