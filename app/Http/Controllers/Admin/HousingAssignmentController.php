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
        $clients     = \App\Models\Client::where('active', true)->orderBy('name')->get();

        return view('admin.housing-assignments.index', compact('assignments', 'housings', 'branches', 'clients'));
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
            'reason'                  => 'nullable|in:sponsorship_transfer,deportation,handover,rental,settlement',
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
        $data = $request->validate([
            'check_out_date' => 'required|date',
            'notes'          => 'nullable|string|max:500',
            'disposition'    => 'nullable|in:rental,settlement',

            // بيانات التأجير
            'rental_client_id'         => 'required_if:disposition,rental|nullable|exists:clients,id',
            'rental_contract_number'   => 'nullable|string|max:100',
            'rent_value'               => 'nullable|numeric|min:0',
            'rent_start_date'          => 'nullable|date',
            'rent_end_date'            => 'nullable|date|after_or_equal:rent_start_date',
            'rental_contract_image'    => 'nullable|image|max:5120',
            'rental_notes'             => 'nullable|string|max:1000',

            // بيانات التسوية
            'settlement_client_id'     => 'required_if:disposition,settlement|nullable|exists:clients,id',
            'settlement_reference'     => 'nullable|string|max:100',
            'settlement_amount'        => 'nullable|numeric|min:0',
            'settlement_type'          => 'nullable|string|max:50',
            'settlement_date'          => 'nullable|date',
            'settlement_document_image'=> 'nullable|image|max:5120',
            'settlement_notes'         => 'nullable|string|max:1000',
        ]);

        if ($request->hasFile('rental_contract_image')) {
            $data['rental_contract_image'] = $request->file('rental_contract_image')
                ->store('housing-rentals', 'public');
        }
        if ($request->hasFile('settlement_document_image')) {
            $data['settlement_document_image'] = $request->file('settlement_document_image')
                ->store('housing-settlements', 'public');
        }

        $this->service->checkout($id, $data);
        return back()->with('success', 'تم تسجيل مغادرة العاملة من السكن.');
    }

    public function destroy(int $id)
    {
        $this->service->delete($id);
        return back()->with('success', 'تم حذف السجل.');
    }
}
