@extends('admin.layouts.app')
@section('title', 'بانرات الموقع')

@section('content')
<div class="p-5">

    <div class="flex items-center justify-between gap-4 mb-5">
        <div>
            <h1 class="text-lg font-bold">بانرات الموقع</h1>
            <p class="text-xs text-slate-500 mt-1">
                صور إعلانية تظهر في الموقع العام، والضغط عليها يفتح واتساب برسالة استفسار.
            </p>
        </div>
        @can('banners.create')
        <a href="{{ route('admin.banners.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-4 py-2.5 rounded-lg transition-colors">
            + إضافة بانر
        </a>
        @endcan
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm font-medium">
        {{ session('success') }}
    </div>
    @endif

    @if($banners->isEmpty())
    <div class="bg-white rounded-xl shadow-sm p-12 text-center">
        <p class="text-sm text-slate-500">لا توجد بانرات بعد.</p>
    </div>
    @else
    <div class="bg-white rounded-xl shadow-sm overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-xs text-slate-500">
                <tr>
                    <th class="px-4 py-3 text-start font-semibold">الصورة</th>
                    <th class="px-4 py-3 text-start font-semibold">الاسم</th>
                    <th class="px-4 py-3 text-start font-semibold">الموضع</th>
                    <th class="px-4 py-3 text-start font-semibold">الفترة</th>
                    <th class="px-4 py-3 text-start font-semibold">الترتيب</th>
                    <th class="px-4 py-3 text-start font-semibold">الحالة</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($banners as $banner)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3">
                        @if($banner->imageUrl())
                        <img src="{{ $banner->imageUrl() }}" alt="" loading="lazy"
                             class="w-28 h-14 object-cover rounded-lg border border-slate-200">
                        @endif
                    </td>
                    <td class="px-4 py-3 font-semibold">{{ $banner->title ?: '—' }}</td>
                    <td class="px-4 py-3">{{ \App\Models\SiteBanner::PLACEMENTS[$banner->placement] ?? $banner->placement }}</td>
                    <td class="px-4 py-3 text-xs text-slate-500 whitespace-nowrap">
                        @if($banner->starts_at || $banner->ends_at)
                            {{ $banner->starts_at?->format('Y-m-d') ?: '—' }}
                            <span class="mx-1">←</span>
                            {{ $banner->ends_at?->format('Y-m-d') ?: '—' }}
                        @else
                            دائم
                        @endif
                    </td>
                    <td class="px-4 py-3 text-slate-500">{{ $banner->sort_order }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold
                                     {{ $banner->active ? 'bg-green-50 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $banner->active ? 'مفعّل' : 'موقوف' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-2">
                            @can('banners.edit')
                            <form method="POST" action="{{ route('admin.banners.toggle', $banner->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-xs font-bold text-amber-600 hover:text-amber-700">
                                    {{ $banner->active ? 'إيقاف' : 'تفعيل' }}
                                </button>
                            </form>
                            <a href="{{ route('admin.banners.edit', $banner->id) }}"
                               class="text-xs font-bold text-blue-600 hover:text-blue-700">تعديل</a>
                            @endcan
                            @can('banners.delete')
                            <form method="POST" action="{{ route('admin.banners.destroy', $banner->id) }}" class="inline"
                                  onsubmit="return confirm('حذف هذا البانر؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-bold text-red-600 hover:text-red-700">حذف</button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-5">{{ $banners->links() }}</div>
    @endif
</div>
@endsection
