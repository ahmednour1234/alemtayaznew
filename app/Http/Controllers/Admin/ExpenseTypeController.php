<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreExpenseTypeRequest;
use App\Http\Requests\Admin\UpdateExpenseTypeRequest;
use App\Services\ExpenseTypeService;

class ExpenseTypeController extends Controller
{
    public function __construct(private readonly ExpenseTypeService $service) {}

    public function index()
    {
        $types   = $this->service->list(request()->only('search', 'active'));
        $trashed = $this->service->trashed();
        return view('admin.expense-types.index', compact('types', 'trashed'));
    }

    public function create()
    {
        return view('admin.expense-types.create');
    }

    public function store(StoreExpenseTypeRequest $request)
    {
        $data = $request->validated();
        $data['active'] = $request->boolean('active', true);
        $this->service->store($data);
        return redirect()->route('admin.expense-types.index')->with('success', 'تم إنشاء نوع المصروف بنجاح.');
    }

    public function show(int $id)
    {
        $type = $this->service->find($id);
        return view('admin.expense-types.show', compact('type'));
    }

    public function edit(int $id)
    {
        $type = $this->service->find($id);
        return view('admin.expense-types.edit', compact('type'));
    }

    public function update(UpdateExpenseTypeRequest $request, int $id)
    {
        $data = $request->validated();
        $data['active'] = $request->boolean('active');
        $this->service->update($id, $data);
        return redirect()->route('admin.expense-types.index')->with('success', 'تم تحديث نوع المصروف.');
    }

    public function destroy(int $id)
    {
        $this->service->destroy($id);
        return back()->with('success', 'تم حذف نوع المصروف.');
    }

    public function restore(int $id)
    {
        $this->service->restore($id);
        return back()->with('success', 'تم استعادة نوع المصروف.');
    }

    public function toggleActive(int $id)
    {
        $type = $this->service->toggleActive($id);
        return back()->with('success', $type->active ? 'تم التفعيل.' : 'تم إلغاء التفعيل.');
    }
}
