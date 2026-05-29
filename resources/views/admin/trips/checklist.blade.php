@extends('admin.layouts.app')
@section('title', 'قائمة مراجعة الرحلة')

@push('styles')
<style>
.form-label { font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
.section-header {
    font-size: 12px; font-weight: 700; color: #94a3b8; letter-spacing: .06em;
    margin-bottom: 14px; padding-bottom: 8px; border-bottom: 1px solid #f1f5f9;
    display: flex; align-items: center; gap: 6px;
}
.status-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 16px; border-radius: 8px; font-size: 13px;
    font-weight: 700; font-family: 'Cairo', sans-serif;
    cursor: pointer; border: 2px solid transparent; transition: all .15s;
    outline: none;
}
.status-btn-arrived { background: #f0fdf4; color: #15803d; border-color: #bbf7d0; }
.status-btn-arrived.active, .status-btn-arrived:hover { background: #16a34a; color: #fff; border-color: #16a34a; }
.status-btn-absent  { background: #fef2f2; color: #b91c1c; border-color: #fecaca; }
.status-btn-absent.active,  .status-btn-absent:hover  { background: #dc2626; color: #fff; border-color: #dc2626; }
.worker-row { transition: background .15s; }
.worker-row:hover { background: #f8fafc; }
[x-cloak] { display: none !important; }
</style>
@endpush

@section('content')

<div x-data="{
    statuses: {{ json_encode($trip->workers->mapWithKeys(fn($w) => [$w->id => 'completed'])->toArray()) }},
    get hasAbsent()  { return Object.values(this.statuses).some(s => s === 'no_show'); },
    get arrivedCount(){ return Object.values(this.statuses).filter(s => s === 'completed').length; },
    get absentCount() { return Object.values(this.statuses).filter(s => s === 'no_show').length; }
}">

{{-- Breadcrumb --}}
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('admin.trips.show', $trip->id) }}"
       class="flex items-center gap-1.5 text-slate-500 hover:text-slate-800 text-sm transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        عودة إلى الرحلة
    </a>
    <span class="text-slate-300">|</span>
    <h2 class="text-lg font-bold text-slate-800">قائمة مراجعة اكتمال الرحلة</h2>
</div>

<form method="POST" action="{{ route('admin.trips.checklist.submit', $trip->id) }}"
      onsubmit="return confirm('تأكيد إكمال الرحلة بهذه البيانات؟')">
@csrf

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">

    {{-- Dark header --}}
    <div style="background:linear-gradient(135deg,#0f172a 0%,#1a2744 100%);padding:20px 24px;display:flex;align-items:center;gap:14px;">
        <div style="width:42px;height:42px;border-radius:10px;background:rgba(201,168,76,.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="22" height="22" fill="none" stroke="#c9a84c" stroke-width="1.8" viewBox="0 0 24 24">
                <path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/>
                <polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/>
            </svg>
        </div>
        <div>
            <div style="color:#c9a84c;font-size:11px;font-weight:600;letter-spacing:.05em;margin-bottom:2px;">تأكيد اكتمال</div>
            <div style="color:#e2e8f0;font-size:15px;font-weight:700;">رحلة: {{ $trip->trip_number }}</div>
        </div>
        <div style="margin-right:auto;display:flex;gap:12px;flex-wrap:wrap;">
            <div style="background:rgba(201,168,76,.15);border:1px solid rgba(201,168,76,.3);border-radius:8px;padding:6px 14px;text-align:center;">
                <div style="color:#c9a84c;font-size:10px;font-weight:600;margin-bottom:1px;">نوع الرحلة</div>
                <div style="color:#e2e8f0;font-size:12px;font-weight:700;">
                    @switch($trip->trip_type)
                        @case('arrival') وصول @break
                        @case('departure') مغادرة @break
                        @case('group_transport') نقل جماعي @break
                        @case('deportation') ترحيل @break
                        @default {{ $trip->trip_type }}
                    @endswitch
                </div>
            </div>
            <div style="background:rgba(201,168,76,.15);border:1px solid rgba(201,168,76,.3);border-radius:8px;padding:6px 14px;text-align:center;">
                <div style="color:#c9a84c;font-size:10px;font-weight:600;margin-bottom:1px;">التاريخ</div>
                <div style="color:#e2e8f0;font-size:12px;font-weight:700;">{{ $trip->trip_date }}</div>
            </div>
            <div style="background:rgba(201,168,76,.15);border:1px solid rgba(201,168,76,.3);border-radius:8px;padding:6px 14px;text-align:center;">
                <div style="color:#c9a84c;font-size:10px;font-weight:600;margin-bottom:1px;">عدد العاملات</div>
                <div style="color:#e2e8f0;font-size:12px;font-weight:700;">{{ $trip->workers->count() }}</div>
            </div>
        </div>
    </div>

    <div class="p-6 space-y-7">

        {{-- Alert banner --}}
        <div x-show="hasAbsent" x-cloak
             style="background:#fef2f2;border:1.5px solid #fca5a5;border-radius:10px;padding:12px 16px;display:flex;align-items:center;gap:10px;">
            <svg width="18" height="18" fill="none" stroke="#dc2626" stroke-width="2" viewBox="0 0 24 24">
                <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
            <span style="font-family:'Cairo',sans-serif;font-size:13px;font-weight:700;color:#b91c1c;">
                تنبيه: <span x-text="absentCount"></span> عاملة لم تظهر — سيُرسَل إشعار تحذيري أحمر للمشرفين عند التأكيد.
            </span>
        </div>

        {{-- Section: حالة العاملات --}}
        <div>
            <div class="section-header">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="flex-shrink-0">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>
                </svg>
                حالة العاملات في الرحلة
                <button type="button"
                        @click="Object.keys(statuses).forEach(k => statuses[k] = 'completed')"
                        style="margin-right:auto;padding:4px 12px;border-radius:6px;font-size:11px;font-weight:700;color:#15803d;background:#f0fdf4;border:1.5px solid #bbf7d0;cursor:pointer;font-family:'Cairo',sans-serif;">
                    تحديد الكل كـ "وصل"
                </button>
            </div>

            @if($trip->workers->isEmpty())
            <div style="text-align:center;padding:40px;color:#94a3b8;font-family:'Cairo',sans-serif;font-size:14px;">
                لا توجد عاملات مضافة لهذه الرحلة
            </div>
            @else
            <div style="border:1.5px solid #f1f5f9;border-radius:10px;overflow:hidden;">
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f8fafc;">
                            <th style="padding:9px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;font-family:'Cairo',sans-serif;">#</th>
                            <th style="padding:9px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;font-family:'Cairo',sans-serif;">العاملة</th>
                            <th style="padding:9px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;font-family:'Cairo',sans-serif;">الجنسية</th>
                            <th style="padding:9px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;font-family:'Cairo',sans-serif;">ملاحظات</th>
                            <th style="padding:9px 16px;text-align:center;font-size:11px;font-weight:700;color:#94a3b8;font-family:'Cairo',sans-serif;">الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($trip->workers as $i => $worker)
                    <tr class="worker-row" style="border-top:1px solid #f1f5f9;">
                        <td style="padding:13px 16px;font-size:12px;color:#94a3b8;font-family:'Cairo',sans-serif;font-weight:600;">{{ $i + 1 }}</td>
                        <td style="padding:13px 16px;">
                            <div style="font-size:14px;font-weight:700;color:#0f172a;font-family:'Cairo',sans-serif;">{{ $worker->name }}</div>
                            @if($worker->file_number)
                            <div style="font-size:11px;color:#94a3b8;font-family:'Cairo',sans-serif;margin-top:1px;">{{ $worker->file_number }}</div>
                            @endif
                        </td>
                        <td style="padding:13px 16px;font-size:13px;color:#475569;font-family:'Cairo',sans-serif;">
                            {{ $worker->nationality?->name ?? '—' }}
                        </td>
                        <td style="padding:13px 16px;font-size:13px;color:#64748b;font-family:'Cairo',sans-serif;">
                            {{ $worker->pivot->notes ?? '—' }}
                        </td>
                        <td style="padding:13px 16px;text-align:center;">
                            <input type="hidden" name="statuses[{{ $worker->id }}]" :value="statuses[{{ $worker->id }}]">
                            <div style="display:inline-flex;gap:8px;">
                                <button type="button"
                                        class="status-btn status-btn-arrived"
                                        :class="{ 'active': statuses[{{ $worker->id }}] === 'completed' }"
                                        @click="statuses[{{ $worker->id }}] = 'completed'">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                                    وصل
                                </button>
                                <button type="button"
                                        class="status-btn status-btn-absent"
                                        :class="{ 'active': statuses[{{ $worker->id }}] === 'no_show' }"
                                        @click="statuses[{{ $worker->id }}] = 'no_show'">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                    لم يظهر
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Summary --}}
            <div style="margin-top:10px;display:flex;gap:16px;font-family:'Cairo',sans-serif;font-size:13px;color:#64748b;">
                <span>الإجمالي: <strong style="color:#0f172a;">{{ $trip->workers->count() }}</strong></span>
                <span style="color:#16a34a;">وصل: <strong x-text="arrivedCount"></strong></span>
                <span style="color:#dc2626;">لم يظهر: <strong x-text="absentCount"></strong></span>
            </div>
            @endif
        </div>

    </div>{{-- end p-6 --}}

    {{-- Footer --}}
    <div style="border-top:1px solid #f1f5f9;padding:16px 24px;display:flex;gap:10px;align-items:center;">
        <button type="submit"
                style="background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;border:none;padding:10px 28px;border-radius:10px;font-size:14px;font-weight:700;font-family:'Cairo',sans-serif;cursor:pointer;display:flex;align-items:center;gap:8px;transition:opacity .2s;"
                onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
            تأكيد اكتمال الرحلة
        </button>
        <a href="{{ route('admin.trips.show', $trip->id) }}"
           style="padding:10px 20px;border-radius:10px;font-size:13px;font-weight:600;color:#64748b;border:1.5px solid #e2e8f0;text-decoration:none;background:#fff;font-family:'Cairo',sans-serif;"
           onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'">
            إلغاء ✕
        </a>
    </div>

</div>{{-- end card --}}
</form>
</div>{{-- end x-data --}}

@endsection