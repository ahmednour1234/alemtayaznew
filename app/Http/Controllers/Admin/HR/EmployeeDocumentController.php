<?php

namespace App\Http\Controllers\Admin\HR;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\EmployeeDocument;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmployeeDocumentController extends Controller
{
    public function __construct(private readonly NotificationService $notifications) {}

    private function branchFilter(): ?int
    {
        $me = Auth::guard('admin')->user();
        return ($me && $me->isBranchAdmin()) ? $me->branch_id : null;
    }

    public function index(Request $request)
    {
        $query = EmployeeDocument::query()->with(['employee', 'branch', 'admin']);

        if ($bid = $this->branchFilter()) {
            $query->where('branch_id', $bid);
        } elseif ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->input('scope') === 'company') {
            $query->whereNull('employee_id');
        }
        if ($s = $request->input('search')) {
            $query->where(fn($q) => $q->where('title', 'like', "%{$s}%")->orWhere('doc_type', 'like', "%{$s}%"));
        }

        $documents = $query->latest()->paginate(20)->withQueryString();
        $branches  = Branch::where('active', true)->orderBy('name')->get();
        $employees = Employee::orderBy('name')->get(['id', 'name']);

        return view('admin.hr.documents.index', compact('documents', 'branches', 'employees'));
    }

    public function create()
    {
        $branches  = Branch::where('active', true)->orderBy('name')->get();
        $employees = Employee::orderBy('name')->get(['id', 'name']);
        return view('admin.hr.documents.create', compact('branches', 'employees'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'nullable|exists:employees,id',
            'title'       => 'required|string|max:191',
            'doc_type'    => 'nullable|string|max:100',
            'issue_date'  => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'notes'       => 'nullable|string|max:1000',
            'branch_id'   => 'nullable|exists:branches,id',
            'file'        => 'required|file|max:20480|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx',
        ]);

        $file = $request->file('file');
        // Private storage — stored on the "local" disk, never publicly served.
        $path = $file->store('employee-documents', EmployeeDocument::DISK);

        if ($bid = $this->branchFilter()) {
            $data['branch_id'] = $bid;
        }

        $doc = EmployeeDocument::create([
            'employee_id'   => $data['employee_id'] ?? null,
            'title'         => $data['title'],
            'doc_type'      => $data['doc_type'] ?? null,
            'file_path'     => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $file->getClientMimeType(),
            'size'          => $file->getSize(),
            'issue_date'    => $data['issue_date'] ?? null,
            'expiry_date'   => $data['expiry_date'] ?? null,
            'notes'         => $data['notes'] ?? null,
            'branch_id'     => $data['branch_id'] ?? null,
            'admin_id'      => Auth::guard('admin')->id(),
        ]);

        $this->notifications->notify(
            'employee_document_uploaded',
            'وثيقة جديدة',
            'تم رفع الوثيقة «' . $doc->title . '»' . ($doc->employee ? ' للموظف ' . $doc->employee->name : ''),
            route('admin.hr.documents.index'),
            $doc->branch_id ? [$doc->branch_id] : []
        );

        return redirect()->route('admin.hr.documents.index')
            ->with('success', 'تم رفع الوثيقة بنجاح.');
    }

    public function edit(EmployeeDocument $document)
    {
        $branches  = Branch::where('active', true)->orderBy('name')->get();
        $employees = Employee::orderBy('name')->get(['id', 'name']);
        return view('admin.hr.documents.edit', compact('document', 'branches', 'employees'));
    }

    public function update(Request $request, EmployeeDocument $document)
    {
        $data = $request->validate([
            'employee_id' => 'nullable|exists:employees,id',
            'title'       => 'required|string|max:191',
            'doc_type'    => 'nullable|string|max:100',
            'issue_date'  => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'notes'       => 'nullable|string|max:1000',
            'branch_id'   => 'nullable|exists:branches,id',
            'file'        => 'nullable|file|max:20480|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx',
        ]);

        if ($request->hasFile('file')) {
            Storage::disk(EmployeeDocument::DISK)->delete($document->file_path);
            $file = $request->file('file');
            $data['file_path']     = $file->store('employee-documents', EmployeeDocument::DISK);
            $data['original_name'] = $file->getClientOriginalName();
            $data['mime_type']     = $file->getClientMimeType();
            $data['size']          = $file->getSize();
        }

        unset($data['file']);
        $document->update($data);

        return redirect()->route('admin.hr.documents.index')
            ->with('success', 'تم تحديث الوثيقة.');
    }

    public function destroy(EmployeeDocument $document)
    {
        Storage::disk(EmployeeDocument::DISK)->delete($document->file_path);
        $document->delete();

        return redirect()->route('admin.hr.documents.index')
            ->with('success', 'تم حذف الوثيقة.');
    }

    /** Stream the private file (never publicly accessible — permission-gated route). */
    public function download(EmployeeDocument $document): StreamedResponse
    {
        $disk = Storage::disk(EmployeeDocument::DISK);
        abort_unless($disk->exists($document->file_path), 404);

        return $disk->download($document->file_path, $document->original_name ?: basename($document->file_path));
    }
}
