<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreComplaintRequest;
use App\Http\Requests\Admin\UpdateComplaintRequest;
use App\Models\Admin;
use App\Models\Branch;
use App\Models\Complaint;
use App\Models\RecruitmentContract;
use App\Services\ComplaintService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    public function __construct(private readonly ComplaintService $service) {}

    // ── Index ─────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $filters = $request->only(
            'search', 'status', 'priority', 'problem_type',
            'branch_id', 'assigned_admin_id', 'on_musaned', 'date_from', 'date_to'
        );

        $me = Auth::guard('admin')->user();
        if ($me && $me->isBranchAdmin()) {
            $filters['branch_id'] = $me->branch_id;
        }

        $complaints = $this->service->list($filters);
        $branches   = Branch::where('active', true)->orderBy('name')->get();
        $admins     = Admin::orderBy('name')->get(['id', 'name', 'branch_id']);
        $trashed    = $this->service->trashed();

        return view('admin.complaints.index', compact(
            'complaints', 'branches', 'admins', 'filters', 'trashed'
        ));
    }

    // ── Create ────────────────────────────────────────────────────────────────
    public function create()
    {
        return view('admin.complaints.create', $this->formData());
    }

    public function store(StoreComplaintRequest $request)
    {
        $data = $request->validated();
        $me   = Auth::guard('admin')->user();
        $data['created_by_admin_id'] = $me?->id;

        if ($me && $me->isBranchAdmin()) {
            $data['branch_id'] = $me->branch_id;
        }

        $files = $request->file('attachments', []);
        $complaint = $this->service->store($data, is_array($files) ? $files : []);

        return redirect()->route('admin.complaints.show', $complaint->id)
            ->with('success', 'تم تسجيل الشكوى رقم ' . $complaint->complaint_number);
    }

    // ── Show ──────────────────────────────────────────────────────────────────
    public function show(int $id)
    {
        $complaint = $this->service->find($id);
        $this->authorizeBranch($complaint);
        return view('admin.complaints.show', compact('complaint'));
    }

    // ── Edit / Update ─────────────────────────────────────────────────────────
    public function edit(int $id)
    {
        $complaint = $this->service->find($id);
        $this->authorizeBranch($complaint);
        return view('admin.complaints.edit', array_merge($this->formData(), compact('complaint')));
    }

    public function update(UpdateComplaintRequest $request, int $id)
    {
        $complaint = $this->service->find($id);
        $this->authorizeBranch($complaint);

        $data  = $request->validated();
        $files = $request->file('attachments', []);
        $this->service->update($id, $data, is_array($files) ? $files : []);

        return redirect()->route('admin.complaints.show', $id)
            ->with('success', 'تم تحديث بيانات الشكوى.');
    }

    // ── Destroy / Restore ─────────────────────────────────────────────────────
    public function destroy(int $id)
    {
        $this->service->destroy($id);
        return back()->with('success', 'تم حذف الشكوى.');
    }

    public function restore(int $id)
    {
        $this->service->restore($id);
        return back()->with('success', 'تم استعادة الشكوى.');
    }

    public function deleteAttachment(int $attachmentId)
    {
        $this->service->deleteAttachment($attachmentId);
        return back()->with('success', 'تم حذف المرفق.');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────
    protected function formData(): array
    {
        $me = Auth::guard('admin')->user();

        $branches = $me && $me->isBranchAdmin()
            ? Branch::where('id', $me->branch_id)->get()
            : Branch::where('active', true)->orderBy('name')->get();

        $contracts = RecruitmentContract::with('client:id,name')
            ->when($me && $me->isBranchAdmin(), fn($q) => $q->where('branch_id', $me->branch_id))
            ->orderByDesc('id')
            ->limit(500)
            ->get(['id', 'contract_number', 'client_id']);

        $admins = $me && $me->isBranchAdmin()
            ? Admin::where('branch_id', $me->branch_id)->orderBy('name')->get(['id', 'name'])
            : Admin::orderBy('name')->get(['id', 'name']);

        return [
            'branches'     => $branches,
            'contracts'    => $contracts,
            'admins'       => $admins,
            'problemTypes' => Complaint::problemTypes(),
            'priorities'   => Complaint::priorities(),
            'statuses'     => Complaint::statuses(),
        ];
    }

    protected function authorizeBranch(Complaint $complaint): void
    {
        $me = Auth::guard('admin')->user();
        if ($me && $me->isBranchAdmin() && $complaint->branch_id !== $me->branch_id) {
            abort(403, 'ليس لديك صلاحية للوصول إلى هذه الشكوى.');
        }
    }
}
