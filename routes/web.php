<?php

use App\Http\Controllers\Admin\GlobalSearchController;
use App\Http\Controllers\Admin\HousingAssignmentController;
use App\Http\Controllers\Admin\HousingVisitController;
use App\Http\Controllers\Admin\TripController;
use App\Http\Controllers\Admin\SponsorshipTransferController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\ComplaintTrackingController;
use App\Http\Controllers\Admin\AirportController;
use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ComplaintController;
use App\Http\Controllers\Admin\ComplaintReportsController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\ExpenseTypeController;
use App\Http\Controllers\Admin\HousingController;
use App\Http\Controllers\Admin\IncomeController;
use App\Http\Controllers\Admin\IncomeTypeController;
use App\Http\Controllers\Admin\NationalityController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\Reports\BranchStatementController;
use App\Http\Controllers\Admin\Reports\ContractReportsController;
use App\Http\Controllers\Admin\Reports\HousingReportsController;
use App\Http\Controllers\Admin\Reports\IncomeStatementController;
use App\Http\Controllers\Admin\Reports\TypeBreakdownController;
use App\Http\Controllers\Admin\Settings\AdminController as SettingsAdminController;
use App\Http\Controllers\Admin\Settings\PermissionController;
use App\Http\Controllers\Admin\Settings\RoleController;
use App\Http\Controllers\Admin\TransferController;
use App\Http\Controllers\Admin\RecruitmentContractController;
use App\Http\Controllers\Admin\WorkerController;
use Illuminate\Support\Facades\Route;

// ── Redirect root to admin dashboard ─────────────────────────────────────────
Route::get('/', fn() => redirect()->route('admin.login'));

// ── Public contract tracking ──────────────────────────────────────────────────
Route::get('/track', [RecruitmentContractController::class, 'publicTrack'])->name('contract.track');

// ── Public complaint tracking ──────────────────────────────────────────────────
Route::get('/complaint/track/{token}', [ComplaintTrackingController::class, 'show'])->name('complaint.track');

// ── Public worker CV/passport (sent via WhatsApp) ────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('workers/{id}/cv',       [WorkerController::class, 'serveCV'])->name('workers.cv');
    Route::get('workers/{id}/passport', [WorkerController::class, 'servePassport'])->name('workers.passport');
});

// ── Admin Auth ────────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');

    // ── Protected ─────────────────────────────────────────────────────────────
    Route::middleware(['auth.admin', 'auto.permission'])->group(function () {

        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        // Global Search
        Route::get('search', [GlobalSearchController::class, 'search'])->name('search');

        // Dashboard
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('dashboard/reject-all-pending',  [DashboardController::class, 'rejectAllPending'])->name('dashboard.reject-all-pending');
        Route::post('dashboard/approve-all-pending', [DashboardController::class, 'approveAllPending'])->name('dashboard.approve-all-pending');
        Route::get('dashboard/import-statement',          [DashboardController::class, 'importStatementPage'])->name('dashboard.import-statement');
        Route::get('dashboard/import-statement/template', [DashboardController::class, 'importStatementTemplate'])->name('dashboard.import-statement.template');
        Route::post('dashboard/import-statement',         [DashboardController::class, 'importStatement'])->name('dashboard.import-statement.store');
        Route::post('dashboard/import-flexible',          [DashboardController::class, 'importFlexible'])->name('dashboard.import-flexible.store');

        // Notifications
        Route::get('notifications',           [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
        Route::get('notifications/{id}/read', [NotificationController::class, 'read'])->name('notifications.read');
        // Nationalities
        Route::post('nationalities/{id}/restore', [NationalityController::class, 'restore'])->name('nationalities.restore');
        Route::post('nationalities/{id}/toggle',  [NationalityController::class, 'toggleActive'])->name('nationalities.toggle');
        Route::resource('nationalities', NationalityController::class)->except('show');

        // Airports
        Route::post('airports/{id}/restore', [AirportController::class, 'restore'])->name('airports.restore');
        Route::post('airports/{id}/toggle',  [AirportController::class, 'toggleActive'])->name('airports.toggle');
        Route::resource('airports', AirportController::class)->except('show');

        // Branches
        Route::get('branches/trashed',         [BranchController::class, 'index'])->name('branches.trashed');
        Route::post('branches/{id}/restore',   [BranchController::class, 'restore'])->name('branches.restore');
        Route::post('branches/{id}/toggle',    [BranchController::class, 'toggleActive'])->name('branches.toggle');
        Route::resource('branches', BranchController::class);

        // Income Types
        Route::post('income-types/{id}/restore', [IncomeTypeController::class, 'restore'])->name('income-types.restore');
        Route::post('income-types/{id}/toggle',  [IncomeTypeController::class, 'toggleActive'])->name('income-types.toggle');
        Route::resource('income-types', IncomeTypeController::class);

        // Expense Types
        Route::post('expense-types/{id}/restore', [ExpenseTypeController::class, 'restore'])->name('expense-types.restore');
        Route::post('expense-types/{id}/toggle',  [ExpenseTypeController::class, 'toggleActive'])->name('expense-types.toggle');
        Route::resource('expense-types', ExpenseTypeController::class);

        // Housing (السكن)
        Route::post('housings/{id}/restore', [HousingController::class, 'restore'])->name('housings.restore');
        Route::post('housings/{id}/toggle',  [HousingController::class, 'toggleActive'])->name('housings.toggle');
        Route::resource('housings', HousingController::class)->except('show');

        // Complaints (الشكاوي)
        Route::get('complaints/reports', [ComplaintReportsController::class, 'index'])->name('complaints.reports');
        Route::post('complaints/{id}/restore', [ComplaintController::class, 'restore'])->name('complaints.restore');
        Route::delete('complaints/attachments/{attachmentId}', [ComplaintController::class, 'deleteAttachment'])->name('complaints.attachments.destroy');
        Route::resource('complaints', ComplaintController::class);

        // Incomes
        Route::get('incomes/export',          [IncomeController::class, 'export'])->name('incomes.export');
        Route::get('incomes/template',        [IncomeController::class, 'importTemplate'])->name('incomes.template');
        Route::post('incomes/import',         [IncomeController::class, 'import'])->name('incomes.import');
        Route::post('incomes/{id}/restore',   [IncomeController::class, 'restore'])->name('incomes.restore');
        Route::resource('incomes', IncomeController::class);

        // Expenses
        Route::get('expenses/export',                [ExpenseController::class, 'export'])->name('expenses.export');
        Route::get('expenses/template',              [ExpenseController::class, 'importTemplate'])->name('expenses.template');
        Route::post('expenses/import',               [ExpenseController::class, 'import'])->name('expenses.import');
        Route::get('expenses/recruitment-template',  [ExpenseController::class, 'recruitmentTemplate'])->name('expenses.recruitment-template');
        Route::post('expenses/recruitment-import',   [ExpenseController::class, 'recruitmentImport'])->name('expenses.recruitment-import');
        Route::post('expenses/{id}/approve',  [ExpenseController::class, 'approve'])->name('expenses.approve');
        Route::post('expenses/{id}/reject',   [ExpenseController::class, 'reject'])->name('expenses.reject');
        Route::post('expenses/{id}/restore',  [ExpenseController::class, 'restore'])->name('expenses.restore');
        Route::resource('expenses', ExpenseController::class);

        // Transfers
        Route::post('transfers/{id}/approve', [TransferController::class, 'approve'])->name('transfers.approve');
        Route::post('transfers/{id}/reject',  [TransferController::class, 'reject'])->name('transfers.reject');
        Route::post('transfers/{id}/restore', [TransferController::class, 'restore'])->name('transfers.restore');
        Route::resource('transfers', TransferController::class);

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('branch-statement',        [BranchStatementController::class, 'index'])->name('branch-statement');
            Route::get('branch-statement/export', [BranchStatementController::class, 'export'])->name('branch-statement.export');
            Route::get('income-statement',        [IncomeStatementController::class, 'index'])->name('income-statement');
            Route::get('income-statement/export', [IncomeStatementController::class, 'export'])->name('income-statement.export');
            Route::get('type-breakdown',          [TypeBreakdownController::class, 'index'])->name('type-breakdown');
            Route::get('type-breakdown/excel',    [TypeBreakdownController::class, 'exportExcel'])->name('type-breakdown.excel');
            Route::get('type-breakdown/pdf',      [TypeBreakdownController::class, 'exportPdf'])->name('type-breakdown.pdf');
            Route::get('contracts-received',      [ContractReportsController::class, 'received'])->name('contracts-received');
            Route::get('contracts-delayed',       [ContractReportsController::class, 'delayed'])->name('contracts-delayed');
            Route::get('contracts-stats',         [ContractReportsController::class, 'stats'])->name('contracts-stats');
            Route::get('housing-rentals',         [HousingReportsController::class, 'rentals'])->name('housing-rentals');
            Route::get('housing-settlements',     [HousingReportsController::class, 'settlements'])->name('housing-settlements');
        });

        // Settings
        Route::prefix('settings')->name('settings.')->group(function () {
            // Admins
            Route::post('admins/{id}/restore',  [SettingsAdminController::class, 'restore'])->name('admins.restore');
            Route::post('admins/{id}/toggle',   [SettingsAdminController::class, 'toggleActive'])->name('admins.toggle');
            Route::resource('admins', SettingsAdminController::class);

            // Roles
            Route::resource('roles', RoleController::class)->except('show');

            // Permissions (read-only view)
            Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
        });

        // Clients
        Route::post('clients/quick-store',  [ClientController::class, 'quickStore'])->name('clients.quick-store');
        Route::post('clients/{id}/restore', [ClientController::class, 'restore'])->name('clients.restore');
        Route::resource('clients', ClientController::class);

        // Agents
        Route::post('agents/{id}/restore', [AgentController::class, 'restore'])->name('agents.restore');
        Route::resource('agents', AgentController::class);

        // Workers
        Route::get('workers/bulk',              [WorkerController::class, 'bulk'])->name('workers.bulk');
        Route::post('workers/bulk-store',       [WorkerController::class, 'bulkStore'])->name('workers.bulk-store');
        Route::post('workers/quick-store',      [WorkerController::class, 'quickStore'])->name('workers.quick-store');
        Route::post('workers/send-whatsapp',    [WorkerController::class, 'sendWhatsapp'])->name('workers.send-whatsapp');
        Route::post('workers/{id}/restore',     [WorkerController::class, 'restore'])->name('workers.restore');
        Route::get('workers/{id}/assign',       [WorkerController::class, 'assign'])->name('workers.assign');
        Route::post('workers/{id}/assign',      [WorkerController::class, 'doAssign'])->name('workers.do-assign');
        Route::post('workers/{id}/unassign',    [WorkerController::class, 'unassign'])->name('workers.unassign');

        Route::resource('workers', WorkerController::class);

        // Recruitment Contracts
        Route::post('contracts/{id}/update-status',  [RecruitmentContractController::class, 'updateStatus'])->name('contracts.update-status');
        Route::post('contracts/{id}/forward',        [RecruitmentContractController::class, 'forward'])->name('contracts.forward');
        Route::get('contracts/trashed',              [RecruitmentContractController::class, 'trashed'])->name('contracts.trashed');
        Route::post('contracts/{id}/restore',        [RecruitmentContractController::class, 'restore'])->name('contracts.restore');
        Route::delete('contracts/{id}/force-delete', [RecruitmentContractController::class, 'forceDelete'])->name('contracts.force-delete');
        Route::get('contracts/export',               [RecruitmentContractController::class, 'export'])->name('contracts.export');
        Route::get('contracts/template',             [RecruitmentContractController::class, 'template'])->name('contracts.template');
        Route::post('contracts/import',              [RecruitmentContractController::class, 'import'])->name('contracts.import');
        Route::post('contracts/bulk-delete',         [RecruitmentContractController::class, 'bulkDelete'])->name('contracts.bulk-delete');
        Route::get('contracts/{id}/print',           [RecruitmentContractController::class, 'printView'])->name('contracts.print');
        Route::resource('contracts', RecruitmentContractController::class);

        // Cities (distinct cities from branches)
        Route::get('cities', function () {
            $cities = \App\Models\Branch::whereNotNull('city')
                ->select('city', \Illuminate\Support\Facades\DB::raw('count(*) as branches_count'))
                ->groupBy('city')
                ->orderBy('city')
                ->get();
            return view('admin.cities.index', compact('cities'));
        })->name('cities.index');

        // Marketing
        Route::prefix('marketing')->name('marketing.')->group(function () {
            Route::post('campaigns/{campaign}/import-sheet', [\App\Http\Controllers\Admin\Marketing\CampaignController::class, 'importSheet'])->name('campaigns.import-sheet');
            Route::post('campaigns/{campaign}/reassign-unassigned', [\App\Http\Controllers\Admin\Marketing\CampaignController::class, 'reassignUnassigned'])->name('campaigns.reassign-unassigned');
            Route::resource('campaigns', \App\Http\Controllers\Admin\Marketing\CampaignController::class);

            Route::post('leads/{lead}/call',    [\App\Http\Controllers\Admin\Marketing\LeadController::class, 'logCall'])->name('leads.call');
            Route::post('leads/{lead}/convert', [\App\Http\Controllers\Admin\Marketing\LeadController::class, 'convert'])->name('leads.convert');
            Route::resource('leads', \App\Http\Controllers\Admin\Marketing\LeadController::class)->except(['create', 'edit']);

            Route::get('reports', [\App\Http\Controllers\Admin\Marketing\ReportsController::class, 'index'])->name('reports');
            Route::get('staff-performance', [\App\Http\Controllers\Admin\Marketing\StaffPerformanceController::class, 'index'])->name('staff-performance');

            // Leads board (branch-grouped dashboard + auto-assign)
            Route::get('leads-board', [\App\Http\Controllers\Admin\Marketing\LeadsBoardController::class, 'index'])->name('leads-board');
            Route::post('leads-board/{branch}/auto-assign', [\App\Http\Controllers\Admin\Marketing\LeadsBoardController::class, 'autoAssign'])->name('leads-board.auto-assign');
        });

        // Housing Assignments (تعيينات السكن)
        Route::get('housing-assignments',                     [HousingAssignmentController::class, 'index'])->name('housing-assignments.index');
        Route::get('housing-assignments/create',              [HousingAssignmentController::class, 'create'])->name('housing-assignments.create');
        Route::post('housing-assignments',                    [HousingAssignmentController::class, 'store'])->name('housing-assignments.store');
        Route::patch('housing-assignments/{id}/checkout',     [HousingAssignmentController::class, 'checkout'])->name('housing-assignments.checkout');
        Route::patch('housing-assignments/{id}',              [HousingAssignmentController::class, 'update'])->name('housing-assignments.update');
        Route::delete('housing-assignments/{id}',             [HousingAssignmentController::class, 'destroy'])->name('housing-assignments.destroy');
        Route::get('housing-visits/reports',                  [HousingVisitController::class, 'reports'])->name('housing-visits.reports');
        Route::resource('housing-visits', HousingVisitController::class);

        // Trips (الرحلات)
        Route::post('trips/{trip}/workers',                   [TripController::class, 'addWorker'])->name('trips.add-worker');
        Route::post('trips/{trip}/workers/bulk',              [TripController::class, 'addWorkersBulk'])->name('trips.add-workers-bulk');
        Route::delete('trips/{trip}/workers/{worker}',        [TripController::class, 'removeWorker'])->name('trips.remove-worker');
        Route::get('trips/{trip}/checklist',                  [TripController::class, 'showChecklist'])->name('trips.checklist');
        Route::post('trips/{trip}/checklist',                 [TripController::class, 'submitChecklist'])->name('trips.checklist.submit');
        Route::patch('trips/{trip}/complete',                 [TripController::class, 'complete'])->name('trips.complete');
        Route::get('trips/{trip}/print',                      [TripController::class, 'print'])->name('trips.print');
        Route::resource('trips', TripController::class);

        // Sponsorship Transfers (عقود نقل الكفالة)
        Route::get('sponsorship-transfers/reports',             [SponsorshipTransferController::class, 'reports'])->name('sponsorship-transfers.reports');
        Route::post('sponsorship-transfers/{id}/update-status', [SponsorshipTransferController::class, 'updateStatus'])->name('sponsorship-transfers.update-status');
        Route::get('sponsorship-transfers/{id}/print',          [SponsorshipTransferController::class, 'print'])->name('sponsorship-transfers.print');
        Route::resource('sponsorship-transfers', SponsorshipTransferController::class);

        // Calendar (التقويم)
        Route::get('calendar',        [CalendarController::class, 'index'])->name('calendar.index');
        Route::get('calendar/events', [CalendarController::class, 'events'])->name('calendar.events');

    });
});
