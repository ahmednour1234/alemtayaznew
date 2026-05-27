@extends('admin.layouts.app')
@section('title', 'بيانات الوكيل')
@section('content')
<div class="w-full">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.agents.index') }}" class="text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-slate-800">بيانات الوكيل</h2>
        @if(Auth::guard('admin')->user()->isSuperAdmin() || Auth::guard('admin')->user()->hasPermission('agents.edit'))
        <a href="{{ route('admin.agents.edit', $agent->id) }}"
           class="mr-auto bg-amber-500 hover:bg-amber-600 text-white text-sm px-4 py-2 rounded-lg">تعديل</a>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-sm">
            <div>
                <dt class="text-xs text-slate-400 mb-0.5">اسم الوكيل</dt>
                <dd class="font-medium text-slate-800">{{ $agent->name }}</dd>
            </div>
            <div>
                <dt class="text-xs text-slate-400 mb-0.5">رقم الجوال</dt>
                <dd class="text-slate-800">{{ $agent->phone }}</dd>
            </div>
            <div>
                <dt class="text-xs text-slate-400 mb-0.5">الإيميل</dt>
                <dd class="text-slate-800">{{ $agent->email ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-slate-400 mb-0.5">الجنسية</dt>
                <dd class="text-slate-800">{{ $agent->nationality?->name ?? '—' }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-xs text-slate-400 mb-1">المستند</dt>
                <dd>
                    @if($agent->document)
                    <a href="{{ Storage::disk('public')->url($agent->document) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 text-blue-600 hover:text-blue-800 text-sm bg-blue-50 px-3 py-1.5 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        تحميل PDF
                    </a>
                    @else
                    <span class="text-slate-400">—</span>
                    @endif
                </dd>
            </div>
            @if($agent->notes)
            <div class="sm:col-span-2">
                <dt class="text-xs text-slate-400 mb-0.5">ملاحظات</dt>
                <dd class="text-slate-700 whitespace-pre-line">{{ $agent->notes }}</dd>
            </div>
            @endif
        </dl>
    </div>
</div>
@endsection
