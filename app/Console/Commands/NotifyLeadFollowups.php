<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Models\AdminNotification;
use App\Models\LeadCallLog;
use Illuminate\Console\Command;

class NotifyLeadFollowups extends Command
{
    protected $signature   = 'leads:notify-followups';
    protected $description = 'Notify CS staff about lead follow-ups due today or overdue';

    public function handle(): void
    {
        // Follow-ups due in the next hour OR already overdue (up to 24 hours ago)
        $dueLogs = LeadCallLog::with(['lead', 'lead.assignedAdmin'])
            ->where('status', 'need_followup')
            ->whereBetween('follow_up_at', [now()->subDay(), now()->addHour()])
            ->whereHas('lead', fn($q) => $q->whereIn('status', ['new', 'in_progress']))
            ->get();

        $notified = 0;

        foreach ($dueLogs as $log) {
            $lead          = $log->lead;
            $assignedAdmin = $lead?->assignedAdmin;

            if (! $assignedAdmin) continue;

            $followupTime = \Carbon\Carbon::parse($log->follow_up_at)->format('Y-m-d H:i');
            $url          = route('admin.marketing.leads.show', $lead);

            $exists = AdminNotification::where('admin_id', $assignedAdmin->id)
                ->where('type', 'lead_followup_due')
                ->where('url', $url)
                ->whereDate('created_at', today())
                ->exists();

            if (! $exists) {
                AdminNotification::create([
                    'admin_id' => $assignedAdmin->id,
                    'type'     => 'lead_followup_due',
                    'title'    => 'موعد متابعة عميل محتمل',
                    'body'     => 'حان موعد متابعة العميل "' . $lead->name . '" المقرر في ' . $followupTime,
                    'url'      => $url,
                ]);
                $notified++;
            }
        }

        $this->info("Sent {$notified} follow-up reminder notifications.");
    }
}
