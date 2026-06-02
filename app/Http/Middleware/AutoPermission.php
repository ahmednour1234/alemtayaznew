<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AutoPermission
{
    /**
     * Route name  →  required permission slug.
     * null  = no permission required (public within auth group).
     */
    private const MAP = [
        // ── Dashboard ────────────────────────────────
        'admin.dashboard'             => null,

        // Notifications — every authenticated admin can see their own
        'admin.notifications.index'   => null,
        'admin.notifications.read'    => null,
        'admin.notifications.read-all'=> null,

        'admin.logout'     => null,
        'admin.cities.index' => null,

        // ── Branches ─────────────────────────────────
        'admin.branches.index'   => 'branches.view',
        'admin.branches.trashed' => 'branches.view',
        'admin.branches.create'  => 'branches.create',
        'admin.branches.store'   => 'branches.create',
        'admin.branches.edit'    => 'branches.edit',
        'admin.branches.update'  => 'branches.edit',
        'admin.branches.destroy' => 'branches.delete',
        'admin.branches.toggle'  => 'branches.toggle',
        'admin.branches.restore' => 'branches.restore',

        // ── Nationalities ─────────────────────────────
        'admin.nationalities.index'   => 'nationalities.view',
        'admin.nationalities.create'  => 'nationalities.create',
        'admin.nationalities.store'   => 'nationalities.create',
        'admin.nationalities.edit'    => 'nationalities.edit',
        'admin.nationalities.update'  => 'nationalities.edit',
        'admin.nationalities.destroy' => 'nationalities.delete',
        'admin.nationalities.toggle'  => 'nationalities.toggle',
        'admin.nationalities.restore' => 'nationalities.restore',

        // ── Airports ──────────────────────────────────
        'admin.airports.index'   => 'airports.view',
        'admin.airports.create'  => 'airports.create',
        'admin.airports.store'   => 'airports.create',
        'admin.airports.edit'    => 'airports.edit',
        'admin.airports.update'  => 'airports.edit',
        'admin.airports.destroy' => 'airports.delete',
        'admin.airports.toggle'  => 'airports.toggle',
        'admin.airports.restore' => 'airports.restore',

        // ── Income Types ──────────────────────────────
        'admin.income-types.index'   => 'income-types.view',
        'admin.income-types.create'  => 'income-types.create',
        'admin.income-types.store'   => 'income-types.create',
        'admin.income-types.edit'    => 'income-types.edit',
        'admin.income-types.update'  => 'income-types.edit',
        'admin.income-types.destroy' => 'income-types.delete',
        'admin.income-types.toggle'  => 'income-types.toggle',
        'admin.income-types.restore' => 'income-types.restore',

        // ── Expense Types ─────────────────────────────
        'admin.expense-types.index'   => 'expense-types.view',
        'admin.expense-types.create'  => 'expense-types.create',
        'admin.expense-types.store'   => 'expense-types.create',
        'admin.expense-types.edit'    => 'expense-types.edit',
        'admin.expense-types.update'  => 'expense-types.edit',
        'admin.expense-types.destroy' => 'expense-types.delete',
        'admin.expense-types.toggle'  => 'expense-types.toggle',
        'admin.expense-types.restore' => 'expense-types.restore',

        // ── Incomes ───────────────────────────────────
        'admin.incomes.index'    => 'incomes.view',
        'admin.incomes.show'     => 'incomes.view',
        'admin.incomes.create'   => 'incomes.create',
        'admin.incomes.store'    => 'incomes.create',
        'admin.incomes.edit'     => 'incomes.edit',
        'admin.incomes.update'   => 'incomes.edit',
        'admin.incomes.destroy'  => 'incomes.delete',
        'admin.incomes.export'   => 'incomes.export',
        'admin.incomes.template' => 'incomes.import',
        'admin.incomes.import'   => 'incomes.import',
        'admin.incomes.restore'  => 'incomes.restore',

        // ── Expenses ──────────────────────────────────
        'admin.expenses.index'    => 'expenses.view',
        'admin.expenses.show'     => 'expenses.view',
        'admin.expenses.create'   => 'expenses.create',
        'admin.expenses.store'    => 'expenses.create',
        'admin.expenses.edit'     => 'expenses.edit',
        'admin.expenses.update'   => 'expenses.edit',
        'admin.expenses.destroy'  => 'expenses.delete',
        'admin.expenses.approve'  => 'expenses.approve',
        'admin.expenses.reject'   => 'expenses.reject',
        'admin.expenses.export'   => 'expenses.export',
        'admin.expenses.template' => 'expenses.import',
        'admin.expenses.import'   => 'expenses.import',
        'admin.expenses.restore'  => 'expenses.restore',

        // ── Transfers ─────────────────────────────────
        'admin.transfers.index'   => 'transfers.view',
        'admin.transfers.show'    => 'transfers.view',
        'admin.transfers.create'  => 'transfers.create',
        'admin.transfers.store'   => 'transfers.create',
        'admin.transfers.edit'    => 'transfers.edit',
        'admin.transfers.update'  => 'transfers.edit',
        'admin.transfers.destroy' => 'transfers.delete',
        'admin.transfers.approve' => 'transfers.approve',
        'admin.transfers.reject'  => 'transfers.reject',
        'admin.transfers.restore' => 'transfers.restore',

        // ── Reports ───────────────────────────────────
        'admin.reports.branch-statement'         => 'reports.branch-statement',
        'admin.reports.branch-statement.export'  => 'reports.branch-statement.export',
        'admin.reports.income-statement'         => 'reports.income-statement',
        'admin.reports.income-statement.export'  => 'reports.income-statement.export',
        'admin.reports.contracts-stats'          => 'reports.contracts-stats',
        'admin.reports.contracts-received'       => 'reports.contracts-received',
        'admin.reports.contracts-delayed'        => 'reports.contracts-delayed',

        // ── Settings: Admins ──────────────────────────
        'admin.settings.admins.index'   => 'admins.manage',
        'admin.settings.admins.create'  => 'admins.manage',
        'admin.settings.admins.store'   => 'admins.manage',
        'admin.settings.admins.edit'    => 'admins.manage',
        'admin.settings.admins.update'  => 'admins.manage',
        'admin.settings.admins.destroy' => 'admins.manage',
        'admin.settings.admins.toggle'  => 'admins.toggle',
        'admin.settings.admins.restore' => 'admins.restore',

        // ── Settings: Roles ───────────────────────────
        'admin.settings.roles.index'   => 'roles.manage',
        'admin.settings.roles.create'  => 'roles.manage',
        'admin.settings.roles.store'   => 'roles.manage',
        'admin.settings.roles.edit'    => 'roles.manage',
        'admin.settings.roles.update'  => 'roles.manage',
        'admin.settings.roles.destroy' => 'roles.manage',

        // ── Settings: Permissions ─────────────────────
        'admin.settings.permissions.index' => 'roles.manage',

        // ── Clients ───────────────────────────────────
        'admin.clients.index'       => 'clients.view',
        'admin.clients.show'        => 'clients.view',
        'admin.clients.create'      => 'clients.create',
        'admin.clients.store'       => 'clients.create',
        'admin.clients.quick-store' => null,   // any authenticated admin can quick-add a client from modal
        'admin.clients.edit'        => 'clients.edit',
        'admin.clients.update'      => 'clients.edit',
        'admin.clients.destroy'     => 'clients.delete',
        'admin.clients.restore'     => 'clients.delete',

        // ── Agents ────────────────────────────────────
        'admin.agents.index'   => 'agents.view',
        'admin.agents.show'    => 'agents.view',
        'admin.agents.create'  => 'agents.create',
        'admin.agents.store'   => 'agents.create',
        'admin.agents.edit'    => 'agents.edit',
        'admin.agents.update'  => 'agents.edit',
        'admin.agents.destroy' => 'agents.delete',
        'admin.agents.restore' => 'agents.delete',
        // ── Workers ───────────────────────────────────────────────────────────
        'admin.workers.index'         => 'workers.view',
        'admin.workers.show'          => 'workers.view',
        'admin.workers.cv'            => 'workers.view',
        'admin.workers.passport'      => 'workers.view',
        'admin.workers.create'        => 'workers.create',
        'admin.workers.store'         => 'workers.create',
        'admin.workers.bulk'          => 'workers.create',
        'admin.workers.bulk-store'    => 'workers.create',
        'admin.workers.quick-store'   => 'workers.create',
        'admin.workers.edit'          => 'workers.edit',
        'admin.workers.update'        => 'workers.edit',
        'admin.workers.destroy'       => 'workers.delete',
        'admin.workers.restore'       => 'workers.delete',
        'admin.workers.assign'        => 'workers.assign',
        'admin.workers.do-assign'     => 'workers.assign',
        'admin.workers.unassign'      => 'workers.assign',
        'admin.workers.send-whatsapp' => 'workers.view',

        // ── Contracts ─────────────────────────────────────────────────────────
        'admin.contracts.index'         => 'contracts.view',
        'admin.contracts.show'          => 'contracts.view',
        'admin.contracts.print'         => 'contracts.view',
        'admin.contracts.export'        => 'contracts.view',
        'admin.contracts.trashed'       => 'contracts.view',
        'admin.contracts.create'        => 'contracts.create',
        'admin.contracts.store'         => 'contracts.create',
        'admin.contracts.template'      => 'contracts.create',
        'admin.contracts.import'        => 'contracts.create',
        'admin.contracts.edit'          => 'contracts.edit',
        'admin.contracts.update'        => 'contracts.edit',
        'admin.contracts.update-status' => 'contracts.edit',
        'admin.contracts.restore'       => 'contracts.edit',
        'admin.contracts.destroy'       => 'contracts.delete',
        'admin.contracts.force-delete'  => 'contracts.delete',

        // ── Marketing: Campaigns ──────────────────────────────────────────────
        'admin.marketing.campaigns.index'         => 'campaigns.view',
        'admin.marketing.campaigns.show'          => 'campaigns.view',
        'admin.marketing.campaigns.create'        => 'campaigns.create',
        'admin.marketing.campaigns.store'         => 'campaigns.create',
        'admin.marketing.campaigns.edit'          => 'campaigns.edit',
        'admin.marketing.campaigns.update'        => 'campaigns.edit',
        'admin.marketing.campaigns.destroy'       => 'campaigns.delete',
        'admin.marketing.campaigns.import-sheet'      => 'campaigns.import',
        'admin.marketing.campaigns.reassign-unassigned' => 'leads.create',

        // ── Marketing: Leads ──────────────────────────────────────────────────
        'admin.marketing.leads.index'   => 'leads.view',
        'admin.marketing.leads.show'    => 'leads.view',
        'admin.marketing.leads.store'   => 'leads.create',
        'admin.marketing.leads.update'  => 'leads.edit',
        'admin.marketing.leads.destroy' => 'leads.delete',
        'admin.marketing.leads.call'    => 'leads.call',
        'admin.marketing.leads.convert' => 'leads.convert',

        // ── Marketing: Reports ────────────────────────────────────────────────
        'admin.marketing.reports'            => 'marketing.reports.view',
        'admin.marketing.staff-performance'  => 'marketing.reports.view',
        'admin.marketing.leads-board'        => 'leads.view',
        'admin.marketing.leads-board.auto-assign' => 'leads.create',
        // ── Housing (السكن) ──────────────────────────────────────
        'admin.housings.index'   => 'housings.view',
        'admin.housings.create'  => 'housings.create',
        'admin.housings.store'   => 'housings.create',
        'admin.housings.edit'    => 'housings.edit',
        'admin.housings.update'  => 'housings.edit',
        'admin.housings.destroy' => 'housings.delete',
        'admin.housings.toggle'  => 'housings.toggle',
        'admin.housings.restore' => 'housings.restore',

        // ── Complaints (الشكاوي) ───────────────────────────────
        'admin.complaints.index'   => 'complaints.view',
        'admin.complaints.show'    => 'complaints.view',
        'admin.complaints.create'  => 'complaints.create',
        'admin.complaints.store'   => 'complaints.create',
        'admin.complaints.edit'    => 'complaints.edit',
        'admin.complaints.update'  => 'complaints.edit',
        'admin.complaints.destroy' => 'complaints.delete',
        'admin.complaints.restore' => 'complaints.restore',
        'admin.complaints.reports' => 'complaints.reports',
        'admin.complaints.attachments.destroy' => 'complaints.edit',

        // ── Housing Assignments (تعيينات السكن) ──────────────
        'admin.housing-assignments.index'    => 'housing-assignments.view',
        'admin.housing-assignments.create'   => 'housing-assignments.create',
        'admin.housing-assignments.store'    => 'housing-assignments.create',
        'admin.housing-assignments.checkout' => 'housing-assignments.edit',
        'admin.housing-assignments.destroy'  => 'housing-assignments.delete',

        // ── Trips (الرحلات والنقل) ────────────────────────────
        'admin.trips.index'        => 'trips.view',
        'admin.trips.show'         => 'trips.view',
        'admin.trips.print'        => 'trips.view',
        'admin.trips.create'       => 'trips.create',
        'admin.trips.store'        => 'trips.create',
        'admin.trips.edit'         => 'trips.edit',
        'admin.trips.update'       => 'trips.edit',
        'admin.trips.complete'     => 'trips.edit',
        'admin.trips.add-worker'   => 'trips.edit',
        'admin.trips.remove-worker'=> 'trips.edit',
        'admin.trips.destroy'      => 'trips.delete',

        // ── Sponsorship Transfers (عقود نقل الكفالة) ─────────
        'admin.sponsorship-transfers.index'         => 'sponsorship-transfers.view',
        'admin.sponsorship-transfers.show'          => 'sponsorship-transfers.view',
        'admin.sponsorship-transfers.print'         => 'sponsorship-transfers.view',
        'admin.sponsorship-transfers.create'        => 'sponsorship-transfers.create',
        'admin.sponsorship-transfers.store'         => 'sponsorship-transfers.create',
        'admin.sponsorship-transfers.edit'          => 'sponsorship-transfers.edit',
        'admin.sponsorship-transfers.update'        => 'sponsorship-transfers.edit',
        'admin.sponsorship-transfers.update-status' => 'sponsorship-transfers.edit',
        'admin.sponsorship-transfers.destroy'       => 'sponsorship-transfers.delete',

        // ── Calendar (التقويم) ────────────────────────────────
        'admin.calendar.index'  => 'calendar.view',
        'admin.calendar.events' => 'calendar.view',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()?->getName();

        // Route not in map → allow (safety fallback)
        if (! array_key_exists($routeName, self::MAP)) {
            return $next($request);
        }

        $required = self::MAP[$routeName];

        // null permission = accessible to all authenticated admins
        if ($required === null) {
            return $next($request);
        }

        $admin = Auth::guard('admin')->user();

        $hasPermission = $admin && ($admin->isSuperAdmin() || (
            is_array($required)
                ? collect($required)->contains(fn($p) => $admin->hasPermission($p))
                : $admin->hasPermission($required)
        ));

        if (! $hasPermission) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'ليس لديك صلاحية لهذا الإجراء.'], 403);
            }
            abort(403, 'ليس لديك صلاحية للوصول إلى هذه الصفحة.');
        }

        // Extra check: contracts.create is restricted to customer_service department only.
        // Accounts and coordination departments process existing contracts — they cannot create new ones,
        // even if their role has the contracts.create permission assigned.
        if (in_array($routeName, ['admin.contracts.create', 'admin.contracts.store', 'admin.contracts.template', 'admin.contracts.import'])) {
            $dept = $admin->department;
            if (in_array($dept, ['accounts', 'accountant', 'coordination'])) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'إنشاء العقود مخصص لقسم خدمة العملاء فقط.'], 403);
                }
                abort(403, 'إنشاء العقود مخصص لقسم خدمة العملاء فقط.');
            }
        }

        return $next($request);
    }
}
