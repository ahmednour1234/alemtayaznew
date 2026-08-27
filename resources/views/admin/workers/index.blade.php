@extends('admin.layouts.app')
@section('title', __('workers.title'))
@section('content')
@php
    // المستخدم الحالي — يُحسب مرة واحدة ويُستخدم في صلاحية فكّ التعيين لكل صف
    $me = Auth::guard('admin')->user();

    // معرّفات العاملات التي لديها CV — فقط هذه يمكن إرسالها عبر واتساب
    $cvIds = $workers->filter(fn($w) => (bool) $w->cv_path)->pluck('id')->values();

    // نصوص تُستخدم داخل JavaScript — تُمرَّر مترجمة مع علامة :count كما هي
    $js = [
        'wa_ready'    => __('workers.whatsapp.ready',       ['count' => ':count']),
        'wa_all'      => __('workers.whatsapp.all_results', ['count' => ':count']),
        'sel_page'    => __('workers.selection.page_only',  ['count' => ':count']),
        'sel_all'     => __('workers.selection.select_all', ['count' => ':count']),
        'sel_matched' => __('workers.selection.all_matched',['count' => ':count']),
        'no_cv_warn'  => __('workers.whatsapp.no_cv_warning',['count' => ':count']),
        'del_bulk'    => __('workers.delete.confirm_bulk',  ['count' => ':count']),
        'send'        => __('common.actions.send'),
        'preparing'   => __('workers.whatsapp.preparing'),
        'no_cv_msg'   => __('workers.whatsapp.no_cv_msg'),
        'fetch_fail'  => __('workers.whatsapp.fetch_failed'),
        'msg_header'  => __('workers.whatsapp.msg_header'),
        'batch_conf'  => __('workers.whatsapp.batch_confirm', ['count' => ':count', 'batches' => ':batches']),
        'batch_next'  => __('workers.whatsapp.batch_next', ['current' => ':current', 'total' => ':total', 'next' => ':next']),
        'limit_conf'  => __('workers.whatsapp.limit_confirm', ['limit' => ':limit', 'total' => ':total']),
        'nationality' => __('common.fields.nationality'),
        'worker'      => __('common.fields.worker'),
    ];
@endphp

{{-- نصوص الترجمة لـ JavaScript — قبل Alpine حتى تكون جاهزة عند التهيئة --}}
<script>const LANG = @json($js);</script>
<div class="w-full" x-data="{
        selected: [],
        waPhone: '',
        showWa: false,
        sending: false,
        selectAllMatching: false,   // true = كل نتائج الفلتر عبر كل الصفحات
        totalMatching: {{ $workers->total() }},
        pageIds: {{ $workers->pluck('id')->toJson() }},
        cvIds: {{ $cvIds->toJson() }},
        get selectedWithCv() { return this.selected.filter(id => this.cvIds.includes(id)); },
        get selectedNoCv()   { return this.selected.filter(id => !this.cvIds.includes(id)); },
        // العدد المعروض في الأزرار: كل النتائج أو المحدد يدوياً
        get actionCount()    { return this.selectAllMatching ? this.totalMatching : this.selected.length; },
        get hasSelection()   { return this.selectAllMatching || this.selected.length > 0; },
        // إلغاء وضع «كل النتائج» فور تعديل التحديد يدوياً
        clearAllMatching() { this.selectAllMatching = false; },
        selectNone() { this.selected = []; this.selectAllMatching = false; },

        // استبدال :count وغيره في النصوص المترجمة
        t(key, params = {}) {
            let s = LANG[key] || key;
            for (const [k, v] of Object.entries(params)) s = s.replaceAll(':' + k, v);
            return s;
        },

        // الإرسال — يفتح واتساب مباشرة بلا تحميل أي صفحة
        async sendSelected() {
            if (!this.waPhone || this.sending) return;

            const PER   = {{ \App\Services\WorkerService::WHATSAPP_MAX_PER_MESSAGE }};
            const TOTAL = {{ \App\Services\WorkerService::WHATSAPP_MAX_TOTAL }};

            // تأكيد عدد الرسائل التي ستُفتح
            const confirmBatches = (n, available) => {
                if (available > n) {
                    if (!confirm(this.t('limit_conf', {limit: n, total: available}))) return false;
                }
                const batches = Math.ceil(n / PER);
                if (batches > 1) {
                    return confirm(this.t('batch_conf', {count: n, batches: batches}));
                }
                return true;
            };

            // الحالة العادية: البيانات موجودة في الصفحة → إرسال فوري
            if (!this.selectAllMatching) {
                const ids = this.selectedWithCv;
                if (!ids.length) { alert(LANG.no_cv_msg); return; }
                const take = Math.min(ids.length, TOTAL);
                if (!confirmBatches(take, ids.length)) return;
                sendWhatsapp(ids.slice(0, take), this.waPhone);
                return;
            }

            // وضع «كل النتائج»: نجلب البيانات من السيرفر
            this.sending = true;
            try {
                const params = new URLSearchParams(window.location.search);
                params.set('has_cv', '1');
                params.set('with_cv_data', '1');
                const res = await fetch('{{ route('admin.workers.matching-ids') }}?' + params.toString(),
                                        { headers: { 'Accept': 'application/json' } });
                if (!res.ok) throw new Error('fetch failed');
                const data = await res.json();

                if (!data.workers.length) { alert(LANG.no_cv_msg); return; }
                if (!confirmBatches(data.workers.length, data.count)) return;

                openWhatsapp(data.workers, this.waPhone);
            } catch (e) {
                alert(LANG.fetch_fail);
            } finally {
                this.sending = false;
            }
        }
     }">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-5">
        <h2 class="text-xl font-bold text-slate-800">{{ __('workers.title') }}</h2>
        <div class="flex gap-2">
            <a href="{{ route('admin.workers.bulk') }}"
               class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm px-4 py-2 rounded-lg font-medium flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                {{ __('workers.bulk_upload') }}
            </a>
            <a href="{{ route('admin.workers.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg font-medium flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ __('workers.add') }}
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" class="bg-white rounded-xl shadow-sm p-4 mb-5">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('common.fields.nationality') }}</label>
                <select name="nationality_id" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">{{ __('workers.all_nats') }}</option>
                    @foreach($nationalities as $nat)
                    <option value="{{ $nat->id }}" {{ ($filters['nationality_id'] ?? '') == $nat->id ? 'selected' : '' }}>{{ $nat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('common.fields.status') }}</label>
                <select name="status" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">{{ __('workers.all_statuses') }}</option>
                    @foreach(__('workers.statuses') as $key => $label)
                    <option value="{{ $key }}" {{ ($filters['status'] ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('common.fields.profession') }}</label>
                <select name="profession" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">{{ __('workers.all_profs') }}</option>
                    @foreach($professions as $key => $label)
                    <option value="{{ $key }}" {{ ($filters['profession'] ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('common.actions.search') }}</label>
                <div class="flex gap-2">
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="{{ __('workers.search_ph') }}"
                           class="flex-1 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <button type="submit" class="bg-blue-600 text-white px-3 py-2 rounded-lg text-sm">{{ __('common.actions.search') }}</button>
                    <a href="{{ route('admin.workers.index') }}" class="bg-slate-100 text-slate-600 px-3 py-2 rounded-lg text-sm">{{ __('common.actions.clear') }}</a>
                </div>
            </div>
        </div>
    </form>

    {{-- WhatsApp panel (shows when items selected) --}}
    <div x-show="hasSelection" x-cloak
         class="bg-green-50 border border-green-200 rounded-xl p-4 mb-5">
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                <span class="text-sm font-semibold text-green-700">{{ __('workers.whatsapp.send') }}</span>
                <span class="bg-green-600 text-white text-xs px-2 py-0.5 rounded-full"
                      x-text="selectAllMatching ? t('wa_all', {count: totalMatching}) : t('wa_ready', {count: selectedWithCv.length})"></span>
            </div>
            <form @submit.prevent="sendSelected()" class="flex gap-2 flex-1 min-w-0">
                <input type="tel" x-model="waPhone" placeholder="{{ __('workers.whatsapp.phone_ph') }}"
                       class="flex-1 border border-green-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 min-w-0" required>
                <button type="submit" :disabled="sending || (!selectAllMatching && selectedWithCv.length === 0)"
                        class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-lg font-medium whitespace-nowrap disabled:bg-slate-300 disabled:cursor-not-allowed">
                    <span x-text="sending ? LANG.preparing : LANG.send"></span>
                </button>
            </form>

            {{-- حذف جماعي --}}
            <form method="POST" action="{{ route('admin.workers.bulk-destroy') }}" class="inline"
                  @submit="return confirm(t('del_bulk', {count: actionCount}))">
                @csrf @method('DELETE')
                {{-- في وضع «كل النتائج» نرسل الفلاتر بدل آلاف المعرّفات --}}
                <template x-if="selectAllMatching">
                    <div>
                        <input type="hidden" name="select_all" value="1">
                        <input type="hidden" name="nationality_id" value="{{ $filters['nationality_id'] ?? '' }}">
                        <input type="hidden" name="status"         value="{{ $filters['status'] ?? '' }}">
                        <input type="hidden" name="profession"     value="{{ $filters['profession'] ?? '' }}">
                        <input type="hidden" name="search"         value="{{ $filters['search'] ?? '' }}">
                    </div>
                </template>
                <template x-if="!selectAllMatching">
                    <div>
                        <template x-for="id in selected" :key="id">
                            <input type="hidden" name="worker_ids[]" :value="id">
                        </template>
                    </div>
                </template>
                <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white text-sm px-4 py-2 rounded-lg font-medium whitespace-nowrap">
                    {{ __('common.actions.bulk_delete') }} (<span x-text="actionCount"></span>)
                </button>
            </form>

            <button @click="selectNone()" class="text-sm text-slate-500 hover:text-slate-700">{{ __('common.actions.deselect_all') }}</button>
        </div>

        {{-- تحديد كل النتائج عبر كل الصفحات --}}
        <div x-show="selected.length === pageIds.length && totalMatching > pageIds.length" x-cloak
             class="mt-3 pt-3 border-t border-green-200 text-sm">
            <template x-if="!selectAllMatching">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-slate-600" x-text="t('sel_page', {count: pageIds.length})"></span>
                    <button type="button" @click="selectAllMatching = true"
                            class="text-blue-600 hover:text-blue-800 font-semibold underline"
                            x-text="t('sel_all', {count: totalMatching})"></button>
                </div>
            </template>
            <template x-if="selectAllMatching">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-green-800 font-semibold" x-text="t('sel_matched', {count: totalMatching})"></span>
                    <button type="button" @click="selectAllMatching = false"
                            class="text-slate-500 hover:text-slate-700 underline">
                        {{ __('workers.selection.page_enough') }}
                    </button>
                </div>
            </template>
        </div>

        {{-- تنبيه عند تحديد عاملات بلا CV --}}
        <p x-show="!selectAllMatching && selectedNoCv.length > 0" x-cloak class="text-xs text-amber-700 mt-2"
           x-text="t('no_cv_warn', {count: selectedNoCv.length})"></p>
        <p x-show="selectAllMatching" x-cloak class="text-xs text-amber-700 mt-2">
            {{ __('workers.whatsapp.only_with_cv') }}
        </p>
    </div>

    {{-- Workers Table --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        @if($workers->count())
        <table class="data-table w-full">
            <thead>
                <tr>
                    <th class="w-8">
                        <input type="checkbox" class="rounded"
                               :checked="selected.length === pageIds.length && pageIds.length > 0"
                               @change="selectAllMatching = false;
                                        selected = $event.target.checked ? [...pageIds] : []">
                    </th>
                    <th>{{ __('workers.worker') }}</th>
                    <th>{{ __('common.fields.nationality') }}</th>
                    <th>{{ __('common.fields.profession') }}</th>
                    <th>{{ __('common.fields.experience') }}</th>
                    <th>{{ __('common.fields.status') }}</th>
                    <th>{{ __('common.fields.client') }}</th>
                    <th>{{ __('workers.reserved_by') }}</th>
                    <th>{{ __('workers.cv') }}</th>
                    <th>{{ __('common.fields.actions') }}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($workers as $w)
            <tr>
                <td>
                    <input type="checkbox" class="rounded" :value="{{ $w->id }}"
                           x-model.number="selected" @change="clearAllMatching()">
                </td>
                <td>
                    <div class="font-medium text-slate-800 text-sm">{{ $w->name ?: '—' }}</div>
                    @if($w->passport_number)
                    <div class="text-xs text-slate-400">{{ __('common.fields.passport_number') }}: {{ $w->passport_number }}</div>
                    @endif
                </td>
                <td>
                    @if($w->nationality)
                    <span style="background:#e0f2fe;color:#0369a1;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;display:inline-block;white-space:nowrap;">{{ $w->nationality->name }}</span>
                    @else
                    <span class="text-slate-300 text-xs">—</span>
                    @endif
                </td>
                <td class="text-sm text-slate-600">{{ $w->profession_label }}</td>
                <td class="text-sm text-slate-600">{{ $w->experience_label }}</td>
                <td>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold"
                          style="background: {{ $w->status_bg }}; color: {{ $w->status_color }};">
                        {{ $w->status_label }}
                    </span>
                </td>
                <td class="text-sm text-slate-600">
                    {{ $w->effectiveClient()?->name ?? '—' }}
                </td>
                <td class="text-sm text-slate-600">
                    @if($w->assignedBy)
                        <span class="block">{{ $w->assignedBy->name }}</span>
                        @if($w->assigned_at)
                        <span class="text-xs text-slate-400">{{ $w->assigned_at->format('Y-m-d H:i') }}</span>
                        @endif
                    @else
                        —
                    @endif
                </td>
                <td>
                    @if($w->cv_path)
                    <a href="{{ route('admin.workers.cv', $w->id) }}" target="_blank"
                       class="inline-flex items-center gap-1 text-xs text-red-600 hover:text-red-800 bg-red-50 px-2 py-1 rounded-lg">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        PDF
                    </a>
                    @else
                    <span class="text-xs text-slate-400">{{ __('workers.no_cv') }}</span>
                    @endif
                </td>
                <td>
                    <div class="flex items-center gap-1">
                        <a href="{{ route('admin.workers.show', $w->id) }}"
                           class="text-xs text-slate-500 hover:text-blue-600 bg-slate-100 hover:bg-blue-50 px-2 py-1 rounded-lg">{{ __('common.actions.view') }}</a>
                        <a href="{{ route('admin.workers.edit', $w->id) }}"
                           class="text-xs text-slate-500 hover:text-emerald-600 bg-slate-100 hover:bg-emerald-50 px-2 py-1 rounded-lg">{{ __('common.actions.edit') }}</a>
                        @if(! $w->isBooked())
                        <a href="{{ route('admin.workers.assign', $w->id) }}"
                           class="text-xs text-white bg-blue-600 hover:bg-blue-700 px-2 py-1 rounded-lg">{{ __('common.actions.assign') }}</a>
                        @else
                        {{-- العاملة المحجوزة: مَن حجزها وحده يبني عليها عقداً --}}
                        @if($w->canCreateContractBy($me))
                        <a href="{{ route('admin.contracts.create', ['worker_id' => $w->id, 'client_id' => $w->client_id]) }}"
                           class="text-xs text-white bg-emerald-600 hover:bg-emerald-700 px-2 py-1 rounded-lg whitespace-nowrap">
                            {{ __('common.actions.create_contract') }}
                        </a>
                        @endif

                        @if($w->canBeUnassignedBy($me))
                        <form action="{{ route('admin.workers.unassign', $w->id) }}" method="POST" class="inline"
                              onsubmit="return confirm('{{ __('workers.assign.confirm_unassign') }}')">
                            @csrf
                            <button type="submit" class="text-xs text-amber-700 bg-amber-50 hover:bg-amber-100 px-2 py-1 rounded-lg">{{ __('common.actions.unassign') }}</button>
                        </form>
                        @elseif($w->hasActiveContract())
                        <span class="text-xs text-slate-300 bg-slate-50 px-2 py-1 rounded-lg cursor-not-allowed"
                              title="{{ __('workers.assign.has_contract') }}">{{ __('common.actions.unassign') }}</span>
                        @else
                        <span class="text-xs text-slate-300 bg-slate-50 px-2 py-1 rounded-lg cursor-not-allowed"
                              title="{{ __('workers.assign.no_permission') }}">{{ __('common.actions.unassign') }}</span>
                        @endif
                        @endif
                        <form action="{{ route('admin.workers.destroy', $w->id) }}" method="POST" class="inline"
                              onsubmit="return confirm('{{ __('workers.delete.confirm_one') }}')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 px-2 py-1 rounded-lg">{{ __('common.actions.delete') }}</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        <div class="px-5 py-4 border-t border-slate-100">
            {{ $workers->links() }}
        </div>
        @else
        <div class="text-center py-16 text-slate-400">
            <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <p class="text-sm">{{ __('workers.no_workers') }}</p>
            <a href="{{ route('admin.workers.bulk') }}" class="mt-3 inline-block text-sm text-blue-600 hover:underline">{{ __('workers.upload_now') }}</a>
        </div>
        @endif
    </div>

    {{-- Trashed --}}
    @if($trashed->count())
    <div x-data="{ open: false }" class="mt-6">
        <button @click="open=!open"
                class="flex items-center gap-2 text-sm text-slate-500 hover:text-slate-700 font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            {{ __('workers.trashed') }} ({{ $trashed->count() }})
            <svg class="w-3 h-3 transition-transform" :class="open?'rotate-180':''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div x-show="open" x-collapse class="mt-3 bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="data-table w-full">
                <thead><tr><th>{{ __('workers.worker') }}</th><th>{{ __('common.fields.nationality') }}</th><th>{{ __('common.actions.restore') }}</th></tr></thead>
                <tbody>
                @foreach($trashed as $w)
                <tr>
                    <td class="text-sm text-slate-600">{{ $w->name ?: __('workers.no_name') }}</td>
                    <td class="text-sm text-slate-600">{{ $w->nationality?->name ?? '—' }}</td>
                    <td>
                        <form action="{{ route('admin.workers.restore', $w->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-xs text-emerald-600 hover:text-emerald-800 bg-emerald-50 px-3 py-1 rounded-lg">{{ __('common.actions.restore') }}</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

@php
$workerCvMap = $workers->map(fn($w) => [
    'id'          => $w->id,
    'name'        => $w->name,
    'nationality' => $w->nationality?->name,
    'cv_url'      => $w->cv_path ? route('admin.workers.cv', $w->id) : null,
])->values();
@endphp

@push('scripts')
<script>
const workerCvData = @json($workerCvMap);

/**
 * يبني رسالة الواتساب ويفتحها فوراً — بلا تحميل أي صفحة.
 *
 * ترتيب النص: الأسماء لاتينية والروابط LTR بينما الرسالة عربية RTL، فلو
 * وُضعت في سطر واحد يقلب واتساب ترتيبها. لذلك كل حقل في سطر مستقل،
 * والأجزاء اللاتينية معزولة بـ U+2066/U+2069 (LRI/PDI) لتُعرض كما هي.
 */
function openWhatsapp(workers, waPhone) {
    if (!workers.length || !waPhone) return;

    const PER   = {{ \App\Services\WorkerService::WHATSAPP_MAX_PER_MESSAGE }};
    const clean = waPhone.replace(/[^0-9]/g, '');
    const LRI = '⁦', PDI = '⁩';   // عزل اتجاه النص اللاتيني
    const ltr = (s) => LRI + s + PDI;

    /**
     * يبني نص الرسالة لدفعة واحدة.
     * نمرّر الترقيم والإزاحة كوسائط لا نقرأها من الخارج، لأن الدالة تُستدعى
     * أثناء حساب الدفعات نفسها (قبل اكتمالها) ولأن أحجام الدفعات متفاوتة.
     */
    const buildMessage = (batch, offset = 0, batchIndex = 0, batchCount = 1) => {
        const header = batchCount > 1
            ? `*${LANG.msg_header} (${batchIndex + 1}/${batchCount})*`
            : `*${LANG.msg_header}*`;

        const lines = [header, ''];

        batch.forEach((w, i) => {
            const num  = offset + i + 1;
            const name = (w.name || (LANG.worker + ' ' + num)).trim();

            lines.push(`*${num}.* ${ltr(name)}`);
            if (w.nationality) lines.push(`${LANG.nationality}: ${w.nationality}`);
            lines.push(ltr(w.cv_url));
            lines.push('');                  // سطر فارغ يفصل كل عاملة
        });

        return lines.join('\n').trimEnd();
    };

    // تقسيم إلى دفعات.
    // العدد وحده لا يكفي: طول رابط wa.me هو القيد الحقيقي (تقصّه المتصفحات
    // حوالي 32k حرف)، وطول الأسماء والروابط يتفاوت. لذا نقسّم بالعدد ثم
    // نتحقق من الطول الفعلي ونُصغّر الدفعة عند تجاوزه — فلا تُقصّ رسالة صامتاً.
    const MAX_URL = {{ \App\Services\WorkerService::WHATSAPP_MAX_URL_LENGTH }};

    const batches = [];
    let cursor = 0;

    while (cursor < workers.length) {
        let size = Math.min(PER, workers.length - cursor);

        // نُقلّص الدفعة حتى يصبح الرابط ضمن الحد
        while (size > 1) {
            const probe = workers.slice(cursor, cursor + size);
            const url   = 'https://wa.me/' + clean + '?text=' + encodeURIComponent(buildMessage(probe, cursor));
            if (url.length <= MAX_URL) break;
            size = Math.floor(size * 0.8);   // تقليص تدريجي أسرع من خطوة واحدة
        }

        batches.push(workers.slice(cursor, cursor + size));
        cursor += size;
    }


    // الرسالة الأولى فوراً (ضمن حدث الضغط حتى لا يحجبها المتصفح)
    window.open('https://wa.me/' + clean + '?text=' + encodeURIComponent(buildMessage(batches[0], 0, 0, batches.length)), '_blank');

    // الباقي بتأكيد يدوي — الفتح التلقائي لعدة نوافذ يحجبه المتصفح
    if (batches.length > 1) {
        setTimeout(() => {
            for (let b = 1; b < batches.length; b++) {
                const msg = LANG.batch_next
                    .replaceAll(':current', b).replaceAll(':total', batches.length).replaceAll(':next', b + 1);
                if (!confirm(msg)) return;
                const offset = batches.slice(0, b).reduce((sum, x) => sum + x.length, 0);
                window.open('https://wa.me/' + clean + '?text=' + encodeURIComponent(buildMessage(batches[b], offset, b, batches.length)), '_blank');
            }
        }, 600);
    }
}

/** إرسال عاملات من الصفحة الحالية (بياناتها موجودة أصلاً في المتصفح). */
function sendWhatsapp(selected, waPhone) {
    if (!selected.length || !waPhone) return;

    const workers = workerCvData.filter(w => selected.includes(w.id) && w.cv_url);
    if (!workers.length) {
        alert(LANG.no_cv_msg);
        return;
    }

    openWhatsapp(workers, waPhone);
}
</script>
@endpush
@endsection
