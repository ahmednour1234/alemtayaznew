<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Nationality;
use Illuminate\Http\Request;

class NationalityController extends Controller
{
    public function index()
    {
        $nationalities = Nationality::orderBy('name')->paginate(20);
        $trashed       = Nationality::onlyTrashed()->get();
        return view('admin.nationalities.index', compact('nationalities', 'trashed'));
    }

    public function create()
    {
        return view('admin.nationalities.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:nationalities,name',
            'code' => 'nullable|string|max:10',
        ]);
        $data['active'] = $request->boolean('active', true);
        Nationality::create($data);
        return redirect()->route('admin.nationalities.index')->with('success', 'تم إضافة الجنسية بنجاح.');
    }

    public function edit(int $id)
    {
        $nationality = Nationality::findOrFail($id);
        return view('admin.nationalities.edit', compact('nationality'));
    }

    public function update(Request $request, int $id)
    {
        $nationality = Nationality::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:nationalities,name,' . $id,
            'code' => 'nullable|string|max:10',
        ]);
        $data['active'] = $request->boolean('active');
        $nationality->update($data);
        return redirect()->route('admin.nationalities.index')->with('success', 'تم تحديث الجنسية.');
    }

    public function destroy(int $id)
    {
        Nationality::findOrFail($id)->delete();
        return back()->with('success', 'تم حذف الجنسية.');
    }

    public function restore(int $id)
    {
        Nationality::withTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'تم استعادة الجنسية.');
    }

    public function toggleActive(int $id)
    {
        $nat = Nationality::findOrFail($id);
        $nat->update(['active' => !$nat->active]);
        return back()->with('success', $nat->active ? 'تم التفعيل.' : 'تم إلغاء التفعيل.');
    }
}
