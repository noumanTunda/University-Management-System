<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ChartOfAccount;
use App\JournalEntry;
use App\JournalEntryItem;
use App\FeeInvoice;
use App\InvoiceItem;
use App\PaymentAllocation;
use App\Student;
use App\Fee;
use App\GePGBill;
use App\FeeCollection;
use Validator;
use DB;
use Carbon\Carbon;

class AccountingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ─── Chart of Accounts ───

    public function coaIndex()
    {
        $accounts = ChartOfAccount::orderBy('code')->get();
        $groups = ['Asset', 'Liability', 'Income', 'Expense'];
        return view('accounting.coa', compact('accounts', 'groups'));
    }

    public function coaStore(Request $request)
    {
        $v = Validator::make($request->all(), [
            'code' => 'required|max:20|unique:chart_of_accounts',
            'name' => 'required|max:150',
            'type' => 'required|in:Asset,Liability,Income,Expense',
        ]);
        if ($v->fails()) return redirect()->back()->withErrors($v);
        ChartOfAccount::create($request->all());
        return redirect()->route('accounting.coa')->with('success', ['title'=>'Created','body'=>'Account added.']);
    }

    // ─── Fee Invoicing ───

    public function invoiceIndex()
    {
        $invoices = FeeInvoice::with('student')->orderBy('created_at', 'desc')->paginate(20);
        return view('accounting.invoices', compact('invoices'));
    }

    public function invoiceCreate()
    {
        $students = Student::whereNull('deleted_at')->orderBy('firstName')->get();
        $fees = Fee::all();
        $accounts = ChartOfAccount::where('type', 'Income')->orderBy('code')->get();
        return view('accounting.invoice_create', compact('students', 'fees', 'accounts'));
    }

    public function invoiceStore(Request $request)
    {
        $v = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'due_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|max:255',
            'items.*.amount' => 'required|numeric|min:1',
            'items.*.account_id' => 'required|exists:chart_of_accounts,id',
        ]);
        if ($v->fails()) return redirect()->back()->withErrors($v);

        $student = Student::findOrFail($request->student_id);
        $total = array_sum(array_column($request->items, 'amount'));
        $invNo = 'INV-' . date('Ymd') . '-' . str_pad(FeeInvoice::count() + 1, 4, '0', STR_PAD_LEFT);

        DB::transaction(function() use ($request, $student, $total, $invNo) {
            $invoice = FeeInvoice::create([
                'student_id' => $student->id,
                'invoice_no' => $invNo,
                'invoice_date' => Carbon::now(),
                'due_date' => $request->due_date,
                'total_amount' => $total,
                'status' => 'Pending',
            ]);

            $incomeAccount = ChartOfAccount::where('code', '1002')->first(); // Student Receivables

            foreach ($request->items as $item) {
                $invoice->items()->create($item);
            }

            // Journal Entry: DR Student Receivables, CR Income Accounts
            $journal = JournalEntry::create([
                'entry_date' => Carbon::now(),
                'description' => 'Invoice ' . $invNo . ' for ' . $student->idNo,
                'reference_type' => 'invoice',
                'reference_id' => $invoice->id,
            ]);

            // DR: Student Receivables
            $journal->items()->create(['account_id' => $incomeAccount->id, 'debit' => $total, 'credit' => 0]);

            // CR: Each income account
            foreach ($request->items as $item) {
                $journal->items()->create(['account_id' => $item['account_id'], 'debit' => 0, 'credit' => $item['amount']]);
            }
        });

        return redirect()->route('accounting.invoices')->with('success', ['title'=>'Created','body'=>'Invoice ' . $invNo . ' created.']);
    }

    // ─── Journal Entries ───

    public function journalIndex()
    {
        $entries = JournalEntry::with('items.account')->orderBy('entry_date', 'desc')->paginate(20);
        return view('accounting.journal', compact('entries'));
    }

    // ─── Reports ───

    public function trialBalance()
    {
        $accounts = ChartOfAccount::orderBy('code')->get();
        $totalDebit = $accounts->sum(function($a) { return $a->balance > 0 ? $a->balance : 0; });
        $totalCredit = $accounts->sum(function($a) { return $a->balance < 0 ? abs($a->balance) : 0; });
        return view('accounting.trial_balance', compact('accounts', 'totalDebit', 'totalCredit'));
    }
}
