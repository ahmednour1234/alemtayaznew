@extends('admin.layouts.app')
@section('title', 'عقد نقل كفالة جديد')

@push('styles')
<style>
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
.worker-info-box {
    background: linear-gradient(135deg, #fdf8e8, #fff9f0);
    border: 1.5px solid #f0e0a4; border-radius: 10px;
    padding: 12px 16px; display: none;
}
.worker-info-box.visible { display: flex; align-items: center; gap: 12px; }
.fee-row { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
@media(max-width:640px){ .fee-row { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')

<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('admin.sponsorship-transfers.index') }}"
       class="flex items-center gap-1.5 text-slate-500 hover:text-slate-800 text-sm transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        عودة إلى العقود
    </a>
    <span class="text-slate-300">|</span>
    <h2 class="text-lg font-bold text-slate-800">إنشاء عقد نقل كفالة جديد</h2>
</div>

<form method="POST" action="{{ route('admin.sponsorship-transfers.store') }}" enctype="multipart/form-data" class="w-full">
    @csrf
    <input type="hidden" name="original_contract_id" id="original_contract_id" value="{{ old('original_contract_id') }}">

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">

        {{-- Header --}}
        <div style="background:linear-gradient(135deg,#0f172a 0%,#1a2744 100%);padding:20px 24px;display:flex;align-items:center;gap:14px;">
            <div style="width:42px;height:42px;border-radius:10px;background:rgba(201,168,76,.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="22" height="22" fill="none" stroke="#c9a84c" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M16 3h5v5M4 20L21 3M21 3l-5 1M8 21H3v-5"/>
                </svg>
            </div>
            <div>
                <div style="color:#c9a84c;font-size:11px;font-weight:600;letter-spacing:.05em;margin-bottom:2px;">عقد جديد</div>
                <div style="color:#e2e8f0;font-size:15px;font-weight:700;">نقل كفالة عاملة</div>
            </div>
            <div style="margin-right:auto;display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                @php $__me = Auth::guard('admin')->user(); @endphp
                <div style="background:rgba(201,168,76,.15);border:1px solid rgba(201,168,76,.3);border-radius:8px;padding:6px 14px;text-align:center;">
                    <div style="color:#c9a84c;font-size:10px;font-weight:600;margin-bottom:1px;">ملاحظة</div>
                    <div style="color:#e2e8f0;font-size:11px;">يُوقف عقد الاستقدام تلقائياً</div>
                </div>
                <div style="background:rgba(99,102,241,.15);border:1px solid rgba(99,102,241,.3);border-radius:8px;padding:6px 14px;">
                    <div style="color:#a5b4fc;font-size:10px;font-weight:600;margin-bottom:2px;">سيُسجَّل بواسطة</div>
                    <div style="color:#e2e8f0;font-size:12px;font-weight:700;">{{ $__me->name }}</div>
                    @if($__me->branch)
                    <div style="color:#94a3b8;font-size:10px;margin-top:1px;">{{ $__me->branch->name }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="p-6 space-y-7" x-data="{ tab: 'contract' }">

            {{-- Tab Navigation --}}
            <div style="display:flex;gap:0;border-bottom:2px solid #f1f5f9;margin-bottom:8px;">
                <button type="button"
                        @click="tab='contract'"
                        :style="tab==='contract' ? 'border-bottom:2px solid #c9a84c;color:#c9a84c;margin-bottom:-2px;' : 'border-bottom:2px solid transparent;color:#94a3b8;margin-bottom:-2px;'"
                        style="padding:10px 22px;font-size:13px;font-weight:700;font-family:'Cairo',sans-serif;background:none;border:none;border-top:none;border-left:none;border-right:none;cursor:pointer;display:flex;align-items:center;gap:6px;transition:color .15s;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    بيانات العقد
                </button>
                <button type="button"
                        @click="tab='fees'"
                        :style="tab==='fees' ? 'border-bottom:2px solid #c9a84c;color:#c9a84c;margin-bottom:-2px;' : 'border-bottom:2px solid transparent;color:#94a3b8;margin-bottom:-2px;'"
                        style="padding:10px 22px;font-size:13px;font-weight:700;font-family:'Cairo',sans-serif;background:none;border:none;border-top:none;border-left:none;border-right:none;cursor:pointer;display:flex;align-items:center;gap:6px;transition:color .15s;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                    الرسوم والملاحظات
                </button>
            </div>

            {{-- TAB 1: بيانات العقد --}}
            <div x-show="tab==='contract'" class="space-y-7">

            {{-- Section: بيانات العاملة --}}
            <div>
                <div class="section-header">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="flex-shrink-0">
                        <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
                    </svg>
                    بيانات العاملة
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- Worker --}}
                    <div>
                        <label class="form-label">
                            <svg width="14" height="14" fill="none" stroke="#c9a84c" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            العاملة <span class="req">*</span>
                        </label>
                        <select name="worker_id" id="worker_select" required class="form-input" style="padding:0"
                                onchange="onWorkerChange(this.value)">
                            <option value="">اختر عاملة...</option>
                            @if($housingWorkers->isNotEmpty())
                            <optgroup label="🏠 عاملات في السكن">
                                @foreach($housingWorkers as $w)
                                <option value="{{ $w->id }}" {{ old('worker_id') == $w->id ? 'selected' : '' }}>
                                    {{ $w->name }}{{ $w->nationality ? ' — '.$w->nationality->name : '' }}
                                </option>
                                @endforeach
                            </optgroup>
                            @endif
                            @if($contractWorkers->isNotEmpty())
                            <optgroup label="📋 وصلت من عقود الاستقدام">
                                @foreach($contractWorkers as $w)
                                <option value="{{ $w->id }}" {{ old('worker_id') == $w->id ? 'selected' : '' }}>
                                    {{ $w->name }}{{ $w->nationality ? ' — '.$w->nationality->name : '' }}
                                </option>
                                @endforeach
                            </optgroup>
                            @endif
                        </select>
                        @error('worker_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Branch --}}
                    @if($branches)
                    <div>
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
                    </div>
                    @else
                    <input type="hidden" name="branch_id" value="{{ $branchId }}">
                    @endif
                </div>

                {{-- Worker info card (auto-filled) --}}
                <div class="worker-info-box mt-4" id="worker_info_box">
                    <div style="width:36px;height:36px;border-radius:9px;background:#fdf8e8;border:1px solid #f0e0a4;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="18" height="18" fill="none" stroke="#c9a84c" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                    </div>
                    <div>
                        <div style="font-size:11px;color:#94a3b8;font-weight:600;margin-bottom:2px;">الكفيل الحالي (تم الجلب تلقائياً من عقد الاستقدام)</div>
                        <div style="font-size:13px;font-weight:700;color:#92400e;" id="current_sponsor_name">—</div>
                    </div>
                </div>
            </div>

            {{-- Section: الكفلاء --}}
            <div>
                <div class="section-header">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="flex-shrink-0">
                        <path d="M16 3h5v5M4 20L21 3"/>
                    </svg>
                    بيانات الكفيل — النقل
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- From client --}}
                    <div>
                        <label class="form-label">
                            <svg width="14" height="14" fill="none" stroke="#c9a84c" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            الكفيل المُحيل (من) <span class="req">*</span>
                            <span style="font-size:10px;font-weight:400;color:#c9a84c;background:#fdf8e8;padding:2px 7px;border-radius:10px;border:1px solid #f0e0a4;">يُجلب تلقائياً</span>
                        </label>
                        <select name="from_client_id" id="from_client_id" required class="form-input" style="padding:0">
                            <option value="">اختر عميل</option>
                            @foreach($clients as $c)
                            <option value="{{ $c->id }}" {{ old('from_client_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                        @error('from_client_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- To client --}}
                    <div>
                        <label class="form-label">
                            <svg width="14" height="14" fill="none" stroke="#c9a84c" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            الكفيل المستلم (إلى)
                        </label>
                        <select name="to_client_id" class="form-input" style="padding:0">
                            <option value="">لم يُحدد بعد</option>
                            @foreach($clients as $c)
                            <option value="{{ $c->id }}" {{ old('to_client_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Transfer date --}}
                    <div>
                        <label class="form-label">
                            <svg width="14" height="14" fill="none" stroke="#c9a84c" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            تاريخ النقل
                        </label>
                        <input type="date" name="transfer_date" value="{{ old('transfer_date') }}" class="form-input">
                    </div>

                    {{-- Musaned contract number --}}
                    <div>
                        <label class="form-label">
                            <svg width="14" height="14" fill="none" stroke="#c9a84c" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                            رقم العقد على مساند
                        </label>
                        <input type="text" name="musaned_contract_number"
                               value="{{ old('musaned_contract_number') }}"
                               placeholder="أدخل رقم العقد على منصة مساند"
                               class="form-input">
                        @error('musaned_contract_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Musaned contract image --}}
                    <div>
                        <label class="form-label">
                            <svg width="14" height="14" fill="none" stroke="#c9a84c" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            صورة العقد (مساند)
                        </label>
                        <input type="file" name="musaned_contract_image" accept="image/*"
                               class="form-input" style="padding:6px 12px;">
                        @error('musaned_contract_image')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Payment status --}}
                    <div>
                        <label class="form-label">
                            <svg width="14" height="14" fill="none" stroke="#c9a84c" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                            حالة الدفع <span class="req">*</span>
                        </label>
                        <select name="payment_status" required class="form-input" style="padding:0">
                            @foreach(\App\Models\SponsorshipTransfer::paymentStatuses() as $val => $label)
                            <option value="{{ $val }}" {{ old('payment_status', 'pending') == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Next Tab Button --}}
            <div class="flex justify-start pt-2">
                <button type="button" @click="tab='fees'"
                        style="background:linear-gradient(135deg,#c9a84c,#a88830);color:#fff;border:none;padding:9px 24px;border-radius:9px;font-size:13px;font-weight:700;font-family:'Cairo',sans-serif;cursor:pointer;display:flex;align-items:center;gap:7px;">
                    التالي — الرسوم والملاحظات
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                </button>
            </div>

            </div>{{-- end tab 1 --}}

            {{-- TAB 2: الرسوم والملاحظات --}}
            <div x-show="tab==='fees'" class="space-y-7">

            {{-- Section: الرسوم --}}
            <div>
                <div class="section-header">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="flex-shrink-0">
                        <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
                    </svg>
                    الرسوم المالية
                </div>
                <div class="fee-row">
                    <div>
                        <label class="form-label">
                            <svg width="14" height="14" fill="none" stroke="#c9a84c" stroke-width="2" viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                            إجمالي الرسوم <span class="req">*</span>
                        </label>
                        <input type="number" name="total_fees" id="total_fees"
                               value="{{ old('total_fees', 0) }}" min="0" step="0.01" required
                               class="form-input" oninput="calcNet()">
                    </div>
                    <div>
                        <label class="form-label">
                            <svg width="14" height="14" fill="none" stroke="#c9a84c" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            رسوم الخدمة <span class="req">*</span>
                        </label>
                        <input type="number" name="service_fee" value="{{ old('service_fee', 0) }}"
                               min="0" step="0.01" required class="form-input">
                    </div>
                    <div>
                        <label class="form-label">
                            <svg width="14" height="14" fill="none" stroke="#dc2626" stroke-width="2" viewBox="0 0 24 24"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/><polyline points="17 18 23 18 23 12"/></svg>
                            الفقد (خسارة) <span class="req">*</span>
                        </label>
                        <input type="number" name="loss_amount" id="loss_amount"
                               value="{{ old('loss_amount', 0) }}" min="0" step="0.01" required
                               class="form-input" oninput="calcNet()">
                    </div>
                </div>

                {{-- Net result preview --}}
                <div id="net_preview" style="display:none;margin-top:12px;padding:10px 16px;border-radius:8px;background:#f8fafc;border:1px solid #e2e8f0;font-size:13px;font-family:'Cairo',sans-serif;display:flex;align-items:center;gap:8px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    <span style="color:#64748b;font-weight:600;">صافي النتيجة:</span>
                    <span id="net_value" style="font-weight:700;font-size:14px;"></span>
                </div>
            </div>

            {{-- Notes --}}
            <div>
                <div class="section-header">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="flex-shrink-0">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>
                    </svg>
                    ملاحظات إضافية
                </div>
                <textarea name="notes" rows="3" class="form-input" placeholder="أي ملاحظات أو تفاصيل إضافية...">{{ old('notes') }}</textarea>
            </div>

            {{-- Back Button --}}
            <div class="flex justify-start pt-2">
                <button type="button" @click="tab='contract'"
                        style="padding:9px 22px;border-radius:9px;font-size:13px;font-weight:700;color:#64748b;border:1.5px solid #e2e8f0;background:#fff;font-family:'Cairo',sans-serif;cursor:pointer;display:flex;align-items:center;gap:7px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
                    السابق — بيانات العقد
                </button>
            </div>

            </div>{{-- end tab 2 --}}

        </div>{{-- end p-6 --}}

        {{-- Footer --}}
        <div style="border-top:1px solid #f1f5f9;padding:16px 24px;display:flex;gap:10px;align-items:center;">
            <button type="submit"
                    style="background:linear-gradient(135deg,#c9a84c,#a88830);color:#fff;border:none;padding:10px 28px;border-radius:10px;font-size:14px;font-weight:700;font-family:'Cairo',sans-serif;cursor:pointer;display:flex;align-items:center;gap:8px;transition:opacity .2s;"
                    onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                </svg>
                إنشاء العقد
            </button>
            <a href="{{ route('admin.sponsorship-transfers.index') }}"
               style="padding:10px 20px;border-radius:10px;font-size:13px;font-weight:600;color:#64748b;border:1.5px solid #e2e8f0;text-decoration:none;background:#fff;font-family:'Cairo',sans-serif;"
               onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'">
                إلغاء
            </a>
        </div>
    </div>
</form>

@push('scripts')
<script>
var workersData = @json($workersJson);

function onWorkerChange(workerId) {
    var info = workersData.find(function(w){ return w.id == workerId; });
    var box  = document.getElementById('worker_info_box');
    var fromSel = document.getElementById('from_client_id');
    var contractInput = document.getElementById('original_contract_id');

    if (!info) {
        box.classList.remove('visible');
        document.getElementById('current_sponsor_name').textContent = '—';
        return;
    }

    // Show info box
    box.classList.add('visible');
    document.getElementById('current_sponsor_name').textContent = info.client_name || 'غير محدد';

    // Auto-select from_client_id
    if (info.client_id && fromSel) {
        fromSel.value = info.client_id;
        // Sync Tom Select if active
        if (fromSel._tomSelect) {
            fromSel._tomSelect.setValue(String(info.client_id));
        }
    }

    // Auto-fill original_contract_id
    if (contractInput) {
        contractInput.value = info.contract_id || '';
    }
}

// Net result calculator
function calcNet() {
    var total = parseFloat(document.getElementById('total_fees').value) || 0;
    var loss  = parseFloat(document.getElementById('loss_amount').value) || 0;
    var net   = total - loss;
    var preview = document.getElementById('net_preview');
    var valEl   = document.getElementById('net_value');
    preview.style.display = 'flex';
    valEl.textContent = net.toLocaleString('ar-SA') + ' ريال';
    valEl.style.color = net >= 0 ? '#16a34a' : '#dc2626';
}

// Restore on validation error
(function(){
    var v = document.getElementById('worker_select').value;
    if (v) onWorkerChange(v);
    calcNet();
    // On validation error, open the fees tab if fees fields have errors
    @if($errors->hasAny(['total_fees','service_fee','loss_amount','notes']))
    document.addEventListener('alpine:init', function(){
        // switch to fees tab on error
        setTimeout(function(){
            var el = document.querySelector('[x-data]');
            if (el && el._x_dataStack) {
                el._x_dataStack[0].tab = 'fees';
            }
        }, 0);
    });
    @endif
})();
</script>
@endpush

@endsection
