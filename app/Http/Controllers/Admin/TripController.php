<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Airport;
use App\Models\Branch;
use App\Models\Nationality;
use App\Models\RecruitmentContract;
use App\Models\Worker;
use App\Services\NotificationService;
use App\Services\TripService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TripController extends Controller
{
    public function __construct(
        private readonly TripService $service,
        private readonly NotificationService $notifications,
    ) {}

    private function branchFilter(): ?int
    {
        $me = Auth::guard('admin')->user();
        return ($me && $me->isBranchAdmin()) ? $me->branch_id : null;
    }

    public function index(Request $request)
    {
        $filters = $request->only('search', 'trip_type', 'status', 'date_from', 'date_to', 'worker_search');
        if ($bid = $this->branchFilter()) {
            $filters['branch_id'] = $bid;
        } elseif ($request->filled('branch_id')) {
            $filters['branch_id'] = $request->branch_id;
        }

        $trips    = $this->service->list($filters);
        $branches = Branch::where('active', true)->orderBy('name')->get();

        return view('admin.trips.index', compact('trips', 'branches'));
    }

    public function create()
    {
        $airports      = Airport::orderBy('name')->get();
        $branches      = Branch::where('active', true)->orderBy('name')->get();
        $nationalities = Nationality::where('active', true)->orderBy('name')->get();
        $branchId      = $this->branchFilter();
        return view('admin.trips.create', compact('airports', 'branches', 'nationalities', 'branchId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'trip_type'              => 'required|in:arrival,departure,group_transport,deportation',
            'trip_date'              => 'required|date',
            'trip_time'              => 'nullable|date_format:H:i',
            'airport_id'             => 'nullable|exists:airports,id',
            'origin_nationality_id'  => 'nullable|exists:nationalities,id',
            'flight_number'          => 'nullable|string|max:50',
            'branch_id'              => 'required|exists:branches,id',
            'notes'                  => 'nullable|string|max:1000',
        ]);

        if ($bid = $this->branchFilter()) {
            $data['branch_id'] = $bid;
        }

        $data['admin_id'] = Auth::guard('admin')->id();
        $data['status']   = 'scheduled';

        $trip = $this->service->create($data);

        $typeLabels = [
            'arrival'         => 'وصول',
            'departure'       => 'مغادرة',
            'group_transport' => 'نقل جماعي',
            'deportation'     => 'ترحيل',
        ];
        $this->notifications->notify(
            'trip_created',
            'تم إنشاء رحلة جديدة',
            'رحلة رقم ' . $trip->trip_number . ' — ' . ($typeLabels[$trip->trip_type] ?? $trip->trip_type),
            route('admin.trips.show', $trip->id),
            [$trip->branch_id]
        );

        return redirect()->route('admin.trips.show', $trip->id)
            ->with('success', 'تم إنشاء الرحلة بنجاح. يمكنك إضافة العاملات الآن.');
    }

    public function show(int $id)
    {
        $trip = $this->service->find($id);

        // Workers already in this trip
        $assignedWorkerIds = $trip->workers->pluck('id');

        // Contracts ready for arrival (status 11 or 12), same branch, worker not yet in trip
        $contractsQuery = RecruitmentContract::with(['client', 'worker.nationality', 'originNationality'])
            ->where('branch_id', $trip->branch_id)
            ->whereIn('current_status', [11, 12])
            ->whereNotNull('worker_id')
            ->whereNotIn('worker_id', $assignedWorkerIds);

        // Filter by origin nationality if set on the trip
        if ($trip->origin_nationality_id) {
            $contractsQuery->where('origin_nationality_id', $trip->origin_nationality_id);
        }

        $contracts = $contractsQuery->orderBy('id')->get();

        $nationalities = Nationality::where('active', true)->orderBy('name')->get();

        return view('admin.trips.show', compact('trip', 'contracts', 'nationalities'));
    }

    public function edit(int $id)
    {
        $trip          = $this->service->find($id);
        $airports      = Airport::orderBy('name')->get();
        $branches      = Branch::where('active', true)->orderBy('name')->get();
        $nationalities = Nationality::where('active', true)->orderBy('name')->get();
        return view('admin.trips.edit', compact('trip', 'airports', 'branches', 'nationalities'));
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'trip_type'              => 'required|in:arrival,departure,group_transport,deportation',
            'trip_date'              => 'required|date',
            'trip_time'              => 'nullable|date_format:H:i',
            'airport_id'             => 'nullable|exists:airports,id',
            'origin_nationality_id'  => 'nullable|exists:nationalities,id',
            'flight_number'          => 'nullable|string|max:50',
            'branch_id'              => 'required|exists:branches,id',
            'notes'                  => 'nullable|string|max:1000',
        ]);

        $this->service->update($id, $data);
        return redirect()->route('admin.trips.show', $id)->with('success', 'تم تحديث بيانات الرحلة.');
    }

    public function destroy(int $id)
    {
        $this->service->delete($id);
        return redirect()->route('admin.trips.index')->with('success', 'تم حذف الرحلة.');
    }

    public function addWorker(Request $request, int $tripId)
    {
        $request->validate([
            'worker_id'   => 'required|exists:workers,id',
            'contract_id' => 'nullable|exists:recruitment_contracts,id',
            'notes'       => 'nullable|string|max:500',
        ]);

        $trip = $this->service->find($tripId);
        $this->service->addWorker($trip, $request->worker_id, $request->contract_id, $request->notes);
        return back()->with('success', 'تم إضافة العاملة إلى الرحلة.');
    }

    public function addWorkersBulk(Request $request, int $tripId)
    {
        $request->validate([
            'contract_ids'   => 'required|array|min:1',
            'contract_ids.*' => 'exists:recruitment_contracts,id',
        ]);

        $trip = $this->service->find($tripId);
        $added = 0;

        foreach ($request->contract_ids as $contractId) {
            $contract = RecruitmentContract::with('worker')->find($contractId);
            if ($contract && $contract->worker_id) {
                // Skip if already in trip
                if ($trip->workers()->where('worker_id', $contract->worker_id)->exists()) {
                    continue;
                }
                $this->service->addWorker($trip, $contract->worker_id, $contract->id, null);
                $added++;
            }
        }

        return back()->with('success', "تم إضافة {$added} عاملة إلى الرحلة.");
    }

    public function removeWorker(int $tripId, int $workerId)
    {
        $trip = $this->service->find($tripId);
        $this->service->removeWorker($trip, $workerId);
        return back()->with('success', 'تم إزالة العاملة من الرحلة.');
    }

    public function complete(int $id)
    {
        $this->service->complete($id);
        return back()->with('success', 'تم تأكيد اكتمال الرحلة.');
    }

    public function showChecklist(int $id)
    {
        $trip = $this->service->find($id);
        return view('admin.trips.checklist', compact('trip'));
    }

    public function submitChecklist(Request $request, int $id)
    {
        $trip = $this->service->find($id);

        $statuses = $request->input('statuses', []);

        // Update each worker's pivot status
        foreach ($trip->workers as $worker) {
            $status = $statuses[$worker->id] ?? 'completed';
            $trip->workers()->updateExistingPivot($worker->id, ['status' => $status]);
        }

        // Complete the trip
        $this->service->complete($id);

        // Collect workers with no_show
        $noShowWorkers = $trip->workers->filter(fn($w) => ($statuses[$w->id] ?? 'completed') === 'no_show');

        if ($noShowWorkers->isNotEmpty()) {
            $names = $noShowWorkers->map(fn($w) => $w->name)->implode('، ');
            $this->notifications->notify(
                type: 'trip_issue',
                title: 'تنبيه: عاملات لم تظهر في رحلة ' . $trip->trip_number,
                body: 'العاملات التاليات لم يظهرن في الرحلة: ' . $names,
                url: route('admin.trips.show', $trip->id),
                branchIds: $trip->branch_id ? [$trip->branch_id] : [],
            );
        }

        return redirect()->route('admin.trips.show', $trip->id)
            ->with('success', 'تم تأكيد اكتمال الرحلة بنجاح.');
    }

    public function print(int $id)
    {
        $trip = $this->service->find($id);
        return view('admin.trips.print', compact('trip'));
    }
}
