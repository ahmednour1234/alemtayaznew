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

    /**
     * تبويبات التصفية: المفتاح في الرابط، والقيمة أنواع الإشعارات تحته.
     * مصفوفة فارغة تعني «كل الأنواع».
     */
    public const TABS = [
        'all'      => [],
        'reserved' => ['cv_reserved'],
        'uploads'  => ['worker_cv_uploaded'],
        'released' => ['worker_reservation_expired', 'worker_unassigned'],
    ];

    public function index(Request $request)
    {
        $me  = Auth::guard('admin')->user();
        $tab = $request->input('tab', 'all');

        if (! array_key_exists($tab, self::TABS)) {
            $tab = 'all';
        }

        $query = self::scope($me);

        if (self::TABS[$tab]) {
            $query->whereIn('type', self::TABS[$tab]);
        }

        // تبويب «غير المقروء» تصفية على الحالة لا على النوع
        if ($request->boolean('unread')) {
            $query->whereNull('read_at');
        }

        $notifications = $query->latest()->paginate(30)->withQueryString();

        // عدد غير المقروء لكل تبويب — يُحسب قبل التعليم بالقراءة
        $counts = [];
        foreach (self::TABS as $key => $types) {
            $c = self::scope($me)->whereNull('read_at');
            if ($types) {
                $c->whereIn('type', $types);
            }
            $counts[$key] = $c->count();
        }

        /*
         * التعليم بالقراءة يقتصر على المعروض في هذه الصفحة.
         * لو عُلّم الكلّ لأفرغنا تبويب «غير المقروء» بمجرّد فتح الصفحة،
         * ولضاع على المستخدم ما لم يره بعد في التبويبات الأخرى.
         */
        $ids = $notifications->getCollection()
            ->whereNull('read_at')
            ->pluck('id')
            ->all();

        if ($ids) {
            AdminNotification::whereIn('id', $ids)->update(['read_at' => now()]);
        }

        return view('cv-panel.notifications.index', [
            'notifications' => $notifications,
            'tab'           => $tab,
            'counts'        => $counts,
            'unreadOnly'    => $request->boolean('unread'),
        ]);
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
