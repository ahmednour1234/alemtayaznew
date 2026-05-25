<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreExpenseRequest;
use App\Http\Requests\Admin\UpdateExpenseRequest;
use App\Services\BranchService;
use App\Services\ExpenseService;
use App\Services\ExpenseTypeService;
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
    ) {}

    public function index(Request $request)
    {
        $filters  = $request->only('branch_id', 'expense_type_id', 'status', 'payment_method', 'date_from', 'date_to');
        $expenses = $this->service->list($filters);
        $totals   = [
            'approved' => $this->service->totalByStatus('approved', $filters),
            'pending'  => $this->service->totalByStatus('pending', $filters),
            'rejected' => $this->service->totalByStatus('rejected', $filters),
        ];
        $branches = $this->branchService->allActive();
        $types    = $this->expenseTypeService->allActive();
        return view('admin.expenses.index', compact('expenses', 'totals', 'branches', 'types', 'filters'));
    }

    public function create()
    {
        $branches = $this->branchService->allActive();
        $types    = $this->expenseTypeService->allActive();
        return view('admin.expenses.create', compact('branches', 'types'));
    }

    public function store(StoreExpenseRequest $request)
    {
        $data = $request->validated();
        $data['admin_id'] = Auth::guard('admin')->id();
        $this->service->store($data, $request->file('attachment'));
        return redirect()->route('admin.expenses.index')->with('success', 'تم إضافة المصروف بنجاح وهو في انتظار الموافقة.');
    }

    public function show(int $id)
    {
        $expense = $this->service->find($id);
        return view('admin.expenses.show', compact('expense'));
    }

    public function edit(int $id)
    {
        $expense  = $this->service->find($id);
        $branches = $this->branchService->allActive();
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
        $filters = $request->only('branch_id', 'expense_type_id', 'status', 'payment_method', 'date_from', 'date_to');
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
