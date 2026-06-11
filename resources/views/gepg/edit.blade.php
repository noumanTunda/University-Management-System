@extends('layouts.master')

@section('title', 'Edit Bill')

@section('content')
<div class="right_col" role="main">
  <div class="">
    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-6 col-md-offset-3">
        <div class="x_panel">
          <div class="x_title"><h2>Edit Bill</h2><div class="clearfix"></div></div>
          <div class="x_content">
            <form method="post" action="{{URL::route('gepg.bill.update', $bill->id)}}">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <div class="form-group">
                <label>Student</label>
                <input class="form-control" value="{{$bill->student->firstName}} {{$bill->student->lastName}}" disabled>
              </div>
              <div class="form-group">
                <label>Control Number</label>
                <input class="form-control" name="control_number" value="{{$bill->control_number}}">
              </div>
              <div class="form-group">
                <label>Description</label>
                <input class="form-control" name="bill_description" value="{{$bill->bill_description}}">
              </div>
              <div class="form-group">
                <label>Amount (TZS)</label>
                <input class="form-control" name="amount" value="{{$bill->amount}}" step="0.01">
              </div>
              <div class="form-group">
                <label>Status</label>
                <select class="form-control" name="status">
                  <option {{$bill->status=='Pending'?'selected':''}}>Pending</option>
                  <option {{$bill->status=='Issued'?'selected':''}}>Issued</option>
                  <option {{$bill->status=='Paid'?'selected':''}}>Paid</option>
                  <option {{$bill->status=='Expired'?'selected':''}}>Expired</option>
                </select>
              </div>
              <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-save"></i> Update</button>
              <a href="{{URL::route('gepg.accountant')}}" class="btn btn-default">Back</a>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
