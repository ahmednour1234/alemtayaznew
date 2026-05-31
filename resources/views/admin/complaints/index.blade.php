@extends('admin.layouts.app')
@section('title', 'الشكاوي')
@section('content')
<div class="w-full" x-data="{ showTrash: false }">

    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">إدارة الشكاوي</h2>
            <p class="text-sm text-slate-500 mt-1">إجمالي: {{ $complaints->total() }} شكوى</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.complaints.reports') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm px-4 py-2.5 rounded-lg inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6h13M9 17l-4-4m0 0l4-4m-4 4h14"/></svg>
                التقارير
            </a>
            <a href="{{ route('admin.complaints.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2.5 rounded-lg inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                شكوى جديدة
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-2.5 rounded-lg mb-4">{{ session('success') }}</div>
    @endif

    {{-- Filters --}}
    <form method="GET" class="bg-white rounded-xl shadow-sm p-4 mb-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="بحث (رقم، وصف، هاتف...)"
               class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">

        <select name="status" class="border border-slate-300 rounded-lg px-3 py-2 text-sm">
            <option value="">— كل الحالات —</option>
            @foreach(\App\Models\Complaint::statuses() as $k => $v)
            <option value="{{ $k }}" {{ ($filters['status'] ?? '') === $k ? 'selected' : '' }}>{{ $v }}</option>
            @endforeach
        </select>

        <select name="priority" class="border border-slate-300 rounded-lg px-3 py-2 text-sm">
            <option value="">— كل الأولويات —</option>
            @foreach(\App\Models\Complaint::priorities() as $k => $v)
            <option value="{{ $k }}" {{ ($filters['priority'] ?? '') === $k ? 'selected' : '' }}>{{ $v }}</option>
            @endforeach
        </select>

        <select name="problem_type" class="border border-slate-300 rounded-lg px-3 py-2 text-sm">
            <option value="">— كل المشاكل —</option>
            @foreach(\App\Models\Complaint::problemTypes() as $k => $v)
            <option value="{{ $k }}" {{ ($filters['problem_type'] ?? '') === $k ? 'selected' : '' }}>{{ $v }}</option>
            @endforeach
        </select>

        @php $_me = auth('admin')->user(); @endphp
        @if($_me->isBranchAdmin())
        <div class="border border-blue-200 bg-blue-50 rounded-lg px-3 py-2 flex items-center gap-2">
            <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            <span class="text-blue-700 text-sm font-medium">{{ $_me->branch?->name ?? 'فرعي' }}</span>
        </div>
        @else
        <select name="branch_id" class="border border-slate-300 rounded-lg px-3 py-2 text-sm">
            <option value="">— كل الفروع —</option>
            @foreach($branches as $b)
            <option value="{{ $b->id }}" {{ ($filters['branch_id'] ?? '') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
            @endforeach
        </select>
        @endif

        <select name="on_musaned" class="border border-slate-300 rounded-lg px-3 py-2 text-sm">
            <option value="">— مساند: الكل —</option>
            <option value="1" {{ ($filters['on_musaned'] ?? '') === '1' ? 'selected' : '' }}>على مساند</option>
            <option value="0" {{ ($filters['on_musaned'] ?? '') === '0' ? 'selected' : '' }}>غير مرفوعة</option>
        </select>

        <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="border border-slate-300 rounded-lg px-3 py-2 text-sm">
        <input type="date" name="date_to"   value="{{ $filters['date_to']   ?? '' }}" class="border border-slate-300 rounded-lg px-3 py-2 text-sm">

        <div class="lg:col-span-4 flex gap-2">
            <button class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-5 py-2 rounded-lg">بحث</button>
            <a href="{{ route('admin.complaints.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm px-5 py-2 rounded-lg">إعادة تعيين</a>
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-right">الرقم</th>
                        <th class="px-4 py-3 text-right">المشكلة</th>
                        <th class="px-4 py-3 text-right">العميل</th>
                        <th class="px-4 py-3 text-right">العقد</th>
                        <th class="px-4 py-3 text-right">الفرع</th>
                        <th class="px-4 py-3 text-right">المسؤول</th>
                        <th class="px-4 py-3 text-right">الأولوية</th>
                        <th class="px-4 py-3 text-right">الحالة</th>
                        <th class="px-4 py-3 text-right">مساند</th>
                        <th class="px-4 py-3 text-right">التاريخ</th>
                        <th class="px-4 py-3 text-right">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($complaints as $c)
                    @php
                        $isStale = in_array($c->status, ['new', 'in_progress']) && $c->created_at->lt(now()->subDays(7));
                    @endphp
                    <tr class="hover:bg-slate-50 {{ $isStale ? 'bg-red-50/40' : '' }}">
                        <td class="px-4 py-3 font-mono text-xs text-slate-700">
                            <a href="{{ route('admin.complaints.show', $c->id) }}" class="text-blue-600 hover:underline">{{ $c->complaint_number }}</a>
                            @if($isStale)<span class="block text-[10px] text-red-600 mt-0.5">⚠ تأخر</span>@endif
                        </td>
                        <td class="px-4 py-3">{{ $c->problem_type_label }}</td>
                        <td class="px-4 py-3">{{ $c->client->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-xs">{{ $c->contract->contract_number ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $c->branch->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $c->assignedAdmin->name ?? '—' }}</td>
                        <td class="px-4 py-3"><span class="inline-block px-2 py-0.5 rounded text-xs {{ $c->priority_badge_class }}">{{ $c->priority_label }}</span></td>
                        <td class="px-4 py-3"><span class="inline-block px-2 py-0.5 rounded text-xs {{ $c->status_badge_class }}">{{ $c->status_label }}</span></td>
                        <td class="px-4 py-3">
                            @if($c->on_musaned)
                                <span class="text-emerald-600 text-xs">✓ {{ $c->musaned_number ?: 'نعم' }}</span>
                            @else
                                <span class="text-slate-400 text-xs">لا</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-500">{{ $c->created_at->format('Y-m-d') }}</td>
                        <td class="px-4 py-3">
                            <div class="flex gap-1">
                                <a href="{{ route('admin.complaints.show', $c->id) }}" class="text-blue-600 hover:bg-blue-50 p-1.5 rounded" title="عرض">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('admin.complaints.edit', $c->id) }}" class="text-amber-600 hover:bg-amber-50 p-1.5 rounded" title="تعديل">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('admin.complaints.destroy', $c->id) }}" method="POST" onsubmit="return confirm('تأكيد الحذف؟')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:bg-red-50 p-1.5 rounded" title="حذف">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="11" class="px-4 py-10 text-center text-slate-400">لا توجد شكاوى</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($complaints->hasPages())
        <div class="px-4 py-3 border-t border-slate-100">{{ $complaints->links() }}</div>
        @endif
    </div>

    {{-- Trashed --}}
    @if($trashed->count())
    <div class="mt-6 bg-white rounded-xl shadow-sm">
        <button type="button" @click="showTrash = !showTrash" class="w-full flex items-center justify-between px-5 py-3 text-sm font-semibold text-slate-700">
            <span>المحذوفات ({{ $trashed->count() }})</span>
            <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': showTrash }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div x-show="showTrash" x-collapse>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-slate-100">
                    @foreach($trashed as $t)
                    <tr>
                        <td class="px-4 py-2 font-mono text-xs">{{ $t->complaint_number }}</td>
                        <td class="px-4 py-2">{{ $t->branch->name ?? '—' }}</td>
                        <td class="px-4 py-2 text-xs text-slate-500">حُذفت في {{ $t->deleted_at->format('Y-m-d') }}</td>
                        <td class="px-4 py-2 text-left">
                            <form action="{{ route('admin.complaints.restore', $t->id) }}" method="POST">
                                @csrf
                                <button class="text-emerald-600 hover:bg-emerald-50 text-xs px-3 py-1 rounded">استعادة</button>
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
@endsection
