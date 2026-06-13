@extends('layouts.master')
@section('title', 'Student Dashboard')
@section('content')
<div class="right_col" role="main">
  <div class="">
    <h3><i class="fa fa-user"></i> Welcome, {{$student->firstName}} {{$student->lastName}}</h3>
    <div class="row">
      <div class="col-md-3 col-sm-6">
        <div class="stat-card" style="background:#d4edda;color:#155724;border-radius:6px;padding:18px;margin-bottom:16px">
          <div class="stat-label">Registrations</div>
          <div class="stat-value" style="font-size:28px;font-weight:700">{{$registrations->count()}}</div>
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="stat-card" style="background:#d1ecf1;color:#0c5460;border-radius:6px;padding:18px;margin-bottom:16px">
          <div class="stat-label">Attendance Days</div>
          <div class="stat-value" style="font-size:28px;font-weight:700">{{$attendances}}</div>
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="stat-card" style="background:#fff3cd;color:#856404;border-radius:6px;padding:18px;margin-bottom:16px">
          <div class="stat-label">Borrowed Books</div>
          <div class="stat-value" style="font-size:28px;font-weight:700">{{$borrowedBooks}}</div>
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="stat-card" style="background:#f8d7da;color:#721c24;border-radius:6px;padding:18px;margin-bottom:16px">
          <div class="stat-label">Pending Bills</div>
          <div class="stat-value" style="font-size:28px;font-weight:700">{{$bills->where('status','!=','Paid')->count()}}</div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="x_panel">
          <div class="x_title"><h2><i class="fa fa-book"></i> Course Registrations</h2><div class="clearfix"></div></div>
          <div class="x_content">
            <table class="table table-striped">
              <thead><tr><th>Year</th><th>Semester</th><th>Department</th></tr></thead>
              <tbody>
                @forelse($registrations as $r)
                <tr><td>{{$r->session}}</td><td>{{$r->levelTerm}}</td><td>{{$r->department->name ?? '-'}}</td></tr>
                @empty <tr><td colspan="3">Not registered yet.</td></tr> @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="x_panel">
          <div class="x_title"><h2><i class="fa fa-line-chart"></i> Enrolled Subjects & Results</h2><div class="clearfix"></div></div>
          <div class="x_content">
            @forelse($grouped as $key => $regs)
              @php
                list($year, $sem) = explode('|', $key);
                $collapseId = 'dash_sem_' . str_replace(['/', ' '], '_', $year) . '_' . $sem;
              @endphp
              <div class="panel panel-default" style="margin-bottom:10px">
                <div class="panel-heading" role="tab" style="cursor:pointer" data-toggle="collapse" data-target="#{{$collapseId}}" aria-expanded="true">
                  <h4 class="panel-title">
                    <i class="fa fa-chevron-down"></i> <strong>{{$year}}</strong> — Semester {{$sem}}
                    <span class="badge pull-right">{{count($regs)}} subject(s)</span>
                  </h4>
                </div>
                <div id="{{$collapseId}}" class="panel-collapse collapse in">
                  <div class="panel-body" style="padding:0">
                    <table class="table table-striped" style="margin-bottom:0">
                      <thead><tr><th>Subject</th><th>Code</th><th>CA</th><th>UE</th><th>Grade</th></tr></thead>
                      <tbody>
                        @foreach($regs as $cr)
                        <tr>
                          <td>{{$cr->subject->name ?? '-'}}</td>
                          <td>{{$cr->subject->code ?? '-'}}</td>
                          <td>{{$cr->ca_score ?? '-'}}</td>
                          <td>{{$cr->ue_score ?? '-'}}</td>
                          <td><strong>{{$cr->grade_letter ?? '-'}}</strong> ({{$cr->grade_point ?? '-'}})</td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            @empty
              <div class="alert alert-info">No subjects enrolled yet.</div>
            @endforelse
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('extrascript')
<script>
$(document).ready(function() {
    // Collapse toggle icon
    $('.panel-heading').on('click', function() {
        var icon = $(this).find('i.fa');
        icon.toggleClass('fa-chevron-down fa-chevron-right');
    });
});
</script>
@endsection
