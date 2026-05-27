<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdminRequest;
use App\Http\Requests\Admin\UpdateAdminRequest;
use App\Models\Branch;
use App\Services\AdminService;
use App\Services\NotificationService;
use App\Services\RoleService;

class AdminController extends Controller
{
    public function __construct(
        private readonly AdminService $service,
        private readonly RoleService  $roleService,
        private readonly NotificationService $notifService,
    ) {}

    public function index()
    {
        $admins  = $this->service->list(request()->only('search', 'active'));
        $trashed = $this->service->trashed();
        return view('admin.settings.admins.index', compact('admins', 'trashed'));
    }

    public function create()
    {
        $roles    = $this->roleService->list();
        $branches = Branch::where('active', true)->orderBy('name')->get();
        return view('admin.settings.admins.create', compact('roles', 'branches'));
    }

    public function store(StoreAdminRequest $request)
    {
        $newAdmin = $this->service->store($request->validated());
        $this->notifService->notify(
            'admin_created',
            'تم إنشاء مستخدم جديد',
            'تم إنشاء مستخدم جديد باسم ' . ($newAdmin->name ?? ''),
            route('admin.settings.admins.show', $newAdmin->id)
        );
        return redirect()->route('admin.settings.admins.index')->with('success', 'تم إنشاء المدير بنجاح.');
    }

    public function show(int $id)
    {
        $admin = $this->service->find($id);
        return view('admin.settings.admins.show', compact('admin'));
    }

    public function edit(int $id)
    {
        $admin    = $this->service->find($id);
        $roles    = $this->roleService->list();
        $branches = Branch::where('active', true)->orderBy('name')->get();
        return view('admin.settings.admins.edit', compact('admin', 'roles', 'branches'));
    }

    public function update(UpdateAdminRequest $request, int $id)
    {
        $this->service->update($id, $request->validated());
        return redirect()->route('admin.settings.admins.index')->with('success', 'تم تحديث المدير.');
    }

    public function destroy(int $id)
    {
        $this->service->destroy($id);
        return back()->with('success', 'تم حذف المدير.');
    }

    public function restore(int $id)
    {
        $this->service->restore($id);
        return back()->with('success', 'تم استعادة المدير.');
    }

    public function toggleActive(int $id)
    {
        $admin = $this->service->toggleActive($id);
        return back()->with('success', $admin->active ? 'تم تفعيل المدير.' : 'تم إلغاء تفعيل المدير.');
    }
}
