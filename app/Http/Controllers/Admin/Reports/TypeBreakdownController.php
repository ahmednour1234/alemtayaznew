<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Exports\TypeBreakdownExport;
use App\Http\Controllers\Controller;
use App\Services\BranchService;
use App\Services\ReportService;
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

    /** تفاصيل عمليات بند معيّن — تُعاد كـ JSON للـ popup */
    public function details(Request $request)
    {
        $kind   = $request->input('kind') === 'expense' ? 'expense' : 'income';
        $typeId = (int) $request->input('type_id');

        $data = $this->reportService->getTypeDetails(
            $kind,
            $typeId,
            $this->branchIds($request),
            $request->date_from,
            $request->date_to,
        );

        return response()->json($data);
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

        // يُستخدم mPDF لا dompdf: الأخير لا يطبّق تشكيل الحروف العربية
        // (Arabic shaping) فتظهر الكلمات مقطّعة ومعكوسة الترتيب.
        $html = view('admin.reports.type-breakdown-pdf', ['report' => $report])->render();

        // mPDF يفشل إن لم يجد مجلد الملفات المؤقتة.
        $tempDir = storage_path('app/mpdf');
        if (! is_dir($tempDir)) {
            @mkdir($tempDir, 0775, true);
        }

        $config = [
            'mode'             => 'utf-8',
            'format'           => 'A4',
            'directionality'   => 'rtl',
            'default_font'     => 'amiri',
            // معطّلان عمداً: تفعيلهما قد يستبدل amiri بخط افتراضي لا يدعم العربية.
            'autoScriptToLang' => false,
            'autoLangToFont'   => false,
            'tempDir'          => $tempDir,
            'fontDir'          => [storage_path('fonts')],
            'fontdata'         => [
                // نسخة من خط Amiri أُزيل منها جدول GPOS فقط، مع الإبقاء على
                // GSUB وهو المسؤول عن تشكيل الحروف العربية ووصلها.
                //
                // السبب: mPDF يفشل في تحليل GPOS الخاص بـ Amiri
                // (Chaining Contextual Positioning) ويرمي:
                //   "GPOS Lookup Type 5, Format 3 not supported"
                // وهذا التحليل يحدث عند تحميل الخط قبل قراءة useOTL،
                // فلا تنفع أي قيمة له — الحل إزالة الجدول من الخط نفسه.
                // GPOS مسؤول عن التموضع الدقيق للتشكيل فقط، وغيابه لا يؤثر
                // على اتصال الحروف.
                'amiri' => [
                    'R' => 'amiri_normal_nogpos.ttf',
                    'B' => 'amiri_bold_nogpos.ttf',
                    'useOTL' => 0xFF,
                ],
            ],
        ];

        try {
            $pdf = $this->renderPdf($config, $html);
        } catch (\Mpdf\Exception\FontException $e) {
            // قد تكون بيانات الخط المخزّنة مؤقتاً تالفة من محاولة سابقة —
            // نظّف الكاش وأعد المحاولة مرة واحدة.
            $this->clearFontCache($tempDir);
            $pdf = $this->renderPdf($config, $html);
        }

        $filename = 'type_breakdown_' . now()->format('Y-m-d') . '.pdf';

        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /** يولّد الـ PDF ويُعيده كنص ثنائي. */
    private function renderPdf(array $config, string $html): string
    {
        $mpdf = new \Mpdf\Mpdf($config);
        $mpdf->SetDirectionality('rtl');
        $mpdf->WriteHTML($html);

        return $mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN);
    }

    /** يحذف ملفات بيانات الخطوط المؤقتة التي يبنيها mPDF. */
    private function clearFontCache(string $tempDir): void
    {
        foreach ([$tempDir . '/ttfontdata/*', $tempDir . '/*.mtx.php', $tempDir . '/*.cw.dat'] as $pattern) {
            foreach (glob($pattern) ?: [] as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }
        }
    }
}
