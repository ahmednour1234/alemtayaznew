<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\AdminNotification;
use App\Models\Agent;
use App\Models\Airport;
use App\Models\Branch;
use App\Models\Campaign;
use App\Models\Client;
use App\Models\Complaint;
use App\Models\ComplaintAttachment;
use App\Models\ContractActivityLog;
use App\Models\ContractStatusHistory;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\FinancialTransfer;
use App\Models\Housing;
use App\Models\HousingAssignment;
use App\Models\Income;
use App\Models\IncomeType;
use App\Models\Lead;
use App\Models\LeadCallLog;
use App\Models\Nationality;
use App\Models\RecruitmentContract;
use App\Models\SponsorshipTransfer;
use App\Models\Trip;
use App\Models\Worker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class FakeDataSeeder extends Seeder
{
    public function run(): void
    {
        // Always start fresh so unique constraints are clean
        Artisan::call('migrate:fresh', ['--force' => true]);

        // Re-run the base seeders (permissions, roles, admin accounts, branches, nationalities, airports)
        $this->call(DatabaseSeeder::class);

        // ── 1. Foundation ────────────────────────────────────────────
        $branches      = Branch::all()->count() > 0 ? Branch::all() : Branch::factory(5)->create();
        $nationalities = Nationality::all()->count() > 0 ? Nationality::all() : Nationality::factory(15)->create();
        $airports      = Airport::all()->count() > 0 ? Airport::all() : Airport::factory(8)->create();
        $admins        = Admin::factory(10)->recycle($branches)->create();

        // ── 2. People ────────────────────────────────────────────────
        $agents  = Agent::factory(10)->recycle($nationalities)->create();

        $clients = Client::factory(30)->recycle($branches, $admins, $nationalities)->create();

        $workers = Worker::factory(40)->recycle($nationalities, $branches, $admins)->create();

        // ── 3. Recruitment Contracts ─────────────────────────────────
        $contracts = RecruitmentContract::factory(20)
            ->recycle($clients, $branches, $admins, $workers, $agents, $airports, $nationalities)
            ->create();

        // Status history (2–4 entries per contract)
        foreach ($contracts as $contract) {
            ContractStatusHistory::factory(rand(2, 4))
                ->recycle($admins)
                ->create(['contract_id' => $contract->id]);

            ContractActivityLog::factory(rand(3, 6))
                ->recycle($admins)
                ->create(['contract_id' => $contract->id]);
        }

        // ── 4. Sponsorship Transfers ─────────────────────────────────
        SponsorshipTransfer::factory(8)
            ->recycle($workers, $clients, $branches, $admins, $contracts)
            ->create();

        // ── 5. Finance ───────────────────────────────────────────────
        $incomeTypes  = IncomeType::factory(6)->create();
        $expenseTypes = ExpenseType::factory(6)->create();

        Income::factory(25)->recycle($branches, $incomeTypes, $admins)->create();

        Expense::factory(25)->recycle($branches, $expenseTypes, $admins)->create();

        Expense::factory(10)->approved()
            ->recycle($branches, $expenseTypes, $admins)
            ->create();

        FinancialTransfer::factory(8)->recycle($branches, $admins)->create();

        // ── 6. Marketing ─────────────────────────────────────────────
        $campaigns = Campaign::factory(5)->recycle($branches, $admins)->create();

        $leads = Lead::factory(30)->recycle($campaigns, $branches, $admins, $nationalities, $clients)->create();

        foreach ($leads->random(15) as $lead) {
            LeadCallLog::factory(rand(1, 3))->recycle($admins)->create(['lead_id' => $lead->id]);
        }

        // ── 7. Complaints ────────────────────────────────────────────
        $complaints = Complaint::factory(15)
            ->recycle($clients, $workers, $branches, $admins, $contracts)
            ->create();

        Complaint::factory(5)->resolved()
            ->recycle($clients, $workers, $branches, $admins, $contracts)
            ->create();

        foreach ($complaints->random(8) as $complaint) {
            ComplaintAttachment::factory(rand(1, 3))->create(['complaint_id' => $complaint->id]);
        }

        // ── 8. Operations ────────────────────────────────────────────
        $housings = Housing::factory(6)->recycle($branches, $admins)->create();

        HousingAssignment::factory(12)
            ->recycle($workers, $housings, $branches, $admins)
            ->create();

        $trips = Trip::factory(10)
            ->withAirport()
            ->recycle($branches, $admins, $airports)
            ->create();

        // Attach workers to trips
        foreach ($trips as $trip) {
            $trip->workers()->attach(
                $workers->random(rand(1, 4))->pluck('id')->mapWithKeys(
                    fn($id) => [$id => ['status' => 'scheduled', 'notes' => null]]
                )->all()
            );
        }

        // ── 9. Notifications ─────────────────────────────────────────
        AdminNotification::factory(20)->recycle($admins)->create();
        AdminNotification::factory(10)->read()->recycle($admins)->create();

        $this->command->info('✓ Fake data seeded successfully.');
        $this->command->table(
            ['Model', 'Count'],
            [
                ['Branches',             $branches->count()],
                ['Nationalities',        $nationalities->count()],
                ['Airports',             $airports->count()],
                ['Admins',               $admins->count()],
                ['Agents',               $agents->count()],
                ['Clients',              $clients->count()],
                ['Workers',              $workers->count()],
                ['Contracts',            $contracts->count()],
                ['Sponsorship Transfers','8'],
                ['Income Types',         $incomeTypes->count()],
                ['Expense Types',        $expenseTypes->count()],
                ['Incomes',              '25'],
                ['Expenses',             '35'],
                ['Financial Transfers',  '8'],
                ['Campaigns',            $campaigns->count()],
                ['Leads',                $leads->count()],
                ['Complaints',           '20'],
                ['Housings',             $housings->count()],
                ['Housing Assignments',  '12'],
                ['Trips',                $trips->count()],
                ['Notifications',        '30'],
            ]
        );
    }
}
