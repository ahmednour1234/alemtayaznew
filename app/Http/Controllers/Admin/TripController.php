<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Airport;
use App\Models\Branch;
use App\Models\Worker;
use App\Services\TripService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TripController extends Controller
{
    public function __construct(private readonly TripService $service) {}

    private function branchFilter(): ?int
    {
        $me = Auth::guard('admin')->user();
        return ($me && $me->isBranchAdmin()) ? $me->branch_id : null;
    }

    public function index(Request $request)
    {
        $filters = $request->only('search', 'trip_type', 'status', 'date_from', 'date_to');
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
        $airports = Airport::orderBy('name')->get();
        $branches = Branch::where('active', true)->orderBy('name')->get();
        $branchId = $this->branchFilter();
        return view('admin.trips.create', compact('airports', 'branches', 'branchId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'trip_type'     => 'required|in:arrival,departure,group_transport,deportation',
            'trip_date'     => 'required|date',
            'trip_time'     => 'nullable|date_format:H:i',
            'airport_id'    => 'nullable|exists:airports,id',
            'flight_number' => 'nullable|string|max:50',
            'branch_id'     => 'required|exists:branches,id',
            'notes'         => 'nullable|string|max:1000',
        ]);

        if ($bid = $this->branchFilter()) {
            $data['branch_id'] = $bid;
        }

        $data['admin_id'] = Auth::guard('admin')->id();
        $data['status']   = 'scheduled';

        $trip = $this->service->create($data);
        return redirect()->route('admin.trips.show', $trip->id)
            ->with('success', 'تم إنشاء الرحلة بنجاح. يمكنك إضافة العاملات الآن.');
    }

    public function show(int $id)
    {
        $trip    = $this->service->find($id);
        $workers = Worker::where('active', true)
            ->when($trip->branch_id, fn($q) => $q->where('branch_id', $trip->branch_id))
            ->orderBy('name')->get();
        return view('admin.trips.show', compact('trip', 'workers'));
    }

    public function edit(int $id)
    {
        $trip     = $this->service->find($id);
        $airports = Airport::orderBy('name')->get();
        $branches = Branch::where('active', true)->orderBy('name')->get();
        return view('admin.trips.edit', compact('trip', 'airports', 'branches'));
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'trip_type'     => 'required|in:arrival,departure,group_transport,deportation',
            'trip_date'     => 'required|date',
            'trip_time'     => 'nullable|date_format:H:i',
            'airport_id'    => 'nullable|exists:airports,id',
            'flight_number' => 'nullable|string|max:50',
            'branch_id'     => 'required|exists:branches,id',
            'notes'         => 'nullable|string|max:1000',
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

    public function removeWorker(int $tripId, int $workerId)
    {
        $trip = $this->service->find($tripId);
        $this->service->removeWorker($trip, $workerId);
        return back()->with('success', 'تم إزالة العاملة من الرحلة.');
    }

    public function complete(int $id)
    {
        $this->service->complete($id);
        return back()->with('success', 'تم تأكيل اكتمال الرحلة.');
    }

    public function print(int $id)
    {
        $trip = $this->service->find($id);
        return view('admin.trips.print', compact('trip'));
    }
}
