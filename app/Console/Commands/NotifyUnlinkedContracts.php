<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Models\AdminNotification;
use App\Models\RecruitmentContract;
use Illuminate\Console\Command;

/**
 * تذكير يومي بالعقود القائمة بلا عاملة مرتبطة.
 *
 * ينشأ هذا الوضع عند إلغاء التأشيرة: يبقى العقد ويُفكّ ربط العاملة، فيحتاج
 * المنسّق إلى ربط عاملة بديلة. بلا تذكير قد يبقى العقد معلّقاً بلا متابعة.
 */
class NotifyUnlinkedContracts extends Command
{
    protected $signature   = 'contracts:notify-unlinked';
    protected $description = 'تذكير يومي بالعقود التي أُلغيت تأشيرتها وتنتظر عاملة بديلة';

    public function handle(): int
    {
        $contracts = RecruitmentContract::whereNull('worker_id')
            ->whereNotNull('visa_cancelled_at')
            ->where('active', true)
            ->with('client', 'branch')
            ->get();

        if ($contracts->isEmpty()) {
            $this->info('لا توجد عقود بلا عاملة.');
            return self::SUCCESS;
        }

        $notified = 0;

        foreach ($contracts as $contract) {
            $days = (int) $contract->visa_cancelled_at->diffInDays(now());

            $title = 'عقد بلا عاملة — بحاجة إلى بديلة';
            $body  = "العقد {$contract->contract_number} للعميل «"
                   . ($contract->client?->name ?? '—') . '» أُلغيت تأشيرته منذ '
                   . ($days > 0 ? "{$days} يوم" : 'اليوم')
                   . ' ولم تُربط به عاملة بديلة بعد.';
            $url   = route('admin.contracts.show', $contract->id);

            // قسم التنسيق في فرع العقد + مديرو الفرع + السوبر أدمن
            $admins = Admin::where('active', true)
                ->where(function ($q) use ($contract) {
                    $q->where(function ($q2) use ($contract) {
                        $q2->where('branch_id', $contract->branch_id)
                           ->whereIn('department', ['coordination', 'branch_manager']);
                    })->orWhereNull('branch_id');
                })
                ->get();

            foreach ($admins as $admin) {
                if ($this->alreadyNotifiedToday($admin->id, $url)) {
                    continue;
                }

                AdminNotification::create([
                    'admin_id' => $admin->id,
                    'type'     => 'contract_unlinked',
                    'title'    => $title,
                    'body'     => $body,
                    'url'      => $url,
                ]);

                $notified++;
            }
        }

        $this->info("عقود بلا عاملة: {$contracts->count()} — أُرسل {$notified} إشعاراً.");

        return self::SUCCESS;
    }

    /** يمنع تكرار الإشعار نفسه أكثر من مرة في اليوم. */
    private function alreadyNotifiedToday(int $adminId, string $url): bool
    {
        return AdminNotification::where('admin_id', $adminId)
            ->where('type', 'contract_unlinked')
            ->where('url', $url)
            ->whereDate('created_at', today())
            ->exists();
    }
}
