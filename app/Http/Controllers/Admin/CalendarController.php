<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Services\TripService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    public function __construct(private readonly TripService $tripService) {}

    public function index()
    {
        $branches = Branch::where('active', true)->orderBy('name')->get();
        return view('admin.calendar.index', compact('branches'));
    }

    public function events(Request $request): JsonResponse
    {
        $month    = $request->input('month', now()->format('Y-m'));
        $branchId = $this->resolveBranch($request);

        $trips = $this->tripService->forCalendar($branchId, $month);

        $events = $trips->map(fn($trip) => [
            'id'         => 'trip-' . $trip->id,
            'title'      => $trip->type_label . ' — ' . ($trip->airport?->name ?? ''),
            'start'      => $trip->trip_date,
            'color'      => $trip->type_color,
            'url'        => route('admin.trips.show', $trip->id),
            'extendedProps' => [
                'workers' => $trip->workers_count ?? 0,
                'status'  => $trip->status_label,
                'branch'  => $trip->branch?->name,
            ],
        ]);

        return response()->json($events);
    }

    private function resolveBranch(Request $request): ?int
    {
        $me = Auth::guard('admin')->user();
        if ($me && $me->isBranchAdmin()) {
            return $me->branch_id;
        }
        return $request->filled('branch_id') ? (int) $request->branch_id : null;
    }
}
