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
