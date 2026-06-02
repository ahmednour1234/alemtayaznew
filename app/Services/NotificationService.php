<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\AdminNotification;

class NotificationService
{
    /**
     * Permission slug required for each notification type.
     * null = superadmin only (system-level events).
     */
    private const SLUG_MAP = [
        'income_created'      => 'incomes.view',
        'expense_created'     => 'expenses.view',
        'expense_approved'    => 'expenses.view',
        'expense_rejected'    => 'expenses.view',
        'transfer_created'    => 'transfers.view',
        'transfer_approved'   => 'transfers.view',
        'transfer_rejected'   => 'transfers.view',
        'sponsorship_transfer_created'   => 'sponsorship-transfers.view',
        'sponsorship_transfer_forwarded' => 'sponsorship-transfers.view',
        'trip_created'        => 'trips.view',
        'trip_completed'      => 'trips.view',
        'trip_issue'          => 'trips.view',
        'contract_created'             => 'contracts.view',
        'contract_updated'             => 'contracts.view',
        'contract_deleted'             => 'contracts.view',
        'sponsorship_transfer_updated' => 'sponsorship-transfers.view',
        'sponsorship_transfer_deleted' => 'sponsorship-transfers.view',
        'housing_assignment_created'   => 'housing-assignments.view',
        'housing_assignment_overdue'   => 'housing-assignments.view',
        'branch_created'      => null,
        'admin_created'       => null,
        'client_created'      => 'clients.view',
        'agent_created'       => 'agents.view',
        'worker_cv_uploaded'         => 'workers.view',
        'worker_assigned'            => 'workers.view',
        'worker_unassigned'          => 'workers.view',
        'worker_no_contract'         => 'workers.view',
        'worker_no_contract_warning' => 'workers.view',
        'worker_assignment_cancelled'=> 'workers.view',
    ];

    /**
     * Send a notification to all eligible admins.
     *
     * @param array $branchIds  Branch IDs relevant to this event.
     *                          Empty = superadmin-only system event.
     *                          Filled = superadmins + branch admins of those branches.
     */
    public function notify(
        string $type,
        string $title,
        string $body,
        string $url,
        array  $branchIds = []
    ): void {
        $slug   = self::SLUG_MAP[$type] ?? null;
        $admins = Admin::where('active', true)->with('roles.permissions')->get();

        $records = [];
        $now     = now();

        foreach ($admins as $admin) {
            // Permission check (superadmins bypass)
            if (! $admin->isSuperAdmin()) {
                if ($slug === null) {
                    continue; // system event: superadmin only
                }
                if (! $admin->hasPermission($slug)) {
                    continue;
                }
            }

            // Branch scope check
            if ($admin->isBranchAdmin()) {
                if (empty($branchIds) || ! in_array($admin->branch_id, $branchIds, true)) {
                    continue;
                }
            }

            $records[] = [
                'admin_id'   => $admin->id,
                'type'       => $type,
                'title'      => $title,
                'body'       => $body,
                'url'        => $url,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (! empty($records)) {
            AdminNotification::insert($records);
        }
    }

    /**
     * Notify a single specific admin (e.g. the creator of an expense/transfer
     * when it gets approved or rejected).
     */
    public function notifyOne(int $adminId, string $type, string $title, string $body, string $url): void
    {
        AdminNotification::create([
            'admin_id' => $adminId,
            'type'     => $type,
            'title'    => $title,
            'body'     => $body,
            'url'      => $url,
        ]);
    }
}
