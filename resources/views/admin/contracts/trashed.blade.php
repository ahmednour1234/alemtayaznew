@extends('admin.layouts.app')
@section('title', 'عقود محذوفة')
@section('content')
<div class="w-full">

    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-red-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h2 class="text-xl font-bold text-slate-800">العقود المحذوفة</h2>
        </div>
        <a href="{{ route('admin.contracts.index') }}" class="flex items-center gap-2 text-sm text-slate-600 bg-white border border-slate-200 rounded-xl px-4 py-2 shadow-sm hover:text-blue-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
            كل العقود
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3 mb-5">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-semibold uppercase tracking-wide">
                        <th class="px-4 py-3 text-right">رقم العقد</th>
                        <th class="px-4 py-3 text-right">العميل</th>
                        <th class="px-4 py-3 text-right">الفرع</th>
                        <th class="px-4 py-3 text-right">الحالة</th>
                        <th class="px-4 py-3 text-right">تاريخ الحذف</th>
                        <th class="px-4 py-3 text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($contracts as $c)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-4 py-3 font-mono font-semibold text-slate-700">{{ $c->contract_number }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $c->client?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $c->branch?->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded-full bg-slate-100 text-slate-600">{{ $c->status_label }}</span>
                        </td>
                        <td class="px-4 py-3 text-slate-400 text-xs">{{ $c->deleted_at->format('Y/m/d H:i') }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <form action="{{ route('admin.contracts.restore', $c->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-xs bg-green-50 hover:bg-green-100 text-green-700 border border-green-200 rounded-lg px-3 py-1.5 transition">استعادة</button>
                                </form>
                                <form action="{{ route('admin.contracts.force-delete', $c->id) }}" method="POST"
                                      onsubmit="return confirm('سيتم حذف العقد نهائياً ولا يمكن التراجع. متأكد؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 rounded-lg px-3 py-1.5 transition">حذف نهائي</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-12 text-slate-400">لا توجد عقود محذوفة</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($contracts->hasPages())
        <div class="px-4 py-3 border-t border-slate-100">{{ $contracts->links() }}</div>
        @endif
    </div>
</div>
@endsection
