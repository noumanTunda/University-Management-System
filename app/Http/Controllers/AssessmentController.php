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
use App\Student;
use App\Registration;
use App\AcademicYear;
use Validator;
use Redirect;

class AssessmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('teacher');
    }

    public function index()
    {
        $plans = AssessmentPlan::with('subject.department', 'semester.academicYear', 'components')
            ->where(function($q) {
                $q->where('is_template', false)->orWhereNull('is_template');
            });
        if (auth()->user()->group === 'Teacher') {
            $currentYear = AcademicYear::where('name', 'LIKE', date('Y') . '-%')->orWhere('name', 'LIKE', '%-' . date('Y'))->orderBy('name', 'desc')->first();
        $currentYearName = $currentYear ? $currentYear->name : date('Y') . '-' . (date('Y') + 1);
        $currentYearId = $currentYear ? $currentYear->id : null;
        $subIds = auth()->user()->subjects()->pluck('subject.id')->toArray();
            $plans->whereIn('subject_id', $subIds);
        }
        $plans = $plans->get();
        $templates = AssessmentPlan::with('components')->where('is_template', true)->get();
        return view('assessment.index', compact('plans', 'templates'));
    }

    public function create()
    {
        $subjects = Subject::select('id','name','code','department_id')->with('department')->orderBy('name');
        if (auth()->user()->group === 'Teacher') {
            $currentYear = AcademicYear::where('name', 'LIKE', date('Y') . '-%')->orWhere('name', 'LIKE', '%-' . date('Y'))->orderBy('name', 'desc')->first();
        $currentYearName = $currentYear ? $currentYear->name : date('Y') . '-' . (date('Y') + 1);
        $currentYearId = $currentYear ? $currentYear->id : null;
        $subIds = auth()->user()->subjects()->pluck('subject.id')->toArray();
            $subjects->whereIn('id', $subIds);
        }
        $subjects = $subjects->get();
        $semesters = Semester::with('academicYear')->orderBy('id')->get();
        $templates = AssessmentPlan::with('components')->where('is_template', true)->orderBy('template_name')->get();
        return view('assessment.create', compact('subjects', 'semesters', 'templates'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $v = Validator::make($data, [
            'subject_id' => 'required|exists:subject,id',
            'semester_id' => 'required|exists:semesters,id',
        ]);
        if ($v->fails()) return redirect()->back()->withErrors($v);
        $plan = AssessmentPlan::create([
            'subject_id' => $data['subject_id'],
            'semester_id' => $data['semester_id'],
        ]);
        // If a template plan is selected, copy its components
        if (!empty($data['template_id'])) {
            $template = AssessmentPlan::with('components')->find($data['template_id']);
            if ($template) {
                foreach ($template->components as $comp) {
                    $plan->components()->create($comp->toArray());
                }
                return redirect()->route('assessment.index')->with('success', ['title'=>'Created','body'=>'Plan created from template "'.$template->template_name.'".']);
            }
        }
        return redirect()->route('assessment.components', $plan->id)->with('success', ['title'=>'Created','body'=>'Plan created. Add components.']);
    }

    public function components($planId)
    {
        $plan = AssessmentPlan::with('subject.department', 'semester.academicYear', 'components')->findOrFail($planId);
        return view('assessment.components', compact('plan'));
    }

    public function storeComponent(Request $request, $planId)
    {
        $plan = AssessmentPlan::findOrFail($planId);
        $v = Validator::make($request->all(), [
            'name' => 'required|max:100',
            'type' => 'required|in:CA,UE',
            'max_score' => 'required|numeric|min:1',
            'weight' => 'required|numeric|min:0|max:100',
        ]);
        if ($v->fails()) return redirect()->back()->withErrors($v);
        $plan->components()->create($request->all());
        return redirect()->route('assessment.components', $planId)->with('success', ['title'=>'Added','body'=>'Component added.']);
    }

    public function destroyComponent($id)
    {
        $comp = AssessmentComponent::findOrFail($id);
        $planId = $comp->assessment_plan_id;
        $comp->delete();
        return redirect()->route('assessment.components', $planId)->with('success', ['title'=>'Deleted','body'=>'Component removed.']);
    }

    public function marks($componentId)
    {
        $comp = AssessmentComponent::with('plan.subject', 'plan.semester')->findOrFail($componentId);
        $plan = $comp->plan;
        $students = Student::whereHas('registered', function($q) use ($plan) {
            $q->where('levelTerm', 'L'.$plan->semester->semester_number.'T1')
              ->orWhere('levelTerm', 'L'.$plan->semester->semester_number.'T2');
        })->orderBy('firstName')->get();
        $marks = AssessmentMark::where('assessment_component_id', $componentId)->get()->keyBy('student_id');
        return view('assessment.marks', compact('comp', 'students', 'marks'));
    }

    public function storeMarks(Request $request, $componentId)
    {
        $comp = AssessmentComponent::findOrFail($componentId);
        foreach ($request->input('scores', []) as $studentId => $score) {
            if ($score === '' || $score === null) continue;
            AssessmentMark::updateOrCreate(
                ['assessment_component_id' => $componentId, 'student_id' => $studentId],
                ['score' => $score]
            );
        }
        return redirect()->back()->with('success', ['title'=>'Saved','body'=>'Marks saved.']);
    }

    // Compute final results from all components
    public function compute($planId)
    {
        $plan = AssessmentPlan::with('components.marks.student', 'subject', 'semester')->findOrFail($planId);
        $students = Student::whereHas('registered')->get();
        $caWeight = $plan->ca_weight / 100;
        $ueWeight = $plan->ue_weight / 100;

        foreach ($students as $student) {
            $caTotal = 0; $caMax = 0;
            $ueTotal = 0; $ueMax = 0;

            foreach ($plan->components as $comp) {
                $mark = AssessmentMark::where('assessment_component_id', $comp->id)
                    ->where('student_id', $student->id)->first();
                $score = $mark ? $mark->score : 0;
                $weighted = ($score / $comp->max_score) * $comp->weight;
                if ($comp->type === 'CA') { $caTotal += $weighted; $caMax += $comp->weight; }
                else { $ueTotal += $weighted; $ueMax += $comp->weight; }
            }

            $caFinal = $caMax > 0 ? ($caTotal / $caMax) * $caWeight * 100 : 0;
            $ueFinal = $ueMax > 0 ? ($ueTotal / $ueMax) * $ueWeight * 100 : 0;
            $final = $caFinal + $ueFinal;
            $grade = CourseRegistration::computeGrade($caFinal, $ueFinal);

            CourseRegistration::updateOrCreate(
                ['student_id' => $student->id, 'subject_id' => $plan->subject_id, 'semester_id' => $plan->semester_id],
                [
                    'ca_score' => $caFinal,
                    'ue_score' => $ueFinal,
                    'grade_letter' => $grade['letter'],
                    'grade_point' => $grade['point'],
                    'status' => $grade['status'],
                ]
            );
        }
        return redirect()->route('assessment.index')->with('success', ['title'=>'Computed','body'=>'Grades computed and saved.']);
    }
}
