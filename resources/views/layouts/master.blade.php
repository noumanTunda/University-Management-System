<!-- <!DOCTYPE html> -->
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>{{session('inNameShort')}} - @yield("title")</title>
    <!-- Bootstrap -->
    <link href="{{ URL::asset('assets/css/bootstrap.min.css')}}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="{{ URL::asset('assets/css/font-awesome.min.css')}}" rel="stylesheet">
    <link href="{{ URL::asset('assets/css/nprogress.css')}}" rel="stylesheet">
    <link href="{{ URL::asset('assets/css/jquery.mCustomScrollbar.min.css')}}" rel="stylesheet">
    <link href="{{ URL::asset('assets/css/pnotify.css')}}" rel="stylesheet">
    <link href="{{ URL::asset('assets/css/pnotify.buttons.css')}}" rel="stylesheet">
    <!-- Custom Theme Style -->
		<link href="{{ URL::asset('assets/css/custom.min.css')}}" rel="stylesheet">
		<link href="{{ URL::asset('assets/css/app.css')}}" rel="stylesheet">
		<link href="{{ URL::asset('assets/css/select2.min.css')}}" rel="stylesheet">
    @yield("extrastyle")
    <script>
      var hash = '{{session('user_session_sha1')}}';
    </script>
  </head>

  <body class="nav-md footer_fixed">

<div class="container body">
      <div class="main_container">

<div class="col-md-3 left_col menu_fixed">
          <div class="left_col scroll-view">

<div class="navbar nav_title" style="border: 0;">
              <a href="{{URL::route('user.dashboard')}}" class="site_title"><i class="fa fa-bank"></i> <span> {{session('inNameShort')}}</span></a>
            </div>

            <div class="clearfix"></div>


            <br />

            <!-- sidebar menu -->
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
              <div class="menu_section">
                <h3>Primary Menu</h3>
                <ul class="nav side-menu">
                  <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard </a></li>
				  @if(Gate::check('Student'))
				  <li><a href="{{URL::route('student.dashboard')}}"><i class="fa fa-graduation-cap"></i> My Portal</a></li>
				  <li><a href="{{URL::route('student.assessments')}}"><i class="fa fa-bar-chart"></i> My Results</a></li>
				  <li><a href="{{URL::route('student.attendance')}}"><i class="fa fa-calendar"></i> My Attendance</a></li>
				  <li><a href="{{URL::route('gepg.student')}}"><i class="fa fa-money"></i> Pay Fees</a></li>
				  <li><a href="/library/search"><i class="fa fa-book"></i> Library</a></li>
				  <li><a href="/library/issuebookview"><i class="fa fa-list"></i> My Books</a></li>
				  @endif
                  @can('Admin')
                  <li><a><i class="fa fa-home"></i> Departments <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="{{URL::route('department.create')}}">Add New</a></li>
                      <li><a href="{{URL::route('department.index')}}">All Departments</a></li>

                    </ul>
                  </li>
                  @endcan
                  @if(Gate::check('Admin') || Gate::check('HeadOfDepartment'))
                  <li><a><i class="fa fa-book"></i> Subjects <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="{{URL::route('subject.create')}}">Add New</a></li>
                      <li><a href="{{URL::route('subject.index')}}">All Subjects</a></li>
                      <li><a href="{{URL::route('teacher.subject.index')}}">Assign to Teachers</a></li>
                    </ul>
                  </li>
                  <li><a><i class="fa fa-graduation-cap"></i> Courses <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="{{URL::route('course.create')}}">Add New</a></li>
                      <li><a href="{{URL::route('course.index')}}">All Courses</a></li>
                    </ul>
                  </li>
                  <li><a><i class="fa fa-users"></i> Students <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="{{URL::route('student.create')}}">New Admission</a></li>
                      <li><a href="{{URL::route('student.index')}}">Admited Student List</a></li>
                      <li><a href="{{URL::route('student.registration.create')}}">New Registration</a></li>
                      <li><a href="{{URL::route('student.registration.index')}}">Registered Student List</a></li>
                      </ul>
                  </li>
                  @endif
                  @if(Gate::check('Admin') || Gate::check('Teacher') || Gate::check('HeadOfDepartment'))
                  <li><a><i class="fa fa-pencil"></i> Attendance <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="{{URL::route('attendance.create')}}">New </a></li>
                      <li><a href="{{URL::route('attendance.index')}}">List</a></li>
                      </ul>
                  </li>
                  <li><a><i class="fa fa-edit"></i> Exams <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="{{URL::route('exam.marks.create')}}">Mark Entry (CA/UE) </a></li>
                      <li><a href="{{URL::route('exam.create')}}">Legacy Entry</a></li>
                      <li><a href="{{URL::route('exam.index')}}">Mark View</a></li>
                      @if(Gate::check('Admin') || Gate::check('HeadOfDepartment'))
                      <li><a href="{{URL::route('exam_type.index')}}">Exam Types</a></li>
                      @endif
                      </ul>
                  </li>
                  <li><a><i class="fa fa-tasks"></i> Assessments <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="{{URL::route('assessment.index')}}">Plans & Marks</a></li>
                      <li><a href="{{URL::route('assessment.create')}}">New Plan</a></li>
                    </ul>
                  </li>
                  <li><a><i class="fa fa-file-text"></i> Result <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="{{URL::route('result.subject')}}"> Subject Wise </a></li>
                      <li><a href="{{URL::route('result.individual')}}">Student Wise</a></li>
                      </ul>
                  </li>
                  @endif
                  @if(Gate::check('Admin') || Gate::check('Account'))
                  <li><a><i class="fa fa-money"></i> Accounting <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="{{URL::route('accounting.coa')}}">Chart of Accounts</a></li>
                      <li><a href="{{URL::route('accounting.invoices')}}">Fee Invoices</a></li>
                      <li><a href="{{URL::route('accounting.journal')}}">Journal Entries</a></li>
                      <li><a href="{{URL::route('accounting.trial.balance')}}">Trial Balance</a></li>
                      </ul>
                  </li>
                  <li><a><i class="glyphicon glyphicon-list-alt"></i> Fees <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="{{URL::route('fees.index')}}">Fee List </a></li>
                      <li><a href="{{URL::route('fees.collection.create')}}">Fee Collection </a></li>
                      <li><a href="{{ URL::route('fees.addPaymentForm') }}">Add Student Payment</a></li>
                      <li><a href="{{URL::route('gepg.student')}}">Pay Fees (GePG)</a></li>
                      <li><a href="{{URL::route('gepg.allocate.form')}}">Fee Allocation</a></li>
                      <li><a href="{{URL::route('gepg.accountant')}}">GePG Bills</a></li>
                    </ul>
                  </li>
                  <li><a><i class="fa fa-print"></i> Reports <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="{{URL::route('accounting.reports.type')}}">Account By Type </a></li>
                      <li><a href="{{URL::route('accounting.reports.balance')}}">Account Balance </a></li>
                      <li><a href="{{URL::route('fees.collection.index')}}">Student Fees </a></li>
                      <li><a href="{{URL::route('fees.collection.report')}}">Fees Collection </a></li>

                      </ul>
                  </li>
                  @endif
                  @can('Admin')
                  <li><a><i class="fa fa-users"></i> Users <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="{{URL::route('user.create')}}">New</a></li>
                      <li><a href="{{URL::route('user.index')}}">All User</a></li>
                    </ul>
                  </li>
                  <li><a  href="{{URL::route('institute.index')}}"><i class="fa fa-building" ></i> Institute </a>

                  </li>
                  @endcan
                  @if(Gate::check('Admin') || Gate::check('Teacher') || Gate::check('HeadOfDepartment') || Gate::check('Account'))
                  <li><a><i class="fa fa-book"></i> Library <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="/library/search">Book Search</a></li>
                      <li><a href="/library/issuebook">Borrow Book</a></li>
                      <li><a href="/library/issuebookview">Borrowed Book List</a></li>
                      <li><a href="/library/view">Book List</a></li>
                      <li><a href="/library/addbook">Book Entry</a></li>
                      <li><a href="/barcode">Barcode Generate</a></li>
                      <li><a href="/library/reports">Reports</a></li>
                      <li><a href="/library/reports/fine">Monthly Fine Reports</a></li>
                    </ul>
                  </li>
                  <li><a><i class="fa fa-home"></i> Dormitory <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="/dormitory">Dormitory</a></li>
                      <li><a href="/dormitory/assignstd">Assign Student</a></li>
                      <li><a href="/dormitory/assignstd/list">Student List</a></li>
                      <li><a href="/dormitory/fee">Fee Collection</a></li>
                      <li><a href="/dormitory/report/std">Dormitory Report</a></li>
                      <li><a href="/dormitory/report/fee">Fee Reports</a></li>
                    </ul>
                  </li>
                  @endif
                </ul>
              </div>
              <div class="menu_section">
                <h3></h3>

              </div>

            </div>
            <!-- /sidebar menu -->

            <!-- /menu footer buttons -->
          <div class="sidebar-footer hidden-small">
            <a href="{{URL::route('user.settings')}}" data-toggle="tooltip" data-placement="top" title="Settings">
              <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
            </a>
            <a class="fullScreen" data-toggle="tooltip" data-placement="top" title="FullScreen">
              <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
            </a>
            <a href="{{URL::route('lock')}}" data-toggle="tooltip" data-placement="top" title="Lock">
              <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
            </a>
            <a href="{{URL::route('user.logout')}}" data-toggle="tooltip" data-placement="top" title="Logout">
              <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
            </a>
          </div>
          <!-- /menu footer buttons -->
          </div>
        </div>

        <!-- top navigation -->
      <div class="top_nav">
        <div class="nav_menu">
          <nav class="" role="navigation">
            <div class="nav toggle">
              <a id="menu_toggle"><i class="fa fa-bars"></i></a>
            </div>

            <ul class="nav navbar-nav navbar-right">
              <li class="">
                <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    	<img src="/assets/images/users/user.png" alt="">{{Session::get('name')}}
                  <span class=" fa fa-angle-down"></span>
                </a>
                <ul class="dropdown-menu dropdown-usermenu">
                  <li>
                    <a href="{{URL::route('user.settings')}}"><i class="glyphicon glyphicon-cog"></i> Settings</a>
                  </li>
                  <li>
                    <a class="fullScreen">
                      <i class="glyphicon glyphicon-fullscreen"></i> Full Screen
                    </a>
                  </li>
                  <li>
                    <a href="{{URL::route('lock')}}"><i class="glyphicon  glyphicon-eye-close"></i> Lock Screen</a>
                  </li>
                  <li><a href="{{URL::route('user.logout')}}"><i class="glyphicon glyphicon-off"></i> Log Out</a></li>
                </ul>
              </li>
          </ul>
          </nav>
        </div>
      </div>
      <!-- /top navigation -->

  <!--Child Page Content Start  -->

  @yield('content')

  <!--Child Page Content End  -->

@include('layouts.footer')

<!-- jQuery -->
<script src="{{ URL::asset('assets/js/jquery.min.js')}}"></script>
<!-- Bootstrap -->
<script src="{{ URL::asset('assets/js/bootstrap.min.js')}}"></script>
<!-- FastClick -->
<script src="{{ URL::asset('assets/js/fastclick.js')}}"></script>
<!-- NProgress -->
<script src="{{ URL::asset('assets/js/nprogress.js')}}"></script>

<script src="{{ URL::asset('assets/js/jquery.mCustomScrollbar.concat.min.js')}}"></script>

<script src="{{ URL::asset('assets/js/pnotify.js')}}"></script>
<script src="{{ URL::asset('assets/js/pnotify.buttons.js')}}"></script>
	<script src="{{ URL::asset('assets/js/select2.full.min.js')}}"></script>
	<script>$(document).ready(function() { $('.select2, select.form-control:not(.no-select2)').select2({ width: '100%' }); });</script>

@yield("extrascript")
<!-- Custom Theme Scripts -->
<script src="{{ URL::asset('assets/js/custom.min.js')}}"></script>
<script src="{{ URL::asset('assets/js/app.js')}}"></script>

<!-- PNotify -->
  <script>
    $(document).ready(function() {
      @if(Session::has('success'))
      new PNotify({
            title: '{{Session::get("success")["title"]}}',
            text: '{{Session::get("success")["body"]}}',
            type: 'success',
            styling: 'bootstrap3'
      });
      @endif
      @if(Session::has('error'))
      new PNotify({
            title: '{{Session::get("error")["title"]}}',
            text: '{{Session::get("error")["body"]}}',
            type: 'error',
            styling: 'bootstrap3'
      });
      @endif
      @if(Session::has('warning'))
      new PNotify({
            title: '{{Session::get("warning")["title"]}}',
            text: '{{Session::get("warning")["body"]}}',
            styling: 'bootstrap3'
      });
      @endif

    });
  </script>
  <!-- /PNotify -->

</body>
</html>