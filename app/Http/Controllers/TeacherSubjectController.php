<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Subject;
use App\Department;
use App\AcademicYear;
use Validator;
use Redirect;

class TeacherSubjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('hod');
    }

    public function index()
    {
        $teachers = User::whereIn('group', ['Teacher', 'HeadOfDepartment'])->with('subjects')->get();
        $departments = Department::select('id','name')->orderBy('name','asc')->get();
        $allSubjects = Subject::select('id','name','code','department_id')->with('department')->orderBy('name')->get();
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $currentYearName = $academicYears->first() ? $academicYears->first()->name : date('Y') . '-' . (date('Y') + 1);
        // Backfill
        \DB::table('teacher_subject')->whereNull('academic_year')->update(['academic_year' => $currentYearName]);
        return view('teacher_subject.index', compact('teachers', 'departments', 'allSubjects', 'academicYears', 'currentYearName'));
    }

    public function getSubjectsByDepartment($deptId)
    {
        $subjects = Subject::select('id','name','code')->where('department_id', $deptId)->orderBy('name','asc')->get();
        return response()->json(['success' => true, 'subjects' => $subjects]);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $rules = ['user_id' => 'required|exists:users,id', 'subject_ids' => 'required|array', 'academic_year' => 'required'];
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
        $teacher = User::findOrFail($data['user_id']);
        $year = $data['academic_year'];
        $syncData = [];
        foreach ($data['subject_ids'] as $sid) {
            $syncData[$sid] = ['academic_year' => $year];
        }
        $teacher->subjects()->sync($syncData);
        $notification = ['title' => 'Data Store', 'body' => 'Subjects assigned for ' . $year . ' successfully.'];
        return redirect()->route('teacher.subject.index')->with('success', $notification);
    }

    public function edit($id)
    {
        $teacher = User::with('subjects')->findOrFail($id);
        $departments = Department::select('id','name')->orderBy('name','asc')->get();
        $allSubjects = Subject::select('id','name','code','department_id')->with('department')->orderBy('name')->get();
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $currentYearName = $academicYears->first() ? $academicYears->first()->name : date('Y') . '-' . (date('Y') + 1);
        return view('teacher_subject.edit', compact('teacher', 'departments', 'allSubjects', 'academicYears', 'currentYearName'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $rules = ['subject_ids' => 'required|array', 'academic_year' => 'required'];
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
        $teacher = User::findOrFail($id);
        $year = $data['academic_year'];
        $syncData = [];
        foreach ($data['subject_ids'] as $sid) {
            $syncData[$sid] = ['academic_year' => $year];
        }
        $teacher->subjects()->sync($syncData);
        $notification = ['title' => 'Data Update', 'body' => 'Subjects updated for ' . $year . ' successfully.'];
        return redirect()->route('teacher.subject.index')->with('success', $notification);
    }

    public function destroy($id)
    {
        $teacher = User::findOrFail($id);
        $teacher->subjects()->detach();
        $notification = ['title' => 'Data Delete', 'body' => 'All subjects unassigned from teacher.'];
        return redirect()->route('teacher.subject.index')->with('success', $notification);
    }
}
