<?php

namespace App\Repositories;

use App\Models\Trip;
use App\Repositories\Contracts\TripRepositoryInterface;
use Carbon\Carbon;

class TripRepository implements TripRepositoryInterface
{
    public function getAll(array $filters = [])
    {
        return Trip::with(['airport', 'branch', 'admin'])
            ->withCount('workers')
            ->when(!empty($filters['branch_id']),   fn($q) => $q->where('branch_id', $filters['branch_id']))
            ->when(!empty($filters['trip_type']),   fn($q) => $q->where('trip_type', $filters['trip_type']))
            ->when(!empty($filters['status']),      fn($q) => $q->where('status', $filters['status']))
            ->when(!empty($filters['date_from']),   fn($q) => $q->whereDate('trip_date', '>=', $filters['date_from']))
            ->when(!empty($filters['date_to']),     fn($q) => $q->whereDate('trip_date', '<=', $filters['date_to']))
            ->when(!empty($filters['search']),      fn($q) =>
                $q->where('trip_number', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('flight_number', 'like', '%' . $filters['search'] . '%')
            )
            ->when(!empty($filters['worker_search']), fn($q) =>
                $q->whereHas('workers', fn($wq) =>
                    $wq->where('name', 'like', '%' . $filters['worker_search'] . '%')
                       ->orWhere('passport_number', 'like', '%' . $filters['worker_search'] . '%')
                       ->orWhere('file_number', 'like', '%' . $filters['worker_search'] . '%')
                )
            )
            ->orderByDesc('trip_date')
            ->paginate(20);
    }

    public function findById(int $id)
    {
        return Trip::with(['airport', 'branch', 'admin', 'workers.nationality', 'workers' => fn($q) => $q->withPivot('contract_id','notes','status')])
            ->findOrFail($id);
    }

    public function create(array $data)
    {
        return Trip::create($data);
    }

    public function update(int $id, array $data)
    {
        $trip = Trip::findOrFail($id);
        $trip->update($data);
        return $trip;
    }

    public function delete(int $id): bool
    {
        return (bool) Trip::findOrFail($id)->delete();
    }

    public function getUpcoming(?int $branchId, int $days = 7)
    {
        return Trip::with(['airport', 'branch'])
            ->withCount('workers')
            ->where('status', 'scheduled')
            ->whereDate('trip_date', '>=', Carbon::today())
            ->whereDate('trip_date', '<=', Carbon::today()->addDays($days))
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->orderBy('trip_date')
            ->get();
    }

    public function getForCalendar(?int $branchId, string $month)
    {
        [$year, $mon] = explode('-', $month);
        return Trip::with(['airport', 'branch'])
            ->withCount('workers')
            ->whereYear('trip_date', $year)
            ->whereMonth('trip_date', $mon)
            ->where('status', '!=', 'cancelled')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->orderBy('trip_date')
            ->get();
    }
}
