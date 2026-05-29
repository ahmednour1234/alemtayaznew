@extends('admin.layouts.app')
@section('title', 'تسكين عاملة جديدة')

@push('styles')
<style>
.reason-card {
    position: relative; cursor: pointer;
    border: 2px solid #e2e8f0; border-radius: 12px;
    padding: 14px 12px; text-align: center;
    transition: all .2s ease; background: #fff; user-select: none;
}
.reason-card:hover { border-color: #c9a84c; background: #fdf8e8; }
.reason-card.selected {
    border-color: #c9a84c; background: #fdf8e8;
    box-shadow: 0 0 0 3px rgba(201,168,76,.18);
}
.reason-card .rc-icon {
    width: 42px; height: 42px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 8px; font-size: 20px;
}
.reason-card .rc-label { font-size: 12px; font-weight: 700; color: #334155; }
.reason-card .rc-desc  { font-size: 10px; color: #94a3b8; margin-top: 3px; }
.form-label { font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
.form-label .req { color: #ef4444; }
.form-input {
    width: 100%; border: 1.5px solid #e2e8f0; border-radius: 8px;
    padding: 9px 12px; font-size: 13px; font-family: 'Cairo', sans-serif;
    color: #0f172a; outline: none; transition: border-color .15s, box-shadow .15s; background: #fff;
}
.form-input:focus { border-color: #c9a84c; box-shadow: 0 0 0 3px rgba(201,168,76,.12); }
.section-header {
    font-size: 12px; font-weight: 700; color: #94a3b8; letter-spacing: .06em;
    margin-bottom: 14px; padding-bottom: 8px; border-bottom: 1px solid #f1f5f9;
    display: flex; align-items: center; gap: 6px;
}
</style>
@endpush

@section('content')

<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('admin.housing-assignments.index') }}"
       class="flex items-center gap-1.5 text-slate-500 hover:text-slate-800 text-sm transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        عودة إلى تعيينات السكن
    </a>
    <span class="text-slate-300">|</span>
    <h2 class="text-lg font-bold text-slate-800">تسكين عاملة جديدة</h2>
</div>

<form method="POST" action="{{ route('admin.housing-assignments.store') }}">
    @csrf

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">

        {{-- Header --}}
        <div style="background:linear-gradient(135deg,#0f172a 0%,#1a2744 100%);padding:20px 24px;display:flex;align-items:center;gap:14px;">
            <div style="width:42px;height:42px;border-radius:10px;background:rgba(201,168,76,.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="22" height="22" fill="none" stroke="#c9a84c" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
            </div>
            <div>
                <div style="color:#c9a84c;font-size:11px;font-weight:600;letter-spacing:.05em;margin-bottom:2px;">السكن</div>
                <div style="color:#e2e8f0;font-size:15px;font-weight:700;">تسكين عاملة جديدة</div>
            </div>
        </div>

        <div class="p-6 space-y-7">

            {{-- سبب التسكين --}}
            <div>
                <div class="section-header">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="flex-shrink-0">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    سبب التسكين <span class="text-red-400">*</span>
                </div>

                <input type="hidden" name="reason" id="reason_val" value="{{ old('reason') }}">

                <div class="grid grid-cols-3 lg:grid-cols-6 gap-3" id="reason-cards">
                    @php
                        $reasons = [
                            'sponsorship_transfer' => ['label'=>'نقل كفالة',  'desc'=>'تحويل عقد الكفالة', 'icon'=>'🔄', 'color'=>'#7c3aed', 'bg'=>'#ede9fe'],
                            'deportation'          => ['label'=>'تسفير',       'desc'=>'ترحيل وإعادة للوطن', 'icon'=>'✈️', 'color'=>'#dc2626', 'bg'=>'#fee2e2'],
                            'handover'             => ['label'=>'تسليم',       'desc'=>'تسليم للصاحب أو الجهة', 'icon'=>'🤝', 'color'=>'#16a34a', 'bg'=>'#dcfce7'],
                        ];
                    @endphp
                    @foreach($reasons as $val => $meta)
                    <div class="reason-card {{ old('reason') == $val ? 'selected' : '' }}"
                         data-value="{{ $val }}"
                         onclick="selectReason('{{ $val }}')">
                        <div class="rc-icon" style="background:{{ $meta['bg'] }};color:{{ $meta['color'] }}">{{ $meta['icon'] }}</div>
                        <div class="rc-label" style="{{ old('reason') == $val ? 'color:'.$meta['color'] : '' }}">{{ $meta['label'] }}</div>
                        <div class="rc-desc">{{ $meta['desc'] }}</div>
                    </div>
                    @endforeach
                </div>
                @error('reason')<p class="text-red-500 text-xs mt-2">{{ $message }}</p>@enderror
            </div>

            {{-- بيانات العاملة والسكن --}}
            <div>
                <div class="section-header">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="flex-shrink-0">
                        <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
                    </svg>
                    بيانات العاملة والسكن
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    {{-- Worker --}}
                    <div>
                        <label class="form-label">
                            <svg width="14" height="14" fill="none" stroke="#c9a84c" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            العاملة <span class="req">*</span>
                        </label>
                        <select name="worker_id" required class="form-input" style="padding:0">
                            <option value="">اختر عاملة</option>
                            @foreach($workers as $w)
                            <option value="{{ $w->id }}" {{ old('worker_id') == $w->id ? 'selected' : '' }}>
                                {{ $w->name }}{{ $w->nationality ? ' — '.$w->nationality->name : '' }}
                            </option>
                            @endforeach
                        </select>
                        @error('worker_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Housing --}}
                    <div>
                        <label class="form-label">
                            <svg width="14" height="14" fill="none" stroke="#c9a84c" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                            السكن <span class="req">*</span>
                        </label>
                        <select name="housing_id" required class="form-input" style="padding:0">
                            <option value="">اختر سكن</option>
                            @foreach($housings as $h)
                            <option value="{{ $h->id }}" {{ old('housing_id') == $h->id ? 'selected' : '' }}>{{ $h->name }}</option>
                            @endforeach
                        </select>
                        @error('housing_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Branch --}}
                    @if($branches)
                    <div>
                        <label class="form-label">
                            <svg width="14" height="14" fill="none" stroke="#c9a84c" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
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
                    @else
                    <input type="hidden" name="branch_id" value="{{ $branchId }}">
                    @endif

                    {{-- Check-in date --}}
                    <div>
                        <label class="form-label">
                            <svg width="14" height="14" fill="none" stroke="#c9a84c" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            تاريخ الدخول <span class="req">*</span>
                        </label>
                        <input type="date" name="check_in_date" value="{{ old('check_in_date', date('Y-m-d')) }}" required class="form-input">
                        @error('check_in_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- ملاحظات --}}
            <div>
                <div class="section-header">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="flex-shrink-0">
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
                حفظ التعيين
            </button>
            <a href="{{ route('admin.housing-assignments.index') }}"
               style="padding:10px 20px;border-radius:10px;font-size:13px;font-weight:600;color:#64748b;border:1.5px solid #e2e8f0;text-decoration:none;background:#fff;font-family:'Cairo',sans-serif;transition:background .15s;"
               onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'">
                إلغاء
            </a>
        </div>
    </div>
</form>

@push('scripts')
<script>
function selectReason(val) {
    document.getElementById('reason_val').value = val;
    document.querySelectorAll('#reason-cards .reason-card').forEach(function(card) {
        card.classList.toggle('selected', card.dataset.value === val);
        var lbl = card.querySelector('.rc-label');
        if (card.dataset.value === val) {
            lbl.style.color = {'sponsorship_transfer':'#7c3aed','deportation':'#dc2626','handover':'#16a34a'}[val] || '#334155';
        } else {
            lbl.style.color = '#334155';
        }
    });
}
(function(){ var v = document.getElementById('reason_val').value; if (v) selectReason(v); })();
</script>
@endpush

@endsection
