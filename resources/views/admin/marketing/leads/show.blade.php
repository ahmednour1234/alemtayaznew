@extends('admin.layouts.app')
@section('title', $lead->name)
@section('content')

@php $leadStatuses = \App\Models\Lead::statuses(); @endphp

<div class="flex items-center gap-3 mb-5">
    <a href="{{ route('admin.marketing.leads.index') }}" class="text-slate-400 hover:text-slate-600">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </a>
    <div class="flex-1">
        <h2 class="text-xl font-bold text-slate-800">{{ $lead->name }}</h2>
        <p class="text-xs text-slate-500 mt-0.5">
            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $leadStatuses[$lead->status]['color'] }}">
                {{ $leadStatuses[$lead->status]['label'] }}
            </span>
            @if($lead->campaign) • حملة: {{ $lead->campaign->name }} @endif
        </p>
    </div>
    @if($lead->status !== 'converted')
    <button onclick="document.getElementById('convertModal').classList.remove('hidden')"
            class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-lg">تحويل لعميل فعلي</button>
    @else
        @if($lead->client)
        <a href="{{ route('admin.clients.show', $lead->client) }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg">عرض العميل</a>
        @endif
    @endif
</div>

@if(session('success'))
<div class="mb-4 bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 text-sm">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    <!-- Main info -->
    <div class="lg:col-span-2 space-y-5">
        <!-- Personal -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-base font-semibold text-slate-700 mb-4 pb-3 border-b border-slate-100">بيانات العميل</h3>
            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div><dt class="text-xs text-slate-400">الاسم</dt><dd class="font-medium text-slate-800">{{ $lead->name }}</dd></div>
                <div><dt class="text-xs text-slate-400">الجوال</dt><dd class="text-slate-800 font-mono">{{ $lead->phone ?? '—' }}</dd></div>
                <div><dt class="text-xs text-slate-400">المدينة</dt><dd class="text-slate-800">{{ $lead->city ?? '—' }}</dd></div>
                <div><dt class="text-xs text-slate-400">الجنسية المطلوبة</dt><dd class="text-slate-800">{{ $lead->nationality?->name ?? '—' }}</dd></div>
                <div><dt class="text-xs text-slate-400">الفرع</dt><dd class="text-slate-800">{{ $lead->branch?->name ?? '—' }}</dd></div>
                <div><dt class="text-xs text-slate-400">المسؤول</dt><dd class="text-slate-800">{{ $lead->assignedAdmin?->name ?? '—' }}</dd></div>
                <div><dt class="text-xs text-slate-400">من جاب التأشيرة</dt><dd class="text-slate-800">{{ $lead->referredByAdmin?->name ?? '—' }}</dd></div>
                <div><dt class="text-xs text-slate-400">المصدر</dt><dd class="text-slate-800">{{ $lead->source ?? '—' }}</dd></div>
            </dl>
            @if($lead->notes)
            <div class="mt-4 pt-4 border-t border-slate-100">
                <p class="text-xs text-slate-400 mb-1">ملاحظات</p>
                <p class="text-sm text-slate-700 whitespace-pre-line">{{ $lead->notes }}</p>
            </div>
            @endif
        </div>

        <!-- Edit assignment -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-base font-semibold text-slate-700 mb-4 pb-3 border-b border-slate-100">تعديل بيانات الطلب</h3>
            <form method="POST" action="{{ route('admin.marketing.leads.update', $lead) }}" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @csrf @method('PUT')
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">الجنسية المطلوبة</label>
                    <select name="nationality_id" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                        <option value="">— غير محدد —</option>
                        @foreach($nationalities as $n)
                        <option value="{{ $n->id }}" {{ $lead->nationality_id == $n->id ? 'selected' : '' }}>{{ $n->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">المسؤول</label>
                    <select name="assigned_admin_id" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                        <option value="">— غير محدد —</option>
                        @foreach($admins as $a)
                        <option value="{{ $a->id }}" {{ $lead->assigned_admin_id == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">من جاب التأشيرة</label>
                    <select name="referred_by_admin_id" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                        <option value="">— غير محدد —</option>
                        @foreach($admins as $a)
                        <option value="{{ $a->id }}" {{ $lead->referred_by_admin_id == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">الفرع</label>
                    <select name="branch_id" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                        <option value="">— غير محدد —</option>
                        @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ $lead->branch_id == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <button class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-5 py-2 rounded-lg">حفظ</button>
                </div>
            </form>
        </div>

        <!-- Call logs -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-base font-semibold text-slate-700 mb-4 pb-3 border-b border-slate-100">سجل المكالمات</h3>
            <div class="space-y-3 mb-5">
                @forelse($lead->callLogs as $log)
                <div class="flex gap-3 p-3 bg-slate-50 rounded-lg">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $callStatuses[$log->status]['color'] }}">
                                {{ $callStatuses[$log->status]['label'] }}
                            </span>
                            <span class="text-xs text-slate-500">{{ $log->admin?->name }}</span>
                            <span class="text-xs text-slate-400 mr-auto">{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                        @if($log->notes)
                        <p class="text-sm text-slate-700 mt-1">{{ $log->notes }}</p>
                        @endif
                        @if($log->follow_up_at)
                        <p class="text-xs text-blue-600 mt-1">موعد المتابعة: {{ $log->follow_up_at->format('Y-m-d H:i') }}</p>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-sm text-slate-400 text-center py-4">لم يتم تسجيل أي مكالمة بعد</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Sidebar: Call form -->
    <div class="space-y-5">
        <div class="bg-white rounded-xl shadow-sm p-6 sticky top-4">
            <h3 class="text-base font-semibold text-slate-700 mb-4 pb-3 border-b border-slate-100">تسجيل مكالمة جديدة</h3>

            <!-- Requested nationality banner -->
            <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2.5 flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4a4 4 0 014-4h10a4 4 0 014 4v4M16 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <div class="flex-1 text-xs">
                    <div class="text-amber-700">الجنسية المطلوبة من العميل</div>
                    <div class="font-bold text-amber-900 text-sm">{{ $lead->nationality?->name ?? 'لم يتم تحديدها بعد' }}</div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.marketing.leads.call', $lead) }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">نتيجة المكالمة *</label>
                    <select name="status" required class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                        @foreach($callStatuses as $k => $s)
                        <option value="{{ $k }}">{{ $s['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">الجنسية المطلوبة</label>
                    <select name="nationality_id" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                        <option value="">— غير محدد —</option>
                        @foreach($nationalities as $n)
                        <option value="{{ $n->id }}" {{ $lead->nationality_id == $n->id ? 'selected' : '' }}>{{ $n->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-slate-400 mt-1">يمكنك تحديث الجنسية المطلوبة بناءً على ما يطلبه العميل في المكالمة</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">موعد المتابعة (اختياري)</label>
                    <input type="datetime-local" name="follow_up_at"
                           class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">ملاحظات</label>
                    <textarea name="notes" rows="3" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm"></textarea>
                </div>
                <button class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm py-2 rounded-lg">تسجيل المكالمة</button>
            </form>
        </div>
    </div>
</div>

<!-- Convert Modal -->
<div id="convertModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">تحويل لعميل فعلي</h3>
        <form method="POST" action="{{ route('admin.marketing.leads.convert', $lead) }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5">الاسم *</label>
                <input type="text" name="name" value="{{ $lead->name }}" required class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5">الهوية الوطنية</label>
                <input type="text" name="national_id" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5">الجوال</label>
                <input type="text" name="phone" value="{{ $lead->phone }}" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5">الفرع *</label>
                <select name="branch_id" required class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                    <option value="">— اختر —</option>
                    @foreach($branches as $b)
                    <option value="{{ $b->id }}" {{ $lead->branch_id == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5">التصنيف</label>
                <select name="classification" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                    <option value="normal">عادي</option>
                    <option value="confirmed">مؤكد</option>
                    <option value="vip">VIP</option>
                </select>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('convertModal').classList.add('hidden')"
                        class="px-5 py-2 text-sm text-slate-600 hover:bg-slate-50 rounded-lg">إلغاء</button>
                <button class="bg-green-600 hover:bg-green-700 text-white text-sm px-5 py-2 rounded-lg">تحويل</button>
            </div>
        </form>
    </div>
</div>
@endsection
