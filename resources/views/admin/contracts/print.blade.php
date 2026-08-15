<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('contracts.print.doc_title') }} - {{ $contract->contract_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Cairo', sans-serif !important; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; margin: 0; padding: 0; }
            .page { box-shadow: none !important; margin: 0 !important; padding: 20mm 15mm !important;
                    border-radius: 0 !important; border: none !important; }
            .page-break { page-break-before: always; }
        }
        .timeline-dot-done  { background: #16a34a; }
        .timeline-dot-cur   { background: #c9a84c; }
        .timeline-dot-empty { background: #e2e8f0; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen py-8 px-4">

    {{-- Action bar --}}
    <div class="no-print max-w-4xl mx-auto mb-5 flex items-center justify-between">
        <a href="{{ route('admin.contracts.show', $contract->id) }}"
           class="flex items-center gap-2 text-sm text-slate-600 bg-white border border-slate-200 rounded-xl px-4 py-2 shadow-sm hover:text-blue-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
            {{ __('contracts.print.back') }}
        </a>
        <button onclick="window.print()"
                class="flex items-center gap-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-xl px-5 py-2 shadow transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            {{ __('contracts.print.print') }} / PDF
        </button>
    </div>

    {{-- Contract page --}}
    <div class="page max-w-4xl mx-auto bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden" style="padding: 0;">

        {{-- ── Header ────────────────────────────────────────────────────────── --}}
        <div style="background:#fff; padding: 20px 36px 16px; border-bottom: 3px solid #1e3a5f; display:flex; align-items:center; justify-content:space-between; gap:16px;">
            <img src="{{ asset('1759760768-33.png') }}" alt="{{ __('nav.company') }}"
                 style="height:52px;object-fit:contain;">
            <div style="text-align:left;">
                <p style="font-size:11px;color:#64748b;margin:0 0 2px;">{{ __('contracts.list.col_number') }}</p>
                <p style="font-size:14px;font-weight:800;color:#1e3a5f;font-family:'Courier New',monospace;margin:0 0 6px;">{{ $contract->contract_number }}</p>
                <p style="font-size:12px;color:#64748b;margin:0;">{{ $contract->request_date?->format('Y/m/d') ?? '—' }} | {{ $contract->branch?->name ?? '—' }}</p>
            </div>
        </div>
        <div style="background: linear-gradient(135deg, #1a2744 0%, #c9a84c 100%); padding: 10px 36px;">
            <h1 style="font-size:18px;font-weight:800;color:#fff;margin:0;">{{ __('contracts.print.doc_title') }}</h1>
        </div>

        {{-- ── Status badge strip ─────────────────────────────────────────────── --}}
        @php
            $statusColors = [13 => ['bg'=>'#dcfce7','txt'=>'#15803d','brd'=>'#86efac'], 9 => ['bg'=>'#fee2e2','txt'=>'#dc2626','brd'=>'#fca5a5'], 15 => ['bg'=>'#fee2e2','txt'=>'#dc2626','brd'=>'#fca5a5']];
            $sc = $statusColors[$contract->current_status] ?? ['bg'=>'#fdf8e8','txt'=>'#a88830','brd'=>'#f0d98a'];
            $depts = \App\Models\RecruitmentContract::departments();
        @endphp
        <div style="background:#f8fafc; border-bottom: 1px solid #e2e8f0; padding: 10px 36px; display:flex; align-items:center; gap:16px;">
            <span style="background:{{ $sc['bg'] }}; color:{{ $sc['txt'] }}; border:1px solid {{ $sc['brd'] }}; border-radius:999px; padding:4px 14px; font-size:12px; font-weight:700;">
                {{ $contract->status_label }}
            </span>
            <span style="background:#f1f5f9; color:#475569; border-radius:999px; padding:4px 14px; font-size:12px; font-weight:600;">
                {{ $depts[$contract->current_department] ?? $contract->current_department }}
            </span>
            @if($contract->payment_status === 'full')
            <span style="background:#dcfce7; color:#15803d; border-radius:999px; padding:4px 14px; font-size:12px; font-weight:700;">{{ __('contracts.print.paid_full') }}</span>
            @elseif($contract->payment_status === 'partial')
            <span style="background:#fef3c7; color:#92400e; border-radius:999px; padding:4px 14px; font-size:12px; font-weight:700;">{{ __('contracts.print.paid_partial') }}</span>
            @else
            <span style="background:#fee2e2; color:#dc2626; border-radius:999px; padding:4px 14px; font-size:12px; font-weight:700;">{{ __('contracts.print.paid_pending') }}</span>
            @endif
        </div>

        <div style="padding: 24px 36px;">

            {{-- ── Parties ─────────────────────────────────────────────────── --}}
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:20px;">
                <div style="border:1px solid #e2e8f0; border-radius:12px; padding:14px 18px; background:#fafbfc;">
                    <p style="font-size:10px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:8px; letter-spacing:.05em;">{{ __('contracts.print.client_data') }}</p>
                    <p style="font-size:15px; font-weight:700; color:#1e293b; margin-bottom:4px;">{{ $contract->client?->name ?? '—' }}</p>
                    @if($contract->client?->phone)
                    <p style="font-size:12px; color:#64748b;">📞 {{ $contract->client->phone }}</p>
                    @endif
                    @if($contract->client?->national_id)
                    <p style="font-size:12px; color:#64748b;">{{ __('common.fields.national_id') }}: {{ $contract->client->national_id }}</p>
                    @endif
                </div>
                <div style="border:1px solid #e2e8f0; border-radius:12px; padding:14px 18px; background:#fafbfc;">
                    <p style="font-size:10px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:8px; letter-spacing:.05em;">{{ __('contracts.print.worker_data') }}</p>
                    @if($contract->worker)
                    <p style="font-size:15px; font-weight:700; color:#1e293b; margin-bottom:4px;">{{ $contract->worker->name }}</p>
                    @if($contract->worker->nationality)
                    <p style="font-size:12px; color:#64748b;">{{ __('common.fields.nationality') }}: {{ $contract->worker->nationality->name }}</p>
                    @endif
                    @if($contract->agent)
                    <p style="font-size:12px; color:#64748b;">{{ __('contracts.show.agent') }}: {{ $contract->agent->name }}</p>
                    @endif
                    @else
                    <p style="font-size:13px; color:#94a3b8;">{{ __('contracts.print.no_worker') }}</p>
                    @endif
                </div>
            </div>

            {{-- ── Contract details table ──────────────────────────────────── --}}
            <div style="margin-bottom:20px;">
                <p style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.05em; margin-bottom:10px; padding-bottom:6px; border-bottom:1px solid #f1f5f9;">{{ __('contracts.print.details') }}</p>
                <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px;">
                    @php
                        $details = [
                            ['label'=> __('contracts.fields.visa_type'),    'value'=> \App\Models\RecruitmentContract::visaTypes()[$contract->visa_type] ?? '—'],
                            ['label'=> __('common.fields.visa_number'),     'value'=> $contract->visa_number ?? '—'],
                            ['label'=> __('common.fields.musaned_number'),  'value'=> $contract->musaned_number ?? '—'],
                            ['label'=> __('contracts.fields.musaned_date'), 'value'=> $contract->musaned_date?->format('Y/m/d') ?? '—'],
                            ['label'=> __('contracts.print.e_doc'),         'value'=> $contract->e_doc_number ?? '—'],
                            ['label'=> __('contracts.fields.total_cost'),   'value'=> $contract->total_cost ? number_format($contract->total_cost, 2) . ' ' . __('contracts.show.currency') : '—'],
                            ['label'=> __('common.fields.arrival_date'),    'value'=> $contract->arrival_date?->format('Y/m/d') ?? '—'],
                            ['label'=> __('contracts.show.trial_end'),      'value'=> $contract->trial_end_date?->format('Y/m/d') ?? '—'],
                            ['label'=> __('contracts.show.contract_end'),   'value'=> $contract->contract_end_date?->format('Y/m/d') ?? '—'],
                        ];
                    @endphp
                    @foreach($details as $d)
                    <div style="padding:10px 12px; background:#f8fafc; border-radius:8px; border:1px solid #f1f5f9;">
                        <p style="font-size:10px; color:#94a3b8; font-weight:600; margin-bottom:2px;">{{ $d['label'] }}</p>
                        <p style="font-size:13px; color:#1e293b; font-weight:600;">{{ $d['value'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- ── Airports ─────────────────────────────────────────────────── --}}
            @if($contract->arrivalAirport || $contract->departureAirport || $contract->deliveryAirport)
            <div style="margin-bottom:20px;">
                <p style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.05em; margin-bottom:10px; padding-bottom:6px; border-bottom:1px solid #f1f5f9;">{{ __('contracts.print.airports') }}</p>
                <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px;">
                    <div style="padding:10px 12px; background:#f0f9ff; border-radius:8px; border:1px solid #bae6fd;">
                        <p style="font-size:10px; color:#0284c7; font-weight:600; margin-bottom:2px;">{{ __('contracts.fields.arrival_airport') }}</p>
                        <p style="font-size:13px; color:#1e293b; font-weight:600;">{{ $contract->arrivalAirport?->name ?? '—' }}</p>
                    </div>
                    <div style="padding:10px 12px; background:#f0f9ff; border-radius:8px; border:1px solid #bae6fd;">
                        <p style="font-size:10px; color:#0284c7; font-weight:600; margin-bottom:2px;">{{ __('contracts.show.origin') }}</p>
                        <p style="font-size:13px; color:#1e293b; font-weight:600;">{{ $contract->departureAirport?->name ?? '—' }}</p>
                    </div>
                    <div style="padding:10px 12px; background:#f0f9ff; border-radius:8px; border:1px solid #bae6fd;">
                        <p style="font-size:10px; color:#0284c7; font-weight:600; margin-bottom:2px;">{{ __('contracts.fields.delivery_city') }}</p>
                        <p style="font-size:13px; color:#1e293b; font-weight:600;">{{ $contract->deliveryCity?->name ?? '—' }}</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- ── Status timeline ─────────────────────────────────────────── --}}
            <div style="margin-bottom:20px;">
                <p style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.05em; margin-bottom:12px; padding-bottom:6px; border-bottom:1px solid #f1f5f9;">{{ __('contracts.print.follow') }}</p>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:0; border:1px solid #e2e8f0; border-radius:10px; overflow:hidden;">
                    @foreach($statuses as $num => $st)
                    @php
                        $h        = $historyMap->get($num);
                        $isDone   = $h && $h->status_date;
                        $isCur    = $num === $contract->current_status;
                        $bgColor  = $isDone ? '#f0fdf4' : ($isCur ? '#eff6ff' : '#fff');
                        $txtColor = $isDone ? '#15803d' : ($isCur ? '#a88830' : '#64748b');
                        $dotColor = $isDone ? '#16a34a' : ($isCur ? '#c9a84c' : '#e2e8f0');
                    @endphp
                    <div style="display:flex; align-items:center; gap:10px; padding:9px 14px; background:{{ $bgColor }}; border-bottom:1px solid #f1f5f9; {{ $loop->odd ? 'border-left:1px solid #f1f5f9;' : '' }}">
                        <div style="width:22px; height:22px; border-radius:50%; background:{{ $dotColor }}; flex-shrink:0; display:flex; align-items:center; justify-content:center; color:#fff; font-size:10px; font-weight:700;">
                            @if($isDone) ✓ @else {{ $num }} @endif
                        </div>
                        <div style="flex:1;">
                            <p style="font-size:11.5px; font-weight:600; color:{{ $txtColor }}; margin:0;">{{ $st['label'] }}</p>
                            @if($isDone)
                            <p style="font-size:10px; color:#6b7280; margin:0;">{{ $h->status_date->format('Y/m/d') }}</p>
                            @elseif($isCur)
                            <p style="font-size:10px; color:#c9a84c; margin:0;">{{ __('contracts.edit_extra.current_stage') }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- ── Notes ───────────────────────────────────────────────────── --}}
            @if($contract->notes)
            <div style="margin-bottom:20px; padding:14px 18px; background:#fefce8; border:1px solid #fde68a; border-radius:10px;">
                <p style="font-size:10px; font-weight:700; color:#92400e; margin-bottom:4px;">{{ __('common.fields.notes') }}</p>
                <p style="font-size:13px; color:#78350f; line-height:1.7;">{{ $contract->notes }}</p>
            </div>
            @endif

            {{-- ── Signature section ───────────────────────────────────────── --}}
            <div style="margin-top:30px; display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px; border-top:1px dashed #e2e8f0; padding-top:20px;">
                @foreach(['{{ __('common.fields.client') }}', '{{ __('contracts.print.office') }}', '{{ __('contracts.print.witness') }}'] as $party)
                <div style="text-align:center;">
                    <p style="font-size:11px; color:#64748b; font-weight:600; margin-bottom:40px;">{{ $party }}</p>
                    <div style="border-bottom:1px solid #94a3b8; margin-bottom:4px;"></div>
                    <p style="font-size:10px; color:#94a3b8;">{{ __('contracts.print.signature') }}</p>
                </div>
                @endforeach
            </div>

        </div>

        {{-- ── Footer ──────────────────────────────────────────────────────── --}}
        <div style="background:#f8fafc; border-top:1px solid #e2e8f0; padding:10px 36px; display:flex; justify-content:space-between; align-items:center;">
            <p style="font-size:10px; color:#94a3b8;">{{ __('common.fields.musaned_number') }}: {{ $contract->musaned_number ?? '—' }}</p>
            <p style="font-size:10px; color:#94a3b8;">{{ __('contracts.print.printed_at') }} {{ now()->format('Y/m/d H:i') }}</p>
            <p style="font-size:10px; color:#94a3b8;">{{ $contract->contract_number }}</p>
        </div>

    </div>

    <p class="no-print text-center text-xs text-slate-400 mt-6">{{ __('common.system') }} &copy; {{ date('Y') }}</p>

</body>
</html>

