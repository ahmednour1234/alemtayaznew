@extends('admin.layouts.app')
@section('title', 'طلبات الموقع')
@section('content')

<div class="w-full">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">طلبات الموقع الإلكتروني</h2>
            <p class="text-xs text-slate-400 mt-1">الطلبات الواردة من نموذج «تواصل معنا» ونافذة الطلب السريع</p>
        </div>
        <a href="{{ route('site.home') }}" target="_blank" rel="noopener"
           class="text-xs text-blue-600 hover:underline">معاينة الموقع ↗</a>
    </div>

    @if(session('success'))
    <div class="mb-5 bg-green-50 border border-green-200 rounded-xl p-4 text-sm text-green-800 font-medium">
        {{ session('success') }}
    </div>
    @endif

    {{-- ══ عدّادات ══ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
        @foreach([
            ['l' => 'إجمالي الطلبات', 'v' => $counts['total'],      'c' => 'text-slate-800'],
            ['l' => 'طلبات جديدة',    'v' => $counts['new'],        'c' => 'text-blue-600'],
            ['l' => 'بلا مسؤول',      'v' => $counts['unassigned'], 'c' => 'text-amber-600'],
            ['l' => 'طلبات اليوم',    'v' => $counts['today'],      'c' => 'text-green-600'],
        ] as $card)
        <div class="bg-white rounded-xl shadow-sm p-4">
            <p class="text-2xl font-bold {{ $card['c'] }}">{{ $card['v'] }}</p>
            <p class="text-xs text-slate-400 mt-0.5">{{ $card['l'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- ══ فلاتر ══ --}}
    <form method="GET" class="bg-white rounded-xl shadow-sm p-4 mb-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">بحث</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="اسم، جوال، مدينة..."
                       class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">الحالة</label>
                <select name="status" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">كل الحالات</option>
                    @foreach($statuses as $key => $st)
                    <option value="{{ $key }}" @selected(request('status') === $key)>{{ $st['label'] }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">المصدر</label>
                <select name="source" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">كل المصادر</option>
                    @foreach($sources as $key => $label)
                    <option value="{{ $key }}" @selected(request('source') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">المسؤول</label>
                <select name="assigned_admin_id" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">الكل</option>
                    <option value="none" @selected(request('assigned_admin_id') === 'none')>بلا مسؤول</option>
                    @foreach($admins as $a)
                    <option value="{{ $a->id }}" @selected(request('assigned_admin_id') == $a->id)>{{ $a->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 rounded-lg transition-colors">بحث</button>
                <a href="{{ route('admin.marketing.website-leads.index') }}"
                   class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm rounded-lg transition-colors">مسح</a>
            </div>
        </div>
    </form>

    @if($leads->isEmpty())
    <div class="bg-white rounded-xl shadow-sm p-12 text-center">
        <p class="font-semibold text-slate-600">لا توجد طلبات مطابقة</p>
        <p class="text-sm text-slate-400 mt-1.5">ستظهر هنا الطلبات الواردة من الموقع الإلكتروني.</p>
    </div>
    @else

    {{-- ══ جدول (شاشات كبيرة) ══ --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden hidden lg:block">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead style="background:#f8fafc">
                    <tr class="text-slate-500 text-xs border-b">
                        <th class="px-4 py-3 text-right font-medium">الاسم</th>
                        <th class="px-4 py-3 text-right font-medium">الجوال</th>
                        <th class="px-4 py-3 text-right font-medium">المدينة</th>
                        <th class="px-4 py-3 text-right font-medium">الخدمة المطلوبة</th>
                        <th class="px-4 py-3 text-right font-medium">المصدر</th>
                        <th class="px-4 py-3 text-right font-medium">المسؤول</th>
                        <th class="px-4 py-3 text-right font-medium">الحالة</th>
                        <th class="px-4 py-3 text-right font-medium">التاريخ</th>
                        <th class="px-4 py-3 text-right font-medium">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leads as $lead)
                    <tr class="border-b border-slate-50 hover:bg-blue-50/30 transition-colors">
                        <td class="px-4 py-3 font-semibold text-slate-700">{{ $lead->name }}</td>
                        <td class="px-4 py-3">
                            <a href="tel:{{ $lead->phone }}" dir="ltr" class="text-blue-600 hover:underline">{{ $lead->phone }}</a>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $lead->city ?: '—' }}</td>
                        <td class="px-4 py-3 text-slate-600 text-xs max-w-[16rem]">
                            <span class="line-clamp-2">{{ $lead->notes ?: '—' }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-[11px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">
                                {{ $sources[$lead->source] ?? $lead->source }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-600">
                            @if($lead->assignedAdmin)
                                {{ $lead->assignedAdmin->name }}
                            @else
                                <span class="text-amber-600 text-xs font-medium">بلا مسؤول</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-[11px] px-2 py-0.5 rounded-full font-medium {{ $statuses[$lead->status]['color'] ?? 'bg-slate-100 text-slate-600' }}">
                                {{ $statuses[$lead->status]['label'] ?? $lead->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-400">{{ $lead->created_at?->format('Y-m-d H:i') }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1">
                                <a href="{{ route('admin.marketing.leads.show', $lead->id) }}"
                                   class="text-xs text-slate-500 hover:text-blue-600 bg-slate-100 hover:bg-blue-50 px-2 py-1 rounded-lg transition-colors">عرض</a>
                                @if(! $lead->assigned_admin_id)
                                <button type="button"
                                        onclick="openAssign({{ $lead->id }}, '{{ e($lead->name) }}')"
                                        class="text-xs text-white bg-blue-600 hover:bg-blue-700 px-2 py-1 rounded-lg transition-colors">إسناد</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ══ بطاقات (جوال وتابلت) ══ --}}
    <div class="space-y-3 lg:hidden">
        @foreach($leads as $lead)
        <div class="bg-white rounded-xl shadow-sm p-4">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="font-bold text-slate-800 truncate">{{ $lead->name }}</p>
                    <a href="tel:{{ $lead->phone }}" dir="ltr" class="text-sm text-blue-600 hover:underline">{{ $lead->phone }}</a>
                </div>
                <span class="text-[11px] px-2 py-0.5 rounded-full font-medium flex-shrink-0 {{ $statuses[$lead->status]['color'] ?? 'bg-slate-100 text-slate-600' }}">
                    {{ $statuses[$lead->status]['label'] ?? $lead->status }}
                </span>
            </div>

            <dl class="grid grid-cols-2 gap-2 mt-3 text-xs">
                <div class="bg-slate-50 rounded-lg px-3 py-2">
                    <dt class="text-slate-400">المدينة</dt>
                    <dd class="font-medium text-slate-700 mt-0.5">{{ $lead->city ?: '—' }}</dd>
                </div>
                <div class="bg-slate-50 rounded-lg px-3 py-2">
                    <dt class="text-slate-400">المسؤول</dt>
                    <dd class="font-medium mt-0.5 {{ $lead->assignedAdmin ? 'text-slate-700' : 'text-amber-600' }}">
                        {{ $lead->assignedAdmin?->name ?? 'بلا مسؤول' }}
                    </dd>
                </div>
            </dl>

            @if($lead->notes)
            <p class="text-xs text-slate-500 mt-3 leading-relaxed line-clamp-3">{{ $lead->notes }}</p>
            @endif

            <div class="flex items-center justify-between gap-2 mt-4 pt-3 border-t border-slate-100">
                <span class="text-[11px] text-slate-400">{{ $lead->created_at?->format('Y-m-d H:i') }}</span>
                <div class="flex items-center gap-1.5">
                    <a href="{{ route('admin.marketing.leads.show', $lead->id) }}"
                       class="text-xs text-slate-600 bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-lg transition-colors">عرض</a>
                    @if(! $lead->assigned_admin_id)
                    <button type="button" onclick="openAssign({{ $lead->id }}, '{{ e($lead->name) }}')"
                            class="text-xs text-white bg-blue-600 hover:bg-blue-700 px-3 py-1.5 rounded-lg transition-colors">إسناد</button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-5">{{ $leads->links() }}</div>
    @endif
</div>

{{-- ══ نافذة الإسناد ══ --}}
<div id="assign-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/50">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
        <h3 class="font-bold text-slate-800 mb-1">إسناد الطلب</h3>
        <p id="assign-lead-name" class="text-xs text-slate-400 mb-4"></p>

        <form id="assign-form" method="POST">
            @csrf
            <label class="block text-xs font-medium text-slate-600 mb-1.5">الموظف المسؤول</label>
            <select name="assigned_admin_id" required
                    class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">اختر الموظف</option>
                @foreach($admins as $a)
                <option value="{{ $a->id }}">{{ $a->name }}</option>
                @endforeach
            </select>

            <div class="flex gap-2 mt-5">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-2.5 rounded-lg transition-colors">إسناد</button>
                <button type="button" onclick="closeAssign()"
                        class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm rounded-lg transition-colors">إلغاء</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // قالب المسار: نستبدل المعرّف عند الفتح بدل بناء الرابط يدوياً
    const ASSIGN_URL = @json(route('admin.marketing.website-leads.assign', ['lead' => '__ID__']));

    function openAssign(id, name) {
        const modal = document.getElementById('assign-modal');
        document.getElementById('assign-form').action = ASSIGN_URL.replace('__ID__', id);
        document.getElementById('assign-lead-name').textContent = name;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeAssign() {
        const modal = document.getElementById('assign-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    document.getElementById('assign-modal').addEventListener('click', function (e) {
        if (e.target === this) closeAssign();
    });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeAssign(); });
</script>
@endpush

@endsection
