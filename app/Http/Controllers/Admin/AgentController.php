<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAgentRequest;
use App\Http\Requests\Admin\UpdateAgentRequest;
use App\Models\Nationality;
use App\Services\AgentService;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    public function __construct(
        private readonly AgentService        $service,
        private readonly NotificationService $notifService,
    ) {}

    public function index(Request $request)
    {
        $filters       = $request->only('nationality_id', 'search');
        $agents        = $this->service->list($filters);
        $nationalities = Nationality::where('active', true)->orderBy('name')->get();
        $trashed       = $this->service->trashed();
        return view('admin.agents.index', compact('agents', 'nationalities', 'filters', 'trashed'));
    }

    public function create()
    {
        $nationalities = Nationality::where('active', true)->orderBy('name')->get();
        return view('admin.agents.create', compact('nationalities'));
    }

    public function store(StoreAgentRequest $request)
    {
        $agent = $this->service->store($request->validated(), $request->file('document'));
        $this->notifService->notify(
            'agent_created',
            'تم إضافة وكيل جديد',
            'تم تسجيل وكيل جديد باسم ' . $agent->name,
            route('admin.agents.show', $agent->id),
            []
        );
        return redirect()->route('admin.agents.index')->with('success', 'تم إضافة الوكيل بنجاح.');
    }

    public function show(int $id)
    {
        $agent = $this->service->find($id);
        return view('admin.agents.show', compact('agent'));
    }

    public function edit(int $id)
    {
        $agent         = $this->service->find($id);
        $nationalities = Nationality::where('active', true)->orderBy('name')->get();
        return view('admin.agents.edit', compact('agent', 'nationalities'));
    }

    public function update(UpdateAgentRequest $request, int $id)
    {
        $this->service->update($id, $request->validated(), $request->file('document'));
        return redirect()->route('admin.agents.index')->with('success', 'تم تحديث بيانات الوكيل.');
    }

    public function destroy(int $id)
    {
        $this->service->destroy($id);
        return back()->with('success', 'تم حذف الوكيل.');
    }

    public function restore(int $id)
    {
        $this->service->restore($id);
        return back()->with('success', 'تم استعادة الوكيل.');
    }
}
