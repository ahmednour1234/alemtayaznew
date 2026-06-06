<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClientRequest;
use App\Http\Requests\Admin\UpdateClientRequest;
use App\Models\Nationality;
use App\Services\BranchService;
use App\Services\ClientService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function __construct(
        private readonly ClientService       $service,
        private readonly BranchService       $branchService,
        private readonly NotificationService $notifService,
    ) {}

    public function index(Request $request)
    {
        $me       = Auth::guard('admin')->user();
        $filters  = $request->only('branch_id', 'classification', 'marital_status', 'search');
        if ($me->isBranchAdmin()) {
            $filters['branch_id'] = $me->branch_id;
        }
        $clients  = $this->service->list($filters);
        $branches = $me->isBranchAdmin()
            ? $this->branchService->allActive()->where('id', $me->branch_id)
            : $this->branchService->allActive();
        $trashed  = $this->service->trashed();
        return view('admin.clients.index', compact('clients', 'branches', 'filters', 'trashed'));
    }

    public function create()
    {
        $me            = Auth::guard('admin')->user();
        $branches      = $me->isBranchAdmin()
            ? $this->branchService->allActive()->where('id', $me->branch_id)
            : $this->branchService->allActive();
        $nationalities = Nationality::where('active', true)->orderBy('name')->get();
        return view('admin.clients.create', compact('branches', 'nationalities'));
    }

    public function store(StoreClientRequest $request)
    {
        $data             = $request->validated();
        $me               = Auth::guard('admin')->user();
        $data['admin_id'] = $me->id;
        // Auto classification: no national_id → potential, has ID → confirmed (unless manually set)
        if (empty($data['national_id'])) {
            $data['national_id']    = null;
            $data['classification'] = 'potential';
        } elseif (empty($data['classification']) || $data['classification'] === 'potential') {
            $data['classification'] = 'confirmed';
        }
        if ($me->isBranchAdmin()) {
            $data['branch_id'] = $me->branch_id;
        }
        $client = $this->service->store($data, $request->file('national_id_image'));
        $this->notifService->notify(
            'client_created',
            'تم إضافة عميل جديد',
            'تم تسجيل عميل جديد باسم ' . $client->name,
            route('admin.clients.show', $client->id),
            $me->isBranchAdmin() ? [$me->branch_id] : ($data['branch_id'] ? [$data['branch_id']] : [])
        );
        return redirect()->route('admin.clients.index')->with('success', 'تم إضافة العميل بنجاح.');
    }

    // ── Quick store (popup from contracts form / worker assign) ────────────────
    public function quickStore(Request $request)
    {
        $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'national_id'    => ['nullable', 'string', 'max:20'],
            'classification' => ['nullable', 'string', 'in:potential,confirmed,premium'],
        ]);

        $me = Auth::guard('admin')->user();

        // If national_id provided and already exists, return the existing client
        if (filled($request->national_id)) {
            $existing = \App\Models\Client::where('national_id', $request->national_id)->first();
            if ($existing) {
                return response()->json([
                    'id'          => $existing->id,
                    'name'        => $existing->name,
                    'phone'       => $existing->phone,
                    'national_id' => $existing->national_id,
                    'existing'    => true,
                ]);
            }
        }

        $classification = $request->classification
            ?? (filled($request->national_id) ? 'confirmed' : 'potential');

        $client = \App\Models\Client::create([
            'name'           => $request->name,
            'phone'          => $request->phone,
            'national_id'    => $request->national_id,
            'marital_status' => 'single',
            'classification' => $classification,
            'branch_id'      => $me->branch_id,
            'admin_id'       => $me->id,
            'active'         => true,
        ]);

        return response()->json([
            'id'          => $client->id,
            'name'        => $client->name,
            'phone'       => $client->phone,
            'national_id' => $client->national_id,
        ]);
    }

    public function show(int $id)
    {
        $client = $this->service->find($id);
        return view('admin.clients.show', compact('client'));
    }

    public function edit(int $id)
    {
        $me            = Auth::guard('admin')->user();
        $client        = $this->service->find($id);
        if ($me->isBranchAdmin() && $client->branch_id !== $me->branch_id) {
            abort(403);
        }
        $branches      = $me->isBranchAdmin()
            ? $this->branchService->allActive()->where('id', $me->branch_id)
            : $this->branchService->allActive();
        $nationalities = Nationality::where('active', true)->orderBy('name')->get();
        return view('admin.clients.edit', compact('client', 'branches', 'nationalities'));
    }

    public function update(UpdateClientRequest $request, int $id)
    {
        $me     = Auth::guard('admin')->user();
        $client = $this->service->find($id);
        if ($me->isBranchAdmin() && $client->branch_id !== $me->branch_id) {
            abort(403);
        }
        $data = $request->validated();
        // Auto classification: no national_id → potential
        if (empty($data['national_id'])) {
            $data['national_id']    = null;
            $data['classification'] = 'potential';
        } elseif (empty($data['classification']) || $data['classification'] === 'potential') {
            $data['classification'] = 'confirmed';
        }
        if ($me->isBranchAdmin()) {
            $data['branch_id'] = $me->branch_id;
        }
        $this->service->update($id, $data, $request->file('national_id_image'));
        return redirect()->route('admin.clients.index')->with('success', 'تم تحديث بيانات العميل.');
    }

    public function destroy(int $id)
    {
        $this->service->destroy($id);
        return back()->with('success', 'تم حذف العميل.');
    }

    public function restore(int $id)
    {
        $this->service->restore($id);
        return back()->with('success', 'تم استعادة العميل.');
    }
}
