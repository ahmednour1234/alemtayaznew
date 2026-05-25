<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreIncomeTypeRequest;
use App\Http\Requests\Admin\UpdateIncomeTypeRequest;
use App\Services\IncomeTypeService;

class IncomeTypeController extends Controller
{
    public function __construct(private readonly IncomeTypeService $service) {}

    public function index()
    {
        $types   = $this->service->list(request()->only('search', 'active'));
        $trashed = $this->service->trashed();
        return view('admin.income-types.index', compact('types', 'trashed'));
    }

    public function create()
    {
        return view('admin.income-types.create');
    }

    public function store(StoreIncomeTypeRequest $request)
    {
        $data = $request->validated();
        $data['active'] = $request->boolean('active', true);
        $this->service->store($data);
        return redirect()->route('admin.income-types.index')->with('success', 'تم إنشاء نوع الدخل بنجاح.');
    }

    public function show(int $id)
    {
        $type = $this->service->find($id);
        return view('admin.income-types.show', compact('type'));
    }

    public function edit(int $id)
    {
        $type = $this->service->find($id);
        return view('admin.income-types.edit', compact('type'));
    }

    public function update(UpdateIncomeTypeRequest $request, int $id)
    {
        $data = $request->validated();
        $data['active'] = $request->boolean('active');
        $this->service->update($id, $data);
        return redirect()->route('admin.income-types.index')->with('success', 'تم تحديث نوع الدخل.');
    }

    public function destroy(int $id)
    {
        $this->service->destroy($id);
        return back()->with('success', 'تم حذف نوع الدخل.');
    }

    public function restore(int $id)
    {
        $this->service->restore($id);
        return back()->with('success', 'تم استعادة نوع الدخل.');
    }

    public function toggleActive(int $id)
    {
        $type = $this->service->toggleActive($id);
        return back()->with('success', $type->active ? 'تم التفعيل.' : 'تم إلغاء التفعيل.');
    }
}
