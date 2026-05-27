@extends('admin.layouts.app')
@section('title', 'الأدوار')
@section('content')

<div class="flex justify-between items-center mb-5">
    <h2 class="text-xl font-bold text-slate-800">الأدوار</h2>
    <a href="{{ route('admin.settings.roles.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        إضافة دور
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-xs border-b">
            <tr>
                <th class="px-4 py-3 text-right">#</th>
                <th class="px-4 py-3 text-right">الاسم</th>
                <th class="px-4 py-3 text-right">الرمز</th>
                <th class="px-4 py-3 text-right">الصلاحيات</th>
                <th class="px-4 py-3 text-right">الإجراءات</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($roles as $role)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 text-slate-400">{{ $loop->iteration }}</td>
                <td class="px-4 py-3 font-medium">{{ $role->name }}</td>
                <td class="px-4 py-3 font-mono text-purple-600">{{ $role->slug }}</td>
                <td class="px-4 py-3 text-xs text-slate-500">{{ $role->permissions_count ?? $role->permissions->count() }} صلاحية</td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        <a href="{{ route('admin.settings.roles.edit', $role->id) }}" class="text-slate-500 hover:text-yellow-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        @if($role->slug !== 'super-admin')
                        <form action="{{ route('admin.settings.roles.destroy', $role->id) }}" method="POST" class="inline"
                              onsubmit="return confirm('حذف هذا الدور؟')">
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
            <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">لا توجد أدوار</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

