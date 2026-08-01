<?php

namespace App\Exports;

use App\Models\RecruitmentContract;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

/**
 * نموذج تحديث حالات العقود بالجملة.
 * يُطابق بالعقد عن طريق رقم التأشيرة، ثم يحدّث الحالة وتاريخها.
 */
class ContractStatusTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    /** قائمة الحالات المتاحة: "1=جديد | 2=..." */
    private function statusLegend(): string
    {
        $parts = [];
        foreach (RecruitmentContract::statuses() as $num => $meta) {
            $parts[] = "{$num}={$meta['label']}";
        }
        return implode(' | ', $parts);
    }

    public function array(): array
    {
        return [
            // سطر الشرح والقيم المقبولة
            [
                'رقم التأشيرة كما هو مسجّل في النظام — يُستخدم للبحث عن العقد',
                'رقم الحالة الجديدة (1–15). ' . $this->statusLegend(),
                'تاريخ الحالة — صيغة YYYY-MM-DD (اختياري، الافتراضي تاريخ اليوم)',
                'ملاحظة تُرسل مع إشعار واتساب (اختياري)',
            ],
            // سطر المثال
            [
                'V-123456',
                '8',
                '2026-02-15',
                'تم استلام الجواز مختوماً',
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'رقم التأشيرة *',
            'الحالة (1–15) *',
            'تاريخ الحالة (YYYY-MM-DD)',
            'رسالة واتساب (اختياري)',
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 26, 'B' => 120, 'C' => 30, 'D' => 34];
    }

    public function title(): string
    {
        return 'تحديث الحالات';
    }

    public function styles(Worksheet $sheet): array
    {
        // صف العناوين
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '1D4ED8']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true],
        ]);
        // سطر الشرح — خلفية رمادية
        $sheet->getStyle('A2:D2')->applyFromArray([
            'font'      => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '475569']],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => 'F1F5F9']],
            'alignment' => ['wrapText' => true, 'vertical' => Alignment::VERTICAL_TOP],
        ]);
        // سطر المثال — خلفية صفراء فاتحة
        $sheet->getStyle('A3:D3')->applyFromArray([
            'font' => ['size' => 10, 'color' => ['rgb' => '1E3A5F']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FEF9C3']],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(25);
        $sheet->getRowDimension(2)->setRowHeight(60);
        $sheet->setRightToLeft(true);

        return [];
    }
}
