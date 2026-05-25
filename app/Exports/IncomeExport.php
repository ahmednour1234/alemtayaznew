<?php

namespace App\Exports;

use App\Models\Income;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class IncomeExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(private readonly array $filters = []) {}

    public function query()
    {
        return Income::query()
            ->with(['branch', 'incomeType'])
            ->when(!empty($this->filters['branch_id']), fn($q) => $q->where('branch_id', $this->filters['branch_id']))
            ->when(!empty($this->filters['income_type_id']), fn($q) => $q->where('income_type_id', $this->filters['income_type_id']))
            ->when(!empty($this->filters['payment_method']), fn($q) => $q->where('payment_method', $this->filters['payment_method']))
            ->when(!empty($this->filters['date_from']), fn($q) => $q->whereDate('date', '>=', $this->filters['date_from']))
            ->when(!empty($this->filters['date_to']), fn($q) => $q->whereDate('date', '<=', $this->filters['date_to']))
            ->latest('date');
    }

    public function headings(): array
    {
        return ['رمز الفرع', 'نوع الدخل', 'المبلغ', 'التاريخ', 'طريقة الدفع', 'رقم المرجع', 'الوصف'];
    }

    public function map($income): array
    {
        return [
            $income->branch?->code,
            $income->incomeType?->name,
            $income->amount,
            $income->date?->format('Y-m-d'),
            $income->payment_method,
            $income->reference_number,
            $income->description,
        ];
    }
}
