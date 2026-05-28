@extends('admin.layouts.app')
@section('title', 'تعديل الشكوى — ' . $complaint->complaint_number)
@section('content')
<div class="w-full">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.complaints.show', $complaint->id) }}" class="text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <h2 class="text-2xl font-bold text-slate-800">تعديل الشكوى — <span class="text-blue-600 font-mono text-lg">{{ $complaint->complaint_number }}</span></h2>
    </div>

    @include('admin.complaints._form', [
        'action'    => route('admin.complaints.update', $complaint->id),
        'method'    => 'PUT',
        'complaint' => $complaint,
    ])
</div>
@endsection
