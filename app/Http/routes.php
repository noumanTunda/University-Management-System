<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', array('as' => 'home', 'uses' => 'HomeController@index'));
Route::get('/login', array('as' => 'home', 'uses' => 'HomeController@index'));
Route::get('/lock',array('as' => 'lock', 'uses' => 'HomeController@lock'));
Route::get('/',"HomeController@index");

Route::post('/user/login',[ 'as' => 'user.login','uses'=>'UserController@login','middleware' => 'throttle:10,1']);
Route::get('/user/logout',[ 'as' => 'user.logout','uses'=>'UserController@logout']);

Route::group(['middleware' => 'auth'], function()
{
  Route::get('/dashboard',[ 'as' => 'user.dashboard','uses'=>'DashboardController@index']);
  // Student Portal
  Route::get('/student/dashboard',[ 'as' => 'student.dashboard','uses'=>'StudentDashboardController@index']);
  Route::get('/student/assessments',[ 'as' => 'student.assessments','uses'=>'StudentDashboardController@assessments']);
  Route::get('/student/attendance',[ 'as' => 'student.attendance','uses'=>'StudentDashboardController@attendance']);
  Route::get('/institute',[ 'as' => 'institute.index','uses'=>'InstituteController@index']);
  Route::post('/institute',[ 'as' => 'institute','uses'=>'InstituteController@save']);

  Route::resource('user','UserController');
  Route::get('users/missing-students',[ 'as' => 'user.missing.students', 'uses'=>'UserController@missingStudents']);
  Route::post('users/create-missing-accounts',[ 'as' => 'user.create.missing', 'uses'=>'UserController@createMissingAccounts']);
  Route::get('/settings',[ 'as' => 'user.settings','uses'=>'UserController@settings']);
  Route::get('academic-years',[ 'as' => 'academic.year.index','uses'=>'AcademicYearController@index']);
  Route::get('academic-years/create',[ 'as' => 'academic.year.create','uses'=>'AcademicYearController@create']);
  Route::post('academic-years',[ 'as' => 'academic.year.store','uses'=>'AcademicYearController@store']);
  Route::get('academic-years/{id}/edit',[ 'as' => 'academic.year.edit','uses'=>'AcademicYearController@edit']);
  Route::put('academic-years/{id}',[ 'as' => 'academic.year.update','uses'=>'AcademicYearController@update']);
  Route::get('academic-years/{id}/delete',[ 'as' => 'academic.year.destroy','uses'=>'AcademicYearController@destroy']);
 Route::post('/settings',[ 'as' => 'user.settings','uses'=>'UserController@postSettings']);

  Route::resource('department','DepartmentController');

  // Bulk subject upload routes (must be defined before the subject resource route to avoid conflict with /subject/{id})
  Route::get('subjects/upload',[ 'as' => 'subject.upload.form','uses'=>'SubjectController@uploadForm']);
  Route::post('subjects/upload',[ 'as' => 'subject.upload.store','uses'=>'SubjectController@uploadStore']);
  Route::get('subjects/template',[ 'as' => 'subject.upload.template','uses'=>'SubjectController@downloadTemplate']);

  Route::resource('subject','SubjectController');
  Route::get('subject/{deparment}/{semester}',[ 'as' => 'subject.DeptAndSem','uses'=>'SubjectController@subjetsByDptSem']);

  Route::get('teacher-subject',[ 'as' => 'teacher.subject.index','uses'=>'TeacherSubjectController@index']);
  Route::post('teacher-subject',[ 'as' => 'teacher.subject.store','uses'=>'TeacherSubjectController@store']);
  Route::get('teacher-subject/subjects/{deptId}',[ 'as' => 'teacher.subject.subjects','uses'=>'TeacherSubjectController@getSubjectsByDepartment']);
  Route::get('teacher-subject/{id}/edit',[ 'as' => 'teacher.subject.edit','uses'=>'TeacherSubjectController@edit']);
  Route::put('teacher-subject/{id}',[ 'as' => 'teacher.subject.update','uses'=>'TeacherSubjectController@update']);
  Route::delete('teacher-subject/{id}',[ 'as' => 'teacher.subject.destroy','uses'=>'TeacherSubjectController@destroy']);
  Route::get('teacher-subject/{userId}/{subjectId}/{yearId}/delete',[ 'as' => 'teacher.subject.delete','uses'=>'TeacherSubjectController@deleteAssignment']);

  Route::resource('course','CourseController');


  // Bulk student upload routes (use a distinct prefix to avoid conflict with the resource routes)
  Route::get('students/upload',[ 'as' => 'student.upload.form','uses'=>'studentController@uploadForm']);
  Route::post('students/upload',[ 'as' => 'student.upload.store','uses'=>'studentController@uploadStore']);
  Route::get('students/template',[ 'as' => 'student.upload.template','uses'=>'studentController@downloadTemplate']);

  Route::resource('student','studentController');
  Route::post('student/departmment',[ 'as' => 'student.department','uses'=>'studentController@index2']);
  Route::get('students/{dID}/{session}',[ 'as' => 'students.departmentAndsession','uses'=>'studentController@studentList']);
  Route::get('students-batch/{dID}/{batch}',[ 'as' => 'students.batch','uses'=>'studentController@studentListByBatch']);
  Route::get('students/{dID}/{session}/{semester}',[ 'as' => 'students.registered','uses'=>'studentController@registeredStudentList']);
  Route::get('student-registration',[ 'as' => 'student.registration.create','uses'=>'studentController@regCreate']);
  Route::post('student-registration',[ 'as' => 'student.registration.store','uses'=>'studentController@regStore']);
  Route::get('student-registration/{id}/delete',[ 'as' => 'student.registration.destroy','uses'=>'studentController@regDestroy']);
  Route::get('registered-students',[ 'as' => 'student.registration.index','uses'=>'studentController@regIndex']);
  Route::post('registered-students',[ 'as' => 'student.registration.list','uses'=>'studentController@regList']);
  // Assign a course to a registered student (POST)
  Route::post('students/{id}/assign-course',[ 'as' => 'student.assignCourse','uses'=>'studentController@assignCourse']);

  Route::resource('attendance','AttendanceController');
  Route::post('attendance/by-subject',[ 'as' => 'attendance.index2','uses'=>'AttendanceController@index2']);

  // Assessment Plans (TCU CA/UE grading)
  Route::get('assessments',[ 'as' => 'assessment.index','uses'=>'AssessmentController@index']);
  Route::get('assessments/create',[ 'as' => 'assessment.create','uses'=>'AssessmentController@create']);
  Route::post('assessments',[ 'as' => 'assessment.store','uses'=>'AssessmentController@store']);
  Route::get('assessments/{id}/components',[ 'as' => 'assessment.components','uses'=>'AssessmentController@components']);
  Route::post('assessments/{id}/components',[ 'as' => 'assessment.component.store','uses'=>'AssessmentController@storeComponent']);

  // Assessment Templates (HOD/Admin only)
  Route::delete('assessments/component/{id}',[ 'as' => 'assessment.component.destroy','uses'=>'AssessmentController@destroyComponent']);
  Route::get('assessments/{id}/compute',[ 'as' => 'assessment.compute','uses'=>'AssessmentController@compute']);
  Route::get('assessments/marks/{componentId}',[ 'as' => 'assessment.marks','uses'=>'AssessmentController@marks']);
  Route::post('assessments/marks/{componentId}',[ 'as' => 'assessment.marks.store','uses'=>'AssessmentController@storeMarks']);

  // Exam Marks Entry (new assessment-based system)
  Route::get('exam-marks',[ 'as' => 'exam.marks.create','uses'=>'ExamMarksController@create']);
  Route::post('exam-marks',[ 'as' => 'exam.marks.store','uses'=>'ExamMarksController@store']);
  Route::get('exam-marks/subjects/{deptId}',[ 'as' => 'exam.marks.subjects','uses'=>'ExamMarksController@getSubjects']);
  Route::get('exam-marks/semesters/{yearId}',[ 'as' => 'exam.marks.semesters','uses'=>'ExamMarksController@getSemesters']);
  Route::get('exam-marks/entry/{subjectId}/{semesterId}',[ 'as' => 'exam.marks.entry','uses'=>'ExamMarksController@getMarkEntry']);
  Route::get('exam-marks/upload',[ 'as' => 'exam.marks.upload.form','uses'=>'ExamMarksController@uploadForm']);
  Route::post('exam-marks/upload',[ 'as' => 'exam.marks.upload.store','uses'=>'ExamMarksController@uploadStore']);
  Route::get('exam-marks/template',[ 'as' => 'exam.marks.template','uses'=>'ExamMarksController@downloadTemplate']);

  // Exam Types
  Route::get('exam-type',[ 'as' => 'exam_type.index','uses'=>'ExamTypeController@index']);
  Route::post('exam-type',[ 'as' => 'exam_type.store','uses'=>'ExamTypeController@store']);
  Route::post('exam-type/{id}',[ 'as' => 'exam_type.update','uses'=>'ExamTypeController@update']);
  Route::get('exam-type/{id}/delete',[ 'as' => 'exam_type.destroy','uses'=>'ExamTypeController@destroy']);

  Route::resource('exam','ExamController');
  Route::post('exam/by-subject',[ 'as' => 'exam.index2','uses'=>'ExamController@index2']);
  Route::get('result-subject',[ 'as' => 'result.subject','uses'=>'ResultController@getSubject']);
  Route::post('result-subject',[ 'as' => 'result.subject.post','uses'=>'ResultController@postSubject']);
  Route::get('result-student',[ 'as' => 'result.individual','uses'=>'ResultController@getStudent']);
  Route::post('result-student',[ 'as' => 'result.individual.post','uses'=>'ResultController@postStudent']);

  //fees collection (custom routes placed before resource to avoid conflicts)
  Route::get('/fees-list/{dId}',[ 'as' => 'fees.list','uses'=>'FeesController@lists']);
  Route::get('/fees-collection/create',[ 'as' => 'fees.collection.create','uses'=>'FeesController@cCreate']);
  Route::post('/fees-collection',[ 'as' => 'fees.collection.store','uses'=>'FeesController@cStore']);
  //Route::post('/fees-collection/{id}',[ 'as' => 'fees.collection.destroy','uses'=>'FeesController@cDestroy']);
  Route::get('/fees-getdue/{stdId}',[ 'as' => 'fees.getdue','uses'=>'FeesController@getDue']);
  // Payment routes for individual fee entries
  // Display payment form for a specific fee (GET)
  Route::get('/fees/pay/{id}', [ 'as' => 'fees.payForm', 'uses' => 'FeesController@payForm' ]);
  // Process payment submission for a specific fee (POST)
  Route::post('/fees/pay/{id}', [ 'as' => 'fees.pay', 'uses' => 'FeesController@pay' ]);

  // Custom routes for adding payments (must be before the resource route)
  Route::get('/fees/add-payment', [ 'as' => 'fees.addPaymentForm', 'uses' => 'FeesController@addPaymentForm' ]);
  Route::post('/fees/add-payment', [ 'as' => 'fees.addPaymentStore', 'uses' => 'FeesController@addPaymentStore' ]);

  // AJAX endpoint to fetch payment info (due amount and fee types) for a student
  Route::get('/fees/payment-info-data/{studentId}', [ 'as' => 'fees.paymentInfoData', 'uses' => 'FeesController@paymentInfoData' ]);

  // Routes for detailed payment information per student
  Route::get('/fees/payment-info/{studentId}', [ 'as' => 'fees.paymentInfoForm', 'uses' => 'FeesController@paymentInfoForm' ]);
  Route::post('/fees/payment-info/{studentId}', [ 'as' => 'fees.paymentInfoStore', 'uses' => 'FeesController@paymentInfoStore' ]);

  // Resource routes for FeesController (standard CRUD)
  Route::resource('fees','FeesController');

  // GePG Payment Routes
  Route::get('gepg/pay',[ 'as' => 'gepg.student','uses'=>'GePGController@studentFees']);
  Route::post('gepg/request',[ 'as' => 'gepg.request','uses'=>'GePGController@requestControl']);
  Route::get('gepg/pay/{id}',[ 'as' => 'gepg.pay.form','uses'=>'GePGController@payForm']);
  Route::post('gepg/pay/{id}',[ 'as' => 'gepg.pay.store','uses'=>'GePGController@payStore']);
  Route::get('gepg/allocate',[ 'as' => 'gepg.allocate.form','uses'=>'GePGController@allocationForm']);
  Route::post('gepg/allocate/bulk',[ 'as' => 'gepg.allocate.bulk','uses'=>'GePGController@allocateBulk']);
  Route::post('gepg/allocate/specific',[ 'as' => 'gepg.allocate.specific','uses'=>'GePGController@allocateSpecific']);
  Route::get('gepg/penalties',[ 'as' => 'gepg.penalties','uses'=>'GePGController@penaltiesForm']);
  Route::get('gepg/students/{courseId}/{academicYearId?}',[ 'as' => 'gepg.students.bycourse','uses'=>'GePGController@getStudentsByCourse']);
  Route::get('gepg/fees-course/{courseId}',[ 'as' => 'gepg.fees.bycourse','uses'=>'GePGController@getFeesByDepartment']);
  Route::get('gepg/allstudents',[ 'as' => 'gepg.students.all','uses'=>'GePGController@getStudentsByCourse']);
  Route::post('gepg/bill',[ 'as' => 'gepg.bill.generate','uses'=>'GePGController@generateBill']);
  Route::get('gepg/bills',[ 'as' => 'gepg.accountant','uses'=>'GePGController@accountantBills']);
  Route::post('gepg/bill/{id}/paid',[ 'as' => 'gepg.bill.paid','uses'=>'GePGController@markPaid']);
  Route::get('gepg/bill/{id}/edit',[ 'as' => 'gepg.bill.edit','uses'=>'GePGController@editBill']);
  Route::post('gepg/bill/{id}',[ 'as' => 'gepg.bill.update','uses'=>'GePGController@updateBill']);
  Route::get('gepg/bill/{id}/delete',[ 'as' => 'gepg.bill.delete','uses'=>'GePGController@deleteBill']);
  Route::post('gepg/callback',[ 'as' => 'gepg.callback','uses'=>'GePGController@callback']);

  //accounting routes
  Route::get('accounting/coa',[ 'as' => 'accounting.coa','uses'=>'AccountingController@coaIndex']);
  Route::post('accounting/coa',[ 'as' => 'accounting.coa.store','uses'=>'AccountingController@coaStore']);
  Route::get('accounting/invoices',[ 'as' => 'accounting.invoices','uses'=>'AccountingController@invoiceIndex']);
  Route::get('accounting/invoices/create',[ 'as' => 'accounting.invoice.create','uses'=>'AccountingController@invoiceCreate']);
  Route::post('accounting/invoices',[ 'as' => 'accounting.invoice.store','uses'=>'AccountingController@invoiceStore']);
  Route::get('accounting/journal',[ 'as' => 'accounting.journal','uses'=>'AccountingController@journalIndex']);
  Route::get('accounting/trial-balance',[ 'as' => 'accounting.trial.balance','uses'=>'AccountingController@trialBalance']);
  //reports
  Route::get('/accounting/reports-by-type',[ 'as' => 'accounting.reports.type','uses'=>'ReportController@reportByType']);
  Route::post('/accounting/reports-by-type',[ 'as' => 'accounting.reports.type','uses'=>'ReportController@reportByType']);
  Route::get('/accounting/reports-balance',[ 'as' => 'accounting.reports.balance','uses'=>'ReportController@reportBalance']);
  Route::post('/accounting/reports-balance',[ 'as' => 'accounting.reports.balance','uses'=>'ReportController@reportBalance']);
  Route::get('/fees-collection/report',[ 'as' => 'fees.collection.report','uses'=>'ReportController@report']);
  Route::post('/fees-collection/report',[ 'as' => 'fees.collection.report','uses'=>'ReportController@report']);
  Route::get('/fees-collection',[ 'as' => 'fees.collection.index','uses'=>'ReportController@cIndex']);
  Route::get('/fees-student/{stdId}',[ 'as' => 'fees.collection.studentfees','uses'=>'ReportController@studentFees']);
  // New page to view all payments for a specific student (installment view)
  Route::get('/fees/student/{stdId}/payments', [ 'as' => 'fees.student.payments', 'uses' => 'FeesController@studentPayments' ]);
  // New routes to add a payment (installment) for any student

  //library routes
  Route::get('/library/addbook','libraryController@getAddbook');
  Route::post('/library/addbook','libraryController@postAddbook');
  Route::get('/library/view','libraryController@getviewbook');

  Route::get('/library/view-show','libraryController@postviewbook');

  Route::get('/library/edit/{id}','libraryController@getBook');
  Route::post('/library/update','libraryController@postUpdateBook');
  Route::get('/library/delete/{id}','libraryController@deleteBook');
  Route::get('/library/issuebook','libraryController@getissueBook');

  //check availabe book
  Route::get('/library/issuebook-availabe/{books_id}/{quantity}','libraryController@checkBookAvailability');
  Route::post('/library/issuebook','libraryController@postissueBook');

  Route::get('/library/issuebookview','libraryController@getissueBookview');
  Route::post('/library/issuebookview','libraryController@postissueBookview');
  Route::get('/library/issuebookupdate/{id}','libraryController@getissueBookupdate');
  Route::post('/library/issuebookupdate','libraryController@postissueBookupdate');
  Route::get('/library/issuebookdelete/{id}','libraryController@deleteissueBook');

  Route::get('/library/search','libraryController@getsearch');
  Route::get('/library/search2','libraryController@getsearch');
  Route::post('/library/search','libraryController@postsearch');
  Route::post('/library/search2','libraryController@postsearch2');

  Route::get('/library/reports','libraryController@getReports');
  Route::get('/library/reports/fine','libraryController@getReportsFine');

  Route::get('/library/reportprint/{do}','libraryController@Reportprint');
  Route::get('/library/reports/fine/{month}','libraryController@ReportsFineprint');

  //Hostel Routes
  Route::get('/dormitory','DormitoryController@index');
  Route::post('/dormitory/create','DormitoryController@create');
  Route::get('/dormitory/edit/{id}','DormitoryController@edit');
  Route::post('/dormitory/update','DormitoryController@update');
  Route::get('/dormitory/delete/{id}','DormitoryController@delete');

  Route::get('/dormitory/getstudents/{dormid}','DormitoryController@getstudents');

  Route::get('/dormitory/assignstd','DormitoryController@stdindex');
  Route::post('/dormitory/assignstd/create','DormitoryController@stdcreate');
  Route::get('/dormitory/assignstd/list','DormitoryController@stdshow');
  Route::post('/dormitory/assignstd/list','DormitoryController@poststdShow');
  Route::get('/dormitory/assignstd/edit/{id}','DormitoryController@stdedit');
  Route::post('/dormitory/assignstd/update','DormitoryController@stdupdate');
  Route::get('/dormitory/assignstd/delete/{id}','DormitoryController@stddelete');

  Route::get('/dormitory/fee','DormitoryController@feeindex');
  Route::post('/dormitory/fee','DormitoryController@feeadd');
  Route::get('/dormitory/fee/info/{regiNo}','DormitoryController@feeinfo');

  Route::get('/dormitory/report/std','DormitoryController@reportstd');
  Route::get('/dormitory/report/std/{dormId}','DormitoryController@reportstdprint');
  Route::get('/dormitory/report/fee','DormitoryController@reportfee');
  Route::get('/dormitory/my-room',[ 'as' => 'dormitory.myroom','uses'=>'DormitoryController@myRoom']);
  Route::post('/dormitory/signout',[ 'as' => 'dormitory.signout','uses'=>'DormitoryController@signout']);
  Route::post('/dormitory/request-signin',[ 'as' => 'dormitory.request.signin','uses'=>'DormitoryController@requestSignin']);
  Route::get('/dormitory/requests',[ 'as' => 'dormitory.requests','uses'=>'DormitoryController@pendingRequests']);
  Route::get('/dormitory/requests/{id}/approve',[ 'as' => 'dormitory.approve','uses'=>'DormitoryController@approveRequest']);
  Route::get('/dormitory/requests/{id}/reject',[ 'as' => 'dormitory.reject','uses'=>'DormitoryController@rejectRequest']);
  Route::get('/dormitory/report/fee/{dormId}/{month}','DormitoryController@reportfeeprint');

  //barcode generate
  Route::get('/barcode','barcodeController@index');
  Route::post('/barcode','barcodeController@generate');
  //db triggers crate route
  Route::get('/hrs-triggers-init',function(){
    //create tiggers for manage book stock
    //book addd trigger
    DB::unprepared('
    CREATE TRIGGER `afterBookAdd` AFTER INSERT ON `books` FOR EACH ROW
    BEGIN
    insert into stock_books
    set
    books_id = new.id,
    quantity = new.quantity;
    END
    ');
    //after book delete
    DB::unprepared('
    CREATE TRIGGER `afterBookDelete` AFTER DELETE ON `books` FOR EACH ROW
    BEGIN
    delete from borrow_books where books_id = old.id;
    delete from stock_books where books_id = old.id;
    END
    ');
    //afeter book update
    DB::unprepared('
    CREATE TRIGGER `afterBookUpdate` AFTER UPDATE ON `books` FOR EACH ROW
    BEGIN
    UPDATE stock_books
    set
    quantity = new.quantity-(old.quantity-quantity)
    WHERE books_id=old.id;
    END
    ');
    //after borrow book add
    DB::unprepared('
    CREATE TRIGGER `afterBorrowBookAdd` AFTER INSERT ON `borrow_books` FOR EACH ROW
    BEGIN
    UPDATE stock_books
    set quantity = quantity-new.quantity
    where books_id=new.books_id;
    END
    ');
    //after borrow book delete
    DB::unprepared("
    CREATE TRIGGER `afterBorrowBookDelete` AFTER DELETE ON `borrow_books` FOR EACH ROW
    IF (old.Status='Borrowed') THEN
    UPDATE stock_books
    set quantity = quantity+old.quantity
    WHERE books_id=old.books_id;
    END IF
    ");
    //after borrow book update
    DB::unprepared("
    CREATE TRIGGER `afterBorrowBookUpdate` AFTER UPDATE ON `borrow_books` FOR EACH ROW
    IF (new.Status='Returned') THEN
    UPDATE stock_books
    set quantity = quantity+new.quantity
    WHERE books_id=new.books_id;
    END IF
    ");
    return "Done!....";
  });


});
