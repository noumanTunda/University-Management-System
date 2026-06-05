<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;
use Validator;
use App\FeeCollection;
use App\Department;
use App\Fee;
use Carbon\Carbon;
use DB;
use App\Sector;
use App\Account;
use App\Student;
use Session;
use App\Registration;
use App\Institute;
class FeesController extends Controller
{
    public function __construct()
    {
        $this->middleware('account');
    }
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
    public function index()
    {
        $departments = Department::select('id','name')->orderby('name','asc')->lists('name', 'id');
        $fees = Fee::with('department')->get();
        return view('fees.lists',compact('departments','fees'));
    }
    public function store(Request $request)
    {
        $data=$request->all();
        $rules=[
            'department_id' => 'required',
            'title' => 'required',
            'amount'=> 'required|numeric',
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator);
        }
        else {
            Fee::create($request->all());
            $notification= array('title' => 'Data Store', 'body' => 'Fee created Succesfully.');
            return redirect()->back()->with("success",$notification);
        }

    }

    public function destroy($id)
    {
        $fee = Fee::findOrFail($id);
        $fee->delete();
        $notification= array('title' => 'Data Delete', 'body' => 'Fee deleted Succesfully.');
        return redirect()->back()->with("success",$notification);
    }
    public function show($id)
    {
        $fee = Fee::findOrFail($id);
        return Response()->json([
            'success' => true,
            'fee' => $fee->amount
        ], 200);
    }
    public function lists($dId)
    {
        $fees = Fee::select('id','title')->where('department_id',$dId)->get();
        return Response()->json([
            'success' => true,
            'fees' => $fees
        ], 200);
    }
    public function getDue($stdId)
    {
        $due = FeeCollection::select(DB::RAW('IFNULL(sum(payableAmount),0)- IFNULL(sum(paidAmount),0) as dueamount'))
        ->where('students_id',$stdId)
        ->first();
        return Response()->json([
            'success' => true,
            'due' => $due->dueamount
        ], 200);
    }



    public function cCreate(){
        $isFeeSector= Sector::where('name','Fees')->where('type','Income')->first();
        $today = Carbon::today();
        $students=[];
        $semesters= $this->semesters;
        $departments = Department::select('id','name')->orderby('name','asc')->lists('name', 'id');
        $sessions=Student::select('session','session')->distinct()->lists('session','session');

        // $isFeeSector is a single model (or null). Use null check instead of count().
        if(!$isFeeSector){
            $notification= array('title' => 'Data Missing', 'body' => '"Fees" income sector missiong in accounting! Without it fee collection not possible.');
            session::flash('error',$notification);
        }
        return view('fees.collection',compact('departments','sessions','students','semesters','today'));
    }
    public function cStore(Request $request){
        $isFeeSector= Sector::where('name','Fees')->where('type','Income')->first();
        // Same null‑check for the store method
        if(!$isFeeSector){
            $notification= array('title' => 'Data Missing', 'body' => '"Fees" income sector missiong in accounting! Without it fee collection not possible.');
            return redirect()->back()->with('error',$notification);
        }
        $data=$request->all();
        // Validation now supports assigning fees to multiple students.
        // `students_id` is expected to be an array of student IDs.
        $rules=[
            'students_id' => 'required|array',
            'students_id.*' => 'required|exists:students,id',
            'gtotal' => 'required|numeric',
            'lateFee'=> 'required|numeric',
            'paidamount'=> 'required|numeric',
            'dueamount'=> 'required|numeric',
            'payDate'=> 'required',
            'fees'=> 'required|array',
            'fee'=> 'required|array',
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator);
        }
        // Prepare common fee collection data (except student ID which varies)
        $commonData = [
            'payableAmount' => $data['gtotal'],
            'lateFee' => $data['lateFee'],
            'paidAmount' => $data['paidamount'],
            'dueAmount' => $data['dueamount'],
            'payDate' => $data['payDate']
        ];

        DB::beginTransaction();
        try {
            // Iterate over each selected student and create a fee collection record.
            foreach ($data['students_id'] as $studentId) {
                $feeData = array_merge(['students_id' => $studentId], $commonData);
                $feeCol = FeeCollection::create($feeData);

                // Build fee items for this collection.
                $feeItemData = [];
                foreach ($data['fees'] as $key => $value) {
                    $feeItemData[] = [
                        'fee_collections_id' => $feeCol->id,
                        'name' => $value,
                        'amount' => $data['fee'][$key]
                    ];
                }
                DB::table('fee_collection_items')->insert($feeItemData);

                // Create accounting entry if payment was made.
                if ($data['paidamount'] > 0.00) {
                    $acData = [
                        'sectors_id' => $isFeeSector->id,
                        'amount' => $data['paidamount'],
                        'date' => $data['payDate'],
                        'description' => 'Student fee collections'
                    ];
                    Account::create($acData);
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            $trimmed = str_replace(["\r", "\n"], ' ', $e->getMessage());
            $notification = array('title' => 'Data Store Failed', 'body' => $trimmed);
            return redirect()->back()->with('error', $notification);
        }
        DB::commit();
        $notification = array('title' => 'Data Store', 'body' => 'Fee collection(s) successfully stored.');
        return redirect()->back()->with('success', $notification);
    }

    /**
     * Show a simple payment form for a single fee collection record.
     * The form allows the user to enter the amount being paid now.
     */
    public function payForm($id)
    {
        $feeCol = FeeCollection::findOrFail($id);
        // Load the related student to display name and registration number
        $student = \App\Student::find($feeCol->students_id);
        return view('fees.pay', compact('feeCol', 'student'));
    }

    /**
     * Process a payment for a fee collection.
     * It updates the paidAmount field and creates an accounting entry
     * if a fee sector exists.
     */
    public function pay(Request $request, $id)
    {
        $feeCol = FeeCollection::findOrFail($id);
        $data = $request->all();
        $rules = [
            'payAmount' => 'required|numeric|min:0.01',
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
        DB::beginTransaction();
        try {
            $feeCol->paidAmount = $feeCol->paidAmount + $data['payAmount'];
            $feeCol->save();
            // create accounting entry if fee sector exists
            $isFeeSector = Sector::where('name','Fees')->where('type','Income')->first();
            if ($isFeeSector && $data['payAmount'] > 0) {
                Account::create([
                    'sectors_id' => $isFeeSector->id,
                    'amount' => $data['payAmount'],
                    'date' => Carbon::today()->format('Y-m-d'),
                    'description' => 'Student fee payment'
                ]);
            }
            DB::commit();
            $notification = ['title'=>'Payment','body'=>'Payment recorded successfully.'];
            // After payment, return to the student's fee report page
            // After recording a payment, return to the fee collection overview (Fees section)
            return redirect()->route('fees.collection.index')
                ->with('success',$notification);
        } catch (\Exception $e) {
            DB::rollback();
            $notification = ['title'=>'Payment','body'=>'Failed to record payment.'];
            return redirect()->back()->with('error',$notification);
        }
    }


}
