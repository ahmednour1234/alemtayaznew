<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * تصدير السير التي فكّ النظام حجزها تلقائياً.
 *
 * الصفوف تأتي جاهزة من الأمر، فالمنطق يبقى في مكان واحد ولا يتفرّع بين
 * العرض في الطرفية والتصدير.
 */
class AutoReleasedWorkersExport implements FromArray, WithHeadings, WithStyles
{
    public function __construct(private readonly array $rows) {}

    public function headings(): array
    {
        return [
            'رقم السيرة',
            'الاسم',
            'الجنسية',
            'العميل السابق',
            'الحاجز',
            'الحالة الآن',
            'مرتبطة بعميل',
            'تاريخ الفكّ',
        ];
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function styles(Worksheet $sheet): array
    {
        // صفّ العناوين بارز ليسهل تمييزه عند الفرز والتصفية
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
