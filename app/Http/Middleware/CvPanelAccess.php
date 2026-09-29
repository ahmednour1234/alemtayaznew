<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * الوصول إلى لوحة إدارة السير الذاتية.
 *
 * اللوحة مقصورة على الأقسام المعنيّة بدورة السيرة الذاتية: التنسيق يرفع،
 * وخدمة العملاء تحجز، والمديرون يشرفون. أي قسم آخر لا شأن له بها.
 */
class CvPanelAccess
{
    /*
     * أدوار لوحة السير الثلاثة ولا رابع لها:
     *  • المنسّق        — يرفع السير للجنسيات المسندة إليه.
     *  • خدمة العملاء   — تحجز السيرة للعميل.
     *  • مدير الفرع     — يشرف ويُسند الجنسيات ويدير مستخدمي اللوحة.
     *
     * المصدر الوحيد لهذه القائمة: يقرأها الوسيط والكنترولرات والخدمات،
     * فلا يتفرّق التعريف ويختلف من موضع لآخر.
     */
    public const COORDINATION     = 'coordination';
    public const CUSTOMER_SERVICE = 'customer_service';
    public const MANAGER          = 'branch_manager';

    /** الأقسام التي تدخل اللوحة. */
    public const DEPARTMENTS = [self::COORDINATION, self::CUSTOMER_SERVICE, self::MANAGER];

    /** من يملك صلاحيات الإشراف داخل اللوحة. */
    public const SUPERVISORS = [self::MANAGER];

    public function handle(Request $request, Closure $next): Response
    {
        $me = Auth::guard('admin')->user();

        if (! $me) {
            return redirect()->route('admin.login');
        }

        if (! self::allows($me)) {
            abort(403, __('cv-panel.denied'));
        }

        return $next($request);
    }

    /**
     * هل يدخل هذا المستخدم اللوحة؟
     * قاعدة واحدة يشترك فيها الوسيط ورابط القائمة الجانبية، فلا يظهر رابط
     * يؤدي إلى 403.
     */
    public static function allows(?\App\Models\Admin $admin): bool
    {
        if (! $admin) {
            return false;
        }

        return $admin->isSuperAdmin()
            || in_array($admin->department, self::DEPARTMENTS, true);
    }
}
