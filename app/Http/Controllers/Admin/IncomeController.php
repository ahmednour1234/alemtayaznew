<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreIncomeRequest;
use App\Http\Requests\Admin\UpdateIncomeRequest;
use App\Services\BranchService;
use App\Services\IncomeService;
use App\Services\IncomeTypeService;
use App\Services\NotificationService;
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
        private readonly NotificationService $notifService,
    ) {}

    public function index(Request $request)
    {
        $me       = Auth::guard('admin')->user();
        $filters  = $request->only('branch_id', 'income_type_id', 'payment_method', 'date_from', 'date_to');
        if ($me->isBranchAdmin()) {
            $filters['branch_id'] = $me->branch_id;
        }
        $incomes  = $this->service->list($filters);
        $total    = $this->service->total($filters);
        $branches = $me->isBranchAdmin()
            ? $this->branchService->allActive()->where('id', $me->branch_id)
            : $this->branchService->allActive();
        $types    = $this->incomeTypeService->allActive();
        $trashed  = $this->service->trashed();
        return view('admin.incomes.index', compact('incomes', 'total', 'branches', 'types', 'filters', 'trashed'));
    }

    public function create()
    {
        $me       = Auth::guard('admin')->user();
        $branches = $me->isBranchAdmin()
            ? $this->branchService->allActive()->where('id', $me->branch_id)
            : $this->branchService->allActive();
        $types    = $this->incomeTypeService->allActive();
        return view('admin.incomes.create', compact('branches', 'types'));
    }

    public function store(StoreIncomeRequest $request)
    {
        $data = $request->validated();
        $me   = Auth::guard('admin')->user();
        $data['admin_id'] = $me->id;
        if ($me->isBranchAdmin()) {
            $data['branch_id'] = $me->branch_id;
        }
        $income = $this->service->store($data, $request->file('attachment'));
        $this->notifService->notify(
            'income_created',
            'تم تسجيل إيراد جديد',
            'تم إضافة إيراد بقيمة ' . number_format($data['amount'] ?? 0, 0) . ' ريال',
            route('admin.incomes.show', $income->id),
            [$data['branch_id']]
        );
        return redirect()->route('admin.incomes.index')->with('success', 'تم إضافة الدخل بنجاح.');
    }

    public function show(int $id)
    {
        $income = $this->service->find($id);
        return view('admin.incomes.show', compact('income'));
    }

    public function edit(int $id)
    {
        $me       = Auth::guard('admin')->user();
        $income   = $this->service->find($id);
        if ($me->isBranchAdmin() && $income->branch_id !== $me->branch_id) {
            abort(403, 'ليس لديك صلاحية تعديل هذا السجل.');
        }
        $branches = $me->isBranchAdmin()
            ? $this->branchService->allActive()->where('id', $me->branch_id)
            : $this->branchService->allActive();
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
        $me      = Auth::guard('admin')->user();
        $filters = $request->only('branch_id', 'income_type_id', 'payment_method', 'date_from', 'date_to');
        if ($me->isBranchAdmin()) {
            $filters['branch_id'] = $me->branch_id;
        }
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
