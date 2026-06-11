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

        // Get the semester's academic year and number to match registrations
        $sem = Semester::with('academicYear')->find($semesterId);
        $session = $sem->academicYear->name ?? '';
        $levelTerm = 'Semester ' . ($sem->semester_number ?? '1');

        // Only load students registered for this academic year + semester
        $students = Student::whereHas('registered', function($q) use ($session, $levelTerm) {
                $q->where('session', $session)->where('levelTerm', $levelTerm);
            })
            ->orderBy('firstName')
            ->get(['id', 'idNo', 'firstName', 'lastName']);

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
        $subjectId = $data['subject_id'];
        $semesterId = $data['semester_id'];

        // Find or auto-create plan with default components
        $plan = AssessmentPlan::firstOrCreate(
            ['subject_id' => $subjectId, 'semester_id' => $semesterId],
            ['subject_id' => $subjectId, 'semester_id' => $semesterId]
        );

        // If plan was just created, add default CA + UE components
        if ($plan->components()->count() == 0) {
            $plan->components()->create(['name' => 'Course Work', 'type' => 'CA', 'max_score' => 40, 'weight' => 40]);
            $plan->components()->create(['name' => 'University Exam', 'type' => 'UE', 'max_score' => 60, 'weight' => 60]);
        }

        $plan->load('components');
        $caComp = $plan->components->where('type', 'CA')->first();
        $ueComp = $plan->components->where('type', 'UE')->first();

        // Save marks to assessment_marks for each component
        foreach ($data['ca'] ?? [] as $studentId => $ca) {
            if ($ca === '' || $ca === null) continue;
            if ($caComp) {
                AssessmentMark::updateOrCreate(
                    ['assessment_component_id' => $caComp->id, 'student_id' => $studentId],
                    ['score' => $ca]
                );
            }
        }
        foreach ($data['ue'] ?? [] as $studentId => $ue) {
            if ($ue === '' || $ue === null) continue;
            if ($ueComp) {
                AssessmentMark::updateOrCreate(
                    ['assessment_component_id' => $ueComp->id, 'student_id' => $studentId],
                    ['score' => $ue]
                );
            }
        }

        // Compute and save final grade to course_registrations
        foreach ($data['ca'] ?? [] as $studentId => $ca) {
            $ue = $data['ue'][$studentId] ?? 0;
            $grade = CourseRegistration::computeGrade($ca, $ue);
            CourseRegistration::updateOrCreate(
                ['student_id' => $studentId, 'subject_id' => $subjectId, 'semester_id' => $semesterId],
                ['ca_score' => $ca, 'ue_score' => $ue, 'grade_letter' => $grade['letter'], 'grade_point' => $grade['point'], 'status' => $grade['status']]
            );
        }

        return redirect()->back()->with('success', ['title'=>'Saved', 'body'=>'Marks saved to assessment system.']);
    }

    // Bulk upload via CSV
    public function uploadForm()
    {
        return view('exam_marks.upload');
    }

    public function downloadTemplate(Request $request)
    {
        $subjectId = $request->input('subject_id');
        $semesterId = $request->input('semester_id');

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="marks_template.csv"',
        ];
        $callback = function() use ($subjectId, $semesterId) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['idNo', 'firstName', 'lastName', 'ca_score', 'ue_score']);

            if ($subjectId && $semesterId) {
                $sem = Semester::with('academicYear')->find($semesterId);
                $session = $sem->academicYear->name ?? '';
                $levelTerm = 'Semester ' . ($sem->semester_number ?? '1');

                $students = Student::whereHas('registered', function($q) use ($session, $levelTerm) {
                        $q->where('session', $session)->where('levelTerm', $levelTerm);
                    })
                    ->orderBy('firstName')
                    ->get(['idNo', 'firstName', 'lastName']);

                foreach ($students as $s) {
                    fputcsv($file, [$s->idNo, $s->firstName, $s->lastName, '', '']);
                }
            } else {
                // No subject/semester selected — provide generic example
                fputcsv($file, ['T24-03-00000', 'Nouman', 'Tunda', '', '']);
                fputcsv($file, ['T22-03-10001', 'Chebet', 'Mbowe', '', '']);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function uploadStore(Request $request)
    {
        $v = Validator::make($request->all(), [
            'marks_file' => 'required|file|mimes:csv,txt',
            'subject_id' => 'required|exists:subject,id',
            'semester_id' => 'required|exists:semesters,id',
        ]);
        if ($v->fails()) return back()->withErrors($v);

        $subjectId = $request->subject_id;
        $semesterId = $request->semester_id;

        // Auto-create plan with default components
        $plan = AssessmentPlan::firstOrCreate(
            ['subject_id' => $subjectId, 'semester_id' => $semesterId],
            ['subject_id' => $subjectId, 'semester_id' => $semesterId]
        );
        if ($plan->components()->count() == 0) {
            $plan->components()->create(['name' => 'Course Work', 'type' => 'CA', 'max_score' => 40, 'weight' => 40]);
            $plan->components()->create(['name' => 'University Exam', 'type' => 'UE', 'max_score' => 60, 'weight' => 60]);
        }
        $plan->load('components');
        $caComp = $plan->components->where('type', 'CA')->first();
        $ueComp = $plan->components->where('type', 'UE')->first();

        $file = $request->file('marks_file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle, 1000, ',');
        if (!$header || count($header) < 3) {
            fclose($handle);
            return back()->withErrors(['marks_file' => 'CSV must have columns: idNo, ca_score, ue_score']);
        }
        $header = array_map(function($h) { return trim(str_replace("\xEF\xBB\xBF", '', $h)); }, $header);

        $imported = 0; $errors = [];
        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            $data = array_combine($header, $row);
            $student = Student::where('idNo', trim($data['idNo'] ?? ''))->first();
            if (!$student) { $errors[] = 'Student not found: ' . ($data['idNo'] ?? '?'); continue; }

            $ca = (float) ($data['ca_score'] ?? 0);
            $ue = (float) ($data['ue_score'] ?? 0);

            if ($caComp) {
                AssessmentMark::updateOrCreate(
                    ['assessment_component_id' => $caComp->id, 'student_id' => $student->id],
                    ['score' => $ca]
                );
            }
            if ($ueComp) {
                AssessmentMark::updateOrCreate(
                    ['assessment_component_id' => $ueComp->id, 'student_id' => $student->id],
                    ['score' => $ue]
                );
            }

            $grade = CourseRegistration::computeGrade($ca, $ue);
            CourseRegistration::updateOrCreate(
                ['student_id' => $student->id, 'subject_id' => $subjectId, 'semester_id' => $semesterId],
                ['ca_score' => $ca, 'ue_score' => $ue, 'grade_letter' => $grade['letter'], 'grade_point' => $grade['point'], 'status' => $grade['status']]
            );
            $imported++;
        }
        fclose($handle);

        $msg = "$imported students imported. ";
        if (!empty($errors)) $msg .= 'Errors: ' . implode('; ', array_slice($errors, 0, 5));
        return redirect()->route('exam.marks.create')->with('success', ['title'=>'Imported', 'body'=>$msg]);
    }
}
