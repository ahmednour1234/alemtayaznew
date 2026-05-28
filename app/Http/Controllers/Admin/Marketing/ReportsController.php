<?php

namespace App\Http\Controllers\Admin\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Lead;
use App\Models\RecruitmentContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $me = Auth::guard('admin')->user();

        $query = Campaign::withCount([
            'leads',
            'leads as converted_count'   => fn($q) => $q->where('status', 'converted'),
            'leads as in_progress_count' => fn($q) => $q->where('status', 'in_progress'),
            'leads as archived_count'    => fn($q) => $q->where('status', 'archived'),
        ])->with('branch')->latest();

        if ($me->isBranchAdmin()) {
            $query->where('branch_id', $me->branch_id);
        }

        $campaigns = $query->get()->map(function ($c) {
            $clientIds = $c->leads()->whereNotNull('client_id')->pluck('client_id');
            $contractsQuery = RecruitmentContract::whereIn('client_id', $clientIds);

            $contractsCount = (clone $contractsQuery)->count();
            $revenue        = (float) (clone $contractsQuery)->sum('total_cost');

            $c->contracts_count    = $contractsCount;
            $c->revenue            = $revenue;
            $c->profit             = $revenue - (float) ($c->budget ?? 0);
            $c->roi                = $c->budget > 0
                ? round((($revenue - $c->budget) / $c->budget) * 100, 1) : 0;
            $c->cost_per_lead      = ($c->budget && $c->leads_count)
                ? round($c->budget / $c->leads_count, 2) : 0;
            $c->cost_per_contract  = ($c->budget && $contractsCount)
                ? round($c->budget / $contractsCount, 2) : 0;
            $c->conversion_rate    = $c->leads_count
                ? round(($c->converted_count / $c->leads_count) * 100, 1) : 0;
            return $c;
        });

        $totals = [
            'budget'    => $campaigns->sum('budget'),
            'leads'     => $campaigns->sum('leads_count'),
            'converted' => $campaigns->sum('converted_count'),
            'contracts' => $campaigns->sum('contracts_count'),
            'revenue'   => $campaigns->sum('revenue'),
            'profit'    => $campaigns->sum('profit'),
        ];

        // Monthly trend (last 6 months)
        $trend = collect();
        for ($i = 5; $i >= 0; $i--) {
            $m = Carbon::now()->subMonths($i);
            $start = $m->copy()->startOfMonth();
            $end   = $m->copy()->endOfMonth();

            $leadsQ     = Lead::whereBetween('created_at', [$start, $end]);
            $contractsQ = RecruitmentContract::whereBetween('created_at', [$start, $end]);

            if ($me->isBranchAdmin()) {
                $leadsQ->where('branch_id', $me->branch_id);
                $contractsQ->where('branch_id', $me->branch_id);
            }

            $trend->push([
                'label'     => $m->translatedFormat('M Y'),
                'leads'     => (int) $leadsQ->count(),
                'contracts' => (int) $contractsQ->count(),
                'revenue'   => (float) $contractsQ->sum('total_cost'),
            ]);
        }

        return view('admin.marketing.reports', compact('campaigns', 'totals', 'trend'));
    }
}
