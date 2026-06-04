<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طباعة رحلة {{ $trip->trip_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Cairo', Arial, sans-serif;
            direction: rtl;
            background: #f1f5f9;
            color: #1e293b;
            padding: 28px;
        }
        .card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 32px rgba(0,0,0,.10);
            overflow: hidden;
            max-width: 960px;
            margin: 0 auto;
        }
        /* ── Top header ── */
        .card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 24px 32px;
            border-bottom: 2px solid #f1f5f9;
        }
        .logo-area { display: flex; align-items: center; gap: 14px; }
        .logo-area img { height: 52px; object-fit: contain; }
        .logo-text .name { font-size: 15px; font-weight: 900; color: #0f172a; }
        .logo-text .sub  { font-size: 10px; font-weight: 600; color: #c9a84c; letter-spacing: .06em; }
        .trip-meta { text-align: left; direction: ltr; }
        .trip-number { font-size: 26px; font-weight: 900; color: #0f172a; letter-spacing: -.5px; }
        .trip-status { font-size: 16px; font-weight: 700; color: #c9a84c; margin-top: 2px; }
        /* ── Info cards row ── */
        .info-row {
            display: flex;
            border-bottom: 2px solid #f1f5f9;
        }
        .info-card {
            flex: 1;
            padding: 16px 20px;
            border-left: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .info-card:last-child { border-left: 0; }
        .info-icon {
            width: 38px; height: 38px;
            border-radius: 10px;
            background: #fef9ee;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .info-label { font-size: 10px; color: #94a3b8; font-weight: 700; margin-bottom: 3px; letter-spacing: .03em; }
        .info-value { font-size: 13px; font-weight: 700; color: #0f172a; }
        /* ── Workers table ── */
        .workers-wrap { padding: 24px 32px; }
        .section-label {
            font-size: 11px; font-weight: 700; color: #94a3b8;
            letter-spacing: .07em; text-transform: uppercase;
            margin-bottom: 14px; padding-bottom: 10px;
            border-bottom: 1.5px solid #f1f5f9;
            display: flex; align-items: center; gap: 7px;
        }
        .tbl-wrap { border: 1.5px solid #f1f5f9; border-radius: 12px; overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #0f172a; }
        thead th {
            padding: 11px 16px; text-align: right;
            font-size: 11px; font-weight: 700; color: #94a3b8;
            letter-spacing: .04em;
        }
        tbody tr { border-top: 1px solid #f1f5f9; }
        tbody tr:first-child { border-top: 0; }
        tbody td { padding: 13px 16px; }
        .num-cell { font-size: 13px; font-weight: 700; color: #94a3b8; }
        .worker-name { font-size: 14px; font-weight: 700; color: #0f172a; }
        .worker-file { font-size: 11px; color: #94a3b8; margin-top: 2px; }
        .nat-badge {
            display: inline-flex; align-items: center; gap: 5px;
            background: #e0f2fe; color: #0369a1;
            font-size: 11px; font-weight: 700;
            padding: 3px 10px 3px 8px; border-radius: 20px;
        }
        .passport-cell { font-size: 12px; color: #334155; font-family: monospace; letter-spacing: .04em; }
        .notes-cell { font-size: 13px; color: #64748b; }
        /* ── Summary ── */
        .summary {
            display: flex; justify-content: flex-end;
            padding: 12px 0 0;
            font-size: 13px; color: #64748b;
            gap: 20px;
        }
        .summary strong { color: #0f172a; }
        /* ── Footer ── */
        .card-footer {
            text-align: center;
            padding: 20px 32px 24px;
            border-top: 1.5px solid #f1f5f9;
            background: #fafbfc;
        }
        .footer-slogan { font-size: 14px; color: #64748b; margin-bottom: 6px; }
        .footer-divider {
            width: 44px; height: 2.5px;
            background: linear-gradient(90deg, #c9a84c, #f0d080);
            margin: 0 auto 8px; border-radius: 2px;
        }
        .footer-company { font-size: 13px; font-weight: 800; color: #0f172a; }
        @media print {
            body { background: #fff; padding: 0; }
            .card { box-shadow: none; border-radius: 0; max-width: 100%; }
        }
    </style>
</head>
<body onload="window.print()">
@php
    function natFlag($code) {
        if (!$code || strlen($code) !== 2) return '';
        return mb_chr(ord(strtoupper($code[0])) + 127397) . mb_chr(ord(strtoupper($code[1])) + 127397);
    }
    $typeLabel = match($trip->trip_type) {
        'arrival'         => 'وصول',
        'departure'       => 'مغادرة',
        'group_transport' => 'نقل جماعي',
        'deportation'     => 'ترحيل',
        default           => $trip->trip_type,
    };
    $tripDate = \Carbon\Carbon::parse($trip->trip_date)->format('Y/m/d');
    $tripTime = $trip->trip_time ? ' ' . $trip->trip_time : '';
@endphp

<div class="card">

    {{-- ── Header ── --}}
    <div class="card-top">
        <div class="logo-area">
            <img src="{{ asset('1759760768-33.png') }}" alt="شركة الامتياز للاستقدام">
            <div class="logo-text">
                <div class="name">شركة الامتياز للاستقدام</div>
                <div class="sub">ALIMTIAZ RECRUITMENT COMPANY</div>
            </div>
        </div>
        <div class="trip-meta">
            <div class="trip-number">رحلة: {{ $trip->trip_number }}</div>
            <div class="trip-status">{{ $typeLabel }}</div>
        </div>
    </div>

    {{-- ── Info cards ── --}}
    <div class="info-row">
        {{-- التاريخ --}}
        <div class="info-card">
            <div class="info-icon">
                <svg width="18" height="18" fill="none" stroke="#c9a84c" stroke-width="1.8" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
            </div>
            <div>
                <div class="info-label">التاريخ</div>
                <div class="info-value">{{ $tripDate }}{{ $tripTime }}</div>
            </div>
        </div>
        {{-- المطار --}}
        <div class="info-card">
            <div class="info-icon">
                <svg width="18" height="18" fill="none" stroke="#c9a84c" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M21 16v-2a4 4 0 00-4-4H5M3 8l4-4 4 4M7 4v16"/>
                </svg>
            </div>
            <div>
                <div class="info-label">المطار</div>
                <div class="info-value">{{ $trip->airport?->name ?? '—' }}</div>
            </div>
        </div>
        {{-- رقم الرحلة الجوية --}}
        <div class="info-card">
            <div class="info-icon">
                <svg width="18" height="18" fill="none" stroke="#c9a84c" stroke-width="1.8" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/>
                    <path d="M12 2a15.3 15.3 0 010 20M12 2a15.3 15.3 0 000 20"/>
                </svg>
            </div>
            <div>
                <div class="info-label">رقم الرحلة الجوية</div>
                <div class="info-value">{{ $trip->flight_number ?? '—' }}</div>
            </div>
        </div>
        {{-- الفرع --}}
        <div class="info-card">
            <div class="info-icon">
                <svg width="18" height="18" fill="none" stroke="#c9a84c" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
                </svg>
            </div>
            <div>
                <div class="info-label">الفرع</div>
                <div class="info-value">{{ $trip->branch?->name ?? '—' }}</div>
            </div>
        </div>
    </div>

    {{-- ── Workers table ── --}}
    <div class="workers-wrap">
        <div class="section-label">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>
            </svg>
            قائمة العاملات
        </div>

        <div class="tbl-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="width:36px;">#</th>
                        <th>اسم العاملة</th>
                        <th>الجنسية</th>
                        <th>رقم الجواز</th>
                        <th>العميل</th>
                        <th>رقم الهوية</th>
                        <th>الجوال</th>
                        <th>الفرع</th>
                        <th>محطة الاستلام</th>
                        <th>ملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($trip->workers as $i => $worker)
                @php $wContract = isset($contracts[$worker->pivot->contract_id]) ? $contracts[$worker->pivot->contract_id] : null; @endphp
                    <tr>
                        <td class="num-cell">{{ $i + 1 }}</td>
                        <td>
                            <div class="worker-name">{{ $worker->name }}</div>
                            @if($worker->file_number)
                            <div class="worker-file">{{ $worker->file_number }}</div>
                            @endif
                        </td>
                        <td>
                            @if($worker->nationality)
                            <span class="nat-badge">
                                {{ natFlag($worker->nationality->code) }}
                                {{ $worker->nationality->name }}
                            </span>
                            @else
                            <span style="color:#cbd5e1;">—</span>
                            @endif
                        </td>
                        <td class="passport-cell">{{ $worker->passport_number ?: '—' }}</td>
                        <td style="font-size:13px;font-weight:700;color:#0f172a;">{{ $wContract?->client?->name ?? '—' }}</td>
                        <td class="passport-cell">{{ $wContract?->client?->national_id ?? '—' }}</td>
                        <td class="passport-cell">{{ $wContract?->client?->phone ?? '—' }}</td>
                        <td style="font-size:12px;color:#16a34a;font-weight:600;">{{ $wContract?->branch?->name ?? '—' }}</td>
                        <td style="font-size:12px;color:#0369a1;font-weight:600;">{{ $wContract?->deliveryCity?->name ?? '—' }}</td>
                        <td class="notes-cell">{{ $worker->pivot->notes ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="text-align:center;padding:32px;color:#94a3b8;font-size:13px;">
                            لا توجد عاملات مضافة لهذه الرحلة
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="summary">
            <span>إجمالي العاملات: <strong>{{ $trip->workers->count() }}</strong></span>
        </div>
    </div>

    {{-- ── Footer ── --}}
    <div class="card-footer">
        <div class="footer-slogan">مع تمنياتنا برحلة موفقة وأمنة</div>
        <div class="footer-divider"></div>
        <div class="footer-company">شركة الامتياز للاستقدام</div>
    </div>

</div>
</body>
</html>
