@extends('admin.layouts.app')
@section('title', 'تفاصيل الإيراد')
@section('content')
<div class="w-full">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.incomes.index') }}" class="text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-slate-800">تفاصيل الإيراد</h2>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div><p class="text-xs text-slate-400">الفرع</p><p class="font-medium mt-0.5">{{ $income->branch?->name }}</p></div>
            <div><p class="text-xs text-slate-400">نوع الدخل</p><p class="font-medium mt-0.5">{{ $income->incomeType?->name }}</p></div>
            <div><p class="text-xs text-slate-400">المبلغ</p><p class="font-bold text-green-600 text-lg mt-0.5">{{ number_format($income->amount, 2) }} ريال</p></div>
            <div><p class="text-xs text-slate-400">التاريخ</p><p class="font-medium mt-0.5">{{ $income->date?->format('Y-m-d') }}</p></div>
            <div>
                <p class="text-xs text-slate-400">طريقة الدفع</p>
                @php $pm = ['cash'=>'نقد','bank_transfer'=>'تحويل بنكي','check'=>'شيك','other'=>'أخرى']; @endphp
                <p class="font-medium mt-0.5">{{ $pm[$income->payment_method] ?? $income->payment_method }}</p>
            </div>
            <div><p class="text-xs text-slate-400">رقم المرجع</p><p class="font-medium mt-0.5">{{ $income->reference_number ?? '-' }}</p></div>
            <div><p class="text-xs text-slate-400">أضيف بواسطة</p><p class="font-medium mt-0.5">{{ $income->admin?->name }}</p></div>
            <div><p class="text-xs text-slate-400">تاريخ الإدخال</p><p class="font-medium mt-0.5">{{ $income->created_at?->format('Y-m-d H:i') }}</p></div>
        </div>
        @if($income->description)
        <div><p class="text-xs text-slate-400">الوصف</p><p class="mt-0.5">{{ $income->description }}</p></div>
        @endif
        @if($income->attachment)
        <div>
            <p class="text-xs text-slate-400">المرفق</p>
            <a href="{{ file_url($income->attachment) }}" target="_blank" class="text-blue-600 text-sm hover:underline mt-0.5 block">عرض المرفق</a>
        </div>
        @endif
        <div class="flex gap-3 pt-4 border-t">
            <a href="{{ route('admin.incomes.edit', $income->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm px-5 py-2 rounded-lg">تعديل</a>
            <a href="{{ route('admin.incomes.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm px-5 py-2 rounded-lg">رجوع</a>
        </div>
    </div>
</div>
@endsection

