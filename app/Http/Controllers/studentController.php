<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;
use App\Student;
use App\Course;
use App\Department;
use Validator;
use Session;
use Carbon\Carbon;
use App\Registration;
use App\Transformers\StudentTransformer;


class studentController extends Controller {
	protected $student;
	    protected $semesters=[
        'Semester 1' => 'Semester 1',
        'Semester 2' => 'Semester 2',
    ];
	public function __construct(Student $student)
	{
		$this->middleware('hod',['except' => ['registeredStudentList']]);
		$this->student = $student;
	}
	/**
	* Display  listing of the resource.
	*
	* @return Response
	*/

	public function index()
	{
		if(Session::has('deptId'))
		{
			$departments = Department::select('id','name')->orderby('name','asc')->lists('name', 'id');
			$selectDep=Session::get('deptId');
			$students =Student::with('course')->where('department_id',$selectDep)->get();
			return view('student.index',compact('students','departments','selectDep'));
		}
		$departments = Department::select('id','name')->orderby('name','asc')->lists('name', 'id');
		$selectDep="";
		$students =array();
		return view('student.index',compact('students','departments','selectDep'));
	}
	public function index2(Request $request)
	{
		Session::put('deptId',$request->department_id);
		$departments = Department::select('id','name')->orderby('name','asc')->lists('name', 'id');
		$selectDep=$request->department_id;
		$students =Student::with('course')->where('department_id',$selectDep)->get();

		return view('student.index',compact('students','departments','selectDep'));
	}

	public function studentList($dID, $session)
	{
		$regYear = (int) substr($session, 0, 4);
		$students = \App\Student::select('students.id','idNo','firstName','lastName','middleName','students.session as admSession','course_id')
			->where('students.department_id', $dID)
			->whereNotNull('course_id')
			->whereNull('students.deleted_at')
			->get()
			->filter(function($s) use ($regYear) {
				$admYear = (int) substr($s->admSession, 0, 4);
				if ($regYear < $admYear) return false;
				$course = \App\Course::find($s->course_id);
				$duration = $course ? (int) $course->duration_years : 4;
				$maxYear = $admYear + $duration;
				return $regYear <= $maxYear;
			})
			->values()
			->map(function($s) {
				return ['id' => $s->id, 'idNo' => $s->idNo, 'firstName' => $s->firstName, 'lastName' => $s->lastName, 'middleName' => $s->middleName];
			});

		return response()->json(['success' => true, 'students' => $students], 200);
	}
	public function studentListByBatch($dID, $batch)
	{
		$students = \App\Student::select('id','idNo','firstName','lastName','middleName')
			->where('department_id', $dID)
			->where('session', $batch)
			->whereNotNull('course_id')
			->whereNull('deleted_at')
			->orderBy('firstName')
			->get();
		return response()->json(['success' => true, 'students' => $students], 200);
	}
	public function registeredStudentList($dID,$session,$semester)
	{

		$sdts=Registration::with(array('student' =>  function($query){
			$query->select('id','idNo','firstName','middleName','lastName','photo');
		}))
		->where('department_id',$dID)
		->where('session',$session)
		->where('levelTerm',$semester)
		->get();

		$students= Fractal()->collection($sdts, new StudentTransformer());
		return Response()->json([
			'success' => true,
			'students' => $students
		], 200);

	}



	/**
	* Show the form for creating a new resource.
	*
	* @return Response
	*/
	public function create()
	{
		$courses = $this->courseOptions();
		return view("student.create",compact("courses"));
	}


	/**
	* Store a newly created resource in storage.
	*
	* @return Response
	*/
	public function store(Request $request)
	{
		$data=$request->all();
		$rules=[
			'idNo' => 'required|unique:students',
			'session' => 'required',
			'course_id' => 'required|exists:courses,id',
			'bncReg' => 'required',
			'batchNo' => 'required',
			'firstName' => 'required',
			'lastName' => 'required',
			'gender' => 'required',
			'religion' => 'required',
			'bloodgroup' => 'required',
			'nationality' => 'required',
			'dob' => 'required',
			'photo' => 'required|mimes:jpeg,jpg,png',
			'mobileNo' => 'required',
			'fatherName' => 'required',
			'fatherMobileNo' => 'required',
			'motherName' => 'required',
			'motherMobileNo' => 'required',
			'presentAddress' => 'required',
			'parmanentAddress' => 'required'
		];
		$validator = Validator::make($data, $rules);
		if ($validator->fails())
		{
			return Redirect::route('student.create')->withInput()->withErrors($validator);
		}
		$course = Course::find($data['course_id']);
		if (!$course) {
			$validator->errors()->add('course_id', 'Selected course does not exist.');
			return Redirect::route('student.create')->withInput()->withErrors($validator);
		}
		$directory = public_path() . "/assets/images/students/";
		$fextention = $data['photo']->getClientOriginalExtension();
		$fileName=str_replace(' ','_',$data['idNo']).'.'.$fextention;
		$data['photo']->move($directory,$fileName);
		$data['photo']=$fileName;
		$student = new Student;
		$student->create($data);
			// Auto-create User account for student login (idNo / lastName)
			$user = AppUser::firstOrCreate(['login' => $data['idNo']], [
			    'firstname' => $data['firstName'],
			    'lastname'  => $data['lastName'],
			    'login'     => $data['idNo'],
			    'password'  => $data['lastName'],
			    'group'     => 'Student',
			    'email'     => $data['idNo'] . '@student.osums.edu',
			]);
		$notification= array('title' => 'Data Store', 'body' => 'Student admitted succesfully.');
		return Redirect::route('student.create')->with("success",$notification);
	}


	/**
	* Display the specified resource.
	*
	* @param  int  $id
	* @return Response
	*/
	public function show($id)
	{
		try
		{

			$student = Student::with(['department', 'course'])->where('id',$id)->first();
			return view('student.show',compact('student'));
		}
		catch (\Exception $e)
		{
			$notification= array('title' => 'Data Edit', 'body' => "There is no record.");
			return Redirect::route('student.index')->with("error",$notification);
		}
	}


	/**
	* Show the form for editing the specified resource.
	*
	* @param  int  $id
	* @return Response
	*/
	public function edit($id)
	{
		try
		{
			$courses = $this->courseOptions();
			$student = Student::findOrFail($id);
			return view('student.edit',compact('courses','student'));
		}
		catch (\Exception $e)
		{
			$notification= array('title' => 'Data Edit', 'body' => "There is no record.");
			return Redirect::route('student.index')->with("error",$notification);
		}
	}


	/**
	* Update the specified resource in storage.
	*
	* @param  int  $id
	* @return Response
	*/
	public function update(Request $request,$id)
	{
		$data=$request->all();
		$rules=[
			'course_id' => 'required|exists:courses,id',
			'bncReg' => 'required',
			'batchNo' => 'required',
			'firstName' => 'required',
			'lastName' => 'required',
			'gender' => 'required',
			'religion' => 'required',
			'bloodgroup' => 'required',
			'nationality' => 'required',
			'dob' => 'required',
			'photo' => 'mimes:jpeg,jpg,png',
			'mobileNo' => 'required',
			'fatherName' => 'required',
			'fatherMobileNo' => 'required',
			'motherName' => 'required',
			'motherMobileNo' => 'required',
			'presentAddress' => 'required',
			'parmanentAddress' => 'required'
		];
		$validator = Validator::make($data, $rules);
		if ($validator->fails())
		{
			return Redirect::route('student.edit',[$id])->withInput()->withErrors($validator);
		}
		$student = Student::findOrFail($id);
		$course = Course::find($data['course_id']);
		if (!$course) {
			$validator->errors()->add('course_id', 'Selected course does not exist.');
			return Redirect::route('student.edit', [$id])->withInput()->withErrors($validator);
		}
		try {
			if($request->exists('photo'))
			{
				$directory = public_path() . "/assets/images/students/";
				$oldPhotoPath = $directory . $student->photo;
				if ($student->photo && $student->photo !== 'default.png' && file_exists($oldPhotoPath)) {
					@unlink($oldPhotoPath);
				}
				$fextention = $data['photo']->getClientOriginalExtension();
				$fileName = str_replace(' ', '_', $student->idNo) . '.' . $fextention;
				$data['photo']->move($directory, $fileName);
				$data['photo'] = $fileName;
			}
			else{
				$data['photo']=$student->photo;
			}
			$data['session']=$student->session;
			$data['idNo']=$student->idNo;
			$student->fill($data)->save();
			$notification= array('title' => 'Data Update', 'body' => "Student Information Updated Succesfully.");
			return Redirect::route('student.index')->with("success",$notification);
		}
		catch (\Exception $e)
		{
			$notification= array('title' => 'Data Update', 'body' => "There is no record.");
			return Redirect::route('student.index')->with("error",$notification);
		}
	}


	/**
	* Remove the specified resource from storage.
	*
	* @param  int  $id
	* @return Response
	*/
	public function destroy($id)
	{
		$student = Student::findOrFail($id);
		$student->delete();
		$notification= array('title' => 'Data Delete', 'body' => 'Student Deleted Succesfully.');
		return Redirect::route('student.index')->with("success",$notification);
	}

	/**
	 * Show the form for uploading a CSV/Excel file to create multiple students.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function uploadForm()
	{
		$departments = Department::select('id','name')->orderby('name','asc')->lists('name', 'id');
		return view('student.upload',compact('departments'));
	}

	/**
	 * Download a CSV template for bulk student import.
	 *
	 * @return \Symfony\Component\HttpFoundation\StreamedResponse
	 */
	public function downloadTemplate()
	{
		$headers = [
			'Content-Type' => 'text/csv',
			'Content-Disposition' => 'attachment; filename="students_template.csv"',
		];
		$columns = [
			'idNo','session','course_id','bncReg','batchNo','firstName','middleName','lastName','mobileNo','gender','religion','bloodgroup','nationality','dob','fatherName','fatherMobileNo','motherName','motherMobileNo','presentAddress','parmanentAddress','isActive'
		];
		$callback = function() use ($columns) {
			$file = fopen('php://output', 'w');
			fputcsv($file, $columns);
			// add an example row (optional)
			fputcsv($file, ['T2Y-03-XXXXX','202Y-202Z',1,'T2Y-03-XXXXX','202Y-202Z','John','A.','Doe','0123456789','Male','Christian','O+','Tanzanian','01/01/2000','Father Name','0123456789','Mother Name','0123456789','Address 1','Address 2',1]);
			fclose($file);
		};
		return response()->stream($callback, 200, $headers);
	}

	/**
	 * Process the uploaded CSV/Excel file and create students in bulk.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function uploadStore(Request $request)
	{
		// Laravel 5.2 does not have the Request::validate helper, use the Validator facade instead
		$validator = Validator::make($request->all(), [
			'students_file' => 'required|file|mimes:csv,txt',
		]);
		if ($validator->fails()) {
			return back()->withErrors($validator);
		}

		$file = $request->file('students_file');
		$handle = fopen($file->getRealPath(), 'r');
		$header = fgetcsv($handle, 1000, ',');
		if (!$header) {
			fclose($handle);
			return back()->withErrors(['students_file' => 'Could not read CSV header.']);
		}
		$header = array_map(function($h) {
			return trim(str_replace("\xEF\xBB\xBF", '', $h));
		}, $header);
		$required = ['idNo','session','course_id','bncReg','batchNo','firstName','middleName','lastName','mobileNo','gender','religion','bloodgroup','nationality','dob','fatherName','fatherMobileNo','motherName','motherMobileNo','presentAddress','parmanentAddress','isActive'];
		$missing = array_diff($required, $header);
		if (!empty($missing)) {
			fclose($handle);
			return back()->withErrors(['students_file' => 'Invalid CSV header. Missing: ' . implode(', ', $missing) . '. Please use the provided template.']);
		}
		$created = 0;
		while (($row = fgetcsv($handle, 1000, ',')) !== false) {
			$data = array_combine($header, $row);
			if (empty($data['idNo']) || empty($data['course_id'])) {
				continue;
			}
			if (Student::where('idNo', $data['idNo'])->exists()) {
				continue;
			}
			if (!Course::where('id', $data['course_id'])->exists()) {
				continue;
			}
			$data['photo'] = 'default.png';
			// convert dob to proper format if needed
			if (!empty($data['dob'])) {
				try {
					$data['dob'] = Carbon::createFromFormat('d/m/Y', $data['dob'])->format('Y-m-d');
				} catch (\Exception $e) {
					$data['dob'] = null;
				}
			}
			Student::create($data);
			$created++;
		}
		fclose($handle);
		$notification = ['title' => 'Bulk Upload', 'body' => "$created students imported successfully."];
		return Redirect::route('student.index')->with('success', $notification);
	}

	/**
	*These below code is responsible for
	*student registration
	*
	*
	*/
	public function regCreate()
	{
		$students=[];
		$semesters= $this->semesters;
		$departments = Department::select('id','name')->orderby('name','asc')->lists('name', 'id');
		$sessions = \App\AcademicYear::orderBy('name', 'desc')->lists('name', 'name');
		$batches = \App\Student::select('session','session')->distinct()->orderBy('session','desc')->lists('session','session');
		return view('student.registration.create',compact('departments','students','semesters','sessions','batches'));
	}
	public function regStore(Request $request){
		$data=$request->all();
		$rules=[
			'department_id' => 'required',
			//	'ids' => 'required',
			//'registeredIds' => 'required',
			'levelTerm' => 'required',
			'session' => 'required',
		];
		$validator = Validator::make($data, $rules);
		//$errors=$validator->messages()->toArray();
		if ($validator->fails()){
			return back()->withErrors($validator);
			// return Response()->json([
			// 	'error' => true,
			// 	'message' => $errors
			// ], 400);
		}
		if(!$request->exists('ids') || !count($data['ids']) || !$request->exists('registeredIds') || !count($data['registeredIds'])){
			$validator->errors()->add('Student', 'Please select at least one student!');
			return back()->withErrors($validator);
		}
		$toBeRegisterStudents = [] ;
		$alreadyRegistered = 0;
		$newRegistration = 0;
			$regYearStart = (int) substr($data['session'], 0, 4);
			$errors = [];
		foreach ($data['ids'] as  $id){
			$isExists = false;
				$student = \App\Student::with('course')->find($id);
				if (!$student) continue;
				$admYear = (int) substr($student->session, 0, 4);
				if ($regYearStart < $admYear) {
					$errors[] = $student->idNo . ': Cannot reg in ' . $data['session'] . ' (admitted ' . $student->session . ').';
					continue;
				}
				$duration = $student->course ? (int) $student->course->duration_years : 4;
				$maxYear = $admYear + $duration;
				if ($regYearStart > $maxYear) {
					$errors[] = $student->idNo . ': ' . $data['session'] . ' exceeds ' . $duration . '-year.';
					continue;
				}
			$isWantTo = $this->isWantToRegister($id,$data['registeredIds']);
			if($isWantTo){
				$sts = Registration::where('department_id',$data['department_id'])
				->where('students_id',$id)
				->where('levelTerm',$data['levelTerm'])->first();
				if($sts){
					$isExists = true;
					$alreadyRegistered +=1;

				}
				if(!$isExists){
					$toBeRegisterStudents [] = [
						'levelTerm' => $data['levelTerm'],
						'department_id' => $data['department_id'],
						'students_id' => $id,
						'session' => $data['session'],
						'created_at' => Carbon::now(),
						'updated_at' => Carbon::now()
					];
					$newRegistration +=1;
				}
			}
		}
		// $isExists = Registration::where('department_id',$data['department_id'])
		// ->where('students_id',$data['students_id'])
		// ->where('levelTerm',$data['levelTerm'])->first();
		//
		// if($isExists){
		// 	return Response()->json([
		// 		'error' => true,
		// 		'message' => ['Data Exists'=>"This student already registered!"]
		// 	], 400);
		// }
			if (!empty($errors)) {
				$errMsg = implode('<br>', $errors);
				return back()->with('error', ['title' => 'Registration Failed', 'body' => $errMsg]);
			}
		Registration::insert($toBeRegisterStudents);
		$notification= array('title' => 'Data Store', 'body' => $newRegistration.' students registered.');
		// return Response()->json([
		// 	'success' => true,
		// 	'message' => $notification
		// ], 200);
		if($alreadyRegistered){
			$notification= array('title' => 'Data Store', 'body' => $newRegistration.' students newly registerd and '.$alreadyRegistered.' has already registered!');
		}
		return back()->with("success",$notification);

	}
	private  function isWantToRegister($id,$registeredIds)
	{
		foreach ($registeredIds as $key => $value) {
			if($id==$key)
			{
				return 1;
			}
		}
		return 0;
	}

	public function regIndex(){
		$departments = Department::select('id','name')->orderby('name','asc')->lists('name', 'id');
		$sessions = \App\AcademicYear::orderBy('name', 'desc')->lists('name', 'name');
		$selectDep="";
		$students =array();
		$semesters= $this->semesters;
		$selectSem="";
		$session="";
		return view('student.registration.index',compact('session','students','sessions','departments','selectDep','semesters','selectSem'));
	}
	public function regList(Request $request){

		$students=Registration::with(array('student' =>  function($query){
			$query->select('id','idNo','firstName','middleName','lastName','photo');
		}))
		->where('department_id',$request->input('department_id'))
		->where('session',$request->input('session'))
		->where('levelTerm',$request->input('levelTerm'))
		->get();

		$departments = Department::select('id','name')->orderby('name','asc')->lists('name', 'id');
		$sessions = \App\AcademicYear::orderBy('name', 'desc')->lists('name', 'name');
		$selectDep=$request->input('department_id');
		$semesters= $this->semesters;
		$selectSem=$request->input('levelTerm');
		$session=$request->input('session');
		return view('student.registration.index',compact('session','sessions','students','departments','selectDep','semesters','selectSem'));

	}
	public function regDestroy($id)
	{
		$student=Registration::findOrFail($id);
		$student->delete();
		$notification= array('title' => 'Data Delete', 'body' => 'Cancel student registration.');
		return Response()->json([
			'success' => true,
			'message' => $notification
		], 200);

	}

	public function assignCourse(Request $request, $id)
	{
		$data = $request->all();
		$validator = Validator::make($data, [
			'course_id' => 'required|exists:courses,id',
		]);

		if ($validator->fails()) {
			return redirect()->back()->withErrors($validator);
		}

		$student = Student::findOrFail($id);
		if (!Registration::where('students_id', $id)->exists()) {
			$notification = ['title' => 'Assignment Error', 'body' => 'Student must be registered before assigning a course.'];
			return redirect()->back()->with('error', $notification);
		}

		$course = Course::find($data['course_id']);
		$student->course_id = $data['course_id'];
		$student->save();
		$notification = ['title' => 'Course Assigned', 'body' => 'Course assigned successfully.'];
		return redirect()->back()->with('success', $notification);
	}

	protected function courseOptions()
	{
		$courses = [];
		foreach (Course::with('department')->orderBy('name', 'asc')->get() as $course) {
			$departmentName = $course->department ? $course->department->name : 'Unassigned';
			if (!isset($courses[$departmentName])) {
				$courses[$departmentName] = [];
			}
			$courses[$departmentName][$course->id] = $course->name;
		}

		return $courses;
	}
}
