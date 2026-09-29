<?php

namespace App\Http\Controllers\CvPanel;

use App\Http\Controllers\Controller;
use App\Http\Middleware\CvPanelAccess;
use App\Models\Admin;
use App\Models\Nationality;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * إدارة مستخدمي لوحة السير الذاتية.
 *
 * ثلاثة أدوار لا رابع لها: منسّق، وخدمة عملاء، ومدير فرع. الإدارة بيد
 * المديرين وحدهم — لو تُركت لغيرهم لأمكن لأيّ مستخدم أن يرقّي نفسه.
 */
class UserController extends Controller
{
    public function index(): View
    {
        $this->authorizeManage();

        return view('cv-panel.users.index', [
            'users'         => Admin::whereIn('department', CvPanelAccess::DEPARTMENTS)
                ->with('managedNationalities', 'branch')
                ->orderBy('name')
                ->get(),
            'roles'         => $this->roles(),
            'nationalities' => Nationality::where('active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeManage();

        $data = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'max:255', 'unique:admins,email'],
            'password'   => ['required', 'string', 'min:8'],
            'department' => ['required', Rule::in(CvPanelAccess::DEPARTMENTS)],
        ], [
            'email.unique'        => __('cv-panel.users.email_taken'),
            'password.min'        => __('cv-panel.users.password_min'),
            'department.in'       => __('cv-panel.users.role_invalid'),
        ]);

        $me = Auth::guard('admin')->user();

        Admin::create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'password'   => Hash::make($data['password']),
            'department' => $data['department'],
            // يرث فرع من أنشأه: اللوحة تعمل داخل الفرع لا عبر الفروع
            'branch_id'  => $me->branch_id,
            'active'     => true,
        ]);

        return back()->with('success', __('cv-panel.users.created', ['name' => $data['name']]));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $this->authorizeManage();

        $user = $this->findPanelUser($id);

        $data = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'max:255', Rule::unique('admins', 'email')->ignore($user->id)],
            // كلمة المرور اختيارية عند التعديل: تُترك فارغة لتبقى كما هي
            'password'   => ['nullable', 'string', 'min:8'],
            'department' => ['required', Rule::in(CvPanelAccess::DEPARTMENTS)],
        ], [
            'email.unique'  => __('cv-panel.users.email_taken'),
            'password.min'  => __('cv-panel.users.password_min'),
            'department.in' => __('cv-panel.users.role_invalid'),
        ]);

        $user->fill([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'department' => $data['department'],
        ]);

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        // من لم يعد منسّقاً لا معنى لبقاء جنسيات مسندة إليه
        if ($data['department'] !== CvPanelAccess::COORDINATION) {
            $user->managedNationalities()->detach();
        }

        return back()->with('success', __('cv-panel.users.updated', ['name' => $user->name]));
    }

    /** تعطيل/تفعيل بدل الحذف: المستخدم مرتبط بسجلّات حجز ورفع. */
    public function toggle(int $id): RedirectResponse
    {
        $this->authorizeManage();

        $user = $this->findPanelUser($id);
        $me   = Auth::guard('admin')->user();

        // لا يُعطّل المستخدم نفسه فيُغلق الباب على من بيده المفتاح
        if ($user->id === $me->id) {
            return back()->with('error', __('cv-panel.users.cannot_disable_self'));
        }

        $user->update(['active' => ! $user->active]);

        return back()->with('success', $user->active
            ? __('cv-panel.users.enabled', ['name' => $user->name])
            : __('cv-panel.users.disabled', ['name' => $user->name]));
    }

    /**
     * المستخدم المقصود لا بدّ أن يكون من مستخدمي اللوحة.
     * بغير هذا القيد أمكن تعديل أي حساب في النظام من هنا.
     */
    private function findPanelUser(int $id): Admin
    {
        return Admin::whereIn('department', CvPanelAccess::DEPARTMENTS)->findOrFail($id);
    }

    private function roles(): array
    {
        $labels = Admin::departments();

        return collect(CvPanelAccess::DEPARTMENTS)
            ->mapWithKeys(fn ($d) => [$d => $labels[$d] ?? $d])
            ->all();
    }

    private function authorizeManage(): void
    {
        $me = Auth::guard('admin')->user();

        abort_unless(
            $me->isSuperAdmin() || in_array($me->department, CvPanelAccess::SUPERVISORS, true),
            403,
            __('cv-panel.users.denied')
        );
    }
}
