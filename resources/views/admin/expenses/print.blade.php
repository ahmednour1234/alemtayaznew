<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>سند مصروف — {{ $expense->id }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'Cairo', Arial, sans-serif;
            direction: rtl; color: #1e293b;
            background: #f8fafc; min-height: 100vh;
            padding: 24px;
        }
        .page {
            max-width: 800px; margin: 0 auto;
            background: #fff; border-radius: 16px;
            box-shadow: 0 4px 32px rgba(0,0,0,.10);
            overflow: hidden;
        }

        /* ── Header ── */
        .header {
            background: linear-gradient(135deg, #0f172a 0%, #1a2744 100%);
            padding: 28px 32px;
            display: flex; justify-content: space-between; align-items: center;
        }
        .header-badge {
            background: rgba(201,168,76,.2); border: 1px solid rgba(201,168,76,.4);
            border-radius: 8px; padding: 6px 16px; display: inline-block;
            color: #c9a84c; font-size: 11px; font-weight: 700; letter-spacing: .06em;
            margin-bottom: 8px;
        }
        .header-title { color: #fff; font-size: 22px; font-weight: 900; line-height: 1.2; }
        .header-sub { color: #94a3b8; font-size: 12px; margin-top: 4px; }
        .header-left { text-align: left; }
        .doc-num {
            color: #c9a84c; font-size: 18px; font-weight: 800;
            border: 2px solid rgba(201,168,76,.4); border-radius: 10px;
            padding: 8px 16px; background: rgba(201,168,76,.08);
        }
        .doc-date { color: #94a3b8; font-size: 11px; margin-top: 6px; text-align: center; }

        /* ── Status bar ── */
        .status-bar {
            padding: 10px 32px;
            display: flex; gap: 16px; align-items: center;
            background: #f8fafc; border-bottom: 1px solid #e2e8f0;
        }
        .status-pill {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 5px 14px; border-radius: 20px;
            font-size: 12px; font-weight: 700;
        }

        .body { padding: 28px 32px; }

        /* Amount card */
        .amount-card {
            background: linear-gradient(135deg, #fdf8e8, #fffdf5);
            border: 1.5px solid #f0e0a4; border-radius: 12px;
            padding: 18px 20px; margin-bottom: 24px;
            display: flex; align-items: center; justify-content: space-between;
        }
        .amount-label { font-size: 12px; color: #64748b; font-weight: 600; }
        .amount-value { font-size: 28px; font-weight: 900; color: #dc2626; margin-top: 4px; }
        .amount-words { font-size: 11px; color: #94a3b8; margin-top: 4px; }
        .amount-type {
            padding: 6px 16px; border-radius: 20px; font-size: 12px; font-weight: 700;
            background: #fef3c7; color: #d97706; border: 1px solid #fde68a;
        }

        /* Sections */
        .section-title {
            font-size: 11px; font-weight: 700; color: #94a3b8;
            letter-spacing: .08em; text-transform: uppercase;
            margin-bottom: 12px; padding-bottom: 8px;
            border-bottom: 1px solid #f1f5f9;
            display: flex; align-items: center; gap: 6px;
        }
        .section-title::before {
            content: ''; display: block; width: 3px; height: 14px;
            background: #c9a84c; border-radius: 2px; flex-shrink: 0;
        }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px; }
        .info-card { border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 16px; }
        .info-row { display: flex; justify-content: space-between; align-items: center; padding: 7px 0; border-bottom: 1px solid #f8fafc; }
        .info-row:last-child { border: none; padding-bottom: 0; }
        .info-label { font-size: 12px; color: #64748b; font-weight: 500; }
        .info-value { font-size: 13px; font-weight: 700; color: #0f172a; }

        .notes-box {
            background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;
            padding: 12px 14px; font-size: 12px; color: #475569; line-height: 1.9;
            margin-bottom: 24px;
        }
        .reject-box {
            background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px;
            padding: 12px 14px; font-size: 12px; color: #b91c1c; line-height: 1.8;
            margin-bottom: 24px;
        }

        /* Signatures */
        .signatures { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-top: 28px; }
        .sig-box {
            border: 1px dashed #cbd5e1; border-radius: 10px;
            padding: 16px; text-align: center; min-height: 90px;
        }
        .sig-label { font-size: 11px; color: #94a3b8; font-weight: 600; margin-bottom: 8px; }
        .sig-name { font-size: 12px; color: #334155; font-weight: 700; margin-top: 8px; }
        .sig-line { border-top: 1px solid #cbd5e1; margin: 8px 20px 0; }

        .print-footer {
            margin-top: 24px; padding-top: 16px;
            border-top: 1px solid #e2e8f0;
            display: flex; justify-content: space-between; align-items: center;
        }
        .print-footer span { font-size: 10px; color: #94a3b8; }

        @media print {
            body { background: #fff; padding: 0; }
            .page { box-shadow: none; border-radius: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

{{-- Print / Close buttons --}}
<div class="no-print" style="max-width:800px;margin:0 auto 16px;display:flex;gap:10px;justify-content:flex-end;">
    <button onclick="window.print()"
            style="padding:10px 24px;background:linear-gradient(135deg,#c9a84c,#a88830);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;font-family:'Cairo',sans-serif;cursor:pointer;">
        طباعة
    </button>
    <button onclick="window.close()"
            style="padding:10px 18px;background:#fff;color:#64748b;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:'Cairo',sans-serif;cursor:pointer;">
        إغلاق
    </button>
</div>

<div class="page">
    {{-- Header --}}
    <div class="header">
        <div class="header-right">
            <div class="header-badge">سند صرف</div>
            <div class="header-title">تفاصيل المصروف</div>
            <div class="header-sub">Expense Voucher</div>
        </div>
        <div class="header-left">
            <div class="doc-num">#{{ $expense->id }}</div>
            <div class="doc-date">{{ now()->format('Y/m/d') }}</div>
        </div>
    </div>

    {{-- Status bar --}}
    @php
        $statusColor = match($expense->status) {
            'approved' => '#16a34a',
            'pending'  => '#d97706',
            default    => '#dc2626',
        };
        $statusLabel = match($expense->status) {
            'approved' => 'معتمد',
            'pending'  => 'معلق',
            default    => 'مرفوض',
        };
    @endphp
    <div class="status-bar">
        <span style="font-size:12px;color:#64748b;font-weight:600;">الحالة:</span>
        <span class="status-pill" style="background:{{ $statusColor }}22;color:{{ $statusColor }};border:1px solid {{ $statusColor }}44;">
            {{ $statusLabel }}
        </span>
        <span style="margin-right:auto;font-size:12px;color:#64748b;">
            @if($expense->date)
            تاريخ المصروف: <strong>{{ $expense->date->format('Y/m/d') }}</strong>
            @endif
        </span>
    </div>

    <div class="body">

        {{-- Amount --}}
        <div class="amount-card">
            <div>
                <div class="amount-label">المبلغ المصروف</div>
                <div class="amount-value">{{ number_format($expense->amount, 2) }} ريال</div>
            </div>
            <div class="amount-type">{{ $expense->expenseType?->name ?? 'مصروف' }}</div>
        </div>

        {{-- Details --}}
        <div class="section-title">بيانات المصروف</div>
        <div class="grid-2">
            <div class="info-card">
                <div class="info-row">
                    <span class="info-label">الفرع</span>
                    <span class="info-value">{{ $expense->branch?->name ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">نوع المصروف</span>
                    <span class="info-value">{{ $expense->expenseType?->name ?? '—' }}</span>
                </div>
            </div>
            <div class="info-card">
                <div class="info-row">
                    <span class="info-label">التاريخ</span>
                    <span class="info-value">{{ $expense->date?->format('Y/m/d') ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">المرفق</span>
                    <span class="info-value">{{ $expense->attachment ? 'يوجد' : 'لا يوجد' }}</span>
                </div>
            </div>
        </div>

        @if($expense->approved_at)
        <div class="section-title">الاعتماد</div>
        <div class="grid-2">
            <div class="info-card">
                <div class="info-row">
                    <span class="info-label">تاريخ الاعتماد</span>
                    <span class="info-value">{{ $expense->approved_at->format('Y/m/d H:i') }}</span>
                </div>
            </div>
            <div class="info-card">
                <div class="info-row">
                    <span class="info-label">اعتمد بواسطة</span>
                    <span class="info-value">{{ $expense->approver?->name ?? '—' }}</span>
                </div>
            </div>
        </div>
        @endif

        @if($expense->description)
        <div class="section-title">الوصف</div>
        <div class="notes-box">{{ $expense->description }}</div>
        @endif

        @if($expense->rejection_reason)
        <div class="section-title">سبب الرفض</div>
        <div class="reject-box">{{ $expense->rejection_reason }}</div>
        @endif

        {{-- Signatures --}}
        <div class="signatures">
            <div class="sig-box">
                <div class="sig-label">المستلم</div>
                <div class="sig-line"></div>
                <div class="sig-name" style="color:#94a3b8;">التوقيع</div>
            </div>
            <div class="sig-box">
                <div class="sig-label">المحاسب</div>
                <div class="sig-line"></div>
                <div class="sig-name" style="color:#94a3b8;">التوقيع</div>
            </div>
            <div class="sig-box">
                <div class="sig-label">المدير المالي</div>
                <div class="sig-line"></div>
                <div class="sig-name">{{ $expense->approver?->name ?? 'التوقيع والختم' }}</div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="print-footer">
            <span>طُبع بتاريخ: {{ now()->format('Y/m/d H:i') }}</span>
            <span>سند مصروف #{{ $expense->id }}</span>
        </div>
    </div>
</div>

<script>
    window.addEventListener('load', function() { window.print(); });
</script>
</body>
</html>
