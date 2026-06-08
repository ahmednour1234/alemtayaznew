<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Exports\TypeBreakdownExport;
use App\Http\Controllers\Controller;
use App\Services\BranchService;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class TypeBreakdownController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService,
        private readonly BranchService $branchService,
    ) {}

    /** الفروع المسموح بها للمستخدم الحالي */
    private function branchIds(Request $request): array
    {
        $me = Auth::guard('admin')->user();

        return $me->isBranchAdmin()
            ? [$me->branch_id]
            : array_filter((array) $request->input('branch_ids', []));
    }

    public function index(Request $request)
    {
        $me       = Auth::guard('admin')->user();
        $branches = $me->isBranchAdmin()
            ? $this->branchService->allActive()->where('id', $me->branch_id)
            : $this->branchService->allActive();

        $report = null;
        if ($request->isMethod('get') && ($request->filled('date_from') || $request->filled('date_to') || $request->filled('branch_ids') || $me->isBranchAdmin())) {
            $report = $this->reportService->getTypeBreakdown(
                $this->branchIds($request),
                $request->date_from,
                $request->date_to,
            );
        }

        return view('admin.reports.type-breakdown', ['branches' => $branches, 'report' => $report]);
    }

    public function exportExcel(Request $request)
    {
        $report = $this->reportService->getTypeBreakdown(
            $this->branchIds($request),
            $request->date_from,
            $request->date_to,
        );

        return Excel::download(
            new TypeBreakdownExport($report),
            'type_breakdown_' . now()->format('Y-m-d') . '.xlsx',
        );
    }

    public function exportPdf(Request $request)
    {
        $report = $this->reportService->getTypeBreakdown(
            $this->branchIds($request),
            $request->date_from,
            $request->date_to,
        );

        // خط Amiri مثبّت مسبقاً في مجلد storage/fonts لدعم تشكيل الحروف العربية،
        // ويُستدعى عبر CSS (font-family: 'Amiri') في قالب الـ PDF.
        $pdf = Pdf::loadView('admin.reports.type-breakdown-pdf', ['report' => $report])
            ->setPaper('a4', 'portrait');

        return $pdf->download('type_breakdown_' . now()->format('Y-m-d') . '.pdf');
    }
}
