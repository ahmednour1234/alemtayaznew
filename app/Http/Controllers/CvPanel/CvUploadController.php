<?php

namespace App\Http\Controllers\CvPanel;

use App\Http\Controllers\Controller;
use App\Models\Nationality;
use App\Models\Worker;
use App\Services\CvPanel\CvUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * رفع السير الذاتية دفعة واحدة في لوحة إدارة CV.
 *
 * المنسّق يرفع عدة ملفات PDF معاً لجنسية واحدة، ويختار الخبرة والديانة
 * إلزامياً — فهما ما يبحث بهما العميل في الصفحة العامة، وتركهما فارغين
 * يجعل السيرة غير قابلة للتصفية.
 */
class CvUploadController extends Controller
{
    public function __construct(
        private readonly CvUploadService $service,
        private readonly \App\Services\CvPanel\CvPurgeService $purge,
    ) {}

    public function create(): View
    {
        $me = Auth::guard('admin')->user();

        $this->authorizeUpload($me);

        $nationalities = $this->allowedNationalities($me);

        return view('cv-panel.upload', [
            'nationalities' => $nationalities,
            // عدد ما سيُحذف لكل جنسية، ليعرف المنسّق حجم الأثر قبل أن يقرّر
            'purgeCounts'   => $nationalities->mapWithKeys(
                fn ($n) => [$n->id => $this->purge->countFor($n->id)]
            ),
            'experiences'   => Worker::experienceOptions(),
            'religions'     => Worker::religionOptions(),
            'professions'   => Worker::professions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $me      = Auth::guard('admin')->user();

        $this->authorizeUpload($me);
        $allowed = $this->allowedNationalities($me)->pluck('id')->all();

        $data = $request->validate([
            // الجنسية مقصورة على ما يديره المستخدم — لا يكفي التحقق من وجودها
            'nationality_id' => ['required', 'integer', Rule::in($allowed)],
            'experience'     => ['required', Rule::in(array_keys(Worker::experienceOptions()))],
            'religion'       => ['required', Rule::in(array_keys(Worker::religionOptions()))],
            'profession'     => ['nullable', Rule::in(array_keys(Worker::professions()))],
            'cvs'            => ['required', 'array', 'min:1', 'max:100'],
            'cvs.*'          => ['file', 'mimes:pdf', 'max:10240'],
            // إقرار المنسّق بحذف سير الجنسية القديمة قبل رفع الدفعة الجديدة
            'purge_old'      => ['nullable', 'boolean'],
        ], [
            'nationality_id.required' => __('cv-panel.upload.nationality_required'),
            'nationality_id.in'       => __('cv-panel.upload.nationality_denied'),
            'experience.required'     => __('cv-panel.upload.experience_required'),
            'religion.required'       => __('cv-panel.upload.religion_required'),
            'cvs.required'            => __('cv-panel.upload.files_required'),
            'cvs.max'                 => __('cv-panel.upload.files_max'),
        ]);

        /*
         * التنظيف يسبق الرفع لا يليه، وإلّا حُذفت الدفعة الجديدة مع القديمة.
         * ولا يمسّ إلّا المتاح: المحجوز والمتعاقد عليه يبقيان.
         */
        $purged = $request->boolean('purge_old')
            ? $this->purge->purge((int) $data['nationality_id'], $me)
            : 0;

        $result = $this->service->upload($data, $request->file('cvs'), $me);

        $message = __('cv-panel.upload.done', ['count' => count($result['created'])]);

        if ($purged > 0) {
            $message .= ' ' . __('cv-panel.upload.purged', ['count' => $purged]);
        }

        if ($result['duplicates']) {
            $message .= ' ' . __('cv-panel.upload.skipped', [
                'count' => count($result['duplicates']),
                'names' => implode('، ', array_slice($result['duplicates'], 0, 5)),
            ]);
        }

        return redirect()
            ->route('cv-panel.cvs.index', ['nationality_id' => $data['nationality_id']])
            ->with('success', $message);
    }

    /**
     * الرفع شغل التنسيق: هو من يجلب السير ويعرف بياناتها.
     * خدمة العملاء تحجز ولا ترفع، والمدير يشرف ولا يرفع.
     */
    public static function canUpload(?\App\Models\Admin $me): bool
    {
        return $me !== null && ($me->isSuperAdmin() || $me->isCoordination());
    }

    private function authorizeUpload($me): void
    {
        abort_unless(self::canUpload($me), 403, __('cv-panel.upload.denied'));
    }

    /** الجنسيات التي يحقّ للمستخدم الرفع إليها. */
    private function allowedNationalities($me)
    {
        // السوبر أدمن وحده يرفع لأي جنسية؛ المنسّق مقصور على المسندة إليه
        if ($me->isSuperAdmin()) {
            return Nationality::where('active', true)->orderBy('name')->get();
        }

        return $me->managedNationalities()->where('active', true)->orderBy('name')->get();
    }
}
