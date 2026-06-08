<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Client;
use App\Models\RecruitmentContract;
use App\Models\Worker;
use App\Models\SponsorshipTransfer;
use App\Services\NotificationService;
use App\Services\SponsorshipTransferService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SponsorshipTransferController extends Controller
{
    public function __construct(
        private readonly SponsorshipTransferService $service,
        private readonly NotificationService $notifications,
    ) {}

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

        $baseQuery = Worker::where('active', true)
            ->with(['nationality', 'client', 'latestContract.client', 'activeHousingAssignment'])
            ->orderBy('name');

        $workers = (clone $baseQuery)
            ->whereNull('client_id')
            ->whereNull('assigned_at')
            ->whereNull('assigned_by_admin_id')
            ->get();

        // Map worker data for JS auto-fill
        $workersJson = $workers->map(function ($w) {
            // العاملة القادمة من عقد استقدام يكون كفيلها الحالي على العقد وليس على عمود client_id
            $client = $w->client ?? $w->latestContract?->client;

            return [
                'id'          => $w->id,
                'client_id'   => $client?->id,
                'client_name' => $client?->name ?? '',
                'contract_id' => $w->latestContract?->id,
                'status'      => $w->status,
            ];
        });

        $clients  = Client::where('active', true)->orderBy('name')->get();
        $branches = $branchId ? null : Branch::where('active', true)->orderBy('name')->get();

        return view('admin.sponsorship-transfers.create', compact(
            'workers', 'workersJson', 'clients', 'branches', 'branchId'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'worker_id'           => 'required|exists:workers,id',
            'from_client_id'      => 'required|exists:clients,id',
            'to_client_id'        => 'nullable|exists:clients,id',
            'branch_id'           => 'required|exists:branches,id',
            'original_contract_id'=> 'nullable|exists:recruitment_contracts,id',
            'transfer_date'            => 'nullable|date',
            'musaned_contract_number'  => 'nullable|string|max:100',
            'musaned_contract_image'   => 'nullable|image|max:5120',
            'total_fees'               => 'required|numeric|min:0',
            'service_fee'              => 'required|numeric|min:0',
            'loss_amount'              => 'required|numeric|min:0',
            'payment_status'           => 'required|in:pending,partial,full',
            'needs_medical_exam'       => 'nullable|boolean',
            'needs_iqama'              => 'nullable|boolean',
            'notes'                    => 'nullable|string|max:1000',
        ]);

        $data['needs_medical_exam'] = $request->boolean('needs_medical_exam');
        $data['needs_iqama']        = $request->boolean('needs_iqama');

        if ($request->hasFile('musaned_contract_image')) {
            $data['musaned_contract_image'] = $request->file('musaned_contract_image')
                ->store('sponsorship-contracts', 'public');
        }

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

        $url = route('admin.sponsorship-transfers.show', $transfer->id);
        $this->notifications->notify(
            'sponsorship_transfer_created',
            'عقد نقل كفالة جديد',
            'تم إنشاء عقد نقل الكفالة رقم ' . $transfer->contract_number,
            $url,
            [$transfer->branch_id]
        );

        return redirect($url)->with('success', 'تم إنشاء عقد نقل الكفالة بنجاح وتم إيقاف عقد الاستقدام.');
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
            'to_client_id'            => 'nullable|exists:clients,id',
            'transfer_date'           => 'nullable|date',
            'musaned_contract_number' => 'nullable|string|max:100',
            'musaned_contract_image'  => 'nullable|image|max:5120',
            'total_fees'              => 'required|numeric|min:0',
            'service_fee'             => 'required|numeric|min:0',
            'loss_amount'             => 'required|numeric|min:0',
            'payment_status'          => 'required|in:pending,partial,full',
            'needs_medical_exam'      => 'nullable|boolean',
            'needs_iqama'             => 'nullable|boolean',
            'notes'                   => 'nullable|string|max:1000',
        ]);

        $data['needs_medical_exam'] = $request->boolean('needs_medical_exam');
        $data['needs_iqama']        = $request->boolean('needs_iqama');

        if ($request->hasFile('musaned_contract_image')) {
            // Delete old image if exists
            $transfer = $this->service->find($id);
            if ($transfer->musaned_contract_image) {
                Storage::disk('public')->delete($transfer->musaned_contract_image);
            }
            $data['musaned_contract_image'] = $request->file('musaned_contract_image')
                ->store('sponsorship-contracts', 'public');
        }

        $this->service->update($id, $data);

        $transfer = $this->service->find($id);
        $this->notifications->notify(
            'sponsorship_transfer_updated',
            'تحديث عقد نقل كفالة',
            'حدّث ' . Auth::guard('admin')->user()->name . ' عقد نقل الكفالة رقم ' . $transfer->contract_number,
            route('admin.sponsorship-transfers.show', $id),
            [$transfer->branch_id]
        );

        return redirect()->route('admin.sponsorship-transfers.show', $id)
            ->with('success', 'تم تحديث العقد.');
    }

    public function destroy(int $id)
    {
        $transfer       = $this->service->find($id);
        $contractNumber = $transfer->contract_number;
        $branchId       = $transfer->branch_id;
        $adminName      = Auth::guard('admin')->user()->name;

        $this->service->delete($id);

        $this->notifications->notify(
            'sponsorship_transfer_deleted',
            'تم حذف عقد نقل كفالة',
            'حذف ' . $adminName . ' عقد نقل الكفالة رقم ' . $contractNumber,
            route('admin.sponsorship-transfers.index'),
            [$branchId]
        );

        return redirect()->route('admin.sponsorship-transfers.index')
            ->with('success', 'تم حذف العقد.');
    }

    public function updateStatus(Request $request, int $id)
    {
        $request->validate(['action' => 'required|in:forward,complete,cancel']);
        $transfer = match ($request->action) {
            'forward'  => $this->service->forwardToAccounts($id),
            'complete' => $this->service->complete($id),
            'cancel'   => $this->service->cancel($id),
        };

        $titles = [
            'forward'  => 'تم إحالة عقد نقل الكفالة للحسابات',
            'complete' => 'اكتمل نقل الكفالة',
            'cancel'   => 'تم إلغاء عقد نقل الكفالة',
        ];
        $this->notifications->notify(
            'sponsorship_transfer_forwarded',
            $titles[$request->action],
            'عقد نقل الكفالة رقم ' . $transfer->contract_number,
            route('admin.sponsorship-transfers.show', $id),
            [$transfer->branch_id]
        );

        return back()->with('success', 'تم تحديث حالة العقد.');
    }

    public function print(int $id)
    {
        $transfer = $this->service->find($id);
        return view('admin.sponsorship-transfers.print', compact('transfer'));
    }

    public function reports(Request $request)
    {
        $branchId = $this->branchFilter();
        $branches = Branch::where('active', true)->orderBy('name')->get();

        $query = SponsorshipTransfer::query()
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($request->filled('branch_id') && !$branchId, fn($q) => $q->where('branch_id', $request->branch_id))
            ->when($request->filled('date_from'), fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn($q) => $q->whereDate('created_at', '<=', $request->date_to));

        $total           = (clone $query)->count();
        $statusCounts    = (clone $query)->selectRaw('current_status, count(*) as cnt')->groupBy('current_status')->pluck('cnt', 'current_status');
        $paymentCounts   = (clone $query)->selectRaw('payment_status, count(*) as cnt')->groupBy('payment_status')->pluck('cnt', 'payment_status');
        $deptCounts      = (clone $query)->selectRaw('current_department, count(*) as cnt')->groupBy('current_department')->pluck('cnt', 'current_department');
        $totalFees       = (clone $query)->sum('total_fees');
        $totalServiceFee = (clone $query)->sum('service_fee');
        $totalLoss       = (clone $query)->sum('loss_amount');
        $byBranch        = (clone $query)->with('branch')
                            ->selectRaw('branch_id, count(*) as cnt, sum(total_fees) as fees')
                            ->groupBy('branch_id')->get();

        $statuses      = SponsorshipTransfer::statuses();
        $paymentLabels = SponsorshipTransfer::paymentStatuses();
        $deptLabels    = SponsorshipTransfer::departments();

        return view('admin.sponsorship-transfers.reports', compact(
            'total', 'statusCounts', 'paymentCounts', 'deptCounts',
            'totalFees', 'totalServiceFee', 'totalLoss', 'byBranch',
            'statuses', 'paymentLabels', 'deptLabels', 'branches', 'branchId'
        ));
    }
}
