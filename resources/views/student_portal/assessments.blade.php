@extends('layouts.master')
@section('title', 'My Results')
@section('content')
<div class="right_col" role="main">
  <div class=""><h3><i class="fa fa-bar-chart"></i> My Assessment Results</h3>
    <div class="row">
      <div class="col-md-12">
        <div class="x_panel">
          <div class="x_content">
            <table class="table table-striped">
              <thead><tr><th>Subject</th><th>Year</th><th>Semester</th><th>CA Score</th><th>UE Score</th><th>Total</th><th>Grade</th><th>GP</th><th>Status</th></tr></thead>
              <tbody>
                @forelse($courseRegs as $cr)
                <tr>
                  <td>{{$cr->subject->name ?? '-'}}</td>
                  <td>{{$cr->semester->academicYear->name ?? '-'}}</td>
                  <td>Semester {{$cr->semester->semester_number ?? '-'}}</td>
                  <td>{{$cr->ca_score ?? '-'}}</td>
                  <td>{{$cr->ue_score ?? '-'}}</td>
                  <td><strong>{{($cr->ca_score ?? 0) + ($cr->ue_score ?? 0)}}</strong></td>
                  <td><span class="label label-{{$cr->status=='Pass'?'success':'danger'}}">{{$cr->grade_letter ?? '-'}}</span></td>
                  <td>{{$cr->grade_point ?? '-'}}</td>
                  <td>{{$cr->status ?? '-'}}</td>
                </tr>
                @empty <tr><td colspan="9">No results yet.</td></tr> @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
