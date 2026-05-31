<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Models\AdminNotification;
use App\Models\HousingAssignment;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class CheckOverdueHousingAssignments extends Command
{
    protected $signature   = 'housing:check-overdue';
    protected $description = 'Send notifications for housing assignments past their expected checkout date';

    public function __construct(private readonly NotificationService $notificationService)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $today = now()->toDateString();

        // Active assignments whose expected_check_out_date has passed
        $overdue = HousingAssignment::with(['worker', 'branch'])
            ->whereNull('check_out_date')
            ->whereNotNull('expected_check_out_date')
            ->whereDate('expected_check_out_date', '<', $today)
            ->get();

        if ($overdue->isEmpty()) {
            $this->info('No overdue housing assignments.');
            return;
        }

        foreach ($overdue as $assignment) {
            $workerName  = $assignment->worker?->name ?? '—';
            $branchName  = $assignment->branch?->name  ?? '—';
            $expected    = $assignment->expected_check_out_date->format('Y-m-d');
            $daysLate    = $assignment->expected_check_out_date->diffInDays(now());

            $title = 'تأخر مغادرة عاملة من السكن';
            $body  = "العاملة {$workerName} ({$branchName}) تأخرت {$daysLate} يوم عن موعد المغادرة المتوقع ({$expected}).";
            $url   = route('admin.housing-assignments.index');

            // Super-admins + branch admins of that branch
            $this->notificationService->notify(
                'housing_assignment_overdue',
                $title,
                $body,
                $url,
                [$assignment->branch_id]
            );

            // Chairmen not already covered as super-admins
            Admin::where('active', true)
                ->where('department', 'chairman')
                ->whereDoesntHave('roles', fn($q) => $q->where('slug', 'super-admin'))
                ->each(fn($admin) => AdminNotification::create([
                    'admin_id' => $admin->id,
                    'type'     => 'housing_assignment_overdue',
                    'title'    => $title,
                    'body'     => $body,
                    'url'      => $url,
                ]));
        }

        $this->info("Sent overdue notifications for {$overdue->count()} assignment(s).");
    }
}
