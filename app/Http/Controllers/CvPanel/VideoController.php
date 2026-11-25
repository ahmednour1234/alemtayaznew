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

    /** حجم القطعة الواحدة من المتصفّح (2 ميجا) — أصغر بكثير من حدود رفع Hostinger. */
    public const CHUNK_BYTES = 2 * 1024 * 1024;

    /** أقصى حجم للفيديو بعد التجميع. */
    public const MAX_BYTES = 500 * 1024 * 1024;

    /**
     * رفع الفيديو على قطع.
     *
     * المتصفّح يقسّم الملف ويرسل القطع بالترتيب؛ نُلحق كل قطعة بملف مؤقت،
     * وعند آخر قطعة نتحقّق أنه فيديو فعلاً وننقله ونربطه بالعاملة.
     * هكذا لا يصطدم الفيديو الكبير بحدّ upload_max_filesize ولا يُسقط المتصفّح.
     */
    public function chunk(Request $request, int $id)
    {
        $worker = Worker::findOrFail($id);
        $me     = $this->authorizeFor($worker);

        $data = $request->validate([
            'upload_id' => ['required', 'string', 'regex:/^[A-Za-z0-9]{16,64}$/'],
            'index'     => ['required', 'integer', 'min:0'],
            'total'     => ['required', 'integer', 'min:1', 'max:' . (int) ceil(self::MAX_BYTES / self::CHUNK_BYTES)],
            'name'      => ['required', 'string', 'max:255'],
            'chunk'     => ['required', 'file', 'max:' . (int) (self::CHUNK_BYTES / 1024 + 64)],
        ]);

        $disk = Storage::disk(self::DISK);
        $disk->makeDirectory('tmp');
        $tmp = $disk->path('tmp/' . $worker->id . '_' . $data['upload_id'] . '.part');

        // أول قطعة تبدأ ملفاً جديداً؛ غيرها يجب أن يأتي بالترتيب
        if ((int) $data['index'] === 0) {
            @unlink($tmp);
        } elseif (! is_file($tmp) || filesize($tmp) !== (int) $data['index'] * self::CHUNK_BYTES) {
            return response()->json(['message' => 'انقطع الرفع — ابدأ من جديد.'], 422);
        }

        $out = fopen($tmp, 'ab');
        $in  = fopen($request->file('chunk')->getRealPath(), 'rb');
        stream_copy_to_stream($in, $out);
        fclose($in);
        fclose($out);

        if (filesize($tmp) > self::MAX_BYTES) {
            @unlink($tmp);
            return response()->json(['message' => 'الفيديو أكبر من 500 ميجا.'], 422);
        }

        // لسه فيه قطع
        if ((int) $data['index'] < (int) $data['total'] - 1) {
            return response()->json(['ok' => true]);
        }

        // آخر قطعة: نتأكد إنه فيديو بمحتواه لا باسمه
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($tmp) ?: '';
        if (! str_starts_with($mime, 'video/')) {
            @unlink($tmp);
            return response()->json(['message' => 'الملف ده مش فيديو.'], 422);
        }

        $ext = strtolower(pathinfo($data['name'], PATHINFO_EXTENSION));
        $ext = in_array($ext, ['mp4', 'mov', 'webm', 'm4v', '3gp'], true) ? $ext : 'mp4';

        $final = 'videos/' . $worker->id . '_' . \Illuminate\Support\Str::random(24) . '.' . $ext;
        $disk->makeDirectory('videos');

        if (! @rename($tmp, $disk->path($final))) {
            @unlink($tmp);
            return response()->json(['message' => 'تعذّر حفظ الفيديو على السيرفر.'], 500);
        }

        $this->deleteFile($worker);
        $worker->forceFill(['video_path' => $final])->saveQuietly();
        $this->log($worker, $me, 'رُفع فيديو العاملة');

        session()->flash('success', 'تم رفع الفيديو — يظهر الآن للعميل في الموقع.');

        return response()->json(['ok' => true, 'done' => true]);
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
