@extends('admin.layouts.app')
@section('title', 'مباني السكن')
@section('content')

<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-slate-800">مباني السكن</h2>
    <a href="{{ route('admin.housings.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        إضافة سكن
    </a>
</div>

@if(session('success'))
<div class="mb-4 bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 text-sm">{{ session('success') }}</div>
@endif

<!-- Filters -->
<div class="bg-white rounded-xl p-4 shadow-sm mb-4 border border-slate-100">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">بحث</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="اسم المبنى"
                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">الفرع</label>
            <select name="branch_id" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                <option value="">الكل</option>
                @foreach($branches as $b)
                <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">الحالة</label>
            <select name="active" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                <option value="">الكل</option>
                <option value="1" {{ request('active') === '1' ? 'selected' : '' }}>نشط</option>
                <option value="0" {{ request('active') === '0' ? 'selected' : '' }}>غير نشط</option>
            </select>
        </div>
        <div class="flex items-end">
            <button class="bg-slate-800 hover:bg-slate-900 text-white text-sm px-5 py-2 rounded-lg w-full">تصفية</button>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-xs border-b">
            <tr>
                <th class="px-4 py-3 text-right">#</th>
                <th class="px-4 py-3 text-right">اسم المبنى</th>
                <th class="px-4 py-3 text-right">الفرع</th>
                <th class="px-4 py-3 text-right">المسؤول</th>
                <th class="px-4 py-3 text-right">العنوان</th>
                <th class="px-4 py-3 text-right">السعة</th>
                <th class="px-4 py-3 text-right">الحالة</th>
                <th class="px-4 py-3 text-right">الإجراءات</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($housings as $h)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 text-slate-400">{{ $loop->iteration }}</td>
                <td class="px-4 py-3 font-medium text-slate-800">{{ $h->name }}</td>
                <td class="px-4 py-3 text-slate-600">{{ $h->branch?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-600">{{ $h->admin?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-500 text-xs">{{ $h->address ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-600 text-center">{{ $h->capacity ?? '—' }}</td>
                <td class="px-4 py-3">
                    @if($h->active)
                        <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs">نشط</span>
                    @else
                        <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs">غير نشط</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        <a href="{{ route('admin.housings.edit', $h->id) }}" class="text-slate-500 hover:text-yellow-600" title="تعديل">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <form action="{{ route('admin.housings.toggle', $h->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-slate-500 hover:text-purple-600" title="تفعيل/تعطيل">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 11-12.728 0M12 3v9"/></svg>
                            </button>
                        </form>
                        <form action="{{ route('admin.housings.destroy', $h->id) }}" method="POST" class="inline"
                              onsubmit="return confirm('حذف هذا السكن؟')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-slate-500 hover:text-red-600" title="حذف">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="px-4 py-8 text-center text-slate-400">لا توجد مباني سكن</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($housings->hasPages())
    <div class="px-4 py-3 border-t">{{ $housings->withQueryString()->links() }}</div>
    @endif
</div>

@if($trashed->isNotEmpty())
<div x-data="{ open: false }" class="mt-6">
    <button @click="open = !open" class="text-sm text-slate-500 hover:text-red-600 flex items-center gap-1">
        المحذوفة ({{ $trashed->count() }})
    </button>
    <div x-show="open" class="mt-3 bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-red-50 text-xs text-slate-500 border-b">
                <tr>
                    <th class="px-4 py-2 text-right">الاسم</th>
                    <th class="px-4 py-2 text-right">الفرع</th>
                    <th class="px-4 py-2 text-right">تاريخ الحذف</th>
                    <th class="px-4 py-2 text-right">استعادة</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($trashed as $h)
                <tr>
                    <td class="px-4 py-2 line-through text-slate-400">{{ $h->name }}</td>
                    <td class="px-4 py-2 text-xs text-slate-500">{{ $h->branch?->name ?? '—' }}</td>
                    <td class="px-4 py-2 text-xs text-slate-400">{{ $h->deleted_at?->format('Y-m-d') }}</td>
                    <td class="px-4 py-2">
                        <form action="{{ route('admin.housings.restore', $h->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-xs text-green-600 hover:underline">استعادة</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
