<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Housing;
use App\Models\Worker;
use App\Services\HousingAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HousingAssignmentController extends Controller
{
    public function __construct(private readonly HousingAssignmentService $service) {}

    private function branchFilter(): ?int
    {
        $me = Auth::guard('admin')->user();
        return ($me && $me->isBranchAdmin()) ? $me->branch_id : null;
    }

    public function index(Request $request)
    {
        $filters = $request->only('search', 'housing_id', 'active', 'reason');
        if ($bid = $this->branchFilter()) {
            $filters['branch_id'] = $bid;
        } elseif ($request->filled('branch_id')) {
            $filters['branch_id'] = $request->branch_id;
        }

        $assignments = $this->service->list($filters);
        $housings    = Housing::where('active', true)
            ->when($bid ?? null, fn($q, $b) => $q->where('branch_id', $b))
            ->orderBy('name')->get();
        $branches    = Branch::where('active', true)->orderBy('name')->get();

        return view('admin.housing-assignments.index', compact('assignments', 'housings', 'branches'));
    }

    public function create()
    {
        $me       = Auth::guard('admin')->user();
        $branchId = $this->branchFilter();

        $housings = Housing::where('active', true)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->orderBy('name')->get();

        $housedWorkerIds = \App\Models\HousingAssignment::whereNull('check_out_date')
            ->pluck('worker_id');

        $workers = Worker::where('active', true)
            ->whereIn('status', ['available', 'assigned', 'reserved'])
            ->whereNotIn('id', $housedWorkerIds)
            ->orderBy('name')->get();

        $branches = $branchId ? null : Branch::where('active', true)->orderBy('name')->get();

        return view('admin.housing-assignments.create', compact('housings', 'workers', 'branches', 'branchId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'worker_id'               => 'required|exists:workers,id',
            'housing_id'              => 'required|exists:housings,id',
            'branch_id'               => 'required|exists:branches,id',
            'check_in_date'           => 'required|date',
            'expected_check_out_date' => 'nullable|date|after_or_equal:check_in_date',
            'reason'                  => 'nullable|in:sponsorship_transfer,deportation,handover',
            'notes'                   => 'nullable|string|max:500',
        ]);

        if ($branchId = $this->branchFilter()) {
            $data['branch_id'] = $branchId;
        }

        $data['admin_id'] = Auth::guard('admin')->id();
        $this->service->create($data);

        return redirect()->route('admin.housing-assignments.index')
            ->with('success', 'تم تسكين العاملة بنجاح.');
    }

    public function checkout(int $id, Request $request)
    {
        $request->validate(['check_out_date' => 'required|date']);
        $this->service->checkout($id, $request->check_out_date, $request->notes);
        return back()->with('success', 'تم تسجيل مغادرة العاملة من السكن.');
    }

    public function destroy(int $id)
    {
        $this->service->delete($id);
        return back()->with('success', 'تم حذف السجل.');
    }
}
