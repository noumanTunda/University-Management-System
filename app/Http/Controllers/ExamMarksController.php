<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\AssessmentPlan;
use App\AssessmentComponent;
use App\AssessmentMark;
use App\CourseRegistration;
use App\Subject;
use App\Semester;
use App\AcademicYear;
use App\Student;
use App\Department;
use Validator;

class ExamMarksController extends Controller
{
    public function __construct()
    {
        $this->middleware('teacher');
    }

    public function create()
    {
        $departments = Department::select('id','name')->orderBy('name')->lists('name', 'id');
        $years = AcademicYear::orderBy('name', 'desc')->lists('name', 'id');
        return view('exam_marks.create', compact('departments', 'years'));
    }

    public function getSemesters($yearId)
    {
        $sems = Semester::where('academic_year_id', $yearId)->select('id', 'semester_number')->get();
        return response()->json(['success' => true, 'semesters' => $sems]);
    }

    public function getSubjects($deptId)
    {
        $subjects = Subject::where('department_id', $deptId)->select('id', 'name', 'code')->orderBy('name')->get();
        return response()->json(['success' => true, 'subjects' => $subjects]);
    }

    public function getComponents($subjectId, $semesterId)
    {
        $plan = AssessmentPlan::where('subject_id', $subjectId)->where('semester_id', $semesterId)->with('components')->first();

        if (!$plan) {
            // Default: Course Work (CA) + UE
            $components = [
                ['id' => 0, 'name' => 'Course Work', 'type' => 'CA', 'max_score' => 40, 'weight' => 40],
                ['id' => 0, 'name' => 'University Exam', 'type' => 'UE', 'max_score' => 60, 'weight' => 60],
            ];
            $planId = 0;
        } else {
            $components = $plan->components->toArray();
            $planId = $plan->id;
        }

        return response()->json(['success' => true, 'plan_id' => $planId, 'components' => $components]);
    }

    public function getStudents($subjectId, $semesterId)
    {
        $students = Student::whereHas('registered', function($q) {
            // Students who have registered
        })->with(['marks' => function($q) use ($subjectId, $semesterId) {
            $q->whereHas('component.plan', function($qq) use ($subjectId, $semesterId) {
                $qq->where('subject_id', $subjectId)->where('semester_id', $semesterId);
            });
        }])->orderBy('firstName')->get(['id', 'idNo', 'firstName', 'lastName']);

        return response()->json(['success' => true, 'students' => $students]);
    }

    public function getMarkEntry($subjectId, $semesterId)
    {
        $plan = AssessmentPlan::where('subject_id', $subjectId)->where('semester_id', $semesterId)->with('components')->first();
        $defaultComponents = [
            ['id' => 0, 'name' => 'Course Work', 'type' => 'CA', 'max_score' => 40, 'weight' => 40],
            ['id' => 0, 'name' => 'University Exam', 'type' => 'UE', 'max_score' => 60, 'weight' => 60],
        ];
        $components = $plan ? $plan->components->toArray() : $defaultComponents;
        $planId = $plan ? $plan->id : 0;

        $students = Student::orderBy('firstName')->get(['id', 'idNo', 'firstName', 'lastName']);
        $marks = [];
        if ($planId > 0) {
            $marks = AssessmentMark::whereIn('assessment_component_id', $plan->components->pluck('id'))
                ->get()
                ->groupBy('student_id');
        }

        return view('exam_marks.entry', compact('components', 'planId', 'students', 'marks', 'subjectId', 'semesterId'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $planId = $data['plan_id'];
        $subjectId = $data['subject_id'];
        $semesterId = $data['semester_id'];

        if ($planId == 0) {
            // Default mode: save to exams table or course_registrations
            foreach ($data['ca'] ?? [] as $studentId => $ca) {
                $ue = $data['ue'][$studentId] ?? 0;
                $grade = CourseRegistration::computeGrade($ca, $ue);
                CourseRegistration::updateOrCreate(
                    ['student_id' => $studentId, 'subject_id' => $subjectId, 'semester_id' => $semesterId],
                    ['ca_score' => $ca, 'ue_score' => $ue, 'grade_letter' => $grade['letter'], 'grade_point' => $grade['point'], 'status' => $grade['status']]
                );
            }
        } else {
            // Plan-based: save marks for each component
            foreach ($data['scores'] ?? [] as $componentId => $studentScores) {
                foreach ($studentScores as $studentId => $score) {
                    if ($score === '' || $score === null) continue;
                    AssessmentMark::updateOrCreate(
                        ['assessment_component_id' => $componentId, 'student_id' => $studentId],
                        ['score' => $score]
                    );
                }
            }
        }

        return redirect()->back()->with('success', ['title'=>'Saved', 'body'=>'Marks saved successfully.']);
    }
}
