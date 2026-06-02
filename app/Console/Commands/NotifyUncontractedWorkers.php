<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Models\AdminNotification;
use App\Models\Worker;
use Illuminate\Console\Command;

/**
 * Runs daily. For every worker that is "assigned" to a client
 * but has no recruitment contract yet:
 *
 *  • Day 1–3 → daily reminder to the admin who assigned the worker.
 *  • Day 4+  → warning to the branch manager + auto-cancel the assignment.
 */
class NotifyUncontractedWorkers extends Command
{
    protected $signature   = 'workers:notify-uncontracted';
    protected $description = 'Remind assigners about workers with no contract; auto-cancel after 4 days';

    public function handle(): void
    {
        // Workers assigned to a client but with no recruitment contract
        $workers = Worker::where('active', true)
            ->where('status', 'assigned')
            ->whereNotNull('client_id')
            ->whereNotNull('assigned_at')
            ->doesntHave('latestContract')
            ->with(['branch', 'client', 'assignedBy'])
            ->get();

        if ($workers->isEmpty()) {
            $this->info('No uncontracted assigned workers found.');
            return;
        }

        $cancelled = 0;
        $reminded  = 0;

        foreach ($workers as $worker) {
            /** @var \Carbon\Carbon $assignedAt */
            $assignedAt = $worker->assigned_at;
            $daysElapsed = (int) $assignedAt->diffInDays(now());

            $workerName  = $worker->name      ?? '—';
            $clientName  = $worker->client?->name  ?? '—';
            $branchName  = $worker->branch?->name  ?? '—';
            $url         = route('admin.workers.show', $worker->id);

            if ($daysElapsed >= 4) {
                // ── Auto-cancel + warn branch manager ────────────────────────
                $worker->update([
                    'client_id'             => null,
                    'status'                => 'available',
                    'assigned_by_admin_id'  => null,
                    'assigned_at'           => null,
                ]);

                $title = 'إلغاء تلقائي لتعيين عاملة';
                $body  = "تم إلغاء تعيين العاملة «{$workerName}» للعميل «{$clientName}» (فرع {$branchName}) تلقائياً لمرور 4 أيام دون إنشاء عقد.";

                // Notify branch manager(s) of that branch
                $managers = Admin::where('active', true)
                    ->where(function ($q) use ($worker) {
                        $q->where('branch_id', $worker->branch_id)
                          ->where('department', 'branch_manager');
                    })
                    ->orWhere(function ($q) {
                        $q->whereNull('branch_id'); // super-admin
                    })
                    ->get();

                foreach ($managers as $manager) {
                    $this->upsertNotification(
                        $manager->id,
                        'worker_assignment_cancelled',
                        $title,
                        $body,
                        $url
                    );
                }

                $cancelled++;
            } else {
                // ── Daily reminder to the assigning admin ────────────────────
                if (! $worker->assigned_by_admin_id) {
                    continue;
                }

                $title = 'تذكير: عاملة معيَّنة بلا عقد';
                $body  = "العاملة «{$workerName}» معيَّنة للعميل «{$clientName}» منذ {$daysElapsed} " .
                         ($daysElapsed === 1 ? 'يوم' : 'أيام') .
                         " ولم يُنشأ لها عقد بعد.";

                $this->upsertNotification(
                    $worker->assigned_by_admin_id,
                    'worker_no_contract',
                    $title,
                    $body,
                    $url
                );

                // Day 3 → also warn the branch manager
                if ($daysElapsed === 3) {
                    $warnTitle = 'تحذير: عاملة معيَّنة منذ 3 أيام بلا عقد';
                    $warnBody  = "تحذير: مرّت 3 أيام على تعيين العاملة «{$workerName}» للعميل «{$clientName}» (فرع {$branchName}) دون إنشاء عقد. ستُلغى التعيين تلقائياً غداً.";

                    $managers = Admin::where('active', true)
                        ->where('branch_id', $worker->branch_id)
                        ->where('department', 'branch_manager')
                        ->get();

                    foreach ($managers as $manager) {
                        $this->upsertNotification(
                            $manager->id,
                            'worker_no_contract_warning',
                            $warnTitle,
                            $warnBody,
                            $url
                        );
                    }
                }

                $reminded++;
            }
        }

        $this->info("Reminded: {$reminded} worker(s). Auto-cancelled: {$cancelled} assignment(s).");
    }

    private function upsertNotification(
        int    $adminId,
        string $type,
        string $title,
        string $body,
        string $url
    ): void {
        $exists = AdminNotification::where('admin_id', $adminId)
            ->where('type', $type)
            ->where('url', $url)
            ->whereDate('created_at', today())
            ->exists();

        if (! $exists) {
            AdminNotification::create([
                'admin_id' => $adminId,
                'type'     => $type,
                'title'    => $title,
                'body'     => $body,
                'url'      => $url,
            ]);
        }
    }
}
