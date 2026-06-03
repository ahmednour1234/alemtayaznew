<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\HousingRental;
use App\Models\HousingSettlement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HousingReportsController extends Controller
{
    private function resolveBranch(Request $request): array
    {
        $me       = Auth::guard('admin')->user();
        $branchId = $me->isBranchAdmin() ? $me->branch_id : ($request->branch_id ?: null);
        $branches = $me->isBranchAdmin()
            ? collect()
            : Branch::where('active', true)->orderBy('name')->get(['id', 'name']);

        return [$branchId ? (int) $branchId : null, $branches];
    }

    // ── تقرير العاملات المؤجَّرة ──────────────────────────────────────────────
    public function rentals(Request $request)
    {
        [$branchId, $branches] = $this->resolveBranch($request);

        $rentals = HousingRental::with(['worker.nationality', 'client', 'branch'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($request->client_id, fn($q) => $q->where('client_id', $request->client_id))
            ->when($request->date_from, fn($q) => $q->whereDate('rent_start_date', '>=', $request->date_from))
            ->when($request->date_to,   fn($q) => $q->whereDate('rent_start_date', '<=', $request->date_to))
            ->latest()
            ->get();

        $totalRent = $rentals->sum('rent_value');

        return view('admin.reports.housing-rentals', compact('rentals', 'branches', 'branchId', 'totalRent'));
    }

    // ── تقرير التسويات ─────────────────────────────────────────────────────────
    public function settlements(Request $request)
    {
        [$branchId, $branches] = $this->resolveBranch($request);

        $settlements = HousingSettlement::with(['worker.nationality', 'client', 'branch'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($request->client_id, fn($q) => $q->where('client_id', $request->client_id))
            ->when($request->date_from, fn($q) => $q->whereDate('settlement_date', '>=', $request->date_from))
            ->when($request->date_to,   fn($q) => $q->whereDate('settlement_date', '<=', $request->date_to))
            ->latest()
            ->get();

        $totalSettlement = $settlements->sum('settlement_amount');

        return view('admin.reports.housing-settlements', compact('settlements', 'branches', 'branchId', 'totalSettlement'));
    }
}
