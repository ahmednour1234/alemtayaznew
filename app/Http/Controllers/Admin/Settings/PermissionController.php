<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Services\RoleService;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function __construct(private readonly RoleService $service) {}

    public function index()
    {
        $permissions = $this->service->allPermissions();
        return view('admin.settings.permissions.index', compact('permissions'));
    }
}
