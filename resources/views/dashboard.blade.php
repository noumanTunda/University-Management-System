@extends('layouts.master')

@section('title', 'Dashboard')

@section('extrastyle')
<style>
.stat-card {
    border-radius: 6px; padding: 16px 14px; margin-bottom: 16px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05); position: relative; overflow: hidden;
}
.stat-card .stat-icon {
    position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
    font-size: 34px; opacity: 0.12;
}
.stat-card .stat-label { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: .3px; margin-bottom: 2px; }
.stat-card .stat-value { font-size: 26px; font-weight: 700; line-height: 1.2; }
.stat-card .stat-desc { font-size: 11px; opacity: .7; margin-top: 2px; }
.bg-primary { background: #d4edda; color: #155724; }
.bg-info { background: #d1ecf1; color: #0c5460; }
.bg-warning { background: #fff3cd; color: #856404; }
.bg-danger { background: #f8d7da; color: #721c24; }
.bg-success { background: #d4edda; color: #155724; }
.bg-purple { background: #e8daef; color: #6c3483; }
.bg-teal { background: #d1f2eb; color: #0e6655; }
.bg-dark { background: #d6dbdf; color: #2c3e50; }
.qlink { text-align: center; padding: 12px 5px; border-radius: 6px; transition: all .2s; display: block; color: #555; text-decoration: none; }
.qlink:hover { background: #f0f0f0; color: #1ab394; text-decoration: none; }
.qlink i { font-size: 24px; display: block; margin-bottom: 4px; }
.qlink span { font-size: 12px; font-weight: 500; }
.chart-wrap { padding: 5px; }
</style>
@endsection

@section('content')
<div class="right_col" role="main">
  <div class="">

    <!-- Stat Cards Row 1 -->
    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="stat-card bg-primary">
          <i class="fa fa-users stat-icon"></i>
          <div class="stat-label">Admitted Students</div>
          <div class="stat-value">{{$total["admitted"]}}</div>
          <div class="stat-desc"><i class="fa fa-check-circle"></i> {{$total["registered"]}} registered</div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="stat-card bg-info">
          <i class="fa fa-book stat-icon"></i>
          <div class="stat-label">Subjects</div>
          <div class="stat-value">{{$total["subject"]}}</div>
          <div class="stat-desc"><i class="fa fa-book"></i> {{$total["book"]}} books in library</div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="stat-card bg-success">
          <i class="fa fa-graduation-cap stat-icon"></i>
          <div class="stat-label">Courses</div>
          <div class="stat-value">{{$total["course"]}}</div>
          <div class="stat-desc"><i class="fa fa-home"></i> {{$total["department"]}} departments</div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="stat-card bg-warning">
          <i class="fa fa-edit stat-icon"></i>
          <div class="stat-label">Exams</div>
          <div class="stat-value">{{$total["exam"]}}</div>
          <div class="stat-desc"><i class="fa fa-calendar"></i> {{$total["attendance"]}} attendance days</div>
        </div>
      </div>
    </div>

    <!-- Stat Cards Row 2 -->
    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="stat-card bg-danger">
          <i class="fa fa-user stat-icon"></i>
          <div class="stat-label">Users</div>
          <div class="stat-value">{{$total["user"]}}</div>
          <div class="stat-desc">System accounts</div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="stat-card bg-purple">
          <i class="fa fa-money stat-icon"></i>
          <div class="stat-label">Fee Collections</div>
          <div class="stat-value">{{$total["fee_collection"]}}</div>
          <div class="stat-desc">Balance: {{number_format($balance, 2)}}</div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="stat-card bg-teal">
          <i class="fa fa-home stat-icon"></i>
          <div class="stat-label">Dormitories</div>
          <div class="stat-value">{{$total["dormitory"]}}</div>
          <div class="stat-desc">Hostel blocks</div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="stat-card bg-dark">
          <i class="fa fa-graduation-cap stat-icon"></i>
          <div class="stat-label">Attendance</div>
          <div class="stat-value">{{$total["attendance"]}}</div>
          <div class="stat-desc">Distinct days recorded</div>
        </div>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
      <div class="col-md-8">
        <div class="x_panel">
          <div class="x_title">
            <h2><i class="fa fa-line-chart"></i> Monthly Accounting</h2>
            <div class="navbar-right"><small>Balance: <strong>{{number_format($balance, 2)}}</strong></small></div>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <canvas height="60" id="lineChart"></canvas>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="x_panel">
          <div class="x_title">
            <h2><i class="fa fa-bar-chart"></i> Exam Avg Scores</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <canvas height="140" id="examChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Links -->
    <div class="row">
      <div class="col-md-12">
        <div class="x_panel">
          <div class="x_title">
            <h2><i class="fa fa-link"></i> Quick Actions</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <div class="row">
              <div class="col-md-2 col-sm-3 col-xs-4">
                <a href="{{URL::route('student.create')}}" class="qlink"><i class="fa fa-user-plus text-success"></i><span>New Admission</span></a>
              </div>
              <div class="col-md-2 col-sm-3 col-xs-4">
                <a href="{{URL::route('student.registration.create')}}" class="qlink"><i class="fa fa-check-square-o text-info"></i><span>Registration</span></a>
              </div>
              <div class="col-md-2 col-sm-3 col-xs-4">
                <a href="{{URL::route('attendance.create')}}" class="qlink"><i class="fa fa-pencil text-warning"></i><span>Attendance</span></a>
              </div>
              <div class="col-md-2 col-sm-3 col-xs-4">
                <a href="{{URL::route('exam.create')}}" class="qlink"><i class="fa fa-edit text-primary"></i><span>New Exam</span></a>
              </div>
              <div class="col-md-2 col-sm-3 col-xs-4">
                <a href="{{URL::route('fees.collection.create')}}" class="qlink"><i class="fa fa-money text-danger"></i><span>Fee Collect</span></a>
              </div>
              <div class="col-md-2 col-sm-3 col-xs-4">
                <a href="{{URL::to('/library/issuebook')}}" class="qlink"><i class="fa fa-book text-info"></i><span>Borrow Book</span></a>
              </div>
              <div class="col-md-2 col-sm-3 col-xs-4">
                <a href="{{URL::route('exam.index')}}" class="qlink"><i class="fa fa-list text-maroon"></i><span>Exam List</span></a>
              </div>
              <div class="col-md-2 col-sm-3 col-xs-4">
                <a href="{{URL::route('user.create')}}" class="qlink"><i class="fa fa-user text-primary"></i><span>New User</span></a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection

@section('extrascript')
<script src="{{ URL::asset('assets/js/Chart.min.js')}}"></script>
<script>
Chart.defaults.global.legend = { display: false };
Chart.defaults.global.tooltips = { backgroundColor: 'rgba(0,0,0,.7)' };

// Monthly Income/Expense - Line
var ctx = document.getElementById("lineChart");
if (ctx) {
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ["<?php echo join('","', $incomes['key'])?>"],
            datasets: [{
                label: "Income",
                backgroundColor: "rgba(26, 179, 148, 0.15)",
                borderColor: "#1ab394",
                pointBackgroundColor: "#1ab394",
                pointBorderColor: "#fff",
                pointBorderWidth: 2,
                pointRadius: 4,
                borderWidth: 2,
                data: [<?php echo join(',', $incomes['value'])?>]
            }, {
                label: "Expense",
                backgroundColor: "rgba(231, 76, 60, 0.1)",
                borderColor: "#e74c3c",
                pointBackgroundColor: "#e74c3c",
                pointBorderColor: "#fff",
                pointBorderWidth: 2,
                pointRadius: 4,
                borderWidth: 2,
                data: [<?php echo join(',', $expences['value'])?>]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                yAxes: [{ ticks: { beginAtZero: true, maxTicksLimit: 6 } }],
                xAxes: [{ gridLines: { display: false } }]
            }
        }
    });
}

// Exam Avg Scores - Bar
var examCtx = document.getElementById("examChart");
if (examCtx && <?php echo count($examChart['keys']) > 0 ? 'true' : 'false'?>) {
    new Chart(examCtx, {
        type: 'bar',
        data: {
            labels: [<?php echo "'" . implode("','", $examChart['keys']) . "'"?>],
            datasets: [{
                backgroundColor: [
                    'rgba(26,179,148,.7)', 'rgba(45,123,205,.7)',
                    'rgba(243,156,18,.7)', 'rgba(231,76,60,.7)',
                    'rgba(39,174,96,.7)', 'rgba(142,68,173,.7)'
                ],
                borderWidth: 0,
                data: [<?php echo implode(',', $examChart['avgs'])?>]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                yAxes: [{ ticks: { beginAtZero: true, maxTicksLimit: 5 } }],
                xAxes: [{ gridLines: { display: false }, barPercentage: .6 }]
            }
        }
    });
}
</script>
@endsection
