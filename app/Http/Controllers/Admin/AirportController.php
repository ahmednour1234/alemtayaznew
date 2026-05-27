<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Airport;
use Illuminate\Http\Request;

class AirportController extends Controller
{
    public function index()
    {
        $airports = Airport::orderBy('city')->paginate(20);
        $trashed  = Airport::onlyTrashed()->get();
        return view('admin.airports.index', compact('airports', 'trashed'));
    }

    public function create()
    {
        return view('admin.airports.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:200',
            'code' => 'nullable|string|max:10|unique:airports,code',
            'city' => 'nullable|string|max:100',
        ]);
        $data['active'] = $request->boolean('active', true);
        Airport::create($data);
        return redirect()->route('admin.airports.index')->with('success', 'تم إضافة المطار بنجاح.');
    }

    public function edit(int $id)
    {
        $airport = Airport::findOrFail($id);
        return view('admin.airports.edit', compact('airport'));
    }

    public function update(Request $request, int $id)
    {
        $airport = Airport::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:200',
            'code' => 'nullable|string|max:10|unique:airports,code,' . $id,
            'city' => 'nullable|string|max:100',
        ]);
        $data['active'] = $request->boolean('active');
        $airport->update($data);
        return redirect()->route('admin.airports.index')->with('success', 'تم تحديث بيانات المطار.');
    }

    public function destroy(int $id)
    {
        Airport::findOrFail($id)->delete();
        return back()->with('success', 'تم حذف المطار.');
    }

    public function restore(int $id)
    {
        Airport::withTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'تم استعادة المطار.');
    }

    public function toggleActive(int $id)
    {
        $airport = Airport::findOrFail($id);
        $airport->update(['active' => !$airport->active]);
        return back()->with('success', $airport->active ? 'تم التفعيل.' : 'تم إلغاء التفعيل.');
    }
}
