<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;
use App\Student;
use App\Department;
use App\Subject;
use Validator;
use App\Exam;
use Carbon\Carbon;
use App\Transformers\MarksTransformer;
use App\ExamType;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use App\Serializers\MySerializer;

class ExamController extends Controller
{
  public function __construct()
	{
		$this->middleware('teacher');
	}
	protected $semesters=[
		'Semester 1' => 'Semester 1',
		'Semester 2' => 'Semester 2',
	];

  public function Create()
  {
    $subjects=[];
    $semesters= $this->semesters;
    $exams = ExamType::lists('name', 'id');
    $departments = Department::select('id','name')->orderby('name','asc')->lists('name', 'id');
    $sessions=Student::select('session','session')->distinct()->lists('session','session');
    return view('exam.create',compact('departments','sessions','semesters','exams','subjects'));
  }

  public function Store(Request $request)
  {
    $data=$request->all();
    $rules=[
      'session' => 'required',
      'department_id'=> 'required',
      'subject_id'=> 'required',
      'levelTerm'=> 'required',
      'ids'=> 'required',
      'ca_score'=> 'required',
      'ue_score'=> 'required',
    ];
    $validator = Validator::make($data, $rules);
    if ($validator->fails())
    {
      return redirect()->back()->withErrors($validator);
    }
    else {

      // Check if marks for this exam already exist.
      $exists = Exam::select('id')
          ->where('department_id', $data['department_id'])
          ->where('session', $data['session'])
          ->where('levelTerm', $data['levelTerm'])
          ->where('subject_id', $data['subject_id'])
          ->first();
      if ($exists) {
          $notification = ['title' => 'Validation', 'body' => 'Exam marks already exists for this subject/semester!'];
          return redirect()->back()->with('error', $notification);
      }
      $nowDateTime=Carbon::now()->toDateTimeString();
      foreach ($data['ids'] as  $id){
        $marksdata[] = [
          'department_id' => $data['department_id'],
          'session' => $data['session'],
          'levelTerm' => $data['levelTerm'],
          'subject_id' => $data['subject_id'],
          'students_id' => $id,
          'raw_score' => $data['ca_score'][$id] ?? 0,
          'percentage' => $data['ue_score'][$id] ?? 0,
          'created_at' => $nowDateTime,
        ];
      }

      Exam::insert($marksdata);
      $notification= array('title' => 'Data Store', 'body' => 'Exam Marks saved Succesfully.');
      return redirect()->back()->with("success",$notification);

    }
  }
  public function index(){
    $exam = "";
    $marks=[];
    $subjects=[];
    $semesters= $this->semesters;
    $exams = ExamType::lists('name', 'id');
    $departments = Department::select('id','name')->orderby('name','asc')->lists('name', 'id');
    $sessions=Student::select('session','session')->distinct()->lists('session','session');
    $selectDep="";
    $selectSem="";
    $selectSub = "";
    $session="";
    return view('exam.index',compact('selectDep','selectSem','selectSub','sessions','session','departments','marks','semesters','subjects','exam','exams'));

  }
  public function index2(Request $request){
    $stds=Exam::with(array('student' =>  function($query){
      $query->select('id','idNo','firstName','middleName','lastName');
    }))
    ->where('department_id',$request->input('department_id'))
    ->where('session',$request->input('session'))
    ->where('levelTerm',$request->input('levelTerm'))
    ->where('subject_id',$request->input('subject_id'))
    ->where('exam',$request->input('exam'))
    ->get();
    $manager = new Manager();
    $manager->setSerializer(new MySerializer());
    $stdData= new Collection($stds,new MarksTransformer());
    $marks=$manager->createData($stdData)->toArray();
    $departments = Department::select('id','name')->orderby('name','asc')->lists('name', 'id');
    $sessions=Student::select('session','session')->distinct()->lists('session','session');
    $subjectsQuery = Subject::select('id','name')->where('department_id',$request->input('department_id'))->where('levelTerm',$request->input('levelTerm'))->orderby('code','asc');
        if (auth()->user()->group === 'Teacher') {
            $currentYear = AcademicYear::where('name', 'LIKE', date('Y') . '-%')->orWhere('name', 'LIKE', '%-' . date('Y'))->orderBy('name', 'desc')->first();
        $currentYearName = $currentYear ? $currentYear->name : date('Y') . '-' . (date('Y') + 1);
        $currentYearId = $currentYear ? $currentYear->id : null;
        $subIds = auth()->user()->subjects()->wherePivot('academic_year_id', $currentYearId)->pluck('subject.id')->toArray();
            $subjectsQuery->whereIn('id', $subIds);
        }
        $subjects = $subjectsQuery->lists('name', 'id');

    $exam =$request->input('exam');
    $semesters= $this->semesters;
    $exams = ExamType::lists('name', 'id');
    $selectDep=$request->input('department_id');
    $selectSem=$request->input('levelTerm');
    $selectSub = $request->input('subject_id');
    $session=$request->input('session');
    return view('exam.index',compact('selectDep','selectSem','selectSub','sessions','session','departments','marks','semesters','subjects','exam','exams'));


  }
  public function update(Request $request,$id){
    $data=$request->all();
    $rules=[
      'raw_score' => 'required',
      'percentage' => 'required',
      'weight' => 'required',
    ];
    $validator = Validator::make($data, $rules);
    $errors=$validator->messages()->toArray();
    if ($validator->fails())
    {
      return Response()->json([
        'error' => true,
        'message' => $errors
      ], 400);
    }
    else {
      $exam = Exam::findOrFail($id);
      $exam->fill($data)->save();

      return Response()->json([
        'success' => true,
        'message' => "Marks updated successfully.",
        'marks' => $exam
      ], 200);

    }
  }
}
