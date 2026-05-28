<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreHousingRequest;
use App\Http\Requests\Admin\UpdateHousingRequest;
use App\Models\Admin;
use App\Models\Branch;
use App\Services\HousingService;
use Illuminate\Support\Facades\Auth;

class HousingController extends Controller
{
    public function __construct(private readonly HousingService $service) {}

    public function index()
    {
        $filters = request()->only('search', 'active', 'branch_id');

        // Branch admins see only their branch
        $me = Auth::guard('admin')->user();
        if ($me && $me->isBranchAdmin()) {
            $filters['branch_id'] = $me->branch_id;
        }

        $housings = $this->service->list($filters);
        $trashed  = $this->service->trashed();
        $branches = Branch::where('active', true)->orderBy('name')->get();

        return view('admin.housings.index', compact('housings', 'trashed', 'branches'));
    }

    public function create()
    {
        $branches = Branch::where('active', true)->orderBy('name')->get();
        $admins   = Admin::where('active', true)->orderBy('name')->get();
        return view('admin.housings.create', compact('branches', 'admins'));
    }

    public function store(StoreHousingRequest $request)
    {
        $data = $request->validated();
        $data['active'] = $request->boolean('active', true);

        $me = Auth::guard('admin')->user();
        if ($me && $me->isBranchAdmin()) {
            $data['branch_id'] = $me->branch_id;
        }

        $this->service->store($data);
        return redirect()->route('admin.housings.index')->with('success', 'تم إنشاء السكن بنجاح.');
    }

    public function edit(int $id)
    {
        $housing  = $this->service->find($id);
        $branches = Branch::where('active', true)->orderBy('name')->get();
        $admins   = Admin::where('active', true)->orderBy('name')->get();
        return view('admin.housings.edit', compact('housing', 'branches', 'admins'));
    }

    public function update(UpdateHousingRequest $request, int $id)
    {
        $data = $request->validated();
        $data['active'] = $request->boolean('active');
        $this->service->update($id, $data);
        return redirect()->route('admin.housings.index')->with('success', 'تم تحديث بيانات السكن.');
    }

    public function destroy(int $id)
    {
        $this->service->destroy($id);
        return back()->with('success', 'تم حذف السكن.');
    }

    public function restore(int $id)
    {
        $this->service->restore($id);
        return back()->with('success', 'تم استعادة السكن.');
    }

    public function toggleActive(int $id)
    {
        $h = $this->service->toggleActive($id);
        return back()->with('success', $h->active ? 'تم تفعيل السكن.' : 'تم تعطيل السكن.');
    }
}
