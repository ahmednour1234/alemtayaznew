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
    ) {}

    public function create(): View
    {
        $me = Auth::guard('admin')->user();

        return view('cv-panel.upload', [
            'nationalities' => $this->allowedNationalities($me),
            'experiences'   => Worker::experienceOptions(),
            'religions'     => Worker::religionOptions(),
            'professions'   => Worker::professions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $me      = Auth::guard('admin')->user();
        $allowed = $this->allowedNationalities($me)->pluck('id')->all();

        $data = $request->validate([
            // الجنسية مقصورة على ما يديره المستخدم — لا يكفي التحقق من وجودها
            'nationality_id' => ['required', 'integer', Rule::in($allowed)],
            'experience'     => ['required', Rule::in(array_keys(Worker::experienceOptions()))],
            'religion'       => ['required', Rule::in(array_keys(Worker::religionOptions()))],
            'profession'     => ['nullable', Rule::in(array_keys(Worker::professions()))],
            'cvs'            => ['required', 'array', 'min:1', 'max:100'],
            'cvs.*'          => ['file', 'mimes:pdf', 'max:10240'],
        ], [
            'nationality_id.required' => __('cv-panel.upload.nationality_required'),
            'nationality_id.in'       => __('cv-panel.upload.nationality_denied'),
            'experience.required'     => __('cv-panel.upload.experience_required'),
            'religion.required'       => __('cv-panel.upload.religion_required'),
            'cvs.required'            => __('cv-panel.upload.files_required'),
            'cvs.max'                 => __('cv-panel.upload.files_max'),
        ]);

        $result = $this->service->upload($data, $request->file('cvs'), $me);

        $message = __('cv-panel.upload.done', ['count' => count($result['created'])]);

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

    /** الجنسيات التي يحقّ للمستخدم الرفع إليها. */
    private function allowedNationalities($me)
    {
        if ($me->isSuperAdmin() || ! $me->isCoordination()) {
            return Nationality::where('active', true)->orderBy('name')->get();
        }

        return $me->managedNationalities()->where('active', true)->orderBy('name')->get();
    }
}
