<?php

namespace App\Http\Controllers\CvPanel;

use App\Http\Controllers\Controller;
use App\Http\Middleware\CvPanelAccess;
use App\Services\Security\SecurityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * دخول لوحة السير الذاتية.
 *
 * تستخدم حارس admin نفسه وحسابات النظام نفسها — فالمنسّق وموظّف خدمة العملاء
 * موظّفون مسجّلون أصلاً ولا داعي لحسابات منفصلة تُدار مرّتين. المستقلّ هنا هو
 * الواجهة والمسار: شاشة دخول خاصة، وتوجيه إلى اللوحة لا إلى لوحة الإدارة،
 * وخروج يعود إلى شاشة اللوحة.
 *
 * ومن يدخل من هنا وليس من أقسام اللوحة يُرفض عند الدخول لا بعد التوجيه،
 * فلا يجد نفسه أمام 403 بعد تسجيل دخول ناجح.
 */
class AuthController extends Controller
{
    public function __construct(private readonly SecurityLogger $security) {}

    public function showLogin()
    {
        $me = Auth::guard('admin')->user();

        if ($me && CvPanelAccess::allows($me)) {
            return redirect()->route('cv-panel.dashboard');
        }

        return view('cv-panel.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => __('cv-panel.auth.bad_credentials')])->onlyInput('email');
        }

        $admin = Auth::guard('admin')->user();

        if (! $admin->active) {
            Auth::guard('admin')->logout();
            $this->security->logFailedLogin($credentials['email'], 'admin', $request, 'inactive');

            return back()->withErrors(['email' => __('cv-panel.auth.inactive')])->onlyInput('email');
        }

        // ليس من أقسام اللوحة: نرفضه هنا بدل أن نوجّهه إلى 403
        if (! CvPanelAccess::allows($admin)) {
            Auth::guard('admin')->logout();

            return back()->withErrors(['email' => __('cv-panel.denied')])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('cv-panel.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('cv-panel.login');
    }
}
