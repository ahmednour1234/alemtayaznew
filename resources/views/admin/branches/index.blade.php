@extends('admin.layouts.app')
@section('title', 'الفروع')
@section('content')
@php
    $totalBranches   = \App\Models\Branch::count();
    $activeBranches  = \App\Models\Branch::where('active', true)->count();
    $inactiveBranches= \App\Models\Branch::where('active', false)->count();
    $citiesCount     = \App\Models\Branch::whereNotNull('city')->distinct()->count('city');
@endphp

<!-- Stat Cards -->
<div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    <!-- Total -->
    <div class="bg-white rounded-2xl p-5 flex items-center gap-4 shadow-sm">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:#eff6ff">
            <svg class="w-6 h-6" style="color:#2563eb" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path d="M3 21h18M3 7l9-4 9 4M4 7v14M20 7v14M9 21V11h6v10"/>
            </svg>
        </div>
        <div>
            <p class="text-2xl font-bold text-slate-800">{{ $totalBranches }}</p>
            <p class="text-xs text-slate-400 mt-0.5">إجمالي الفروع</p>
            <p class="text-[11px] text-blue-500 mt-0.5">فرع مسجل</p>
        </div>
    </div>
    <!-- Active -->
    <div class="bg-white rounded-2xl p-5 flex items-center gap-4 shadow-sm">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:#f0fdf4">
            <svg class="w-6 h-6" style="color:#16a34a" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
        </div>
        <div>
            <p class="text-2xl font-bold text-slate-800">{{ $activeBranches }}</p>
            <p class="text-xs text-slate-400 mt-0.5">الفروع النشطة</p>
            <p class="text-[11px] text-green-500 mt-0.5">نشط</p>
        </div>
    </div>
    <!-- Inactive -->
    <div class="bg-white rounded-2xl p-5 flex items-center gap-4 shadow-sm">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:#fff1f2">
            <svg class="w-6 h-6" style="color:#dc2626" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
            </svg>
        </div>
        <div>
            <p class="text-2xl font-bold text-slate-800">{{ $inactiveBranches }}</p>
            <p class="text-xs text-slate-400 mt-0.5">الفروع غير النشطة</p>
            <p class="text-[11px] text-red-400 mt-0.5">غير نشط</p>
        </div>
    </div>
    <!-- Cities -->
    <div class="bg-white rounded-2xl p-5 flex items-center gap-4 shadow-sm">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:#faf5ff">
            <svg class="w-6 h-6" style="color:#7c3aed" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                <circle cx="12" cy="9" r="2.5"/>
            </svg>
        </div>
        <div>
            <p class="text-2xl font-bold text-slate-800">{{ $citiesCount }}</p>
            <p class="text-xs text-slate-400 mt-0.5">المدن</p>
            <p class="text-[11px] text-purple-500 mt-0.5">مدن مختلفة</p>
        </div>
    </div>
</div>

<!-- Main content: Table + optional Add Panel -->
<div x-data="{ showPanel: false, editMode: false, editId: null }" class="flex gap-4">

    <!-- Table card -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden flex-1 min-w-0">

        <!-- Table header bar -->
        <div class="px-5 py-4 flex flex-wrap items-center justify-between gap-3 border-b border-slate-100">
            <p class="font-semibold text-slate-700 text-sm">قائمة الفروع</p>
            <div class="flex items-center gap-2 flex-wrap">
                <!-- Search -->
                <form method="GET" class="flex items-center gap-2">
                    <div class="relative">
                        <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="ابحث عن فرع..."
                               class="bg-slate-50 border border-slate-200 rounded-lg pr-8 pl-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-blue-300 w-44">
                    </div>
                    <select name="active" class="bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-blue-300">
                        <option value="">الكل</option>
                        <option value="1" {{ request('active') === '1' ? 'selected' : '' }}>نشط</option>
                        <option value="0" {{ request('active') === '0' ? 'selected' : '' }}>غير نشط</option>
                    </select>
                    <button type="submit" class="w-7 h-7 flex items-center justify-center bg-slate-100 rounded-lg text-slate-500 hover:bg-slate-200">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </button>
                </form>
                <!-- Export -->
                <a href="{{ route('admin.branches.index', array_merge(request()->query(), ['export' => 1])) }}"
                   class="flex items-center gap-1.5 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs px-3 py-1.5 rounded-lg font-medium">
                    <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v14M5 9l7 7 7-7"/><path d="M3 21h18"/></svg>
                    تصدير Excel
                </a>
                <!-- Add Button -->
                <button @click="showPanel = true; editMode = false"
                        class="flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1.5 rounded-lg font-medium transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    إضافة فرع جديد
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead style="background:#f8fafc">
                    <tr class="text-xs text-slate-500 border-b border-slate-100">
                        <th class="px-4 py-3 text-right font-medium">#</th>
                        <th class="px-4 py-3 text-right font-medium">اسم الفرع</th>
                        <th class="px-4 py-3 text-right font-medium">الكود</th>
                        <th class="px-4 py-3 text-right font-medium">المدينة</th>
                        <th class="px-4 py-3 text-right font-medium">المدير</th>
                        <th class="px-4 py-3 text-right font-medium">الهاتف</th>
                        <th class="px-4 py-3 text-right font-medium">الحالة</th>
                        <th class="px-4 py-3 text-right font-medium">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($branches as $branch)
                    <tr class="border-b border-slate-50 hover:bg-blue-50/30 transition-colors">
                        <td class="px-4 py-3 text-slate-400 text-xs">{{ ($branches->currentPage() - 1) * $branches->perPage() + $loop->iteration }}</td>
                        <td class="px-4 py-3">
                            <span class="font-semibold text-slate-700 text-[13px]">{{ $branch->name }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded-md">{{ $branch->code }}</span>
                        </td>
                        <td class="px-4 py-3 text-slate-600 text-[13px]">{{ $branch->city ?? '-' }}</td>
                        <td class="px-4 py-3 text-slate-600 text-[13px]">{{ $branch->manager_name ?? '-' }}</td>
                        <td class="px-4 py-3 text-slate-500 text-[13px] font-mono">{{ $branch->phone ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @if($branch->active)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-green-50 text-green-700 border border-green-200 rounded-full text-xs font-medium">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> نشط
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-red-50 text-red-600 border border-red-200 rounded-full text-xs font-medium">
                                    <span class="w-1.5 h-1.5 bg-red-400 rounded-full"></span> غير نشط
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1">
                                <a href="{{ route('admin.branches.edit', $branch->id) }}"
                                   class="w-7 h-7 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition" title="تعديل">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.branches.destroy', $branch->id) }}" method="POST" class="inline"
                                      onsubmit="return confirm('هل أنت متأكد من حذف هذا الفرع؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="w-7 h-7 flex items-center justify-center rounded-lg bg-red-50 text-red-500 hover:bg-red-100 transition" title="حذف">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6l-1 14H6L5 6M10 11v6M14 11v6M9 6V4h6v2"/>
                                        </svg>
                                    </button>
                                </form>
                                <!-- More actions -->
                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = !open"
                                            class="w-7 h-7 flex items-center justify-center rounded-lg bg-slate-50 text-slate-500 hover:bg-slate-100 transition">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                            <circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/>
                                        </svg>
                                    </button>
                                    <div x-show="open" @click.outside="open = false" x-cloak
                                         class="absolute left-0 top-8 w-40 bg-white rounded-xl shadow-lg border border-slate-100 py-1 z-20 text-xs">
                                        <a href="{{ route('admin.branches.show', $branch->id) }}"
                                           class="flex items-center gap-2 px-3 py-2 hover:bg-slate-50 text-slate-600">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            عرض التفاصيل
                                        </a>
                                        <form action="{{ route('admin.branches.toggle', $branch->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 hover:bg-slate-50 text-slate-600">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18.364 5.636a9 9 0 11-12.728 0"/><line x1="12" y1="3" x2="12" y2="12"/></svg>
                                                {{ $branch->active ? 'تعطيل' : 'تفعيل' }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-4 py-12 text-center text-slate-400">لا توجد فروع مسجلة</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($branches->hasPages())
        <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span>عرض {{ $branches->firstItem() }} إلى {{ $branches->lastItem() }} من {{ $branches->total() }} نتيجة</span>
            <div>{{ $branches->withQueryString()->links() }}</div>
        </div>
        @endif
    </div>

    <!-- Add / Quick panel -->
    <div x-show="showPanel" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-x-4"
         x-transition:enter-end="opacity-100 translate-x-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0 translate-x-4"
         class="bg-white rounded-2xl shadow-sm w-80 flex-shrink-0 overflow-hidden self-start">

        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
            <p class="font-semibold text-slate-700 text-sm">إضافة فرع جديد</p>
            <button @click="showPanel = false" class="w-6 h-6 flex items-center justify-center rounded-full text-slate-400 hover:bg-slate-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <form action="{{ route('admin.branches.store') }}" method="POST" class="px-5 py-4 space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">اسم الفرع <span class="text-red-500">*</span></label>
                <input type="text" name="name" required placeholder="أدخل اسم الفرع"
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 bg-slate-50">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">كود الفرع <span class="text-red-500">*</span></label>
                <input type="text" name="code" required placeholder="مثال: MAIN-001"
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-300 bg-slate-50">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">المدينة <span class="text-red-500">*</span></label>
                <input type="text" name="city" required placeholder="اختر المدينة"
                       list="cities-list"
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 bg-slate-50">
                <datalist id="cities-list">
                    @foreach(\App\Models\Branch::distinct()->whereNotNull('city')->pluck('city') as $city)
                    <option value="{{ $city }}">
                    @endforeach
                </datalist>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">رقم الهاتف <span class="text-red-500">*</span></label>
                <input type="text" name="phone" required placeholder="05xxxxxxxx"
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-300 bg-slate-50">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">العنوان <span class="text-red-500">*</span></label>
                <textarea name="address" required rows="2" placeholder="أدخل عنوان الفرع بالتفصيل"
                          class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 bg-slate-50 resize-none"></textarea>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">اسم المدير</label>
                <input type="text" name="manager_name" placeholder="أدخل اسم مدير الفرع"
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 bg-slate-50">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">الحالة <span class="text-red-500">*</span></label>
                <select name="active"
                        class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 bg-slate-50">
                    <option value="1">نشط</option>
                    <option value="0">غير نشط</option>
                </select>
            </div>
            <div class="flex gap-2 pt-1">
                <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm py-2 rounded-lg font-medium transition">
                    حفظ الفرع
                </button>
                <button type="button" @click="showPanel = false"
                        class="px-4 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm py-2 rounded-lg font-medium transition">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Trashed -->
@if($trashed->isNotEmpty())
<div x-data="{ open: false }" class="mt-5">
    <button @click="open = !open" class="text-xs text-slate-500 hover:text-red-600 flex items-center gap-1.5">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/></svg>
        المحذوفة ({{ $trashed->count() }})
    </button>
    <div x-show="open" class="mt-3 bg-white rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-red-50 text-slate-500 text-xs border-b">
                <tr>
                    <th class="px-4 py-2 text-right">الاسم</th>
                    <th class="px-4 py-2 text-right">الرمز</th>
                    <th class="px-4 py-2 text-right">تاريخ الحذف</th>
                    <th class="px-4 py-2 text-right">استعادة</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($trashed as $branch)
                <tr class="hover:bg-red-50">
                    <td class="px-4 py-2 line-through text-slate-400">{{ $branch->name }}</td>
                    <td class="px-4 py-2 text-slate-400 font-mono text-xs">{{ $branch->code }}</td>
                    <td class="px-4 py-2 text-xs text-slate-400">{{ $branch->deleted_at?->format('Y-m-d') }}</td>
                    <td class="px-4 py-2">
                        <form action="{{ route('admin.branches.restore', $branch->id) }}" method="POST" class="inline">
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

