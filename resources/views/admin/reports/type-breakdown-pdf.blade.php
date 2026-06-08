<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { direction: rtl; font-size: 12px; color: #1e293b; }
        h1 { font-size: 18px; text-align: center; margin-bottom: 4px; }
        .meta { text-align: center; color: #64748b; font-size: 11px; margin-bottom: 16px; }
        h2 { font-size: 14px; margin: 18px 0 6px; padding: 6px; }
        h2.inc { background: #dcfce7; color: #166534; }
        h2.exp { background: #fee2e2; color: #991b1b; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: right; }
        th { background: #f1f5f9; }
        tfoot td { font-weight: bold; background: #f8fafc; }
        .summary td { font-weight: bold; }
        .net { font-size: 14px; }
    </style>
</head>
<body>
    <h1>تقرير الإيرادات والمصروفات حسب البند</h1>
    <div class="meta">
        @if($report['date_from'] || $report['date_to'])
            الفترة: {{ $report['date_from'] ?? '...' }} إلى {{ $report['date_to'] ?? '...' }}
        @else
            كل الفترات
        @endif
    </div>

    <h2 class="inc">الإيرادات حسب البند</h2>
    <table>
        <thead>
            <tr><th>البند</th><th>عدد العمليات</th><th>الإجمالي</th></tr>
        </thead>
        <tbody>
            @forelse($report['income_rows'] as $row)
            <tr>
                <td>{{ $row['name'] }}</td>
                <td>{{ number_format($row['count']) }}</td>
                <td>{{ number_format($row['total'], 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="3" style="text-align:center;color:#94a3b8;">لا توجد إيرادات</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr><td>الإجمالي</td><td></td><td>{{ number_format($report['income_total'], 2) }}</td></tr>
        </tfoot>
    </table>

    <h2 class="exp">المصروفات حسب البند (المعتمدة)</h2>
    <table>
        <thead>
            <tr><th>البند</th><th>عدد العمليات</th><th>الإجمالي</th></tr>
        </thead>
        <tbody>
            @forelse($report['expense_rows'] as $row)
            <tr>
                <td>{{ $row['name'] }}</td>
                <td>{{ number_format($row['count']) }}</td>
                <td>{{ number_format($row['total'], 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="3" style="text-align:center;color:#94a3b8;">لا توجد مصروفات</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr><td>الإجمالي</td><td></td><td>{{ number_format($report['expense_total'], 2) }}</td></tr>
        </tfoot>
    </table>

    <table class="summary">
        <tr>
            <td class="net">الصافي (الإيرادات - المصروفات)</td>
            <td class="net">{{ number_format($report['net'], 2) }}</td>
        </tr>
    </table>
</body>
</html>
