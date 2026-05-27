@extends('admin.layouts.app')
@section('title', 'الصلاحيات')
@section('content')

<div class="flex justify-between items-center mb-5">
    <h2 class="text-xl font-bold text-slate-800">الصلاحيات</h2>
    <p class="text-sm text-slate-400">للقراءة فقط - يتم إدارتها من خلال الأدوار</p>
</div>

@php $grouped = $permissions->groupBy(fn($p) => explode('.', $p->slug)[0]); @endphp

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach($grouped as $group => $groupPerms)
    <div class="bg-white rounded-xl shadow-sm p-4">
        <h3 class="font-semibold text-slate-700 mb-3 capitalize flex items-center gap-2">
            <span class="w-2 h-2 bg-purple-500 rounded-full inline-block"></span>
            {{ $group }}
        </h3>
        <div class="space-y-2">
            @foreach($groupPerms as $perm)
            <div class="flex justify-between items-center">
                <span class="text-sm">{{ $perm->name }}</span>
                <span class="text-xs font-mono text-slate-400">{{ $perm->slug }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
@endsection

