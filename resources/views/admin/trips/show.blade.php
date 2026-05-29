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
        <form method="POST" action="{{ route('admin.trips.complete', $trip->id) }}"
              onsubmit="return confirm('تأكيد اكتمال الرحلة؟')">
            @csrf @method('PATCH')
            <button type="submit"
                    style="padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;color:#fff;background:#16a34a;border:none;font-family:'Cairo',sans-serif;cursor:pointer;display:flex;align-items:center;gap:6px;"
                    onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                تأكيد الاكتمال
            </button>
        </form>
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

{{-- Add worker section --}}
@if($trip->status === 'scheduled')
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-6">
    <div style="background:linear-gradient(135deg,#0f172a 0%,#1a2744 100%);padding:14px 20px;display:flex;align-items:center;gap:10px;">
        <div style="width:32px;height:32px;border-radius:8px;background:rgba(201,168,76,.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="16" height="16" fill="none" stroke="#c9a84c" stroke-width="2" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
        </div>
        <span style="color:#e2e8f0;font-size:14px;font-weight:700;font-family:'Cairo',sans-serif;">إضافة عاملة للرحلة</span>
    </div>
    <div class="p-5">
        <form method="POST" action="{{ route('admin.trips.add-worker', $trip->id) }}" class="flex flex-wrap gap-3 items-end">
            @csrf
            <div class="flex-1" style="min-width:200px">
                <label style="font-size:11px;color:#94a3b8;font-weight:600;display:block;margin-bottom:5px;">اختر عاملة</label>
                <select name="worker_id" required class="form-input-sm" style="padding:0">
                    <option value="">-- اختر --</option>
                    @foreach($workers as $w)
                    <option value="{{ $w->id }}">{{ $w->name }}{{ $w->nationality ? ' — '.$w->nationality->name : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1" style="min-width:200px">
                <label style="font-size:11px;color:#94a3b8;font-weight:600;display:block;margin-bottom:5px;">ملاحظات (اختياري)</label>
                <input type="text" name="notes" placeholder="اختياري" class="form-input-sm">
            </div>
            <button type="submit"
                    style="padding:9px 20px;border-radius:8px;font-size:13px;font-weight:700;color:#fff;background:linear-gradient(135deg,#c9a84c,#a88830);border:none;font-family:'Cairo',sans-serif;cursor:pointer;display:flex;align-items:center;gap:6px;white-space:nowrap;"
                    onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                إضافة
            </button>
        </form>
    </div>
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
                <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;letter-spacing:.04em;">الجنسية</th>
                <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;letter-spacing:.04em;">ملاحظات</th>
                <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;letter-spacing:.04em;">إجراءات</th>
            </tr>
        </thead>
        <tbody>
            @forelse($trip->workers as $i => $worker)
            <tr class="worker-row" style="border-bottom:1px solid #f1f5f9;">
                <td style="padding:12px 16px;color:#94a3b8;font-size:12px;">{{ $i + 1 }}</td>
                <td style="padding:12px 16px;font-weight:700;color:#0f172a;">{{ $worker->name }}</td>
                <td style="padding:12px 16px;">
                    @if($worker->nationality)
                    <span style="background:#f1f5f9;color:#475569;font-size:11px;padding:3px 9px;border-radius:6px;font-weight:600;">{{ $worker->nationality->name }}</span>
                    @else
                    <span style="color:#cbd5e1;">—</span>
                    @endif
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
                <td colspan="5" style="padding:48px 16px;text-align:center;color:#94a3b8;font-size:13px;">
                    <div style="margin-bottom:8px;font-size:32px;">👥</div>
                    لم تتم إضافة عاملات بعد
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
