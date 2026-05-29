<?php

namespace App\Providers;

use App\Repositories\AdminRepository;
use App\Repositories\AgentRepository;
use App\Repositories\BranchRepository;
use App\Repositories\ClientRepository;
use App\Repositories\ComplaintRepository;
use App\Repositories\Contracts\AdminRepositoryInterface;
use App\Repositories\Contracts\AgentRepositoryInterface;
use App\Repositories\Contracts\BranchRepositoryInterface;
use App\Repositories\Contracts\ClientRepositoryInterface;
use App\Repositories\Contracts\ComplaintRepositoryInterface;
use App\Repositories\Contracts\ExpenseRepositoryInterface;
use App\Repositories\Contracts\ExpenseTypeRepositoryInterface;
use App\Repositories\Contracts\HousingAssignmentRepositoryInterface;
use App\Repositories\Contracts\HousingRepositoryInterface;
use App\Repositories\Contracts\IncomeRepositoryInterface;
use App\Repositories\Contracts\IncomeTypeRepositoryInterface;
use App\Repositories\Contracts\PermissionRepositoryInterface;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Contracts\SponsorshipTransferRepositoryInterface;
use App\Repositories\Contracts\TransferRepositoryInterface;
use App\Repositories\Contracts\TripRepositoryInterface;
use App\Repositories\ExpenseRepository;
use App\Repositories\ExpenseTypeRepository;
use App\Repositories\HousingAssignmentRepository;
use App\Repositories\HousingRepository;
use App\Repositories\IncomeRepository;
use App\Repositories\IncomeTypeRepository;
use App\Repositories\PermissionRepository;
use App\Repositories\RoleRepository;
use App\Repositories\Contracts\WorkerRepositoryInterface;
use App\Repositories\SponsorshipTransferRepository;
use App\Repositories\TransferRepository;
use App\Repositories\TripRepository;
use App\Repositories\WorkerRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AdminRepositoryInterface::class, AdminRepository::class);
        $this->app->bind(AgentRepositoryInterface::class, AgentRepository::class);
        $this->app->bind(BranchRepositoryInterface::class, BranchRepository::class);
        $this->app->bind(ClientRepositoryInterface::class, ClientRepository::class);
        $this->app->bind(ComplaintRepositoryInterface::class, ComplaintRepository::class);
        $this->app->bind(IncomeTypeRepositoryInterface::class, IncomeTypeRepository::class);
        $this->app->bind(ExpenseTypeRepositoryInterface::class, ExpenseTypeRepository::class);
        $this->app->bind(HousingRepositoryInterface::class, HousingRepository::class);
        $this->app->bind(IncomeRepositoryInterface::class, IncomeRepository::class);
        $this->app->bind(ExpenseRepositoryInterface::class, ExpenseRepository::class);
        $this->app->bind(TransferRepositoryInterface::class, TransferRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(PermissionRepositoryInterface::class, PermissionRepository::class);
        $this->app->bind(WorkerRepositoryInterface::class, WorkerRepository::class);
        $this->app->bind(
            \App\Repositories\Contracts\RecruitmentContractRepositoryInterface::class,
            \App\Repositories\RecruitmentContractRepository::class
        );
        $this->app->bind(HousingAssignmentRepositoryInterface::class, HousingAssignmentRepository::class);
        $this->app->bind(TripRepositoryInterface::class, TripRepository::class);
        $this->app->bind(SponsorshipTransferRepositoryInterface::class, SponsorshipTransferRepository::class);
    }
}
