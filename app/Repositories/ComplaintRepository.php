<?php

namespace App\Repositories;

use App\Models\Complaint;
use App\Repositories\Contracts\ComplaintRepositoryInterface;

class ComplaintRepository implements ComplaintRepositoryInterface
{
    public function getAll(array $filters = [])
    {
        $q = Complaint::query()->with([
            'contract:id,contract_number',
            'client:id,name',
            'worker:id,name',
            'branch:id,name',
            'assignedAdmin:id,name',
        ]);

        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $q->where(function ($w) use ($s) {
                $w->where('complaint_number', 'like', "%$s%")
                  ->orWhere('description', 'like', "%$s%")
                  ->orWhere('phone', 'like', "%$s%")
                  ->orWhere('musaned_number', 'like', "%$s%");
            });
        }
        if (!empty($filters['status']))       $q->where('status', $filters['status']);
        if (!empty($filters['priority']))     $q->where('priority', $filters['priority']);
        if (!empty($filters['problem_type'])) $q->where('problem_type', $filters['problem_type']);
        if (!empty($filters['branch_id']))    $q->where('branch_id', $filters['branch_id']);
        if (!empty($filters['assigned_admin_id'])) $q->where('assigned_admin_id', $filters['assigned_admin_id']);
        if (isset($filters['on_musaned']) && $filters['on_musaned'] !== '') $q->where('on_musaned', (bool) $filters['on_musaned']);
        if (!empty($filters['date_from']))    $q->whereDate('created_at', '>=', $filters['date_from']);
        if (!empty($filters['date_to']))      $q->whereDate('created_at', '<=', $filters['date_to']);

        return $q->orderByDesc('created_at')->paginate(20)->withQueryString();
    }

    public function findById(int $id)
    {
        return Complaint::with([
            'contract', 'client', 'worker', 'branch',
            'assignedAdmin', 'createdBy', 'attachments',
        ])->findOrFail($id);
    }

    public function create(array $data)
    {
        return Complaint::create($data);
    }

    public function update(int $id, array $data)
    {
        $c = Complaint::findOrFail($id);
        $c->update($data);
        return $c;
    }

    public function delete(int $id): void
    {
        Complaint::findOrFail($id)->delete();
    }

    public function restore(int $id): void
    {
        Complaint::withTrashed()->findOrFail($id)->restore();
    }

    public function getTrashed()
    {
        return Complaint::onlyTrashed()->with('branch:id,name')->latest()->limit(50)->get();
    }

    public function getStaleNew(int $days = 7)
    {
        return Complaint::whereIn('status', ['new', 'in_progress'])
            ->where('created_at', '<=', now()->subDays($days))
            ->where(function ($q) {
                $q->whereNull('last_stale_notified_at')
                  ->orWhere('last_stale_notified_at', '<=', now()->subDays(1));
            })
            ->with(['branch', 'assignedAdmin'])
            ->get();
    }
}
