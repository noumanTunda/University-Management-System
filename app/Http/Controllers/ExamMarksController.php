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
use App\ExamType;
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
        $subjects = Subject::where('department_id', $deptId)->select('id', 'name', 'code')->orderBy('name');
        if (auth()->user()->group === 'Teacher') {
            $subIds = auth()->user()->subjects()->pluck('subject.id')->toArray();
            $subjects->whereIn('id', $subIds);
        }
        $subjects = $subjects->get();
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

        $examTypes = ExamType::all();
        return view('exam_marks.entry', compact('components', 'planId', 'students', 'marks', 'subjectId', 'semesterId', 'examTypes'));
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

        // Support both scores[component_id][student_id] AND ca[]/ue[] formats
        if (isset($data['scores'])) {
            // Plan-based format: scores[component_id][student_id]
            foreach ($data['scores'] as $compId => $studentScores) {
                foreach ($studentScores as $studentId => $score) {
                    if ($score === '' || $score === null) continue;
                    AssessmentMark::updateOrCreate(
                        ['assessment_component_id' => $compId, 'student_id' => $studentId],
                        ['score' => $score]
                    );
                }
            }
        } else {
            // Legacy format: ca[] and ue[]
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
        }

        // Get exam_type_id from form (default to Regular = 1)
        $examTypeId = isset($data['exam_type_id']) ? (int)$data['exam_type_id'] : 1;

        // Compute and save final grade to course_registrations
        if (isset($data['scores'])) {
            foreach ($data['scores'] as $compId => $studentScores) {
                $comp = $plan->components->where('id', $compId)->first();
                $type = $comp ? $comp->type : 'CA';
                foreach ($studentScores as $studentId => $score) {
                    if ($score === '' || $score === null) continue;
                    // Accumulate by student - get or create registration record
                    $reg = CourseRegistration::firstOrNew(
                        ['student_id' => $studentId, 'subject_id' => $subjectId, 'semester_id' => $semesterId]
                    );
                    if ($type === 'CA') $reg->ca_score = ($reg->ca_score ?? 0) + $score;
                    if ($type === 'UE') $reg->ue_score = ($reg->ue_score ?? 0) + $score;
                    $grade = CourseRegistration::computeGrade($reg->ca_score ?? 0, $reg->ue_score ?? 0, $examTypeId);
                    $reg->grade_letter = $grade['letter'];
                    $reg->grade_point = $grade['point'];
                    $reg->status = $examTypeId == 2 ? 'Special' : ($examTypeId == 3 ? 'Supp' : ($examTypeId == 4 ? 'Retake' : $grade['status']));
                    $reg->save();
                }
            }
        } else {
            foreach ($data['ca'] ?? [] as $studentId => $ca) {
                $ue = $data['ue'][$studentId] ?? 0;
                $grade = CourseRegistration::computeGrade($ca, $ue, $examTypeId);
                $status = $examTypeId == 2 ? 'Special' : ($examTypeId == 3 ? 'Supp' : ($examTypeId == 4 ? 'Retake' : $grade['status']));
                CourseRegistration::updateOrCreate(
                    ['student_id' => $studentId, 'subject_id' => $subjectId, 'semester_id' => $semesterId],
                    ['ca_score' => $ca, 'ue_score' => $ue, 'grade_letter' => $grade['letter'], 'grade_point' => $grade['point'], 'status' => $status]
                );
            }
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

        // Get assessment plan components for this subject/semester
        $plan = AssessmentPlan::where('subject_id', $subjectId)->where('semester_id', $semesterId)->with('components')->first();
        $columns = ['idNo', 'firstName', 'lastName'];
        if ($plan && $plan->components->count() > 0) {
            foreach ($plan->components as $comp) {
                $columns[] = $comp->name;
            }
        } else {
            $columns[] = 'ca_score';
            $columns[] = 'ue_score';
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="marks_template.csv"',
        ];
        $callback = function() use ($subjectId, $semesterId, $columns, $plan) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

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
                    $row = [$s->idNo, $s->firstName, $s->lastName];
                    // Pre-fill existing marks if available
                    if ($plan) {
                        foreach ($plan->components as $comp) {
                            $mark = AssessmentMark::where('assessment_component_id', $comp->id)
                                ->where('student_id', $s->id)->first();
                            $row[] = $mark ? $mark->score : '';
                        }
                    } else {
                        $row[] = '';
                        $row[] = '';
                    }
                    fputcsv($file, $row);
                }
            } else {
                $row = ['T24-03-00000', 'Nouman', 'Tunda'];
                for ($i = 3; $i < count($columns); $i++) $row[] = '';
                fputcsv($file, $row);
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
        $plan = AssessmentPlan::where('subject_id', $subjectId)->where('semester_id', $semesterId)->with('components')->first();
        if (!$plan || $plan->components()->count() == 0) {
            return back()->withErrors(['marks_file' => 'No assessment plan found for this subject/semester. Create an assessment plan with components first.']);
        }
        $plan->load('components');
        $compMap = [];
        foreach ($plan->components as $comp) {
            $compMap[$comp->name] = $comp;
        }

        $file = $request->file('marks_file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle, 1000, ',');
        if (!$header || count($header) < 3) {
            fclose($handle);
            return back()->withErrors(['marks_file' => 'CSV must have at least idNo, firstName, lastName columns.']);
        }
        $header = array_map(function($h) { return trim(str_replace("\xEF\xBB\xBF", '', $h)); }, $header);

        $imported = 0; $errors = [];
        $caTotal = 0; $ueTotal = 0;
        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            $data = array_combine($header, $row);
            $student = Student::where('idNo', trim($data['idNo'] ?? ''))->first();
            if (!$student) { $errors[] = 'Student not found: ' . ($data['idNo'] ?? '?'); continue; }

            $caSum = 0; $ueSum = 0;
            foreach ($compMap as $compName => $comp) {
                $score = (float) ($data[$compName] ?? 0);
                if ($score > 0) {
                    AssessmentMark::updateOrCreate(
                        ['assessment_component_id' => $comp->id, 'student_id' => $student->id],
                        ['score' => $score]
                    );
                    if ($comp->type === 'CA') $caSum += ($score / $comp->max_score) * $comp->weight;
                    else $ueSum += ($score / $comp->max_score) * $comp->weight;
                }
            }

            // Compute final from plan weights
            $caWeight = $plan->ca_weight / 100;
            $ueWeight = $plan->ue_weight / 100;
            $caFinal = $caWeight * $caSum;
            $ueFinal = $ueWeight * $ueSum;
            $final = $caFinal + $ueFinal;
            $grade = CourseRegistration::computeGrade($caFinal, $ueFinal);

            CourseRegistration::updateOrCreate(
                ['student_id' => $student->id, 'subject_id' => $subjectId, 'semester_id' => $semesterId],
                ['ca_score' => $caFinal, 'ue_score' => $ueFinal, 'final_mark' => $final, 'grade_letter' => $grade['letter'], 'grade_point' => $grade['point'], 'status' => $grade['status']]
            );
            $imported++;
        }
        fclose($handle);

        $msg = "$imported students imported. ";
        if (!empty($errors)) $msg .= 'Errors: ' . implode('; ', array_slice($errors, 0, 5));
        return redirect()->route('exam.marks.create')->with('success', ['title'=>'Imported', 'body'=>$msg]);
    }
}
