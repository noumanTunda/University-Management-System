<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;
use Auth;
use App\User;
use App\Student;
use Mail;
use Session;
use App\Institute;
use Validator;
use Hash;
use App\Http\Helpers\AppHelper;

class UserController extends Controller {

	public function __construct()
	{
		$this->middleware('admin', ['except' => ['login', 'logout','settings','postSettings','forgotPasswordForm','sendNewPassword']]);
	}
	/**
	* Make Login
	*
	* @return Response
	*/
	public function login()
	{

					$login = Input::get('login');
			$password = Input::get('password');
			if (Auth::attempt(array('login'=>$login, 'password'=>$password))) {
			$name=Auth::user()->firstname.' '.Auth::user()->lastname;
			Session::put('name', $name);
			Session::put('user_session_sha1', AppHelper::getUserSessionHash());

			$institute=Institute::select('name')->first();
			if(!$institute)
			{
				if (Auth::user()->group != "Admin")
				{
					return Redirect::to('/')->with('warning','Institute Information not setup yet! Please contact administrator.');
				}
				else {
					$institute=new Institute;
					$institute->name="TundasLab";
					Session::put('inName', $institute->name);
					$notification= array('title' => 'Information Missing', 'body' => 'Please provide institute information.');
					return Redirect::to('/institute')->with('warning',$notification);

				}
			}
			else {
				Session::put('inName', $institute->name);
				Session::put('inNameShort', AppHelper::getShortName($institute->name));
				$notification= array('title' => 'Login', 'body' => 'You are now logged in.');
					// Redirect students to their portal
					if (Auth::user()->group == 'Student') {
					    return Redirect::to('/student/dashboard')->with('success',$notification);
					}
				return Redirect::to('/dashboard')->with('success',$notification);
			}

		} else {
			return Redirect::to('/')->with('error', 'Your username/password combination was incorrect');

		}



	}

	public function logout()
	{
		Auth::logout();
		return Redirect::to('/')->with('success', 'Your are now logged out!');
	}

	/**
	* Show all user.
	*
	* @return Response
	*/
	public function index()
	{
		$users = User::all();
		return view('user.index',compact('users'));
	}
	/**
	* Show the form for creating a new resource.
	*
	* @return Response
	*/
	public function create()
	{
		return view('user.create');
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
			'firstname' => 'required|max:255',
			'lastname' => 'required|max:255',
			'group' => 'required',
			'login' => 'required|unique:users',
			'emal' => 'email',
			'password' => 'required|confirmed|min:6'
		];
		$message=[
			'unique' => 'User name already exits!'
		];
		$validator = Validator::make($data, $rules,$message);
		if ($validator->fails())
		{
			return Redirect::route('user.create')->withErrors($validator);
		}
		else {
			$user= new User;
			$user->create($data);
			$notification= array('title' => 'Data Store', 'body' => 'User Created Succesfully.');
			return Redirect::route('user.create')->with("success",$notification);
		}
	}

	/**
	 * Show the form for editing the specified user.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$user = User::findOrFail($id);
		return view('user.edit', compact('user'));
	}

	/**
	 * Update the specified user in storage.
	 *
	 * @param  Request  $request
	 * @param  int  $id
	 * @return Response
	 */
	public function update(Request $request, $id)
	{
		$data = $request->all();
		$rules = [
			'firstname' => 'required|max:255',
			'lastname' => 'required|max:255',
			'group' => 'required',
			'email' => 'email',
		];
		$validator = Validator::make($data, $rules);
		if ($validator->fails()) {
			return Redirect::route('user.edit', [$id])->withErrors($validator);
		}
		$user = User::findOrFail($id);
		$user->fill($data);
		if (!empty($data['password'])) {
			$user->password = $data['password'];
		}
		$user->save();
		$notification = ['title' => 'Data Update', 'body' => 'User Updated Succesfully.'];
		return Redirect::route('user.index')->with('success', $notification);
	}

	/**
	* Remove the specified resource from storage.
	*
	* @param  int  $id
	* @return Response
	*/
	public function destroy($id)
	{
		$user = User::findOrFail($id);
		$user->delete();
		$notification= array('title' => 'Data Delete', 'body' => 'User Deleted Succesfully.');
		return Redirect::route('user.index')->with("success",$notification);
	}

	/**
	* Change the specified user informations.
	*
	*@return Response
	*/
	public function settings()
	{
		$user = auth()->user();
		return view('user.settings',compact('user'));
	}

	public function postSettings(Request $request)
	{

		if ($request->exists('for'))
		{
			$data = $request->except(['userName','group']);
			if($request->input('for')=="info")
			{
				$rules=[
					'firstname' => 'required',
					'lastname' => 'required',
					'email' => 'email',

				];
			}
			else {
				if(!Hash::check($request->input('oldpassword'), auth()->user()->password)){
					$notification= array('title' => 'Validation Error', 'body' => 'Old Password did not match!!!');
					return Redirect::back()->with('error',$notification);
				}
				$rules=[
					'oldpassword' => 'required|min:6',
					'password' => 'required|confirmed|min:6'
				];
			}
			$validator = Validator::make($data, $rules);
			if ($validator->fails())
			{
				return Redirect::back()->withErrors($validator);
			}


			$user = User::findOrFail(auth()->user()->id);
			$user->fill($data)->save();
			$notification= array('title' => 'Data Change', 'body' => 'Information Updated Successfully');
			return Redirect::back()->with('success',$notification);
		}
		return Redirect::back()->with('error','Invalid request!!!');


	}

    public function missingStudents()
    {
        $students = Student::whereNull('deleted_at')->get();
        $missing = [];
        foreach ($students as $s) {
            $exists = User::where('login', $s->idNo)->exists();
            if (!$exists) {
                $missing[] = $s;
            }
        }
        return view('user.missing_students', compact('missing'));
    }

    public function createMissingAccounts(Request $request)
    {
        $ids = $request->input('student_ids', []);
        if (empty($ids)) {
            return redirect()->route('user.missing.students')->with('error', [
                'title' => 'No Selection',
                'body' => 'Please select at least one student.'
            ]);
        }

        $created = 0;
        $skipped = 0;
        foreach ($ids as $studentId) {
            $student = Student::find($studentId);
            if (!$student) continue;

            $exists = User::where('login', $student->idNo)->exists();
            if ($exists) {
                $skipped++;
                continue;
            }

            User::create([
                'login'     => $student->idNo,
                'password'  => $student->lastName,
                'firstname' => $student->firstName,
                'lastname'  => $student->lastName,
                'group'     => 'Student',
                'email'     => $student->email ?: $student->idNo . '@student.osums.edu',
            ]);
            $created++;

            // Send email
            try {
                $data = [
                    'name'     => $student->firstName . ' ' . $student->lastName,
                    'login'    => $student->idNo,
                    'password' => $student->lastName,
                ];
                $emailTo = $student->email ?: $student->idNo . '@student.osums.edu';
                Mail::send('emails.account_created', $data, function($message) use ($emailTo, $student) {
                    $message->to($emailTo, $student->firstName)
                            ->subject('Your OSUMS Account Has Been Created');
                });
            } catch (\Exception $e) {
                // Email sending failed - silently continue
            }
        }

        $msg = "Created {$created} new account(s) successfully.";
        if ($skipped > 0) {
            $msg .= " {$skipped} skipped (already exist).";
        }

        return redirect()->route('user.index')->with('success', [
            'title' => 'Accounts Created',
            'body'  => $msg
        ]);
    }


    public function forgotPasswordForm()
    {
        return view('home_forgot_password');
    }

    public function sendNewPassword(Request $request)
    {
        $email = trim($request->input('email', ''));
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->route('user.forgot.password')
                ->with('error', ['title' => 'Invalid Email', 'body' => 'Please enter a valid email address.']);
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            return redirect()->route('user.forgot.password')
                ->with('error', ['title' => 'Email Not Found', 'body' => 'No account found with that email address.']);
        }

        $newPassword = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%'), 0, 12);
        $user->password = $newPassword;
        $user->save();

        try {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $loginUrl = "$protocol://$host/login";
            $fullName = trim($user->firstname . ' ' . $user->lastname);
            $data = [
                'name' => $fullName,
                'loginUrl' => $loginUrl,
                'username' => $user->login,
                'password' => $newPassword,
            ];
            Mail::send('emails.password_reset', $data, function($message) use ($email, $fullName) {
                $message->to($email, $fullName)
                        ->subject('OSUMS — Password Reset');
            });
        } catch (\Exception $e) {}

        return redirect()->route('home')
            ->with('success', 'Your password has been reset successfully! Check your inbox for new login credentials.');
    }
}