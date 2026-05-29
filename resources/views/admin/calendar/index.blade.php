@extends('admin.layouts.app')
@section('title', 'التقويم')
@section('content')

<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-slate-800">التقويم</h2>
    <div class="flex gap-3 items-center flex-wrap">
        @if($branches->count() > 1)
        <form method="GET" id="branch-form">
            <select name="branch_id" onchange="document.getElementById('branch-form').submit()"
                    class="border border-slate-200 rounded-lg px-3 py-2 text-sm">
                <option value="">جميع الفروع</option>
                @foreach($branches as $b)
                <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                @endforeach
            </select>
        </form>
        @endif
        <button onclick="window.print()"
                style="padding:7px 16px;border-radius:8px;font-size:13px;font-weight:600;color:#475569;border:1.5px solid #e2e8f0;background:#fff;cursor:pointer;display:flex;align-items:center;gap:6px;font-family:'Cairo',sans-serif;"
                onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            طباعة
        </button>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm p-4">
    <div id="calendar"></div>
</div>

<!-- FullCalendar CDN -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.11/locales/ar.global.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const branchId = '{{ request("branch_id", "") }}';

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'ar',
        direction: 'rtl',
        headerToolbar: {
            start: 'prev,next today',
            center: 'title',
            end: 'dayGridMonth,listMonth'
        },
        height: 'auto',
        buttonText: {
            today: 'اليوم',
            month: 'شهر',
            list: 'قائمة',
        },
        events: function(info, successCallback, failureCallback) {
            // Use midpoint of the date range to get the actual displayed month
            // (info.start is the grid start — often a few days before the 1st of the month)
            const mid = new Date((info.start.getTime() + info.end.getTime()) / 2);
            const month = mid.getFullYear() + '-' + String(mid.getMonth() + 1).padStart(2, '0');
            fetch(`{{ route('admin.calendar.events') }}?month=${month}&branch_id=${branchId}`)
                .then(r => r.json())
                .then(data => successCallback(data))
                .catch(() => failureCallback());
        },
        eventClick: function(info) {
            if (info.event.url) {
                info.jsEvent.preventDefault();
                window.open(info.event.url, '_blank');
            }
        },
        eventContent: function(arg) {
            const props = arg.event.extendedProps;
            return {
                html: `<div style="padding:2px 5px; font-size:12px; overflow:hidden; white-space:nowrap; text-overflow:ellipsis;">
                    ${arg.event.title}
                    ${props.workers ? ' (' + props.workers + ' عاملة)' : ''}
                </div>`
            };
        },
    });

    calendar.render();
});
</script>

<style>
#calendar .fc-toolbar-title { font-size: 1rem; font-weight: 600; }
#calendar .fc-button { font-size: 12px; padding: 4px 10px; }
#calendar .fc-event { cursor: pointer; border-radius: 4px; }

/* Fix overflow */
.bg-white.rounded-xl { overflow: hidden; }
#calendar { max-width: 100%; overflow-x: auto; }

@media print {
    #sidebar, #topbar, #sidebar-overlay, .no-print { display: none !important; }
    #main-wrap { margin-right: 0 !important; }
    body { background: #fff !important; }
    .bg-white.rounded-xl { box-shadow: none !important; border: 1px solid #e2e8f0 !important; }
    #calendar .fc-button, button { display: none !important; }
    .fc-toolbar .fc-button-group { display: none !important; }
    .fc-header-toolbar .fc-toolbar-chunk:first-child,
    .fc-header-toolbar .fc-toolbar-chunk:last-child { display: none !important; }
    h2 { font-size: 16pt; margin-bottom: 12px; }
    .flex.justify-between { margin-bottom: 10px; }
}
</style>
@endsection
