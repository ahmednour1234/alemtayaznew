<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CombinedStatementTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new CombinedIncomeSheetTemplate(),
            new CombinedExpenseSheetTemplate(),
        ];
    }
}
