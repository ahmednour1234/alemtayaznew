@extends('admin.layouts.app')
@section('title', 'رحلة جديدة')

@push('styles')
<style>
.trip-type-card {
    position: relative;
    cursor: pointer;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 14px 12px;
    text-align: center;
    transition: all .2s ease;
    background: #fff;
    user-select: none;
}
.trip-type-card:hover { border-color: #c9a84c; background: #fdf8e8; }
.trip-type-card.selected {
    border-color: #c9a84c;
    background: #fdf8e8;
    box-shadow: 0 0 0 3px rgba(201,168,76,.18);
}
.trip-type-card .tc-icon {
    width: 40px; height: 40px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 8px;
    font-size: 20px;
}
.trip-type-card .tc-label { font-size: 12px; font-weight: 600; color: #334155; }
.trip-type-card input[type=radio] { position: absolute; opacity: 0; width: 0; height: 0; }
.form-label { font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
.form-label .req { color: #ef4444; }
.form-input {
    width: 100%; border: 1.5px solid #e2e8f0; border-radius: 8px;
    padding: 9px 12px; font-size: 13px; font-family: 'Cairo', sans-serif;
    color: #0f172a; outline: none; transition: border-color .15s, box-shadow .15s;
    background: #fff;
}
.form-input:focus { border-color: #c9a84c; box-shadow: 0 0 0 3px rgba(201,168,76,.12); }
.section-header {
    font-size: 12px; font-weight: 700; color: #94a3b8; letter-spacing: .06em;
    text-transform: uppercase; margin-bottom: 14px;
    padding-bottom: 8px; border-bottom: 1px solid #f1f5f9;
    display: flex; align-items: center; gap-6;
}
</style>
@endpush

@section('content')

{{-- Back link --}}
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('admin.trips.index') }}"
       class="flex items-center gap-1.5 text-slate-500 hover:text-slate-800 text-sm transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        عودة إلى الرحلات
    </a>
    <span class="text-slate-300">|</span>
    <h2 class="text-lg font-bold text-slate-800">إنشاء رحلة جديدة</h2>
</div>

<form method="POST" action="{{ route('admin.trips.store') }}">
    @csrf

    {{-- Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">

        {{-- Header bar --}}
        <div style="background:linear-gradient(135deg,#0f172a 0%,#1a2744 100%);padding:20px 24px;display:flex;align-items:center;gap:14px;">
            <div style="width:42px;height:42px;border-radius:10px;background:rgba(201,168,76,.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="22" height="22" fill="none" stroke="#c9a84c" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-3.16-3.16 19.79 19.79 0 01-3.07-8.67A2 2 0 016.93 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11l-1.27 1.27a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/>
                </svg>
            </div>
            <div>
                <div style="color:#c9a84c;font-size:11px;font-weight:600;letter-spacing:.05em;margin-bottom:2px;">رحلة جديدة</div>
                <div style="color:#e2e8f0;font-size:15px;font-weight:700;">بيانات الرحلة</div>
            </div>
        </div>

        <div class="p-6 space-y-7">

            {{-- Section: نوع الرحلة --}}
            <div>
                <div class="section-header">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="inline-block ml-1.5">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    نوع الرحلة <span class="text-red-400">*</span>
                </div>

                {{-- Hidden real select (for form submission + Tom Select ignored) --}}
                <input type="hidden" name="trip_type" id="trip_type_val" value="{{ old('trip_type') }}" required>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3" id="type-cards">
                    @php
                        $types = [
                            'arrival'         => ['label'=>'استلام وصول',  'icon'=>'✈️',  'color'=>'#16a34a', 'bg'=>'#dcfce7'],
                            'departure'       => ['label'=>'مغادرة',        'icon'=>'🛫',  'color'=>'#2563eb', 'bg'=>'#dbeafe'],
                            'group_transport' => ['label'=>'نقل جماعي',     'icon'=>'🚌',  'color'=>'#c9a84c', 'bg'=>'#fef9c3'],
                            'deportation'     => ['label'=>'تسفير',          'icon'=>'📋',  'color'=>'#dc2626', 'bg'=>'#fee2e2'],
                        ];
                    @endphp
                    @foreach($types as $val => $meta)
                    <div class="trip-type-card {{ old('trip_type') == $val ? 'selected' : '' }}"
                         data-value="{{ $val }}"
                         onclick="selectTripType('{{ $val }}')">
                        <div class="tc-icon" style="background:{{ $meta['bg'] }};color:{{ $meta['color'] }}">
                            {{ $meta['icon'] }}
                        </div>
                        <div class="tc-label" style="{{ old('trip_type') == $val ? 'color:'.$meta['color'] : '' }}">{{ $meta['label'] }}</div>
                    </div>
                    @endforeach
                </div>
                @error('trip_type')<p class="text-red-500 text-xs mt-2">{{ $message }}</p>@enderror
            </div>

            {{-- Section: تفاصيل الرحلة --}}
            <div>
                <div class="section-header">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="inline-block ml-1.5">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    تفاصيل الرحلة
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

                    {{-- Date --}}
                    <div>
                        <label class="form-label">
                            <svg width="14" height="14" fill="none" stroke="#c9a84c" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            تاريخ الرحلة <span class="req">*</span>
                        </label>
                        <input type="date" name="trip_date" value="{{ old('trip_date') }}" required class="form-input">
                        @error('trip_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Time --}}
                    <div>
                        <label class="form-label">
                            <svg width="14" height="14" fill="none" stroke="#c9a84c" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            وقت الرحلة
                        </label>
                        <input type="time" name="trip_time" value="{{ old('trip_time') }}" class="form-input">
                    </div>

                    {{-- Airport --}}
                    <div>
                        <label class="form-label">
                            <svg width="14" height="14" fill="none" stroke="#c9a84c" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 017.03 15.5a19.79 19.79 0 01-3.07-8.67A2 2 0 015.93 4h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L10.91 11.9a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                            المطار
                        </label>
                        <select name="airport_id" class="form-input" style="padding:0">
                            <option value="">بدون مطار</option>
                            @foreach($airports as $ap)
                            <option value="{{ $ap->id }}" {{ old('airport_id') == $ap->id ? 'selected' : '' }}>{{ $ap->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Flight number --}}
                    <div>
                        <label class="form-label">
                            <svg width="14" height="14" fill="none" stroke="#c9a84c" stroke-width="2" viewBox="0 0 24 24"><path d="M3 17l2-2m0 0l4-4m-4 4l4-4m10-6l-2 2m0 0l-4 4m4-4l-4 4"/><path d="M12 3l1.5 3h3L15 8l.5 3L12 9l-3.5 2L9 8 7.5 6h3z"/></svg>
                            رقم الرحلة الجوية
                        </label>
                        <input type="text" name="flight_number" value="{{ old('flight_number') }}" placeholder="مثال: SV205" class="form-input">
                    </div>

                    {{-- Branch --}}
                    @if($branchId)
                    <input type="hidden" name="branch_id" value="{{ $branchId }}">
                    <div>
                        <label class="form-label">
                            <svg width="14" height="14" fill="none" stroke="#c9a84c" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                            الفرع
                        </label>
                        <div class="w-full border border-blue-200 bg-blue-50 rounded-lg px-3 py-2 text-sm text-blue-700 font-medium flex items-center gap-1.5" style="min-height:38px;">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                            {{ Auth::guard('admin')->user()->branch?->name ?? 'فرعي' }}
                        </div>
                    </div>
                    @else
                    <div class="sm:col-span-2">
                        <label class="form-label">
                            <svg width="14" height="14" fill="none" stroke="#c9a84c" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                            الفرع <span class="req">*</span>
                        </label>
                        <select name="branch_id" required class="form-input" style="padding:0">
                            <option value="">اختر فرع</option>
                            @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ old('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                            @endforeach
                        </select>
                        @error('branch_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    @endif

                </div>
            </div>

            {{-- Section: ملاحظات --}}
            <div>
                <div class="section-header">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="inline-block ml-1.5">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>
                    </svg>
                    ملاحظات إضافية
                </div>
                <textarea name="notes" rows="3" class="form-input" placeholder="أي ملاحظات أو تفاصيل إضافية...">{{ old('notes') }}</textarea>
            </div>

        </div>

        {{-- Footer --}}
        <div style="border-top:1px solid #f1f5f9;padding:16px 24px;display:flex;gap:10px;align-items:center;">
            <button type="submit"
                    style="background:linear-gradient(135deg,#c9a84c,#a88830);color:#fff;border:none;padding:10px 28px;border-radius:10px;font-size:14px;font-weight:700;font-family:'Cairo',sans-serif;cursor:pointer;display:flex;align-items:center;gap:8px;transition:opacity .2s;"
                    onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                </svg>
                إنشاء الرحلة
            </button>
            <a href="{{ route('admin.trips.index') }}"
               style="padding:10px 20px;border-radius:10px;font-size:13px;font-weight:600;color:#64748b;border:1.5px solid #e2e8f0;text-decoration:none;background:#fff;font-family:'Cairo',sans-serif;transition:background .15s;"
               onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'">
                إلغاء
            </a>
        </div>
    </div>
</form>

@push('scripts')
<script>
function selectTripType(val) {
    document.getElementById('trip_type_val').value = val;
    document.querySelectorAll('#type-cards .trip-type-card').forEach(function(card) {
        card.classList.toggle('selected', card.dataset.value === val);
    });
}
// Restore on validation error
(function() {
    var v = document.getElementById('trip_type_val').value;
    if (v) selectTripType(v);
})();
</script>
@endpush

@endsection

