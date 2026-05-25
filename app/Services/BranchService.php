<?php

namespace App\Services;

use App\Repositories\Contracts\BranchRepositoryInterface;
use Illuminate\Support\Facades\DB;

class BranchService
{
    public function __construct(
        private readonly BranchRepositoryInterface $repo
    ) {}

    public function list(array $filters = [])
    {
        return $this->repo->getAll($filters);
    }

    public function allActive()
    {
        return $this->repo->getAllActive();
    }

    public function find(int $id)
    {
        return $this->repo->findById($id);
    }

    public function store(array $data)
    {
        return DB::transaction(fn() => $this->repo->create($data));
    }

    public function update(int $id, array $data)
    {
        return DB::transaction(fn() => $this->repo->update($id, $data));
    }

    public function destroy(int $id): bool
    {
        return DB::transaction(fn() => $this->repo->delete($id));
    }

    public function restore(int $id): bool
    {
        return DB::transaction(fn() => $this->repo->restore($id));
    }

    public function trashed()
    {
        return $this->repo->getTrashed();
    }

    public function toggleActive(int $id)
    {
        $branch = $this->repo->findById($id);
        return $this->repo->update($id, ['active' => ! $branch->active]);
    }
}
