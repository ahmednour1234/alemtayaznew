<?php

namespace App\Services\CvPanel;

use App\Models\Admin;
use App\Models\Worker;
use App\Models\WorkerActivityLog;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Request;

/**
 * رفع السير الذاتية في لوحة إدارة CV.
 *
 * تُخزَّن الملفات على القرص الخاص cv_private لا العام: المسار ‎/file/{path}‎
 * يخدم أي ملف تحت storage/app/public بلا تحقّق، فوضعها هناك يجعل السير
 * المحجوزة قابلة للفتح بالرابط المباشر.
 */
class CvUploadService
{
    private const DISK = 'cv_private';

    /**
     * @param  array<string,mixed>   $data   بيانات مشتركة لكل الملفات
     * @param  UploadedFile[]        $files
     * @return array{created: Worker[], duplicates: string[]}
     */
    public function upload(array $data, array $files, Admin $actor): array
    {
        $created    = [];
        $duplicates = [];

        foreach ($files as $file) {
            $originalName = $file->getClientOriginalName();

            // التكرار يُقاس باسم الملف الأصلي — وهو ما يميّز السيرة عملياً
            $existing = Worker::where('original_cv_name', $originalName)->first();

            // سيرة سُجّلت من قبل لكن ملفها لم يُحفظ: نرفق الملف بها بدل رفضها كمكرّرة
            if ($existing && ! $existing->hasCvFile()) {
                $existing->update([
                    'cv_path' => $this->storeOrFail($file, $originalName),
                    'cv_disk' => self::DISK,
                ]);
                $created[] = $existing;
                continue;
            }

            if ($existing) {
                $duplicates[] = $originalName;
                continue;
            }

            // نحفظ الملف أولاً: لو فشل لا يُنشأ سجلّ بلا ملف
            $path = $this->storeOrFail($file, $originalName);

            $worker = Worker::create([
                'name'             => pathinfo($originalName, PATHINFO_FILENAME),
                'nationality_id'   => $data['nationality_id'],
                'experience'       => $data['experience'],
                'religion'         => $data['religion'],
                'profession'       => $data['profession'] ?? null,
                'cv_path'          => $path,
                'cv_disk'          => self::DISK,
                'is_transfer'      => (bool) ($data['is_transfer'] ?? false),
                'original_cv_name' => $originalName,
                'status'           => 'available',
                'gender'           => 'female',
                'admin_id'         => $actor->id,
                'branch_id'        => $actor->branch_id,
                'active'           => true,
            ]);

            $this->log($worker, $actor);
            $created[] = $worker;
        }

        return compact('created', 'duplicates');
    }

    /** التسجيل لا يجب أن يُفشل الرفع، لذا نبتلع أي خطأ. */
    /**
     * قرص السير مضبوط على throw=false، فالفشل (صلاحيات المجلد، امتلاء القرص)
     * يرجع false بصمت ويُنشئ سيرة بلا ملف. نحوّله لخطأ واضح يظهر للمنسّق.
     */
    private function storeOrFail(UploadedFile $file, string $originalName): string
    {
        $path = $file->store('cvs', self::DISK);

        if (! $path || ! \Illuminate\Support\Facades\Storage::disk(self::DISK)->exists($path)) {
            \Illuminate\Support\Facades\Log::error('CV upload: failed to store file', [
                'file' => $originalName,
                'root' => config('filesystems.disks.' . self::DISK . '.root'),
            ]);

            throw new \RuntimeException("تعذّر حفظ ملف «{$originalName}» على السيرفر. راجع صلاحيات مجلد storage/app/cv-private.");
        }

        return $path;
    }

    private function log(Worker $worker, Admin $actor): void
    {
        try {
            WorkerActivityLog::create([
                'worker_id'   => $worker->id,
                'worker_name' => $worker->name,
                'admin_id'    => $actor->id,
                'admin_name'  => $actor->name,
                'action'      => 'created',
                'label'       => 'رُفعت السيرة الذاتية من لوحة إدارة CV — '
                               . ($worker->nationality?->name ?? '—')
                               . '، ' . ($worker->experience_label ?? '—')
                               . '، ' . (Worker::religionOptions()[$worker->religion] ?? '—'),
                'ip_address'  => Request::ip(),
            ]);
        } catch (\Throwable) {
            // لا نُعطّل الرفع بسبب فشل التسجيل
        }
    }
}
