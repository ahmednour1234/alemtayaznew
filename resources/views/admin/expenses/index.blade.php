@extends('admin.layouts.app')
@section('title', 'المصاريف')
@section('content')

<div class="flex justify-between items-center mb-5">
    <h2 class="text-xl font-bold text-slate-800">المصاريف</h2>
    <div class="flex gap-2">
        <a href="{{ route('admin.expenses.export', request()->query()) }}" class="bg-green-600 hover:bg-green-700 text-white text-sm px-3 py-2 rounded-lg">تصدير Excel</a>
        <a href="{{ route('admin.expenses.template') }}" class="bg-slate-600 hover:bg-slate-700 text-white text-sm px-3 py-2 rounded-lg">قالب الاستيراد</a>
        <button onclick="document.getElementById('importModal').classList.remove('hidden')"
                class="bg-purple-600 hover:bg-purple-700 text-white text-sm px-3 py-2 rounded-lg">استيراد</button>
        <button onclick="document.getElementById('recruitmentModal').classList.remove('hidden')"
                class="bg-teal-600 hover:bg-teal-700 text-white text-sm px-3 py-2 rounded-lg">استيراد كشف الاستقدام</button>
        <a href="{{ route('admin.expenses.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg flex items-center gap-1">
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

            {{-- Status --}}
            <div class="flex-1 min-w-[150px]">
                <label class="block text-xs font-medium text-slate-500 mb-1.5">الحالة</label>
                <div style="position:relative;">
                    <svg style="position:absolute;right:10px;top:50%;transform:translateY(-50%);pointer-events:none;color:#94a3b8;"
                         width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/>
                    </svg>
                    <select name="status"
                            style="width:100%;padding:8px 34px 8px 32px;border:1.5px solid #e2e8f0;border-radius:8px;
                                   font-size:13px;color:#0f172a;background:#fff;outline:none;
                                   font-family:Cairo,sans-serif;appearance:none;-webkit-appearance:none;cursor:pointer;">
                        <option value="">الكل</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>معلق</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>معتمد</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>مرفوض</option>
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
                <a href="{{ route('admin.expenses.index') }}"
                   style="font-size:13px;color:#94a3b8;text-decoration:none;white-space:nowrap;
                          padding:8px 10px;border-radius:8px;"
                   onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#94a3b8'">
                    مسح
                </a>
            </div>

        </div>
    </form>
</div>

<!-- Totals by Status -->
<div class="grid grid-cols-3 gap-3 mb-4">
    <div class="bg-orange-50 border border-orange-200 rounded-lg px-4 py-2.5 text-center">
        <p class="text-xs text-orange-600">معلق</p>
        <p class="font-bold text-orange-700">{{ number_format($totals['pending'] ?? 0, 2) }}</p>
    </div>
    <div class="bg-green-50 border border-green-200 rounded-lg px-4 py-2.5 text-center">
        <p class="text-xs text-green-600">معتمد</p>
        <p class="font-bold text-green-700">{{ number_format($totals['approved'] ?? 0, 2) }}</p>
    </div>
    <div class="bg-red-50 border border-red-200 rounded-lg px-4 py-2.5 text-center">
        <p class="text-xs text-red-600">مرفوض</p>
        <p class="font-bold text-red-700">{{ number_format($totals['rejected'] ?? 0, 2) }}</p>
    </div>
</div>

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
                    <th class="px-4 py-3 text-right">الحالة</th>
                    <th class="px-4 py-3 text-right">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($expenses as $expense)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 text-slate-400">{{ $expenses->firstItem() + $loop->index }}</td>
                    <td class="px-4 py-3">{{ $expense->branch?->name }}</td>
                    <td class="px-4 py-3">{{ $expense->expenseType?->name }}</td>
                    <td class="px-4 py-3 text-slate-600 max-w-[260px]">
                        <div class="line-clamp-2" title="{{ $expense->description }}">
                            {{ $expense->description ?: '—' }}
                        </div>
                    </td>
                    <td class="px-4 py-3 font-semibold text-red-600">{{ number_format($expense->amount, 2) }}</td>
                    <td class="px-4 py-3">{{ $expense->date?->format('Y-m-d') }}</td>
                    <td class="px-4 py-3">
                        @if($expense->status === 'approved')
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs">معتمد</span>
                        @elseif($expense->status === 'pending')
                            <span class="px-2 py-0.5 bg-orange-100 text-orange-700 rounded-full text-xs">معلق</span>
                        @else
                            <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs">مرفوض</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.expenses.show', $expense->id) }}" class="text-slate-500 hover:text-blue-600" title="عرض">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            @if($expense->isPending())
                            <a href="{{ route('admin.expenses.edit', $expense->id) }}" class="text-slate-500 hover:text-yellow-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form action="{{ route('admin.expenses.approve', $expense->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-xs text-green-600 hover:underline">موافقة</button>
                            </form>
                            @endif
                            <form action="{{ route('admin.expenses.destroy', $expense->id) }}" method="POST" class="inline"
                                  onsubmit="return confirm('حذف هذا المصروف؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-slate-500 hover:text-red-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-4 py-8 text-center text-slate-400">لا توجد مصاريف</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($expenses->hasPages())
    <div class="px-4 py-3 border-t">{{ $expenses->withQueryString()->links() }}</div>
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
                    <th class="px-4 py-2 text-right">استعادة</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($trashed as $expense)
                <tr>
                    <td class="px-4 py-2 text-slate-400">{{ $expense->branch?->name }}</td>
                    <td class="px-4 py-2 text-slate-400">{{ $expense->description ?: '—' }}</td>
                    <td class="px-4 py-2 text-slate-400">{{ number_format($expense->amount, 2) }}</td>
                    <td class="px-4 py-2">
                        <form action="{{ route('admin.expenses.restore', $expense->id) }}" method="POST" class="inline">
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
        <form action="{{ route('admin.expenses.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">ملف Excel</label>
                <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                       class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
                <p class="text-xs text-slate-400 mt-1">
                    استخدم أعمدة: record_type, branch_name, type_name, amount, date, payment_method.
                    <a href="{{ route('admin.expenses.template') }}" class="text-blue-600 hover:underline">تحميل القالب</a>
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

{{-- Recruitment Statement Import Modal --}}
<div id="recruitmentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold mb-1">استيراد كشف الاستقدام</h3>
        <p class="text-xs text-slate-500 mb-4">كل صف سينشئ: إيراد استقدام + تكاليف استقدام + ضريبة العقود تلقائياً</p>
        <form action="{{ route('admin.expenses.recruitment-import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">ملف Excel</label>
                <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                       class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
                <p class="text-xs text-slate-400 mt-1">
                    الأعمدة المطلوبة: الفرع، تاريخ بداية العقد، ايراد استقدام، تكاليف الاستقدام مصاريف، مباشرة للعقود الضريبية.
                    <a href="{{ route('admin.expenses.recruitment-template') }}" class="text-teal-600 hover:underline">تحميل القالب</a>
                </p>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white text-sm px-5 py-2 rounded-lg">استيراد</button>
                <button type="button" onclick="document.getElementById('recruitmentModal').classList.add('hidden')"
                        class="bg-slate-200 text-slate-700 text-sm px-5 py-2 rounded-lg">إلغاء</button>
            </div>
        </form>
    </div>
</div>

@endsection
