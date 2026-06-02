@extends('admin.layouts.app')
@section('title', 'تعيين عاملة لعميل')
@section('content')
<div class="w-full">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.workers.index') }}" class="text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-slate-800">تعيين عاملة لعميل</h2>
    </div>

    {{-- Already-assigned warning --}}
    @if($existingClient)
    <div class="mb-5 bg-amber-50 border border-amber-300 rounded-xl px-5 py-4 flex gap-3">
        <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        <div>
            <p class="font-semibold text-amber-800 text-sm">هذه العاملة معيَّنة بالفعل</p>
            <p class="text-amber-700 text-xs mt-0.5">
                تم تعيينها مسبقاً للعميل <strong>{{ $existingClient->name }}</strong>
                @if($existingClient->phone) ({{ $existingClient->phone }}) @endif.
                إعادة التعيين ستنقلها لعميل آخر.
            </p>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Worker info card --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <h3 class="text-sm font-semibold text-slate-600 mb-4 pb-2 border-b border-slate-100">بيانات العاملة</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-slate-500">الاسم</dt>
                    <dd class="font-medium text-slate-800">{{ $worker->name ?: '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">الجنسية</dt>
                    <dd class="font-medium">{{ $worker->nationality?->name ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">المهنة</dt>
                    <dd class="font-medium">{{ $worker->profession_label }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">الخبرة</dt>
                    <dd class="font-medium">{{ $worker->experience_label }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">الحالة</dt>
                    <dd>
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold"
                              style="background: {{ $worker->status_bg }}; color: {{ $worker->status_color }}">
                            {{ $worker->status_label }}
                        </span>
                    </dd>
                </div>
                @if($worker->cv_path)
                <div class="pt-2 border-t border-slate-100">
                    <a href="{{ route('admin.workers.cv', $worker->id) }}" target="_blank"
                       class="flex items-center gap-2 text-sm text-red-600 hover:text-red-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        عرض ملف CV
                    </a>
                </div>
                @endif
            </dl>
        </div>

        {{-- Assign form --}}
        <div class="lg:col-span-2 space-y-5">
            <form action="{{ route('admin.workers.do-assign', $worker->id) }}" method="POST">
                @csrf

                {{-- Select client / lead --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
                        <h3 class="text-sm font-semibold text-slate-600">اختر العميل</h3>
                        <button type="button"
                                onclick="document.getElementById('addClientModal').style.display='flex'"
                                class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            إضافة عميل جديد
                        </button>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">العميل أو العميل المحتمل <span class="text-red-500">*</span></label>
                        <select name="assignee" id="assigneeSelect" required data-ts-ignore="1"
                                class="w-full border border-slate-300 rounded-lg text-sm @error('assignee') border-red-400 @enderror">
                            <option value="">ابحث أو اختر...</option>
                            @if($clients->isNotEmpty())
                            <optgroup label="✅ عملاء مؤكدون ({{ $clients->count() }})">
                                @foreach($clients as $client)
                                <option value="client:{{ $client->id }}"
                                        {{ old('assignee') === 'client:'.$client->id ? 'selected' : '' }}>
                                    {{ $client->name }}{{ $client->phone ? ' — '.$client->phone : '' }}
                                </option>
                                @endforeach
                            </optgroup>
                            @endif
                            @if($leads->isNotEmpty())
                            <optgroup label="🔶 عملاء محتملون ({{ $leads->count() }})">
                                @foreach($leads as $lead)
                                <option value="lead:{{ $lead->id }}"
                                        {{ old('assignee') === 'lead:'.$lead->id ? 'selected' : '' }}>
                                    {{ $lead->name }}{{ $lead->phone ? ' — '.$lead->phone : '' }} (محتمل)
                                </option>
                                @endforeach
                            </optgroup>
                            @endif
                        </select>
                        @error('assignee')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        @if($clients->isEmpty() && $leads->isEmpty())
                        <p class="text-amber-600 text-xs mt-2">لا يوجد عملاء أو عملاء محتملون. يرجى إضافة عميل أولاً.</p>
                        @endif
                        @if($leads->isNotEmpty())
                        <p class="text-blue-600 text-xs mt-2">اختيار عميل محتمل سيحوله تلقائياً إلى عميل مؤكد عند التعيين.</p>
                        @endif
                    </div>
                </div>

                {{-- Update worker details --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-slate-600 mb-1 pb-2 border-b border-slate-100">تحديث بيانات العاملة <span class="text-slate-400 font-normal text-xs">(اختياري — يمكن تركها فارغة)</span></h3>
                    <p class="text-xs text-slate-400 mb-4">البيانات التالية ستُحدَّث إذا مُلئت</p>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">الاسم</label>
                            <input type="text" name="name" value="{{ old('name', $worker->name) }}"
                                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">رقم الجواز</label>
                            <input type="text" name="passport_number" value="{{ old('passport_number', $worker->passport_number) }}"
                                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">الهاتف</label>
                            <input type="text" name="phone" value="{{ old('phone', $worker->phone) }}"
                                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">العمر</label>
                            <input type="number" name="age" value="{{ old('age', $worker->age) }}" min="18" max="60"
                                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>
                        <div class="col-span-2 lg:col-span-4">
                            <label class="block text-xs font-medium text-slate-600 mb-1">ملاحظات</label>
                            <textarea name="notes" rows="2"
                                      class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 resize-none">{{ old('notes', $worker->notes) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit" @if($clients->isEmpty() && $leads->isEmpty()) disabled @endif
                            class="bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white text-sm px-6 py-2.5 rounded-lg font-medium">
                        تعيين العاملة للعميل
                    </button>
                    <a href="{{ route('admin.workers.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm px-6 py-2.5 rounded-lg font-medium">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ─── Add Client Modal ─────────────────────────────────────────────────────── --}}
<div id="addClientModal"
     style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.45); align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:16px; box-shadow:0 20px 60px rgba(0,0,0,.25); width:100%; max-width:440px; margin:0 16px; overflow:hidden; font-family:'Cairo',sans-serif;">

        {{-- Header --}}
        <div style="display:flex; align-items:center; justify-content:space-between; padding:18px 24px; border-bottom:1px solid #f1f5f9;">
            <span style="font-weight:700; font-size:15px; color:#1e293b;">إضافة عميل جديد</span>
            <button type="button"
                    onclick="document.getElementById('addClientModal').style.display='none'"
                    style="background:none; border:none; cursor:pointer; color:#94a3b8; padding:4px;">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div style="padding:20px 24px; display:flex; flex-direction:column; gap:16px;">
            <div id="addClientError"
                 style="display:none; background:#fef2f2; color:#b91c1c; font-size:13px; border-radius:8px; padding:10px 14px; border:1px solid #fecaca;"></div>

            <div>
                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">
                    اسم العميل <span style="color:#ef4444;">*</span>
                </label>
                <input type="text" id="qcName" placeholder="أدخل الاسم الكامل"
                       style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:10px 12px; font-size:13px; font-family:'Cairo',sans-serif; outline:none; box-sizing:border-box;">
            </div>

            <div>
                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">رقم الجوال</label>
                <input type="text" id="qcPhone" placeholder="05xxxxxxxx"
                       style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:10px 12px; font-size:13px; font-family:'Cairo',sans-serif; outline:none; box-sizing:border-box;">
            </div>

            <div>
                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">رقم الهوية / الإقامة</label>
                <input type="text" id="qcNationalId" placeholder="10 أرقام"
                       style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:10px 12px; font-size:13px; font-family:'Cairo',sans-serif; outline:none; box-sizing:border-box;">
                <p style="font-size:11px; color:#94a3b8; margin-top:4px;">إدخال الهوية يحوِّل العميل إلى عميل مؤكد تلقائياً</p>
            </div>
        </div>

        {{-- Footer --}}
        <div style="display:flex; gap:12px; padding:0 24px 20px;">
            <button type="button" id="saveAddClientBtn"
                    style="flex:1; background:#2563eb; color:#fff; border:none; border-radius:8px; padding:11px; font-size:13px; font-weight:600; cursor:pointer; font-family:'Cairo',sans-serif;">
                حفظ وإضافة للقائمة
            </button>
            <button type="button"
                    onclick="document.getElementById('addClientModal').style.display='none'"
                    style="background:#f1f5f9; color:#475569; border:none; border-radius:8px; padding:11px 20px; font-size:13px; font-weight:600; cursor:pointer; font-family:'Cairo',sans-serif;">
                إلغاء
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    // ── Tom Select init for this page ──────────────────────────────────────────
    function initAssignSelect() {
        var el = document.getElementById('assigneeSelect');
        if (!el || el.tomselect) return;
        new TomSelect(el, {
            placeholder: 'ابحث بالاسم أو الهاتف...',
            searchField: ['text'],
            allowEmptyOption: true,
            maxOptions: 500,
            render: {
                no_results: function() { return '<div class="no-results">لا توجد نتائج</div>'; }
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAssignSelect);
    } else {
        initAssignSelect();
    }

    // ── Quick-add client save ──────────────────────────────────────────────────
    document.addEventListener('click', function(e) {
        if (e.target && e.target.id === 'saveAddClientBtn') {
            saveQuickClient();
        }
        // Close modal on backdrop click
        var modal = document.getElementById('addClientModal');
        if (e.target === modal) modal.style.display = 'none';
    });

    // Enter key in modal inputs
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            var active = document.activeElement;
            if (active && ['qcName','qcPhone','qcNationalId'].indexOf(active.id) !== -1) {
                saveQuickClient();
            }
        }
    });

    function saveQuickClient() {
        var errBox  = document.getElementById('addClientError');
        var nameEl  = document.getElementById('qcName');
        var phoneEl = document.getElementById('qcPhone');
        var natEl   = document.getElementById('qcNationalId');
        var btn     = document.getElementById('saveAddClientBtn');

        errBox.style.display = 'none';
        var name = nameEl.value.trim();
        if (!name) {
            errBox.textContent = 'اسم العميل مطلوب.';
            errBox.style.display = 'block';
            nameEl.focus();
            return;
        }

        btn.disabled = true;
        btn.textContent = 'جارٍ الحفظ...';

        fetch('{{ route('admin.clients.quick-store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                name:           name,
                phone:          phoneEl.value.trim() || null,
                national_id:    natEl.value.trim() || null,
                classification: 'confirmed',
            }),
        })
        .then(function(res) { return res.json().then(function(d) { return { ok: res.ok, data: d }; }); })
        .then(function(result) {
            if (!result.ok) {
                var msgs = result.data.errors
                    ? Object.values(result.data.errors).flat().join(' ')
                    : (result.data.message || 'حدث خطأ. حاول مجدداً.');
                errBox.textContent = msgs;
                errBox.style.display = 'block';
                return;
            }
            var d = result.data;
            var label = d.name + (d.phone ? ' — ' + d.phone : '');
            var selectEl = document.getElementById('assigneeSelect');
            var ts = selectEl && selectEl.tomselect;
            if (ts) {
                ts.addOption({ value: 'client:' + d.id, text: label });
                ts.setValue('client:' + d.id);
            } else {
                var opt = document.createElement('option');
                opt.value = 'client:' + d.id;
                opt.text  = label;
                opt.selected = true;
                selectEl.appendChild(opt);
            }
            document.getElementById('addClientModal').style.display = 'none';
            // Clear modal inputs for next use
            nameEl.value = '';
            phoneEl.value = '';
            natEl.value = '';
        })
        .catch(function() {
            errBox.textContent = 'حدث خطأ في الاتصال. حاول مجدداً.';
            errBox.style.display = 'block';
        })
        .finally(function() {
            btn.disabled = false;
            btn.textContent = 'حفظ وإضافة للقائمة';
        });
    }
})();
</script>
@endpush
@endsection
