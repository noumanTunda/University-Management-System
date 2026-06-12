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
        // Build flat assignments list for DataTable
        $assignments = \DB::table('teacher_subject')
            ->join('users', 'teacher_subject.user_id', '=', 'users.id')
            ->join('subject', 'teacher_subject.subject_id', '=', 'subject.id')
            ->leftJoin('department', 'subject.department_id', '=', 'department.id')
            ->leftJoin('academic_years', 'teacher_subject.academic_year_id', '=', 'academic_years.id')
            ->select(
                'teacher_subject.user_id',
                'users.firstname as teacher_name',
                'users.lastname as teacher_lastname',
                'users.login',
                'subject.name as subject_name',
                'subject.code as subject_code',
                'department.name as department_name',
                'academic_years.name as academic_year_name',
                'teacher_subject.subject_id',
                'teacher_subject.academic_year_id',
            )
            ->orderBy('users.firstname')
            ->orderBy('subject.name')
            ->get();
        return view('teacher_subject.index', compact('teachers', 'departments', 'allSubjects', 'academicYears', 'currentYearName', 'currentYearId', 'yearNames', 'assignments'));
    }

    public function getSubjectsByDepartment($deptId)
    {
        $subjects = Subject::select('id','name','code')->where('department_id', $deptId)->orderBy('name','asc')->get();
        return response()->json(['success' => true, 'subjects' => $subjects]);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $rules = ['user_id' => 'required|exists:users,id', 'subject_ids' => 'required|array', 'academic_year_ids' => 'required|array', 'academic_year_ids.*' => 'exists:academic_years,id'];
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
        $teacher = User::findOrFail($data['user_id']);
        $yearIds = $data['academic_year_ids'];
        $inserts = [];
        foreach ($yearIds as $yearId) {
            \DB::table('teacher_subject')
                ->where('user_id', $teacher->id)
                ->where('academic_year_id', $yearId)
                ->delete();
            foreach ($data['subject_ids'] as $sid) {
                $inserts[] = [
                    'user_id' => $teacher->id,
                    'subject_id' => $sid,
                    'academic_year_id' => $yearId,
                    'created_at' => \Carbon\Carbon::now(),
                    'updated_at' => \Carbon\Carbon::now(),
                ];
            }
        }
        \DB::table('teacher_subject')->insert($inserts);
        $yearCount = count($yearIds);
        $subjCount = count($data['subject_ids']);
        $notification = ['title' => 'Data Store', 'body' => "{$subjCount} subject(s) assigned across {$yearCount} year(s)."];
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
        $rules = ['subject_ids' => 'required|array', 'academic_year_ids' => 'required|array', 'academic_year_ids.*' => 'exists:academic_years,id'];
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
        $inserts = [];
        foreach ($data['subject_ids'] as $sid) {
            $inserts[] = [
                'user_id' => $teacher->id,
                'subject_id' => $sid,
                'academic_year_id' => $yearId,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ];
        }
        \DB::table('teacher_subject')->where('user_id', $teacher->id)->where('academic_year_id', $yearId)->delete();
        \DB::table('teacher_subject')->insert($inserts);
        $notification = ['title' => 'Data Update', 'body' => 'Subjects updated successfully.'];
        return redirect()->route('teacher.subject.index')->with('success', $notification);
    }

    public function deleteAssignment($userId, $subjectId, $yearId)
    {
        \DB::table('teacher_subject')
            ->where('user_id', $userId)
            ->where('subject_id', $subjectId)
            ->where('academic_year_id', $yearId)
            ->delete();
        $notification = ['title' => 'Deleted', 'body' => 'Assignment removed successfully.'];
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
