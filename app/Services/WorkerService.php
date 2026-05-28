<?php

namespace App\Services;

use App\Repositories\Contracts\WorkerRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class WorkerService
{
    public function __construct(private readonly WorkerRepositoryInterface $repo) {}

    public function list(array $filters = []): mixed
    {
        return $this->repo->getAll($filters);
    }

    public function find(int $id): mixed
    {
        return $this->repo->findById($id);
    }

    public function trashed(): mixed
    {
        return $this->repo->getTrashed();
    }

    public function store(array $data, ?UploadedFile $cv = null, ?UploadedFile $passportImage = null): mixed
    {
        if ($cv) {
            $data['cv_path'] = $cv->store('workers/cvs', 'public');
        }
        if ($passportImage) {
            $data['passport_image'] = $passportImage->store('workers/passports', 'public');
        }
        return $this->repo->create($data);
    }

    /** Bulk upload: each PDF becomes one worker record */
    public function bulkStore(array $commonData, array $files): array
    {
        $created = [];
        foreach ($files as $file) {
            $data             = $commonData;
            $data['cv_path']  = $file->store('workers/cvs', 'public');
            $data['name']     = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $created[]        = $this->repo->create($data);
        }
        return $created;
    }

    public function update(int $id, array $data, ?UploadedFile $cv = null, ?UploadedFile $passportImage = null): mixed
    {
        if ($cv) {
            $old = $this->repo->findById($id);
            if ($old->cv_path) {
                Storage::disk('public')->delete($old->cv_path);
            }
            $data['cv_path'] = $cv->store('workers/cvs', 'public');
        }
        if ($passportImage) {
            $old = $old ?? $this->repo->findById($id);
            if ($old->passport_image) {
                Storage::disk('public')->delete($old->passport_image);
            }
            $data['passport_image'] = $passportImage->store('workers/passports', 'public');
        }
        return $this->repo->update($id, $data);
    }

    public function destroy(int $id): void
    {
        $this->repo->delete($id);
    }

    public function restore(int $id): void
    {
        $this->repo->restore($id);
    }

    public function assignToClient(int $id, int $clientId): void
    {
        $this->repo->assignToClient($id, $clientId);
    }

    public function unassign(int $id): void
    {
        $this->repo->unassign($id);
    }

    /** Generate a WhatsApp URL for the given worker IDs */
    public function buildWhatsappUrl(string $phone, array $workerIds): string
    {
        $workers = \App\Models\Worker::whereIn('id', $workerIds)
                    ->whereNotNull('cv_path')->get();

        $lines = ["السلام عليكم، مرفق مجموعة CV عاملات للمراجعة:\n"];
        foreach ($workers as $i => $w) {
            $url     = Storage::disk('public')->url($w->cv_path);
            $name    = $w->name ?: ('عاملة ' . ($i + 1));
            $nat     = $w->nationality?->name ?? '';
            $lines[] = ($i + 1) . ". {$name}" . ($nat ? " ({$nat})" : '') . "\n{$url}";
        }

        $message = implode("\n\n", $lines);
        $clean   = preg_replace('/[^0-9]/', '', $phone);
        return 'https://wa.me/' . $clean . '?text=' . rawurlencode($message);
    }
}
