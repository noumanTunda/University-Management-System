@extends('layouts.master')
@section('title', 'My Attendance')
@section('content')
<div class="right_col" role="main">
  <div class=""><h3><i class="fa fa-calendar"></i> My Attendance Records</h3>
    <div class="row">
      <div class="col-md-12">
        <div class="x_panel">
          <div class="x_content">
            <table class="table table-striped">
              <thead><tr><th>Date</th><th>Subject</th><th>Status</th></tr></thead>
              <tbody>
                @forelse($records as $r)
                <tr>
                  <td>{{$r->date}}</td>
                  <td>{{$r->subject->name ?? '-'}}</td>
                  <td>{!! $r->present ? '<span class="label label-success">Present</span>' : '<span class="label label-danger">Absent</span>' !!}</td>
                </tr>
                @empty <tr><td colspan="3">No attendance records.</td></tr> @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
