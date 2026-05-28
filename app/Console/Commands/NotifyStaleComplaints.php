<?php

namespace App\Console\Commands;

use App\Services\ComplaintService;
use Illuminate\Console\Command;

class NotifyStaleComplaints extends Command
{
    protected $signature   = 'complaints:notify-stale {--days=7}';
    protected $description = 'Send notifications for complaints still open after N days (default 7)';

    public function handle(ComplaintService $service): int
    {
        $days  = (int) $this->option('days');
        $count = $service->notifyStale($days);
        $this->info("Stale notifications sent for {$count} complaint(s).");
        return self::SUCCESS;
    }
}
