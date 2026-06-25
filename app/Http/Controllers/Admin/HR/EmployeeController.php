<?php

namespace App\Http\Controllers\Admin\HR;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Employee;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function __construct(private readonly NotificationService $notifications) {}

    private function branchFilter(): ?int
    {
        $me = Auth::guard('admin')->user();
        return ($me && $me->isBranchAdmin()) ? $me->branch_id : null;
    }

    public function index(Request $request)
    {
        $query = Employee::query()->with('branch');

        if ($bid = $this->branchFilter()) {
            $query->where('branch_id', $bid);
        } elseif ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('transferred_in')) {
            $query->where('sponsorship_transferred_in', $request->transferred_in === '1');
        }
        if ($s = $request->input('search')) {
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('employee_no', 'like', "%{$s}%")
                  ->orWhere('iqama_number', 'like', "%{$s}%");
            });
        }

        $employees = $query->orderBy('name')->paginate(20)->withQueryString();
        $branches  = Branch::where('active', true)->orderBy('name')->get();

        return view('admin.hr.employees.index', compact('employees', 'branches'));
    }

    public function create()
    {
        $branches = Branch::where('active', true)->orderBy('name')->get();
        return view('admin.hr.employees.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['admin_id'] = Auth::guard('admin')->id();
        if ($bid = $this->branchFilter()) {
            $data['branch_id'] = $bid;
        }

        $employee = Employee::create($data);

        $this->notifications->notify(
            'employee_created',
            'موظف جديد',
            'تمت إضافة الموظف ' . $employee->name,
            route('admin.hr.employees.show', $employee->id),
            $employee->branch_id ? [$employee->branch_id] : []
        );

        return redirect()->route('admin.hr.employees.show', $employee->id)
            ->with('success', 'تمت إضافة الموظف بنجاح.');
    }

    public function show(Employee $employee)
    {
        $employee->load(['branch', 'documents', 'leaves.approver', 'insurances']);
        return view('admin.hr.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $branches = Branch::where('active', true)->orderBy('name')->get();
        return view('admin.hr.employees.edit', compact('employee', 'branches'));
    }

    public function update(Request $request, Employee $employee)
    {
        $data = $this->validateData($request, $employee->id);
        if ($bid = $this->branchFilter()) {
            $data['branch_id'] = $bid;
        }
        $employee->update($data);

        return redirect()->route('admin.hr.employees.show', $employee->id)
            ->with('success', 'تم تحديث بيانات الموظف.');
    }

    public function destroy(Employee $employee)
    {
        $name = $employee->name;
        $employee->delete();

        $this->notifications->notify(
            'employee_deleted',
            'تم حذف موظف',
            'حذف ' . Auth::guard('admin')->user()->name . ' الموظف ' . $name,
            route('admin.hr.employees.index'),
            $employee->branch_id ? [$employee->branch_id] : []
        );

        return redirect()->route('admin.hr.employees.index')
            ->with('success', 'تم حذف الموظف.');
    }

    private function validateData(Request $request, ?int $id = null): array
    {
        $data = $request->validate([
            'name'                       => 'required|string|max:191',
            'employee_no'                => 'nullable|string|max:50|unique:employees,employee_no' . ($id ? ",{$id}" : ''),
            'iqama_number'               => 'nullable|string|max:50',
            'iqama_expiry_date'          => 'nullable|date',
            'job_title'                  => 'nullable|string|max:191',
            'phone'                      => 'nullable|string|max:30',
            'email'                      => 'nullable|email|max:191',
            'hire_date'                  => 'nullable|date',
            'status'                     => 'required|in:probation,active,terminated',
            'probation_end_date'         => 'nullable|date',
            'sponsorship_transferred_in' => 'nullable|boolean',
            'previous_sponsor'           => 'nullable|string|max:191',
            'sponsorship_transfer_date'  => 'nullable|date',
            'sponsorship_notes'          => 'nullable|string|max:1000',
            'branch_id'                  => 'nullable|exists:branches,id',
            'notes'                      => 'nullable|string|max:1000',
            'active'                     => 'nullable|boolean',
        ]);

        $data['sponsorship_transferred_in'] = $request->boolean('sponsorship_transferred_in');
        $data['active'] = $request->boolean('active', true);

        return $data;
    }
}
