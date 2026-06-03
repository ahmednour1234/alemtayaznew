@extends('admin.layouts.app')
@section('title', 'الإيرادات')
@section('content')

<div class="flex justify-between items-center mb-5">
    <h2 class="text-xl font-bold text-slate-800">الإيرادات</h2>
    <div class="flex gap-2">
        <a href="{{ route('admin.incomes.export', request()->query()) }}"
           class="bg-green-600 hover:bg-green-700 text-white text-sm px-3 py-2 rounded-lg flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            تصدير Excel
        </a>
        <a href="{{ route('admin.incomes.template') }}"
           class="bg-slate-600 hover:bg-slate-700 text-white text-sm px-3 py-2 rounded-lg flex items-center gap-1">
            قالب الاستيراد
        </a>
        <button onclick="document.getElementById('importModal').classList.remove('hidden')"
                class="bg-purple-600 hover:bg-purple-700 text-white text-sm px-3 py-2 rounded-lg">استيراد</button>
        <a href="{{ route('admin.incomes.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            إضافة
        </a>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl p-5 shadow-sm mb-4 border border-slate-100">
    <form method="GET">
        <div class="flex flex-wrap gap-3 items-end">

            {{-- Branch --}}
            <div class="flex-1 min-w-[150px]">
                <label class="block text-xs font-medium text-slate-500 mb-1.5">الفرع</label>
                <div style="position:relative;">
                    <svg style="position:absolute;right:10px;top:50%;transform:translateY(-50%);pointer-events:none;color:#94a3b8;"
                         width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                    <select name="branch_id"
                            style="width:100%;padding:8px 34px 8px 32px;border:1.5px solid #e2e8f0;border-radius:8px;
                                   font-size:13px;color:#0f172a;background:#fff;outline:none;
                                   font-family:Cairo,sans-serif;appearance:none;-webkit-appearance:none;cursor:pointer;">
                        <option value="">الكل</option>
                        @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                    <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);pointer-events:none;color:#94a3b8;"
                         width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </div>
            </div>

            {{-- Income Type --}}
            <div class="flex-1 min-w-[150px]">
                <label class="block text-xs font-medium text-slate-500 mb-1.5">نوع الدخل</label>
                <div style="position:relative;">
                    <svg style="position:absolute;right:10px;top:50%;transform:translateY(-50%);pointer-events:none;color:#94a3b8;"
                         width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/>
                        <path d="M9 9h.01"/><line x1="9" y1="9" x2="15" y2="15"/>
                    </svg>
                    <select name="income_type_id"
                            style="width:100%;padding:8px 34px 8px 32px;border:1.5px solid #e2e8f0;border-radius:8px;
                                   font-size:13px;color:#0f172a;background:#fff;outline:none;
                                   font-family:Cairo,sans-serif;appearance:none;-webkit-appearance:none;cursor:pointer;">
                        <option value="">الكل</option>
                        @foreach($types as $type)
                        <option value="{{ $type->id }}" {{ request('income_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                    <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);pointer-events:none;color:#94a3b8;"
                         width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </div>
            </div>

            {{-- Date From --}}
            <div class="flex-1 min-w-[150px]">
                <label class="block text-xs font-medium text-slate-500 mb-1.5">من تاريخ</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:8px;
                              font-size:13px;color:#0f172a;outline:none;font-family:Cairo,sans-serif;">
            </div>

            {{-- Date To --}}
            <div class="flex-1 min-w-[150px]">
                <label class="block text-xs font-medium text-slate-500 mb-1.5">إلى تاريخ</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:8px;
                              font-size:13px;color:#0f172a;outline:none;font-family:Cairo,sans-serif;">
            </div>

            {{-- Buttons --}}
            <div class="flex items-center gap-2 pb-0.5">
                <button type="submit"
                        style="display:flex;align-items:center;gap:6px;padding:8px 18px;
                               background:#2563eb;color:#fff;border:none;border-radius:8px;
                               font-size:13px;font-weight:600;font-family:Cairo,sans-serif;
                               cursor:pointer;white-space:nowrap;">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8" fill="none" stroke="currentColor"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    بحث
                </button>
                <a href="{{ route('admin.incomes.index') }}"
                   style="font-size:13px;color:#94a3b8;text-decoration:none;white-space:nowrap;
                          padding:8px 10px;border-radius:8px;transition:color .15s;"
                   onmouseover="this.style.color='#ef4444'"
                   onmouseout="this.style.color='#94a3b8'">
                    مسح
                </a>
            </div>

        </div>
    </form>
</div>

<!-- Total -->
@if($total > 0)
<div class="bg-green-50 border border-green-200 rounded-lg px-4 py-3 mb-4 flex justify-between items-center">
    <span class="text-sm text-green-700">إجمالي الإيرادات المصفاة:</span>
    <span class="font-bold text-green-700 text-lg">{{ number_format($total, 2) }} ريال</span>
</div>
@endif

<!-- Table -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-xs border-b">
                <tr>
                    <th class="px-4 py-3 text-right">#</th>
                    <th class="px-4 py-3 text-right">الفرع</th>
                    <th class="px-4 py-3 text-right">النوع</th>
                    <th class="px-4 py-3 text-right">البيان</th>
                    <th class="px-4 py-3 text-right">المبلغ</th>
                    <th class="px-4 py-3 text-right">التاريخ</th>
                    <th class="px-4 py-3 text-right">طريقة الدفع</th>
                    <th class="px-4 py-3 text-right">المرجع</th>
                    <th class="px-4 py-3 text-right">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($incomes as $income)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 text-slate-400">{{ $incomes->firstItem() + $loop->index }}</td>
                    <td class="px-4 py-3">{{ $income->branch?->name }}</td>
                    <td class="px-4 py-3">{{ $income->incomeType?->name }}</td>
                    <td class="px-4 py-3 text-slate-600 max-w-[260px]">
                        <div class="line-clamp-2" title="{{ $income->description }}">
                            {{ $income->description ?: '—' }}
                        </div>
                    </td>
                    <td class="px-4 py-3 font-semibold text-green-600">{{ number_format($income->amount, 2) }}</td>
                    <td class="px-4 py-3">{{ $income->date?->format('Y-m-d') }}</td>
                    <td class="px-4 py-3 text-xs">
                        @php $pm = ['cash'=>'نقد','bank_transfer'=>'تحويل بنكي','check'=>'شيك','other'=>'أخرى']; @endphp
                        {{ $pm[$income->payment_method] ?? $income->payment_method }}
                    </td>
                    <td class="px-4 py-3 text-xs text-slate-400">{{ $income->reference_number ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.incomes.show', $income->id) }}" class="text-slate-500 hover:text-blue-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <a href="{{ route('admin.incomes.edit', $income->id) }}" class="text-slate-500 hover:text-yellow-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form action="{{ route('admin.incomes.destroy', $income->id) }}" method="POST" class="inline"
                                  onsubmit="return confirm('حذف هذا الإيراد؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-slate-500 hover:text-red-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="px-4 py-8 text-center text-slate-400">لا توجد إيرادات</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($incomes->hasPages())
    <div class="px-4 py-3 border-t">{{ $incomes->withQueryString()->links() }}</div>
    @endif
</div>

<!-- Trashed -->
@if($trashed->isNotEmpty())
<div x-data="{ open: false }" class="mt-6">
    <button @click="open = !open" class="text-sm text-slate-500 hover:text-red-600">المحذوفة ({{ $trashed->count() }})</button>
    <div x-show="open" class="mt-3 bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-red-50 text-xs text-slate-500 border-b">
                <tr>
                    <th class="px-4 py-2 text-right">الفرع</th>
                    <th class="px-4 py-2 text-right">البيان</th>
                    <th class="px-4 py-2 text-right">المبلغ</th>
                    <th class="px-4 py-2 text-right">التاريخ</th>
                    <th class="px-4 py-2 text-right">استعادة</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($trashed as $income)
                <tr>
                    <td class="px-4 py-2 text-slate-400">{{ $income->branch?->name }}</td>
                    <td class="px-4 py-2 text-slate-400">{{ $income->description ?: '—' }}</td>
                    <td class="px-4 py-2 text-slate-400">{{ number_format($income->amount, 2) }}</td>
                    <td class="px-4 py-2 text-xs text-slate-400">{{ $income->date?->format('Y-m-d') }}</td>
                    <td class="px-4 py-2">
                        <form action="{{ route('admin.incomes.restore', $income->id) }}" method="POST" class="inline">
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

<!-- Import Modal -->
<div id="importModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold mb-4">استيراد إيرادات ومصروفات من Excel</h3>
        <form action="{{ route('admin.incomes.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">ملف Excel</label>
                <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                       class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
                <p class="text-xs text-slate-400 mt-1">
                    استخدم أعمدة: record_type, branch_name, type_name, amount, date, payment_method.
                    <a href="{{ route('admin.incomes.template') }}" class="text-blue-600 hover:underline">تحميل القالب</a>
                </p>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white text-sm px-5 py-2 rounded-lg">استيراد</button>
                <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')"
                        class="bg-slate-200 text-slate-700 text-sm px-5 py-2 rounded-lg">إلغاء</button>
            </div>
        </form>
    </div>
</div>

@endsection
