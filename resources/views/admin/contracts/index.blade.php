@extends('admin.layouts.app')
@section('title', 'عقود الاستقدام')
@section('content')
<div class="w-full space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-800">عقود الاستقدام</h2>
            <p class="text-slate-400 text-xs mt-0.5">إجمالي: {{ $contracts->total() }} عقد</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            {{-- Import form trigger --}}
            <form action="{{ route('admin.contracts.import') }}" method="POST" enctype="multipart/form-data" id="import-form">
                @csrf
                <input type="file" name="file" id="import-file" accept=".xlsx,.xls,.csv" class="hidden"
                       onchange="document.getElementById('import-form').submit()">
            </form>
            <button onclick="document.getElementById('import-file').click()"
                    class="flex items-center gap-1.5 text-sm bg-white border border-slate-200 text-slate-600 hover:text-green-700 hover:border-green-300 px-4 py-2 rounded-xl shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                استيراد
            </button>
            <a href="{{ route('admin.contracts.template') }}"
               class="flex items-center gap-1.5 text-sm bg-white border border-slate-200 text-slate-600 hover:text-blue-700 hover:border-blue-300 px-4 py-2 rounded-xl shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                نموذج
            </a>
            <a href="{{ route('admin.contracts.export', request()->query()) }}"
               class="flex items-center gap-1.5 text-sm bg-white border border-slate-200 text-slate-600 hover:text-emerald-700 hover:border-emerald-300 px-4 py-2 rounded-xl shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                تصدير Excel
            </a>
            <a href="{{ route('admin.contracts.trashed') }}"
               class="flex items-center gap-1.5 text-sm bg-white border border-slate-200 text-red-500 hover:bg-red-50 px-4 py-2 rounded-xl shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                محذوف
            </a>
            <a href="{{ route('admin.contracts.create') }}"
               class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2.5 rounded-xl shadow transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                عقد جديد
            </a>
        </div>
    </div>

    {{-- Department tabs --}}
    <div class="flex gap-2 flex-wrap">
        <a href="{{ route('admin.contracts.index', array_merge(request()->query(), ['department' => ''])) }}"
           class="px-4 py-2 rounded-xl text-sm font-medium transition {{ !request('department') ? 'bg-blue-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
            الكل
        </a>
        @foreach($departments as $key => $label)
        <a href="{{ route('admin.contracts.index', array_merge(request()->query(), ['department' => $key])) }}"
           class="px-4 py-2 rounded-xl text-sm font-medium transition {{ request('department') === $key ? 'bg-blue-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    {{-- Filters --}}
    <form method="GET" class="bg-white rounded-xl border border-slate-100 shadow-sm p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-3">
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="بحث برقم العقد / مساند / العميل..."
                   class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            <select name="status" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">— كل الحالات —</option>
                @foreach($statuses as $num => $st)
                <option value="{{ $num }}" {{ ($filters['status'] ?? '') == $num ? 'selected' : '' }}>{{ $st['label'] }}</option>
                @endforeach
            </select>
            <select name="payment_status" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">— حالة الدفع —</option>
                <option value="pending" {{ ($filters['payment_status'] ?? '') === 'pending' ? 'selected' : '' }}>معلق</option>
                <option value="partial" {{ ($filters['payment_status'] ?? '') === 'partial' ? 'selected' : '' }}>جزئي</option>
                <option value="full"    {{ ($filters['payment_status'] ?? '') === 'full' ? 'selected' : '' }}>كامل</option>
            </select>
            <select name="nationality_id" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">— الجنسية —</option>
                @foreach($nationalities as $nat)
                <option value="{{ $nat->id }}" {{ ($filters['nationality_id'] ?? '') == $nat->id ? 'selected' : '' }}>{{ $nat->name }}</option>
                @endforeach
            </select>
            @auth('admin')
            @if(Auth::guard('admin')->user()->isSuperAdmin())
            <select name="branch_id" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">— كل الفروع —</option>
                @foreach($branches as $br)
                <option value="{{ $br->id }}" {{ ($filters['branch_id'] ?? '') == $br->id ? 'selected' : '' }}>{{ $br->name }}</option>
                @endforeach
            </select>
            @endif
            @endauth
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg">بحث</button>
                <a href="{{ route('admin.contracts.index') }}" class="flex-1 text-center bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm px-4 py-2 rounded-lg">مسح</a>
            </div>
        </div>
    </form>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3">{{ session('success') }}</div>
    @endif

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-x-auto">
        <table class="w-full text-sm text-right">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-4 py-3 font-semibold text-slate-600">رقم العقد</th>
                    <th class="px-4 py-3 font-semibold text-slate-600">العميل</th>
                    <th class="px-4 py-3 font-semibold text-slate-600">الفرع</th>
                    <th class="px-4 py-3 font-semibold text-slate-600">العاملة</th>
                    <th class="px-4 py-3 font-semibold text-slate-600">الجنسية</th>
                    <th class="px-4 py-3 font-semibold text-slate-600">الحالة</th>
                    <th class="px-4 py-3 font-semibold text-slate-600">القسم</th>
                    <th class="px-4 py-3 font-semibold text-slate-600">الدفع</th>
                    <th class="px-4 py-3 font-semibold text-slate-600">التاريخ</th>
                    <th class="px-4 py-3 font-semibold text-slate-600">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($contracts as $c)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.contracts.show', $c->id) }}" class="font-mono text-blue-600 hover:underline">{{ $c->contract_number }}</a>
                        @if($c->musaned_number)
                        <div class="text-xs text-slate-400">مساند: {{ $c->musaned_number }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3 font-medium text-slate-700">{{ $c->client->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ $c->branch->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ $c->worker->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-slate-500 text-xs">{{ $c->worker->nationality->name ?? '—' }}</td>
                    <td class="px-4 py-3">
                        @php
                            $statusColors = [13 => 'bg-green-100 text-green-700', 9 => 'bg-red-100 text-red-700', 15 => 'bg-red-100 text-red-700'];
                            $color = $statusColors[$c->current_status] ?? 'bg-blue-100 text-blue-700';
                        @endphp
                        <span class="inline-block px-2.5 py-1 rounded-full text-xs font-medium {{ $color }}">
                            {{ $c->status_label }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-slate-500 text-xs">{{ $c->department_label }}</td>
                    <td class="px-4 py-3">
                        @php
                            $payColors = ['full' => 'bg-green-100 text-green-700', 'partial' => 'bg-yellow-100 text-yellow-700', 'pending' => 'bg-slate-100 text-slate-500'];
                        @endphp
                        <span class="inline-block px-2.5 py-1 rounded-full text-xs {{ $payColors[$c->payment_status] ?? '' }}">{{ $c->payment_label }}</span>
                    </td>
                    <td class="px-4 py-3 text-slate-400 text-xs">{{ $c->request_date?->format('Y/m/d') }}</td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.contracts.show', $c->id) }}" class="text-blue-600 hover:text-blue-800 text-xs">عرض</a>
                            <a href="{{ route('admin.contracts.edit', $c->id) }}" class="text-slate-500 hover:text-slate-700 text-xs">تعديل</a>
                            <form action="{{ route('admin.contracts.destroy', $c->id) }}" method="POST" onsubmit="return confirm('حذف العقد؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs">حذف</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center py-10 text-slate-400">لا توجد عقود</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $contracts->links() }}
</div>
@endsection
