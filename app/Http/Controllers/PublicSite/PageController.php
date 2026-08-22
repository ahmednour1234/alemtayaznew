<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Models\Nationality;
use App\Models\Worker;

/**
 * صفحات الموقع التعريفي: الرئيسية، من نحن، تواصل معنا.
 */
class PageController extends Controller
{
    public function home()
    {
        // الجنسيات التي لدينا فيها عاملات متاحة فعلاً — لا نعرض جنسية فارغة
        $nationalities = Nationality::where('active', true)
            ->whereHas('workers', fn ($q) => $this->availableScope($q))
            ->withCount(['workers' => fn ($q) => $this->availableScope($q)])
            ->orderBy('name')
            ->get();

        $featured = Worker::query()
            ->tap(fn ($q) => $this->availableScope($q))
            ->with('nationality')
            ->latest('id')
            ->take(8)
            ->get();

        $stats = [
            'available'     => Worker::query()->tap(fn ($q) => $this->availableScope($q))->count(),
            'nationalities' => $nationalities->count(),
        ];

        return view('public.home', compact('nationalities', 'featured', 'stats') + [
            'features' => $this->features(),
            'steps'    => $this->steps(),
            'counters' => [
                ['v' => $stats['available'] . '+',     'l' => 'عاملة متاحة'],
                ['v' => $stats['nationalities'] . '+', 'l' => 'جنسية متنوعة'],
                ['v' => '98%',                         'l' => 'رضا العملاء'],
                ['v' => '24/7',                        'l' => 'دعم ومتابعة'],
            ],
        ]);
    }

    public function about()
    {
        return view('public.about');
    }

    /** مزايا الشركة المعروضة أسفل الواجهة الرئيسية. */
    private function features(): array
    {
        return [
            ['t' => 'عمالة موثّقة',     'd' => 'سير ذاتية موثّقة وجوازات سارية',  'i' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['t' => 'ضمان بعد الوصول',  'd' => 'فترة ضمان وتدريب بعد الاستلام',   'i' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
            ['t' => 'سرعة الإنجاز',     'd' => 'إجراءات مختصرة ومتابعة يومية',    'i' => 'M13 10V3L4 14h7v7l9-11h-7z'],
            ['t' => 'متابعة مستمرة',    'd' => 'تتبّع حالة معاملتك أولاً بأول',    'i' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'],
        ];
    }

    /** خطوات الاستقدام المعروضة في الصفحة الرئيسية. */
    private function steps(): array
    {
        return [
            ['n' => '1', 't' => 'اختيار العاملة',   'd' => 'تصفّح السير الذاتية واختر ما يناسبك'],
            ['n' => '2', 't' => 'تقديم الطلب',      'd' => 'استكمال البيانات وتوقيع العقد'],
            ['n' => '3', 't' => 'إجراءات التأشيرة', 'd' => 'إصدار التأشيرة ومتابعة الإجراءات'],
            ['n' => '4', 't' => 'الوصول والاستلام', 'd' => 'استقبال العاملة وتسليمها لك'],
        ];
    }

    /**
     * الشرط الموحّد لما يجوز عرضه للعامة:
     * عاملة نشطة، متاحة، ولديها ملف CV.
     */
    private function availableScope($query)
    {
        return $query->where('active', true)
            ->where('status', 'available')
            ->whereNotNull('cv_path');
    }
}
