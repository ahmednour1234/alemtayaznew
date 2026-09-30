<?php

namespace App\Http\Controllers\CvPanel;

use App\Http\Controllers\Controller;
use App\Models\Nationality;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * قائمة السير الذاتية في لوحة إدارة CV.
 *
 * المنسّق يرى سير الجنسيات المسندة إليه وحدها؛ خدمة العملاء والمديرون
 * يرون الكل — فالحجز يقع عليهم لا على المنسّق.
 */
class CvController extends Controller
{
    public function index(Request $request): View
    {
        $me    = Auth::guard('admin')->user();
        $scope = $this->nationalityScope($me);

        $query = Worker::query()
            ->whereNotNull('cv_path')
            ->where('active', true)
            ->with(['nationality', 'client', 'assignedBy'])
            ->when($scope !== null, fn ($q) => $q->whereIn('nationality_id', $scope))
            ->latest();

        foreach (['nationality_id', 'status', 'experience', 'religion'] as $filter) {
            if ($value = $request->input($filter)) {
                $query->where($filter, $value);
            }
        }

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");

                if (ctype_digit($search)) {
                    $q->orWhere('id', (int) $search);
                }
            });
        }

        return view('cv-panel.cvs.index', [
            'workers'       => $query->paginate(24)->withQueryString(),
            'nationalities' => $this->visibleNationalities($scope),
            'experiences'   => Worker::experienceOptions(),
            'religions'     => Worker::religionOptions(),
            'statuses'      => Worker::statusOptions(),
            'filters'       => $request->only(['nationality_id', 'status', 'experience', 'religion', 'search']),
        ]);
    }

    /**
     * يخدم ملف السيرة داخل اللوحة.
     *
     * لا نعتمد على مسار لوحة الإدارة (admin.workers.cv) لأنه محكوم بصلاحية
     * workers.view، وقد لا يملكها موظّف خدمة العملاء هنا. اللوحة مستقلة،
     * فتخدم ملفاتها بنفسها ضمن نطاق ما يراه المستخدم.
     */
    public function file(int $id)
    {
        $me     = Auth::guard('admin')->user();
        $worker = Worker::findOrFail($id);
        $scope  = $this->nationalityScope($me);

        // المنسّق لا يفتح ملفاً خارج جنسياته
        if ($scope !== null && ! in_array($worker->nationality_id, $scope, true)) {
            abort(403);
        }

        abort_unless($worker->hasCvFile(), 404);

        $path = \Illuminate\Support\Facades\Storage::disk($worker->cvDisk())->path($worker->cv_path);

        // BinaryFileResponse يدعم Range فيستأنف المتصفّح ما انقطع،
        // بخلاف response()->file() التي تُعلّق التحميل على الملفات الكبيرة.
        $response = new \Symfony\Component\HttpFoundation\BinaryFileResponse($path);

        $response->headers->set('Content-Type', 'application/pdf');
        $response->setContentDisposition(
            \Symfony\Component\HttpFoundation\ResponseHeaderBag::DISPOSITION_INLINE,
            'cv-' . $worker->id . '.pdf'
        );
        $response->headers->set('Accept-Ranges', 'bytes');
        $response->setAutoEtag();
        $response->setAutoLastModified();
        $response->setPrivate();
        $response->setMaxAge(600);

        return $response;
    }

    /**
     * حذف سيرة ذاتية.
     *
     * مقصور على موظّف التنسيق وعلى الجنسيات المسندة إليه وحدها: هو من رفعها
     * فهو من يحذفها. الحذف ناعم — الصفّ والملف باقيان ويمكن استرجاعهما.
     *
     * لا تُحذف سيرة عليها ارتباط قائم (محجوزة أو مُسندة لعميل أو لها عقد)،
     * فذلك التزام جارٍ لا يُمحى من هنا.
     */
    public function destroy(int $id)
    {
        $me = Auth::guard('admin')->user();

        // التنسيق وحده — لا خدمة العملاء ولا المدير
        abort_unless($me->isCoordination(), 403, __('cv-panel.delete.denied'));

        $worker = Worker::findOrFail($id);

        // وعلى جنسياته وحدها
        $scope = $me->managedNationalities->pluck('id')->all();

        if (! in_array($worker->nationality_id, $scope, true)) {
            abort(403, __('cv-panel.delete.denied_nationality'));
        }

        if ($worker->isBooked() || $worker->client_id || $worker->hasActiveContract()) {
            return back()->with('error', __('cv-panel.delete.booked'));
        }

        \App\Models\WorkerActivityLog::create([
            'worker_id'   => $worker->id,
            'worker_name' => $worker->name,
            'admin_id'    => $me->id,
            'admin_name'  => $me->name,
            'action'      => 'deleted',
            'label'       => 'حذف السيرة الذاتية من لوحة السير (حذف ناعم — السجلّ والملف محفوظان)',
            'ip_address'  => request()?->ip(),
        ]);

        $worker->delete();

        return back()->with('success', __('cv-panel.delete.done', ['name' => $worker->name]));
    }

    /** معرّفات الجنسيات المرئية، أو null لمن يرى الكل. */
    protected function nationalityScope($me): ?array
    {
        if ($me->isSuperAdmin() || ! $me->isCoordination()) {
            return null;
        }

        return $me->managedNationalities->pluck('id')->all();
    }

    protected function visibleNationalities(?array $scope)
    {
        return Nationality::where('active', true)
            ->when($scope !== null, fn ($q) => $q->whereIn('id', $scope))
            ->orderBy('name')
            ->get();
    }
}
