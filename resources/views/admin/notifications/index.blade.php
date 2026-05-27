@extends('admin.layouts.app')

@section('title', 'الإشعارات')

@section('content')
<div style="max-width:760px;margin:0 auto;">

    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
        <h2 style="font-size:20px;font-weight:700;color:#0f172a;margin:0;">الإشعارات</h2>
        @if($notifications->total() > 0)
            <form method="POST" action="{{ route('admin.notifications.read-all') }}">
                @csrf
                <button type="submit"
                        style="display:flex;align-items:center;gap:6px;padding:8px 16px;
                               border-radius:8px;border:1px solid #e2e8f0;background:#fff;
                               font-size:13px;font-weight:600;color:#64748b;cursor:pointer;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    تحديد الكل كمقروء
                </button>
            </form>
        @endif
    </div>

    {{-- Notifications list --}}
    <div style="background:#fff;border-radius:16px;border:1px solid #e8edf5;overflow:hidden;
                box-shadow:0 1px 3px rgba(15,23,42,.06);">

        @forelse($notifications as $notif)
            <a href="{{ route('admin.notifications.read', $notif->id) }}"
               style="display:flex;align-items:flex-start;gap:14px;
                      padding:16px 20px;text-decoration:none;
                      border-bottom:1px solid #f1f5f9;
                      background:{{ $notif->isRead() ? '#fff' : '#f0f7ff' }};
                      transition:background .15s;"
               onmouseover="this.style.background='#f8fafc'"
               onmouseout="this.style.background='{{ $notif->isRead() ? '#fff' : '#f0f7ff' }}'">

                {{-- Icon --}}
                <div style="width:44px;height:44px;border-radius:12px;flex-shrink:0;
                            background:{{ $notif->icon_bg }};color:{{ $notif->icon_color }};
                            display:flex;align-items:center;justify-content:center;">
                    {!! $notif->icon_svg !!}
                </div>

                {{-- Text --}}
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:3px;">
                        <p style="font-size:14px;font-weight:{{ $notif->isRead() ? '500' : '700' }};
                                  color:#0f172a;margin:0;">
                            {{ $notif->title }}
                        </p>
                        @if(! $notif->isRead())
                            <span style="display:inline-block;width:7px;height:7px;border-radius:50%;
                                         background:#2563eb;flex-shrink:0;"></span>
                        @endif
                    </div>
                    <p style="font-size:13px;color:#475569;margin:0 0 5px;">
                        {{ $notif->body }}
                    </p>
                    <p style="font-size:11.5px;color:#94a3b8;margin:0;">
                        {{ $notif->created_at->diffForHumans() }}
                    </p>
                </div>

                {{-- Chevron --}}
                <div style="color:#cbd5e1;flex-shrink:0;margin-top:12px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <polyline points="15 18 9 12 15 6"/>
                    </svg>
                </div>
            </a>
        @empty
            <div style="padding:60px 20px;text-align:center;color:#94a3b8;">
                <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.2"
                     viewBox="0 0 24 24" style="margin:0 auto 12px;display:block;opacity:.35;">
                    <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 01-3.46 0"/>
                </svg>
                <p style="font-size:15px;font-weight:600;color:#64748b;margin:0 0 4px;">لا توجد إشعارات</p>
                <p style="font-size:13px;margin:0;">ستظهر هنا إشعاراتك عند حدوث أي نشاط</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($notifications->hasPages())
        <div style="margin-top:20px;display:flex;justify-content:center;">
            {{ $notifications->links() }}
        </div>
    @endif

</div>
@endsection
