@extends('admin.layouts.app')
@section('title', 'التحويلات المالية')
@section('content')

<div class="flex justify-between items-center mb-5">
    <h2 class="text-xl font-bold text-slate-800">التحويلات المالية</h2>
    <a href="{{ route('admin.transfers.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        إضافة تحويل
    </a>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl p-5 shadow-sm mb-4 border border-slate-100">
    <form method="GET">
        <div class="flex flex-wrap gap-3 items-end">

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
                <a href="{{ route('admin.transfers.index') }}"
                   style="font-size:13px;color:#94a3b8;text-decoration:none;white-space:nowrap;
                          padding:8px 10px;border-radius:8px;"
                   onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#94a3b8'">
                    مسح
                </a>
            </div>

        </div>
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-xs border-b">
                <tr>
                    <th class="px-4 py-3 text-right">#</th>
                    <th class="px-4 py-3 text-right">من فرع</th>
                    <th class="px-4 py-3 text-right">إلى فرع</th>
                    <th class="px-4 py-3 text-right">المبلغ</th>
                    <th class="px-4 py-3 text-right">التاريخ</th>
                    <th class="px-4 py-3 text-right">الحالة</th>
                    <th class="px-4 py-3 text-right">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($transfers as $transfer)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 text-slate-400">{{ $transfers->firstItem() + $loop->index }}</td>
                    <td class="px-4 py-3">{{ $transfer->fromBranch?->name ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $transfer->toBranch?->name ?? '-' }}</td>
                    <td class="px-4 py-3 font-semibold text-blue-600">{{ number_format($transfer->amount, 2) }}</td>
                    <td class="px-4 py-3">{{ $transfer->date?->format('Y-m-d') }}</td>
                    <td class="px-4 py-3">
                        @if($transfer->status === 'approved')
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs">معتمد</span>
                        @elseif($transfer->status === 'pending')
                            <span class="px-2 py-0.5 bg-orange-100 text-orange-700 rounded-full text-xs">معلق</span>
                        @else
                            <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs">مرفوض</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.transfers.show', $transfer->id) }}" class="text-slate-500 hover:text-blue-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            @if($transfer->isPending())
                            <a href="{{ route('admin.transfers.edit', $transfer->id) }}" class="text-slate-500 hover:text-yellow-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form action="{{ route('admin.transfers.approve', $transfer->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-xs text-green-600 hover:underline">موافقة</button>
                            </form>
                            @endif
                            <form action="{{ route('admin.transfers.destroy', $transfer->id) }}" method="POST" class="inline"
                                  onsubmit="return confirm('حذف هذا التحويل؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-slate-500 hover:text-red-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">لا توجد تحويلات</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($transfers->hasPages())
    <div class="px-4 py-3 border-t">{{ $transfers->withQueryString()->links() }}</div>
    @endif
</div>

@if($trashed->isNotEmpty())
<div x-data="{ open: false }" class="mt-6">
    <button @click="open = !open" class="text-sm text-slate-500 hover:text-red-600">المحذوفة ({{ $trashed->count() }})</button>
    <div x-show="open" class="mt-3 bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-red-50 text-xs text-slate-500 border-b">
                <tr>
                    <th class="px-4 py-2 text-right">من فرع</th>
                    <th class="px-4 py-2 text-right">إلى فرع</th>
                    <th class="px-4 py-2 text-right">المبلغ</th>
                    <th class="px-4 py-2 text-right">استعادة</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($trashed as $transfer)
                <tr>
                    <td class="px-4 py-2 text-slate-400">{{ $transfer->fromBranch?->name }}</td>
                    <td class="px-4 py-2 text-slate-400">{{ $transfer->toBranch?->name }}</td>
                    <td class="px-4 py-2 text-slate-400">{{ number_format($transfer->amount, 2) }}</td>
                    <td class="px-4 py-2">
                        <form action="{{ route('admin.transfers.restore', $transfer->id) }}" method="POST" class="inline">
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

