<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Client;
use App\Models\Complaint;
use App\Models\Expense;
use App\Models\FinancialTransfer;
use App\Models\Housing;
use App\Models\HousingAssignment;
use App\Models\Income;
use App\Models\RecruitmentContract;
use App\Models\SponsorshipTransfer;
use App\Models\Trip;
use App\Models\Worker;

class DashboardService
{
    public function getStats(?int $branchId = null): array
    {
        $now  = now();
        $thisMonth = $now->month;
        $thisYear  = $now->year;
        $lastMonth = $now->copy()->subMonth()->month;
        $lastMonthYear = $now->copy()->subMonth()->year;

        $totalIncome   = Income::when($branchId, fn($q) => $q->where('branch_id', $branchId))->sum('amount');
        $totalExpenses = Expense::where('status', 'approved')->when($branchId, fn($q) => $q->where('branch_id', $branchId))->sum('amount');
        $pendingExpenses  = Expense::where('status', 'pending')->when($branchId, fn($q) => $q->where('branch_id', $branchId))->count();
        $pendingTransfers = FinancialTransfer::where('status', 'pending')
            ->when($branchId, fn($q) => $q->where(fn($q2) =>
                $q2->where('from_branch_id', $branchId)->orWhere('to_branch_id', $branchId)
            ))->count();
        $branchCount   = $branchId ? 1 : Branch::where('active', true)->count();

        // New stats for redesigned dashboard
        $totalClients     = Client::when($branchId, fn($q) => $q->where('branch_id', $branchId))->count();
        $totalWorkers     = Worker::count();
        $activeContracts  = RecruitmentContract::whereNotIn('current_status', [13, 14, 15])->count();
        $pendingContracts = RecruitmentContract::where('current_status', 1)->count();

        // Month-over-month helpers
        $contractsThisMonth = RecruitmentContract::whereYear('created_at', $thisYear)->whereMonth('created_at', $thisMonth)->count();
        $contractsLastMonth = RecruitmentContract::whereYear('created_at', $lastMonthYear)->whereMonth('created_at', $lastMonth)->count();
        $contractsChange    = $contractsLastMonth > 0 ? round((($contractsThisMonth - $contractsLastMonth) / $contractsLastMonth) * 100) : 0;

        $complaintsThisMonth = Complaint::whereYear('created_at', $thisYear)->whereMonth('created_at', $thisMonth)->count();
        $complaintsLastMonth = Complaint::whereYear('created_at', $lastMonthYear)->whereMonth('created_at', $lastMonth)->count();
        $complaintsChange    = $complaintsLastMonth > 0 ? round((($complaintsThisMonth - $complaintsLastMonth) / $complaintsLastMonth) * 100) : 0;

        $completedContracts = RecruitmentContract::where('current_status', 13)->count();
        $completionRate     = ($activeContracts + $completedContracts) > 0
            ? round(($completedContracts / ($activeContracts + $completedContracts)) * 100)
            : 0;

        // Income this month vs last month
        $incomeThisMonth = Income::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereYear('date', $thisYear)->whereMonth('date', $thisMonth)->sum('amount');
        $incomeLastMonth = Income::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereYear('date', $lastMonthYear)->whereMonth('date', $lastMonth)->sum('amount');
        $incomeChange    = $incomeLastMonth > 0 ? round((($incomeThisMonth - $incomeLastMonth) / $incomeLastMonth) * 100) : 0;

        // Average completion days: avg days from created_at to last status update for completed contracts
        $driver = config('database.default');
        $diffExpr = $driver === 'sqlite'
            ? 'AVG(CAST((julianday(updated_at) - julianday(created_at)) AS INTEGER)) as avg_days'
            : 'AVG(DATEDIFF(updated_at, created_at)) as avg_days';
        $avgCompletionDays = RecruitmentContract::where('current_status', 13)
            ->whereNotNull('updated_at')
            ->selectRaw($diffExpr)
            ->value('avg_days');
        $avgCompletionDays = $avgCompletionDays ? round($avgCompletionDays, 1) : '—';

        $expensesThisMonth = Expense::where('status', 'approved')->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereYear('date', $thisYear)->whereMonth('date', $thisMonth)->sum('amount');
        $expensesLastMonth = Expense::where('status', 'approved')->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereYear('date', $lastMonthYear)->whereMonth('date', $lastMonth)->sum('amount');
        $expensesChange    = $expensesLastMonth > 0 ? round((($expensesThisMonth - $expensesLastMonth) / $expensesLastMonth) * 100) : 0;

        // Tomorrow's trips count
        $tomorrowTripsCount = Trip::whereDate('trip_date', now()->addDay())->where('status', 'scheduled')->count();

        // Housing quick stats
        $housingCapacity = Housing::where('active', true)->sum('capacity');
        $housingOccupied = HousingAssignment::whereNull('check_out_date')->count();

        // Musaned open + urgent complaints
        $musanedOpen      = Complaint::where('on_musaned', true)->whereNotIn('status', ['resolved', 'closed'])->count();
        $urgentComplaints = Complaint::whereIn('priority', ['high', 'urgent'])->whereNotIn('status', ['resolved', 'closed'])->count();

        return [
            'total_income'           => $totalIncome,
            'total_expenses'         => $totalExpenses,
            'net_profit'             => $totalIncome - $totalExpenses,
            'branch_count'           => $branchCount,
            'pending_expenses'       => $pendingExpenses,
            'pending_transfers'      => $pendingTransfers,
            'total_clients'          => $totalClients,
            'total_workers'          => $totalWorkers,
            'active_contracts'       => $activeContracts,
            'avg_completion_days'    => $avgCompletionDays,
            'pending_contracts'      => $pendingContracts,
            'contracts_this_month'   => $contractsThisMonth,
            'contracts_change'       => $contractsChange,
            'complaints_this_month'  => $complaintsThisMonth,
            'complaints_change'      => $complaintsChange,
            'completion_rate'        => $completionRate,
            'income_change'          => $incomeChange,
            'expenses_change'        => $expensesChange,
            'tomorrow_trips'         => $tomorrowTripsCount,
            'housing_capacity'       => $housingCapacity,
            'housing_occupied'       => $housingOccupied,
            'housing_available'      => max(0, $housingCapacity - $housingOccupied),
            'musaned_open'           => $musanedOpen,
            'urgent_complaints'      => $urgentComplaints,
        ];
    }

    public function getChartData(?int $branchId = null): array
    {
        $months = [];
        $incomes = [];
        $expenses = [];

        for ($m = 1; $m <= 12; $m++) {
            $months[] = \Carbon\Carbon::create(null, $m)->translatedFormat('M');
            $incomes[]  = Income::when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->whereYear('date', now()->year)->whereMonth('date', $m)->sum('amount');
            $expenses[] = Expense::where('status', 'approved')
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->whereYear('date', now()->year)->whereMonth('date', $m)->sum('amount');
        }

        return compact('months', 'incomes', 'expenses');
    }

    public function getBranchComparisonData(): array
    {
        $branches = Branch::where('active', true)->get();
        $year     = now()->year;

        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[] = \Carbon\Carbon::create($year, $m)->translatedFormat('M');
        }

        $palette = ['#2563eb', '#16a34a', '#f97316', '#9333ea', '#ef4444', '#ca8a04', '#0891b2', '#db2777'];

        $datasets = [];
        foreach ($branches as $i => $branch) {
            $monthlyData = [];
            for ($m = 1; $m <= 12; $m++) {
                $monthlyData[] = (float) Income::where('branch_id', $branch->id)
                    ->whereYear('date', $year)
                    ->whereMonth('date', $m)
                    ->sum('amount');
            }
            $color = $palette[$i % count($palette)];
            $datasets[] = [
                'label'           => $branch->name,
                'data'            => $monthlyData,
                'borderColor'     => $color,
                'backgroundColor' => $color . '22',
                'borderWidth'     => 2.5,
                'tension'         => 0.4,
                'pointRadius'     => 4,
                'pointHoverRadius'=> 6,
                'fill'            => false,
            ];
        }

        return ['labels' => $months, 'datasets' => $datasets];
    }

    /** Monthly contracts created this year */
    public function getContractsMonthlyData(): array
    {
        $year   = now()->year;
        $months = [];
        $counts = [];

        for ($m = 1; $m <= 12; $m++) {
            $months[] = \Carbon\Carbon::create($year, $m)->translatedFormat('M');
            $counts[] = RecruitmentContract::whereYear('created_at', $year)
                ->whereMonth('created_at', $m)
                ->count();
        }

        return ['labels' => $months, 'data' => $counts];
    }

    /** Contracts grouped by status category for donut chart */
    public function getContractsByStatusData(): array
    {
        $statuses = RecruitmentContract::selectRaw('current_status, COUNT(*) as total')
            ->groupBy('current_status')
            ->pluck('total', 'current_status')
            ->toArray();

        $groups = [
            'جديد'           => $statuses[1]  ?? 0,
            'قيد المعالجة'  => collect(range(2, 12))->sum(fn($s) => $statuses[$s] ?? 0),
            'تم الاستلام'   => $statuses[13] ?? 0,
            'رجيع الضمان'   => $statuses[14] ?? 0,
            'هروب'           => $statuses[15] ?? 0,
        ];

        return [
            'labels' => array_keys($groups),
            'data'   => array_values($groups),
            'colors' => ['#f97316', '#2563eb', '#16a34a', '#ef4444', '#9333ea'],
        ];
    }

    /** Top campaigns with leads and conversion counts */
    public function getCampaignsChartData(): array
    {
        $campaigns = \App\Models\Campaign::withCount([
            'leads',
            'leads as converted_count' => fn($q) => $q->where('status', 'converted'),
        ])->orderByDesc('leads_count')->limit(8)->get();

        return [
            'labels'    => $campaigns->pluck('name')->toArray(),
            'leads'     => $campaigns->pluck('leads_count')->toArray(),
            'converted' => $campaigns->pluck('converted_count')->toArray(),
        ];
    }

    /** Monthly sponsorship transfers chart */
    public function getSponsorshipTransfersChart(): array
    {
        $year = now()->year;
        $months = $new = $completed = [];

        for ($m = 1; $m <= 12; $m++) {
            $months[]    = \Carbon\Carbon::create($year, $m)->translatedFormat('M');
            $new[]       = SponsorshipTransfer::whereYear('created_at', $year)->whereMonth('created_at', $m)->count();
            $completed[] = SponsorshipTransfer::whereYear('created_at', $year)->whereMonth('created_at', $m)
                ->where('current_status', 3)->count();
        }

        return compact('months', 'new', 'completed');
    }

    /** Trips scheduled for tomorrow */
    public function getTomorrowTrips()
    {
        return Trip::with(['airport', 'branch'])
            ->withCount('workers')
            ->whereDate('trip_date', now()->addDay())
            ->where('status', 'scheduled')
            ->orderBy('trip_time')
            ->get();
    }

    /** Housing capacity and occupancy */
    public function getHousingStats(): array
    {
        $capacity = (int) Housing::where('active', true)->sum('capacity');
        $occupied = HousingAssignment::whereNull('check_out_date')->count();

        return [
            'houses'    => Housing::where('active', true)->count(),
            'capacity'  => $capacity,
            'occupied'  => $occupied,
            'available' => max(0, $capacity - $occupied),
            'rate'      => $capacity > 0 ? round(($occupied / $capacity) * 100) : 0,
        ];
    }

    /** Monthly complaints — open vs resolved */
    public function getComplaintsMonthlyData(): array
    {
        $year = now()->year;
        $months = $open = $resolved = [];

        for ($m = 1; $m <= 12; $m++) {
            $months[]   = \Carbon\Carbon::create($year, $m)->translatedFormat('M');
            $open[]     = Complaint::whereYear('created_at', $year)->whereMonth('created_at', $m)
                ->whereNotIn('status', ['resolved', 'closed'])->count();
            $resolved[] = Complaint::whereYear('created_at', $year)->whereMonth('created_at', $m)
                ->whereIn('status', ['resolved', 'closed'])->count();
        }

        return compact('months', 'open', 'resolved');
    }
}
