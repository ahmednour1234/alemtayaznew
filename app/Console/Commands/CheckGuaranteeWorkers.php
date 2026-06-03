<?php

namespace App\Console\Commands;

use App\Models\HousingAssignment;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class CheckGuaranteeWorkers extends Command
{
    protected $signature   = 'housing:check-guarantee';
    protected $description = 'Notify branch managers, general managers, and housing officials about workers in their guarantee period (arrival within 90 days) who are currently in housing';

    public function __construct(private readonly NotificationService $notificationService)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        // عاملات مقيمات حالياً في السكن لديهن عقد استقدام وصل خلال آخر 90 يوم
        $assignments = HousingAssignment::with(['worker.recruitmentContracts', 'branch'])
            ->whereNull('check_out_date')
            ->whereHas('worker.recruitmentContracts', function ($q) {
                $q->whereNotNull('arrival_date')
                  ->where('arrival_date', '>=', now()->subDays(90));
            })
            ->get();

        if ($assignments->isEmpty()) {
            $this->info('No workers in guarantee period.');
            return;
        }

        // Group by branch to send one notification per branch
        $byBranch = $assignments->groupBy('branch_id');

        foreach ($byBranch as $branchId => $group) {
            $branchName = $group->first()->branch?->name ?? '—';
            $count      = $group->count();
            $names      = $group->take(3)->map(fn($a) => $a->worker?->name ?? '—')->join('، ');

            $title = 'عاملات في فترة الضمان – ' . $branchName;
            $body  = "يوجد {$count} عاملة في فترة الضمان (وصلن خلال 90 يوماً) في السكن: {$names}" . ($count > 3 ? ' وأخريات...' : '');
            $url   = route('admin.housing-assignments.index', ['worker_status' => 'normal', 'active' => '1']);

            $this->notificationService->notify(
                'housing_guarantee_period',
                $title,
                $body,
                $url,
                [$branchId]
            );
        }

        $this->info("Sent guarantee period notifications for {$assignments->count()} worker(s) in {$byBranch->count()} branch(es).");
    }
}
