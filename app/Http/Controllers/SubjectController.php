<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;

use App\Subject;
use App\Department;
use Validator;
use App\Transformers\SubjectTransformer;
class SubjectController extends Controller
{
    protected $subject;
    protected $semesters=[
    'L1T1' => '1st Year 1st Semester',
    'L1T2' => '1st Year 2nd Semester',
    'L2T1' => '2nd Year 1st Semester',
    'L2T2' => '2nd Year 2nd Semester',
    'L3T1' => '3rd Year 1st Semester',
    'L3T2' => '3rd Year 2nd Semester',
    'L4T1' => '4th Year 1st Semester',
    'L4T2' => '4th Year 2nd Semester'
    ];
    public function __construct(Subject $subject)
    {
        $this->middleware('admin', ['except' => ['subjetsByDptSem']]);
        $this->subject = $subject;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $semesters=$this->semesters;
        $subjects = Subject::with('department')->get();
        return view('subject.index', compact('subjects', 'semesters'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $semesters=$this->semesters;
        $departments = Department::select('id', 'name')->orderby('name', 'asc')->lists('name', 'id');
        return view('subject.create', compact('departments', 'semesters'));
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
         'name' => 'required',
         'code' => 'required|unique:subject,code',
         'credit' => 'required|numeric',
         'department_id' => 'required|numeric',
         'levelTerm' => 'required',
         'description' => 'required|max:255'
        ];
        $validator = \Validator::make($data, $rules);
        if ($validator->fails()) {
            return Redirect::route('subject.create')->withInput()->withErrors($validator);
        }
        else {

            $this->subject->create($data);
            $notification= array('title' => 'Data Store', 'body' => 'Subject Created Succesfully.');
            return Redirect::route('subject.create')->with("success", $notification);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        try
        {
            $semesters=$this->semesters;
            $departments = Department::select('id', 'name')->orderby('name', 'asc')->lists('name', 'id');
            $subject = Subject::findOrFail($id);
            return view('subject.edit', compact('departments', 'subject', 'semesters'));
        }
        catch (Exception $e)
        {
            $notification= array('title' => 'Data Edit', 'body' => "There is no record.");
            return Redirect::route('subject.index')->with("error", $notification);
        }

    }


    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update(Request $request,$id)
    {
        $data=$request->all();
        $rules=[
         'name' => 'required',
         'code' => 'required',
         'credit' => 'required|numeric',
         'department_id' => 'required|numeric',
         'levelTerm' => 'required',
         'description' => 'required|max:255'
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator);
        }
        else {
            try {
                $subject = Subject::findOrFail($id);
                $subject->fill($data)->save();
                $notification= array('title' => 'Data Update', 'body' => 'Subject Updated Succesfully.');
                return Redirect::route('subject.index')->with("success", $notification);
            }
            catch (Exception $e)
            {
                $notification= array('title' => 'Data Update', 'body' => "There is no record.");
                return Redirect::route('subject.index')->with("error", $notification);
            }
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        $subject = Subject::findOrFail($id);
        $subject->delete();
        $notification= array('title' => 'Data Delete', 'body' => 'Subject Deleted Succesfully.');
        return Redirect::route('subject.index')->with("success", $notification);

    }

    /**
     * Show the bulk upload form for subjects.
     */
    public function uploadForm()
    {
        $departments = Department::select('id','name')->orderby('name','asc')->lists('name','id');
        $semesters = $this->semesters;
        return view('subject.upload',compact('departments','semesters'));
    }

    /**
     * Download a CSV template for bulk subject import.
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="subjects_template.csv"',
        ];
        $columns = ['name','code','credit','department_id','description','levelTerm'];
        $callback = function() use ($columns) {
            $file = fopen('php://output','w');
            fputcsv($file,$columns);
            // example row
            fputcsv($file,['Biology','BIO101',3,1,'Basic Biology','L1T1']);
            fclose($file);
        };
        return response()->stream($callback,200,$headers);
    }

    /**
     * Process uploaded CSV and create subjects in bulk.
     */
    public function uploadStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subjects_file' => 'required|file|mimes:csv,txt',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator);
        }
        $file = $request->file('subjects_file');
        $handle = fopen($file->getRealPath(),'r');
        $header = fgetcsv($handle,1000,',');
        $required = ['name','code','credit','department_id','description','levelTerm'];
        if (array_diff($required,$header)) {
            fclose($handle);
            return back()->withErrors(['subjects_file'=>'Invalid CSV header. Use the provided template.']);
        }
        $created = 0;
        while (($row = fgetcsv($handle,1000,',')) !== false) {
            $data = array_combine($header,$row);
            if (empty($data['code'])) continue;
            // avoid duplicate code
            if (Subject::where('code',$data['code'])->exists()) continue;
            Subject::create($data);
            $created++;
        }
        fclose($handle);
        $notification = ['title'=>'Bulk Upload','body'=>"$created subjects imported successfully."];
        return Redirect::route('subject.index')->with('success',$notification);
    }
    public function subjetsByDptSem($department,$semester)
    {
        $subs = Subject::where('department_id', $department)
        ->where('levelTerm', $semester)->get();
        $subjects=Fractal()->collection($subs, new SubjectTransformer());
        return Response()->json(
            [
            'success' => true,
            'subjects' => $subjects
            ], 200
        );



    }


}
