<?php

namespace App\Http\Controllers\Admin\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeMedicalInsurance;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeMedicalInsuranceController extends Controller
{
    public function __construct(private readonly NotificationService $notifications) {}

    public function index(Request $request)
    {
        $query = EmployeeMedicalInsurance::query()->with('employee');

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

        $insurances = $query->latest('end_date')->paginate(20)->withQueryString();
        $employees  = Employee::orderBy('name')->get(['id', 'name']);

        return view('admin.hr.insurances.index', compact('insurances', 'employees'));
    }

    public function create(Request $request)
    {
        $employees = Employee::orderBy('name')->get(['id', 'name']);
        $selected  = $request->input('employee_id');
        return view('admin.hr.insurances.create', compact('employees', 'selected'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['admin_id'] = Auth::guard('admin')->id();

        $insurance = EmployeeMedicalInsurance::create($data);
        $employee  = $insurance->employee;

        $this->notifications->notify(
            'employee_insurance_created',
            'تأمين طبي جديد',
            'تم تسجيل تأمين طبي للموظف ' . ($employee?->name ?? '') . ' لدى ' . $insurance->provider,
            route('admin.hr.insurances.index'),
            $employee?->branch_id ? [$employee->branch_id] : []
        );

        return redirect()->route('admin.hr.insurances.index')->with('success', 'تم تسجيل التأمين الطبي.');
    }

    public function edit(EmployeeMedicalInsurance $insurance)
    {
        $employees = Employee::orderBy('name')->get(['id', 'name']);
        return view('admin.hr.insurances.edit', compact('insurance', 'employees'));
    }

    public function update(Request $request, EmployeeMedicalInsurance $insurance)
    {
        $insurance->update($this->validateData($request));
        return redirect()->route('admin.hr.insurances.index')->with('success', 'تم تحديث التأمين الطبي.');
    }

    public function destroy(EmployeeMedicalInsurance $insurance)
    {
        $insurance->delete();
        return redirect()->route('admin.hr.insurances.index')->with('success', 'تم حذف التأمين الطبي.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'employee_id'   => 'required|exists:employees,id',
            'provider'      => 'required|string|max:191',
            'policy_number' => 'nullable|string|max:100',
            'class'         => 'nullable|string|max:100',
            'start_date'    => 'nullable|date',
            'end_date'      => 'nullable|date|after_or_equal:start_date',
            'cost'          => 'nullable|numeric|min:0',
            'status'        => 'required|in:active,expired,cancelled',
            'notes'         => 'nullable|string|max:1000',
        ]);
    }
}
