<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طباعة عقد نقل كفالة {{ $transfer->contract_number }}</title>
    <style>
        body { font-family: 'Cairo', Arial, sans-serif; direction: rtl; color: #1e293b; margin: 20px; font-size: 13px; }
        h1 { font-size: 18px; color: #0f172a; border-bottom: 2px solid #c9a84c; padding-bottom: 8px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 16px 0; }
        .card { border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; }
        .card h2 { font-size: 13px; color: #64748b; margin: 0 0 8px; }
        .row { display: flex; justify-content: space-between; padding: 4px 0; border-bottom: 1px solid #f1f5f9; }
        .row:last-child { border: none; }
        .label { color: #64748b; }
        .value { font-weight: 600; }
        .net-pos { color: #16a34a; }
        .net-neg { color: #dc2626; }
        @media print { body { margin: 0; } }
    </style>
</head>
<body onload="window.print()">
    <h1>عقد نقل كفالة — {{ $transfer->contract_number }}</h1>
    <div class="grid">
        <div class="card">
            <h2>بيانات العقد</h2>
            <div class="row"><span class="label">العاملة</span><span class="value">{{ $transfer->worker?->name }}</span></div>
            <div class="row"><span class="label">من عميل</span><span class="value">{{ $transfer->fromClient?->name ?? '—' }}</span></div>
            <div class="row"><span class="label">إلى عميل</span><span class="value">{{ $transfer->toClient?->name ?? '—' }}</span></div>
            <div class="row"><span class="label">تاريخ النقل</span><span class="value">{{ $transfer->transfer_date ?? '—' }}</span></div>
            <div class="row"><span class="label">الحالة</span><span class="value">{{ $transfer->status_label }}</span></div>
        </div>
        <div class="card">
            <h2>البيانات المالية</h2>
            <div class="row"><span class="label">إجمالي الرسوم</span><span class="value">{{ number_format($transfer->total_fees) }}</span></div>
            <div class="row"><span class="label">رسوم الخدمة</span><span class="value">{{ number_format($transfer->service_fee) }}</span></div>
            <div class="row"><span class="label">الفقد</span><span class="value" style="color:#dc2626">{{ number_format($transfer->loss_amount) }}</span></div>
            <div class="row">
                <span class="label">صافي النتيجة</span>
                <span class="value {{ $transfer->net_result >= 0 ? 'net-pos' : 'net-neg' }}">
                    {{ number_format($transfer->net_result) }}
                </span>
            </div>
        </div>
    </div>
    @if($transfer->notes)
    <div class="card" style="margin-top:16px">
        <h2>ملاحظات</h2>
        <p>{{ $transfer->notes }}</p>
    </div>
    @endif
</body>
</html>
