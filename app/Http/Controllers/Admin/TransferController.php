<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTransferRequest;
use App\Services\BranchService;
use App\Services\NotificationService;
use App\Services\TransferService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransferController extends Controller
{
    public function __construct(
        private readonly TransferService $service,
        private readonly BranchService $branchService,
        private readonly NotificationService $notifService,
    ) {}

    public function index(Request $request)
    {
        $me        = Auth::guard('admin')->user();
        $filters   = $request->only('from_branch_id', 'to_branch_id', 'status', 'date_from', 'date_to');
        if ($me->isBranchAdmin()) {
            $filters['branch_id'] = $me->branch_id;
            unset($filters['from_branch_id'], $filters['to_branch_id']);
        }
        $transfers = $this->service->list($filters);
        $branches  = $me->isBranchAdmin()
            ? $this->branchService->allActive()->where('id', $me->branch_id)
            : $this->branchService->allActive();
        $trashed   = $this->service->trashed();
        return view('admin.transfers.index', compact('transfers', 'branches', 'filters', 'trashed'));
    }

    public function create()
    {
        $me       = Auth::guard('admin')->user();
        // Branch admin can only transfer FROM their branch; destination = all active branches
        $branches = $this->branchService->allActive();
        $fromBranch = $me->isBranchAdmin() ? $me->branch_id : null;
        return view('admin.transfers.create', compact('branches', 'fromBranch'));
    }

    public function store(StoreTransferRequest $request)
    {
        $data = $request->validated();
        $me   = Auth::guard('admin')->user();
        $data['admin_id'] = $me->id;
        if ($me->isBranchAdmin()) {
            $data['from_branch_id'] = $me->branch_id;
        }
        $transfer = $this->service->store($data);
        $this->notifService->notify(
            'transfer_created',
            'تم تحويل مبلغ بين الفروع',
            'تم إنشاء طلب تحويل بقيمة ' . number_format($data['amount'] ?? 0, 0) . ' ريال (بانتظار الموافقة)',
            route('admin.transfers.index'),
            array_unique(array_filter([$data['from_branch_id'] ?? null, $data['to_branch_id'] ?? null]))
        );
        return redirect()->route('admin.transfers.index')->with('success', 'تم إنشاء طلب التحويل وهو في انتظار الموافقة.');
    }

    public function show(int $id)
    {
        $transfer = $this->service->find($id);
        return view('admin.transfers.show', compact('transfer'));
    }

    public function edit(int $id)
    {
        $me       = Auth::guard('admin')->user();
        $transfer = $this->service->find($id);
        if ($me->isBranchAdmin() &&
            $transfer->from_branch_id !== $me->branch_id &&
            $transfer->to_branch_id !== $me->branch_id) {
            abort(403);
        }
        $branches = $this->branchService->allActive();
        return view('admin.transfers.edit', compact('transfer', 'branches'));
    }

    public function update(StoreTransferRequest $request, int $id)
    {
        try {
            $this->service->update($id, $request->validated());
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
        return redirect()->route('admin.transfers.index')->with('success', 'تم تحديث التحويل.');
    }

    public function approve(int $id)
    {
        $admin = Auth::guard('admin')->user();
        if (! $admin->isSuperAdmin() && ! $admin->hasPermission('transfers.approve')) {
            abort(403, 'ليس لديك صلاحية الموافقة على التحويلات.');
        }
        try {
            $this->service->approve($id, $admin);
            $transfer = $this->service->find($id);
            $this->notifService->notifyOne(
                $transfer->admin_id,
                'transfer_approved',
                'تمت الموافقة على التحويل',
                'تمت الموافقة على تحويلك بقيمة ' . number_format($transfer->amount, 0) . ' ريال',
                route('admin.transfers.index')
            );
            $this->notifService->notify(
                'transfer_approved',
                'تمت الموافقة على تحويل',
                'تمت الموافقة على تحويل بقيمة ' . number_format($transfer->amount, 0) . ' ريال',
                route('admin.transfers.index'),
                array_unique(array_filter([$transfer->from_branch_id, $transfer->to_branch_id]))
            );
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
        return back()->with('success', 'تمت الموافقة على التحويل.');
    }

    public function reject(Request $request, int $id)
    {
        $request->validate(['rejection_reason' => ['required', 'string', 'max:500']]);
        $admin = Auth::guard('admin')->user();
        if (! $admin->isSuperAdmin() && ! $admin->hasPermission('transfers.approve')) {
            abort(403);
        }
        try {
            $this->service->reject($id, $admin, $request->rejection_reason);
            $transfer = $this->service->find($id);
            $this->notifService->notifyOne(
                $transfer->admin_id,
                'transfer_rejected',
                'تم رفض التحويل',
                'تم رفض تحويلك بقيمة ' . number_format($transfer->amount, 0) . ' ريال',
                route('admin.transfers.index')
            );
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
        return back()->with('success', 'تم رفض التحويل.');
    }

    public function destroy(int $id)
    {
        $this->service->destroy($id);
        return back()->with('success', 'تم حذف التحويل.');
    }

    public function restore(int $id)
    {
        $this->service->restore($id);
        return back()->with('success', 'تم استعادة التحويل.');
    }
}
