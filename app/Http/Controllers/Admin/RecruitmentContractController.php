<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRecruitmentContractRequest;
use App\Http\Requests\Admin\UpdateRecruitmentContractRequest;
use App\Exports\ContractExport;
use App\Exports\ContractTemplateExport;
use App\Exports\ContractStatusTemplateExport;
use App\Imports\ContractImport;
use App\Imports\ContractStatusImport;
use App\Models\Agent;
use App\Models\Airport;
use App\Models\City;
use App\Models\Branch;
use App\Models\Client;
use App\Models\Nationality;
use App\Models\RecruitmentContract;
use App\Models\Worker;
use App\Services\NotificationService;
use App\Services\RecruitmentContractService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class RecruitmentContractController extends Controller
{
    public function __construct(
        private readonly RecruitmentContractService $service,
        private readonly NotificationService $notifications,
    ) {}

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

    /**
     * Enforce department + branch access on a specific contract.
     * - Super admins: unrestricted.
     * - Branch managers / chairmen: restricted to their own branch.
     * - Department employees: restricted to their branch AND only contracts
     *   whose current_department matches their own department.
     */
    private function authorizeContractAccess(RecruitmentContract $contract): void
    {
        $me     = $this->me();
        $myDept = $me->department;

        // Super admin: full access
        if ($me->isSuperAdmin()) return;

        // Branch check: everyone (except super admin) must share the contract's branch
        if ($me->branch_id && $contract->branch_id !== $me->branch_id) {
            abort(403, 'ليس لديك صلاحية الوصول إلى عقود فرع آخر.');
        }

        // All other employees within the branch can view/edit the contract at any stage.
        // Field-level restrictions are enforced in update() by stripping fields outside their dept.
    }

    private function formData(): array
    {
        return [
            'clients'       => Client::where('active', true)->orderBy('name')->get(['id', 'name', 'phone']),
            'branches'      => Branch::where('active', true)->orderBy('name')->get(['id', 'name']),
            'workers'       => Worker::where('active', true)->where('status', 'available')->orderBy('name')->get(['id', 'name', 'nationality_id']),
            'agents'        => Agent::where('active', true)->orderBy('name')->get(['id', 'name']),
            'airports'      => Airport::where('active', true)->orderBy('name')->get(['id', 'name', 'code']),
            'cities'        => City::where('active', true)->orderBy('name')->get(['id', 'name']),
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
        $filters  = $request->only(['search', 'department', 'status', 'payment_status', 'branch_id', 'origin_nationality_id']);
        $branchId = $this->branchFilter();

        // No auto-filter by department — users see all branch contracts by default.
        // They can filter manually via the department tabs.

        $contracts     = $this->service->getList($filters, $branchId);
        $statuses      = RecruitmentContract::statuses();
        $departments   = RecruitmentContract::departments();
        $nationalities = Nationality::where('active', true)->orderBy('name')->get(['id', 'name']);
        $branches      = Branch::where('active', true)->orderBy('name')->get(['id', 'name']);

        return view('admin.contracts.index', compact('contracts', 'statuses', 'departments', 'filters', 'nationalities', 'branches'));
    }

    public function create(Request $request): View
    {
        $me = $this->me();
        // Department guard (middleware also enforces this, belt-and-suspenders)
        if (in_array($me->department, ['accounts', 'accountant', 'coordination'])) {
            abort(403, 'إنشاء العقود مخصص لقسم خدمة العملاء فقط.');
        }
        $data = $this->formData();
        $data['defaultBranch']   = $me->branch_id;
        $data['suggestedBranch'] = null;

        // قادم من شاشة حجز العاملة (?worker_id=&client_id=) — املأ النموذج مسبقاً
        $data['prefill'] = [
            'worker_id' => $request->integer('worker_id') ?: null,
            'client_id' => $request->integer('client_id') ?: null,
        ];

        if ($data['prefill']['worker_id']) {
            $worker = Worker::with('assignedBy')->find($data['prefill']['worker_id']);

            // قائمة العاملات تعرض المتاحات فقط، والعاملة المحجوزة حالتها
            // «reserved» — نضيفها يدوياً وإلا اختفت من القائمة.
            if ($worker) {
                $data['workers'] = $data['workers']
                    ->merge(collect([$worker]))
                    ->unique('id')
                    ->sortBy('name')
                    ->values();

                // المدير العام لا فرع له، فيبقى حقل الفرع فارغاً ويتعذّر حفظ العقد.
                // نقترح فرع من حجز العاملة، ثم فرع العاملة، ثم فرع العميل —
                // مع إبقاء القائمة قابلة للتغيير لأن المدير العام يرى كل الفروع.
                $data['suggestedBranch'] = $worker->assignedBy?->branch_id
                    ?? $worker->branch_id
                    ?? Client::find($data['prefill']['client_id'])?->branch_id;
            }

            // رقم الجواز يُعرض للتأكيد لأنه إلزامي لإتمام العقد
            $data['prefillWorker'] = $worker;
        }

        return view('admin.contracts.create', $data);
    }

    public function store(StoreRecruitmentContractRequest $request): RedirectResponse
    {
        $me = $this->me();
        // Department guard (middleware also enforces this, belt-and-suspenders)
        if (in_array($me->department, ['accounts', 'accountant', 'coordination'])) {
            abort(403, 'إنشاء العقود مخصص لقسم خدمة العملاء فقط.');
        }
        $data = $request->validated();
        $data['client_sms']    = $request->boolean('client_sms');
        $data['client_rating'] = $request->boolean('client_rating');

        // Branch ownership: non-super-admin with a fixed branch cannot create for another branch
        if (!$me->isSuperAdmin() && $me->branch_id && (int)($data['branch_id'] ?? 0) !== (int)$me->branch_id) {
            abort(403, 'لا يمكنك إضافة عقود لفرع غير فرعك. فرعك المخصص: ' . $me->branch?->name);
        }

        // ── CV → Client lock ──────────────────────────────────────────────────
        $contractWorker = null;
        if (! empty($data['worker_id'])) {
            $contractWorker = Worker::find($data['worker_id']);
            if ($contractWorker && $contractWorker->client_id && (int)$contractWorker->client_id !== (int)($data['client_id'] ?? 0)) {
                return back()
                    ->withInput()
                    ->withErrors(['worker_id' => 'هذه العاملة مُعيَّنة لعميل آخر. يجب أن يكون العقد للعميل نفسه المُعيَّنة له العاملة.']);
            }
        }

        // رقم الجواز يُحفظ في ملف العاملة لا في العقد
        $passportNumber = $data['worker_passport_number'] ?? null;
        unset($data['worker_passport_number']);

        $contract = $this->service->store($data);

        // ── تثبيت الحجز ───────────────────────────────────────────────────────
        // العاملة كانت «محجوزة» بمهلة 72 ساعة؛ بإنشاء العقد تصبح «مُعيَّنة»
        // فلا يفكّها أمر workers:notify-uncontracted بعد انتهاء المهلة.
        if ($contractWorker) {
            $update = ['status' => 'assigned'];

            if ($passportNumber) {
                $update['passport_number'] = $passportNumber;
            }
            if (! empty($data['client_id'])) {
                $update['client_id'] = $data['client_id'];
            }

            $contractWorker->update($update);
        }

        $this->notifications->notify(
            'contract_created',
            'عقد استقدام جديد',
            'أنشأ ' . $me->name . ' العقد ' . $contract->contract_number,
            route('admin.contracts.show', $contract->id),
            [$contract->branch_id]
        );

        return redirect()->route('admin.contracts.show', $contract->id)
            ->with('success', "تم إنشاء العقد {$contract->contract_number} بنجاح");
    }

    public function show(int $id): View
    {
        $contract    = $this->service->findById($id);
        $this->authorizeContractAccess($contract);
        $statuses     = RecruitmentContract::statuses();
        $departments  = RecruitmentContract::departments();
        $historyMap   = $contract->statusHistories->keyBy('status');
        $activityLogs = $contract->activityLogs;

        return view('admin.contracts.show', compact('contract', 'statuses', 'departments', 'historyMap', 'activityLogs'));
    }

    public function edit(int $id): View
    {
        $contract    = $this->service->findById($id);
        $this->authorizeContractAccess($contract);
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
        $this->authorizeContractAccess($contract);
        $data     = $request->validated();
        $data['client_sms']    = $request->boolean('client_sms');
        $data['client_rating'] = $request->boolean('client_rating');

        $me     = $this->me();
        $myDept = $me->department;

        // Department-based field isolation (strip sections the user doesn't own)
        if (!$me->isSuperAdmin()) {
            $csFields      = ['client_id', 'branch_id', 'request_date', 'visa_type', 'visa_image',
                              'visa_number', 'arrival_airport_id', 'origin_nationality_id',
                              'delivery_city_id', 'musaned_number', 'musaned_date', 'musaned_file'];
            $accountsFields = ['payment_status', 'total_cost'];
            $coordFields    = ['arrival_date', 'trial_end_date', 'contract_end_date',
                               'worker_id', 'e_doc_number', 'agent_id',
                               'notes', 'client_sms', 'client_rating', 'rating_image'];

            if ($myDept === 'customer_service') {
                // CS: cannot modify accounts or coordination data — strip both sections
                foreach (array_merge($accountsFields, $coordFields) as $f) unset($data[$f]);
                // Branch ownership: CS user cannot reassign a contract to a different branch
                if ($me->branch_id && isset($data['branch_id']) && (int)$data['branch_id'] !== (int)$me->branch_id) {
                    abort(403, 'لا يمكنك تغيير فرع العقد إلى فرع غير فرعك.');
                }
            } elseif (in_array($myDept, ['accounts', 'accountant'])) {
                // Accounts: strip CS data and ALWAYS advance to coordination
                foreach ($csFields as $f) unset($data[$f]);
                $data['current_department'] = 'coordination';
            } elseif ($myDept === 'coordination') {
                // Coordination: cannot modify CS or accounts data
                foreach (array_merge($csFields, $accountsFields) as $f) unset($data[$f]);
            }
        }

        // Explicit department forwarding via the "حفظ وإرسال" buttons
        // Each department can only advance to the NEXT stage — no skipping.
        $advanceTo = $request->input('advance_to');
        if ($advanceTo) {
            $nextMap = [
                'customer_service' => 'accounts',
                'accounts'         => 'coordination',
                'accountant'       => 'coordination',
            ];
            $allowedNext = $me->isSuperAdmin()
                ? $advanceTo                                        // super admin: any stage
                : ($nextMap[$myDept] ?? null);                      // dept user: only their next stage

            if ($allowedNext === $advanceTo || $me->isSuperAdmin()) {
                $data['current_department'] = $advanceTo;
            }
        } else {
            // No forwarding requested — keep current_department as-is (don't let non-bosses change it)
            if (!$me->isSuperAdmin() && !in_array($myDept, ['branch_manager', 'chairman'])) {
                unset($data['current_department']);
            }
        }

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

        $this->notifications->notify(
            'contract_updated',
            'تحديث عقد استقدام',
            'حدّث ' . $me->name . ' العقد ' . $contract->contract_number,
            route('admin.contracts.show', $id),
            [$contract->branch_id]
        );

        $successMsg = match (true) {
            in_array($myDept, ['accounts', 'accountant']) && !$me->isSuperAdmin()
                                          => 'تم حفظ بيانات الحسابات وإحالة العقد تلقائياً لقسم التنسيق.',
            $advanceTo === 'accounts'     => 'تم حفظ بيانات خدمة العملاء وإحالة العقد لقسم الحسابات.',
            $advanceTo === 'coordination' => 'تم حفظ بيانات الحسابات وإحالة العقد لقسم التنسيق.',
            default                       => 'تم تحديث العقد بنجاح.',
        };

        return redirect()->route('admin.contracts.show', $id)
            ->with('success', $successMsg);
    }

    /**
     * Quick-forward: advance a contract to the next department stage directly from the list.
     * Only the dept user whose stage matches the contract's current stage can forward.
     */
    public function forward(int $id): RedirectResponse
    {
        $contract = $this->service->findById($id);

        $me     = $this->me();
        $myDept = $me->department;

        // Super admins and bosses cannot use the quick-forward (they use the full edit)
        if ($me->isSuperAdmin() || in_array($myDept, ['branch_manager', 'chairman'])) {
            abort(403, 'استخدم صفحة التعديل لتغيير القسم.');
        }

        // Branch check
        if ($me->branch_id && $contract->branch_id !== $me->branch_id) {
            abort(403, 'ليس لديك صلاحية الوصول إلى عقود فرع آخر.');
        }

        $nextMap = [
            'customer_service' => 'accounts',
            'accounts'         => 'coordination',
            'accountant'       => 'coordination',
        ];
        $next = $nextMap[$myDept] ?? null;
        if (!$next) {
            abort(403, 'قسمك لا يملك صلاحية توجيه العقود.');
        }

        // Contract must be at the current dept's stage
        $myStage = match ($myDept) {
            'accountant' => 'accounts',
            default      => $myDept,
        };
        if ($contract->current_department !== $myStage) {
            return back()->with('error', 'العقد ليس في مرحلة قسمك الحالية.');
        }

        $this->service->update($contract, ['current_department' => $next]);

        $deptLabels = RecruitmentContract::departments();
        return back()->with('success',
            "تم توجيه العقد {$contract->contract_number} إلى {$deptLabels[$next]}.");
    }

    public function destroy(int $id): RedirectResponse
    {
        $contract = $this->service->findById($id);
        $this->authorizeContractAccess($contract);

        // Deletion is restricted: accounts & coordination departments cannot delete contracts.
        // Only customer_service, branch managers, chairmen, and super-admins may delete.
        $me = $this->me();
        if (! $me->isSuperAdmin() && in_array($me->department, ['accounts', 'accountant', 'coordination'])) {
            abort(403, 'حذف العقود غير مسموح لقسمك.');
        }

        $contractNumber = $contract->contract_number;
        $branchId       = $contract->branch_id;

        $this->service->delete($id);

        $this->notifications->notify(
            'contract_deleted',
            'تم حذف عقد استقدام',
            'حذف ' . $me->name . ' العقد ' . $contractNumber,
            route('admin.contracts.index'),
            [$branchId]
        );

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

        $created = $importer->importedCount();
        $updated = $importer->updatedCount();

        $parts = [];
        if ($created) { $parts[] = "إضافة {$created} عقد جديد"; }
        if ($updated) { $parts[] = "تحديث {$updated} عقد موجود"; }

        $msg = $parts ? 'تم ' . implode(' و', $parts) : 'لم يتم استيراد أي صف';

        if ($errs = $importer->importErrors()) {
            $msg .= '. تحذيرات: ' . implode(' | ', array_slice($errs, 0, 5));
        }

        return back()->with('success', $msg);
    }

    // ── Bulk status update (رفع اكسل لتحديث الحالات) ────────────────────────────

    public function statusTemplate()
    {
        return Excel::download(new ContractStatusTemplateExport(), 'نموذج-تحديث-حالات-العقود.xlsx');
    }

    public function statusImport(Request $request): RedirectResponse
    {
        $request->validate(['file' => ['required', 'file', 'mimes:xlsx,xls,csv']]);

        $importer = new ContractStatusImport($this->service);
        Excel::import($importer, $request->file('file'));

        $updated = $importer->updatedCount();
        $skipped = $importer->skippedCount();
        $errs    = $importer->importErrors();

        $msg = "تم تحديث حالة {$updated} عقد";
        if ($skipped > 0) {
            $msg .= " — وتم تخطي {$skipped} عقد (الحالة نفسها بالفعل)";
        }

        // لا شيء نجح وكل الصفوف بها أخطاء → اعرضها كرسالة خطأ
        if ($updated === 0 && $errs) {
            return back()->with('error', 'لم يتم تحديث أي عقد. ' . implode(' | ', array_slice($errs, 0, 5)));
        }

        if ($errs) {
            $msg .= '. تحذيرات (' . count($errs) . '): ' . implode(' | ', array_slice($errs, 0, 5));
        }

        return back()->with('success', $msg);
    }

    // ── Bulk delete ─────────────────────────────────────────────────────────────

    public function bulkDelete(Request $request): RedirectResponse
    {
        $me = $this->me();
        if (! $me->isSuperAdmin() && ! $me->hasPermission('contracts.delete')) {
            abort(403);
        }

        $ids = array_filter((array) $request->input('ids', []), 'is_numeric');
        if (empty($ids)) {
            return back()->with('error', 'لم يتم تحديد أي عقد');
        }

        // Branch-scoped admins can only delete contracts in their branch
        $query = RecruitmentContract::whereIn('id', $ids);
        if ($me->isBranchAdmin()) {
            $query->where('branch_id', $me->branch_id);
        }
        $deleted = $query->count();
        $query->delete();

        return back()->with('success', "تم حذف {$deleted} عقد بنجاح");
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
        $contractNum = $request->query('contract_number', '');

        if ($musanedNum) {
            $contract = $this->service->findByMusanedNumber(trim($musanedNum));
        } elseif ($contractNum) {
            $contract = RecruitmentContract::where('contract_number', trim($contractNum))->first();
        }

        if ($contract) {
            $historyMap = $contract->statusHistories->keyBy('status');
        }

        return view('public.contract-track', compact('contract', 'historyMap', 'statuses', 'musanedNum'));
    }
}
