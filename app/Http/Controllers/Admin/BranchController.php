<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBranchRequest;
use App\Http\Requests\Admin\UpdateBranchRequest;
use App\Services\BranchService;
use App\Services\NotificationService;

class BranchController extends Controller
{
    public function __construct(
        private readonly BranchService $service,
        private readonly NotificationService $notifService,
    ) {}

    public function index()
    {
        $branches = $this->service->list(request()->only('search', 'active', 'city'));
        $trashed  = $this->service->trashed();
        return view('admin.branches.index', compact('branches', 'trashed'));
    }

    public function create()
    {
        return view('admin.branches.create');
    }

    public function store(StoreBranchRequest $request)
    {
        $data = $request->validated();
        $data['active'] = $request->boolean('active', true);
        $data['public'] = $request->boolean('public');
        $branch = $this->service->store($data);
        $this->notifService->notify(
            'branch_created',
            'تم إضافة فرع جديد',
            'تم إضافة فرع جديد باسم ' . ($branch->name ?? ''),
            route('admin.branches.show', $branch->id)
        );
        return redirect()->route('admin.branches.index')->with('success', 'تم إنشاء الفرع بنجاح.');
    }

    public function show(int $id)
    {
        $branch = $this->service->find($id);
        return view('admin.branches.show', compact('branch'));
    }

    public function edit(int $id)
    {
        $branch = $this->service->find($id);
        return view('admin.branches.edit', compact('branch'));
    }

    public function update(UpdateBranchRequest $request, int $id)
    {
        $data = $request->validated();
        $data['active'] = $request->boolean('active');
        $data['public'] = $request->boolean('public');
        $this->service->update($id, $data);
        return redirect()->route('admin.branches.index')->with('success', 'تم تحديث الفرع بنجاح.');
    }

    public function destroy(int $id)
    {
        $this->service->destroy($id);
        return back()->with('success', 'تم حذف الفرع.');
    }

    public function restore(int $id)
    {
        $this->service->restore($id);
        return back()->with('success', 'تم استعادة الفرع.');
    }

    public function toggleActive(int $id)
    {
        $branch = $this->service->toggleActive($id);
        $msg = $branch->active ? 'تم تفعيل الفرع.' : 'تم إلغاء تفعيل الفرع.';
        return back()->with('success', $msg);
    }
}
