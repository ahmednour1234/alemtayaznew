<?php

namespace App\Http\Controllers\Admin\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeLeave;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class EmployeeLeaveController extends Controller
{
    public function __construct(private readonly NotificationService $notifications) {}

    public function index(Request $request)
    {
        $query = EmployeeLeave::query()->with(['employee', 'approver']);

        $me = Auth::guard('admin')->user();
        if ($me->isBranchAdmin()) {
            $query->whereHas('employee', fn($q) => $q->where('branch_id', $me->branch_id));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $leaves    = $query->latest('start_date')->paginate(20)->withQueryString();
        $employees = Employee::orderBy('name')->get(['id', 'name']);

        return view('admin.hr.leaves.index', compact('leaves', 'employees'));
    }

    public function create(Request $request)
    {
        $employees = Employee::orderBy('name')->get(['id', 'name']);
        $selected  = $request->input('employee_id');
        return view('admin.hr.leaves.create', compact('employees', 'selected'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['days']     = $this->computeDays($data['start_date'], $data['end_date']);
        $data['admin_id'] = Auth::guard('admin')->id();
        $data['status']   = 'pending';

        $leave = EmployeeLeave::create($data);
        $employee = $leave->employee;

        $this->notifications->notify(
            'employee_leave_created',
            'طلب إجازة جديد',
            'طلب إجازة لـ ' . ($employee?->name ?? '') . ' من ' . $leave->start_date->format('Y-m-d') . ' إلى ' . $leave->end_date->format('Y-m-d'),
            route('admin.hr.leaves.index'),
            $employee?->branch_id ? [$employee->branch_id] : []
        );

        return redirect()->route('admin.hr.leaves.index')->with('success', 'تم تسجيل طلب الإجازة.');
    }

    public function edit(EmployeeLeave $leave)
    {
        $employees = Employee::orderBy('name')->get(['id', 'name']);
        return view('admin.hr.leaves.edit', compact('leave', 'employees'));
    }

    public function update(Request $request, EmployeeLeave $leave)
    {
        $data = $this->validateData($request);
        $data['days'] = $this->computeDays($data['start_date'], $data['end_date']);
        $leave->update($data);

        return redirect()->route('admin.hr.leaves.index')->with('success', 'تم تحديث الإجازة.');
    }

    public function destroy(EmployeeLeave $leave)
    {
        $leave->delete();
        return redirect()->route('admin.hr.leaves.index')->with('success', 'تم حذف الإجازة.');
    }

    public function decide(Request $request, EmployeeLeave $leave)
    {
        $request->validate(['decision' => 'required|in:approved,rejected']);

        $leave->update([
            'status'      => $request->decision,
            'approved_by' => Auth::guard('admin')->id(),
            'decided_at'  => now(),
        ]);

        $employee = $leave->employee;
        $label = $request->decision === 'approved' ? 'اعتماد' : 'رفض';

        $this->notifications->notify(
            'employee_leave_decided',
            'تحديث حالة إجازة',
            'تم ' . $label . ' إجازة ' . ($employee?->name ?? ''),
            route('admin.hr.leaves.index'),
            $employee?->branch_id ? [$employee->branch_id] : []
        );

        return back()->with('success', 'تم تحديث حالة الإجازة.');
    }

    private function computeDays(string $start, string $end): int
    {
        return Carbon::parse($start)->diffInDays(Carbon::parse($end)) + 1;
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'type'        => 'required|in:annual,sick,unpaid,emergency,other',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'reason'      => 'nullable|string|max:1000',
        ]);
    }
}
