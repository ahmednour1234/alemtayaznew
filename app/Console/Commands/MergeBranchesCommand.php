<?php

namespace App\Console\Commands;

use App\Models\Branch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * دمج فرعين مكرّرين: ينقل كل البيانات من الفرع المصدر إلى الفرع الهدف
 * ثم يحذف المصدر حذفاً ناعماً (soft delete).
 *
 * مثال — دمج «حفر» داخل «حفر الباطن» وإعادة تسمية الهدف إلى «حفر الباطن»:
 *   php artisan branches:merge --from=45 --into=2 --rename="حفر الباطن" --dry-run
 *   php artisan branches:merge --from=45 --into=2 --rename="حفر الباطن"
 */
class MergeBranchesCommand extends Command
{
    protected $signature = 'branches:merge
                            {--from= : معرّف الفرع المصدر (سيُحذف حذفاً ناعماً)}
                            {--into= : معرّف الفرع الهدف (سيبقى)}
                            {--rename= : اسم جديد اختياري للفرع الهدف}
                            {--dry-run : عرض ما سيحدث دون تعديل أي بيانات}';

    protected $description = 'دمج فرع داخل فرع آخر: نقل كل البيانات ثم حذف المصدر حذفاً ناعماً';

    /**
     * كل الجداول والأعمدة التي تشير إلى branches.
     * @var array<string, string[]>
     */
    private const BRANCH_REFERENCES = [
        'incomes'                        => ['branch_id'],
        'expenses'                       => ['branch_id'],
        'financial_transfers'            => ['from_branch_id', 'to_branch_id'],
        'admins'                         => ['branch_id'],
        'clients'                        => ['branch_id'],
        'workers'                        => ['branch_id'],
        'recruitment_contracts'          => ['branch_id'],
        'campaigns'                      => ['branch_id'],
        'leads'                          => ['branch_id'],
        'housings'                       => ['branch_id'],
        'complaints'                     => ['branch_id'],
        'worker_housing_assignments'     => ['branch_id'],
        'trips'                          => ['branch_id'],
        'sponsorship_transfer_contracts' => ['branch_id'],
        'housing_visits'                 => ['branch_id'],
        'housing_rentals'                => ['branch_id'],
        'housing_settlements'            => ['branch_id'],
        'employees'                      => ['branch_id'],
        'employee_documents'             => ['branch_id'],
    ];

    public function handle(): int
    {
        $fromId = (int) $this->option('from');
        $intoId = (int) $this->option('into');
        $rename = $this->option('rename');
        $dryRun = (bool) $this->option('dry-run');

        if (! $fromId || ! $intoId) {
            $this->error('يجب تحديد --from و --into');
            return self::FAILURE;
        }

        if ($fromId === $intoId) {
            $this->error('لا يمكن دمج الفرع مع نفسه.');
            return self::FAILURE;
        }

        $from = Branch::withTrashed()->find($fromId);
        $into = Branch::withTrashed()->find($intoId);

        if (! $from) {
            $this->error("الفرع المصدر [{$fromId}] غير موجود.");
            return self::FAILURE;
        }

        if (! $into) {
            $this->error("الفرع الهدف [{$intoId}] غير موجود.");
            return self::FAILURE;
        }

        if ($into->trashed()) {
            $this->error("الفرع الهدف [{$intoId}] محذوف. اختر فرعاً فعّالاً.");
            return self::FAILURE;
        }

        $this->info("دمج: [{$from->id}] {$from->name} ({$from->code})  ←  داخل  →  [{$into->id}] {$into->name} ({$into->code})");
        $this->newLine();

        // ── إحصاء الصفوف المتأثرة ────────────────────────────────────────────
        $counts = [];
        $total  = 0;

        foreach (self::BRANCH_REFERENCES as $table => $columns) {
            if (! Schema::hasTable($table)) {
                $this->warn("تخطّي [{$table}] — الجدول غير موجود.");
                continue;
            }

            foreach ($columns as $column) {
                if (! Schema::hasColumn($table, $column)) {
                    $this->warn("تخطّي [{$table}.{$column}] — العمود غير موجود.");
                    continue;
                }

                $count = DB::table($table)->where($column, $fromId)->count();

                if ($count > 0) {
                    $counts[] = [$table, $column, $count];
                    $total += $count;
                }
            }
        }

        if ($counts) {
            $this->table(['الجدول', 'العمود', 'عدد الصفوف'], $counts);
        }

        $this->info("إجمالي الصفوف التي ستُنقل: {$total}");

        if ($rename) {
            $this->info("سيُعاد تسمية الفرع الهدف إلى: «{$rename}»");
        }

        $this->info("سيُحذف الفرع المصدر [{$from->id}] حذفاً ناعماً (يمكن استرجاعه).");

        if ($dryRun) {
            $this->newLine();
            $this->comment('— وضع المعاينة (--dry-run): لم يتم تعديل أي بيانات. —');
            return self::SUCCESS;
        }

        if (! $this->confirm('هل تريد المتابعة وتنفيذ الدمج؟', false)) {
            $this->comment('تم الإلغاء.');
            return self::SUCCESS;
        }

        // ── التنفيذ داخل معاملة واحدة ────────────────────────────────────────
        try {
            DB::transaction(function () use ($fromId, $intoId, $from, $into, $rename) {
                foreach (self::BRANCH_REFERENCES as $table => $columns) {
                    if (! Schema::hasTable($table)) {
                        continue;
                    }

                    foreach ($columns as $column) {
                        if (! Schema::hasColumn($table, $column)) {
                            continue;
                        }

                        DB::table($table)->where($column, $fromId)->update([$column => $intoId]);
                    }
                }

                // منع تحويل مبلغ من الفرع إلى نفسه بعد الدمج
                if (Schema::hasTable('financial_transfers')) {
                    $selfTransfers = DB::table('financial_transfers')
                        ->whereColumn('from_branch_id', 'to_branch_id')
                        ->where('from_branch_id', $intoId)
                        ->count();

                    if ($selfTransfers > 0) {
                        $this->warn("تنبيه: يوجد {$selfTransfers} تحويل مالي أصبح من الفرع إلى نفسه بعد الدمج — راجعها يدوياً.");
                    }
                }

                if ($rename) {
                    $into->name = $rename;
                    $into->save();
                }

                $from->delete(); // soft delete
            });
        } catch (\Throwable $e) {
            $this->error('فشل الدمج، وتم التراجع عن كل التغييرات: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->newLine();
        $this->info("تم الدمج بنجاح. نُقل {$total} صف إلى الفرع [{$into->id}] «{$into->name}».");
        $this->info("الفرع [{$from->id}] «{$from->name}» محذوف حذفاً ناعماً — يمكن استرجاعه عند الحاجة.");

        return self::SUCCESS;
    }
}
