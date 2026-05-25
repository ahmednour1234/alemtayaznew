<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTransferRequest;
use App\Services\BranchService;
use App\Services\TransferService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransferController extends Controller
{
    public function __construct(
        private readonly TransferService $service,
        private readonly BranchService $branchService,
    ) {}

    public function index(Request $request)
    {
        $filters   = $request->only('from_branch_id', 'to_branch_id', 'status', 'date_from', 'date_to');
        $transfers = $this->service->list($filters);
        $branches  = $this->branchService->allActive();
        return view('admin.transfers.index', compact('transfers', 'branches', 'filters'));
    }

    public function create()
    {
        $branches = $this->branchService->allActive();
        return view('admin.transfers.create', compact('branches'));
    }

    public function store(StoreTransferRequest $request)
    {
        $data = $request->validated();
        $data['admin_id'] = Auth::guard('admin')->id();
        $this->service->store($data);
        return redirect()->route('admin.transfers.index')->with('success', 'تم إنشاء طلب التحويل وهو في انتظار الموافقة.');
    }

    public function show(int $id)
    {
        $transfer = $this->service->find($id);
        return view('admin.transfers.show', compact('transfer'));
    }

    public function edit(int $id)
    {
        $transfer = $this->service->find($id);
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
