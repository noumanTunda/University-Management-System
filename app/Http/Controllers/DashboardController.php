<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Session;
use App\Exam;
use App\Department;
use App\Attendance;
use App\Registration;
use App\Student;
use App\Subject;
use App\Account;

class DashboardController extends Controller {

	/**
	* Display a listing of the resource.
	*
	* @return Response
	*/
	public function index()
	{
		$error = Session::get('error');
		$success=Session::get('success');
		// Use collection count to avoid Builder::count() issue on newer PHP versions
		// Use DB query to avoid SoftDeletes scope triggering count warning
		$totalAdmit = \DB::table('students')->count();
		// Count distinct registered students
		$totalRegisterd = \DB::table('registrations')->distinct('students_id')->count('students_id');
		$totalDepartment = \DB::table('department')->count();
		$totalSubject = \DB::table('subject')->count();
		// Count distinct attendance dates
		$totalAttendance = \DB::table('attendances')->distinct('date')->count('date');
		// Count total exams (distinct combinations not directly supported, using total rows as approximation)
		$totalExam = \DB::table('exams')->count();
		$total = [
			'admitted' => $totalAdmit,
			'registered' => $totalRegisterd,
			'department' => $totalDepartment,
			'subject' => $totalSubject,
			'attendance' => $totalAttendance,
			'exam' => $totalExam,
		];
		//graph data
		$monthlyIncome = Account::selectRaw('month(date) as month, sum(amount) as amount')
		    ->with(['sector' => function ($query) {
		        $query->where('type', 'Income');
		    }])
		    // Correct whereHas signature: relation, callback, operator, count
		    ->whereHas('sector', function ($query) {
		        $query->where('type', 'Income');
		    }, '>=', 1)
		    ->groupBy('month')
		    ->get();

		$monthlyExpences = Account::selectRaw('month(date) as month, sum(amount) as amount')
		    ->with(['sector' => function ($query) {
		        $query->where('type', 'Expence');
		    }])
		    ->whereHas('sector', function ($query) {
		        $query->where('type', 'Expence');
		    }, '>=', 1)
		    ->groupBy('month')
		    ->get();

		// Attendance data for teacher/dashboard charts (monthly attendance count)
		$monthlyAttendance = Attendance::selectRaw('month(date) as month, count(*) as cnt')
		    ->groupBy('month')
		    ->get();

		$incomeTotal = Account::with(['sector' => function ($query) {
		        $query->where('type', 'Income');
		    }])
		    ->whereHas('sector', function ($query) {
		        $query->where('type', 'Income');
		    }, '>=', 1)
		    ->sum('amount');

		$expenceTotal = Account::with(['sector' => function ($query) {
		        $query->where('type', 'Expence');
		    }])
		    ->whereHas('sector', function ($query) {
		        $query->where('type', 'Expence');
		    }, '>=', 1)
		    ->sum('amount');
		$incomes=$this->datahelper($monthlyIncome);
		$expences=$this->datahelper($monthlyExpences);
		$balance = $incomeTotal - $expenceTotal;
		$attendance = $this->datahelper($monthlyAttendance);
		return view('dashboard',compact('error','success','total','incomes','expences','balance','attendance'));
	}
	function datahelper($data)
	{
		$DataKey = [];
		$DataVlaue =[];
		foreach ($data as $d) {
			array_push($DataKey,date("F", mktime(0, 0, 0, $d->month, 10)));
			array_push($DataVlaue,$d->amount);

		}
		return ["key"=>$DataKey,"value"=>$DataVlaue];

	}

}
