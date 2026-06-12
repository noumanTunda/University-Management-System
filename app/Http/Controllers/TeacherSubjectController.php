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
        $currentYear = $academicYears->first();
        $currentYearName = $currentYear ? $currentYear->name : date('Y') . '-' . (date('Y') + 1);
        $currentYearId = $currentYear ? $currentYear->id : null;
        $yearNames = AcademicYear::pluck('name', 'id')->toArray();
        return view('teacher_subject.index', compact('teachers', 'departments', 'allSubjects', 'academicYears', 'currentYearName', 'currentYearId', 'yearNames'));
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
        $yearId = $data['academic_year_id'];
        $yearName = '';
        if ($yearId) {
            $y = AcademicYear::find($yearId);
            $yearName = $y ? $y->name : '';
        }
        $syncData = [];
        foreach ($data['subject_ids'] as $sid) {
            $syncData[$sid] = ['academic_year_id' => $yearId, 'academic_year' => $yearName];
        }
        $teacher->subjects()->sync($syncData);
        $notification = ['title' => 'Data Store', 'body' => 'Subjects assigned for ' . ($yearName ?: 'N/A') . ' successfully.'];
        return redirect()->route('teacher.subject.index')->with('success', $notification);
    }

    public function edit($id)
    {
        $teacher = User::with('subjects')->findOrFail($id);
        $departments = Department::select('id','name')->orderBy('name','asc')->get();
        $allSubjects = Subject::select('id','name','code','department_id')->with('department')->orderBy('name')->get();
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $currentYear = $academicYears->first();
        $currentYearName = $currentYear ? $currentYear->name : date('Y') . '-' . (date('Y') + 1);
        $currentYearId = $currentYear ? $currentYear->id : null;
        $yearNames = AcademicYear::pluck('name', 'id')->toArray();
        return view('teacher_subject.edit', compact('teacher', 'departments', 'allSubjects', 'academicYears', 'currentYearName', 'currentYearId', 'yearNames'));
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
        $yearId = $data['academic_year_id'];
        $yearName = '';
        if ($yearId) {
            $y = AcademicYear::find($yearId);
            $yearName = $y ? $y->name : '';
        }
        $syncData = [];
        foreach ($data['subject_ids'] as $sid) {
            $syncData[$sid] = ['academic_year_id' => $yearId, 'academic_year' => $yearName];
        }
        $teacher->subjects()->sync($syncData);
        $notification = ['title' => 'Data Update', 'body' => 'Subjects updated for ' . ($yearName ?: 'N/A') . ' successfully.'];
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
