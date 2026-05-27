@extends('admin.layouts.app')
@section('title', 'المديرون')
@section('content')

<div class="flex justify-between items-center mb-5">
    <h2 class="text-xl font-bold text-slate-800">المديرون</h2>
    <a href="{{ route('admin.settings.admins.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        إضافة مدير
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-xs border-b">
            <tr>
                <th class="px-4 py-3 text-right">#</th>
                <th class="px-4 py-3 text-right">الاسم</th>
                <th class="px-4 py-3 text-right">البريد</th>
                <th class="px-4 py-3 text-right">الأدوار</th>
                <th class="px-4 py-3 text-right">الحالة</th>
                <th class="px-4 py-3 text-right">الإجراءات</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($admins as $admin)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 text-slate-400">{{ $loop->iteration }}</td>
                <td class="px-4 py-3 font-medium">{{ $admin->name }}</td>
                <td class="px-4 py-3 text-slate-500">{{ $admin->email }}</td>
                <td class="px-4 py-3">
                    @foreach($admin->roles as $role)
                    <span class="px-2 py-0.5 bg-purple-100 text-purple-700 rounded-full text-xs ml-1">{{ $role->name }}</span>
                    @endforeach
                </td>
                <td class="px-4 py-3">
                    @if($admin->active)
                        <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs">نشط</span>
                    @else
                        <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs">غير نشط</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        <a href="{{ route('admin.settings.admins.edit', $admin->id) }}" class="text-slate-500 hover:text-yellow-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        @if($admin->id !== auth('admin')->id())
                        <form action="{{ route('admin.settings.admins.toggle', $admin->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-slate-500 hover:text-purple-600" title="{{ $admin->active ? 'تعطيل' : 'تفعيل' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 11-12.728 0M12 3v9"/></svg>
                            </button>
                        </form>
                        <form action="{{ route('admin.settings.admins.destroy', $admin->id) }}" method="POST" class="inline"
                              onsubmit="return confirm('حذف هذا المدير؟')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-slate-500 hover:text-red-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7"/></svg>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">لا يوجد مديرون</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($admins->hasPages())
    <div class="px-4 py-3 border-t">{{ $admins->withQueryString()->links() }}</div>
    @endif
</div>

@if($trashed->isNotEmpty())
<div x-data="{ open: false }" class="mt-6">
    <button @click="open = !open" class="text-sm text-slate-500 hover:text-red-600">المحذوفة ({{ $trashed->count() }})</button>
    <div x-show="open" class="mt-3 bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-red-50 text-xs text-slate-500 border-b">
                <tr>
                    <th class="px-4 py-2 text-right">الاسم</th>
                    <th class="px-4 py-2 text-right">البريد</th>
                    <th class="px-4 py-2 text-right">استعادة</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($trashed as $admin)
                <tr>
                    <td class="px-4 py-2 line-through text-slate-400">{{ $admin->name }}</td>
                    <td class="px-4 py-2 text-slate-400">{{ $admin->email }}</td>
                    <td class="px-4 py-2">
                        <form action="{{ route('admin.settings.admins.restore', $admin->id) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
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

