<?php

namespace App\Http\Controllers\Admin\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Branch;
use App\Models\Admin;
use App\Models\Nationality;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $me = Auth::guard('admin')->user();

        $query = Campaign::withCount([
            'leads',
            'leads as converted_count' => fn($q) => $q->where('status', 'converted'),
        ])->with('branch', 'admin')->latest();

        if ($me->isBranchAdmin()) {
            $query->where('branch_id', $me->branch_id);
        }

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $campaigns = $query->paginate(20)->withQueryString();

        return view('admin.marketing.campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        $branches = Branch::where('active', true)->get();
        return view('admin.marketing.campaigns.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'sheet_url'   => 'nullable|url',
            'budget'      => 'nullable|numeric|min:0',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'branch_id'   => 'nullable|exists:branches,id',
        ]);

        $me = Auth::guard('admin')->user();
        if ($me->isBranchAdmin()) {
            $data['branch_id'] = $me->branch_id;
        }

        $data['admin_id'] = $me->id;

        $campaign = Campaign::create($data);

        return redirect()->route('admin.marketing.campaigns.show', $campaign)
            ->with('success', 'تم إنشاء الحملة بنجاح');
    }

    public function show(Campaign $campaign)
    {
        $campaign->load(['branch', 'admin', 'leads.nationality', 'leads.assignedAdmin', 'leads.latestCallLog']);

        $stats = [
            'total'       => $campaign->leads()->count(),
            'new'         => $campaign->leads()->where('status', 'new')->count(),
            'in_progress' => $campaign->leads()->where('status', 'in_progress')->count(),
            'converted'   => $campaign->leads()->where('status', 'converted')->count(),
            'archived'    => $campaign->leads()->where('status', 'archived')->count(),
        ];

        $admins      = Admin::where('active', true)->orderBy('name')->get();
        $nationalities = Nationality::where('active', true)->orderBy('name')->get();

        return view('admin.marketing.campaigns.show', compact('campaign', 'stats', 'admins', 'nationalities'));
    }

    public function edit(Campaign $campaign)
    {
        $branches = Branch::where('active', true)->get();
        return view('admin.marketing.campaigns.edit', compact('campaign', 'branches'));
    }

    public function update(Request $request, Campaign $campaign)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'sheet_url'   => 'nullable|url',
            'budget'      => 'nullable|numeric|min:0',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'branch_id'   => 'nullable|exists:branches,id',
            'active'      => 'boolean',
        ]);

        $campaign->update($data);

        return back()->with('success', 'تم تحديث الحملة');
    }

    /**
     * Import leads from Google Sheets public URL.
     * Sheet columns: A=الاسم, B=الجوال, C=المدينة, D=الجنسية المطلوبة
     */
    public function importSheet(Request $request, Campaign $campaign)
    {
        $request->validate(['sheet_url' => 'required|url']);

        $url = $request->input('sheet_url');
        $campaign->update(['sheet_url' => $url]);

        $csvUrl = $this->sheetToCsvUrl($url);
        if (! $csvUrl) {
            return back()->withErrors(['sheet_url' => 'رابط الشيت غير صحيح، تأكد أنه رابط Google Sheets عام']);
        }

        try {
            $response = Http::timeout(30)->get($csvUrl);
            if (! $response->ok()) {
                return back()->withErrors(['sheet_url' => 'تعذّر جلب البيانات من الشيت، تأكد أنه مشارك للعموم']);
            }

            $rows    = array_filter(explode("\n", $response->body()));
            $imported = 0;
            $skipped  = 0;
            $me      = Auth::guard('admin')->user();

            // Build a set of phones already in this campaign for fast O(1) lookup
            $existingPhones = Lead::where('campaign_id', $campaign->id)
                ->whereNotNull('phone')
                ->pluck('phone')
                ->map(fn($p) => preg_replace('/\D/', '', $p)) // digits only
                ->flip()
                ->toArray();

            foreach (array_slice($rows, 1) as $line) { // skip header row
                $cols = str_getcsv(trim($line));
                $name  = trim($cols[0] ?? '');
                if (! $name) continue;

                $phone    = trim($cols[1] ?? '') ?: null;
                $city     = trim($cols[2] ?? '') ?: null;
                $natName  = trim($cols[3] ?? '') ?: null;

                // ── Duplicate guard ─────────────────────────────────────────
                if ($phone) {
                    $digitsOnly = preg_replace('/\D/', '', $phone);
                    if (isset($existingPhones[$digitsOnly])) {
                        $skipped++;
                        continue;
                    }
                    // Mark as seen so duplicates within the same sheet are also skipped
                    $existingPhones[$digitsOnly] = true;
                }
                // ────────────────────────────────────────────────────────────

                $natId = null;
                if ($natName) {
                    $natId = \App\Models\Nationality::where('name', 'like', "%{$natName}%")
                        ->value('id');
                }

                $branchId = $campaign->branch_id ?? ($me->isBranchAdmin() ? $me->branch_id : null);

                $lead = Lead::create([
                    'campaign_id'  => $campaign->id,
                    'name'         => $name,
                    'phone'        => $phone,
                    'city'         => $city,
                    'nationality_id' => $natId,
                    'branch_id'    => $branchId,
                    'source'       => 'sheet',
                    'status'       => 'new',
                ]);

                // Auto-assign to least-busy CS staff in the branch
                if ($branchId && ! $lead->assigned_admin_id) {
                    $this->autoAssignLead($lead, $branchId);
                }

                $imported++;
            }

            // Notify branch admins
            if ($campaign->branch_id && $imported > 0) {
                $this->notifyBranchAdmins($campaign, $imported);
            }

            $msg = "تم استيراد {$imported} عميل محتمل بنجاح";
            if ($skipped > 0) {
                $msg .= " (تم تجاهل {$skipped} مكرر)";
            }

            return back()->with('success', $msg);
        } catch (\Throwable $e) {
            return back()->withErrors(['sheet_url' => 'حدث خطأ أثناء الاستيراد: ' . $e->getMessage()]);
        }
    }

    private function sheetToCsvUrl(string $url): ?string
    {
        // https://docs.google.com/spreadsheets/d/SHEET_ID/edit#gid=0
        // → https://docs.google.com/spreadsheets/d/SHEET_ID/export?format=csv&gid=0
        if (preg_match('~/spreadsheets/d/([^/]+)~', $url, $m)) {
            $gid = '';
            if (preg_match('~[#&]gid=(\d+)~', $url, $g)) {
                $gid = '&gid=' . $g[1];
            }
            return "https://docs.google.com/spreadsheets/d/{$m[1]}/export?format=csv{$gid}";
        }
        return null;
    }

    private function notifyBranchAdmins(Campaign $campaign, int $count): void
    {
        $admins = Admin::where('branch_id', $campaign->branch_id)
            ->where('active', true)
            ->get();

        foreach ($admins as $admin) {
            \App\Models\AdminNotification::create([
                'admin_id' => $admin->id,
                'title'    => 'عملاء محتملون جدد',
                'body'     => "تم إضافة {$count} عميل محتمل للحملة «{$campaign->name}»",
                'type'     => 'lead',
                'url'      => route('admin.marketing.campaigns.show', $campaign->id),
            ]);
        }
    }

    /**
     * Auto-assign a single lead to the least-busy CS staff in the branch.
     */
    private function autoAssignLead(Lead $lead, int $branchId): void
    {
        static $staffCache = [];

        if (! isset($staffCache[$branchId])) {
            $staffCache[$branchId] = Admin::where('branch_id', $branchId)
                ->where('department', 'customer_service')
                ->where('active', true)
                ->get();
        }

        $csStaff = $staffCache[$branchId];
        if ($csStaff->isEmpty()) return;

        $assignee = $csStaff->sortBy(fn($admin) =>
            Lead::where('assigned_admin_id', $admin->id)
                ->whereIn('status', ['new', 'in_progress'])
                ->count()
        )->first();

        $lead->update(['assigned_admin_id' => $assignee->id]);

        \App\Models\AdminNotification::create([
            'admin_id' => $assignee->id,
            'type'     => 'lead_assigned',
            'title'    => 'تم تعيين عميل محتمل جديد لك',
            'body'     => 'العميل: ' . $lead->name . ($lead->phone ? ' — ' . $lead->phone : ''),
            'url'      => route('admin.marketing.leads.show', $lead),
        ]);
    }

    /**
     * Re-assign all unassigned leads in this campaign to CS staff.
     * POST campaigns/{campaign}/reassign-unassigned
     */
    public function reassignUnassigned(Campaign $campaign)
    {
        $unassigned = Lead::where('campaign_id', $campaign->id)
            ->whereIn('status', ['new', 'in_progress'])
            ->whereNull('assigned_admin_id')
            ->get();

        if ($unassigned->isEmpty()) {
            return back()->with('success', 'جميع العملاء موزّعون بالفعل');
        }

        // Pre-load all active branches and CS staff
        $allBranches     = Branch::where('active', true)->get();
        $csStaffByBranch = [];   // branch_id → Collection<Admin>
        $loadCache       = [];   // admin_id  → active lead count

        // Fallback pool: all active CS staff across all branches (for unmatched cities)
        $allCsStaff = Admin::where('department', 'customer_service')
            ->where('active', true)
            ->get();

        $count = 0;

        foreach ($unassigned as $lead) {
            // ── 1. Resolve branch ────────────────────────────────────────────
            $branchId = $lead->branch_id ?? $campaign->branch_id;

            if (! $branchId && $lead->city) {
                $leadCity = preg_replace('/^ال/', '', trim($lead->city));
                $matched  = $allBranches->first(function ($b) use ($leadCity) {
                    $bCity = preg_replace('/^ال/', '', trim($b->city ?? ''));
                    $bName = preg_replace('/^ال/', '', trim($b->name));
                    return ($bCity !== '' && (mb_stripos($bCity, $leadCity) !== false || mb_stripos($leadCity, $bCity) !== false))
                        || mb_stripos($bName, $leadCity) !== false
                        || mb_stripos($leadCity, $bName) !== false;
                });
                if ($matched) {
                    $branchId = $matched->id;
                    $lead->update(['branch_id' => $branchId]);
                }
            }

            // ── 2. Pick CS staff pool ─────────────────────────────────────────
            if ($branchId) {
                if (! isset($csStaffByBranch[$branchId])) {
                    $csStaffByBranch[$branchId] = Admin::where('branch_id', $branchId)
                        ->where('department', 'customer_service')
                        ->where('active', true)
                        ->get();
                }
                $pool = $csStaffByBranch[$branchId];

                // If branch has no CS staff, fall back to global pool
                if ($pool->isEmpty()) {
                    $pool = $allCsStaff;
                }
            } else {
                // No branch matched at all → use global pool (random distribution)
                $pool = $allCsStaff;
            }

            if ($pool->isEmpty()) {
                continue; // no staff anywhere — truly nothing to do
            }

            // ── 3. Assign to least-busy staff in the pool ────────────────────
            $assignee = $pool->sortBy(function ($admin) use (&$loadCache) {
                if (! isset($loadCache[$admin->id])) {
                    $loadCache[$admin->id] = Lead::where('assigned_admin_id', $admin->id)
                        ->whereIn('status', ['new', 'in_progress'])
                        ->count();
                }
                return $loadCache[$admin->id];
            })->first();

            $lead->update(['assigned_admin_id' => $assignee->id]);
            $loadCache[$assignee->id] = ($loadCache[$assignee->id] ?? 0) + 1;

            \App\Models\AdminNotification::create([
                'admin_id' => $assignee->id,
                'type'     => 'lead_assigned',
                'title'    => 'تم تعيين عميل محتمل لك',
                'body'     => 'العميل: ' . $lead->name . ($lead->phone ? ' — ' . $lead->phone : ''),
                'url'      => route('admin.marketing.leads.show', $lead),
            ]);

            $count++;
        }

        return back()->with('success', "تم توزيع {$count} عميل محتمل على موظفي خدمة العملاء");
    }
}
