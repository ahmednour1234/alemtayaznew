<?php

use App\Http\Controllers\Admin\AirportController;
use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\ExpenseTypeController;
use App\Http\Controllers\Admin\IncomeController;
use App\Http\Controllers\Admin\IncomeTypeController;
use App\Http\Controllers\Admin\NationalityController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\Reports\BranchStatementController;
use App\Http\Controllers\Admin\Reports\ContractReportsController;
use App\Http\Controllers\Admin\Reports\IncomeStatementController;
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

// ── Admin Auth ────────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');

    // ── Protected ─────────────────────────────────────────────────────────────
    Route::middleware(['auth.admin', 'auto.permission'])->group(function () {

        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        // Dashboard
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

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

        // Incomes
        Route::get('incomes/export',          [IncomeController::class, 'export'])->name('incomes.export');
        Route::get('incomes/template',        [IncomeController::class, 'importTemplate'])->name('incomes.template');
        Route::post('incomes/import',         [IncomeController::class, 'import'])->name('incomes.import');
        Route::post('incomes/{id}/restore',   [IncomeController::class, 'restore'])->name('incomes.restore');
        Route::resource('incomes', IncomeController::class);

        // Expenses
        Route::get('expenses/export',         [ExpenseController::class, 'export'])->name('expenses.export');
        Route::get('expenses/template',       [ExpenseController::class, 'importTemplate'])->name('expenses.template');
        Route::post('expenses/import',        [ExpenseController::class, 'import'])->name('expenses.import');
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
            Route::get('contracts-received',      [ContractReportsController::class, 'received'])->name('contracts-received');
            Route::get('contracts-delayed',       [ContractReportsController::class, 'delayed'])->name('contracts-delayed');
            Route::get('contracts-stats',         [ContractReportsController::class, 'stats'])->name('contracts-stats');
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

    });
});

