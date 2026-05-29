@extends('admin.layouts.app')
@section('title', 'الشكوى ' . $complaint->complaint_number)
@section('content')
@php
    $isStale = in_array($complaint->status, ['new', 'in_progress']) && $complaint->created_at->lt(now()->subDays(7));
    $daysOpen = (int) $complaint->created_at->diffInDays(now());
@endphp
<div class="w-full">
    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-3 mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.complaints.index') }}" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-slate-800 flex items-center gap-3">
                    شكوى <span class="font-mono text-blue-600">{{ $complaint->complaint_number }}</span>
                    <span class="inline-block px-3 py-0.5 rounded text-xs {{ $complaint->status_badge_class }}">{{ $complaint->status_label }}</span>
                    <span class="inline-block px-3 py-0.5 rounded text-xs {{ $complaint->priority_badge_class }}">{{ $complaint->priority_label }}</span>
                </h2>
                <p class="text-sm text-slate-500 mt-1">سُجلت في {{ $complaint->created_at->format('Y-m-d H:i') }} — منذ {{ $daysOpen }} يوم</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.complaints.edit', $complaint->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg">تعديل</a>
            @if($complaint->public_token)
            <button type="button" id="copyLinkBtn"
                onclick="navigator.clipboard.writeText('{{ route('complaint.track', $complaint->public_token) }}').then(()=>{ this.textContent='تم النسخ ✓'; setTimeout(()=>this.textContent='رابط العميل',2000); })"
                class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm px-4 py-2 rounded-lg">
                رابط العميل
            </button>
            @endif
            <form action="{{ route('admin.complaints.destroy', $complaint->id) }}" method="POST" onsubmit="return confirm('تأكيد الحذف؟')">
                @csrf @method('DELETE')
                <button class="bg-red-600 hover:bg-red-700 text-white text-sm px-4 py-2 rounded-lg">حذف</button>
            </form>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-2.5 rounded-lg mb-4">{{ session('success') }}</div>
    @endif

    @if($isStale)
    <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg mb-4 flex items-center gap-3">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <span>⚠ هذه الشكوى مفتوحة منذ أكثر من 7 أيام بدون حل — يرجى المتابعة عاجلًا.</span>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main column --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-sm font-semibold text-slate-500 uppercase mb-3">تفاصيل المشكلة</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs text-slate-500 mb-1">نوع المشكلة</p>
                        <p class="font-semibold text-slate-800">{{ $complaint->problem_type_label }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 mb-1">رقم التليفون</p>
                        <p class="font-semibold text-slate-800">{{ $complaint->phone ?: '—' }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs text-slate-500 mb-1">الوصف</p>
                        <p class="text-slate-800 whitespace-pre-wrap leading-relaxed">{{ $complaint->description }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-sm font-semibold text-slate-500 uppercase mb-3">الحل والمعالجة</h3>
                <div class="space-y-3 text-sm">
                    @if($complaint->resolution)
                    <div>
                        <p class="text-xs text-slate-500 mb-1">الإجراء المتخذ</p>
                        <p class="text-slate-800 whitespace-pre-wrap leading-relaxed">{{ $complaint->resolution }}</p>
                    </div>
                    @else
                    <p class="text-slate-400 italic">لم يتم تسجيل إجراء بعد.</p>
                    @endif
                    <div class="grid grid-cols-2 gap-4 pt-3 border-t border-slate-100">
                        <div>
                            <p class="text-xs text-slate-500 mb-1">قيد المعالجة منذ</p>
                            <p class="font-semibold text-slate-800">{{ $complaint->processed_at?->format('Y-m-d H:i') ?: '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 mb-1">تاريخ الحل</p>
                            <p class="font-semibold {{ $complaint->resolved_at ? 'text-emerald-700' : 'text-slate-400' }}">{{ $complaint->resolved_at?->format('Y-m-d H:i') ?: '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($complaint->attachments->count())
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-sm font-semibold text-slate-500 uppercase mb-3">المرفقات ({{ $complaint->attachments->count() }})</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach($complaint->attachments as $att)
                    <div class="relative border border-slate-200 rounded-lg p-2 group">
                        @if($att->is_image)
                        <a href="{{ $att->url }}" target="_blank">
                            <img src="{{ $att->url }}" class="w-full h-28 object-cover rounded">
                        </a>
                        @else
                        <a href="{{ $att->url }}" target="_blank" class="flex flex-col items-center justify-center h-28 text-slate-500 text-xs">
                            <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            <span class="truncate w-full text-center">{{ Str::limit($att->original_name, 18) }}</span>
                        </a>
                        @endif
                        <form action="{{ route('admin.complaints.attachments.destroy', $att->id) }}" method="POST" onsubmit="return confirm('حذف المرفق؟')"
                              class="absolute top-1 left-1 opacity-0 group-hover:opacity-100 transition">
                            @csrf @method('DELETE')
                            <button class="bg-red-600 text-white rounded p-1" title="حذف">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-4">
            <div class="bg-white rounded-xl shadow-sm p-5">
                <h4 class="text-xs font-semibold text-slate-500 uppercase mb-3">الأطراف</h4>
                <dl class="space-y-2.5 text-sm">
                    <div class="flex justify-between"><dt class="text-slate-500">العميل:</dt><dd class="font-medium">{{ $complaint->client->name ?? '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">العاملة:</dt><dd class="font-medium">{{ $complaint->worker->name ?? '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">العقد:</dt><dd class="font-mono text-xs">{{ $complaint->contract->contract_number ?? '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">الفرع:</dt><dd class="font-medium">{{ $complaint->branch->name ?? '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">المسؤول:</dt><dd class="font-medium">{{ $complaint->assignedAdmin->name ?? '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">سجّلها:</dt><dd class="font-medium">{{ $complaint->createdBy->name ?? '—' }}</dd></div>
                </dl>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-5">
                <h4 class="text-xs font-semibold text-slate-500 uppercase mb-3">مساند</h4>
                @if($complaint->on_musaned)
                <div class="flex items-center gap-2 text-emerald-700 text-sm mb-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    <span class="font-semibold">مرفوعة على مساند</span>
                </div>
                @if($complaint->musaned_number)
                <p class="text-sm font-mono bg-slate-50 rounded p-2">{{ $complaint->musaned_number }}</p>
                @endif
                @else
                <p class="text-sm text-slate-400">غير مرفوعة على مساند</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
