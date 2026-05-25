<?php

namespace App\Exports;

use App\Models\Expense;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExpenseExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(private readonly array $filters = []) {}

    public function query()
    {
        return Expense::query()
            ->with(['branch', 'expenseType'])
            ->when(!empty($this->filters['branch_id']), fn($q) => $q->where('branch_id', $this->filters['branch_id']))
            ->when(!empty($this->filters['expense_type_id']), fn($q) => $q->where('expense_type_id', $this->filters['expense_type_id']))
            ->when(!empty($this->filters['status']), fn($q) => $q->where('status', $this->filters['status']))
            ->when(!empty($this->filters['date_from']), fn($q) => $q->whereDate('date', '>=', $this->filters['date_from']))
            ->when(!empty($this->filters['date_to']), fn($q) => $q->whereDate('date', '<=', $this->filters['date_to']))
            ->latest('date');
    }

    public function headings(): array
    {
        return ['رمز الفرع', 'نوع المصروف', 'المبلغ', 'التاريخ', 'طريقة الدفع', 'الحالة', 'رقم المرجع', 'الوصف'];
    }

    public function map($expense): array
    {
        return [
            $expense->branch?->code,
            $expense->expenseType?->name,
            $expense->amount,
            $expense->date?->format('Y-m-d'),
            $expense->payment_method,
            $expense->status,
            $expense->reference_number,
            $expense->description,
        ];
    }
}
