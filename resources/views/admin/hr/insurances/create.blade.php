@extends('admin.layouts.app')
@section('title', 'تأمين طبي جديد')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-slate-800">تسجيل تأمين طبي</h2>
    <a href="{{ route('admin.hr.insurances.index') }}" class="text-sm text-slate-500 hover:text-slate-700">رجوع</a>
</div>
<div class="bg-white rounded-xl p-6 shadow-sm border border-slate-100">
    <form method="POST" action="{{ route('admin.hr.insurances.store') }}">
        @csrf
        @include('admin.hr.insurances._form')
        <div class="mt-6 flex gap-3">
            <button class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-6 py-2.5 rounded-lg">حفظ</button>
            <a href="{{ route('admin.hr.insurances.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm px-6 py-2.5 rounded-lg">إلغاء</a>
        </div>
    </form>
</div>
@endsection
