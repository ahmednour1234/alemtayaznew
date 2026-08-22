<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Models\Nationality;
use App\Models\Worker;
use Illuminate\Http\Request;

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
            'profession'     => ['nullable', 'string', 'max:50'],
            'experience'     => ['nullable', 'string', 'max:10'],
            'religion'       => ['nullable', 'string', 'max:20'],
            'age_min'        => ['nullable', 'integer', 'min:18', 'max:60'],
            'age_max'        => ['nullable', 'integer', 'min:18', 'max:60'],
            'search'         => ['nullable', 'string', 'max:100'],
        ]);

        $query = $this->baseQuery()->with('nationality');

        if (! empty($filters['nationality_id'])) {
            $query->where('nationality_id', $filters['nationality_id']);
        }
        if (! empty($filters['profession'])) {
            $query->where('profession', $filters['profession']);
        }
        if (! empty($filters['experience'])) {
            $query->where('experience', $filters['experience']);
        }
        if (! empty($filters['religion'])) {
            $query->where('religion', $filters['religion']);
        }
        if (! empty($filters['age_min'])) {
            $query->where('age', '>=', $filters['age_min']);
        }
        if (! empty($filters['age_max'])) {
            $query->where('age', '<=', $filters['age_max']);
        }

        // البحث بالاسم فقط — رقم الجواز مشفّر ولا يجوز كشفه في واجهة عامة
        if (! empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        $workers = $query->latest('id')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return view('public.cvs.index', [
            'workers'       => $workers,
            'filters'       => $filters,
            'nationalities' => Nationality::where('active', true)->orderBy('name')->get(),
            'professions'   => Worker::professions(),
            'experiences'   => Worker::experienceOptions(),
            'religions'     => Worker::religionOptions(),
        ]);
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

    /** الشرط الموحّد لما يجوز عرضه للعامة. */
    private function baseQuery()
    {
        return Worker::query()
            ->where('active', true)
            ->where('status', 'available')
            ->whereNotNull('cv_path');
    }
}
