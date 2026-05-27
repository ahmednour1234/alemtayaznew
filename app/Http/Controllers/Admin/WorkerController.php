<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreWorkerRequest;
use App\Http\Requests\Admin\UpdateWorkerRequest;
use App\Models\Client;
use App\Models\Nationality;
use App\Models\Worker;
use App\Services\BranchService;
use App\Services\WorkerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $this->service->store($data, $request->file('cv'));
        return redirect()->route('admin.workers.index')->with('success', 'تم إضافة العاملة بنجاح.');
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
        $request->validate([
            'nationality_id' => ['nullable', 'exists:nationalities,id'],
            'profession'     => ['nullable', 'string', 'max:100'],
            'status'         => ['nullable', 'in:available,reserved'],
            'cvs'            => ['required', 'array', 'min:1'],
            'cvs.*'          => ['file', 'mimes:pdf', 'max:10240'],
        ]);

        $me   = Auth::guard('admin')->user();
        $data = [
            'nationality_id' => $request->nationality_id,
            'profession'     => $request->profession,
            'status'         => $request->status ?? 'available',
            'admin_id'       => $me->id,
            'branch_id'      => $me->isBranchAdmin() ? $me->branch_id : $request->branch_id,
            'active'         => true,
        ];

        $count = count($this->service->bulkStore($data, $request->file('cvs')));
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
        $this->service->update($id, $data, $request->file('cv'));
        return redirect()->route('admin.workers.index')->with('success', 'تم تحديث بيانات العاملة.');
    }

    // ── Destroy / Restore ─────────────────────────────────────────────────────
    public function destroy(int $id)
    {
        $this->service->destroy($id);
        return back()->with('success', 'تم حذف العاملة.');
    }

    public function restore(int $id)
    {
        $this->service->restore($id);
        return back()->with('success', 'تم استعادة العاملة.');
    }

    // ── Assign to Client ──────────────────────────────────────────────────────
    public function assign(int $id)
    {
        $worker  = $this->service->find($id);
        $clients = Client::where('active', true)
                         ->whereIn('classification', ['confirmed', 'premium'])
                         ->orderBy('name')->get();
        return view('admin.workers.assign', compact('worker', 'clients'));
    }

    public function doAssign(Request $request, int $id)
    {
        $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
        ]);
        // Update worker details if provided
        $data = array_filter($request->only(
            'name', 'passport_number', 'nationality_id', 'profession',
            'gender', 'experience', 'religion', 'age', 'phone', 'notes'
        ), fn($v) => $v !== null && $v !== '');

        if (!empty($data)) {
            $this->service->update($id, $data);
        }

        $this->service->assignToClient($id, (int) $request->client_id);
        return redirect()->route('admin.workers.index')
            ->with('success', 'تم تعيين العاملة للعميل بنجاح.');
    }

    // ── Unassign ──────────────────────────────────────────────────────────────
    public function unassign(int $id)
    {
        $this->service->unassign($id);
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
