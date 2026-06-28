<?php

namespace App\Http\Controllers\Admin\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadCallLog;
use App\Models\Client;
use App\Models\Admin;
use App\Models\AdminNotification;
use App\Models\Branch;
use App\Models\Nationality;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $me = Auth::guard('admin')->user();

        $query = Lead::with(['campaign', 'branch', 'nationality', 'assignedAdmin', 'latestCallLog'])
            ->latest();





        foreach (['status', 'branch_id', 'nationality_id', 'campaign_id', 'assigned_admin_id'] as $f) {
            if ($v = $request->input($f)) $query->where($f, $v);
        }
        if ($s = $request->input('search')) {
            $query->where(fn($q) => $q->where('name', 'like', "%{$s}%")->orWhere('phone_hash', \App\Models\Lead::hashPii($s)));
        }

        $leads       = $query->where('assigned_admin_id', $me->id)->paginate(30)->withQueryString();
        $statuses    = Lead::statuses();
        $branches    = Branch::where('active', true)->get();
        $nationalities = Nationality::where('active', true)->orderBy('name')->get();
        $campaigns   = Campaign::orderBy('name')->get();
        $admins      = Admin::where('active', true)->where('department', 'customer_service')->orderBy('name')->get();

        return view('admin.marketing.leads.index', compact(
            'leads', 'statuses', 'branches', 'nationalities', 'campaigns', 'admins'
        ));
    }

    public function show(Lead $lead)
    {
        $lead->load(['campaign', 'branch', 'nationality', 'assignedAdmin', 'referredByAdmin', 'callLogs.admin', 'client']);
        $callStatuses  = LeadCallLog::statuses();
        $admins        = Admin::where('active', true)->where('department', 'customer_service')->orderBy('name')->get();
        $branches      = Branch::where('active', true)->get();
        $nationalities = Nationality::where('active', true)->orderBy('name')->get();

        return view('admin.marketing.leads.show', compact('lead', 'callStatuses', 'admins', 'branches', 'nationalities'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'campaign_id'          => 'nullable|exists:campaigns,id',
            'name'                 => 'required|string|max:255',
            'phone'                => 'nullable|string|max:30',
            'city'                 => 'nullable|string|max:100',
            'nationality_id'       => 'nullable|exists:nationalities,id',
            'branch_id'            => 'nullable|exists:branches,id',
            'assigned_admin_id'    => 'nullable|exists:admins,id',
            'referred_by_admin_id' => 'nullable|exists:admins,id',
            'source'               => 'nullable|string|max:100',
            'notes'                => 'nullable|string',
        ]);

        $me = Auth::guard('admin')->user();
        if ($me->isBranchAdmin()) {
            $data['branch_id'] = $me->branch_id;
        }

        $lead = Lead::create($data);

        // Auto-assign to least-busy CS staff in the branch if not manually assigned
        if (! $lead->assigned_admin_id && $lead->branch_id) {
            $csStaff = Admin::where('branch_id', $lead->branch_id)
                ->where('department', 'customer_service')
                ->where('active', true)
                ->get();

            if ($csStaff->isNotEmpty()) {
                $assignee = $csStaff->sortBy(fn($admin) =>
                    Lead::where('assigned_admin_id', $admin->id)
                        ->whereIn('status', ['new', 'in_progress'])
                        ->count()
                )->first();

                $lead->update(['assigned_admin_id' => $assignee->id]);

                AdminNotification::create([
                    'admin_id' => $assignee->id,
                    'type'     => 'lead_assigned',
                    'title'    => 'تم تعيين عميل محتمل جديد لك',
                    'body'     => 'العميل: ' . $lead->name . ($lead->phone ? ' — ' . $lead->phone : ''),
                    'url'      => route('admin.marketing.leads.show', $lead),
                ]);
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'lead' => $lead]);
        }

        return back()->with('success', 'تم إضافة العميل المحتمل');
    }

    public function update(Request $request, Lead $lead)
    {
        $data = $request->validate([
            'name'                 => 'sometimes|string|max:255',
            'phone'                => 'nullable|string|max:30',
            'city'                 => 'nullable|string|max:100',
            'nationality_id'       => 'nullable|exists:nationalities,id',
            'branch_id'            => 'nullable|exists:branches,id',
            'assigned_admin_id'    => 'nullable|exists:admins,id',
            'referred_by_admin_id' => 'nullable|exists:admins,id',
            'status'               => 'sometimes|in:new,in_progress,converted,archived',
            'notes'                => 'nullable|string',
        ]);

        $previousAssigned = $lead->assigned_admin_id;

        $lead->update($data);

        // Notify the new assignee when assignment changes
        if (
            array_key_exists('assigned_admin_id', $data)
            && $data['assigned_admin_id']
            && $data['assigned_admin_id'] != $previousAssigned
        ) {
            AdminNotification::create([
                'admin_id' => $data['assigned_admin_id'],
                'type'     => 'lead_assigned',
                'title'    => 'تم تعيين عميل محتمل جديد لك',
                'body'     => 'العميل: ' . $lead->name . ($lead->phone ? ' — ' . $lead->phone : ''),
                'url'      => route('admin.marketing.leads.show', $lead),
            ]);
        }

        return back()->with('success', 'تم تحديث بيانات العميل');
    }

    /** Log a call attempt */
    public function logCall(Request $request, Lead $lead)
    {
        $data = $request->validate([
            'status'         => 'required|in:no_answer,not_suitable,nationality_unavailable,wants_rent,profiles_rejected,need_followup,converted,wrong_number',
            'notes'          => 'nullable|string',
            'follow_up_at'   => 'nullable|date',
            'nationality_id' => 'nullable|exists:nationalities,id',
        ]);

        $me = Auth::guard('admin')->user();

        // Update lead's requested nationality if provided & changed
        if (array_key_exists('nationality_id', $data) && $data['nationality_id'] != $lead->nationality_id) {
            $lead->update(['nationality_id' => $data['nationality_id']]);
        }
        unset($data['nationality_id']);

        $data['admin_id'] = $me->id;
        $data['lead_id']  = $lead->id;

        LeadCallLog::create($data);

        // Update lead status & last_contacted_at
        $leadStatus = match($data['status']) {
            'converted'  => 'converted',
            'no_answer', 'need_followup', 'nationality_unavailable',
            'wants_rent', 'profiles_rejected', 'wrong_number' => 'in_progress',
            'not_suitable' => 'archived',
            default        => $lead->status,
        };

        $lead->update([
            'status'           => $leadStatus,
            'last_contacted_at'=> now(),
        ]);

        // Notify assigned admin about follow-up time
        if (
            !empty($data['follow_up_at'])
            && $lead->assigned_admin_id
            && $lead->assigned_admin_id != $me->id
        ) {
            AdminNotification::create([
                'admin_id' => $lead->assigned_admin_id,
                'type'     => 'lead_followup',
                'title'    => 'موعد متابعة لعميل محتمل',
                'body'     => 'العميل: ' . $lead->name . ' — الموعد: ' . \Carbon\Carbon::parse($data['follow_up_at'])->format('Y-m-d H:i'),
                'url'      => route('admin.marketing.leads.show', $lead),
            ]);
        }

        return back()->with('success', 'تم تسجيل المكالمة');
    }

    /** Convert lead → actual Client */
    public function convert(Request $request, Lead $lead)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'national_id'   => 'nullable|string|max:20',
            'phone'         => 'nullable|string|max:30',
            'branch_id'     => 'required|exists:branches,id',
            'classification'=> 'nullable|in:vip,confirmed,normal',
        ]);

        $me     = Auth::guard('admin')->user();
        $client = Client::create(array_merge($data, [
            'admin_id'   => $me->id,
            'active'     => true,
        ]));

        $lead->update([
            'status'    => 'converted',
            'client_id' => $client->id,
        ]);

        // Log final call entry
        LeadCallLog::create([
            'lead_id'  => $lead->id,
            'admin_id' => $me->id,
            'status'   => 'converted',
            'notes'    => 'تم التحويل لعميل رقم ' . $client->id,
        ]);

        // Notify branch managers, chairman, super admins, and assigned admin
        $title = 'تحويل عميل محتمل لعميل فعلي';
        $body  = $me->name . ' حوّل العميل المحتمل "' . $lead->name . '" إلى عميل فعلي.';
        $url   = route('admin.clients.show', $client);

        $targets = Admin::query()
            ->where('active', true)
            ->where(function ($q) use ($lead) {
                $q->whereNull('branch_id')                           // super admins
                  ->orWhereIn('department', ['chairman', 'branch_manager'])
                  ->orWhere(function ($q2) use ($lead) {
                      $q2->where('branch_id', $lead->branch_id ?? $lead->client?->branch_id);
                  });
            })
            ->where('id', '!=', $me->id)
            ->pluck('id');

        if ($lead->assigned_admin_id && $lead->assigned_admin_id != $me->id) {
            $targets->push($lead->assigned_admin_id);
        }

        foreach ($targets->unique() as $adminId) {
            AdminNotification::create([
                'admin_id' => $adminId,
                'type'     => 'lead_converted',
                'title'    => $title,
                'body'     => $body,
                'url'      => $url,
            ]);
        }

        return redirect()->route('admin.clients.show', $client)
            ->with('success', 'تم تحويل العميل المحتمل لعميل فعلي بنجاح');
    }
}
