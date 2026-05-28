@extends('admin.layouts.app')
@section('title', 'شكوى جديدة')
@section('content')
@php
    $c = old();
    $isEdit = false;
    $complaint = null;
@endphp
<div class="w-full">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.complaints.index') }}" class="text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <h2 class="text-2xl font-bold text-slate-800">تسجيل شكوى جديدة</h2>
    </div>

    @include('admin.complaints._form', [
        'action'    => route('admin.complaints.store'),
        'method'    => 'POST',
        'complaint' => null,
    ])
</div>
@endsection
