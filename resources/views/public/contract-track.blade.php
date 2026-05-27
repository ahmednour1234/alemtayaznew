<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تتبع عقد الاستقدام</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Cairo', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen">

    <div class="max-w-2xl mx-auto py-10 px-4">

        {{-- Logo / Header --}}
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-slate-800">تتبع عقد الاستقدام</h1>
            <p class="text-slate-400 text-sm mt-1">أدخل رقم عقد مساند لمتابعة حالة طلبك</p>
        </div>

        {{-- Search form --}}
        <form method="GET" action="{{ url('/track') }}" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 mb-6">
            <label class="block text-sm font-semibold text-slate-600 mb-2">رقم عقد مساند</label>
            <div class="flex gap-3">
                <input type="text" name="musaned_number" value="{{ $musanedNum }}"
                       placeholder="أدخل رقم العقد..." required
                       class="flex-1 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-6 py-2.5 rounded-xl shadow">بحث</button>
            </div>
        </form>

        @if($musanedNum && !$contract)
        <div class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-xl px-4 py-3 mb-5">
            لم يتم العثور على عقد بهذا الرقم. تأكد من صحة رقم عقد مساند.
        </div>
        @endif

        @if($contract)
        {{-- Contract summary --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 mb-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-xs text-slate-400">رقم العقد الداخلي</p>
                    <p class="font-mono font-bold text-slate-800">{{ $contract->contract_number }}</p>
                </div>
                @php $statusColors = [13 => 'bg-green-100 text-green-700', 9 => 'bg-red-100 text-red-700', 15 => 'bg-red-100 text-red-700']; $statusColor = $statusColors[$contract->current_status] ?? 'bg-blue-100 text-blue-700'; @endphp
                <span class="px-3 py-1.5 rounded-full text-sm font-medium {{ $statusColor }}">
                    {{ $contract->status_label }}
                </span>
            </div>
            <div class="grid grid-cols-2 gap-4 text-sm border-t border-slate-100 pt-4">
                <div>
                    <p class="text-xs text-slate-400">العميل</p>
                    <p class="font-medium text-slate-700">{{ $contract->client->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400">رقم مساند</p>
                    <p class="font-mono text-slate-700">{{ $contract->musaned_number }}</p>
                </div>
                @if($contract->worker)
                <div>
                    <p class="text-xs text-slate-400">العاملة</p>
                    <p class="font-medium text-slate-700">{{ $contract->worker->name }}</p>
                </div>
                @endif
                @if($contract->arrival_date)
                <div>
                    <p class="text-xs text-slate-400">تاريخ الوصول</p>
                    <p class="text-slate-700">{{ $contract->arrival_date->format('Y/m/d') }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Status timeline --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-sm font-bold text-slate-700 mb-5">متابعة مراحل العقد</h3>
            <div class="relative">
                <div class="absolute right-3.5 top-0 bottom-0 w-0.5 bg-slate-100"></div>
                <div class="space-y-4">
                    @foreach($statuses as $num => $st)
                    @php
                        $h = $historyMap->get($num);
                        $isDone    = $h && $h->status_date;
                        $isCurrent = $num === $contract->current_status;
                    @endphp
                    @if($isDone || $isCurrent)
                    <div class="flex items-start gap-4 relative">
                        <div class="w-7 h-7 flex-shrink-0 flex items-center justify-center rounded-full z-10
                            {{ $isDone && !$isCurrent ? 'bg-green-500' : ($isCurrent ? 'bg-blue-500' : 'bg-slate-200') }} text-white text-xs font-bold shadow-sm">
                            @if($isDone && !$isCurrent) ✓ @else {{ $num }} @endif
                        </div>
                        <div class="flex-1 pt-0.5">
                            <p class="text-sm font-semibold {{ $isCurrent ? 'text-blue-700' : 'text-slate-700' }}">{{ $st['label'] }}</p>
                            @if($isDone)
                            <p class="text-xs text-slate-400 mt-0.5">{{ $h->status_date->format('Y/m/d') }}</p>
                            @endif
                            @if($isCurrent && !$isDone)
                            <p class="text-xs text-blue-400 mt-0.5">المرحلة الحالية</p>
                            @endif
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <p class="text-center text-xs text-slate-400 mt-8">نظام الامتياز للاستقدام &copy; {{ date('Y') }}</p>
    </div>
</body>
</html>
