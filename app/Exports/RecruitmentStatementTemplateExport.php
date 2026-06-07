<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RecruitmentStatementTemplateExport implements FromArray, WithColumnWidths, WithHeadings, WithStyles, WithTitle
{
    public function title(): string
    {
        return 'كشف الاستقدام';
    }

    public function headings(): array
    {
        // ترتيب الأعمدة يطابق الملف الفعلي المرفوع (A→K):
        // A=رقم العقد, B=هوية صاحب العمل, C=رقم الصادر, D=المهنة, E=الجنسية,
        // F=ايراد استقدام, G=تكاليف الاستقدام, H=مصاريف مباشرة للعقود, I=الضريبي,
        // J=تاريخ بداية العقد, K=الفرع (اسم أو كود رقمي)
        return [
            'رقم العقد',
            'هوية صاحب العمل',
            'رقم الصادر',
            'المهنة',
            'الجنسية',
            'ايراد استقدام',
            'تكاليف الاستقدام',
            'مصاريف مباشرة للعقود',
            'الضريبي',
            'تاريخ بداية العقد',
            'الفرع',
        ];
    }

    public function array(): array
    {
        // عمود الفرع (K) يقبل: اسم الفرع (الرياض، عرعر، حفر الباطن) أو رمز الفرع (امتياز، متميز، انجاز)
        return [
            ['13155973', '1106644873', '1908026802', 'عاملة منزلية', 'اثيوبيا',  3418.85, 2250, 16.5, 0, '2026-05-30', 'الرياض'],
            ['13155877', '1037210349', '1907973282', 'عاملة منزلية', 'كينيا',    5974.83, 3750, 112.05, 0, '2026-05-30', 'الرياض'],
            ['13159575', '1083989036', '1908029401', 'عاملة منزلية', 'بنجلاديش', 7604.32, 4500, 53, 0, '2026-05-31', 'عرعر'],
            ['13155929', '1109992998', '1907992588', 'عاملة منزلية', 'اثيوبيا',  4023.75, 2250, 16.5, 0, '2026-06-01', 'حفر الباطن'],
            ['13166376', '1080010091', '1908032688', 'عاملة منزلية', 'كينيا',    6075.05, 3750, 112.05, 0, '2026-06-01', 'حفر الباطن'],
            ['13175191', '1028158630', '1908030289', 'عاملة منزلية', 'اثيوبيا',  4029.5,  2250, 16.5, 0, '2026-06-02', 'عرعر'],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getRowDimension(1)->setRowHeight(30);

        $styles = [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1e3a5f']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ],
        ];

        // Alternate row colors
        foreach ([2, 4, 6] as $r) {
            $styles[$r] = ['fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'f0fdf4']]];
        }
        foreach ([3, 5, 7] as $r) {
            $styles[$r] = ['fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'fffbeb']]];
        }

        return $styles;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 14, // رقم العقد
            'B' => 18, // هوية صاحب العمل
            'C' => 16, // رقم الصادر
            'D' => 18, // المهنة
            'E' => 14, // الجنسية
            'F' => 18, // ايراد استقدام
            'G' => 20, // تكاليف الاستقدام
            'H' => 24, // مصاريف مباشرة للعقود
            'I' => 14, // الضريبي
            'J' => 22, // تاريخ بداية العقد
            'K' => 16, // الفرع
        ];
    }
}
