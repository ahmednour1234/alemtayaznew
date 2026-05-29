@extends('admin.layouts.app')
@section('title', 'تعديل رحلة')
@section('content')

<div class="mb-6">
    <a href="{{ route('admin.trips.show', $trip->id) }}" class="text-slate-500 hover:text-slate-700 text-sm flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        عودة إلى تفاصيل الرحلة
    </a>
    <h2 class="text-xl font-bold text-slate-800 mt-2">تعديل رحلة — {{ $trip->trip_number }}</h2>
</div>

<div class="bg-white rounded-xl shadow-sm p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.trips.update', $trip->id) }}">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">نوع الرحلة <span class="text-red-500">*</span></label>
                <select name="trip_type" required class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                    @foreach(\App\Models\Trip::typeLabels() as $val => $label)
                    <option value="{{ $val }}" {{ $trip->trip_type == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">تاريخ الرحلة <span class="text-red-500">*</span></label>
                <input type="date" name="trip_date" value="{{ $trip->trip_date }}" required
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">وقت الرحلة</label>
                <input type="time" name="trip_time" value="{{ $trip->trip_time }}"
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">المطار</label>
                <select name="airport_id" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                    <option value="">بدون مطار</option>
                    @foreach($airports as $ap)
                    <option value="{{ $ap->id }}" {{ $trip->airport_id == $ap->id ? 'selected' : '' }}>{{ $ap->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">رقم الرحلة الجوية</label>
                <input type="text" name="flight_number" value="{{ $trip->flight_number }}"
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">الفرع <span class="text-red-500">*</span></label>
                <select name="branch_id" required class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                    @foreach($branches as $b)
                    <option value="{{ $b->id }}" {{ $trip->branch_id == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-5">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">ملاحظات</label>
            <textarea name="notes" rows="3" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">{{ $trip->notes }}</textarea>
        </div>

        <div class="flex gap-3 mt-6">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-medium">
                حفظ التعديلات
            </button>
            <a href="{{ route('admin.trips.show', $trip->id) }}" class="text-slate-600 px-6 py-2 rounded-lg text-sm border border-slate-200 hover:bg-slate-50">
                إلغاء
            </a>
        </div>
    </form>
</div>
@endsection
