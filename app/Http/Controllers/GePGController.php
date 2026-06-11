<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\GePGBill;
use App\GePGPaymentReceipt;
use App\Fees;
use App\Student;
use App\FeeCollection;
use Validator;
use Redirect;

class GePGController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Student: list fees & pay
    public function studentFees()
    {
        $student = Student::where('idNo', auth()->user()->login)->first();
        $bills = $student ? GePGBill::where('student_id', $student->id)->orderBy('created_at', 'desc')->get() : [];
        $fees = Fees::all();
        return view('gepg.student', compact('student', 'bills', 'fees'));
    }

    // Generate a 12-digit control number for a fee
    public function generateBill(Request $request)
    {
        $v = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'fee_id' => 'required|exists:fees,id',
            'amount' => 'required|numeric|min:1',
        ]);
        if ($v->fails()) return redirect()->back()->withErrors($v);

        $fee = Fees::findOrFail($request->fee_id);
        $controlNo = $this->generateControlNumber();

        $bill = GePGBill::create([
            'student_id' => $request->student_id,
            'control_number' => $controlNo,
            'amount' => $request->amount,
            'bill_description' => $fee->title,
            'status' => 'Issued',
            'expires_at' => now()->addDays(30),
        ]);
        return redirect()->back()->with('success', ['title'=>'Bill Generated', 'body'=>"Control No: $controlNo | Amount: ".number_format($request->amount, 2).' TZS']);
    }

    // Accountant: list all bills
    public function accountantBills()
    {
        $bills = GePGBill::with('student')->orderBy('created_at', 'desc')->get();
        return view('gepg.accountant', compact('bills'));
    }

    // Accountant: mark as paid manually
    public function markPaid(Request $request, $id)
    {
        $bill = GePGBill::findOrFail($id);
        $bill->update(['status' => 'Paid']);
        // Also create a receipt entry
        GePGPaymentReceipt::create([
            'control_number' => $bill->control_number,
            'transaction_id' => 'MANUAL-' . strtoupper(str_random(12)),
            'amount_paid' => $bill->amount,
            'payment_provider' => 'Manual',
            'paid_at' => now(),
        ]);
        return redirect()->back()->with('success', ['title'=>'Paid', 'body'=>'Bill marked as paid.']);
    }

    // Accountant: edit bill details
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

    // Webhook: GePG payment callback
    public function callback(Request $request)
    {
        $controlNo = $request->input('ControlNo');
        $bill = GePGBill::where('control_number', $controlNo)->first();
        if (!$bill) return response('<GepgResponse>FAILED</GepgResponse>', 200)->header('Content-Type', 'text/xml');

        GePGPaymentReceipt::updateOrCreate(
            ['transaction_id' => $request->input('TrxId')],
            [
                'control_number' => $controlNo,
                'amount_paid' => $request->input('PaidAmount', 0),
                'payment_provider' => $request->input('PaymentProvider', 'GePG'),
                'payer_mobile' => $request->input('PayerMobile'),
                'paid_at' => now(),
            ]
        );
        $bill->update(['status' => 'Paid']);
        return response('<GepgResponse>SUCCESS</GepgResponse>', 200)->header('Content-Type', 'text/xml');
    }

    private function generateControlNumber()
    {
        do {
            $no = str_pad(mt_rand(0, 999999999999), 12, '0', STR_PAD_LEFT);
        } while (GePGBill::where('control_number', $no)->exists());
        return $no;
    }
}
