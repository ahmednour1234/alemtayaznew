@extends('admin.layouts.app')
@section('title', 'الوكلاء')
@section('content')

<div class="flex justify-between items-center mb-5">
    <h2 class="text-xl font-bold text-slate-800">الوكلاء</h2>
    @if(Auth::guard('admin')->user()->isSuperAdmin() || Auth::guard('admin')->user()->hasPermission('agents.create'))
    <a href="{{ route('admin.agents.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        إضافة وكيل
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
            <div class="flex-1 min-w-[150px]">
                <label class="block text-xs font-medium text-slate-500 mb-1.5">الجنسية</label>
                <select name="nationality_id"
                        class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">الكل</option>
                    @foreach($nationalities as $nat)
                    <option value="{{ $nat->id }}" {{ request('nationality_id') == $nat->id ? 'selected' : '' }}>{{ $nat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[180px]">
                <label class="block text-xs font-medium text-slate-500 mb-1.5">بحث</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="الاسم، الجوال، الإيميل..."
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div class="flex gap-2">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg">بحث</button>
                <a href="{{ route('admin.agents.index') }}"
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
                    <th class="px-4 py-3 text-right font-medium">الجوال</th>
                    <th class="px-4 py-3 text-right font-medium">الإيميل</th>
                    <th class="px-4 py-3 text-right font-medium">الجنسية</th>
                    <th class="px-4 py-3 text-right font-medium">المستند</th>
                    <th class="px-4 py-3 text-right font-medium">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($agents as $agent)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3 text-slate-400">{{ $agents->firstItem() + $loop->index }}</td>
                    <td class="px-4 py-3 font-medium text-slate-800">{{ $agent->name }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $agent->phone }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $agent->email ?? '—' }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $agent->nationality?->name ?? '—' }}</td>
                    <td class="px-4 py-3">
                        @if($agent->document)
                        <a href="{{ Storage::disk('public')->url($agent->document) }}" target="_blank"
                           class="text-blue-600 hover:text-blue-800 text-xs flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            PDF
                        </a>
                        @else
                        <span class="text-slate-300 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            @if(Auth::guard('admin')->user()->isSuperAdmin() || Auth::guard('admin')->user()->hasPermission('agents.view'))
                            <a href="{{ route('admin.agents.show', $agent->id) }}"
                               class="text-slate-400 hover:text-blue-600" title="عرض">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            @endif
                            @if(Auth::guard('admin')->user()->isSuperAdmin() || Auth::guard('admin')->user()->hasPermission('agents.edit'))
                            <a href="{{ route('admin.agents.edit', $agent->id) }}"
                               class="text-slate-400 hover:text-amber-600" title="تعديل">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            @endif
                            @if(Auth::guard('admin')->user()->isSuperAdmin() || Auth::guard('admin')->user()->hasPermission('agents.delete'))
                            <form action="{{ route('admin.agents.destroy', $agent->id) }}" method="POST"
                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا الوكيل؟')">
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
                <tr><td colspan="7" class="px-4 py-10 text-center text-slate-400">لا توجد بيانات</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($agents->hasPages())
    <div class="px-4 py-3 border-t border-slate-100">{{ $agents->withQueryString()->links() }}</div>
    @endif
</div>

<!-- Trashed -->
@if($trashed->count() && (Auth::guard('admin')->user()->isSuperAdmin() || Auth::guard('admin')->user()->hasPermission('agents.delete')))
<div class="mt-6 bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden" x-data="{ open: false }">
    <button @click="open = !open" class="w-full flex items-center justify-between px-5 py-4 text-sm font-medium text-slate-600 hover:bg-slate-50">
        <span>الوكلاء المحذوفون ({{ $trashed->count() }})</span>
        <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
    </button>
    <div x-show="open" x-collapse class="border-t border-slate-100">
        <table class="w-full text-sm">
            <tbody class="divide-y divide-slate-100">
                @foreach($trashed as $agent)
                <tr class="bg-red-50">
                    <td class="px-4 py-3 text-slate-500">{{ $agent->name }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ $agent->phone }}</td>
                    <td class="px-4 py-3">
                        <form action="{{ route('admin.agents.restore', $agent->id) }}" method="POST">
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
