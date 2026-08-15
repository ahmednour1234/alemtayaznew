@extends('admin.layouts.app')
@section('title', 'العاملات - CV')
@section('content')
@php
    // معرّفات العاملات التي لديها CV — فقط هذه يمكن إرسالها عبر واتساب
    $cvIds = $workers->filter(fn($w) => (bool) $w->cv_path)->pluck('id')->values();
@endphp
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

        // الإرسال — يفتح واتساب مباشرة بلا تحميل أي صفحة
        async sendSelected() {
            if (!this.waPhone || this.sending) return;

            const MAX = {{ \App\Services\WorkerService::WHATSAPP_MAX_PER_MESSAGE }};

            // الحالة العادية: البيانات موجودة في الصفحة → إرسال فوري
            if (!this.selectAllMatching) {
                const ids = this.selectedWithCv;
                if (!ids.length) { alert('لا توجد عاملات لديها CV ضمن التحديد.'); return; }
                if (ids.length > MAX && !confirm('سيتم إرسال أول ' + MAX + ' عاملة فقط من أصل ' + ids.length + ' لأن رسالة واتساب لا تتسع لأكثر من ذلك. متابعة؟')) return;
                sendWhatsapp(ids.slice(0, MAX), this.waPhone);
                return;
            }

            // وضع «كل النتائج»: نجلب بيانات أول MAX عاملة فقط
            this.sending = true;
            try {
                const params = new URLSearchParams(window.location.search);
                params.set('has_cv', '1');
                params.set('with_cv_data', '1');
                const res = await fetch('{{ route('admin.workers.matching-ids') }}?' + params.toString(),
                                        { headers: { 'Accept': 'application/json' } });
                if (!res.ok) throw new Error('fetch failed');
                const data = await res.json();

                if (!data.workers.length) { alert('لا توجد عاملات لديها CV ضمن النتائج.'); return; }
                if (data.count > data.limit && !confirm('سيتم إرسال أول ' + data.limit + ' عاملة فقط من أصل ' + data.count + ' لأن رسالة واتساب لا تتسع لأكثر من ذلك. متابعة؟')) return;

                openWhatsapp(data.workers, this.waPhone);
            } catch (e) {
                alert('تعذّر جلب قائمة العاملات. حاول مرة أخرى.');
            } finally {
                this.sending = false;
            }
        }
     }">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-5">
        <h2 class="text-xl font-bold text-slate-800">العاملات — إدارة CV</h2>
        <div class="flex gap-2">
            <a href="{{ route('admin.workers.bulk') }}"
               class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm px-4 py-2 rounded-lg font-medium flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                رفع CV متعددة
            </a>
            <a href="{{ route('admin.workers.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg font-medium flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                إضافة عاملة
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" class="bg-white rounded-xl shadow-sm p-4 mb-5">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">الجنسية</label>
                <select name="nationality_id" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">كل الجنسيات</option>
                    @foreach($nationalities as $nat)
                    <option value="{{ $nat->id }}" {{ ($filters['nationality_id'] ?? '') == $nat->id ? 'selected' : '' }}>{{ $nat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">الحالة</label>
                <select name="status" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">كل الحالات</option>
                    <option value="available"            {{ ($filters['status'] ?? '') === 'available'            ? 'selected' : '' }}>متاحة</option>
                    <option value="reserved"             {{ ($filters['status'] ?? '') === 'reserved'             ? 'selected' : '' }}>محجوزة</option>
                    <option value="assigned"             {{ ($filters['status'] ?? '') === 'assigned'             ? 'selected' : '' }}>تم التعيين</option>
                    <option value="in_housing"           {{ ($filters['status'] ?? '') === 'in_housing'           ? 'selected' : '' }}>في السكن</option>
                    <option value="sponsorship_transfer" {{ ($filters['status'] ?? '') === 'sponsorship_transfer' ? 'selected' : '' }}>نقل كفالة</option>
                    <option value="deportation"          {{ ($filters['status'] ?? '') === 'deportation'          ? 'selected' : '' }}>تسفير</option>
                    <option value="returned"             {{ ($filters['status'] ?? '') === 'returned'             ? 'selected' : '' }}>عودة</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">المهنة</label>
                <select name="profession" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">كل المهن</option>
                    @foreach($professions as $key => $label)
                    <option value="{{ $key }}" {{ ($filters['profession'] ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">بحث</label>
                <div class="flex gap-2">
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="اسم، جواز، هاتف..."
                           class="flex-1 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <button type="submit" class="bg-blue-600 text-white px-3 py-2 rounded-lg text-sm">بحث</button>
                    <a href="{{ route('admin.workers.index') }}" class="bg-slate-100 text-slate-600 px-3 py-2 rounded-lg text-sm">مسح</a>
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
                <span class="text-sm font-semibold text-green-700">إرسال عبر واتساب</span>
                <span class="bg-green-600 text-white text-xs px-2 py-0.5 rounded-full"
                      x-text="selectAllMatching ? ('كل النتائج (' + totalMatching + ')') : (selectedWithCv.length + ' CV جاهز للإرسال')"></span>
            </div>
            <form @submit.prevent="sendSelected()" class="flex gap-2 flex-1 min-w-0">
                <input type="tel" x-model="waPhone" placeholder="رقم الواتساب (مثال: 966501234567)"
                       class="flex-1 border border-green-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 min-w-0" required>
                <button type="submit" :disabled="sending || (!selectAllMatching && selectedWithCv.length === 0)"
                        class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-lg font-medium whitespace-nowrap disabled:bg-slate-300 disabled:cursor-not-allowed">
                    <span x-text="sending ? 'جارٍ التحضير...' : 'إرسال'"></span>
                </button>
            </form>

            {{-- حذف جماعي --}}
            <form method="POST" action="{{ route('admin.workers.bulk-destroy') }}" class="inline"
                  @submit="return confirm('حذف ' + actionCount + ' عاملة؟ العاملات المرتبطة بعقود استقدام لن تُحذف.')">
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
                    حذف المحدد (<span x-text="actionCount"></span>)
                </button>
            </form>

            <button @click="selectNone()" class="text-sm text-slate-500 hover:text-slate-700">إلغاء التحديد</button>
        </div>

        {{-- تحديد كل النتائج عبر كل الصفحات --}}
        <div x-show="selected.length === pageIds.length && totalMatching > pageIds.length" x-cloak
             class="mt-3 pt-3 border-t border-green-200 text-sm">
            <template x-if="!selectAllMatching">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-slate-600">
                        تم تحديد <strong x-text="pageIds.length"></strong> في هذه الصفحة فقط.
                    </span>
                    <button type="button" @click="selectAllMatching = true"
                            class="text-blue-600 hover:text-blue-800 font-semibold underline">
                        تحديد كل النتائج المطابقة (<span x-text="totalMatching"></span>)
                    </button>
                </div>
            </template>
            <template x-if="selectAllMatching">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-green-800 font-semibold">
                        محدَّد الآن كل النتائج المطابقة للفلتر (<span x-text="totalMatching"></span>) عبر كل الصفحات.
                    </span>
                    <button type="button" @click="selectAllMatching = false"
                            class="text-slate-500 hover:text-slate-700 underline">
                        الاكتفاء بهذه الصفحة
                    </button>
                </div>
            </template>
        </div>

        {{-- تنبيه عند تحديد عاملات بلا CV --}}
        <p x-show="!selectAllMatching && selectedNoCv.length > 0" x-cloak class="text-xs text-amber-700 mt-2">
            <span x-text="selectedNoCv.length"></span> من المحددات بلا CV — ستُستبعد من الإرسال عبر واتساب، لكنها ستُحذف لو ضغطت «حذف المحدد».
        </p>
        <p x-show="selectAllMatching" x-cloak class="text-xs text-amber-700 mt-2">
            عند الإرسال عبر واتساب ستُرسل فقط العاملات التي لديها CV من ضمن النتائج المطابقة.
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
                    <th>العاملة</th>
                    <th>الجنسية</th>
                    <th>المهنة</th>
                    <th>الخبرة</th>
                    <th>الحالة</th>
                    <th>العميل</th>
                    <th>حجزها</th>
                    <th>CV</th>
                    <th>الإجراءات</th>
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
                    <div class="text-xs text-slate-400">جواز: {{ $w->passport_number }}</div>
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
                    {{ $w->client?->name ?? '—' }}
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
                    <span class="text-xs text-slate-400">لا يوجد</span>
                    @endif
                </td>
                <td>
                    <div class="flex items-center gap-1">
                        <a href="{{ route('admin.workers.show', $w->id) }}"
                           class="text-xs text-slate-500 hover:text-blue-600 bg-slate-100 hover:bg-blue-50 px-2 py-1 rounded-lg">عرض</a>
                        <a href="{{ route('admin.workers.edit', $w->id) }}"
                           class="text-xs text-slate-500 hover:text-emerald-600 bg-slate-100 hover:bg-emerald-50 px-2 py-1 rounded-lg">تعديل</a>
                        @if($w->status !== 'assigned')
                        <a href="{{ route('admin.workers.assign', $w->id) }}"
                           class="text-xs text-white bg-blue-600 hover:bg-blue-700 px-2 py-1 rounded-lg">تعيين</a>
                        @else
                        <form action="{{ route('admin.workers.unassign', $w->id) }}" method="POST" class="inline"
                              onsubmit="return confirm('إلغاء تعيين العاملة وإرجاعها متاحة؟')">
                            @csrf
                            <button type="submit" class="text-xs text-amber-700 bg-amber-50 hover:bg-amber-100 px-2 py-1 rounded-lg">إلغاء التعيين</button>
                        </form>
                        @endif
                        <form action="{{ route('admin.workers.destroy', $w->id) }}" method="POST" class="inline"
                              onsubmit="return confirm('حذف هذه العاملة؟')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 px-2 py-1 rounded-lg">حذف</button>
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
            <p class="text-sm">لا توجد عاملات</p>
            <a href="{{ route('admin.workers.bulk') }}" class="mt-3 inline-block text-sm text-blue-600 hover:underline">رفع CVs الآن</a>
        </div>
        @endif
    </div>

    {{-- Trashed --}}
    @if($trashed->count())
    <div x-data="{ open: false }" class="mt-6">
        <button @click="open=!open"
                class="flex items-center gap-2 text-sm text-slate-500 hover:text-slate-700 font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            المحذوفون ({{ $trashed->count() }})
            <svg class="w-3 h-3 transition-transform" :class="open?'rotate-180':''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div x-show="open" x-collapse class="mt-3 bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="data-table w-full">
                <thead><tr><th>العاملة</th><th>الجنسية</th><th>استعادة</th></tr></thead>
                <tbody>
                @foreach($trashed as $w)
                <tr>
                    <td class="text-sm text-slate-600">{{ $w->name ?: 'بدون اسم' }}</td>
                    <td class="text-sm text-slate-600">{{ $w->nationality?->name ?? '—' }}</td>
                    <td>
                        <form action="{{ route('admin.workers.restore', $w->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-xs text-emerald-600 hover:text-emerald-800 bg-emerald-50 px-3 py-1 rounded-lg">استعادة</button>
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

/** يبني رسالة الواتساب ويفتحها فوراً — بلا تحميل أي صفحة. */
function openWhatsapp(workers, waPhone) {
    if (!workers.length || !waPhone) return;

    const lines = ['السلام عليكم، مرفق مجموعة CV عاملات للمراجعة:\n'];
    workers.forEach((w, i) => {
        const name = w.name || ('عاملة ' + (i + 1));
        const nat  = w.nationality ? ` (${w.nationality})` : '';
        lines.push(`${i + 1}. ${name}${nat}\n${w.cv_url}`);
    });

    const message = lines.join('\n\n');
    const clean   = waPhone.replace(/[^0-9]/g, '');
    window.open('https://wa.me/' + clean + '?text=' + encodeURIComponent(message), '_blank');
}

/** إرسال عاملات من الصفحة الحالية (بياناتها موجودة أصلاً في المتصفح). */
function sendWhatsapp(selected, waPhone) {
    if (!selected.length || !waPhone) return;

    const workers = workerCvData.filter(w => selected.includes(w.id) && w.cv_url);
    if (!workers.length) {
        alert('العاملات المحددة لا تحتوي على CV');
        return;
    }

    openWhatsapp(workers, waPhone);
}
</script>
@endpush
@endsection
