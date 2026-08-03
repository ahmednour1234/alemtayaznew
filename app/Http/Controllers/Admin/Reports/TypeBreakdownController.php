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

        // mPDF يفشل في تحليل جدول GPOS الخاص بخط Amiri ويرمي:
        //   "GPOS Lookup Type 5, Format 3 not supported"
        // ويحدث ذلك عند تحميل الخط قبل قراءة useOTL، فلا تنفع أي قيمة له.
        // لذلك نولّد نسخة من الخط بلا GPOS ونستخدمها. التوليد يتم هنا في الكود
        // (لا كملف مرفوع) حتى لا يعتمد الحل على رفع ملفات ثنائية أو مسح الكاش
        // يدوياً. GSUB يبقى وهو المسؤول عن وصل الحروف العربية وتشكيلها.
        $fontDir = $this->prepareFonts($tempDir);

        $config = [
            'mode'             => 'utf-8',
            'format'           => 'A4',
            'directionality'   => 'rtl',
            'default_font'     => 'amiri',
            // معطّلان عمداً: تفعيلهما قد يستبدل amiri بخط افتراضي لا يدعم العربية.
            'autoScriptToLang' => false,
            'autoLangToFont'   => false,
            'tempDir'          => $tempDir,
            'fontDir'          => [$fontDir],
            'fontdata'         => [
                'amiri' => [
                    'R'      => 'amiri_r_nogpos.ttf',
                    'B'      => 'amiri_b_nogpos.ttf',
                    'useOTL' => 0xFF,
                ],
            ],
        ];

        try {
            $pdf = $this->renderPdf($config, $html);
        } catch (\Mpdf\Exception\FontException $e) {
            // بيانات خط مخزّنة من محاولة سابقة قد تكون تالفة — نظّف، أعد بناء
            // الخطوط من الأصل، ثم أعد المحاولة مرة واحدة.
            $this->clearFontCache($tempDir);
            $this->prepareFonts($tempDir);
            $pdf = $this->renderPdf($config, $html);
        }

        $filename = 'type_breakdown_' . now()->format('Y-m-d') . '.pdf';

        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * يبني (مرة واحدة) نسخة من خط Amiri بلا جدول GPOS داخل مجلد العمل،
     * ويُعيد مسار المجلد الذي يحوي الخطوط الجاهزة لـ mPDF.
     */
    private function prepareFonts(string $tempDir): string
    {
        $outDir = $tempDir . '/fonts';
        if (! is_dir($outDir)) {
            @mkdir($outDir, 0775, true);
        }

        $map = [
            'amiri_normal.ttf' => 'amiri_r_nogpos.ttf',
            'amiri_bold.ttf'   => 'amiri_b_nogpos.ttf',
        ];

        foreach ($map as $source => $target) {
            $src = storage_path('fonts/' . $source);
            $dst = $outDir . '/' . $target;

            if (! is_file($src)) {
                continue;
            }

            // أعد البناء فقط إذا كان الأصل أحدث من الناتج.
            if (is_file($dst) && filemtime($dst) >= filemtime($src)) {
                continue;
            }

            $this->stripFontTables($src, $dst, ['GPOS']);
        }

        return $outDir;
    }

    /** يعيد كتابة ملف TTF بعد حذف الجداول المطلوبة منه. */
    private function stripFontTables(string $src, string $dst, array $drop): void
    {
        $data = file_get_contents($src);
        if ($data === false || strlen($data) < 12) {
            return;
        }

        $numTables = unpack('n', substr($data, 4, 2))[1];
        $tables    = [];

        for ($i = 0; $i < $numTables; $i++) {
            $rec = 12 + $i * 16;
            $tag = substr($data, $rec, 4);
            if (in_array($tag, $drop, true)) {
                continue;
            }
            $offset = unpack('N', substr($data, $rec + 8, 4))[1];
            $length = unpack('N', substr($data, $rec + 12, 4))[1];
            $tables[$tag] = [
                'checksum' => substr($data, $rec + 4, 4),
                'data'     => substr($data, $offset, $length),
            ];
        }

        ksort($tables);
        $count = count($tables);

        // searchRange / entrySelector / rangeShift حسب مواصفة TTF
        $searchRange = 1;
        $entrySelector = 0;
        while ($searchRange * 2 <= $count) {
            $searchRange *= 2;
            $entrySelector++;
        }
        $searchRange *= 16;

        $header = pack('N', 0x00010000)
            . pack('nnnn', $count, $searchRange, $entrySelector, $count * 16 - $searchRange);

        $offset    = 12 + $count * 16;
        $directory = '';
        $body      = '';

        foreach ($tables as $tag => $table) {
            $length     = strlen($table['data']);
            $directory .= $tag . $table['checksum'] . pack('N', $offset) . pack('N', $length);
            $padding    = (4 - ($length % 4)) % 4;
            $body      .= $table['data'] . str_repeat("\0", $padding);
            $offset    += $length + $padding;
        }

        file_put_contents($dst, $header . $directory . $body);
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
        $patterns = [
            $tempDir . '/ttfontdata/*',
            $tempDir . '/*.mtx.php',
            $tempDir . '/*.cw.dat',
            // والخطوط المولّدة نفسها، حتى يُعاد بناؤها من الأصل.
            $tempDir . '/fonts/*',
        ];

        foreach ($patterns as $pattern) {
            foreach (glob($pattern) ?: [] as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }
        }
    }
}
