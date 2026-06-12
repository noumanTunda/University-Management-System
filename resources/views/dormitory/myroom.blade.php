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
            @if($assignment && $assignment->isActive)
            <div class="alert alert-success"><i class="fa fa-check-circle"></i> Your room is currently <strong>active</strong>.</div>
            <table class="table table-bordered">
              <tr><th>Dormitory</th><td>{{$assignment->dormitory}}</td></tr>
              <tr><th>Address</th><td>{{$assignment->address ?? 'N/A'}}</td></tr>
              <tr><th>Room No</th><td><strong>{{$assignment->roomNo}}</strong></td></tr>
              <tr><th>Assigned Since</th><td>{{$assignment->joinDate}}</td></tr>
              <tr><th>Status</th><td><span class="label label-success">Active</span></td></tr>
            </table>
            <hr>
            <form method="POST" action="{{URL::route('dormitory.signout')}}" onsubmit="return confirm('Sign out of this room? You will need to submit your keys to the administration.')">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <div class="form-group">
                <label>Reason for sign-out (optional)</label>
                <input type="text" name="reason" class="form-control" placeholder="e.g. End of semester, personal reasons">
              </div>
              <button type="submit" class="btn btn-warning btn-block"><i class="fa fa-sign-out"></i> Sign Out & Submit Keys</button>
            </form>

            @elseif($lastAssignment)
            <div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> You have <strong>signed out</strong> of your room. Keys must be submitted to the administration.</div>
            <table class="table table-bordered">
              <tr><th>Dormitory</th><td>{{$lastAssignment->dormitories_id ? DB::table('dormitories')->where('id', $lastAssignment->dormitories_id)->value('name') : '-'}}</td></tr>
              <tr><th>Room No</th><td><strong>{{$lastAssignment->roomNo ?? '-'}}</strong></td></tr>
              <tr><th>Signed Out</th><td>{{$lastAssignment->signed_out_at ?? '-'}}</td></tr>
              <tr><th>Reason</th><td>{{$lastAssignment->signout_reason ?? '-'}}</td></tr>
              <tr><th>Status</th><td><span class="label label-danger">Inactive</span></td></tr>
            </table>
            @if($pendingRequest)
              <div class="alert alert-info"><i class="fa fa-clock-o"></i> Your sign-in request is <strong>pending approval</strong>. Wait for a Teacher or HOD to approve.</div>
            @else
              <hr>
              <form method="POST" action="{{URL::route('dormitory.request.signin')}}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <button type="submit" class="btn btn-success btn-block"><i class="fa fa-sign-in"></i> Request Sign In</button>
                <p class="help-block text-center">A Teacher or HOD must approve your sign-in before the room becomes active again.</p>
              </form>
            @endif

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
