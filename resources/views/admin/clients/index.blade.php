@extends('admin.layouts.app')
@section('title', 'العملاء')
@section('content')

<div class="flex justify-between items-center mb-5">
    <h2 class="text-xl font-bold text-slate-800">العملاء</h2>
    @if(Auth::guard('admin')->user()->isSuperAdmin() || Auth::guard('admin')->user()->hasPermission('clients.create'))
    <a href="{{ route('admin.clients.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        إضافة عميل
    </a>
    @endif
</div>

@if(session('success'))
<div class="mb-4 bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 text-sm">{{ session('success') }}</div>
@endif

<!-- Filters -->
<div class="bg-white rounded-xl p-5 shadow-sm mb-4 border border-slate-100">
    <form method="GET">
        <div class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[140px]">
                <label class="block text-xs font-medium text-slate-500 mb-1.5">الحالة الاجتماعية</label>
                <select name="marital_status"
                        class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">الكل</option>
                    <option value="single"   {{ request('marital_status') === 'single'   ? 'selected' : '' }}>أعزب</option>
                    <option value="married"  {{ request('marital_status') === 'married'  ? 'selected' : '' }}>متزوج</option>
                    <option value="divorced" {{ request('marital_status') === 'divorced' ? 'selected' : '' }}>مطلق</option>
                    <option value="widowed"  {{ request('marital_status') === 'widowed'  ? 'selected' : '' }}>أرمل</option>
                </select>
            </div>
            <div class="flex-1 min-w-[140px]">
                <label class="block text-xs font-medium text-slate-500 mb-1.5">التصنيف</label>
                <select name="classification"
                        class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">الكل</option>
                    <option value="new"     {{ request('classification') === 'new'     ? 'selected' : '' }}>جديد</option>
                    <option value="premium" {{ request('classification') === 'premium' ? 'selected' : '' }}>مميز</option>
                    <option value="blocked" {{ request('classification') === 'blocked' ? 'selected' : '' }}>محظور</option>
                </select>
            </div>
            @unless(Auth::guard('admin')->user()->isBranchAdmin())
            <div class="flex-1 min-w-[140px]">
                <label class="block text-xs font-medium text-slate-500 mb-1.5">الفرع</label>
                <select name="branch_id"
                        class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">الكل</option>
                    @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            @endunless
            <div class="flex-1 min-w-[180px]">
                <label class="block text-xs font-medium text-slate-500 mb-1.5">بحث</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="الاسم، الهوية، الجوال..."
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div class="flex gap-2">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg">بحث</button>
                <a href="{{ route('admin.clients.index') }}"
                   class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm px-4 py-2 rounded-lg">إعادة ضبط</a>
            </div>
        </div>
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs uppercase">
                    <th class="px-4 py-3 text-right font-medium">#</th>
                    <th class="px-4 py-3 text-right font-medium">الاسم</th>
                    <th class="px-4 py-3 text-right font-medium">الهوية الوطنية</th>
                    <th class="px-4 py-3 text-right font-medium">الجوال</th>
                    <th class="px-4 py-3 text-right font-medium">الحالة الاجتماعية</th>
                    <th class="px-4 py-3 text-right font-medium">التصنيف</th>
                    <th class="px-4 py-3 text-right font-medium">الفرع</th>
                    <th class="px-4 py-3 text-right font-medium">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($clients as $client)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3 text-slate-400">{{ $clients->firstItem() + $loop->index }}</td>
                    <td class="px-4 py-3 font-medium text-slate-800">{{ $client->name }}</td>
                    <td class="px-4 py-3 text-slate-600 font-mono">{{ $client->national_id }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $client->phone }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $client->marital_status_label }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold"
                              style="background:{{ $client->classification_color }}22;color:{{ $client->classification_color }}">
                            {{ $client->classification_label }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-slate-600">{{ $client->branch?->name ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            @if(Auth::guard('admin')->user()->isSuperAdmin() || Auth::guard('admin')->user()->hasPermission('clients.view'))
                            <a href="{{ route('admin.clients.show', $client->id) }}"
                               class="text-slate-400 hover:text-blue-600" title="عرض">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            @endif
                            @if(Auth::guard('admin')->user()->isSuperAdmin() || Auth::guard('admin')->user()->hasPermission('clients.edit'))
                            <a href="{{ route('admin.clients.edit', $client->id) }}"
                               class="text-slate-400 hover:text-amber-600" title="تعديل">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            @endif
                            @if(Auth::guard('admin')->user()->isSuperAdmin() || Auth::guard('admin')->user()->hasPermission('clients.delete'))
                            <form action="{{ route('admin.clients.destroy', $client->id) }}" method="POST"
                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا العميل؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-slate-400 hover:text-red-600" title="حذف">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-4 py-10 text-center text-slate-400">لا توجد بيانات</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($clients->hasPages())
    <div class="px-4 py-3 border-t border-slate-100">{{ $clients->withQueryString()->links() }}</div>
    @endif
</div>

<!-- Trashed -->
@if($trashed->count() && (Auth::guard('admin')->user()->isSuperAdmin() || Auth::guard('admin')->user()->hasPermission('clients.delete')))
<div class="mt-6 bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden" x-data="{ open: false }">
    <button @click="open = !open" class="w-full flex items-center justify-between px-5 py-4 text-sm font-medium text-slate-600 hover:bg-slate-50">
        <span>العملاء المحذوفون ({{ $trashed->count() }})</span>
        <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
    </button>
    <div x-show="open" x-collapse class="border-t border-slate-100">
        <table class="w-full text-sm">
            <tbody class="divide-y divide-slate-100">
                @foreach($trashed as $client)
                <tr class="bg-red-50">
                    <td class="px-4 py-3 text-slate-500">{{ $client->name }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ $client->national_id }}</td>
                    <td class="px-4 py-3">
                        <form action="{{ route('admin.clients.restore', $client->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-xs bg-green-100 hover:bg-green-200 text-green-700 px-3 py-1 rounded-lg">استعادة</button>
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
