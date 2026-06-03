<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminNotification;
use App\Models\Branch;
use App\Models\Housing;
use App\Models\HousingVisit;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class HousingVisitController extends Controller
{
    public function __construct(private readonly NotificationService $notifications) {}

    private function branchFilter(): ?int
    {
        $me = Auth::guard('admin')->user();
        return ($me && $me->isBranchAdmin()) ? $me->branch_id : null;
    }

    public function index(Request $request)
    {
        $branchId = $this->branchFilter();
        $visits = $this->baseQuery($request)
            ->latest('visit_date')
            ->latest()
            ->paginate(20);

        [$branches, $housings, $employees] = $this->filtersData($branchId);

        return view('admin.housing-visits.index', compact('visits', 'branches', 'housings', 'employees', 'branchId'));
    }

    public function create()
    {
        $branchId = $this->branchFilter();
        [$branches, $housings, $employees] = $this->filtersData($branchId);

        return view('admin.housing-visits.create', compact('branches', 'housings', 'employees', 'branchId'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        if ($request->hasFile('documentation_file')) {
            $data['documentation_file'] = $request->file('documentation_file')
                ->store('housing-visits', 'public');
        }

        $employeeIds = $data['employee_ids'];
        unset($data['employee_ids']);

        $data['admin_id'] = Auth::guard('admin')->id();
        $visit = HousingVisit::create($data);
        $visit->employees()->sync($employeeIds);

        $this->sendVisitNotifications($visit, 'created');

        return redirect()->route('admin.housing-visits.show', $visit)
            ->with('success', 'تم تسجيل زيارة السكن وإرسال الإشعارات.');
    }

    public function show(HousingVisit $housingVisit)
    {
        $this->authorizeBranch($housingVisit);
        $housingVisit->load(['branch', 'housing', 'admin', 'employees.branch']);

        return view('admin.housing-visits.show', ['visit' => $housingVisit]);
    }

    public function edit(HousingVisit $housingVisit)
    {
        $this->authorizeBranch($housingVisit);
        $branchId = $this->branchFilter();
        [$branches, $housings, $employees] = $this->filtersData($branchId ?: $housingVisit->branch_id);
        $housingVisit->load('employees');

        return view('admin.housing-visits.edit', [
            'visit' => $housingVisit,
            'branches' => $branches,
            'housings' => $housings,
            'employees' => $employees,
            'branchId' => $branchId,
        ]);
    }

    public function update(Request $request, HousingVisit $housingVisit)
    {
        $this->authorizeBranch($housingVisit);
        $data = $this->validatedData($request, $housingVisit);

        if ($request->hasFile('documentation_file')) {
            if ($housingVisit->documentation_file) {
                Storage::disk('public')->delete($housingVisit->documentation_file);
            }
            $data['documentation_file'] = $request->file('documentation_file')
                ->store('housing-visits', 'public');
        }

        $employeeIds = $data['employee_ids'];
        unset($data['employee_ids']);

        $housingVisit->update($data);
        $housingVisit->employees()->sync($employeeIds);

        $this->sendVisitNotifications($housingVisit, 'updated');

        return redirect()->route('admin.housing-visits.show', $housingVisit)
            ->with('success', 'تم تحديث زيارة السكن وإرسال الإشعارات.');
    }

    public function destroy(HousingVisit $housingVisit)
    {
        $this->authorizeBranch($housingVisit);
        $housingVisit->delete();

        return redirect()->route('admin.housing-visits.index')
            ->with('success', 'تم حذف زيارة السكن.');
    }

    public function reports(Request $request)
    {
        $branchId = $this->branchFilter();
        $query = $this->baseQuery($request);

        $total = (clone $query)->count();
        $withDocumentation = (clone $query)->where(function ($q) {
            $q->whereNotNull('documentation')
              ->orWhereNotNull('documentation_file');
        })->count();
        $visitingEmployees = Admin::whereHas('housingVisits', fn($q) => $q->whereIn('housing_visits.id', (clone $query)->pluck('id')))
            ->count();
        $byBranch = (clone $query)
            ->selectRaw('branch_id, count(*) as visits_count')
            ->with('branch')
            ->groupBy('branch_id')
            ->get();
        $visits = (clone $query)->latest('visit_date')->with(['branch', 'housing', 'employees'])->paginate(25);

        [$branches, $housings, $employees] = $this->filtersData($branchId);

        return view('admin.housing-visits.reports', compact(
            'total',
            'withDocumentation',
            'visitingEmployees',
            'byBranch',
            'visits',
            'branches',
            'housings',
            'employees',
            'branchId'
        ));
    }

    private function baseQuery(Request $request)
    {
        $branchId = $this->branchFilter();

        return HousingVisit::query()
            ->with(['branch', 'housing', 'admin', 'employees'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when(! $branchId && $request->filled('branch_id'), fn($q) => $q->where('branch_id', $request->branch_id))
            ->when($request->filled('housing_id'), fn($q) => $q->where('housing_id', $request->housing_id))
            ->when($request->filled('employee_id'), fn($q) => $q->whereHas('employees', fn($e) => $e->where('admins.id', $request->employee_id)))
            ->when($request->filled('date_from'), fn($q) => $q->whereDate('visit_date', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn($q) => $q->whereDate('visit_date', '<=', $request->date_to));
    }

    private function filtersData(?int $branchId): array
    {
        $branches = Branch::where('active', true)->orderBy('name')->get();
        $housings = Housing::where('active', true)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->with('branch')
            ->orderBy('name')
            ->get();
        $employees = Admin::where('active', true)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->with('branch')
            ->orderBy('name')
            ->get();

        return [$branches, $housings, $employees];
    }

    private function validatedData(Request $request, ?HousingVisit $visit = null): array
    {
        $data = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'housing_id' => 'required|exists:housings,id',
            'employee_ids' => 'required|array|min:1',
            'employee_ids.*' => 'required|exists:admins,id',
            'visit_date' => 'required|date',
            'documentation' => 'nullable|string|max:2000',
            'documentation_file' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
            'branch_employee_notes' => 'nullable|string|max:2000',
        ]);

        if ($branchId = $this->branchFilter()) {
            $data['branch_id'] = $branchId;
        }

        $housingMatchesBranch = Housing::whereKey($data['housing_id'])
            ->where('branch_id', $data['branch_id'])
            ->exists();

        if (! $housingMatchesBranch) {
            throw ValidationException::withMessages([
                'housing_id' => 'السكن المختار لا يتبع الفرع المحدد.',
            ]);
        }

        $selectedEmployeesCount = Admin::whereIn('id', $data['employee_ids'])
            ->where('branch_id', $data['branch_id'])
            ->count();

        if ($selectedEmployeesCount !== count(array_unique($data['employee_ids']))) {
            throw ValidationException::withMessages([
                'employee_ids' => 'يجب اختيار موظفين تابعين لنفس الفرع.',
            ]);
        }

        return $data;
    }

    private function authorizeBranch(HousingVisit $visit): void
    {
        if (($branchId = $this->branchFilter()) && $visit->branch_id !== $branchId) {
            abort(403);
        }
    }

    private function sendVisitNotifications(HousingVisit $visit, string $event): void
    {
        $visit->load(['branch', 'housing', 'employees']);

        $title = $event === 'created' ? 'زيارة سكن جديدة' : 'تحديث زيارة سكن';
        $body = sprintf(
            '%s للسكن %s في فرع %s بتاريخ %s',
            $event === 'created' ? 'تم تسجيل زيارة' : 'تم تحديث زيارة',
            $visit->housing?->name ?? '—',
            $visit->branch?->name ?? '—',
            $visit->visit_date?->format('Y-m-d')
        );
        $url = route('admin.housing-visits.show', $visit);

        $managementIds = Admin::where('active', true)
            ->with('roles')
            ->get()
            ->filter(fn(Admin $admin) =>
                $admin->isSuperAdmin()
                || $admin->department === 'chairman'
                || ($admin->department === 'branch_manager' && (int) $admin->branch_id === (int) $visit->branch_id)
            )
            ->pluck('id');

        $recipientIds = $visit->employees->pluck('id')
            ->merge($managementIds)
            ->unique()
            ->values();

        foreach ($recipientIds as $adminId) {
            $this->notifications->notifyOne((int) $adminId, 'housing_visit_' . $event, $title, $body, $url);
        }
    }
}
