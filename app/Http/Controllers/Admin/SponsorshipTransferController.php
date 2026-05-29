<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Client;
use App\Models\RecruitmentContract;
use App\Models\Worker;
use App\Services\SponsorshipTransferService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SponsorshipTransferController extends Controller
{
    public function __construct(private readonly SponsorshipTransferService $service) {}

    private function branchFilter(): ?int
    {
        $me = Auth::guard('admin')->user();
        return ($me && $me->isBranchAdmin()) ? $me->branch_id : null;
    }

    public function index(Request $request)
    {
        $filters = $request->only('search', 'current_department', 'current_status', 'payment_status');
        if ($bid = $this->branchFilter()) {
            $filters['branch_id'] = $bid;
        } elseif ($request->filled('branch_id')) {
            $filters['branch_id'] = $request->branch_id;
        }

        $transfers = $this->service->list($filters);
        $branches  = Branch::where('active', true)->orderBy('name')->get();

        return view('admin.sponsorship-transfers.index', compact('transfers', 'branches'));
    }

    public function create()
    {
        $branchId = $this->branchFilter();

        $workers = Worker::where('active', true)
            ->whereIn('status', ['in_housing', 'sponsorship_transfer', 'assigned'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->with(['nationality', 'client', 'latestContract'])
            ->orderBy('name')->get();

        // Map worker data for JS auto-fill
        $workersJson = $workers->map(fn($w) => [
            'id'          => $w->id,
            'client_id'   => $w->client_id,
            'client_name' => $w->client?->name ?? '',
            'contract_id' => $w->latestContract?->id,
            'status'      => $w->status,
        ]);

        $clients  = Client::where('active', true)->orderBy('name')->get();
        $branches = $branchId ? null : Branch::where('active', true)->orderBy('name')->get();

        return view('admin.sponsorship-transfers.create', compact('workers', 'workersJson', 'clients', 'branches', 'branchId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'worker_id'           => 'required|exists:workers,id',
            'from_client_id'      => 'required|exists:clients,id',
            'to_client_id'        => 'nullable|exists:clients,id',
            'branch_id'           => 'required|exists:branches,id',
            'original_contract_id'=> 'nullable|exists:recruitment_contracts,id',
            'transfer_date'       => 'nullable|date',
            'total_fees'          => 'required|numeric|min:0',
            'service_fee'         => 'required|numeric|min:0',
            'loss_amount'         => 'required|numeric|min:0',
            'payment_status'      => 'required|in:pending,partial,full',
            'notes'               => 'nullable|string|max:1000',
        ]);

        if ($bid = $this->branchFilter()) {
            $data['branch_id'] = $bid;
        }

        $data['admin_id']           = Auth::guard('admin')->id();
        $data['current_department'] = 'customer_service';
        $data['current_status']     = 1;

        $transfer = $this->service->create($data);

        // Pause the worker's active recruitment contract
        RecruitmentContract::where('worker_id', $data['worker_id'])
            ->where('active', true)
            ->update(['active' => false]);

        // Update worker status to sponsorship_transfer
        Worker::where('id', $data['worker_id'])
            ->update(['status' => 'sponsorship_transfer']);

        return redirect()->route('admin.sponsorship-transfers.show', $transfer->id)
            ->with('success', 'تم إنشاء عقد نقل الكفالة بنجاح وتم إيقاف عقد الاستقدام.');
    }

    public function show(int $id)
    {
        $transfer = $this->service->find($id);
        return view('admin.sponsorship-transfers.show', compact('transfer'));
    }

    public function edit(int $id)
    {
        $transfer = $this->service->find($id);
        $clients  = Client::where('active', true)->orderBy('name')->get();
        $branches = Branch::where('active', true)->orderBy('name')->get();
        return view('admin.sponsorship-transfers.edit', compact('transfer', 'clients', 'branches'));
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'to_client_id'   => 'nullable|exists:clients,id',
            'transfer_date'  => 'nullable|date',
            'total_fees'     => 'required|numeric|min:0',
            'service_fee'    => 'required|numeric|min:0',
            'loss_amount'    => 'required|numeric|min:0',
            'payment_status' => 'required|in:pending,partial,full',
            'notes'          => 'nullable|string|max:1000',
        ]);

        $this->service->update($id, $data);
        return redirect()->route('admin.sponsorship-transfers.show', $id)
            ->with('success', 'تم تحديث العقد.');
    }

    public function destroy(int $id)
    {
        $this->service->delete($id);
        return redirect()->route('admin.sponsorship-transfers.index')
            ->with('success', 'تم حذف العقد.');
    }

    public function updateStatus(Request $request, int $id)
    {
        $request->validate(['action' => 'required|in:forward,complete,cancel']);
        match ($request->action) {
            'forward'  => $this->service->forwardToAccounts($id),
            'complete' => $this->service->complete($id),
            'cancel'   => $this->service->cancel($id),
        };
        return back()->with('success', 'تم تحديث حالة العقد.');
    }

    public function print(int $id)
    {
        $transfer = $this->service->find($id);
        return view('admin.sponsorship-transfers.print', compact('transfer'));
    }
}
