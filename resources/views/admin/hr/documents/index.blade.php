@extends('admin.layouts.app')
@section('title', 'وثائق الشركة')
@section('content')

<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-slate-800">وثائق الشركة والموظفين</h2>
    @can('employee-documents.create')
    <a href="{{ route('admin.hr.documents.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        رفع وثيقة
    </a>
    @endcan
</div>

<div class="bg-amber-50 border border-amber-200 text-amber-700 rounded-lg px-4 py-2 text-xs mb-4 flex items-center gap-2">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
    الوثائق مخزّنة في مساحة خاصة ولا تُعرض علناً — التحميل عبر رابط محمي بصلاحية فقط.
</div>

<div class="bg-white rounded-xl p-4 shadow-sm mb-4 border border-slate-100">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">بحث</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="العنوان / النوع"
                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">الموظف</label>
            <select name="employee_id" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                <option value="">الكل</option>
                @foreach($employees as $emp)
                <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">النطاق</label>
            <select name="scope" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                <option value="">الكل</option>
                <option value="company" {{ request('scope') === 'company' ? 'selected' : '' }}>وثائق الشركة العامة</option>
            </select>
        </div>
        <div class="flex items-end">
            <button class="bg-slate-800 hover:bg-slate-900 text-white text-sm px-5 py-2 rounded-lg w-full">تصفية</button>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-xs"><tr>
            <th class="px-4 py-3 text-right font-medium">العنوان</th>
            <th class="px-4 py-3 text-right font-medium">النوع</th>
            <th class="px-4 py-3 text-right font-medium">الموظف</th>
            <th class="px-4 py-3 text-right font-medium">الانتهاء</th>
            <th class="px-4 py-3 text-right font-medium">الحجم</th>
            <th class="px-4 py-3 text-right font-medium">إجراءات</th>
        </tr></thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($documents as $doc)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 font-medium text-slate-800">{{ $doc->title }}</td>
                <td class="px-4 py-3 text-slate-500">{{ $doc->doc_type ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-500">{{ $doc->employee?->name ?? 'وثيقة شركة' }}</td>
                <td class="px-4 py-3 text-slate-500">{{ $doc->expiry_date?->format('Y-m-d') ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-400 text-xs">{{ $doc->size_label }}</td>
                <td class="px-4 py-3">
                    <div class="flex gap-3 text-xs">
                        @can('employee-documents.download')
                        <a href="{{ route('admin.hr.documents.download', $doc->id) }}" class="text-blue-600 hover:underline">تحميل</a>
                        @endcan
                        @can('employee-documents.edit')
                        <a href="{{ route('admin.hr.documents.edit', $doc->id) }}" class="text-slate-500 hover:underline">تعديل</a>
                        @endcan
                        @can('employee-documents.delete')
                        <form method="POST" action="{{ route('admin.hr.documents.destroy', $doc->id) }}" onsubmit="return confirm('حذف الوثيقة؟')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:underline">حذف</button>
                        </form>
                        @endcan
                    </div>
                </td>
            </tr>
            @empty<tr><td colspan="6" class="px-4 py-10 text-center text-slate-400">لا توجد وثائق</td></tr>@endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-slate-100">{{ $documents->links() }}</div>
</div>
@endsection
