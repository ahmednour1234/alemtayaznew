<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Models\Nationality;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * تصفّح السير الذاتية المتاحة للعامة، مع بحث وفلاتر.
 *
 * لا يُعرض هنا إلا ما هو متاح فعلاً: عاملة نشطة، حالتها «متاحة»، ولديها ملف CV.
 * لا تُعرض أي بيانات تعريفية (جواز/هاتف) — هذه مشفّرة وخاصة بلوحة التحكم.
 */
class CvController extends Controller
{
    private const PER_PAGE = 12;

    public function index(Request $request)
    {
        $filters = $request->validate([
            'nationality_id' => ['nullable', 'integer', 'exists:nationalities,id'],
            // الخبرة والديانة يُختاران من قوائم النظام لا نصاً حرّاً،
            // فلا يمرّ إلى الاستعلام إلا مفتاح معروف.
            'experience'     => ['nullable', Rule::in(array_keys(Worker::experienceOptions()))],
            'religion'       => ['nullable', Rule::in(array_keys(Worker::religionOptions()))],
        ]);

        $query = $this->baseQuery()->with('nationality');

        foreach (['nationality_id', 'experience', 'religion'] as $field) {
            if (! empty($filters[$field])) {
                $query->where($field, $filters[$field]);
            }
        }

        $workers = $query->latest('id')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        // طلب التمرير اللانهائي يجلب البطاقات وحدها ليُلحقها بالقائمة
        if ($request->boolean('partial')) {
            return response()->view('public.cvs._cards', ['workers' => $workers]);
        }

        return view('public.cvs.index', [
            'workers'       => $workers,
            'filters'       => $filters,
            'experiences'   => Worker::experienceOptions(),
            'religions'     => Worker::religionOptions(),
            'nationalities' => Nationality::where('active', true)
                ->whereHas('workers', fn ($q) => $q->where('active', true)
                    ->where('status', 'available')->whereNotNull('cv_path'))
                ->orderBy('name')
                ->get(),
        ]);
    }

    /**
     * صفحة جنسية واحدة: /nationality/et
     *
     * تُترجم المفتاح إلى معرّف الجنسية ثم تُعيد استخدام index() نفسها،
     * فتبقى الفلاتر والترقيم والبحث كما هي بلا تكرار للمنطق.
     */
    public function byNationality(Request $request, string $key)
    {
        $nationality = Nationality::where('active', true)
            ->where(function ($q) use ($key) {
                $q->whereRaw('LOWER(code) = ?', [strtolower($key)]);
                if (ctype_digit($key)) {
                    $q->orWhere('id', (int) $key);
                }
            })
            ->firstOrFail();

        $request->merge(['nationality_id' => $nationality->id]);

        $response = $this->index($request);

        // طلب جزئي (تمرير لانهائي) يعود ببطاقات فقط، فلا بيانات إضافية له
        return $request->boolean('partial')
            ? $response
            : $response->with('activeNationality', $nationality);
    }

    public function show(int $id)
    {
        $worker = $this->baseQuery()->with('nationality')->findOrFail($id);

        $similar = $this->baseQuery()
            ->with('nationality')
            ->where('id', '!=', $worker->id)
            ->where('nationality_id', $worker->nationality_id)
            ->take(4)
            ->get();

        return view('public.cvs.show', compact('worker', 'similar'));
    }

    /**
     * يعرض ملف السيرة الذاتية PDF للزائر.
     *
     * لا يخدم إلا ما تخدمه الصفحة العامة نفسها (baseQuery): عاملة نشطة متاحة
     * ولها ملف. فور حجزها تختفي من القائمة ويُمنع ملفها كذلك، فلا يبقى الرابط
     * المنسوخ صالحاً بعد الحجز.
     *
     * نعرضه inline لا كتنزيل ليقرأه العميل في المتصفح مباشرة، وبلا اسم الملف
     * الأصلي لأنه قد يحمل بيانات داخلية.
     */
    public function pdf(int $id)
    {
        $worker = $this->baseQuery()->findOrFail($id);

        abort_unless($worker->hasCvFile(), 404);

        return response()->file(
            \Illuminate\Support\Facades\Storage::disk($worker->cvDisk())->path($worker->cv_path),
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="cv-' . $worker->id . '.pdf"',
            ]
        );
    }

    /** الشرط الموحّد لما يجوز عرضه للعامة. */
    private function baseQuery()
    {
        return Worker::query()
            ->where('active', true)
            ->where('status', 'available')
            ->whereNotNull('cv_path');
    }
}
