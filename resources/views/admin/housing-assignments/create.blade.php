@extends('admin.layouts.app')
@section('title', 'تسكين عاملة جديدة')

@push('styles')
<style>
.reason-card {
    position: relative; cursor: pointer;
    border: 2px solid #e2e8f0; border-radius: 14px;
    padding: 20px 14px; text-align: center;
    transition: all .22s ease; background: #fff; user-select: none;
    display: flex; flex-direction: column; align-items: center; gap: 10px;
}
.reason-card:hover { border-color: #c9a84c; background: #fdf8e8; transform: translateY(-2px); box-shadow: 0 4px 16px rgba(0,0,0,.08); }
.reason-card.selected {
    border-color: #c9a84c; background: #fdf8e8;
    box-shadow: 0 0 0 3px rgba(201,168,76,.18), 0 4px 16px rgba(0,0,0,.08);
    transform: translateY(-2px);
}
.reason-card .rc-check {
    position: absolute; top: 10px; left: 10px;
    width: 20px; height: 20px; border-radius: 50%;
    background: #c9a84c; display: none; align-items: center; justify-content: center;
}
.reason-card.selected .rc-check { display: flex; }
.reason-card .rc-icon {
    width: 54px; height: 54px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center; font-size: 26px;
    transition: transform .2s;
}
.reason-card:hover .rc-icon, .reason-card.selected .rc-icon { transform: scale(1.08); }
.reason-card .rc-label { font-size: 13px; font-weight: 700; color: #334155; }
.reason-card .rc-desc  { font-size: 11px; color: #94a3b8; line-height: 1.4; }
.form-label { font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
.form-label .req { color: #ef4444; }
.form-input {
    width: 100%; border: 1.5px solid #e2e8f0; border-radius: 8px;
    padding: 9px 12px; font-size: 13px; font-family: 'Cairo', sans-serif;
    color: #0f172a; outline: none; transition: border-color .15s, box-shadow .15s; background: #fff;
}
.form-input:focus { border-color: #c9a84c; box-shadow: 0 0 0 3px rgba(201,168,76,.12); }
.section-header {
    font-size: 11px; font-weight: 700; color: #94a3b8; letter-spacing: .07em; text-transform: uppercase;
    margin-bottom: 16px; padding-bottom: 10px; border-bottom: 1px solid #f1f5f9;
    display: flex; align-items: center; gap: 8px;
}
.field-card {
    background: #fafafa; border: 1.5px solid #f1f5f9; border-radius: 12px;
    padding: 14px 16px; transition: border-color .15s;
}
.field-card:focus-within { border-color: #c9a84c; background: #fffdf5; }
.field-card .form-label { margin-bottom: 4px; }
.field-card .form-input { border: none; background: transparent; padding: 4px 0; font-size: 14px; }
.field-card .form-input:focus { box-shadow: none; }
</style>
@endpush

@section('content')

@php $__me = Auth::guard('admin')->user(); @endphp

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

<div x-data="housingForm()">
<form method="POST" action="{{ route('admin.housing-assignments.store') }}">
    @csrf

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">

        {{-- Header --}}
        <div style="background:linear-gradient(135deg,#0f172a 0%,#1a2744 100%);padding:22px 28px;display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
            <div style="width:46px;height:46px;border-radius:12px;background:rgba(201,168,76,.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="24" height="24" fill="none" stroke="#c9a84c" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
            </div>
            <div style="flex:1;min-width:0;">
                <div style="color:#c9a84c;font-size:11px;font-weight:600;letter-spacing:.06em;margin-bottom:3px;">سكن جديد</div>
                <div style="color:#e2e8f0;font-size:16px;font-weight:700;">تسكين عاملة جديدة</div>
            </div>
            {{-- Creator badge --}}
            <div style="background:rgba(99,102,241,.15);border:1px solid rgba(99,102,241,.3);border-radius:10px;padding:8px 16px;">
                <div style="color:#a5b4fc;font-size:10px;font-weight:600;margin-bottom:3px;">سيُسجَّل بواسطة</div>
                <div style="color:#e2e8f0;font-size:13px;font-weight:700;">{{ $__me->name }}</div>
                @if($__me->branch)
                <div style="color:#94a3b8;font-size:10px;margin-top:1px;display:flex;align-items:center;gap:4px;">
                    <svg width="9" height="9" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                    {{ $__me->branch->name }}
                </div>
                @endif
            </div>
        </div>

        <div class="p-7 space-y-8">

            {{-- سبب التسكين --}}
            <div>
                <div class="section-header">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="flex-shrink-0">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    سبب التسكين <span class="text-red-400 font-normal">*</span>
                </div>

                <input type="hidden" name="reason" id="reason_val" value="{{ old('reason') }}">

                <div class="grid grid-cols-1 sm:grid-cols-3 xl:grid-cols-5 gap-4" id="reason-cards">
                    @php
                        $reasons = [
                            'sponsorship_transfer' => [
                                'label' => 'نقل كفالة',
                                'desc'  => 'تحويل عقد الكفالة لكفيل جديد',
                                'icon'  => '🔄',
                                'color' => '#7c3aed',
                                'bg'    => 'linear-gradient(135deg,#ede9fe,#ddd6fe)',
                            ],
                            'deportation' => [
                                'label' => 'تسفير',
                                'desc'  => 'ترحيل العاملة وإعادتها للوطن',
                                'icon'  => '✈️',
                                'color' => '#dc2626',
                                'bg'    => 'linear-gradient(135deg,#fee2e2,#fecaca)',
                            ],
                            'handover' => [
                                'label' => 'تسليم',
                                'desc'  => 'تسليم للصاحب أو الجهة المعنية',
                                'icon'  => '🤝',
                                'color' => '#16a34a',
                                'bg'    => 'linear-gradient(135deg,#dcfce7,#bbf7d0)',
                            ],
                            'rental' => [
                                'label' => 'تأجير',
                                'desc'  => 'العاملة متاحة للتأجير وتعمل في السكن',
                                'icon'  => '🏷️',
                                'color' => '#0891b2',
                                'bg'    => 'linear-gradient(135deg,#cffafe,#a5f3fc)',
                            ],
                            'settlement' => [
                                'label' => 'تسوية',
                                'desc'  => 'تسوية وضع العاملة مع الكفيل أو الجهة',
                                'icon'  => '⚖️',
                                'color' => '#b45309',
                                'bg'    => 'linear-gradient(135deg,#fef3c7,#fde68a)',
                            ],
                        ];
                    @endphp
                    @foreach($reasons as $val => $meta)
                    <div class="reason-card {{ old('reason') == $val ? 'selected' : '' }}"
                         data-value="{{ $val }}" data-color="{{ $meta['color'] }}"
                         onclick="selectReason('{{ $val }}')">
                        <div class="rc-check">
                            <svg width="11" height="11" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
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
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="flex-shrink-0">
                        <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
                    </svg>
                    بيانات العاملة والسكن
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

                    {{-- Worker --}}
                    <div class="field-card">
                        <label class="form-label" style="justify-content:space-between;">
                            <span style="display:flex;align-items:center;gap:6px;">
                                <svg width="13" height="13" fill="none" stroke="#c9a84c" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                العاملة <span class="req">*</span>
                            </span>
                            <button type="button" @click="workerModal.open=true"
                                    class="text-indigo-500 hover:text-indigo-700 font-normal text-xs underline">+ إضافة عاملة جديدة</button>
                        </label>
                        <select id="workerSelect" name="worker_id" required class="form-input" style="padding:4px 0">
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
                    <div class="field-card">
                        <label class="form-label">
                            <svg width="13" height="13" fill="none" stroke="#c9a84c" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                            السكن <span class="req">*</span>
                        </label>
                        <select name="housing_id" required class="form-input" style="padding:4px 0">
                            <option value="">اختر سكن</option>
                            @foreach($housings as $h)
                            <option value="{{ $h->id }}" {{ old('housing_id') == $h->id ? 'selected' : '' }}>{{ $h->name }}</option>
                            @endforeach
                        </select>
                        @error('housing_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Branch --}}
                    <div class="field-card">
                        <label class="form-label">
                            <svg width="13" height="13" fill="none" stroke="#c9a84c" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                            الفرع <span class="req">*</span>
                        </label>
                        @if($branches)
                            <select name="branch_id" required class="form-input" style="padding:4px 0">
                                <option value="">اختر فرع</option>
                                @foreach($branches as $b)
                                <option value="{{ $b->id }}" {{ old('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                @endforeach
                            </select>
                            @error('branch_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        @else
                            <input type="hidden" name="branch_id" value="{{ $branchId }}">
                            <div class="flex items-center gap-1.5 text-blue-700 font-medium text-sm pt-1">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                                {{ $__me->branch?->name ?? 'فرعي' }}
                            </div>
                        @endif
                    </div>

                    {{-- Check-in date --}}
                    <div class="field-card">
                        <label class="form-label">
                            <svg width="13" height="13" fill="none" stroke="#c9a84c" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            تاريخ الدخول <span class="req">*</span>
                        </label>
                        <input type="date" name="check_in_date" value="{{ old('check_in_date', date('Y-m-d')) }}" required class="form-input">
                        @error('check_in_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                </div>
            </div>

            {{-- التاريخ المتوقع للمغادرة --}}
            <div>
                <div class="section-header">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="flex-shrink-0">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    التاريخ المتوقع للمغادرة
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="field-card">
                        <label class="form-label">
                            <svg width="13" height="13" fill="none" stroke="#c9a84c" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            التاريخ المتوقع للمغادرة
                            <span class="text-slate-400 font-normal text-xs mr-1">(اختياري)</span>
                        </label>
                        <input type="date" name="expected_check_out_date"
                               value="{{ old('expected_check_out_date') }}"
                               class="form-input">
                        @error('expected_check_out_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex items-center gap-2 text-slate-500 text-xs pt-2">
                        <svg width="14" height="14" fill="none" stroke="#f59e0b" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        سيصل تنبيه تلقائي لمدير الفرع والمدير العام ورئيس مجلس الإدارة عند التأخر عن هذا التاريخ.
                    </div>
                </div>

            {{-- حالة العمالة --}}
            <div>
                <div class="section-header">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="flex-shrink:0">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    حالة العمالة
                </div>
                <input type="hidden" name="worker_status" id="worker_status_val" value="{{ old('worker_status', 'normal') }}">
                <div class="flex gap-3 flex-wrap">
                    @foreach(\App\Models\HousingAssignment::workerStatuses() as $val => $meta)
                    <div onclick="selectWorkerStatus('{{ $val }}')"
                         id="ws_card_{{ $val }}"
                         style="cursor:pointer;border:2px solid {{ old('worker_status','normal') === $val ? $meta['color'] : '#e2e8f0' }};border-radius:12px;padding:12px 22px;background:{{ old('worker_status','normal') === $val ? $meta['bg'] : '#fff' }};transition:all .18s;display:flex;align-items:center;gap:8px;font-size:13px;font-weight:700;color:{{ old('worker_status','normal') === $val ? $meta['color'] : '#64748b' }};">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><circle cx="12" cy="7" r="4"/><path d="M20 21a8 8 0 10-16 0"/></svg>
                        {{ $meta['label'] }}
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- ملاحظات --}}
            <div>
                <div class="section-header">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="flex-shrink-0">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>
                    </svg>
                    ملاحظات إضافية
                </div>
                <textarea name="notes" rows="3" class="form-input" placeholder="أي ملاحظات أو تفاصيل إضافية...">{{ old('notes') }}</textarea>
            </div>

        </div>

        {{-- Footer --}}
        <div style="border-top:1px solid #f1f5f9;padding:18px 28px;display:flex;gap:10px;align-items:center;background:#fafafa;">
            <button type="submit"
                    style="background:linear-gradient(135deg,#c9a84c,#a88830);color:#fff;border:none;padding:11px 30px;border-radius:10px;font-size:14px;font-weight:700;font-family:'Cairo',sans-serif;cursor:pointer;display:flex;align-items:center;gap:8px;transition:opacity .2s;box-shadow:0 2px 8px rgba(201,168,76,.35);"
                    onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                </svg>
                حفظ التعيين
            </button>
            <a href="{{ route('admin.housing-assignments.index') }}"
               style="padding:11px 22px;border-radius:10px;font-size:13px;font-weight:600;color:#64748b;border:1.5px solid #e2e8f0;text-decoration:none;background:#fff;font-family:'Cairo',sans-serif;transition:background .15s;"
               onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'">
                إلغاء
            </a>
        </div>
    </div>
</form>

    {{-- ═══ MODAL: إضافة عاملة ════════════════════════════════════════════════ --}}
    <div x-show="workerModal.open" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center"
         style="background:rgba(0,0,0,.45)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden"
             @click.outside="workerModal.open=false">
            <div class="bg-indigo-600 px-6 py-4 flex items-center justify-between">
                <h3 class="text-white font-bold text-base">إضافة عاملة جديدة</h3>
                <button type="button" @click="workerModal.open=false"
                        class="text-indigo-100 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">الاسم <span class="text-red-500">*</span></label>
                    <input type="text" x-model="workerModal.name" placeholder="اسم العاملة"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">الجنسية</label>
                    <select x-model="workerModal.nationality_id"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition">
                        <option value="">— اختر —</option>
                        @foreach(\App\Models\Nationality::where('active', true)->orderBy('name')->get() as $nat)
                        <option value="{{ $nat->id }}">{{ $nat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">رقم الجواز</label>
                    <input type="text" x-model="workerModal.passport_number" placeholder="A1234567"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition">
                </div>
                <div x-show="workerModal.error" class="bg-red-50 text-red-600 text-sm rounded-xl px-4 py-2" x-text="workerModal.error"></div>
            </div>
            <div class="px-6 pb-5 flex gap-3 justify-end">
                <button type="button" @click="workerModal.open=false"
                        class="bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm px-5 py-2 rounded-xl">إلغاء</button>
                <button type="button" @click="submitWorker()" :disabled="workerModal.loading"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-6 py-2 rounded-xl shadow disabled:opacity-60">
                    <span x-show="!workerModal.loading">حفظ العاملة</span>
                    <span x-show="workerModal.loading">جاري الحفظ...</span>
                </button>
            </div>
        </div>
    </div>

</div>{{-- end x-data --}}

@push('scripts')
<script>
const reasonColors = {
    sponsorship_transfer: '#7c3aed',
    deportation: '#dc2626',
    handover: '#16a34a',
    rental: '#0891b2',
    settlement: '#b45309',
};
function selectReason(val) {
    document.getElementById('reason_val').value = val;
    document.querySelectorAll('#reason-cards .reason-card').forEach(function(card) {
        const isSelected = card.dataset.value === val;
        card.classList.toggle('selected', isSelected);
        card.querySelector('.rc-label').style.color = isSelected ? (reasonColors[val] || '#334155') : '#334155';
    });
}
(function(){ var v = document.getElementById('reason_val').value; if (v) selectReason(v); })();

// حالة العمالة
var wsColors = { normal: '#16a34a', escaped: '#b91c1c', sick: '#b45309' };
var wsBg     = { normal: '#dcfce7', escaped: '#fee2e2', sick: '#fef3c7' };
function selectWorkerStatus(val) {
    document.getElementById('worker_status_val').value = val;
    ['normal','escaped','sick'].forEach(function(s) {
        var el = document.getElementById('ws_card_' + s);
        if (!el) return;
        var active = s === val;
        el.style.borderColor  = active ? wsColors[s] : '#e2e8f0';
        el.style.background   = active ? wsBg[s]     : '#fff';
        el.style.color        = active ? wsColors[s]  : '#64748b';
    });
}
</script>

<script>
function housingForm() {
    return {
        workerModal: { open: false, name: '', nationality_id: '', passport_number: '', loading: false, error: '' },

        async submitWorker() {
            if (!this.workerModal.name.trim()) {
                this.workerModal.error = 'الاسم مطلوب';
                return;
            }
            this.workerModal.loading = true;
            this.workerModal.error   = '';
            try {
                const res = await fetch('{{ route("admin.workers.quick-store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        name: this.workerModal.name,
                        nationality_id: this.workerModal.nationality_id || null,
                        passport_number: this.workerModal.passport_number || null
                    })
                });
                const data = await res.json();
                if (data.id) {
                    const select = document.getElementById('workerSelect');
                    const opt = new Option(data.name + (data.nationality ? ' — ' + data.nationality : ''), data.id, true, true);
                    select.appendChild(opt);
                    select.value = data.id;
                    this.workerModal = { open: false, name: '', nationality_id: '', passport_number: '', loading: false, error: '' };
                } else {
                    this.workerModal.error = data.message || 'حدث خطأ';
                }
            } catch (e) {
                this.workerModal.error = 'تعذّر الاتصال بالخادم';
            }
            this.workerModal.loading = false;
        },
    };
}
</script>
@endpush

@endsection
