<?php

namespace App\Services;

use App\Repositories\Contracts\AgentRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AgentService
{
    public function __construct(
        private readonly AgentRepositoryInterface $repo
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

    public function store(array $data, ?UploadedFile $document = null)
    {
        if ($document) {
            $data['document'] = $document->store('agents/documents', 'public');
        }
        return $this->repo->create($data);
    }

    public function update(int $id, array $data, ?UploadedFile $document = null)
    {
        if ($document) {
            $old = $this->repo->findById($id);
            if ($old->document) {
                Storage::disk('public')->delete($old->document);
            }
            $data['document'] = $document->store('agents/documents', 'public');
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
