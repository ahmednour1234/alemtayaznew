<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CombinedStatementImport implements WithMultipleSheets
{
    private int $incomeCount   = 0;
    private int $expenseCount  = 0;

    public function sheets(): array
    {
        $incomeImport  = new IncomeImport();
        $expenseImport = new ExpenseImport();

        return [
            0 => $incomeImport,
            1 => $expenseImport,
        ];
    }
}
