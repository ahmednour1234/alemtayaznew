<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreWorkerRequest;
use App\Http\Requests\Admin\UpdateWorkerRequest;
use App\Models\Client;
use App\Models\Lead;
use App\Models\Nationality;
use App\Models\Worker;
use App\Services\BranchService;
use App\Services\WorkerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class WorkerController extends Controller
{
    public function __construct(
        private readonly WorkerService  $service,
        private readonly BranchService  $branchService,
    ) {}

    // ── Index ─────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $filters       = $request->only('nationality_id', 'status', 'profession', 'search');
        $workers       = $this->service->list($filters);
        $nationalities = Nationality::where('active', true)->orderBy('name')->get();
        $professions   = Worker::professions();
        $trashed       = $this->service->trashed();
        return view('admin.workers.index', compact('workers', 'nationalities', 'professions', 'filters', 'trashed'));
    }

    // ── Create (single) ───────────────────────────────────────────────────────
    public function create()
    {
        $me            = Auth::guard('admin')->user();
        $nationalities = Nationality::where('active', true)->orderBy('name')->get();
        $branches      = $me->isBranchAdmin()
            ? $this->branchService->allActive()->where('id', $me->branch_id)
            : $this->branchService->allActive();
        $professions   = Worker::professions();
        $experiences   = Worker::experienceOptions();
        $genders       = Worker::genderOptions();
        $religions     = Worker::religionOptions();
        return view('admin.workers.create', compact(
            'nationalities', 'branches', 'professions', 'experiences', 'genders', 'religions'
        ));
    }

    public function store(StoreWorkerRequest $request)
    {
        $data             = $request->validated();
        $me               = Auth::guard('admin')->user();
        $data['admin_id'] = $me->id;
        if ($me->isBranchAdmin()) {
            $data['branch_id'] = $me->branch_id;
        }
        $data['status'] = $data['status'] ?? 'available';

        // ── Duplicate CV check ────────────────────────────────────────────────
        $cvFile       = $request->file('cv');
        $passportNum  = $data['passport_number'] ?? null;
        $originalName = $cvFile ? $cvFile->getClientOriginalName() : null;
        $duplicate    = $this->service->findDuplicate($passportNum, $originalName);

        if ($duplicate && ! $request->boolean('force_upload')) {
            return back()
                ->withInput()
                ->with('cv_duplicate_warning', true)
                ->with('cv_duplicate_name', $duplicate->name ?? $duplicate->passport_number)
                ->with('cv_duplicate_id', $duplicate->id);
        }

        $this->service->store($data, $cvFile, $request->file('passport_image'));
        return redirect()->route('admin.workers.index')->with('success', 'تم إضافة العاملة بنجاح.');
    }

    // ── Quick store (popup from contracts form) ────────────────────────────────
    public function quickStore(Request $request)
    {
        $request->validate([
            'name'            => ['required', 'string', 'max:255'],
            'nationality_id'  => ['nullable', 'integer', 'exists:nationalities,id'],
            'passport_number' => ['nullable', 'string', 'max:50'],
            'sponsor_name'    => ['nullable', 'string', 'max:255'],
            'sponsor_phone'   => ['nullable', 'string', 'max:20'],
        ]);

        $me = Auth::guard('admin')->user();

        // Save sponsor as client if provided
        $clientId = null;
        if (filled($request->sponsor_name)) {
            $sponsor = \App\Models\Client::create([
                'name'           => $request->sponsor_name,
                'phone'          => $request->sponsor_phone,
                'marital_status' => 'single',
                'classification' => 'potential',
                'branch_id'      => $me->branch_id,
                'admin_id'       => $me->id,
                'active'         => true,
            ]);
            $clientId = $sponsor->id;
        }

        $worker = Worker::create([
            'name'            => $request->name,
            'nationality_id'  => $request->nationality_id,
            'passport_number' => $request->passport_number,
            'status'          => 'available',
            'branch_id'       => $me->branch_id,
            'admin_id'        => $me->id,
            'client_id'       => $clientId,
            'active'          => true,
        ]);

        return response()->json(['id' => $worker->id, 'name' => $worker->name]);
    }

    // ── Bulk Upload ───────────────────────────────────────────────────────────
    public function bulk()
    {
        $nationalities = Nationality::where('active', true)->orderBy('name')->get();
        $professions   = Worker::professions();
        return view('admin.workers.bulk', compact('nationalities', 'professions'));
    }

    public function bulkStore(Request $request)
    {
        $me   = Auth::guard('admin')->user();
        $data = [
            'nationality_id' => $request->nationality_id,
            'profession'     => $request->profession,
            'status'         => $request->status ?? 'available',
            'admin_id'       => $me->id,
            'branch_id'      => $me->isBranchAdmin() ? $me->branch_id : $request->branch_id,
            'active'         => true,
        ];

        // ── Force-upload path: process previously saved temp files ────────────
        if ($request->boolean('force_upload')) {
            $tempPaths  = session('cv_temp_files', []);
            $savedData  = session('cv_temp_data', []);
            if (empty($tempPaths)) {
                return redirect()->route('admin.workers.bulk')
                    ->with('error', 'انتهت صلاحية الجلسة. يرجى رفع الملفات مجدداً.');
            }
            // Use saved common data (nationality, profession, status) from the original upload
            if (! empty($savedData)) {
                $data = array_merge($data, $savedData);
            }
            $result = $this->service->bulkStoreFromTempPaths($data, $tempPaths);
            session()->forget(['cv_temp_files', 'cv_temp_data']);
            $count = count($result['created']);
            return redirect()->route('admin.workers.index')
                ->with('success', "تم رفع {$count} CV بنجاح.");
        }

        // ── Normal upload path ────────────────────────────────────────────────
        $request->validate([
            'nationality_id' => ['nullable', 'exists:nationalities,id'],
            'profession'     => ['nullable', 'string', 'max:100'],
            'status'         => ['nullable', 'in:available,reserved'],
            'cvs'            => ['required', 'array', 'min:1'],
            'cvs.*'          => ['file', 'mimes:pdf', 'max:10240'],
        ]);

        $result = $this->service->bulkStore($data, $request->file('cvs'), false);

        if (! empty($result['duplicates'])) {
            // Save duplicate files to temp storage (persists across requests)
            $sessionKey = 'bulk_temp_' . session()->getId();
            $tempPaths  = [];
            foreach ($request->file('cvs') as $file) {
                if (in_array($file->getClientOriginalName(), $result['duplicates'])) {
                    $path = $file->storeAs('temp_bulk/' . $sessionKey, $file->getClientOriginalName());
                    $tempPaths[$file->getClientOriginalName()] = $path;
                }
            }
            session()->put('cv_temp_files', $tempPaths);
            // Save common data so force_upload can use the exact same nationality/profession/status
            session()->put('cv_temp_data', [
                'nationality_id' => $request->nationality_id,
                'profession'     => $request->profession,
                'status'         => $request->status ?? 'available',
            ]);

            return back()
                ->with('cv_duplicate_warning', true)
                ->with('cv_duplicate_files', $result['duplicates'])
                ->with('cv_created_count', count($result['created']));
        }

        $count = count($result['created']);
        return redirect()->route('admin.workers.index')
            ->with('success', "تم رفع {$count} CV بنجاح.");
    }

    // ── Show ──────────────────────────────────────────────────────────────────
    public function show(int $id)
    {
        $worker = $this->service->find($id);
        return view('admin.workers.show', compact('worker'));
    }

    // ── Edit ──────────────────────────────────────────────────────────────────
    public function edit(int $id)
    {
        $me            = Auth::guard('admin')->user();
        $worker        = $this->service->find($id);
        $nationalities = Nationality::where('active', true)->orderBy('name')->get();
        $branches      = $me->isBranchAdmin()
            ? $this->branchService->allActive()->where('id', $me->branch_id)
            : $this->branchService->allActive();
        $professions   = Worker::professions();
        $experiences   = Worker::experienceOptions();
        $genders       = Worker::genderOptions();
        $religions     = Worker::religionOptions();
        return view('admin.workers.edit', compact(
            'worker', 'nationalities', 'branches', 'professions', 'experiences', 'genders', 'religions'
        ));
    }

    public function update(UpdateWorkerRequest $request, int $id)
    {
        $data = $request->validated();
        $me   = Auth::guard('admin')->user();
        if ($me->isBranchAdmin()) {
            $data['branch_id'] = $me->branch_id;
        }
        $this->service->update($id, $data, $request->file('cv'), $request->file('passport_image'));
        return redirect()->route('admin.workers.index')->with('success', 'تم تحديث بيانات العاملة.');
    }

    // ── Destroy / Restore ─────────────────────────────────────────────────────
    public function destroy(int $id)
    {
        try {
            $this->service->destroy($id);
        } catch (\RuntimeException $e) {
            return back()->withErrors(['delete' => $e->getMessage()]);
        }

        return back()->with('success', 'تم حذف العاملة.');
    }

    /** حذف جماعي — يتخطّى العاملات المرتبطة بعقود استقدام. */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'worker_ids'   => ['required', 'array', 'min:1'],
            'worker_ids.*' => ['integer', 'exists:workers,id'],
        ], [], ['worker_ids' => 'العاملات']);

        $result = $this->service->bulkDestroy($request->input('worker_ids'));

        if ($result['deleted'] === 0 && $result['skipped']) {
            return back()->withErrors([
                'delete' => 'لم يتم حذف أي عاملة — جميع المحددات مرتبطة بعقود استقدام: '
                            . implode('، ', $result['skipped']),
            ]);
        }

        $message = "تم حذف {$result['deleted']} عاملة.";

        if ($result['skipped']) {
            $count    = count($result['skipped']);
            $message .= " وتم تخطّي {$count} لارتباطها بعقود استقدام: " . implode('، ', $result['skipped']);
        }

        return back()->with('success', $message);
    }

    public function restore(int $id)
    {
        $this->service->restore($id);
        return back()->with('success', 'تم استعادة العاملة.');
    }

    // ── Serve CV / Passport files (no symlink needed) ─────────────────────────
    public function serveCV(int $id)
    {
        $worker = $this->service->find($id);
        abort_if(! $worker->cv_path, 404);
        $path = storage_path('app/public/' . $worker->cv_path);
        abort_if(! file_exists($path), 404);
        return response()->file($path, [
            'Content-Type'        => mime_content_type($path) ?: 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($worker->cv_path) . '"',
        ]);
    }

    public function servePassport(int $id)
    {
        $worker = $this->service->find($id);
        abort_if(! $worker->passport_image, 404);
        $path = storage_path('app/public/' . $worker->passport_image);
        abort_if(! file_exists($path), 404);
        return response()->file($path, [
            'Content-Type'        => mime_content_type($path) ?: 'image/jpeg',
            'Content-Disposition' => 'inline; filename="' . basename($worker->passport_image) . '"',
        ]);
    }

    // ── Assign to Client ──────────────────────────────────────────────────────
    public function assign(int $id)
    {
        $me     = Auth::guard('admin')->user();
        $worker = $this->service->find($id);

        $clientsQuery = Client::where('active', true)
            ->whereIn('classification', ['confirmed', 'premium'])
            ->orderBy('name');
        // Only leads not yet converted to clients
        $leadsQuery = Lead::whereIn('status', ['new', 'in_progress'])
            ->whereNull('client_id')
            ->whereNotNull('name')
            ->where('name', '!=', '')
            ->orderBy('name');

        if ($me->isBranchAdmin()) {
            $clientsQuery->where('branch_id', $me->branch_id);
            $leadsQuery->where('branch_id', $me->branch_id);
        }

        $clients        = $clientsQuery->get();
        $leads          = $leadsQuery->get();
        $existingClient = $worker->client_id ? Client::find($worker->client_id) : null;

        return view('admin.workers.assign', compact('worker', 'clients', 'leads', 'existingClient'));
    }

    public function doAssign(Request $request, int $id)
    {
        $worker = $this->service->find($id);

        $request->validate([
            'assignee' => ['required', 'string'],
            // رقم الجواز إلزامي لإتمام الحجز — يُقبل من النموذج أو يكون مسجّلاً مسبقاً
            'passport_number' => [
                $worker->passport_number ? 'nullable' : 'required',
                'string',
                'max:50',
            ],
            // رقم التأشيرة اختياري
            'visa_number' => ['nullable', 'string', 'max:50'],
        ], [], [
            'passport_number' => 'رقم جواز العاملة',
            'visa_number'     => 'رقم التأشيرة',
        ]);

        // Resolve client_id — either a direct client or a lead to convert
        $assignee = $request->input('assignee'); // format: "client:123" or "lead:456"
        [$type, $rawId] = explode(':', $assignee);
        $clientId = null;

        if ($type === 'client') {
            $client = Client::findOrFail((int) $rawId);
            $clientId = $client->id;
        } elseif ($type === 'lead') {
            $lead = Lead::findOrFail((int) $rawId);

            // If lead was already converted, reuse its client
            if ($lead->client_id) {
                $clientId = $lead->client_id;
            } else {
                // Convert lead → client
                $me = Auth::guard('admin')->user();
                $newClient = Client::create([
                    'name'           => $lead->name,
                    'phone'          => $lead->phone,
                    'classification' => 'confirmed',
                    'branch_id'      => $lead->branch_id ?? $me->branch_id,
                    'admin_id'       => $me->id,
                    'active'         => true,
                ]);
                $lead->update(['status' => 'converted', 'client_id' => $newClient->id]);
                $clientId = $newClient->id;
            }
        } else {
            return back()->withErrors(['assignee' => 'اختيار غير صالح.']);
        }

        // Update worker details if provided
        $data = array_filter($request->only(
            'name', 'passport_number', 'visa_number', 'nationality_id', 'profession',
            'gender', 'experience', 'religion', 'age', 'phone', 'notes'
        ), fn($v) => $v !== null && $v !== '');

        if (!empty($data)) {
            $this->service->update($id, $data);
        }

        $me = Auth::guard('admin')->user();

        try {
            $this->service->assignToClient($id, $clientId, $me->id);
        } catch (\RuntimeException $e) {
            return back()->withErrors(['assignee' => $e->getMessage()]);
        }

        // زر «حجز وإنشاء عقد» → ينتقل مباشرة لشاشة عقد الاستقدام بالبيانات جاهزة
        if ($request->boolean('create_contract')) {
            return redirect()->route('admin.contracts.create', [
                'worker_id' => $id,
                'client_id' => $clientId,
            ])->with('success', 'تم حجز العاملة لمدة ' . \App\Console\Commands\NotifyUncontractedWorkers::RESERVATION_HOURS . ' ساعة — أكمل بيانات العقد الآن.');
        }

        return redirect()->route('admin.workers.index')
            ->with('success', 'تم حجز العاملة للعميل لمدة ' . \App\Console\Commands\NotifyUncontractedWorkers::RESERVATION_HOURS . ' ساعة. أنشئ عقد الاستقدام قبل انتهاء المهلة وإلا يُفكّ الحجز تلقائياً.');
    }

    // ── Unassign ──────────────────────────────────────────────────────────────
    public function unassign(int $id)
    {
        $me = Auth::guard('admin')->user();

        try {
            $this->service->unassign($id, $me);
        } catch (\RuntimeException $e) {
            return back()->withErrors(['permission' => $e->getMessage()]);
        }

        return back()->with('success', 'تم إلغاء تعيين العاملة وأصبحت متاحة.');
    }

    // ── WhatsApp ──────────────────────────────────────────────────────────────
    public function sendWhatsapp(Request $request)
    {
        $request->validate([
            'phone'       => ['required', 'string'],
            'worker_ids'  => ['required', 'array', 'min:1'],
            'worker_ids.*'=> ['exists:workers,id'],
        ]);

        $url = $this->service->buildWhatsappUrl(
            $request->phone,
            $request->worker_ids
        );

        return redirect($url);
    }
}
