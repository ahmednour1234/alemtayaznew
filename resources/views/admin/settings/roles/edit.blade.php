@extends('admin.layouts.app')
@section('title', 'تعديل الدور')
@section('content')
<div class="w-full">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.settings.roles.index') }}" class="text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-slate-800">تعديل: {{ $role->name }}</h2>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form action="{{ route('admin.settings.roles.update', $role->id) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">الاسم <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $role->name) }}" required
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">الرمز <span class="text-red-500">*</span></label>
                    <input type="text" name="slug" value="{{ old('slug', $role->slug) }}" required
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">الصلاحيات</label>
                @php
                    $grouped = $permissions->groupBy(fn($p) => explode('.', $p->slug)[0]);
                    $currentPerms = old('permissions', $role->permissions->pluck('id')->toArray());
                    $groupLabels = [
                        'branches'           => 'الفروع',
                        'income-types'       => 'أنواع الدخل',
                        'expense-types'      => 'أنواع المصاريف',
                        'nationalities'      => 'الجنسيات',
                        'airports'           => 'المطارات',
                        'incomes'            => 'الإيرادات',
                        'expenses'           => 'المصاريف',
                        'transfers'          => 'التحويلات',
                        'reports'            => 'التقارير',
                        'admins'             => 'المديرين',
                        'roles'              => 'الأدوار والصلاحيات',
                        'housing-rentals'    => 'تأجير العاملات',
                        'housing-settlements'=> 'التسويات',
                    ];
                @endphp
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($grouped as $group => $groupPerms)
                    @php $groupId = 'grp_' . Str::slug($group); @endphp
                    <div class="bg-slate-50 rounded-lg p-3">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-xs font-bold text-slate-700">{{ $groupLabels[$group] ?? strtoupper($group) }}</p>
                            <label class="flex items-center gap-1 text-xs text-slate-500 cursor-pointer">
                                <input type="checkbox" class="rounded" onchange="document.querySelectorAll('.' + '{{ $groupId }}').forEach(c => c.checked = this.checked)">
                                الكل
                            </label>
                        </div>
                        <div class="space-y-1.5">
                            @foreach($groupPerms as $perm)
                            <label class="flex items-center gap-2 text-xs">
                                <input type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                                       class="rounded {{ $groupId }}"
                                       {{ in_array($perm->id, $currentPerms) ? 'checked' : '' }}>
                                {{ $perm->name }}
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-6 py-2.5 rounded-lg">تحديث</button>
                <a href="{{ route('admin.settings.roles.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm px-6 py-2.5 rounded-lg">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection

