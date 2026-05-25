@extends('admin.layouts.app')
@section('title', 'ØªØ¹Ø¯ÙŠÙ„ Ø§Ù„Ù…ØµØ±ÙˆÙ')
@section('content')
<div class="w-full">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.expenses.index') }}" class="text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-slate-800">ØªØ¹Ø¯ÙŠÙ„ Ø§Ù„Ù…ØµØ±ÙˆÙ</h2>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form action="{{ route('admin.expenses.update', $expense->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Ø§Ù„ÙØ±Ø¹ <span class="text-red-500">*</span></label>
                    <select name="branch_id" required class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ old('branch_id', $expense->branch_id) == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Ù†ÙˆØ¹ Ø§Ù„Ù…ØµØ±ÙˆÙ <span class="text-red-500">*</span></label>
                    <select name="expense_type_id" required class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        @foreach($expenseTypes as $type)
                        <option value="{{ $type->id }}" {{ old('expense_type_id', $expense->expense_type_id) == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Ø§Ù„Ù…Ø¨Ù„Øº <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" value="{{ old('amount', $expense->amount) }}" step="0.01" min="0" required
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Ø§Ù„ØªØ§Ø±ÙŠØ® <span class="text-red-500">*</span></label>
                    <input type="date" name="date" value="{{ old('date', $expense->date?->format('Y-m-d')) }}" required
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Ø·Ø±ÙŠÙ‚Ø© Ø§Ù„Ø¯ÙØ¹</label>
                    <select name="payment_method" class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        @foreach(['cash'=>'Ù†Ù‚Ø¯','bank_transfer'=>'ØªØ­ÙˆÙŠÙ„ Ø¨Ù†ÙƒÙŠ','check'=>'Ø´ÙŠÙƒ','other'=>'Ø£Ø®Ø±Ù‰'] as $val => $label)
                        <option value="{{ $val }}" {{ old('payment_method', $expense->payment_method) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Ø±Ù‚Ù… Ø§Ù„Ù…Ø±Ø¬Ø¹</label>
                    <input type="text" name="reference_number" value="{{ old('reference_number', $expense->reference_number) }}"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Ø§Ù„ÙˆØµÙ</label>
                <textarea name="description" rows="2"
                          class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('description', $expense->description) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Ø§Ù„Ù…Ø±ÙÙ‚</label>
                @if($expense->attachment)
                    <a href="{{ Storage::url($expense->attachment) }}" target="_blank" class="text-blue-600 text-xs hover:underline block mb-2">Ø¹Ø±Ø¶ Ø§Ù„Ù…Ø±ÙÙ‚ Ø§Ù„Ø­Ø§Ù„ÙŠ</a>
                @endif
                <input type="file" name="attachment" accept="image/*,.pdf"
                       class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-6 py-2.5 rounded-lg">ØªØ­Ø¯ÙŠØ«</button>
                <a href="{{ route('admin.expenses.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm px-6 py-2.5 rounded-lg">Ø¥Ù„ØºØ§Ø¡</a>
            </div>
        </form>
    </div>
</div>
@endsection

