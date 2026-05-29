@extends('admin.layouts.app')
@section('title', 'التقويم')
@section('content')

<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-slate-800">التقويم</h2>
    <div class="flex gap-3 items-center">
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
</style>
@endsection
