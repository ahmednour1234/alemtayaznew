<?php

namespace App\Http\Controllers\CvPanel;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Nationality;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * إسناد الجنسيات إلى المنسّقين.
 *
 * المنسّق يعمل على الجنسيات المسندة إليه وحدها: يرفع سيرها ويصله إشعار
 * حين تُحجز عاملة منها. الإسناد بيد المديرين لا المنسّق نفسه.
 */
class CoordinatorController extends Controller
{
    public function index(): View
    {
        $this->authorizeManage();

        return view('cv-panel.coordinators.index', [
            'coordinators' => Admin::where('active', true)
                ->where('department', 'coordination')
                ->with('managedNationalities', 'branch')
                ->orderBy('name')
                ->get(),
            'nationalities' => Nationality::where('active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $this->authorizeManage();

        $data = $request->validate([
            'nationality_ids'   => ['nullable', 'array'],
            'nationality_ids.*' => ['integer', 'exists:nationalities,id'],
        ]);

        $coordinator = Admin::where('department', 'coordination')->findOrFail($id);
        $coordinator->managedNationalities()->sync($data['nationality_ids'] ?? []);

        return back()->with('success', __('cv-panel.coordinators.saved', ['name' => $coordinator->name]));
    }

    /**
     * الإسناد إجراء إداري: مقصور على المديرين والسوبر أدمن.
     * لو تُرك للمنسّق لأمكنه توسيع نطاقه بنفسه.
     */
    private function authorizeManage(): void
    {
        $me = Auth::guard('admin')->user();

        abort_unless(
            $me->isSuperAdmin() || in_array($me->department, ['branch_manager', 'chairman'], true),
            403,
            __('cv-panel.coordinators.denied')
        );
    }
}
