@extends('layouts.master')
@section('title', 'My Room')
@section('content')
<div class="right_col" role="main">
  <div class="">
    <div class="row">
      <div class="col-md-6 col-md-offset-3">
        <div class="x_panel">
          <div class="x_title"><h2><i class="fa fa-building"></i> My Room Allocation</h2><div class="clearfix"></div></div>
          <div class="x_content">
            @if($assignment)
            <table class="table table-bordered">
              <tr><th>Dormitory</th><td>{{$assignment->dormitory}}</td></tr>
              <tr><th>Room No</th><td><strong>{{$assignment->room_no}}</strong></td></tr>
              <tr><th>Assigned Since</th><td>{{$assignment->joinDate}}</td></tr>
              <tr><th>Status</th><td><span class="label label-success">Active</span></td></tr>
            </table>
            @else
            <div class="alert alert-info">
              <i class="fa fa-info-circle"></i> You have not been assigned to any dormitory room yet. Contact the administration.
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
