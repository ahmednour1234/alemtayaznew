<?php

namespace App\Http\Controllers\CvPanel;

use App\Http\Controllers\Controller;
use App\Models\Nationality;
use App\Models\Worker;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * لوحة إدارة السير الذاتية — الصفحة الرئيسية.
 *
 * منظومة مستقلة عن لوحة الإدارة العامة: يعمل فيها المنسّق على الجنسيات
 * المسندة إليه وحدها، ويرى خدمة العملاء ما هو متاح للحجز.
 */
class DashboardController extends Controller
{
    public function index(): View
    {
        $me = Auth::guard('admin')->user();

        // نطاق الجنسيات: المنسّق يرى المسند إليه، ومن سواه يرى الكل
        $scope = $this->nationalityScope($me);

        $base = fn () => Worker::query()
            ->when($scope !== null, fn ($q) => $q->whereIn('nationality_id', $scope))
            ->whereNotNull('cv_path')
            ->where('active', true);

        /*
         * خدمة العملاء تُقاس بحجوزاتها هي لا بمخزون السير كلّه: أرقام عامّة
         * لا تخصّها ولا تدلّها على شيء. فنعرض لها ما حجزته هي، ونُخفي شبكة
         * الجنسيات وأحدث ما رُفع — فذلك شأن التنسيق.
         */
        $isAgent = $me->isCustomerService() && ! $me->isSuperAdmin();

        if ($isAgent) {
            $mine = fn () => Worker::query()
                ->whereNotNull('cv_path')
                ->where('assigned_by_admin_id', $me->id);

            return view('cv-panel.dashboard', [
                'agentView' => true,
                'stats'     => [
                    'my_reserved' => (clone $mine())->where('status', 'reserved')->count(),
                    'my_assigned' => (clone $mine())->where('status', 'assigned')->count(),
                    'my_today'    => (clone $mine())->whereDate('assigned_at', today())->count(),
                    'available'   => (clone $base())->where('status', 'available')->count(),
                ],
                // الروابط العامة يحتاجها الموظّف ليرسلها للعملاء — وهي أصل
                // عمله. المخفيّ عنه أعداد المخزون لا الروابط نفسها.
                'nationalities' => $this->visibleNationalities($me),
                'recent'        => (clone $mine())->with(['nationality', 'client'])
                    ->latest('assigned_at')->limit(12)->get(),
            ]);
        }

        return view('cv-panel.dashboard', [
            'agentView' => false,
            'stats' => [
                'available' => (clone $base())->where('status', 'available')->count(),
                'reserved'  => (clone $base())->where('status', 'reserved')->count(),
                'assigned'  => (clone $base())->where('status', 'assigned')->count(),
                'today'     => (clone $base())->whereDate('created_at', today())->count(),
            ],
            'nationalities' => $this->visibleNationalities($me),
            'recent'        => (clone $base())->with('nationality')->latest()->limit(12)->get(),
        ]);
    }

    /**
     * معرّفات الجنسيات التي يراها المستخدم، أو null إن كان يرى الكل.
     * null لا مصفوفة فارغة — فالفارغة تعني «لا شيء» لا «كل شيء».
     */
    protected function nationalityScope($me): ?array
    {
        if ($me->isSuperAdmin() || ! $me->isCoordination()) {
            return null;
        }

        return $me->managedNationalities->pluck('id')->all();
    }

    /** الجنسيات المعروضة للمستخدم مع عدد السير المتاحة في كل منها. */
    protected function visibleNationalities($me)
    {
        $scope = $this->nationalityScope($me);

        return Nationality::where('active', true)
            ->when($scope !== null, fn ($q) => $q->whereIn('id', $scope))
            ->withCount(['workers as available_count' => fn ($q) => $q
                ->where('active', true)
                ->where('status', 'available')
                ->whereNotNull('cv_path')])
            ->orderBy('name')
            ->get();
    }
}
