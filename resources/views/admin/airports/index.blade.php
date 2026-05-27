@extends('admin.layouts.app')
@section('title', 'المطارات')
@section('content')

<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-slate-800">المطارات السعودية</h2>
    <a href="{{ route('admin.airports.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        إضافة مطار
    </a>
</div>

@if(session('success'))
<div class="mb-4 px-4 py-3 bg-green-100 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
@endif

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-xs border-b">
            <tr>
                <th class="px-4 py-3 text-right">#</th>
                <th class="px-4 py-3 text-right">اسم المطار</th>
                <th class="px-4 py-3 text-right">كود IATA</th>
                <th class="px-4 py-3 text-right">المدينة</th>
                <th class="px-4 py-3 text-right">الحالة</th>
                <th class="px-4 py-3 text-right">الإجراءات</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($airports as $airport)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 text-slate-400">{{ $loop->iteration }}</td>
                <td class="px-4 py-3 font-medium">{{ $airport->name }}</td>
                <td class="px-4 py-3">
                    @if($airport->code)
                    <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs font-mono font-bold">{{ $airport->code }}</span>
                    @else
                    <span class="text-slate-300">—</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-slate-500">{{ $airport->city ?? '—' }}</td>
                <td class="px-4 py-3">
                    @if($airport->active)
                        <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs">نشط</span>
                    @else
                        <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs">غير نشط</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        <a href="{{ route('admin.airports.edit', $airport->id) }}" class="text-slate-500 hover:text-yellow-600" title="تعديل">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <form action="{{ route('admin.airports.toggle', $airport->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-slate-500 hover:text-purple-600" title="{{ $airport->active ? 'تعطيل' : 'تفعيل' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 11-12.728 0M12 3v9"/></svg>
                            </button>
                        </form>
                        <form action="{{ route('admin.airports.destroy', $airport->id) }}" method="POST" class="inline"
                              onsubmit="return confirm('حذف هذا المطار؟')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-slate-500 hover:text-red-600" title="حذف">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">لا توجد مطارات</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($airports->hasPages())
    <div class="px-4 py-3 border-t">{{ $airports->withQueryString()->links() }}</div>
    @endif
</div>

@if($trashed->isNotEmpty())
<div x-data="{ open: false }" class="mt-6">
    <button @click="open = !open" class="text-sm text-slate-500 hover:text-red-600 flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7"/></svg>
        المحذوفة ({{ $trashed->count() }})
    </button>
    <div x-show="open" class="mt-3 bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-red-50 text-xs text-slate-500 border-b">
                <tr>
                    <th class="px-4 py-2 text-right">المطار</th>
                    <th class="px-4 py-2 text-right">الكود</th>
                    <th class="px-4 py-2 text-right">تاريخ الحذف</th>
                    <th class="px-4 py-2 text-right">استعادة</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($trashed as $apt)
                <tr>
                    <td class="px-4 py-2 text-slate-500">{{ $apt->name }}</td>
                    <td class="px-4 py-2"><span class="font-mono text-xs">{{ $apt->code }}</span></td>
                    <td class="px-4 py-2 text-slate-400 text-xs">{{ $apt->deleted_at->format('Y-m-d') }}</td>
                    <td class="px-4 py-2">
                        <form action="{{ route('admin.airports.restore', $apt->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-xs text-blue-600 hover:text-blue-800">استعادة</button>
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
