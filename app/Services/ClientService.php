<?php

namespace App\Services;

use App\Repositories\Contracts\ClientRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ClientService
{
    public function __construct(
        private readonly ClientRepositoryInterface $repo
    ) {}

    public function list(array $filters = [])
    {
        return $this->repo->getAll($filters);
    }

    public function find(int $id)
    {
        return $this->repo->findById($id);
    }

    public function trashed()
    {
        return $this->repo->getTrashed();
    }

    public function store(array $data, ?UploadedFile $image = null)
    {
        if ($image) {
            $data['national_id_image'] = $image->store('clients/id-images', 'public');
        }
        return $this->repo->create($data);
    }

    public function update(int $id, array $data, ?UploadedFile $image = null)
    {
        if ($image) {
            $old = $this->repo->findById($id);
            if ($old->national_id_image) {
                Storage::disk('public')->delete($old->national_id_image);
            }
            $data['national_id_image'] = $image->store('clients/id-images', 'public');
        }
        return $this->repo->update($id, $data);
    }

    public function destroy(int $id): bool
    {
        return $this->repo->delete($id);
    }

    public function restore(int $id): bool
    {
        return $this->repo->restore($id);
    }
}
