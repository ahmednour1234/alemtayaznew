<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRecruitmentContractRequest;
use App\Http\Requests\Admin\UpdateRecruitmentContractRequest;
use App\Exports\ContractExport;
use App\Exports\ContractTemplateExport;
use App\Imports\ContractImport;
use App\Models\Agent;
use App\Models\Airport;
use App\Models\Branch;
use App\Models\Client;
use App\Models\Nationality;
use App\Models\RecruitmentContract;
use App\Models\Worker;
use App\Services\RecruitmentContractService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class RecruitmentContractController extends Controller
{
    public function __construct(private readonly RecruitmentContractService $service) {}

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function me(): \App\Models\Admin
    {
        return Auth::guard('admin')->user();
    }

    private function branchFilter(): ?int
    {
        $me = $this->me();
        return $me->isSuperAdmin() ? null : $me->branch_id;
    }

    private function formData(): array
    {
        return [
            'clients'       => Client::where('active', true)->orderBy('name')->get(['id', 'name', 'phone']),
            'branches'      => Branch::where('active', true)->orderBy('name')->get(['id', 'name']),
            'workers'       => Worker::where('active', true)->where('status', 'available')->orderBy('name')->get(['id', 'name', 'nationality_id']),
            'agents'        => Agent::where('active', true)->orderBy('name')->get(['id', 'name']),
            'airports'      => Airport::where('active', true)->orderBy('name')->get(['id', 'name', 'code']),
            'nationalities' => Nationality::where('active', true)->orderBy('name')->get(['id', 'name']),
            'statuses'      => RecruitmentContract::statuses(),
            'visaTypes'     => RecruitmentContract::visaTypes(),
            'payStatuses'   => RecruitmentContract::paymentStatuses(),
            'departments'   => RecruitmentContract::departments(),
        ];
    }

    // ── CRUD ──────────────────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        $me       = $this->me();
        $filters  = $request->only(['search', 'department', 'status', 'payment_status', 'branch_id', 'nationality_id']);
        $branchId = $this->branchFilter();

        // Non-super-admin: restrict to their department view
        if (! $me->isSuperAdmin() && $me->department && ! isset($filters['department'])) {
            $dept = match ($me->department) {
                'customer_service' => 'customer_service',
                'accounts', 'accountant' => 'accounts',
                'coordination'     => 'coordination',
                default            => null,
            };
            if ($dept) $filters['department'] = $dept;
        }

        $contracts     = $this->service->getList($filters, $branchId);
        $statuses      = RecruitmentContract::statuses();
        $departments   = RecruitmentContract::departments();
        $nationalities = Nationality::where('active', true)->orderBy('name')->get(['id', 'name']);
        $branches      = Branch::where('active', true)->orderBy('name')->get(['id', 'name']);

        return view('admin.contracts.index', compact('contracts', 'statuses', 'departments', 'filters', 'nationalities', 'branches'));
    }

    public function create(): View
    {
        $me = $this->me();
        $data = $this->formData();
        // Pre-select branch for branch admins
        $data['defaultBranch'] = $me->branch_id;
        return view('admin.contracts.create', $data);
    }

    public function store(StoreRecruitmentContractRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['client_sms']    = $request->boolean('client_sms');
        $data['client_rating'] = $request->boolean('client_rating');

        $contract = $this->service->store($data);

        return redirect()->route('admin.contracts.show', $contract->id)
            ->with('success', "تم إنشاء العقد {$contract->contract_number} بنجاح");
    }

    public function show(int $id): View
    {
        $contract    = $this->service->findById($id);
        $statuses     = RecruitmentContract::statuses();
        $departments  = RecruitmentContract::departments();
        $historyMap   = $contract->statusHistories->keyBy('status');
        $activityLogs = $contract->activityLogs;

        return view('admin.contracts.show', compact('contract', 'statuses', 'departments', 'historyMap', 'activityLogs'));
    }

    public function edit(int $id): View
    {
        $contract    = $this->service->findById($id);
        $historyMap  = $contract->statusHistories->keyBy('status');
        $data        = $this->formData();
        // Also include the currently assigned worker even if they are 'assigned'
        if ($contract->worker_id) {
            $currentWorker = Worker::where('id', $contract->worker_id)->get(['id', 'name', 'nationality_id']);
            $data['workers'] = $data['workers']->merge($currentWorker)->unique('id')->sortBy('name')->values();
        }
        $data['contract']   = $contract;
        $data['historyMap'] = $historyMap;

        return view('admin.contracts.edit', $data);
    }

    public function update(UpdateRecruitmentContractRequest $request, int $id): RedirectResponse
    {
        $contract = $this->service->findById($id);
        $data     = $request->validated();
        $data['client_sms']    = $request->boolean('client_sms');
        $data['client_rating'] = $request->boolean('client_rating');

        // Handle status update
        if ($request->filled('update_status')) {
            $this->service->updateStatus(
                $contract,
                (int) $request->update_status,
                $request->status_date,
                $request->whatsapp_message
            );
        }

        // Remove status fields from main update
        unset($data['update_status'], $data['status_date'], $data['whatsapp_message']);

        $this->service->update($contract, $data);

        return redirect()->route('admin.contracts.show', $id)
            ->with('success', 'تم تحديث العقد بنجاح');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->service->delete($id);
        return redirect()->route('admin.contracts.index')
            ->with('success', 'تم حذف العقد');
    }

    // ── Soft delete management ─────────────────────────────────────────────────

    public function trashed(Request $request): View
    {
        $branchId  = $this->branchFilter();
        $contracts = $this->service->getTrashed($branchId);
        return view('admin.contracts.trashed', compact('contracts'));
    }

    public function restore(int $id): RedirectResponse
    {
        $this->service->restore($id);
        return redirect()->route('admin.contracts.trashed')
            ->with('success', 'تم استعادة العقد بنجاح');
    }

    public function forceDelete(int $id): RedirectResponse
    {
        $this->service->forceDelete($id);
        return redirect()->route('admin.contracts.trashed')
            ->with('success', 'تم الحذف النهائي للعقد');
    }

    // ── Export / Import ────────────────────────────────────────────────────────

    public function export(Request $request)
    {
        $filters = $request->only(['branch_id', 'department', 'status', 'payment_status']);
        if ($this->branchFilter()) {
            $filters['branch_id'] = $this->branchFilter();
        }
        return Excel::download(new ContractExport($filters), 'عقود-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function template()
    {
        return Excel::download(new ContractTemplateExport(), 'نموذج-استيراد-عقود.xlsx');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate(['file' => ['required', 'file', 'mimes:xlsx,xls,csv']]);

        $importer = new ContractImport();
        Excel::import($importer, $request->file('file'));

        $msg = "تم استيراد {$importer->importedCount()} عقد بنجاح";
        if ($errs = $importer->importErrors()) {
            $msg .= '. تحذيرات: ' . implode(' | ', array_slice($errs, 0, 5));
        }

        return back()->with('success', $msg);
    }

    // ── Print view ─────────────────────────────────────────────────────────────

    public function printView(int $id): View
    {
        $contract   = $this->service->findById($id);
        $statuses   = RecruitmentContract::statuses();
        $historyMap = $contract->statusHistories->keyBy('status');
        return view('admin.contracts.print', compact('contract', 'statuses', 'historyMap'));
    }

    // ── Status quick-update (AJAX-friendly POST) ──────────────────────────────

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'status'            => ['required', 'integer', 'min:1', 'max:15'],
            'status_date'       => ['nullable', 'date'],
            'whatsapp_message'  => ['nullable', 'string', 'max:1000'],
        ]);

        $contract = $this->service->findById($id);
        $this->service->updateStatus(
            $contract,
            (int) $request->status,
            $request->status_date,
            $request->whatsapp_message
        );

        return back()->with('success', 'تم تحديث الحالة بنجاح');
    }

    // ── Public tracking page ──────────────────────────────────────────────────

    public function publicTrack(Request $request): View
    {
        $contract    = null;
        $historyMap  = collect();
        $statuses    = RecruitmentContract::statuses();
        $musanedNum  = $request->query('musaned_number', '');

        if ($musanedNum) {
            $contract   = $this->service->findByMusanedNumber(trim($musanedNum));
            if ($contract) {
                $historyMap = $contract->statusHistories->keyBy('status');
            }
        }

        return view('public.contract-track', compact('contract', 'historyMap', 'statuses', 'musanedNum'));
    }
}
