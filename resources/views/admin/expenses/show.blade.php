@extends('admin.layouts.app')
@section('title', 'تفاصيل المصروف')
@section('content')
<div class="w-full">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.expenses.index') }}" class="text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-slate-800">تفاصيل المصروف</h2>
        @if($expense->status === 'approved')
            <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs">معتمد</span>
        @elseif($expense->status === 'pending')
            <span class="px-2 py-0.5 bg-orange-100 text-orange-700 rounded-full text-xs">معلق</span>
        @else
            <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs">مرفوض</span>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div><p class="text-xs text-slate-400">الفرع</p><p class="font-medium mt-0.5">{{ $expense->branch?->name }}</p></div>
            <div><p class="text-xs text-slate-400">نوع المصروف</p><p class="font-medium mt-0.5">{{ $expense->expenseType?->name }}</p></div>
            <div><p class="text-xs text-slate-400">المبلغ</p><p class="font-bold text-red-600 text-lg mt-0.5">{{ number_format($expense->amount, 2) }} ريال</p></div>
            <div><p class="text-xs text-slate-400">التاريخ</p><p class="font-medium mt-0.5">{{ $expense->date?->format('Y-m-d') }}</p></div>
            @if($expense->approved_at)
            <div><p class="text-xs text-slate-400">تاريخ الاعتماد</p><p class="font-medium mt-0.5">{{ $expense->approved_at?->format('Y-m-d H:i') }}</p></div>
            <div><p class="text-xs text-slate-400">اعتمد بواسطة</p><p class="font-medium mt-0.5">{{ $expense->approver?->name }}</p></div>
            @endif
        </div>
        @if($expense->rejection_reason)
        <div class="bg-red-50 rounded-lg p-3">
            <p class="text-xs text-red-500">سبب الرفض</p>
            <p class="text-sm mt-0.5">{{ $expense->rejection_reason }}</p>
        </div>
        @endif
        @if($expense->description)
        <div><p class="text-xs text-slate-400">الوصف</p><p class="mt-0.5">{{ $expense->description }}</p></div>
        @endif
        @if($expense->attachment)
        <div><p class="text-xs text-slate-400">المرفق</p><a href="{{ Storage::url($expense->attachment) }}" target="_blank" class="text-blue-600 text-sm hover:underline mt-0.5 block">عرض المرفق</a></div>
        @endif

        <!-- Actions -->
        <div class="flex flex-wrap gap-3 pt-4 border-t" x-data="{ rejectModal: false }">
            @if($expense->isPending())
            <form action="{{ route('admin.expenses.approve', $expense->id) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-sm px-5 py-2 rounded-lg">اعتماد</button>
            </form>
            <button @click="rejectModal = true" class="bg-red-600 hover:bg-red-700 text-white text-sm px-5 py-2 rounded-lg">رفض</button>
            <a href="{{ route('admin.expenses.edit', $expense->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm px-5 py-2 rounded-lg">تعديل</a>
            @endif
            <a href="{{ route('admin.expenses.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm px-5 py-2 rounded-lg">رجوع</a>

            <!-- Reject Modal -->
            <div x-show="rejectModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
                    <h3 class="text-lg font-semibold mb-4">سبب الرفض</h3>
                    <form action="{{ route('admin.expenses.reject', $expense->id) }}" method="POST">
                        @csrf
                        <textarea name="rejection_reason" rows="3" required placeholder="أدخل سبب الرفض..."
                                  class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 mb-4"></textarea>
                        <div class="flex gap-3">
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-sm px-5 py-2 rounded-lg">رفض</button>
                            <button type="button" @click="rejectModal = false" class="bg-slate-200 text-slate-700 text-sm px-5 py-2 rounded-lg">إلغاء</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

