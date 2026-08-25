<?php

namespace App\Console\Commands;

use App\Models\Worker;
use Illuminate\Console\Command;

/**
 * إحصاء العاملات المتاحات ولهنّ رقم تأشيرة.
 *
 * رقم التأشيرة قد يكون على العاملة نفسها (workers.visa_number) أو على
 * عقدها (recruitment_contracts.visa_number)، فنعرض المصدرين منفصلين
 * ومجتمعين — لأن الفرق بينهما يكشف بيانات ناقصة لا مجرد اختلاف تخزين.
 */
class CountAvailableWithVisa extends Command
{
    protected $signature = 'workers:count-visa
                            {--list : عرض قائمة العاملات لا العدد فقط}
                            {--status=available : الحالة المطلوبة، أو all لكل الحالات}';

    protected $description = 'عدد العاملات اللاتي لهنّ رقم تأشيرة حسب الحالة';

    public function handle(): int
    {
        $status = $this->option('status');

        $scope = fn () => Worker::query()
            ->when($status !== 'all', fn ($q) => $q->where('status', $status));

        // رقم التأشيرة على صف العاملة
        $onWorker = (clone $scope())
            ->whereNotNull('visa_number')
            ->where('visa_number', '!=', '')
            ->count();

        // رقم التأشيرة على عقد العاملة
        $onContract = (clone $scope())
            ->whereHas('recruitmentContracts', fn ($q) => $q
                ->whereNotNull('visa_number')->where('visa_number', '!=', ''))
            ->count();

        // أي من المصدرين
        $either = (clone $scope())
            ->where(fn ($q) => $q
                ->where(fn ($w) => $w->whereNotNull('visa_number')->where('visa_number', '!=', ''))
                ->orWhereHas('recruitmentContracts', fn ($c) => $c
                    ->whereNotNull('visa_number')->where('visa_number', '!=', '')))
            ->count();

        $total = (clone $scope())->count();

        $label = $status === 'all' ? 'كل الحالات' : (Worker::statusOptions()[$status] ?? $status);

        $this->newLine();
        $this->info("العاملات — الحالة: {$label}");
        $this->table(
            ['البيان', 'العدد'],
            [
                ['إجمالي العاملات في هذه الحالة', $total],
                ['لهنّ رقم تأشيرة (على صف العاملة)', $onWorker],
                ['لهنّ رقم تأشيرة (على العقد)', $onContract],
                ['لهنّ رقم تأشيرة (من أي مصدر)', $either],
                ['بلا رقم تأشيرة', $total - $either],
            ]
        );

        if ($this->option('list') && $either > 0) {
            $rows = (clone $scope())
                ->with(['nationality:id,name', 'latestContract'])
                ->where(fn ($q) => $q
                    ->where(fn ($w) => $w->whereNotNull('visa_number')->where('visa_number', '!=', ''))
                    ->orWhereHas('recruitmentContracts', fn ($c) => $c
                        ->whereNotNull('visa_number')->where('visa_number', '!=', '')))
                ->orderBy('id')
                ->get(['id', 'name', 'status', 'visa_number', 'nationality_id']);

            $this->newLine();
            $this->table(
                ['#', 'الاسم', 'الجنسية', 'تأشيرة العاملة', 'تأشيرة العقد', 'رقم العقد'],
                $rows->map(fn (Worker $w) => [
                    $w->id,
                    mb_substr($w->name ?? '—', 0, 28),
                    $w->nationality?->name ?? '—',
                    $w->visa_number ?: '—',
                    $w->latestContract?->visa_number ?: '—',
                    $w->latestContract?->contract_number ?: '—',
                ])->all()
            );
        }

        // تنبيه على التعارض: تأشيرة على العقد ولا شيء على العاملة
        $gap = $onContract - (clone $scope())
            ->whereNotNull('visa_number')->where('visa_number', '!=', '')
            ->whereHas('recruitmentContracts', fn ($q) => $q
                ->whereNotNull('visa_number')->where('visa_number', '!=', ''))
            ->count();

        if ($gap > 0) {
            $this->newLine();
            $this->warn("{$gap} عاملة رقم تأشيرتها مسجّل على العقد فقط دون صف العاملة.");
        }

        return self::SUCCESS;
    }
}
