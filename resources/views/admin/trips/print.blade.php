<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طباعة رحلة {{ $trip->trip_number }}</title>
    <style>
        body { font-family: 'Cairo', Arial, sans-serif; direction: rtl; color: #1e293b; margin: 20px; }
        h1 { font-size: 20px; color: #0f172a; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #e2e8f0; padding: 8px 12px; text-align: right; font-size: 13px; }
        th { background: #f8fafc; font-weight: 600; }
        .meta { display: flex; gap: 40px; margin: 12px 0; font-size: 13px; }
        .meta span { color: #64748b; }
        @media print { body { margin: 0; } }
    </style>
</head>
<body onload="window.print()">
    <h1>رحلة: {{ $trip->trip_number }} — {{ $trip->type_label }}</h1>
    <div class="meta">
        <div><span>التاريخ: </span>{{ \Carbon\Carbon::parse($trip->trip_date)->format('Y/m/d') }}
            @if($trip->trip_time) {{ $trip->trip_time }} @endif</div>
        <div><span>المطار: </span>{{ $trip->airport?->name ?? '—' }}</div>
        <div><span>رقم الرحلة الجوية: </span>{{ $trip->flight_number ?? '—' }}</div>
        <div><span>الفرع: </span>{{ $trip->branch?->name ?? '—' }}</div>
    </div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>اسم العاملة</th>
                <th>الجنسية</th>
                <th>ملاحظات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($trip->workers as $i => $worker)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $worker->name }}</td>
                <td>{{ $worker->nationality?->name ?? '—' }}</td>
                <td>{{ $worker->pivot->notes ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <p style="margin-top:16px; font-size:12px; color:#94a3b8;">
        إجمالي العاملات: {{ $trip->workers->count() }}
    </p>
</body>
</html>
