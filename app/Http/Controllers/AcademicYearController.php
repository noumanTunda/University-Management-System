<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AcademicYear;
use Validator;

class AcademicYearController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('teacher'); // Allows Admin, HOD, Teacher
    }

    public function index()
    {
        $years = AcademicYear::orderBy('name', 'desc')->get();
        return view('academic_year.index', compact('years'));
    }

    public function create()
    {
        return view('academic_year.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|unique:academic_years,name|max:20',
            'is_active' => 'sometimes|boolean',
        ];
        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) return redirect()->back()->withErrors($v)->withInput();

        $data = ['name' => $request->name, 'is_active' => $request->has('is_active') ? true : false];

        // If activating this year, deactivate all others
        if ($data['is_active']) {
            AcademicYear::where('is_active', true)->update(['is_active' => false]);
        }

        AcademicYear::create($data);

        return redirect()->route('academic.year.index')
            ->with('success', ['title' => 'Created', 'body' => 'Academic year added successfully.']);
    }

    public function edit($id)
    {
        $year = AcademicYear::findOrFail($id);
        return view('academic_year.edit', compact('year'));
    }

    public function update(Request $request, $id)
    {
        $year = AcademicYear::findOrFail($id);
        $rules = [
            'name' => 'required|max:20|unique:academic_years,name,' . $id,
            'is_active' => 'sometimes|boolean',
        ];
        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) return redirect()->back()->withErrors($v)->withInput();

        // If activating this year, deactivate all others
        if ($request->has('is_active')) {
            AcademicYear::where('is_active', true)->update(['is_active' => false]);
        }

        $year->update([
            'name' => $request->name,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('academic.year.index')
            ->with('success', ['title' => 'Updated', 'body' => 'Academic year updated successfully.']);
    }

    public function destroy($id)
    {
        $year = AcademicYear::findOrFail($id);
        $year->delete();

        return redirect()->route('academic.year.index')
            ->with('success', ['title' => 'Deleted', 'body' => 'Academic year deleted.']);
    }
}
