<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreIncomeRequest;
use App\Http\Requests\Admin\UpdateIncomeRequest;
use App\Services\BranchService;
use App\Services\IncomeService;
use App\Services\IncomeTypeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\IncomeExport;
use App\Imports\IncomeImport;

class IncomeController extends Controller
{
    public function __construct(
        private readonly IncomeService $service,
        private readonly BranchService $branchService,
        private readonly IncomeTypeService $incomeTypeService,
    ) {}

    public function index(Request $request)
    {
        $filters  = $request->only('branch_id', 'income_type_id', 'payment_method', 'date_from', 'date_to');
        $incomes  = $this->service->list($filters);
        $total    = $this->service->total($filters);
        $branches = $this->branchService->allActive();
        $types    = $this->incomeTypeService->allActive();
        return view('admin.incomes.index', compact('incomes', 'total', 'branches', 'types', 'filters'));
    }

    public function create()
    {
        $branches = $this->branchService->allActive();
        $types    = $this->incomeTypeService->allActive();
        return view('admin.incomes.create', compact('branches', 'types'));
    }

    public function store(StoreIncomeRequest $request)
    {
        $data = $request->validated();
        $data['admin_id'] = Auth::guard('admin')->id();
        $this->service->store($data, $request->file('attachment'));
        return redirect()->route('admin.incomes.index')->with('success', 'تم إضافة الدخل بنجاح.');
    }

    public function show(int $id)
    {
        $income = $this->service->find($id);
        return view('admin.incomes.show', compact('income'));
    }

    public function edit(int $id)
    {
        $income   = $this->service->find($id);
        $branches = $this->branchService->allActive();
        $types    = $this->incomeTypeService->allActive();
        return view('admin.incomes.edit', compact('income', 'branches', 'types'));
    }

    public function update(UpdateIncomeRequest $request, int $id)
    {
        $data = $request->validated();
        $this->service->update($id, $data, $request->file('attachment'));
        return redirect()->route('admin.incomes.index')->with('success', 'تم تحديث الدخل بنجاح.');
    }

    public function destroy(int $id)
    {
        $this->service->destroy($id);
        return back()->with('success', 'تم حذف الدخل.');
    }

    public function restore(int $id)
    {
        $this->service->restore($id);
        return back()->with('success', 'تم استعادة الدخل.');
    }

    public function export(Request $request)
    {
        $filters = $request->only('branch_id', 'income_type_id', 'payment_method', 'date_from', 'date_to');
        return Excel::download(new IncomeExport($filters), 'incomes_' . now()->format('Y-m-d') . '.xlsx');
    }

    public function importTemplate()
    {
        return Excel::download(new \App\Exports\IncomeTemplateExport(), 'incomes_template.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => ['required', 'file', 'mimes:xlsx,xls,csv']]);
        Excel::import(new IncomeImport(), $request->file('file'));
        return back()->with('success', 'تم استيراد البيانات بنجاح.');
    }
}
