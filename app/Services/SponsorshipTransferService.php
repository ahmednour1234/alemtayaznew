<?php

namespace App\Services;

use App\Models\SponsorshipTransfer;
use App\Models\Worker;
use App\Repositories\Contracts\SponsorshipTransferRepositoryInterface;

class SponsorshipTransferService
{
    public function __construct(
        private readonly SponsorshipTransferRepositoryInterface $repo
    ) {}

    public function list(array $filters = [])
    {
        return $this->repo->getAll($filters);
    }

    public function find(int $id)
    {
        return $this->repo->findById($id);
    }

    public function create(array $data): SponsorshipTransfer
    {
        $data['contract_number'] = SponsorshipTransfer::generateNumber();
        $transfer = $this->repo->create($data);

        // Mark worker as sponsorship_transfer
        Worker::where('id', $data['worker_id'])->update(['status' => 'sponsorship_transfer']);

        return $transfer;
    }

    public function update(int $id, array $data): SponsorshipTransfer
    {
        return $this->repo->update($id, $data);
    }

    public function forwardToAccounts(int $id): SponsorshipTransfer
    {
        return $this->repo->update($id, [
            'current_department' => 'accounts',
            'current_status'     => 2,
        ]);
    }

    public function complete(int $id): SponsorshipTransfer
    {
        $transfer = $this->repo->update($id, ['current_status' => 3]);

        // Update worker status to assigned (now with new client)
        Worker::where('id', $transfer->worker_id)->update(['status' => 'assigned']);

        return $transfer;
    }

    public function cancel(int $id): SponsorshipTransfer
    {
        $transfer = $this->repo->update($id, ['current_status' => 4]);

        // Revert worker status
        Worker::where('id', $transfer->worker_id)
            ->where('status', 'sponsorship_transfer')
            ->update(['status' => 'assigned']);

        return $transfer;
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }

    public function restore(int $id): bool
    {
        return $this->repo->restore($id);
    }

    public function countPending(?int $branchId): int
    {
        return $this->repo->countPending($branchId);
    }
}
