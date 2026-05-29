<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>عقد نقل كفالة — {{ $transfer->contract_number }}</title>
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
        .header-right { }
        .header-badge {
            background: rgba(201,168,76,.2); border: 1px solid rgba(201,168,76,.4);
            border-radius: 8px; padding: 6px 16px; display: inline-block;
            color: #c9a84c; font-size: 11px; font-weight: 700; letter-spacing: .06em;
            margin-bottom: 8px;
        }
        .header-title { color: #fff; font-size: 22px; font-weight: 900; line-height: 1.2; }
        .header-sub { color: #94a3b8; font-size: 12px; margin-top: 4px; }
        .header-left { text-align: left; }
        .contract-num {
            color: #c9a84c; font-size: 18px; font-weight: 800;
            border: 2px solid rgba(201,168,76,.4); border-radius: 10px;
            padding: 8px 16px; background: rgba(201,168,76,.08);
        }
        .contract-date { color: #94a3b8; font-size: 11px; margin-top: 6px; text-align: center; }

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

        /* ── Body ── */
        .body { padding: 28px 32px; }

        /* Worker card */
        .worker-card {
            background: linear-gradient(135deg, #fdf8e8, #fffdf5);
            border: 1.5px solid #f0e0a4; border-radius: 12px;
            padding: 16px 20px; margin-bottom: 24px;
            display: flex; align-items: center; gap: 16px;
        }
        .worker-avatar {
            width: 52px; height: 52px; border-radius: 12px;
            background: #c9a84c; display: flex; align-items: center; justify-content: center;
            font-size: 22px; font-weight: 800; color: #fff; flex-shrink: 0;
        }
        .worker-name { font-size: 16px; font-weight: 800; color: #0f172a; }
        .worker-meta { font-size: 12px; color: #64748b; margin-top: 3px; }
        .worker-status {
            margin-right: auto;
            padding: 5px 14px; border-radius: 20px;
            font-size: 12px; font-weight: 700;
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
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .info-card {
            border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 16px;
        }
        .info-row { display: flex; justify-content: space-between; align-items: center; padding: 7px 0; border-bottom: 1px solid #f8fafc; }
        .info-row:last-child { border: none; padding-bottom: 0; }
        .info-label { font-size: 12px; color: #64748b; font-weight: 500; }
        .info-value { font-size: 13px; font-weight: 700; color: #0f172a; }

        /* Financial box */
        .finance-card {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border: 1.5px solid #e2e8f0; border-radius: 12px; overflow: hidden;
            margin-bottom: 20px;
        }
        .finance-header {
            background: #e2e8f0; padding: 10px 16px;
            font-size: 11px; font-weight: 700; color: #475569; letter-spacing: .05em;
        }
        .finance-body { padding: 16px; }
        .finance-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #e2e8f0; }
        .finance-row:last-child { border: none; padding-bottom: 0; }
        .finance-label { font-size: 12px; color: #64748b; }
        .finance-value { font-size: 14px; font-weight: 800; }
        .net-positive { color: #16a34a; }
        .net-negative { color: #dc2626; }
        .net-row { background: #fff; border-radius: 8px; padding: 10px 12px; margin-top: 10px; border: 1.5px solid #e2e8f0; }

        /* Signatures */
        .signatures { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-top: 28px; }
        .sig-box {
            border: 1px dashed #cbd5e1; border-radius: 10px;
            padding: 16px; text-align: center; min-height: 90px;
        }
        .sig-label { font-size: 11px; color: #94a3b8; font-weight: 600; margin-bottom: 8px; }
        .sig-name { font-size: 12px; color: #334155; font-weight: 700; margin-top: 8px; }
        .sig-line { border-top: 1px solid #cbd5e1; margin: 8px 20px 0; }

        /* Footer */
        .print-footer {
            margin-top: 24px; padding-top: 16px;
            border-top: 1px solid #e2e8f0;
            display: flex; justify-content: space-between; align-items: center;
        }
        .print-footer span { font-size: 10px; color: #94a3b8; }

        /* Notes */
        .notes-box {
            background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;
            padding: 12px 14px; font-size: 12px; color: #475569; line-height: 1.7;
        }

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
            <div class="header-badge">عقد نقل كفالة</div>
            <div class="header-title">عقد نقل الكفالة</div>
            <div class="header-sub">Sponsorship Transfer Contract</div>
        </div>
        <div class="header-left">
            <div class="contract-num">{{ $transfer->contract_number }}</div>
            <div class="contract-date">{{ now()->format('Y/m/d') }}</div>
        </div>
    </div>

    {{-- Status bar --}}
    <div class="status-bar">
        <span style="font-size:12px;color:#64748b;font-weight:600;">الحالة:</span>
        <span class="status-pill" style="background:{{ $transfer->status_color }}22;color:{{ $transfer->status_color }};border:1px solid {{ $transfer->status_color }}44;">
            {{ $transfer->status_label }}
        </span>
        <span style="margin-right:auto;font-size:12px;color:#64748b;">
            @if($transfer->transfer_date)
            تاريخ النقل: <strong>{{ $transfer->transfer_date->format('Y/m/d') }}</strong>
            @endif
        </span>
    </div>

    <div class="body">

        {{-- Worker card --}}
        <div class="worker-card">
            <div class="worker-avatar">
                {{ mb_substr($transfer->worker?->name ?? '؟', 0, 1) }}
            </div>
            <div>
                <div class="worker-name">{{ $transfer->worker?->name ?? '—' }}</div>
                <div class="worker-meta">
                    الجنسية: {{ $transfer->worker?->nationality?->name ?? '—' }}
                    @if($transfer->worker?->passport_number)
                     &nbsp;|&nbsp; جواز سفر: {{ $transfer->worker->passport_number }}
                    @endif
                </div>
            </div>
            <div class="worker-status">نقل كفالة</div>
        </div>

        {{-- Sponsors grid --}}
        <div class="section-title">بيانات الكفلاء</div>
        <div class="grid-2" style="margin-bottom:24px;">
            <div class="info-card">
                <div style="font-size:11px;color:#dc2626;font-weight:700;margin-bottom:8px;display:flex;align-items:center;gap:5px;">
                    <span style="width:8px;height:8px;border-radius:50%;background:#dc2626;display:inline-block;"></span>
                    الكفيل المُحيل (من)
                </div>
                <div style="font-size:15px;font-weight:800;color:#0f172a;">{{ $transfer->fromClient?->name ?? '—' }}</div>
                @if($transfer->fromClient?->phone)
                <div style="font-size:11px;color:#64748b;margin-top:4px;">{{ $transfer->fromClient->phone }}</div>
                @endif
            </div>
            <div class="info-card">
                <div style="font-size:11px;color:#16a34a;font-weight:700;margin-bottom:8px;display:flex;align-items:center;gap:5px;">
                    <span style="width:8px;height:8px;border-radius:50%;background:#16a34a;display:inline-block;"></span>
                    الكفيل المستلم (إلى)
                </div>
                <div style="font-size:15px;font-weight:800;color:#0f172a;">{{ $transfer->toClient?->name ?? 'لم يُحدد بعد' }}</div>
                @if($transfer->toClient?->phone)
                <div style="font-size:11px;color:#64748b;margin-top:4px;">{{ $transfer->toClient->phone }}</div>
                @endif
            </div>
        </div>

        {{-- Financial details --}}
        <div class="section-title">البيانات المالية</div>
        <div class="finance-card">
            <div class="finance-header">تفاصيل الرسوم</div>
            <div class="finance-body">
                <div class="finance-row">
                    <span class="finance-label">إجمالي الرسوم</span>
                    <span class="finance-value" style="color:#0f172a;">{{ number_format($transfer->total_fees) }} ريال</span>
                </div>
                <div class="finance-row">
                    <span class="finance-label">رسوم الخدمة</span>
                    <span class="finance-value" style="color:#2563eb;">{{ number_format($transfer->service_fee) }} ريال</span>
                </div>
                <div class="finance-row">
                    <span class="finance-label">الفقد (خسارة)</span>
                    <span class="finance-value net-negative">— {{ number_format($transfer->loss_amount) }} ريال</span>
                </div>
                <div class="net-row finance-row" style="border-bottom:none;">
                    <span style="font-size:13px;font-weight:700;color:#334155;">صافي النتيجة</span>
                    <span class="finance-value {{ $transfer->net_result >= 0 ? 'net-positive' : 'net-negative' }}" style="font-size:18px;">
                        {{ number_format($transfer->net_result) }} ريال
                    </span>
                </div>
            </div>
        </div>

        @if($transfer->notes)
        <div class="section-title">ملاحظات</div>
        <div class="notes-box">{{ $transfer->notes }}</div>
        @endif

        {{-- Signatures --}}
        <div class="signatures">
            <div class="sig-box">
                <div class="sig-label">الكفيل المُحيل</div>
                <div class="sig-line"></div>
                <div class="sig-name">{{ $transfer->fromClient?->name ?? '—' }}</div>
            </div>
            <div class="sig-box">
                <div class="sig-label">الكفيل المستلم</div>
                <div class="sig-line"></div>
                <div class="sig-name">{{ $transfer->toClient?->name ?? 'لم يُحدد' }}</div>
            </div>
            <div class="sig-box">
                <div class="sig-label">ممثل الشركة</div>
                <div class="sig-line"></div>
                <div class="sig-name" style="color:#94a3b8;">التوقيع والختم</div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="print-footer">
            <span>طُبع بتاريخ: {{ now()->format('Y/m/d H:i') }}</span>
            <span>{{ $transfer->contract_number }}</span>
        </div>
    </div>
</div>

<script>
    window.addEventListener('load', function() { window.print(); });
</script>
</body>
</html>
