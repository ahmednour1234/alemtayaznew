@extends('admin.layouts.app')
@section('title', 'عقود الاستقدام')
@push('styles')
<style>
@media print {
    body * { visibility: hidden !important; }
    #print-area, #print-area * { visibility: visible !important; }
    #print-area { position: fixed; inset: 0; padding: 16px; }
    .no-print { display: none !important; }
}
</style>
@endpush
@section('content')
<div class="w-full space-y-5" x-data="{
    importModal: false,
    statusModal: false,
    selected: [],
    get hasSelection() { return this.selected.length > 0; },
    toggleAll(ids) {
        if (this.selected.length === ids.length) { this.selected = []; }
        else { this.selected = ids.map(String); }
    },
    isAllSelected(ids) { return ids.length > 0 && this.selected.length === ids.length; },
    printSelected() {
        document.querySelectorAll('.contract-row').forEach(row => {
            row.classList.toggle('print-hidden', !this.selected.includes(row.dataset.id));
        });
        document.querySelectorAll('.print-hidden').forEach(el => el.style.display = 'none');
        window.print();
        document.querySelectorAll('.contract-row').forEach(row => row.style.display = '');
    },
    printAll() { window.print(); }
}">

    {{-- ===== Import Modal ===== --}}
    <div x-show="importModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @keydown.escape.window="importModal = false">
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="importModal = false"></div>
        {{-- Dialog --}}
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10">
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-xl bg-green-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">استيراد العقود من Excel</h3>
                </div>
                <button @click="importModal = false" class="text-slate-400 hover:text-slate-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            {{-- Body --}}
            <form action="{{ route('admin.contracts.import') }}" method="POST" enctype="multipart/form-data" id="import-modal-form">
                @csrf
                <div class="px-6 py-5 space-y-4">
                    {{-- Instructions --}}
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-3 text-xs text-blue-700 space-y-1 leading-relaxed">
                        <p class="font-semibold text-blue-800">تعليمات الاستيراد:</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            <li>يجب أن يكون الملف بصيغة <strong>.xlsx</strong> أو <strong>.xls</strong></li>
                            <li>استخدم النموذج الرسمي لضمان قبول البيانات</li>
                            <li>كود الفرع يجب أن يكون موجوداً في النظام</li>
                            <li>الصف الأول عناوين، الصف الثاني شرح — تبدأ البيانات من الصف الثالث</li>
                        </ul>
                    </div>
                    {{-- Template download --}}
                    <a href="{{ route('admin.contracts.template') }}"
                       class="flex items-center gap-2 w-full justify-center border border-dashed border-blue-300 bg-blue-50 hover:bg-blue-100 text-blue-700 text-sm font-medium py-2.5 rounded-xl transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                        تحميل النموذج الرسمي
                    </a>
                    {{-- File drop zone --}}
                    <div x-data="{ fileName: '' }">
                        <label for="import-file-modal"
                               class="flex flex-col items-center justify-center gap-2 w-full border-2 border-dashed border-slate-200 hover:border-green-400 bg-slate-50 hover:bg-green-50 rounded-xl py-8 cursor-pointer transition">
                            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 17v-2m3 2v-4m3 4v-6M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/></svg>
                            <span class="text-sm text-slate-500" x-text="fileName || 'انقر لاختيار الملف أو اسحب وأفلت'"></span>
                            <span class="text-xs text-slate-400">.xlsx / .xls / .csv</span>
                        </label>
                        <input type="file" name="file" id="import-file-modal" accept=".xlsx,.xls,.csv"
                               class="hidden"
                               @change="fileName = $event.target.files[0]?.name ?? ''">
                    </div>
                </div>
                {{-- Footer --}}
                <div class="flex gap-3 px-6 pb-5">
                    <button type="submit"
                            class="flex-1 flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-2.5 rounded-xl shadow transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        رفع واستيراد
                    </button>
                    <button type="button" @click="importModal = false"
                            class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium py-2.5 rounded-xl transition">
                        إلغاء
                    </button>
                </div>
            </form>
        </div>
    </div>
    {{-- ===== End Import Modal ===== --}}

    {{-- ===== Bulk Status Update Modal ===== --}}
    <div x-show="statusModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @keydown.escape.window="statusModal = false">
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="statusModal = false"></div>
        {{-- Dialog --}}
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10">
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">تحديث حالات العقود من Excel</h3>
                </div>
                <button @click="statusModal = false" class="text-slate-400 hover:text-slate-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            {{-- Body --}}
            <form action="{{ route('admin.contracts.status-import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="px-6 py-5 space-y-4">
                    {{-- Instructions --}}
                    <div class="bg-amber-50 border border-amber-100 rounded-xl p-3 text-xs text-amber-800 space-y-1 leading-relaxed">
                        <p class="font-semibold text-amber-900">تعليمات التحديث:</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            <li>يتم البحث عن العقد بواسطة <strong>رقم التأشيرة</strong></li>
                            <li><strong>رقم الحالة</strong> من 1 إلى 15 (موضحة في النموذج)</li>
                            <li><strong>تاريخ الحالة</strong> بصيغة YYYY-MM-DD — إن تُرك فارغاً يُستخدم تاريخ اليوم</li>
                            <li>الصف الأول عناوين، الصف الثاني شرح — تبدأ البيانات من الصف الثالث</li>
                            <li>يتم تسجيل التحديث في سجل الحالات وإشعار القسم المختص</li>
                        </ul>
                    </div>
                    {{-- Template download --}}
                    <a href="{{ route('admin.contracts.status-template') }}"
                       class="flex items-center gap-2 w-full justify-center border border-dashed border-amber-300 bg-amber-50 hover:bg-amber-100 text-amber-700 text-sm font-medium py-2.5 rounded-xl transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                        تحميل نموذج تحديث الحالات
                    </a>
                    {{-- File drop zone --}}
                    <div x-data="{ fileName: '' }">
                        <label for="status-file-modal"
                               class="flex flex-col items-center justify-center gap-2 w-full border-2 border-dashed border-slate-200 hover:border-amber-400 bg-slate-50 hover:bg-amber-50 rounded-xl py-8 cursor-pointer transition">
                            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 17v-2m3 2v-4m3 4v-6M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/></svg>
                            <span class="text-sm text-slate-500" x-text="fileName || 'انقر لاختيار الملف أو اسحب وأفلت'"></span>
                            <span class="text-xs text-slate-400">.xlsx / .xls / .csv</span>
                        </label>
                        <input type="file" name="file" id="status-file-modal" accept=".xlsx,.xls,.csv"
                               class="hidden"
                               @change="fileName = $event.target.files[0]?.name ?? ''">
                    </div>
                </div>
                {{-- Footer --}}
                <div class="flex gap-3 px-6 pb-5">
                    <button type="submit"
                            class="flex-1 flex items-center justify-center gap-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium py-2.5 rounded-xl shadow transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        رفع وتحديث الحالات
                    </button>
                    <button type="button" @click="statusModal = false"
                            class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium py-2.5 rounded-xl transition">
                        إلغاء
                    </button>
                </div>
            </form>
        </div>
    </div>
    {{-- ===== End Bulk Status Update Modal ===== --}}

    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-800">عقود الاستقدام</h2>
            <p class="text-slate-400 text-xs mt-0.5">إجمالي: {{ $contracts->total() }} عقد</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <button @click="importModal = true"
                    class="flex items-center gap-1.5 text-sm bg-white border border-slate-200 text-slate-600 hover:text-green-700 hover:border-green-300 px-4 py-2 rounded-xl shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                استيراد
            </button>
            <a href="{{ route('admin.contracts.template') }}"
               class="flex items-center gap-1.5 text-sm bg-white border border-slate-200 text-slate-600 hover:text-blue-700 hover:border-blue-300 px-4 py-2 rounded-xl shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                نموذج
            </a>
            <button @click="statusModal = true"
                    class="flex items-center gap-1.5 text-sm bg-white border border-slate-200 text-slate-600 hover:text-amber-700 hover:border-amber-300 px-4 py-2 rounded-xl shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                تحديث الحالات
            </button>
            <a href="{{ route('admin.contracts.export', request()->query()) }}"
               class="flex items-center gap-1.5 text-sm bg-white border border-slate-200 text-slate-600 hover:text-emerald-700 hover:border-emerald-300 px-4 py-2 rounded-xl shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                تصدير Excel
            </a>
            <a href="{{ route('admin.contracts.trashed') }}"
               class="flex items-center gap-1.5 text-sm bg-white border border-slate-200 text-red-500 hover:bg-red-50 px-4 py-2 rounded-xl shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                محذوف
            </a>
@php
    $_u    = Auth::guard('admin')->user();
    $_dept = $_u->department;
    // Can create = has the permission (or is super-admin) AND not in accounts/coordination
    $canCreateContract = ($_u->isSuperAdmin() || $_u->hasPermission('contracts.create'))
                      && ! in_array($_dept, ['accounts', 'accountant', 'coordination']);
    // Can edit = has contracts.edit permission (or super-admin)
    $canEditContract   = $_u->isSuperAdmin() || $_u->hasPermission('contracts.edit');
    // Can delete = has contracts.delete permission (or super-admin) AND not accounts/coordination
    $canDeleteContract = ($_u->isSuperAdmin() || $_u->hasPermission('contracts.delete'))
                      && ! in_array($_dept, ['accounts', 'accountant', 'coordination']);
    // Quick-forward: only dept users (not bosses) who are at a forwardable stage
    $fwdNextMap = [
        'customer_service' => ['stage' => 'customer_service', 'next' => 'accounts',     'label' => 'إرسال للحسابات'],
        'accounts'         => ['stage' => 'accounts',         'next' => 'coordination', 'label' => 'إرسال للتنسيق'],
        'accountant'       => ['stage' => 'accounts',         'next' => 'coordination', 'label' => 'إرسال للتنسيق'],
    ];
    $myForward = (!$_u->isSuperAdmin() && !in_array($_dept, ['branch_manager', 'chairman', 'coordination']))
                 ? ($fwdNextMap[$_dept] ?? null)
                 : null;
@endphp
@if($canCreateContract)
            <a href="{{ route('admin.contracts.create') }}"
               class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2.5 rounded-xl shadow transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                عقد جديد
            </a>
@endif
        </div>
    </div>

    {{-- Department tabs --}}
    <div class="flex gap-2 flex-wrap">
        <a href="{{ route('admin.contracts.index', array_merge(request()->query(), ['department' => ''])) }}"
           class="px-4 py-2 rounded-xl text-sm font-medium transition {{ !request('department') ? 'bg-blue-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
            الكل
        </a>
        @foreach($departments as $key => $label)
        <a href="{{ route('admin.contracts.index', array_merge(request()->query(), ['department' => $key])) }}"
           class="px-4 py-2 rounded-xl text-sm font-medium transition {{ request('department') === $key ? 'bg-blue-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    {{-- Filters --}}
    <form method="GET" class="bg-white rounded-xl border border-slate-100 shadow-sm p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-3">
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="بحث برقم العقد / مساند / التأشيرة / الجواز / العميل / اسم العاملة..."
                   class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            <select name="status" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">— كل الحالات —</option>
                @foreach($statuses as $num => $st)
                <option value="{{ $num }}" {{ ($filters['status'] ?? '') == $num ? 'selected' : '' }}>{{ $st['label'] }}</option>
                @endforeach
            </select>
            <select name="payment_status" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">— حالة الدفع —</option>
                <option value="pending" {{ ($filters['payment_status'] ?? '') === 'pending' ? 'selected' : '' }}>معلق</option>
                <option value="partial" {{ ($filters['payment_status'] ?? '') === 'partial' ? 'selected' : '' }}>جزئي</option>
                <option value="full"    {{ ($filters['payment_status'] ?? '') === 'full' ? 'selected' : '' }}>كامل</option>
            </select>
            <select name="origin_nationality_id" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">— الجنسية —</option>
                @foreach($nationalities as $nat)
                <option value="{{ $nat->id }}" {{ ($filters['origin_nationality_id'] ?? '') == $nat->id ? 'selected' : '' }}>{{ $nat->name }}</option>
                @endforeach
            </select>
            @auth('admin')
            @if(Auth::guard('admin')->user()->isSuperAdmin())
            <select name="branch_id" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">— كل الفروع —</option>
                @foreach($branches as $br)
                <option value="{{ $br->id }}" {{ ($filters['branch_id'] ?? '') == $br->id ? 'selected' : '' }}>{{ $br->name }}</option>
                @endforeach
            </select>
            @endif
            @endauth
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg">بحث</button>
                <a href="{{ route('admin.contracts.index') }}" class="flex-1 text-center bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm px-4 py-2 rounded-lg">مسح</a>
            </div>
        </div>
    </form>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3">{{ session('error') }}</div>
    @endif

    {{-- Bulk Actions Bar (visible when items selected) --}}
    <div x-show="hasSelection" x-cloak x-transition
         class="no-print flex items-center justify-between bg-blue-50 border border-blue-200 rounded-xl px-4 py-3">
        <span class="text-sm text-blue-700 font-medium">
            تم تحديد <span x-text="selected.length" class="font-bold"></span> عقد
        </span>
        <div class="flex items-center gap-2">
            {{-- Print selected --}}
            <button @click="printSelected()"
                    class="flex items-center gap-1.5 text-sm bg-white border border-slate-200 text-slate-600 hover:text-blue-700 hover:border-blue-300 px-3 py-1.5 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2m-2 4H8v-6h8v6z"/></svg>
                طباعة المحدد
            </button>
            {{-- Deselect --}}
            <button @click="selected = []"
                    class="text-sm text-slate-500 hover:text-slate-700 px-3 py-1.5 rounded-lg border border-slate-200 bg-white transition">
                إلغاء التحديد
            </button>
            {{-- Bulk delete --}}
            @if($canDeleteContract)
            <form action="{{ route('admin.contracts.bulk-delete') }}" method="POST"
                  @submit.prevent="if(confirm('حذف ' + selected.length + ' عقد؟')) { $el.submit(); }">
                @csrf
                <template x-for="id in selected" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>
                <button type="submit"
                        class="flex items-center gap-1.5 text-sm bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    حذف المحدد (<span x-text="selected.length"></span>)
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- Table --}}
    <div id="print-area" class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-x-auto">
        {{-- Print header (only visible on print) --}}
        <div class="hidden print:block p-4 border-b border-slate-200 text-center">
            <h2 class="text-lg font-bold">قائمة عقود الاستقدام</h2>
            <p class="text-xs text-slate-500">{{ now()->format('Y/m/d H:i') }}</p>
        </div>
        <table class="w-full text-sm text-right">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-3 py-3 no-print">
                        @php $allIds = $contracts->pluck('id')->map(fn($id) => (string)$id)->toArray(); @endphp
                        <input type="checkbox"
                               class="w-4 h-4 rounded accent-blue-600 cursor-pointer"
                               :checked="isAllSelected({{ json_encode($allIds) }})"
                               @change="toggleAll({{ json_encode($allIds) }})">
                    </th>
                    <th class="px-4 py-3 font-semibold text-slate-600">رقم العقد</th>
                    <th class="px-4 py-3 font-semibold text-slate-600">العميل</th>
                    <th class="px-4 py-3 font-semibold text-slate-600">الفرع</th>
                    <th class="px-4 py-3 font-semibold text-slate-600">العاملة</th>
                    <th class="px-4 py-3 font-semibold text-slate-600">الجنسية</th>
                    <th class="px-4 py-3 font-semibold text-slate-600">الحالة</th>
                    <th class="px-4 py-3 font-semibold text-slate-600">القسم</th>
                    <th class="px-4 py-3 font-semibold text-slate-600">الدفع</th>
                    <th class="px-4 py-3 font-semibold text-slate-600">تاريخ الطلب</th>
                    <th class="px-4 py-3 font-semibold text-slate-600">تاريخ الوصول</th>
                    <th class="px-4 py-3 font-semibold text-slate-600">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($contracts as $c)
                <tr class="hover:bg-slate-50 transition contract-row" data-id="{{ $c->id }}">
                    <td class="px-3 py-3 no-print">
                        <input type="checkbox" class="w-4 h-4 rounded accent-blue-600 cursor-pointer"
                               value="{{ $c->id }}"
                               x-model="selected">
                    </td>                    <td class="px-4 py-3">
                        <a href="{{ route('admin.contracts.show', $c->id) }}" class="font-mono text-blue-600 hover:underline">{{ $c->contract_number }}</a>
                        @if($c->musaned_number)
                        <div class="text-xs text-slate-400">مساند: {{ $c->musaned_number }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3 font-medium text-slate-700">{{ $c->client->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ $c->branch->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ $c->worker->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-slate-500 text-xs">{{ $c->originNationality->name ?? $c->worker->nationality->name ?? '—' }}</td>
                    <td class="px-4 py-3">
                        @php
                            $statusColors = [13 => 'bg-green-100 text-green-700', 9 => 'bg-red-100 text-red-700', 15 => 'bg-red-100 text-red-700'];
                            $color = $statusColors[$c->current_status] ?? 'bg-blue-100 text-blue-700';
                        @endphp
                        <span class="inline-block px-2.5 py-1 rounded-full text-xs font-medium {{ $color }}">
                            {{ $c->status_label }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-slate-500 text-xs">{{ $c->department_label }}</td>
                    <td class="px-4 py-3">
                        @php
                            $payColors = ['full' => 'bg-green-100 text-green-700', 'partial' => 'bg-yellow-100 text-yellow-700', 'pending' => 'bg-slate-100 text-slate-500'];
                        @endphp
                        <span class="inline-block px-2.5 py-1 rounded-full text-xs {{ $payColors[$c->payment_status] ?? '' }}">{{ $c->payment_label }}</span>
                    </td>
                    <td class="px-4 py-3 text-slate-400 text-xs">{{ $c->request_date?->format('Y/m/d') }}</td>
                    <td class="px-4 py-3 text-slate-400 text-xs">{{ $c->arrival_date?->format('Y/m/d') ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2 items-center flex-wrap">
                            <a href="{{ route('admin.contracts.show', $c->id) }}" class="text-blue-600 hover:text-blue-800 text-xs">عرض</a>
                            @if($canEditContract)
                            <a href="{{ route('admin.contracts.edit', $c->id) }}" class="text-slate-500 hover:text-slate-700 text-xs">تعديل</a>
                            @endif
                            @if($myForward && $c->current_department === $myForward['stage'])
                            <form action="{{ route('admin.contracts.forward', $c->id) }}" method="POST"
                                  onsubmit="return confirm('توجيه العقد للقسم التالي؟')">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center gap-1 text-xs text-emerald-700 hover:text-emerald-900 font-medium">
                                    {{ $myForward['label'] }}
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                </button>
                            </form>
                            @endif
                            @if($canDeleteContract)
                            <form action="{{ route('admin.contracts.destroy', $c->id) }}" method="POST" onsubmit="return confirm('حذف العقد؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs">حذف</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="11" class="text-center py-10 text-slate-400">لا توجد عقود</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Footer: print-all + pagination --}}
    <div class="no-print flex items-center justify-between flex-wrap gap-3">
        <button @click="printAll()"
                class="flex items-center gap-1.5 text-sm bg-white border border-slate-200 text-slate-600 hover:text-blue-700 hover:border-blue-300 px-4 py-2 rounded-xl shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2m-2 4H8v-6h8v6z"/></svg>
            طباعة الصفحة الحالية
        </button>
        <div>{{ $contracts->links() }}</div>
    </div>
</div>
@endsection
