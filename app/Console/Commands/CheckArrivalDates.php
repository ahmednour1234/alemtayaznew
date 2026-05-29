<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Models\AdminNotification;
use App\Models\RecruitmentContract;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckArrivalDates extends Command
{
    protected $signature   = 'contracts:check-arrivals';
    protected $description = 'تُرسل تنبيهات للعقود التي اقتربت من ميعاد الوصول أو تأخرت عن الاستلام';

    public function handle(): int
    {
        $today    = Carbon::today();
        $tomorrow = Carbon::tomorrow();
        $sent     = 0;

        // ── 1. Contracts with arrival_date = TOMORROW (status 12 = معاد الوصول) ──
        $soonContracts = RecruitmentContract::with(['worker', 'branch'])
            ->where('current_status', 12)
            ->where('active', true)
            ->whereDate('arrival_date', $tomorrow)
            ->get();

        foreach ($soonContracts as $contract) {
            $workerName = $contract->worker?->name ?? '—';
            $title      = "📅 وصول عاملة غداً";
            $body       = "العاملة \"{$workerName}\" مقررة غداً — العقد رقم {$contract->contract_number}";
            $url        = route('admin.contracts.show', $contract->id);

            if ($this->alreadySentToday('arrival.due', $url)) {
                continue;
            }

            $this->sendNotification('arrival.due', $title, $body, $url, $contract->branch_id);
            $sent++;
            $this->line("  [غداً] {$contract->contract_number}: {$workerName}");
        }

        // ── 2. Contracts with arrival_date PASSED but still at status 12 (overdue) ──
        $overdueContracts = RecruitmentContract::with(['worker', 'branch'])
            ->where('current_status', 12)
            ->where('active', true)
            ->whereNotNull('arrival_date')
            ->whereDate('arrival_date', '<', $today)
            ->get();

        foreach ($overdueContracts as $contract) {
            $workerName  = $contract->worker?->name ?? '—';
            $daysOverdue = (int) $contract->arrival_date->diffInDays($today);
            $title       = "⚠ عاملة لم تُستلم في الميعاد";
            $body        = "العاملة \"{$workerName}\" متأخرة {$daysOverdue} يوم عن ميعاد الوصول — العقد {$contract->contract_number}";
            $url         = route('admin.contracts.show', $contract->id);

            if ($this->alreadySentToday('arrival.overdue', $url)) {
                continue;
            }

            $this->sendNotification('arrival.overdue', $title, $body, $url, $contract->branch_id);
            $sent++;
            $this->line("  [متأخر {$daysOverdue}ي] {$contract->contract_number}: {$workerName}");
        }

        $this->info("✓ تم إرسال {$sent} تنبيه وصول");
        return self::SUCCESS;
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function alreadySentToday(string $type, string $url): bool
    {
        return AdminNotification::where('type', $type)
            ->where('url', $url)
            ->whereDate('created_at', Carbon::today())
            ->exists();
    }

    private function sendNotification(string $type, string $title, string $body, string $url, ?int $branchId): void
    {
        $admins = Admin::where('active', true)
            ->where(function ($q) use ($branchId) {
                $q->where(function ($q2) use ($branchId) {
                        $q2->where('branch_id', $branchId)
                           ->whereIn('department', ['branch_manager', 'coordination', 'chairman']);
                    })
                    ->orWhere('department', 'chairman')
                    ->orWhereNull('branch_id');
            })
            ->pluck('id');

        $rows = $admins->map(fn($adminId) => [
            'admin_id'   => $adminId,
            'type'       => $type,
            'title'      => $title,
            'body'       => $body,
            'url'        => $url,
            'created_at' => now(),
            'updated_at' => now(),
        ])->toArray();

        if (! empty($rows)) {
            AdminNotification::insert($rows);
        }
    }
}
