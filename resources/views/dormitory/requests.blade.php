@extends('layouts.master')
@section('title', 'Dormitory Sign-In Requests')
@section('content')
<div class="right_col" role="main">
  <div class="">
    <div class="row">
      <div class="col-md-12">
        <div class="x_panel">
          <div class="x_title"><h2><i class="fa fa-building"></i> Pending Sign-In Requests</h2><div class="clearfix"></div></div>
          <div class="x_content">
            @if($requests->count() > 0)
            <table class="table table-bordered table-striped">
              <thead><tr><th>Student</th><th>Dormitory</th><th>Room</th><th>Requested</th><th>Actions</th></tr></thead>
              <tbody>
                @foreach($requests as $r)
                <tr>
                  <td>{{$r->idNo}} - {{$r->firstName}} {{$r->lastName}}</td>
                  <td>{{$r->dormitory}}</td>
                  <td><strong>{{$r->roomNo}}</strong></td>
                  <td>{{$r->created_at}}</td>
                  <td>
                    <a href="{{URL::route('dormitory.approve', $r->id)}}" class="btn btn-success btn-xs" onclick="return confirm('Approve this sign-in request?')"><i class="fa fa-check"></i> Approve</a>
                    <a href="{{URL::route('dormitory.reject', $r->id)}}" class="btn btn-danger btn-xs" onclick="return confirm('Reject this sign-in request?')"><i class="fa fa-times"></i> Reject</a>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
            @else
            <div class="alert alert-info"><i class="fa fa-info-circle"></i> No pending sign-in requests.</div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
