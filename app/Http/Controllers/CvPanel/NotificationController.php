<?php

namespace App\Http\Controllers\CvPanel;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * إشعارات لوحة السير الذاتية.
 *
 * تقرأ من AdminNotification نفسه — فالإشعار واحد أينما ظهر — لكنها تعرضه
 * داخل اللوحة ولا تعتمد على مسارات لوحة الإدارة المحكومة بالصلاحيات.
 */
class NotificationController extends Controller
{
    /** أنواع الإشعارات التي تخصّ دورة السيرة الذاتية. */
    public const TYPES = [
        'cv_reserved',
        'worker_reservation_expired',
        'worker_reservation_expiring',
        'worker_cv_uploaded',
        'worker_assigned',
        'worker_unassigned',
    ];

    public function index()
    {
        $me = Auth::guard('admin')->user();

        $notifications = self::scope($me)->latest()->paginate(30);

        // فتح القائمة كاملةً يعني الاطّلاع عليها
        self::scope($me)->whereNull('read_at')->update(['read_at' => now()]);

        return view('cv-panel.notifications.index', compact('notifications'));
    }

    /**
     * تعليم إشعار مقروءاً ثم التحويل إلى وجهته داخل اللوحة.
     *
     * روابط الإشعارات تشير إلى صفحة العاملة في لوحة الإدارة، وقد لا يملك
     * موظّف اللوحة صلاحيتها؛ فنحوّله إلى قائمة السير مبحوثاً فيها برقم
     * العاملة بدل أن يصطدم بـ 403.
     */
    public function read(int $id)
    {
        $me           = Auth::guard('admin')->user();
        $notification = AdminNotification::where('admin_id', $me->id)->findOrFail($id);
        $notification->markRead();

        if (preg_match('#/workers/(\d+)#', (string) $notification->url, $m)) {
            return redirect()->route('cv-panel.cvs.index', ['search' => $m[1]]);
        }

        return redirect()->route('cv-panel.notifications.index');
    }

    public function readAll(Request $request)
    {
        $me = Auth::guard('admin')->user();

        self::scope($me)->whereNull('read_at')->update(['read_at' => now()]);

        return $request->wantsJson()
            ? response()->json(['ok' => true])
            : back();
    }

    /** إشعارات هذا المستخدم المتعلّقة بدورة السيرة الذاتية. */
    public static function scope($me)
    {
        return AdminNotification::where('admin_id', $me->id)
            ->whereIn('type', self::TYPES);
    }
}
