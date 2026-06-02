@extends('admin.layouts.app')
@section('title', 'تعديل رحلة')

@push('styles')
<style>
.trip-type-card {
    position: relative; cursor: pointer;
    border: 2px solid #e2e8f0; border-radius: 12px;
    padding: 14px 12px; text-align: center;
    transition: all .2s ease; background: #fff; user-select: none;
}
.trip-type-card:hover { border-color: #c9a84c; background: #fdf8e8; }
.trip-type-card.selected {
    border-color: #c9a84c; background: #fdf8e8;
    box-shadow: 0 0 0 3px rgba(201,168,76,.18);
}
.trip-type-card .tc-check {
    position: absolute; top: 8px; left: 8px;
    width: 18px; height: 18px; border-radius: 50%;
    background: #c9a84c; display: none; align-items: center; justify-content: center;
}
.trip-type-card.selected .tc-check { display: flex; }
.trip-type-card .tc-icon {
    width: 40px; height: 40px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 8px; font-size: 20px;
}
.trip-type-card .tc-label { font-size: 12px; font-weight: 700; color: #334155; }
.form-label { font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
.form-label .req { color: #ef4444; }
.form-input {
    width: 100%; border: 1.5px solid #e2e8f0; border-radius: 8px;
    padding: 9px 12px; font-size: 13px; font-family: 'Cairo', sans-serif;
    color: #0f172a; outline: none; transition: border-color .15s, box-shadow .15s; background: #fff;
}
.form-input:focus { border-color: #c9a84c; box-shadow: 0 0 0 3px rgba(201,168,76,.12); }
select.form-input { padding-top: 9px; padding-bottom: 9px; }
.section-header {
    font-size: 11px; font-weight: 700; color: #94a3b8; letter-spacing: .07em;
    margin-bottom: 16px; padding-bottom: 10px; border-bottom: 1px solid #f1f5f9;
    display: flex; align-items: center; gap: 8px;
}
</style>
@endpush

@section('content')

<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('admin.trips.show', $trip->id) }}"
       class="flex items-center gap-1.5 text-slate-500 hover:text-slate-800 text-sm transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        عودة إلى تفاصيل الرحلة
    </a>
    <span class="text-slate-300">|</span>
    <h2 class="text-lg font-bold text-slate-800">تعديل رحلة — <span style="color:#c9a84c">{{ $trip->trip_number }}</span></h2>
</div>

@if($errors->any())
<div style="margin-bottom:16px;background:#fef2f2;border:1px solid #fecaca;color:#dc2626;border-radius:10px;padding:12px 16px;font-size:13px;">
    <ul class="list-disc list-inside space-y-1">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('admin.trips.update', $trip->id) }}">
    @csrf @method('PUT')

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">

        {{-- Header --}}
        <div style="background:linear-gradient(135deg,#0f172a 0%,#1a2744 100%);padding:20px 24px;display:flex;align-items:center;gap:14px;">
            <div style="width:42px;height:42px;border-radius:10px;background:rgba(201,168,76,.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="20" height="20" fill="none" stroke="#c9a84c" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
            </div>
            <div>
                <div style="color:#c9a84c;font-size:11px;font-weight:600;letter-spacing:.05em;margin-bottom:2px;">تعديل رحلة</div>
                <div style="color:#e2e8f0;font-size:15px;font-weight:700;">{{ $trip->trip_number }}</div>
            </div>
        </div>

        <div class="p-6 space-y-8">

            {{-- 1. نوع الرحلة --}}
            <div>
                <div class="section-header">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    نوع الرحلة <span class="text-red-400">*</span>
                </div>
                @php
                    $types = [
                        'arrival'         => ['label'=>'استلام وصول','icon'=>'✈️', 'color'=>'#16a34a','bg'=>'#dcfce7'],
                        'departure'       => ['label'=>'مغادرة',      'icon'=>'🛫','color'=>'#2563eb','bg'=>'#dbeafe'],
                        'group_transport' => ['label'=>'نقل جماعي',   'icon'=>'🚌', 'color'=>'#c9a84c','bg'=>'#fef9c3'],
                        'deportation'     => ['label'=>'تسفير',        'icon'=>'📋', 'color'=>'#dc2626','bg'=>'#fee2e2'],
                    ];
                    $currentType = old('trip_type', $trip->trip_type);
                @endphp
                <input type="hidden" name="trip_type" id="trip_type_val" value="{{ $currentType }}">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach($types as $val => $meta)
                    <div class="trip-type-card {{ $currentType == $val ? 'selected' : '' }}"
                         data-value="{{ $val }}" onclick="selectTripType('{{ $val }}')">
                        <div class="tc-check">
                            <svg width="10" height="10" fill="none" stroke="#fff" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <div class="tc-icon" style="background:{{ $meta['bg'] }}">{{ $meta['icon'] }}</div>
                        <div class="tc-label" style="{{ $currentType == $val ? 'color:'.$meta['color'] : '' }}">{{ $meta['label'] }}</div>
                    </div>
                    @endforeach
                </div>
                @error('trip_type')<p class="text-red-500 text-xs mt-2">{{ $message }}</p>@enderror
            </div>

            {{-- 2. تفاصيل الرحلة --}}
            <div>
                <div class="section-header">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    تفاصيل الرحلة
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

                    <div>
                        <label class="form-label">
                            <svg width="13" height="13" fill="none" stroke="#c9a84c" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            تاريخ الرحلة <span class="req">*</span>
                        </label>
                        <input type="date" name="trip_date"
                               value="{{ old('trip_date', $trip->trip_date?->format('Y-m-d')) }}"
                               required class="form-input">
                        @error('trip_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="form-label">
                            <svg width="13" height="13" fill="none" stroke="#c9a84c" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            وقت الرحلة
                        </label>
                        @php $tripTime = $trip->trip_time ? \Illuminate\Support\Str::substr($trip->trip_time, 0, 5) : ''; @endphp
                        <input type="time" name="trip_time"
                               value="{{ old('trip_time', $tripTime) }}"
                               class="form-input">
                    </div>

                    <div>
                        <label class="form-label">
                            <svg width="13" height="13" fill="none" stroke="#c9a84c" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 017.03 15.5a19.79 19.79 0 01-3.07-8.67A2 2 0 015.93 4h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11l-1.27 1.27a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                            المطار
                        </label>
                        <select name="airport_id" class="form-input">
                            <option value="">بدون مطار</option>
                            @foreach($airports as $ap)
                            <option value="{{ $ap->id }}" {{ old('airport_id', $trip->airport_id) == $ap->id ? 'selected' : '' }}>{{ $ap->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">
                            <svg width="13" height="13" fill="none" stroke="#c9a84c" stroke-width="2" viewBox="0 0 24 24"><path d="M17.8 19.2L16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"/></svg>
                            رقم الرحلة الجوية
                        </label>
                        <input type="text" name="flight_number"
                               value="{{ old('flight_number', $trip->flight_number) }}"
                               placeholder="مثال: SV205" class="form-input">
                    </div>

                    <div>
                        <label class="form-label">
                            🌍 بلد المنشأ <span style="color:#94a3b8;font-weight:400;font-size:12px;">(اختياري)</span>
                        </label>
                        <select name="origin_nationality_id" class="form-input">
                            <option value="">-- كل الجنسيات --</option>
                            @foreach($nationalities ?? [] as $nat)
                            <option value="{{ $nat->id }}" {{ old('origin_nationality_id', $trip->origin_nationality_id) == $nat->id ? 'selected' : '' }}>{{ $nat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">
                            <svg width="13" height="13" fill="none" stroke="#c9a84c" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                            الفرع <span class="req">*</span>
                        </label>
                        <select name="branch_id" required class="form-input">
                            @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ old('branch_id', $trip->branch_id) == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                            @endforeach
                        </select>
                        @error('branch_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                </div>
            </div>

            {{-- 3. ملاحظات --}}
            <div>
                <div class="section-header">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    ملاحظات
                </div>
                <textarea name="notes" rows="3" placeholder="ملاحظات اختيارية..."
                          class="form-input" style="resize:vertical;">{{ old('notes', $trip->notes) }}</textarea>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
                <button type="submit"
                        style="padding:10px 28px;border-radius:10px;font-size:14px;font-weight:700;color:#fff;background:linear-gradient(135deg,#c9a84c,#a88830);border:none;font-family:'Cairo',sans-serif;cursor:pointer;display:flex;align-items:center;gap:8px;"
                        onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                    حفظ التعديلات
                </button>
                <a href="{{ route('admin.trips.show', $trip->id) }}"
                   style="padding:10px 22px;border-radius:10px;font-size:14px;font-weight:600;color:#64748b;border:1.5px solid #e2e8f0;text-decoration:none;font-family:'Cairo',sans-serif;background:#fff;"
                   onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'">
                    إلغاء
                </a>
            </div>

        </div>
    </div>
</form>

@push('scripts')
<script>
function selectTripType(val) {
    document.getElementById('trip_type_val').value = val;
    document.querySelectorAll('.trip-type-card').forEach(function(card) {
        card.classList.toggle('selected', card.dataset.value === val);
    });
}
</script>
@endpush
@endsection
