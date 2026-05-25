<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Services\RoleService;

class RoleController extends Controller
{
    public function __construct(private readonly RoleService $service) {}

    public function index()
    {
        $roles = $this->service->list();
        return view('admin.settings.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = $this->service->allPermissions();
        return view('admin.settings.roles.create', compact('permissions'));
    }

    public function store(StoreRoleRequest $request)
    {
        $this->service->store($request->validated());
        return redirect()->route('admin.settings.roles.index')->with('success', 'تم إنشاء الدور بنجاح.');
    }

    public function edit(int $id)
    {
        $role        = $this->service->find($id);
        $permissions = $this->service->allPermissions();
        return view('admin.settings.roles.edit', compact('role', 'permissions'));
    }

    public function update(UpdateRoleRequest $request, int $id)
    {
        $this->service->update($id, $request->validated());
        return redirect()->route('admin.settings.roles.index')->with('success', 'تم تحديث الدور.');
    }

    public function destroy(int $id)
    {
        $this->service->destroy($id);
        return back()->with('success', 'تم حذف الدور.');
    }
}
