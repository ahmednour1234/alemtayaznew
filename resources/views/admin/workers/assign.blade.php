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
                    <a href="{{ Storage::disk('public')->url($worker->cv_path) }}" target="_blank"
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
                        <button type="button" id="openAddClientBtn"
                                class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            إضافة عميل جديد
                        </button>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">العميل أو العميل المحتمل <span class="text-red-500">*</span></label>
                        <select name="assignee" id="assigneeSelect" required
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
                                    {{ $lead->name }}{{ $lead->phone ? ' — '.$lead->phone : '' }}
                                    (محتمل)
                                </option>
                                @endforeach
                            </optgroup>
                            @endif
                        </select>
                        @error('assignee')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        @if($clients->isEmpty() && $leads->isEmpty())
                        <p class="text-amber-600 text-xs mt-2">لا يوجد عملاء أو عملاء محتملون. يرجى إضافة عميل أولاً.</p>
                        @endif
                        @if($clients->isEmpty() && $leads->isNotEmpty())
                        <p class="text-blue-600 text-xs mt-2">اختيار عميل محتمل سيحوله تلقائياً إلى عميل مؤكد عند التعيين.</p>
                        @endif
                    </div>
                </div>

                {{-- Update worker details --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-slate-600 mb-1 pb-2 border-b border-slate-100">تحديث بيانات العاملة <span class="text-slate-400 font-normal text-xs">(اختياري — يمكن تركها فارغة)</span></h3>
                    <p class="text-xs text-slate-400 mb-4">البيانات التالية ستُحدّث إذا مُلئت</p>
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

{{-- ─── Add Client Modal ─────────────────────────────────────────────────── --}}
<div id="addClientModal"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden"
     role="dialog" aria-modal="true" aria-labelledby="addClientModalTitle">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h3 id="addClientModalTitle" class="font-bold text-slate-800">إضافة عميل جديد</h3>
            <button type="button" id="closeAddClientModal"
                    class="text-slate-400 hover:text-slate-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="px-6 py-5 space-y-4">
            <div id="addClientError"
                 class="hidden bg-red-50 text-red-700 text-sm rounded-lg px-4 py-3 border border-red-200"></div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">
                    اسم العميل <span class="text-red-500">*</span>
                </label>
                <input type="text" id="qcName" placeholder="أدخل الاسم الكامل"
                       class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">رقم الجوال</label>
                <input type="text" id="qcPhone" placeholder="05xxxxxxxx"
                       class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">رقم الهوية / الإقامة</label>
                <input type="text" id="qcNationalId" placeholder="10 أرقام"
                       class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                <p class="text-xs text-slate-400 mt-1">إدخال الهوية يحوّل العميل إلى عميل مؤكد تلقائياً</p>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex gap-3 px-6 pb-5">
            <button type="button" id="saveAddClientBtn"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2.5 rounded-lg transition disabled:opacity-60">
                <span id="saveAddClientLabel">حفظ وإضافة للقائمة</span>
                <span id="saveAddClientSpinner" class="hidden">جارٍ الحفظ...</span>
            </button>
            <button type="button" id="cancelAddClientBtn"
                    class="px-5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium py-2.5 rounded-lg transition">
                إلغاء
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ── Tom Select init ──────────────────────────────────────────────────────
    const selectEl = document.getElementById('assigneeSelect');
    let ts = null;
    if (selectEl && !selectEl._tomSelect) {
        ts = new TomSelect(selectEl, {
            placeholder: 'ابحث بالاسم أو الهاتف...',
            searchField: ['text'],
            allowEmptyOption: true,
            maxOptions: 300,
        });
    }

    // ── Modal helpers ────────────────────────────────────────────────────────
    const modal      = document.getElementById('addClientModal');
    const openBtn    = document.getElementById('openAddClientBtn');
    const closeBtn   = document.getElementById('closeAddClientModal');
    const cancelBtn  = document.getElementById('cancelAddClientBtn');
    const saveBtn    = document.getElementById('saveAddClientBtn');
    const saveLabel  = document.getElementById('saveAddClientLabel');
    const saveSpinner= document.getElementById('saveAddClientSpinner');
    const errBox     = document.getElementById('addClientError');
    const qcName     = document.getElementById('qcName');
    const qcPhone    = document.getElementById('qcPhone');
    const qcNationalId = document.getElementById('qcNationalId');

    function openModal() {
        qcName.value = '';
        qcPhone.value = '';
        qcNationalId.value = '';
        errBox.classList.add('hidden');
        errBox.textContent = '';
        modal.classList.remove('hidden');
        setTimeout(() => qcName.focus(), 80);
    }

    function closeModal() {
        modal.classList.add('hidden');
    }

    openBtn.addEventListener('click', openModal);
    closeBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);

    // Close on backdrop click
    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
    });

    // ── Save handler ─────────────────────────────────────────────────────────
    saveBtn.addEventListener('click', async function () {
        errBox.classList.add('hidden');
        const name = qcName.value.trim();
        if (!name) {
            errBox.textContent = 'اسم العميل مطلوب.';
            errBox.classList.remove('hidden');
            qcName.focus();
            return;
        }

        // Loading state
        saveBtn.disabled = true;
        saveLabel.classList.add('hidden');
        saveSpinner.classList.remove('hidden');

        try {
            const res = await fetch('{{ route('admin.clients.quick-store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    name:           name,
                    phone:          qcPhone.value.trim() || null,
                    national_id:    qcNationalId.value.trim() || null,
                    classification: 'confirmed',
                }),
            });

            const data = await res.json();

            if (!res.ok) {
                const msgs = data.errors
                    ? Object.values(data.errors).flat().join(' ')
                    : (data.message || 'حدث خطأ. حاول مجدداً.');
                errBox.textContent = msgs;
                errBox.classList.remove('hidden');
                return;
            }

            // Build the display label
            const label = data.name + (data.phone ? ' — ' + data.phone : '');

            // Add the new option to Tom Select and select it
            if (ts) {
                ts.addOption({ value: 'client:' + data.id, text: label });
                ts.setValue('client:' + data.id);
            } else {
                // Fallback: plain select
                const opt = new Option(label, 'client:' + data.id, true, true);
                selectEl.appendChild(opt);
            }

            closeModal();
        } catch (err) {
            errBox.textContent = 'حدث خطأ في الاتصال. تحقق من الإنترنت وحاول مجدداً.';
            errBox.classList.remove('hidden');
        } finally {
            saveBtn.disabled = false;
            saveLabel.classList.remove('hidden');
            saveSpinner.classList.add('hidden');
        }
    });

    // Allow Enter key in modal inputs to trigger save
    [qcName, qcPhone, qcNationalId].forEach(inp => {
        inp.addEventListener('keydown', e => { if (e.key === 'Enter') saveBtn.click(); });
    });
});
</script>
@endpush
@endsection
