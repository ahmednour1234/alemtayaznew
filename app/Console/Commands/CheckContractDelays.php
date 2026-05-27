<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Models\AdminNotification;
use App\Models\RecruitmentContract;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckContractDelays extends Command
{
    protected $signature   = 'contracts:check-delays';
    protected $description = 'ترسل تنبيهات للعقود المتأخرة في مراحلها';

    public function handle(): int
    {
        $statuses = RecruitmentContract::statuses();
        $today    = Carbon::today();
        $sent     = 0;

        // Active contracts not in final statuses (13=received, 14=returned, 15=escaped)
        $contracts = RecruitmentContract::with(['statusHistories', 'branch'])
            ->whereNotIn('current_status', [13, 14, 15])
            ->where('active', true)
            ->get();

        foreach ($contracts as $contract) {
            $currentStatus = $contract->current_status;
            $expectedDays  = $statuses[$currentStatus]['days'] ?? null;

            // Skip statuses without a day limit
            if (! $expectedDays) {
                continue;
            }

            // Find when the current status was set
            $history = $contract->statusHistories
                ->where('status', $currentStatus)
                ->first();

            if (! $history || ! $history->status_date) {
                continue;
            }

            $daysInStatus = $history->status_date->diffInDays($today);

            // Only trigger when delay exceeds expected days by 2+ days
            if ($daysInStatus < $expectedDays + 2) {
                continue;
            }

            $delay = $daysInStatus - $expectedDays;

            $statusLabel = $statuses[$currentStatus]['label'];
            $title       = "⚠ تأخير عقد {$contract->contract_number}";
            $body        = "العقد متأخر في مرحلة \"{$statusLabel}\" بـ {$delay} يوم — الرجاء المراجعة الفورية";
            $url         = route('admin.contracts.show', $contract->id);

            // Recipients: branch_manager (same branch) + chairman (any branch) + super admins (no branch)
            $admins = Admin::where('active', true)
                ->where(function ($q) use ($contract) {
                    $q->where(function ($q2) use ($contract) {
                            // Branch manager of the same branch
                            $q2->where('branch_id', $contract->branch_id)
                               ->where('department', 'branch_manager');
                        })
                        ->orWhere('department', 'chairman')  // رئيس مجلس الإدارة (any branch)
                        ->orWhereNull('branch_id');           // مدير عام — super admins
                })
                ->pluck('id');

            // Check if we already sent today
            $alreadySent = AdminNotification::whereIn('admin_id', $admins)
                ->where('type', 'contracts.delay')
                ->where('url', $url)
                ->whereDate('created_at', $today)
                ->exists();

            if ($alreadySent) {
                continue;
            }

            $rows = $admins->map(fn($adminId) => [
                'admin_id'   => $adminId,
                'type'       => 'contracts.delay',
                'title'      => $title,
                'body'       => $body,
                'url'        => $url,
                'created_at' => now(),
                'updated_at' => now(),
            ])->toArray();

            AdminNotification::insert($rows);
            $sent++;

            $this->line("  تنبيه تأخير → {$contract->contract_number}: {$body}");
        }

        $this->info("✓ تم إرسال {$sent} تنبيه للعقود المتأخرة");

        return Command::SUCCESS;
    }
}
