<?php

namespace App\Repositories;

use App\Models\Worker;
use App\Repositories\Contracts\WorkerRepositoryInterface;

class WorkerRepository implements WorkerRepositoryInterface
{
    public function getAll(array $filters = []): mixed
    {
        return $this->filteredQuery($filters)
                    ->with(['nationality', 'client', 'branch', 'assignedBy'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(20)
                    ->withQueryString();
    }

    /**
     * الاستعلام الأساسي بعد تطبيق الفلاتر — يُستخدم للعرض وللعمليات الجماعية
     * («تحديد كل النتائج») حتى يبقى نطاق الفلترة واحداً في الحالتين.
     */
    public function filteredQuery(array $filters = []): \Illuminate\Database\Eloquent\Builder
    {
        $q = Worker::query()->where('active', true);

        if (!empty($filters['nationality_id'])) {
            $q->where('nationality_id', $filters['nationality_id']);
        }
        if (!empty($filters['status'])) {
            $q->where('status', $filters['status']);
        }
        if (!empty($filters['profession'])) {
            $q->where('profession', $filters['profession']);
        }
        if (!empty($filters['search'])) {
            $term = $filters['search'];
            // passport_number & phone are encrypted at rest → exact-match via hash columns.
            $q->where(function ($q2) use ($term) {
                $q2->where('name', 'like', '%' . $term . '%')
                   ->orWhere('passport_number_hash', Worker::hashPii($term))
                   ->orWhere('phone_hash', Worker::hashPii($term));
            });
        }

        // فقط من لديها CV — يُستخدم عند الإرسال عبر واتساب
        if (!empty($filters['has_cv'])) {
            $q->whereNotNull('cv_path');
        }

        return $q;
    }

    public function findById(int $id): mixed
    {
        return Worker::with(['nationality', 'client', 'branch', 'latestContract.client', 'latestContract.originNationality', 'latestContract.agent'])->findOrFail($id);
    }

    public function create(array $data): mixed
    {
        return Worker::create($data);
    }

    public function update(int $id, array $data): mixed
    {
        $worker = Worker::findOrFail($id);
        $worker->update($data);
        return $worker;
    }

    public function delete(int $id): void
    {
        Worker::findOrFail($id)->delete();
    }

    public function restore(int $id): void
    {
        Worker::withTrashed()->findOrFail($id)->restore();
    }

    public function getTrashed(): mixed
    {
        return Worker::onlyTrashed()->with('nationality')->get();
    }

    /**
     * حجز العاملة لعميل. الحالة «محجوزة» لا «تم التعيين» لأن الحجز مؤقت
     * (72 ساعة) ويُفكّ تلقائياً إن لم يُنشأ عقد — راجع workers:notify-uncontracted.
     * تتحول إلى «تم التعيين» عند إنشاء عقد الاستقدام.
     */
    public function assignToClient(int $id, int $clientId, int $assignedByAdminId): void
    {
        $worker = Worker::findOrFail($id);

        // منع حجز نفس العاملة لنفس العميل مرتين طالما الحجز/التعيين ما زال قائماً
        if ($worker->client_id === $clientId && in_array($worker->status, ['reserved', 'assigned'], true)) {
            throw new \RuntimeException('هذه العاملة محجوزة بالفعل لنفس العميل. لا يمكن حجزها مرتين.');
        }

        // العاملة محجوزة لعميل آخر — لا تُسحب منه ضمنياً
        if ($worker->client_id && $worker->client_id !== $clientId && in_array($worker->status, ['reserved', 'assigned'], true)) {
            throw new \RuntimeException('هذه العاملة محجوزة لعميل آخر. ألغِ الحجز الحالي أولاً.');
        }

        $worker->update([
            'client_id'             => $clientId,
            'status'                => 'reserved',
            'assigned_by_admin_id'  => $assignedByAdminId,
            'assigned_at'           => now(),
        ]);
    }

    public function unassign(int $id): void
    {
        Worker::findOrFail($id)->update([
            'client_id'             => null,
            'status'                => 'available',
            'assigned_by_admin_id'  => null,
            'assigned_at'           => null,
        ]);
    }

    public function findDuplicateCv(?string $passportNumber, ?string $originalCvName): mixed
    {
        $q = Worker::where('active', true);
        $q->where(function ($sub) use ($passportNumber, $originalCvName) {
            if ($passportNumber) {
                $sub->orWhere('passport_number_hash', Worker::hashPii($passportNumber));
            }
            if ($originalCvName) {
                $sub->orWhere('original_cv_name', $originalCvName);
            }
        });
        return $q->first();
    }
}
