@extends('admin.layouts.app')
@section('title', 'إنشاء عقد نقل كفالة')

@push('styles')
<style>
    [x-cloak] { display: none !important; }
    .st-shell {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 0 0 14px 14px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, .06);
        overflow: hidden;
    }
    .st-hero {
        min-height: 72px;
        padding: 14px 26px;
        background:
            radial-gradient(circle at 20% 0%, rgba(59, 130, 246, .16), transparent 32%),
            linear-gradient(135deg, #0a1428 0%, #132344 100%);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        color: #fff;
    }
    .st-title-wrap { display: flex; align-items: center; gap: 14px; }
    .st-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 9px;
        border: 1px solid rgba(201, 168, 76, .8);
        display: grid;
        place-items: center;
        color: #c9a84c;
    }
    .st-kicker { color: #c9a84c; font-size: 11px; font-weight: 700; margin-bottom: 2px; }
    .st-title { color: #f8fafc; font-size: 18px; font-weight: 800; }
    .st-actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .st-top-btn {
        min-height: 50px;
        padding: 8px 18px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        font-size: 12px;
        font-weight: 800;
        transition: .15s ease;
    }
    .st-top-btn.primary { border: 1px solid rgba(201, 168, 76, .85); color: #f8e8a8; }
    .st-top-btn.ghost { border: 1px solid rgba(148, 163, 184, .65); color: #e2e8f0; }
    .st-top-btn:hover { transform: translateY(-1px); background: rgba(255,255,255,.06); }
    .st-body { padding: 24px 26px 28px; }
    .st-tabs {
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: flex-end;
        gap: 22px;
        margin-bottom: 20px;
    }
    .st-tab {
        border: 0;
        background: transparent;
        padding: 0 0 11px;
        margin-bottom: -1px;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        font-family: Cairo, sans-serif;
        font-size: 13px;
        font-weight: 800;
        color: #94a3b8;
        border-bottom: 2px solid transparent;
        cursor: pointer;
    }
    .st-tab.active { color: #172033; border-bottom-color: #c9a84c; }
    .st-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px 22px; }
    .st-field { min-width: 0; }
    .st-label {
        min-height: 23px;
        display: flex;
        align-items: center;
        gap: 7px;
        margin-bottom: 6px;
        color: #172033;
        font-size: 12px;
        font-weight: 800;
    }
    .st-label svg { color: #111827; flex-shrink: 0; }
    .st-label .req { color: #dc2626; }
    .st-label .tag {
        color: #b38619;
        border: 1px solid #efd27a;
        background: #fffbeb;
        border-radius: 999px;
        padding: 1px 8px;
        font-size: 10px;
        font-weight: 700;
    }
    .st-control {
        width: 100%;
        min-height: 36px;
        border: 1px solid #d9e0ea;
        border-radius: 7px;
        background: #fff;
        color: #0f172a;
        padding: 7px 12px;
        font-family: Cairo, sans-serif;
        font-size: 12.5px;
        outline: none;
        transition: border-color .15s ease, box-shadow .15s ease;
    }
    select.st-control {
        padding: 6px 12px;
        line-height: 1.5;
    }
    .st-control:focus {
        border-color: #c9a84c;
        box-shadow: 0 0 0 3px rgba(201, 168, 76, .13);
    }
    .st-control::placeholder { color: #94a3b8; }
    .st-check-card {
        border: 1px solid #d9e0ea;
        border-radius: 7px;
        min-height: 56px;
        padding: 10px 13px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 10px;
        background: #fff;
        cursor: pointer;
    }
    .st-check-card input { width: 18px; height: 18px; accent-color: #c9a84c; flex-shrink: 0; }
    .st-check-title { color: #172033; font-size: 12px; font-weight: 800; }
    .st-check-hint { color: #64748b; font-size: 11px; margin-top: 2px; }
    .st-subsection {
        grid-column: 1 / -1;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 8px;
        color: #94a3b8;
        font-size: 12px;
        font-weight: 700;
        padding-top: 2px;
    }
    .st-worker-box {
        grid-column: 1 / -1;
        display: none;
        border: 1px solid #f0e0a4;
        border-radius: 8px;
        background: #fffaf0;
        padding: 10px 14px;
        align-items: center;
        gap: 10px;
    }
    .st-worker-box.visible { display: flex; }
    .st-next-row {
        margin-top: 28px;
        display: flex;
        justify-content: flex-start;
    }
    .st-gold-btn {
        border: 0;
        border-radius: 7px;
        min-width: 220px;
        min-height: 40px;
        padding: 8px 22px;
        background: linear-gradient(135deg, #c9a84c, #b78f25);
        color: #fff;
        font-family: Cairo, sans-serif;
        font-size: 13px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        cursor: pointer;
        box-shadow: 0 8px 18px rgba(201, 168, 76, .18);
    }
    .st-secondary-btn {
        border: 1px solid #dbe3ee;
        border-radius: 7px;
        min-height: 40px;
        padding: 8px 22px;
        color: #64748b;
        background: #fff;
        text-decoration: none;
        font-size: 13px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    .st-footer {
        border-top: 1px solid #e5e7eb;
        background: #fff;
        padding: 14px 26px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 14px;
    }
    .st-net {
        display: none;
        margin-top: 12px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #f8fafc;
        padding: 10px 14px;
        align-items: center;
        gap: 8px;
        font-size: 13px;
    }
    @media(max-width: 768px) {
        .st-hero { align-items: stretch; flex-direction: column; }
        .st-actions { justify-content: stretch; }
        .st-top-btn { flex: 1; }
        .st-grid { grid-template-columns: 1fr; }
        .st-footer { flex-wrap: wrap; }
        .st-gold-btn, .st-secondary-btn { width: 100%; }
    }
</style>
@endpush

@section('content')
<script>
document.addEventListener('alpine:init', function () {
    Alpine.data('stForm', function () {
        return {
            tab: '{{ $errors->hasAny(["total_fees","service_fee","loss_amount","notes"]) ? "fees" : "contract" }}',
            clientModal: { open: false, name: '', phone: '', national_id: '', loading: false, error: '' },

            async submitClientST() {
                if (!this.clientModal.name.trim()) {
                    this.clientModal.error = 'الاسم مطلوب';
                    return;
                }
                this.clientModal.loading = true;
                this.clientModal.error   = '';
                try {
                    const res = await fetch('{{ route("admin.clients.quick-store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            name: this.clientModal.name,
                            phone: this.clientModal.phone,
                            national_id: this.clientModal.national_id || null
                        })
                    });
                    const data = await res.json();
                    if (data.id) {
                        const select = document.getElementById('to_client_id');
                        if (select && select._tomSelect) {
                            select._tomSelect.addOption({ value: String(data.id), text: data.name });
                            select._tomSelect.setValue(String(data.id));
                        } else if (select) {
                            select.appendChild(new Option(data.name, data.id, true, true));
                            select.value = data.id;
                        }
                        this.clientModal = { open: false, name: '', phone: '', national_id: '', loading: false, error: '' };
                    } else {
                        this.clientModal.error = data.message || 'حدث خطأ';
                    }
                } catch (e) {
                    this.clientModal.error = 'تعذّر الاتصال بالخادم';
                }
                this.clientModal.loading = false;
            },
        };
    });
});
</script>
<form method="POST" action="{{ route('admin.sponsorship-transfers.store') }}" enctype="multipart/form-data" class="w-full"
      data-initial-tab="{{ $errors->hasAny(['total_fees','service_fee','loss_amount','notes']) ? 'fees' : 'contract' }}">
    @csrf
    <input type="hidden" name="original_contract_id" id="original_contract_id" value="{{ old('original_contract_id') }}">

    <div class="st-shell" x-data="stForm()">
        <div class="st-hero">
            <div class="st-title-wrap">
                <div class="st-icon-box">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path d="M16 3h5v5M4 20L21 3M21 3l-5 1M8 21H3v-5"/>
                    </svg>
                </div>
                <div>
                    <div class="st-kicker">جديد</div>
                    <div class="st-title">إنشاء عقد نقل كفالة</div>
                </div>
            </div>

            <div class="st-actions">
                <a href="{{ route('admin.sponsorship-transfers.index') }}" class="st-top-btn ghost">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5m7 7l-7-7 7-7"/></svg>
                    العودة إلى جميع العقود
                </a>
                <a href="{{ route('admin.sponsorship-transfers.index') }}" class="st-top-btn primary">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/></svg>
                    عرض عقود نقل الكفالة
                </a>
            </div>
        </div>

        <div class="st-body">
            <div class="st-tabs">
                <button type="button" class="st-tab" :class="{ 'active': tab === 'contract' }" @click="tab = 'contract'">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/></svg>
                    بيانات العقد والكفالة
                </button>
                <button type="button" class="st-tab" :class="{ 'active': tab === 'fees' }" @click="tab = 'fees'">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                    الرسوم والملاحظات
                </button>
            </div>

            <div x-show="tab === 'contract'" x-cloak>
                <div class="st-grid">
                    <div class="st-subsection">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        بيانات العاملة
                    </div>

                    <div class="st-field">
                        <label class="st-label">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            العاملة <span class="req">*</span>
                        </label>
                        <select name="worker_id" id="worker_select" required class="st-control" onchange="onWorkerChange(this.value)">
                            <option value="">اختر عاملة...</option>
                            @if($housingWorkers->isNotEmpty())
                            <optgroup label="عاملات في السكن">
                                @foreach($housingWorkers as $w)
                                <option value="{{ $w->id }}" @selected(old('worker_id') == $w->id)>
                                    {{ $w->name }}{{ $w->nationality ? ' - '.$w->nationality->name : '' }}
                                </option>
                                @endforeach
                            </optgroup>
                            @endif
                            @if($contractWorkers->isNotEmpty())
                            <optgroup label="وصلت من عقود الاستقدام">
                                @foreach($contractWorkers as $w)
                                <option value="{{ $w->id }}" @selected(old('worker_id') == $w->id)>
                                    {{ $w->name }}{{ $w->nationality ? ' - '.$w->nationality->name : '' }}
                                </option>
                                @endforeach
                            </optgroup>
                            @endif
                        </select>
                        @error('worker_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="st-field">
                        <label class="st-label">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path d="M9 22V12h6v10"/></svg>
                            الفرع <span class="req">*</span>
                        </label>
                        @if($branches)
                        <select name="branch_id" required class="st-control">
                            <option value="">اختر الفرع...</option>
                            @foreach($branches as $b)
                            <option value="{{ $b->id }}" @selected(old('branch_id') == $b->id)>{{ $b->name }}</option>
                            @endforeach
                        </select>
                        @else
                        <input type="hidden" name="branch_id" value="{{ $branchId }}">
                        <div class="st-control" style="background:#f8fafc;color:#475569;">
                            {{ Auth::guard('admin')->user()?->branch?->name ?? 'فرعك الحالي' }}
                        </div>
                        @endif
                        @error('branch_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="st-worker-box" id="worker_info_box">
                        <div style="width:34px;height:34px;border-radius:8px;background:#fdf8e8;border:1px solid #f0e0a4;display:grid;place-items:center;color:#c9a84c;">
                            <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                        </div>
                        <div>
                            <div style="font-size:11px;color:#94a3b8;font-weight:700;">الكفيل الحالي يتم جلبه تلقائيا من بيانات العاملة</div>
                            <div style="font-size:13px;font-weight:800;color:#92400e;" id="current_sponsor_name">—</div>
                        </div>
                    </div>

                    <input type="hidden" name="needs_medical_exam" value="0">
                    <label class="st-check-card">
                        <input type="checkbox" name="needs_medical_exam" value="1" @checked(old('needs_medical_exam'))>
                        <span>
                            <span class="st-check-title">العاملة تحتاج فحص طبي</span>
                            <span class="st-check-hint">حدد هذا الخيار في حال أن العاملة تحتاج فحص طبي</span>
                        </span>
                    </label>

                    <input type="hidden" name="needs_iqama" value="0">
                    <label class="st-check-card">
                        <input type="checkbox" name="needs_iqama" value="1" @checked(old('needs_iqama'))>
                        <span>
                            <span class="st-check-title">العاملة تحتاج إقامة</span>
                            <span class="st-check-hint">حدد هذا الخيار في حال أن العاملة تحتاج إقامة</span>
                        </span>
                    </label>

                    <div class="st-subsection">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 3h5v5M4 20L21 3"/></svg>
                        بيانات الكفيل - النقل
                    </div>

                    <div class="st-field">
                        <label class="st-label">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
                            الكفيل المحيل (من) <span class="req">*</span> <span class="tag">يتم تلقائيا</span>
                        </label>
                        <select name="from_client_id" id="from_client_id" required class="st-control">
                            <option value="">اختر عميل</option>
                            @foreach($clients as $c)
                            <option value="{{ $c->id }}" @selected(old('from_client_id') == $c->id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                        @error('from_client_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="st-field">
                        <label class="st-label" style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="display:flex;align-items:center;gap:6px;">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><path d="M22 4L12 14.01l-3-3"/></svg>
                                الكفيل المستلم (إلى)
                            </span>
                            <button type="button" onclick="document.getElementById('addClientModal').style.display='flex'"
                                    style="font-size:11px;color:#2563eb;text-decoration:underline;background:none;border:none;cursor:pointer;padding:0;">+ إضافة كفيل جديد</button>
                        </label>
                        <select name="to_client_id" id="to_client_id" class="st-control">
                            <option value="">لم يحدد بعد</option>
                            @foreach($clients as $c)
                            <option value="{{ $c->id }}" @selected(old('to_client_id') == $c->id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="st-field">
                        <label class="st-label">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                            تاريخ النقل
                        </label>
                        <input type="date" name="transfer_date" value="{{ old('transfer_date') }}" class="st-control">
                    </div>

                    <div class="st-field">
                        <label class="st-label">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/></svg>
                            رقم العقد على مساند
                        </label>
                        <input type="text" name="musaned_contract_number" value="{{ old('musaned_contract_number') }}" placeholder="أدخل رقم العقد على منصة مساند" class="st-control">
                        @error('musaned_contract_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="st-field">
                        <label class="st-label">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg>
                            صورة العقد (مساند)
                        </label>
                        <input type="file" name="musaned_contract_image" accept="image/*" class="st-control" style="padding:5px 10px;">
                        @error('musaned_contract_image')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="st-field">
                        <label class="st-label">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2"/><path d="M1 10h22"/></svg>
                            حالة الدفع <span class="req">*</span>
                        </label>
                        <select name="payment_status" required class="st-control">
                            @foreach(\App\Models\SponsorshipTransfer::paymentStatuses() as $val => $label)
                            <option value="{{ $val }}" @selected(old('payment_status', 'pending') == $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="st-next-row">
                    <button type="button" class="st-gold-btn" @click="tab = 'fees'">
                        التالي - الرسوم والمصروفات
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                    </button>
                </div>
            </div>

            <div x-show="tab === 'fees'" x-cloak>
                <div class="st-grid">
                    <div class="st-subsection">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                        الرسوم المالية
                    </div>

                    <div class="st-field">
                        <label class="st-label">
                            إجمالي الرسوم <span class="req">*</span>
                        </label>
                        <input type="number" name="total_fees" id="total_fees" value="{{ old('total_fees', 0) }}" min="0" step="0.01" required class="st-control" oninput="calcNet()">
                    </div>

                    <div class="st-field">
                        <label class="st-label">
                            رسوم الخدمة <span class="req">*</span>
                        </label>
                        <input type="number" name="service_fee" value="{{ old('service_fee', 0) }}" min="0" step="0.01" required class="st-control">
                    </div>

                    <div class="st-field">
                        <label class="st-label">
                            الفقد (خسارة) <span class="req">*</span>
                        </label>
                        <input type="number" name="loss_amount" id="loss_amount" value="{{ old('loss_amount', 0) }}" min="0" step="0.01" required class="st-control" oninput="calcNet()">
                    </div>

                    <div class="st-field">
                        <div id="net_preview" class="st-net">
                            <span style="color:#64748b;font-weight:800;">صافي النتيجة:</span>
                            <span id="net_value" style="font-weight:900;"></span>
                        </div>
                    </div>

                    <div class="st-field" style="grid-column:1 / -1;">
                        <label class="st-label">
                            ملاحظات إضافية
                        </label>
                        <textarea name="notes" rows="4" class="st-control" placeholder="أي ملاحظات أو تفاصيل إضافية...">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="st-next-row">
                    <button type="button" class="st-secondary-btn" @click="tab = 'contract'">
                        السابق - بيانات العقد
                    </button>
                </div>
            </div>
        </div>

        {{-- ═══ MODAL: إضافة كفيل جديد (Vanilla JS) ══════════════════════════ --}}
        <div id="addClientModal"
             style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;background:rgba(0,0,0,.45);"
             onclick="if(event.target===this)this.style.display='none'">
            <div style="background:#fff;border-radius:16px;box-shadow:0 25px 50px rgba(0,0,0,.25);width:100%;max-width:420px;margin:0 16px;overflow:hidden;">
                <div style="background:#2563eb;padding:16px 24px;display:flex;align-items:center;justify-content:space-between;">
                    <h3 style="color:#fff;font-weight:700;font-size:15px;font-family:Cairo,sans-serif;margin:0;">إضافة كفيل جديد</h3>
                    <button type="button" onclick="document.getElementById('addClientModal').style.display='none'"
                            style="color:#bfdbfe;background:none;border:none;cursor:pointer;line-height:1;">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div style="padding:24px;display:flex;flex-direction:column;gap:16px;">
                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#475569;margin-bottom:6px;font-family:Cairo,sans-serif;">الاسم <span style="color:#ef4444">*</span></label>
                        <input id="newClientName" type="text" placeholder="اسم الكفيل"
                               style="width:100%;border:1.5px solid #e2e8f0;border-radius:10px;padding:9px 12px;font-size:13px;font-family:Cairo,sans-serif;outline:none;box-sizing:border-box;">
                    </div>
                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#475569;margin-bottom:6px;font-family:Cairo,sans-serif;">رقم الجوال</label>
                        <input id="newClientPhone" type="text" placeholder="05xxxxxxxx"
                               style="width:100%;border:1.5px solid #e2e8f0;border-radius:10px;padding:9px 12px;font-size:13px;font-family:Cairo,sans-serif;outline:none;box-sizing:border-box;">
                    </div>
                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#475569;margin-bottom:6px;font-family:Cairo,sans-serif;">رقم الهوية</label>
                        <input id="newClientNationalId" type="text" placeholder="1xxxxxxxxx"
                               style="width:100%;border:1.5px solid #e2e8f0;border-radius:10px;padding:9px 12px;font-size:13px;font-family:Cairo,sans-serif;outline:none;box-sizing:border-box;">
                    </div>
                    <div id="newClientError" style="display:none;background:#fef2f2;border:1px solid #fecaca;color:#dc2626;font-size:13px;border-radius:8px;padding:8px 12px;font-family:Cairo,sans-serif;"></div>
                </div>
                <div style="padding:0 24px 20px;display:flex;gap:10px;justify-content:flex-end;">
                    <button type="button" onclick="document.getElementById('addClientModal').style.display='none'"
                            style="padding:9px 20px;border-radius:10px;font-size:13px;font-weight:600;color:#64748b;border:1.5px solid #e2e8f0;background:#fff;cursor:pointer;font-family:Cairo,sans-serif;">إلغاء</button>
                    <button type="button" id="saveClientBtn" onclick="saveNewClient()"
                            style="padding:9px 24px;border-radius:10px;font-size:13px;font-weight:700;color:#fff;background:#2563eb;border:none;cursor:pointer;font-family:Cairo,sans-serif;">حفظ الكفيل</button>
                </div>
            </div>
        </div>

        <div class="st-footer">
            <button type="submit" class="st-gold-btn" style="min-width:170px;">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><path d="M17 21v-8H7v8M7 3v5h8"/></svg>
                إنشاء العقد
            </button>
            <a href="{{ route('admin.sponsorship-transfers.index') }}" class="st-secondary-btn">إلغاء</a>
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

    box.classList.add('visible');
    document.getElementById('current_sponsor_name').textContent = info.client_name || 'غير محدد';

    if (info.client_id && fromSel) {
        fromSel.value = info.client_id;
        if (fromSel._tomSelect) {
            fromSel._tomSelect.setValue(String(info.client_id));
        }
    }

    if (contractInput) {
        contractInput.value = info.contract_id || '';
    }
}

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

(function(){
    var workerSelect = document.getElementById('worker_select');
    if (workerSelect && workerSelect.value) onWorkerChange(workerSelect.value);
    calcNet();
})();

async function saveNewClient() {
    var name       = document.getElementById('newClientName').value.trim();
    var phone      = document.getElementById('newClientPhone').value.trim();
    var nationalId = document.getElementById('newClientNationalId').value.trim();
    var errEl      = document.getElementById('newClientError');
    var btn        = document.getElementById('saveClientBtn');

    errEl.style.display = 'none';
    if (!name) { errEl.textContent = 'الاسم مطلوب'; errEl.style.display = 'block'; return; }

    btn.disabled = true;
    btn.textContent = 'جاري الحفظ...';

    try {
        var res = await fetch('{{ route("admin.clients.quick-store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ name: name, phone: phone || null, national_id: nationalId || null })
        });
        var data = await res.json();
        if (data.id) {
            var select = document.getElementById('to_client_id');
            var ts = select && select.tomselect ? select.tomselect : (select && select._tomSelect ? select._tomSelect : null);
            if (ts) {
                ts.addOption({ value: String(data.id), text: data.name });
                ts.addItem(String(data.id));
            } else {
                select.appendChild(new Option(data.name, data.id, true, true));
                select.value = data.id;
            }
            document.getElementById('addClientModal').style.display = 'none';
            document.getElementById('newClientName').value = '';
            document.getElementById('newClientPhone').value = '';
            document.getElementById('newClientNationalId').value = '';
        } else {
            errEl.textContent = data.message || 'حدث خطأ';
            errEl.style.display = 'block';
        }
    } catch(e) {
        errEl.textContent = 'تعذّر الاتصال بالخادم';
        errEl.style.display = 'block';
    }

    btn.disabled = false;
    btn.textContent = 'حفظ الكفيل';
}
</script>
@endpush
@endsection
