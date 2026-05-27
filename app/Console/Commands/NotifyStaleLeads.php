<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Models\AdminNotification;
use App\Models\Lead;
use Illuminate\Console\Command;

class NotifyStaleLeads extends Command
{
    protected $signature   = 'leads:notify-stale';
    protected $description = 'Send notifications for leads not contacted within 24 hours';

    public function handle(): void
    {
        // Leads in "new" status for more than 24 hours with no contact
        $stale = Lead::where('status', 'new')
            ->where('created_at', '<=', now()->subDay())
            ->whereNull('last_contacted_at')
            ->with('branch', 'campaign')
            ->get();

        if ($stale->isEmpty()) return;

        // Group by branch
        $grouped = $stale->groupBy('branch_id');

        foreach ($grouped as $branchId => $leads) {
            $count    = $leads->count();
            $branch   = $leads->first()->branch;
            $bLabel   = $branch?->name ?? 'غير محدد';
            $body     = "يوجد {$count} عميل محتمل في فرع {$bLabel} لم يتم التواصل معهم منذ أكثر من 24 ساعة";
            $url      = route('admin.marketing.leads.index', ['branch_id' => $branchId, 'status' => 'new']);

            // Notify branch admins + superadmins
            $recipients = Admin::where('active', true)
                ->where(fn($q) => $q
                    ->whereNull('branch_id')                        // super admin
                    ->orWhere('branch_id', $branchId)              // branch admin
                    ->orWhereIn('department', ['chairman', 'branch_manager'])
                )->get();

            foreach ($recipients as $admin) {
                // Avoid duplicate notifications (one per day per branch per admin)
                $exists = AdminNotification::where('admin_id', $admin->id)
                    ->where('type', 'stale_lead')
                    ->where('url', $url)
                    ->whereDate('created_at', today())
                    ->exists();

                if (! $exists) {
                    AdminNotification::create([
                        'admin_id' => $admin->id,
                        'title'    => 'تأخر في متابعة العملاء المحتملين',
                        'body'     => $body,
                        'type'     => 'stale_lead',
                        'url'      => $url,
                    ]);
                }
            }
        }

        $this->info('Stale lead notifications sent.');
    }
}
