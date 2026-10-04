<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Models\Client;
use App\Models\Nationality;
use App\Models\Worker;
use App\Models\WorkerActivityLog;
use Illuminate\Console\Command;

/**
 * إرجاع الحجوزات التي فكّها النظام تلقائياً.
 *
 * بعد إلغاء مهلة الـ72 ساعة صار الفكّ التلقائي القديم خطأً يُصحَّح: سير حُجزت
 * لعملاء ثمّ أُعيدت للإتاحة بمرور الوقت لا بقرار.
 *
 * العميل والحاجز لا يُحفظان في أعمدة عند الفكّ (تُصفَّر)، وإنّما يُذكران في
 * نصّ سجلّ النشاط، فنستخرجهما منه. ولأن العميل يُذكر بالاسم لا بالمعرّف،
 * نتخطّى أي اسم يطابق أكثر من عميل بدل أن نخمّن.
 */
class RestoreAutoReleasedReservations extends Command
{
    protected $signature = 'workers:restore-reservations
                            {--nationality= : اسم الجنسية أو جزء منه أو معرّفها الرقمي}
                            {--admin= : اسم الموظّف الحاجز أو جزء منه أو معرّفه الرقمي}
                            {--since= : اقتصر على ما فُكّ من هذا التاريخ (YYYY-MM-DD)}
                            {--apply : نفّذ التعديل؛ بدونه عرض فقط}
                            {--excel= : صدّر النتيجة إلى ملف إكسل بهذا الاسم}';

    protected $description = 'إرجاع حجوزات فكّها النظام تلقائياً إلى «محجوزة»';

    public function handle(): int
    {
        $natTerm   = $this->option('nationality');
        $adminTerm = $this->option('admin');

        // معرّف رقمي أدقّ من الاسم: أسماء الموظّفين تحمل بادئات
        // (مثل «موظف استقبال فرع كذا-») فيتعذّر مطابقتها نصّاً.
        if ($adminTerm && ctype_digit((string) $adminTerm)) {
            $admin = Admin::find((int) $adminTerm);

            if (! $admin) {
                $this->error("لا يوجد موظّف بالمعرّف {$adminTerm}.");
                return self::FAILURE;
            }

            $adminTerm = $admin->name;
            $this->info("الموظّف: {$adminTerm}");
        }

        $natIds = null;

        if ($natTerm) {
            // المعرّف الرقمي أضمن: أسماء الجنسيات تختلف في الهمزات والمسافات
            $natIds = ctype_digit((string) $natTerm)
                ? Nationality::where('id', (int) $natTerm)->pluck('id')
                : Nationality::where('name', 'like', '%' . $natTerm . '%')->pluck('id');

            if ($natIds->isEmpty()) {
                $this->error("لم يُعثر على جنسية تطابق «{$natTerm}». الجنسيات المتاحة:");

                Nationality::orderBy('name')->get(['id', 'name'])
                    ->each(fn ($n) => $this->line("  {$n->id} | {$n->name}"));

                return self::FAILURE;
            }
        }

        $logs = WorkerActivityLog::where('action', 'unassigned')
            ->whereNull('admin_id')
            ->where('label', 'like', '%فكّ النظام حجز العاملة تلقائياً%')
            ->when($adminTerm, fn ($q) => $q->where('label', 'like', '%بواسطة %' . $adminTerm . '%'))
            ->when($this->option('since'), fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->latest()
            ->get()
            ->unique('worker_id'); // أحدث فكّ لكلّ عاملة

        if ($logs->isEmpty()) {
            $this->warn('لا توجد سجلّات فكّ تلقائي مطابقة.');
            return self::SUCCESS;
        }

        $workers = Worker::whereIn('id', $logs->pluck('worker_id'))
            ->when($natIds, fn ($q) => $q->whereIn('nationality_id', $natIds))
            ->with('nationality')
            ->get()
            ->keyBy('id');

        $rows      = [];
        $excelRows = [];
        $plan      = [];
        $problems  = [];

        foreach ($logs as $log) {
            $worker = $workers->get($log->worker_id);

            if (! $worker) {
                continue; // خارج الجنسية المطلوبة أو حُذفت
            }

            // لا نمسّ سيرة ارتبطت من جديد بعد الفكّ
            if (! in_array(strtolower((string) $worker->status), ['available', 'reserved', 'assigned'], true)) {
                $problems[] = [$worker->id, $worker->name, 'حالتها ' . $worker->status_label . ' — لم تُمسّ'];
                continue;
            }

            [$clientName, $reserverName] = $this->parse($log->label);

            if (! $clientName) {
                $problems[] = [$worker->id, $worker->name, 'تعذّر استخراج اسم العميل من السجلّ'];
                continue;
            }

            $clients = Client::where('name', $clientName)->get();

            if ($clients->isEmpty()) {
                $problems[] = [$worker->id, $worker->name, "العميل «{$clientName}» غير موجود"];
                continue;
            }

            if ($clients->count() > 1) {
                $problems[] = [$worker->id, $worker->name, "«{$clientName}» يطابق {$clients->count()} عملاء — تخطّي"];
                continue;
            }

            $reserver = $reserverName
                ? Admin::where('name', $reserverName)->first()
                : null;

            $plan[] = [
                'worker'   => $worker,
                'client'   => $clients->first(),
                'reserver' => $reserver,
                'at'       => $log->created_at,
            ];

            $rows[] = [
                $worker->id,
                mb_strimwidth((string) $worker->name, 0, 26, '…'),
                $worker->nationality?->name ?? '—',
                mb_strimwidth($clientName, 0, 22, '…'),
                $reserver?->name ? mb_strimwidth($reserver->name, 0, 22, '…') : '— (بلا حاجز)',
            ];

            // صفوف التصدير كاملة بلا اختصار، فالإكسل لا يضيق كالطرفية
            $excelRows[] = [
                $worker->id,
                $worker->name,
                $worker->nationality?->name ?? '—',
                $clientName,
                $reserver?->name ?? '—',
                $worker->status_label,
                $worker->client_id ? 'نعم' : 'لا',
                $log->created_at?->format('Y-m-d H:i'),
            ];
        }

        if ($rows) {
            $this->info('السير التي ستُرجَع محجوزة:');
            $this->table(['الرقم', 'الاسم', 'الجنسية', 'العميل', 'الحاجز'], $rows);
        }

        if ($problems) {
            $this->warn('سير لن تُمسّ:');
            $this->table(['الرقم', 'الاسم', 'السبب'], $problems);
        }

        if ($path = $this->option('excel')) {
            $this->export($excelRows, $path);
        }

        if (! $plan) {
            $this->warn('لا توجد سير قابلة للإرجاع.');
            return self::SUCCESS;
        }

        if (! $this->option('apply')) {
            $this->comment('عرض فقط — أعد التشغيل مع ‎--apply‎ للتنفيذ.');
            return self::SUCCESS;
        }

        $done = 0;

        foreach ($plan as $item) {
            /** @var Worker $worker */
            $worker = $item['worker'];

            $worker->update([
                'client_id'            => $item['client']->id,
                'status'               => 'reserved',
                'assigned_by_admin_id' => $item['reserver']?->id,
                'assigned_at'          => $item['at'],
            ]);

            $this->log($worker, $item['client']->name);
            $done++;
        }

        $this->info("تم إرجاع {$done} حجزاً.");

        return self::SUCCESS;
    }

    /** يكتب الصفوف في ملف إكسل داخل storage/app. */
    private function export(array $rows, string $path): void
    {
        if (! $rows) {
            $this->warn('لا صفوف للتصدير.');
            return;
        }

        if (! str_ends_with(strtolower($path), '.xlsx')) {
            $path .= '.xlsx';
        }

        \Maatwebsite\Excel\Facades\Excel::store(
            new \App\Exports\AutoReleasedWorkersExport($rows),
            $path
        );

        $this->info('تم التصدير إلى: ' . storage_path('app/' . $path));
    }

    /**
     * استخراج اسم العميل والحاجز من نصّ السجلّ.
     *
     * @return array{0: ?string, 1: ?string}
     */
    private function parse(string $label): array
    {
        $client   = null;
        $reserver = null;

        if (preg_match('/من العميل «(.+?)»/u', $label, $m)) {
            $client = trim($m[1]);
        }

        if (preg_match('/كان الحجز بواسطة (.+?) بتاريخ/u', $label, $m)) {
            $reserver = trim($m[1]);
        }

        return [$client, $reserver];
    }

    private function log(Worker $worker, string $clientName): void
    {
        try {
            WorkerActivityLog::create([
                'worker_id'   => $worker->id,
                'worker_name' => $worker->name,
                'admin_id'    => null,
                'admin_name'  => 'النظام',
                'action'      => 'assigned',
                'label'       => "إرجاع حجز فكّه النظام تلقائياً — أُعيد ربطها بالعميل «{$clientName}»",
                'ip_address'  => null,
            ]);
        } catch (\Throwable) {
            // لا نُعطّل الإرجاع بسبب فشل التسجيل
        }
    }
}
