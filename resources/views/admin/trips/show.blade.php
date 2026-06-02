@extends('admin.layouts.app')
@section('title', 'تفاصيل الرحلة')

@push('styles')
<style>
.stat-chip {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 12px; border-radius: 20px;
    font-size: 12px; font-weight: 700; font-family: 'Cairo', sans-serif;
}
.worker-row:hover { background: #f8fafc; }
.form-input-sm {
    border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 8px 12px;
    font-size: 13px; font-family: 'Cairo', sans-serif; color: #0f172a;
    outline: none; transition: border-color .15s, box-shadow .15s; background: #fff; width: 100%;
}
.form-input-sm:focus { border-color: #c9a84c; box-shadow: 0 0 0 3px rgba(201,168,76,.12); }
</style>
@endpush

@section('content')

{{-- Top bar --}}
<div class="flex flex-wrap justify-between items-start gap-3 mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.trips.index') }}"
           class="flex items-center gap-1.5 text-slate-500 hover:text-slate-800 text-sm transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            عودة إلى الرحلات
        </a>
        <span class="text-slate-300">|</span>
        <h2 class="text-lg font-bold text-slate-800">رحلة: <span style="color:#c9a84c">{{ $trip->trip_number }}</span></h2>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.trips.print', $trip->id) }}" target="_blank"
           style="padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;color:#475569;border:1.5px solid #e2e8f0;background:#fff;text-decoration:none;display:flex;align-items:center;gap:6px;font-family:'Cairo',sans-serif;"
           onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            طباعة
        </a>
        @can('trips.edit')
        <a href="{{ route('admin.trips.edit', $trip->id) }}"
           style="padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;color:#fff;background:#f59e0b;text-decoration:none;display:flex;align-items:center;gap:6px;font-family:'Cairo',sans-serif;"
           onmouseover="this.style.background='#d97706'" onmouseout="this.style.background='#f59e0b'">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            تعديل
        </a>
        @endcan
        @if($trip->status === 'scheduled')
        <a href="{{ route('admin.trips.checklist', $trip->id) }}"
           style="padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;color:#fff;background:#16a34a;text-decoration:none;display:flex;align-items:center;gap:6px;font-family:'Cairo',sans-serif;"
           onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            تأكيد الاكتمال
        </a>
        @endif
    </div>
</div>

@if(session('success'))
<div style="margin-bottom:16px;background:#f0fdf4;border:1px solid #bbf7d0;color:#16a34a;border-radius:10px;padding:12px 16px;font-size:13px;display:flex;align-items:center;gap:8px;">
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
    {{ session('success') }}
</div>
@endif

{{-- Info cards --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    {{-- Type --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
        <div style="width:44px;height:44px;border-radius:11px;background:{{ $trip->type_color }}22;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            @php
                $typeIcons = ['arrival'=>'✈️','departure'=>'🛫','group_transport'=>'🚌','deportation'=>'📋'];
            @endphp
            <span style="font-size:20px;">{{ $typeIcons[$trip->trip_type] ?? '✈️' }}</span>
        </div>
        <div>
            <div style="font-size:11px;color:#94a3b8;font-weight:600;margin-bottom:4px;">النوع</div>
            <span class="stat-chip" style="background:{{ $trip->type_color }}22;color:{{ $trip->type_color }}">
                {{ $trip->type_label }}
            </span>
        </div>
    </div>

    {{-- Date/time --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
        <div style="width:44px;height:44px;border-radius:11px;background:#fdf8e8;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="20" height="20" fill="none" stroke="#c9a84c" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div>
            <div style="font-size:11px;color:#94a3b8;font-weight:600;margin-bottom:4px;">التاريخ والوقت</div>
            <div style="font-size:14px;font-weight:700;color:#0f172a;">{{ \Carbon\Carbon::parse($trip->trip_date)->format('Y/m/d') }}</div>
            @if($trip->trip_time)<div style="font-size:12px;color:#64748b;margin-top:2px;">{{ $trip->trip_time }}</div>@endif
        </div>
    </div>

    {{-- Airport --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
        <div style="width:44px;height:44px;border-radius:11px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="20" height="20" fill="none" stroke="#2563eb" stroke-width="1.8" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 017.03 15.5a19.79 19.79 0 01-3.07-8.67A2 2 0 015.93 4h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11l-1.27 1.27a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
        </div>
        <div>
            <div style="font-size:11px;color:#94a3b8;font-weight:600;margin-bottom:4px;">المطار / رقم الرحلة</div>
            <div style="font-size:14px;font-weight:700;color:#0f172a;">{{ $trip->airport?->name ?? '—' }}</div>
            @if($trip->flight_number)<div style="font-size:12px;color:#64748b;margin-top:2px;">{{ $trip->flight_number }}</div>@endif
        </div>
    </div>
</div>

{{-- Contracts selection table (add from recruitment contracts) --}}
@if($trip->status === 'scheduled')
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-6">
    <div style="background:linear-gradient(135deg,#0f172a 0%,#1a2744 100%);padding:14px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:32px;height:32px;border-radius:8px;background:rgba(201,168,76,.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="16" height="16" fill="none" stroke="#c9a84c" stroke-width="2" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
            </div>
            <div>
                <span style="color:#e2e8f0;font-size:14px;font-weight:700;font-family:'Cairo',sans-serif;">إضافة من عقود الاستقدام</span>
                @if($trip->originNationality)
                <span style="margin-right:8px;background:rgba(201,168,76,.2);color:#c9a84c;font-size:11px;padding:2px 8px;border-radius:12px;font-weight:600;">
                    🌍 {{ $trip->originNationality->name }}
                </span>
                @endif
            </div>
        </div>
        <span style="color:#64748b;font-size:12px;font-family:'Cairo',sans-serif;">
            عقود الاستقدام المرتبطة بهذا الفرع
        </span>
    </div>

    @if($contracts->isNotEmpty() || request('contract_search'))
    {{-- Search bar (outside bulk form) --}}
    <div style="padding:12px 16px;background:#fff;border-bottom:1px solid #f1f5f9;">
        <form method="GET" action="{{ route('admin.trips.show', $trip->id) }}" style="display:flex;gap:8px;align-items:center;">
                <input type="text" name="contract_search" value="{{ request('contract_search') }}"
                       placeholder="ابحث باسم العميل أو العاملة أو رقم العقد..."
                       style="flex:1;border:1.5px solid #e2e8f0;border-radius:8px;padding:8px 12px;font-size:13px;font-family:'Cairo',sans-serif;outline:none;"
                       onfocus="this.style.borderColor='#c9a84c'" onblur="this.style.borderColor='#e2e8f0'">
                <button type="submit"
                        style="padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;color:#fff;background:#64748b;border:none;font-family:'Cairo',sans-serif;cursor:pointer;display:flex;align-items:center;gap:5px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    بحث
                </button>
                @if(request('contract_search'))
                <a href="{{ route('admin.trips.show', $trip->id) }}"
                   style="padding:8px 12px;border-radius:8px;font-size:13px;color:#94a3b8;border:1.5px solid #e2e8f0;text-decoration:none;font-family:'Cairo',sans-serif;">مسح</a>
                @endif
            </form>
        </div>
    <form method="POST" action="{{ route('admin.trips.add-workers-bulk', $trip->id) }}" id="bulk-form">
        @csrf
        <div style="padding:12px 16px;background:#f8fafc;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
            <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px;font-weight:600;color:#475569;font-family:'Cairo',sans-serif;">
                <input type="checkbox" id="select-all" style="width:16px;height:16px;accent-color:#c9a84c;cursor:pointer;">
                تحديد الكل
            </label>
            <button type="submit" id="bulk-submit"
                    style="padding:8px 18px;border-radius:8px;font-size:13px;font-weight:700;color:#fff;background:linear-gradient(135deg,#c9a84c,#a88830);border:none;font-family:'Cairo',sans-serif;cursor:pointer;display:flex;align-items:center;gap:6px;"
                    onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                إضافة المحددات للرحلة
            </button>
        </div>
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr style="background:#f8fafc;border-bottom:1px solid #f1f5f9;">
                    <th style="padding:10px 12px;width:36px;"></th>
                    <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;">#</th>
                    <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;">العميل</th>
                    <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;">العاملة</th>
                    <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;">رقم الجواز</th>
                    <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;">الجنسية / البلد</th>
                    <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;">رقم العقد</th>
                    <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;">الفرع</th>
                    <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;">المرحلة</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contracts as $i => $contract)
                <tr class="worker-row" style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:10px 12px;text-align:center;">
                        <input type="checkbox" name="contract_ids[]" value="{{ $contract->id }}"
                               class="contract-checkbox" style="width:16px;height:16px;accent-color:#c9a84c;cursor:pointer;">
                    </td>
                    <td style="padding:10px 16px;color:#94a3b8;font-size:12px;">{{ $i + 1 }}</td>
                    <td style="padding:10px 16px;">
                        <div style="font-weight:700;color:#0f172a;font-size:13px;">{{ $contract->client?->name ?? '—' }}</div>
                        @if($contract->client?->phone)
                        <div style="font-size:11px;color:#94a3b8;margin-top:2px;">{{ $contract->client->phone }}</div>
                        @endif
                    </td>
                    <td style="padding:10px 16px;">
                        <div style="font-weight:600;color:#1e293b;font-size:13px;">{{ $contract->worker?->name ?? '—' }}</div>
                    </td>
                    <td style="padding:10px 16px;color:#475569;font-size:12px;font-family:monospace;">
                        {{ $contract->worker?->passport_number ?? '—' }}
                    </td>
                    <td style="padding:10px 16px;">
                        @if($contract->originNationality)
                        <span style="background:#ede9fe;color:#7c3aed;font-size:11px;padding:3px 9px;border-radius:6px;font-weight:600;">
                            🌍 {{ $contract->originNationality->name }}
                        </span>
                        @elseif($contract->worker?->nationality)
                        <span style="background:#f1f5f9;color:#475569;font-size:11px;padding:3px 9px;border-radius:6px;font-weight:600;">
                            {{ $contract->worker->nationality->name }}
                        </span>
                        @else
                        <span style="color:#cbd5e1;">—</span>
                        @endif
                    </td>
                    <td style="padding:10px 16px;color:#64748b;font-size:12px;font-family:monospace;">
                        {{ $contract->contract_number }}
                    </td>
                    <td style="padding:10px 16px;">
                        @if($contract->branch)
                        <span style="background:#f0fdf4;color:#16a34a;font-size:11px;padding:3px 9px;border-radius:6px;font-weight:600;">
                            {{ $contract->branch->name }}
                        </span>
                        @else
                        <span style="color:#cbd5e1;">—</span>
                        @endif
                    </td>
                    <td style="padding:10px 16px;">
                        @php $statusLabel = \App\Models\RecruitmentContract::statuses()[$contract->current_status]['label'] ?? "مرحلة {$contract->current_status}"; @endphp
                        <span style="background:#f1f5f9;color:#64748b;font-size:11px;padding:3px 9px;border-radius:6px;font-weight:600;">
                            {{ $statusLabel }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </form>
    @else
    <div style="padding:40px 16px;text-align:center;color:#94a3b8;font-size:13px;font-family:'Cairo',sans-serif;">
        <div style="font-size:32px;margin-bottom:10px;">📋</div>
        لا توجد عقود متاحة
        @if(request('contract_search'))
        <br><a href="{{ route('admin.trips.show', $trip->id) }}" style="font-size:12px;color:#c9a84c;">مسح البحث</a>
        @endif
    </div>
    @endif
</div>
@endif

{{-- Workers table --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div style="padding:14px 20px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;">
        <div style="display:flex;align-items:center;gap:8px;">
            <svg width="16" height="16" fill="none" stroke="#c9a84c" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87m-4-12a4 4 0 010 7.75"/></svg>
            <span style="font-size:14px;font-weight:700;color:#0f172a;font-family:'Cairo',sans-serif;">العاملات في الرحلة</span>
            <span style="background:#fdf8e8;color:#c9a84c;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;border:1px solid #f0e0a4;">{{ $trip->workers->count() }}</span>
        </div>
    </div>
    <table class="w-full text-sm">
        <thead>
            <tr style="background:#f8fafc;border-bottom:1px solid #f1f5f9;">
                <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;letter-spacing:.04em;">#</th>
                <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;letter-spacing:.04em;">الاسم</th>
                <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;letter-spacing:.04em;">العميل</th>
                <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;letter-spacing:.04em;">الجنسية</th>
                <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;letter-spacing:.04em;">رقم الجواز</th>
                <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;letter-spacing:.04em;">ملاحظات</th>
                <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;letter-spacing:.04em;">إجراءات</th>
            </tr>
        </thead>
        <tbody>
            @forelse($trip->workers as $i => $worker)
            <tr class="worker-row" style="border-bottom:1px solid #f1f5f9;">
                <td style="padding:12px 16px;color:#94a3b8;font-size:12px;">{{ $i + 1 }}</td>
                <td style="padding:12px 16px;font-weight:700;color:#0f172a;">{{ $worker->name }}</td>
                <td style="padding:12px 16px;color:#475569;font-size:12px;">
                    @php
                        $wContract = $trip->workers()->where('worker_id', $worker->id)->first()?->pivot?->contract_id
                            ? \App\Models\RecruitmentContract::with('client')->find($worker->pivot->contract_id)
                            : null;
                    @endphp
                    {{ $wContract?->client?->name ?? '—' }}
                </td>
                <td style="padding:12px 16px;">
                    @if($worker->nationality)
                    <span style="background:#f1f5f9;color:#475569;font-size:11px;padding:3px 9px;border-radius:6px;font-weight:600;">{{ $worker->nationality->name }}</span>
                    @else
                    <span style="color:#cbd5e1;">—</span>
                    @endif
                </td>
                <td style="padding:12px 16px;color:#64748b;font-size:12px;font-family:monospace;letter-spacing:.03em;">
                    {{ $worker->passport_number ?: '—' }}
                </td>
                <td style="padding:12px 16px;color:#94a3b8;font-size:12px;">{{ $worker->pivot->notes ?: '—' }}</td>
                <td style="padding:12px 16px;">
                    @if($trip->status === 'scheduled')
                    <form method="POST" action="{{ route('admin.trips.remove-worker', [$trip->id, $worker->id]) }}"
                          onsubmit="return confirm('إزالة هذه العاملة؟')">
                        @csrf @method('DELETE')
                        <button type="submit" style="color:#ef4444;font-size:12px;font-weight:600;background:none;border:none;cursor:pointer;font-family:'Cairo',sans-serif;padding:4px 8px;border-radius:6px;transition:background .15s;"
                                onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='none'">إزالة</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="padding:48px 16px;text-align:center;color:#94a3b8;font-size:13px;">
                    <div style="margin-bottom:8px;font-size:32px;">👥</div>
                    لم تتم إضافة عاملات بعد
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.contract-checkbox');

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
        });
        checkboxes.forEach(cb => cb.addEventListener('change', function () {
            selectAll.checked = [...checkboxes].every(c => c.checked);
        }));
    }

    const bulkForm = document.getElementById('bulk-form');
    if (bulkForm) {
        bulkForm.addEventListener('submit', function (e) {
            const selected = document.querySelectorAll('.contract-checkbox:checked');
            if (selected.length === 0) {
                e.preventDefault();
                alert('يرجى تحديد عاملة واحدة على الأقل');
            }
        });
    }
});
</script>
@endpush
@endsection
