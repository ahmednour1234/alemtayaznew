<?php

namespace App\Http\Controllers\CvPanel;

use App\Http\Controllers\Controller;
use App\Http\Middleware\CvPanelAccess;
use App\Models\Admin;
use App\Models\Worker;
use App\Models\WorkerActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * فيديو العاملة في لوحة السير: رابط أو ملف مرفوع.
 *
 * الملف يُحفظ على القرص الخاص ويُخدَم للعامة عبر الكنترولر فقط (مثل الـ PDF)،
 * فلا يبقى رابطه صالحاً بعد حجز العاملة.
 */
class VideoController extends Controller
{
    public const DISK = 'cv_private';

    public function edit(int $id): View
    {
        $worker = Worker::with('nationality')->findOrFail($id);
        $this->authorizeFor($worker);

        return view('cv-panel.cvs.video', ['worker' => $worker]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $worker = Worker::findOrFail($id);
        $me     = $this->authorizeFor($worker);

        $data = $request->validate([
            'video_url'  => ['nullable', 'url', 'max:1000'],
            // 100 ميجا — راجع upload_max_filesize و post_max_size على السيرفر
            'video_file' => ['nullable', 'file', 'mimetypes:video/mp4,video/quicktime,video/webm,video/x-m4v,video/3gpp', 'max:102400'],
            'remove'     => ['nullable', 'boolean'],
        ], [
            'video_url.url'        => 'الرابط غير صحيح — لازم يبدأ بـ https://',
            'video_file.mimetypes' => 'الملف لازم يكون فيديو (mp4 / mov / webm).',
            'video_file.max'       => 'حجم الفيديو أكبر من 100 ميجا — ارفعه على Google Drive وحط الرابط.',
            'video_file.uploaded'  => 'فشل رفع الفيديو — غالباً حجمه أكبر من المسموح على السيرفر.',
        ]);

        if ($request->boolean('remove')) {
            $this->deleteFile($worker);
            $worker->forceFill(['video_url' => null, 'video_path' => null])->saveQuietly();
            $this->log($worker, $me, 'حُذف فيديو العاملة');

            return redirect()->route('cv-panel.cvs.video', $worker->id)->with('success', 'تم حذف الفيديو.');
        }

        $changes = ['video_url' => $data['video_url'] ?? null];

        if ($request->hasFile('video_file')) {
            $path = $request->file('video_file')->store('videos', self::DISK);

            if (! $path || ! Storage::disk(self::DISK)->exists($path)) {
                return back()->withInput()->with('error', 'تعذّر حفظ الفيديو على السيرفر.');
            }

            $this->deleteFile($worker);
            $changes['video_path'] = $path;
        }

        if (empty($changes['video_url']) && empty($changes['video_path']) && ! $worker->video_path) {
            return back()->withInput()->with('error', 'حط رابط الفيديو أو ارفع ملف.');
        }

        // saveQuietly: لا نريد تشغيل حرّاس الحالة لتعديل لا يخصّها
        $worker->forceFill($changes)->saveQuietly();
        $this->log($worker, $me, 'أُضيف/عُدّل فيديو العاملة');

        return redirect()->route('cv-panel.cvs.video', $worker->id)->with('success', 'تم حفظ الفيديو — يظهر الآن للعميل في الموقع.');
    }

    /** معاينة الملف داخل اللوحة. */
    public function file(int $id)
    {
        $worker = Worker::findOrFail($id);
        $this->authorizeFor($worker);

        abort_unless($worker->video_path && Storage::disk(self::DISK)->exists($worker->video_path), 404);

        return self::stream($worker);
    }

    /** بثّ الفيديو مع دعم Range — ضروري للتقديم/الترجيع في المتصفّح والجوال. */
    public static function stream(Worker $worker)
    {
        $response = new \Symfony\Component\HttpFoundation\BinaryFileResponse(
            Storage::disk(self::DISK)->path($worker->video_path)
        );
        $response->headers->set('Accept-Ranges', 'bytes');
        $response->setContentDisposition(
            \Symfony\Component\HttpFoundation\ResponseHeaderBag::DISPOSITION_INLINE,
            'video-' . $worker->id . '.' . pathinfo($worker->video_path, PATHINFO_EXTENSION)
        );
        $response->setAutoEtag();
        $response->setPublic();
        $response->setMaxAge(3600);

        return $response;
    }

    /**
     * من يضيف الفيديو: السوبر أدمن، والمدير، ومنسّق الجنسية.
     * خدمة العملاء تشاهد ولا تعدّل.
     */
    public static function canManage(?Admin $me, Worker $worker): bool
    {
        if (! $me) {
            return false;
        }

        return $me->isSuperAdmin()
            || in_array($me->department, CvPanelAccess::SUPERVISORS, true)
            || ($me->isCoordination() && $me->managedNationalities->contains('id', $worker->nationality_id));
    }

    private function authorizeFor(Worker $worker): Admin
    {
        $me = Auth::guard('admin')->user();
        abort_unless(self::canManage($me, $worker), 403, 'إضافة الفيديو لمنسّق الجنسية والمديرين فقط.');

        return $me;
    }

    private function deleteFile(Worker $worker): void
    {
        if ($worker->video_path) {
            Storage::disk(self::DISK)->delete($worker->video_path);
        }
    }

    private function log(Worker $worker, Admin $me, string $label): void
    {
        try {
            WorkerActivityLog::create([
                'worker_id'   => $worker->id,
                'worker_name' => $worker->name,
                'admin_id'    => $me->id,
                'admin_name'  => $me->name,
                'action'      => 'updated',
                'label'       => $label . ' من لوحة السير الذاتية',
                'ip_address'  => request()?->ip(),
            ]);
        } catch (\Throwable) {
        }
    }
}
