<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\GePGBill;
use App\GePGPaymentReceipt;
use App\Fee;
use App\Student;
use App\AcademicYear;
use App\Course;
use App\FeeCollection;
use DB;
use Carbon\Carbon;
use Validator;
use Redirect;

class GePGController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ─── Student: view bills & request missing ───
    public function studentFees()
    {
        $student = Student::where('idNo', auth()->user()->login)->first();
        $bills = $student ? GePGBill::where('student_id', $student->id)->orderBy('created_at', 'desc')->get() : [];
        return view('gepg.student', compact('student', 'bills'));
    }

    // Student: request a missing control number
    public function requestControl(Request $request)
    {
        $student = Student::where('idNo', auth()->user()->login)->first();
        if (!$student) return redirect()->back()->with('error', ['title'=>'Error', 'body'=>'Student profile not found.']);

        $v = Validator::make($request->all(), [
            'description' => 'required|max:255',
            'amount' => 'required|numeric|min:1',
        ]);
        if ($v->fails()) return redirect()->back()->withErrors($v);

        GePGBill::create([
            'student_id' => $student->id,
            'control_number' => 'REQUESTED',
            'amount' => $request->amount,
            'bill_description' => $request->description,
            'status' => 'Pending',
        ]);
        return redirect()->back()->with('success', ['title'=>'Request Sent', 'body'=>'Your payment request has been submitted. Accountant will issue a control number.']);
    }

    // Student: pay a bill (simulate GePG)
    public function payForm($billId)
    {
        $student = Student::where('idNo', auth()->user()->login)->first();
        $bill = GePGBill::where('id', $billId)->where('student_id', $student->id)->firstOrFail();
        $dueAmount = $bill->amount - $bill->paid_amount;
        return view('gepg.pay', compact('bill', 'dueAmount'));
    }

    public function payStore(Request $request, $billId)
    {
        $student = Student::where('idNo', auth()->user()->login)->first();
        $bill = GePGBill::where('id', $billId)->where('student_id', $student->id)->firstOrFail();

        $v = Validator::make($request->all(), [
            'amount' => "required|numeric|min:1|max:{$bill->amount}",
        ]);
        if ($v->fails()) return redirect()->back()->withErrors($v);

        $payAmount = (float) $request->amount;
        $newPaid = $bill->paid_amount + $payAmount;
        $dueAmount = $bill->amount - $newPaid;

        DB::transaction(function() use ($bill, $payAmount, $newPaid, $dueAmount, $request) {
            // Determine new status
            if ($dueAmount <= 0) {
                $status = 'Paid';
            } elseif ($newPaid > 0) {
                $status = 'Partial';
            } else {
                $status = 'Issued';
            }

            $bill->update([
                'paid_amount' => $newPaid,
                'status' => $status,
            ]);

            // Create receipt
            GePGPaymentReceipt::create([
                'control_number' => $bill->control_number,
                'transaction_id' => 'TXN-' . strtoupper(str_random(12)),
                'amount_paid' => $payAmount,
                'payment_provider' => 'Simulated GePG',
                'payer_mobile' => $request->input('payer_mobile', ''),
                'paid_at' => Carbon::now(),
            ]);
        });

        $msg = "Payment of TZS " . number_format($payAmount, 2) . " received.";
        if ($dueAmount > 0) $msg .= " Remaining balance: TZS " . number_format($dueAmount, 2);
        return redirect()->route('gepg.student')->with('success', ['title'=>'Payment Successful', 'body'=>$msg]);
    }

    // ─── Accountant: allocate fees & generate control numbers ───

    // Show allocation form
    public function allocationForm()
    {
        $courses = Course::with('department')->orderBy('name')->get();
        $fees = Fee::all();
        $years = AcademicYear::orderBy('name', 'desc')->get();
        $students = [];
        return view('gepg.allocation', compact('courses', 'fees', 'years', 'students'));
    }

    // Allocate fee to students — generates control numbers for each fee × student
    public function allocateBulk(Request $request)
    {
        $v = Validator::make($request->all(), [
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'fees' => 'required|array',
            'fees.*.id' => 'exists:fees,id',
            'fees.*.amount' => 'required|numeric',
        ]);
        if ($v->fails()) return redirect()->back()->withErrors($v);

        $academicYearId = $request->input('academic_year_id');
        $yearName = '';
        if ($academicYearId) {
            $year = AcademicYear::find($academicYearId);
            $yearName = $year ? $year->name : '';
        }

        $count = 0;
        $skipped = 0;

        foreach ($request->student_ids as $studentId) {
            foreach ($request->fees as $fee) {
                // Check duplicate: same student + fee title + academic year
                $exists = GePGBill::where('student_id', $studentId)
                    ->where('bill_description', $fee['title'] ?? Fee::find($fee['id'])->title)
                    ->where('academic_year', $yearName)
                    ->exists();
                if ($exists) { $skipped++; continue; }

                $controlNo = $this->generateControlNumber();
                GePGBill::create([
                    'student_id' => $studentId,
                    'control_number' => $controlNo,
                    'amount' => $fee['amount'],
                    'paid_amount' => 0,
                    'bill_description' => $fee['title'] ?? Fee::find($fee['id'])->title,
                    'status' => 'Issued',
                    'expires_at' => Carbon::now()->addDays(30),
                    'academic_year' => $yearName,
                ]);
                $count++;
            }
        }

        $msg = "$count control numbers generated.";
        if ($skipped > 0) $msg .= " $skipped skipped (already allocated this year).";
        return redirect()->route('gepg.accountant')->with('success', ['title'=>'Allocated', 'body'=>$msg]);
    }

    // AJAX: get students by course AND academic year (only registered students)
    public function getStudentsByCourse($courseId, $academicYearId = null)
    {
        $query = Student::whereNull('students.deleted_at')
            ->select('students.id', 'students.idNo', 'students.firstName', 'students.lastName');
        if ($courseId && $courseId != 'all') {
            $query->where('students.course_id', $courseId);
        }

        if ($academicYearId && $academicYearId != 'all') {
            $year = AcademicYear::find($academicYearId);
            if ($year) {
                $session = $year->name;
                $query->whereHas('registered', function($q) use ($session) {
                    $q->where('session', $session);
                });
            }
        }

        $students = $query->orderBy('students.firstName')->get();
        return response()->json(['success' => true, 'students' => $students]);
    }

    // AJAX: get fees by course's department
    public function getFeesByDepartment($courseId)
    {
        $deptId = 0;
        if ($courseId && $courseId != 'all') {
            $course = Course::find($courseId);
            $deptId = $course ? $course->department_id : 0;
        }
        if ($deptId) {
            $fees = Fee::with('department')->where('department_id', $deptId)->orWhereNull('department_id')->get(['id', 'title', 'amount', 'department_id']);
        } else {
            $fees = Fee::with('department')->get(['id', 'title', 'amount', 'department_id']);
        }
        $fees = $fees->map(function($f) {
            return ['id' => $f->id, 'title' => $f->title, 'amount' => $f->amount, 'department' => $f->department->name ?? 'General'];
        });
        return response()->json(['success' => true, 'fees' => $fees]);
    }

    // Allocate fee to students (bulk) — generates control numbers
    public function penaltiesForm()
    {
        return view('gepg.penalties');
    }

    public function allocateSpecific(Request $request)
    {
        $v = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'description' => 'required|max:255',
            'amount' => 'required|numeric|min:1',
        ]);
        if ($v->fails()) return redirect()->back()->withErrors($v);

        $controlNo = $this->generateControlNumber();
        GePGBill::create([
            'student_id' => $request->student_id,
            'control_number' => $controlNo,
            'amount' => $request->amount,
            'bill_description' => $request->description,
            'status' => 'Issued',
            'expires_at' => Carbon::now()->addDays(30),
        ]);
        return redirect()->route('gepg.accountant')->with('success', ['title'=>'Created', 'body'=>'Special fee bill created.']);
    }

    // ─── Accountant: list, edit, mark paid ───

    public function accountantBills(Request $request)
    {
        $years = AcademicYear::orderBy('name', 'desc')->get();
        $selectedYear = $request->input('academic_year', '');

        $query = GePGBill::with('student');
        if ($selectedYear) {
            $query->where('academic_year', $selectedYear);
        }
        $bills = $query->orderBy('created_at', 'desc')->get();

        return view('gepg.accountant', compact('bills', 'years', 'selectedYear'));
    }

    public function markPaid(Request $request, $id)
    {
        $bill = GePGBill::with('student')->findOrFail($id);
        DB::transaction(function() use ($bill) {
            $bill->update(['status' => 'Paid']);
            $trxId = 'MANUAL-' . strtoupper(str_random(12));
            GePGPaymentReceipt::create([
                'control_number' => $bill->control_number,
                'transaction_id' => $trxId,
                'amount_paid' => $bill->amount,
                'payment_provider' => 'Manual',
                'paid_at' => Carbon::now(),
            ]);
            if ($bill->student) {
                FeeCollection::create([
                    'students_id' => $bill->student_id,
                    'payableAmount' => $bill->amount,
                    'lateFee' => 0,
                    'paidAmount' => $bill->amount,
                    'payDate' => Carbon::now()->format('Y-m-d'),
                ]);
            }
        });
        return redirect()->back()->with('success', ['title'=>'Paid', 'body'=>'Bill marked as paid.']);
    }

    public function editBill($id)
    {
        $bill = GePGBill::with('student')->findOrFail($id);
        return view('gepg.edit', compact('bill'));
    }

    public function updateBill(Request $request, $id)
    {
        $bill = GePGBill::findOrFail($id);
        $bill->update($request->only(['amount', 'bill_description', 'status', 'control_number']));
        return redirect()->route('gepg.accountant')->with('success', ['title'=>'Updated', 'body'=>'Bill updated.']);
    }

    // ─── Webhook ───
    public function callback(Request $request)
    {
        $controlNo = $request->input('ControlNo');
        $bill = GePGBill::where('control_number', $controlNo)->first();
        if (!$bill) return response('<GepgResponse>FAILED</GepgResponse>', 200)->header('Content-Type', 'text/xml');

        DB::transaction(function() use ($bill, $request) {
            $bill->update(['status' => 'Paid']);
            GePGPaymentReceipt::updateOrCreate(
                ['transaction_id' => $request->input('TrxId')],
                [
                    'control_number' => $controlNo,
                    'amount_paid' => $request->input('PaidAmount', 0),
                    'payment_provider' => $request->input('PaymentProvider', 'GePG'),
                    'payer_mobile' => $request->input('PayerMobile'),
                    'paid_at' => Carbon::now(),
                ]
            );
        });
        return response('<GepgResponse>SUCCESS</GepgResponse>', 200)->header('Content-Type', 'text/xml');
    }


    public function getAllStudents()
    {
        $students = Student::whereNull('deleted_at')
            ->select('id', 'idNo', 'firstName', 'lastName')
            ->orderBy('firstName')
            ->get();
        return response()->json(['success' => true, 'students' => $students]);
    }
    private function generateControlNumber()
    {
        do {
            $no = str_pad(mt_rand(0, 999999999999), 12, '0', STR_PAD_LEFT);
        } while (GePGBill::where('control_number', $no)->exists());
        return $no;
    }
}
