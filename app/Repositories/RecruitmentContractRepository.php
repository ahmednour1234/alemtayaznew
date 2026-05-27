<?php

namespace App\Repositories;

use App\Models\ContractStatusHistory;
use App\Models\RecruitmentContract;
use App\Repositories\Contracts\RecruitmentContractRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class RecruitmentContractRepository implements RecruitmentContractRepositoryInterface
{
    public function getAll(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $q = RecruitmentContract::with(['client', 'branch', 'admin', 'worker.nationality'])
            ->latest();

        if (! empty($filters['branch_id'])) {
            $q->where('branch_id', $filters['branch_id']);
        }

        if (! empty($filters['department'])) {
            $q->where('current_department', $filters['department']);
        }

        if (! empty($filters['status'])) {
            $q->where('current_status', $filters['status']);
        }

        if (! empty($filters['payment_status'])) {
            $q->where('payment_status', $filters['payment_status']);
        }

        if (! empty($filters['search'])) {
            $s = $filters['search'];
            $q->where(function ($qr) use ($s) {
                $qr->where('contract_number', 'like', "%{$s}%")
                   ->orWhere('musaned_number', 'like', "%{$s}%")
                   ->orWhereHas('client', fn($c) => $c->where('name', 'like', "%{$s}%"));
            });
        }

        if (! empty($filters['nationality_id'])) {
            $q->whereHas('worker', fn($w) => $w->where('nationality_id', $filters['nationality_id']));
        }

        return $q->paginate($perPage)->withQueryString();
    }

    public function findById(int $id): RecruitmentContract
    {
        return RecruitmentContract::with([
            'client', 'branch', 'admin', 'worker', 'agent',
            'arrivalAirport', 'originNationality', 'deliveryAirport',
            'statusHistories.admin',
        ])->findOrFail($id);
    }

    public function findByMusanedNumber(string $musanedNumber): ?RecruitmentContract
    {
        return RecruitmentContract::with(['client', 'branch', 'worker', 'statusHistories'])
            ->where('musaned_number', $musanedNumber)
            ->first();
    }

    public function create(array $data): RecruitmentContract
    {
        return RecruitmentContract::create($data);
    }

    public function update(RecruitmentContract $contract, array $data): RecruitmentContract
    {
        $contract->update($data);
        return $contract->fresh();
    }

    public function delete(int $id): void
    {
        RecruitmentContract::findOrFail($id)->delete();
    }

    public function updateStatus(RecruitmentContract $contract, int $status, ?string $date, ?string $waMessage, int $adminId): void
    {
        // Upsert status history row for this status number
        ContractStatusHistory::updateOrCreate(
            ['contract_id' => $contract->id, 'status' => $status],
            ['status_date' => $date, 'admin_id' => $adminId, 'whatsapp_message' => $waMessage]
        );

        $department = match (true) {
            $status <= 4  => 'customer_service',
            $status <= 8  => 'accounts',
            default       => 'coordination',
        };

        $contract->update([
            'current_status'     => $status,
            'current_department' => $department,
        ]);
    }

    public function getStatsByBranch(): array
    {
        return RecruitmentContract::selectRaw('branch_id, current_status, count(*) as total')
            ->with('branch:id,name')
            ->groupBy('branch_id', 'current_status')
            ->get()
            ->groupBy('branch_id')
            ->map(fn($rows) => [
                'branch' => $rows->first()->branch->name ?? '—',
                'total'  => $rows->sum('total'),
                'active' => $rows->whereNotIn('current_status', [9, 15])->sum('total'),
                'done'   => $rows->where('current_status', 13)->sum('total'),
            ])
            ->values()
            ->toArray();
    }
}
