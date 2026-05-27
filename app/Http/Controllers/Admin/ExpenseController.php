<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreExpenseRequest;
use App\Http\Requests\Admin\UpdateExpenseRequest;
use App\Services\BranchService;
use App\Services\ExpenseService;
use App\Services\ExpenseTypeService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExpenseExport;
use App\Imports\ExpenseImport;

class ExpenseController extends Controller
{
    public function __construct(
        private readonly ExpenseService $service,
        private readonly BranchService $branchService,
        private readonly ExpenseTypeService $expenseTypeService,
        private readonly NotificationService $notifService,
    ) {}

    public function index(Request $request)
    {
        $me       = Auth::guard('admin')->user();
        $filters  = $request->only('branch_id', 'expense_type_id', 'status', 'payment_method', 'date_from', 'date_to');
        if ($me->isBranchAdmin()) {
            $filters['branch_id'] = $me->branch_id;
        }
        $expenses = $this->service->list($filters);
        $totals   = [
            'approved' => $this->service->totalByStatus('approved', $filters),
            'pending'  => $this->service->totalByStatus('pending', $filters),
            'rejected' => $this->service->totalByStatus('rejected', $filters),
        ];
        $branches = $me->isBranchAdmin()
            ? $this->branchService->allActive()->where('id', $me->branch_id)
            : $this->branchService->allActive();
        $types    = $this->expenseTypeService->allActive();
        $trashed  = $this->service->trashed();
        return view('admin.expenses.index', compact('expenses', 'totals', 'branches', 'types', 'filters', 'trashed'));
    }

    public function create()
    {
        $me       = Auth::guard('admin')->user();
        $branches = $me->isBranchAdmin()
            ? $this->branchService->allActive()->where('id', $me->branch_id)
            : $this->branchService->allActive();
        $types    = $this->expenseTypeService->allActive();
        return view('admin.expenses.create', compact('branches', 'types'));
    }

    public function store(StoreExpenseRequest $request)
    {
        $data = $request->validated();
        $me   = Auth::guard('admin')->user();
        $data['admin_id'] = $me->id;
        if ($me->isBranchAdmin()) {
            $data['branch_id'] = $me->branch_id;
        }
        $expense = $this->service->store($data, $request->file('attachment'));
        $this->notifService->notify(
            'expense_created',
            'تم تسجيل مصروف جديد',
            'تم إضافة مصروف بقيمة ' . number_format($data['amount'] ?? 0, 0) . ' ريال (بانتظار الموافقة)',
            route('admin.expenses.show', $expense->id),
            [$data['branch_id']]
        );
        return redirect()->route('admin.expenses.index')->with('success', 'تم إضافة المصروف بنجاح وهو في انتظار الموافقة.');
    }

    public function show(int $id)
    {
        $expense = $this->service->find($id);
        return view('admin.expenses.show', compact('expense'));
    }

    public function edit(int $id)
    {
        $me       = Auth::guard('admin')->user();
        $expense  = $this->service->find($id);
        if ($me->isBranchAdmin() && $expense->branch_id !== $me->branch_id) {
            abort(403, 'ليس لديك صلاحية تعديل هذا السجل.');
        }
        $branches = $me->isBranchAdmin()
            ? $this->branchService->allActive()->where('id', $me->branch_id)
            : $this->branchService->allActive();
        $types    = $this->expenseTypeService->allActive();
        return view('admin.expenses.edit', compact('expense', 'branches', 'types'));
    }

    public function update(UpdateExpenseRequest $request, int $id)
    {
        $data = $request->validated();
        try {
            $this->service->update($id, $data, $request->file('attachment'));
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
        return redirect()->route('admin.expenses.index')->with('success', 'تم تحديث المصروف.');
    }

    public function approve(int $id)
    {
        $admin = Auth::guard('admin')->user();
        if (! $admin->isSuperAdmin() && ! $admin->hasPermission('expenses.approve')) {
            abort(403, 'ليس لديك صلاحية الموافقة على المصاريف.');
        }
        try {
            $this->service->approve($id, $admin);
            $expense = $this->service->find($id);
            // Notify the creator that their expense was approved
            $this->notifService->notifyOne(
                $expense->admin_id,
                'expense_approved',
                'تمت الموافقة على المصروف',
                'تمت الموافقة على مصروفك بقيمة ' . number_format($expense->amount, 0) . ' ريال',
                route('admin.expenses.show', $id)
            );
            // Notify all admins with view permission (branch scoped)
            $this->notifService->notify(
                'expense_approved',
                'تمت الموافقة على مصروف',
                'تمت الموافقة على مصروف بقيمة ' . number_format($expense->amount, 0) . ' ريال',
                route('admin.expenses.show', $id),
                [$expense->branch_id]
            );
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
        return back()->with('success', 'تمت الموافقة على المصروف.');
    }

    public function reject(Request $request, int $id)
    {
        $request->validate(['rejection_reason' => ['required', 'string', 'max:500']]);
        $admin = Auth::guard('admin')->user();
        if (! $admin->isSuperAdmin() && ! $admin->hasPermission('expenses.approve')) {
            abort(403, 'ليس لديك صلاحية رفض المصاريف.');
        }
        try {
            $this->service->reject($id, $admin, $request->rejection_reason);
            $expense = $this->service->find($id);
            $this->notifService->notifyOne(
                $expense->admin_id,
                'expense_rejected',
                'تم رفض المصروف',
                'تم رفض مصروفك بقيمة ' . number_format($expense->amount, 0) . ' ريال',
                route('admin.expenses.show', $id)
            );
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
        return back()->with('success', 'تم رفض المصروف.');
    }

    public function destroy(int $id)
    {
        $this->service->destroy($id);
        return back()->with('success', 'تم حذف المصروف.');
    }

    public function restore(int $id)
    {
        $this->service->restore($id);
        return back()->with('success', 'تم استعادة المصروف.');
    }

    public function export(Request $request)
    {
        $me      = Auth::guard('admin')->user();
        $filters = $request->only('branch_id', 'expense_type_id', 'status', 'payment_method', 'date_from', 'date_to');
        if ($me->isBranchAdmin()) {
            $filters['branch_id'] = $me->branch_id;
        }
        return Excel::download(new ExpenseExport($filters), 'expenses_' . now()->format('Y-m-d') . '.xlsx');
    }

    public function importTemplate()
    {
        return Excel::download(new \App\Exports\ExpenseTemplateExport(), 'expenses_template.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => ['required', 'file', 'mimes:xlsx,xls,csv']]);
        Excel::import(new ExpenseImport(), $request->file('file'));
        return back()->with('success', 'تم استيراد البيانات بنجاح.');
    }
}
