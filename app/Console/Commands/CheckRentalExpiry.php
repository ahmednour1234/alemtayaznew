<?php

namespace App\Console\Commands;

use App\Models\HousingRental;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class CheckRentalExpiry extends Command
{
    protected $signature   = 'housing:check-rental-expiry';
    protected $description = 'Notify admins when a housing rental is expiring within 7 days or has already expired';

    public function __construct(private readonly NotificationService $notificationService)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $today  = now()->toDateString();
        $in7    = now()->addDays(7)->toDateString();

        // إيجارات تنتهي خلال 7 أيام
        $expiring = HousingRental::with(['worker', 'branch', 'assignment'])
            ->whereNull('deleted_at')
            ->whereNotNull('rent_end_date')
            ->whereBetween('rent_end_date', [$today, $in7])
            ->whereHas('assignment', fn($q) => $q->whereNull('check_out_date')) // لا تزال مقيمة
            ->get();

        // إيجارات انتهت بالفعل ولم يُسجَّل مغادرة
        $expired = HousingRental::with(['worker', 'branch', 'assignment'])
            ->whereNull('deleted_at')
            ->whereNotNull('rent_end_date')
            ->where('rent_end_date', '<', $today)
            ->whereHas('assignment', fn($q) => $q->whereNull('check_out_date'))
            ->get();

        $total = $expiring->count() + $expired->count();

        if ($total === 0) {
            $this->info('No rental expiry alerts.');
            return;
        }

        // ── تنبيهات الإيجارات التي توشك على الانتهاء ────────────────────────
        foreach ($expiring as $rental) {
            $workerName = $rental->worker?->name ?? '—';
            $branchName = $rental->branch?->name  ?? '—';
            $endDate    = $rental->rent_end_date->format('Y-m-d');
            $daysLeft   = now()->diffInDays($rental->rent_end_date);

            $title = 'إيجار يوشك على الانتهاء – ' . $branchName;
            $body  = "إيجار العاملة {$workerName} ينتهي في {$endDate} ({$daysLeft} يوم متبقٍ).";
            $url   = route('admin.housing-assignments.index', ['active' => '1']);

            $this->notificationService->notify(
                'housing_rental_expiry',
                $title,
                $body,
                $url,
                [$rental->branch_id]
            );
        }

        // ── تنبيهات الإيجارات المنتهية ────────────────────────────────────────
        foreach ($expired as $rental) {
            $workerName = $rental->worker?->name ?? '—';
            $branchName = $rental->branch?->name  ?? '—';
            $endDate    = $rental->rent_end_date->format('Y-m-d');
            $daysLate   = $rental->rent_end_date->diffInDays(now());

            $title = 'إيجار منتهٍ ولم تغادر العاملة – ' . $branchName;
            $body  = "انتهى إيجار العاملة {$workerName} في {$endDate} منذ {$daysLate} يوم ولم تُسجَّل مغادرة.";
            $url   = route('admin.housing-assignments.index', ['active' => '1']);

            $this->notificationService->notify(
                'housing_rental_expiry',
                $title,
                $body,
                $url,
                [$rental->branch_id]
            );
        }

        $this->info("Sent rental expiry notifications: {$expiring->count()} expiring, {$expired->count()} expired.");
    }
}
