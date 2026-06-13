<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Student;
use App\Registration;
use App\AssessmentPlan;
use App\AssessmentMark;
use App\Attendance;
use App\GePGBill;
use App\BorrowBook;
use App\CourseRegistration;
use Auth;

class StudentDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('student');
    }

    public function index()
    {
        $student = Student::where('idNo', Auth::user()->login)->first();
        if (!$student) {
            return view('student_portal.dashboard')->with('error', 'No student profile linked.');
        }

        $registrations = Registration::where('students_id', $student->id)->with('department')->get();
        $bills = GePGBill::where('student_id', $student->id)->orderBy('created_at', 'desc')->limit(10)->get();
        $borrowedBooks = BorrowBook::where('students_id', $student->id)->where('Status', 'Borrowed')->count();
        $attendances = Attendance::where('students_id', $student->id)->count();
        $courseRegs = CourseRegistration::where('student_id', $student->id)
            ->with('subject', 'semester.academicYear')->get();
        $grouped = [];
        foreach ($courseRegs as $cr) {
            $year = $cr->semester->academicYear->name ?? 'Unknown';
            $sem = $cr->semester->semester_number ?? '0';
            $grouped[$year . '|' . $sem][] = $cr;
        }

        return view('student_portal.dashboard', compact('student', 'registrations', 'bills', 'borrowedBooks', 'attendances', 'courseRegs', 'grouped'));
    }

    public function assessments()
    {
        $student = Student::where('idNo', Auth::user()->login)->first();
        $courseRegs = CourseRegistration::where('student_id', $student->id)
            ->with('subject', 'semester.academicYear')->get();
        $grouped = [];
        foreach ($courseRegs as $cr) {
            $year = $cr->semester->academicYear->name ?? 'Unknown';
            $sem = $cr->semester->semester_number ?? '0';
            $grouped[$year . '|' . $sem][] = $cr;
        }
        return view('student_portal.assessments', compact('grouped'));
    }

    public function attendance()
    {
        $student = Student::where('idNo', Auth::user()->login)->first();
        $records = Attendance::where('students_id', $student->id)->with('subject')->orderBy('date', 'desc')->get();
        return view('student_portal.attendance', compact('records'));
    }
}
