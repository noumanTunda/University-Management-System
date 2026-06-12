@extends('layouts.master')
@section('title', 'My Fees')
@section('content')
<div class="right_col" role="main">
  <div class="">
    <div class="clearfix"></div>
    <div class="row">
      @if(!$student)
      <div class="col-md-12"><div class="alert alert-warning">No student profile linked to your account. Contact admin.</div></div>
      @else
      <div class="col-md-5">
        <div class="x_panel">
          <div class="x_title"><h2>Request Missing Payment</h2><div class="clearfix"></div></div>
          <div class="x_content">
            <p class="text-muted">If you need to pay a fee that is not yet billed, submit a request. Accountant will issue a control number.</p>
            <form method="post" action="{{URL::route('gepg.request')}}">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <div class="form-group">
                <label>Description</label>
                <input type="text" name="description" class="form-control" placeholder="e.g. Lab fee, Library fine" required>
              </div>
              <div class="form-group">
                <label>Amount (TZS)</label>
                <input type="number" name="amount" class="form-control" required min="1" step="0.01">
              </div>
              <button type="submit" class="btn btn-warning"><i class="fa fa-send"></i> Submit Request</button>
            </form>
          </div>
        </div>
      </div>
      <div class="col-md-7">
        <div class="x_panel">
          <div class="x_title"><h2>My Bills</h2><div class="clearfix"></div></div>
          <div class="x_content">
            <table class="table table-bordered">
              <thead><tr><th>Control No</th><th>Fee</th><th>Amount</th><th>Status</th><th>Expires</th></tr></thead>
              <tbody>
                @forelse($bills as $b)
                <tr>
                  <td><code>{{$b->control_number}}</code></td>
                  <td>{{$b->bill_description}}</td>
                  <td>{{number_format($b->amount)}}</td>
                  <td><span class="label label-{{$b->status=='Paid'?'success':($b->status=='Pending'?'info':'warning')}}">{{$b->status}}</span></td>
                  <td>{{$b->expires_at ? $b->expires_at->format('d/m/Y') : '-'}}</td>
                </tr>
                @empty
                <tr><td colspan="5">No bills yet.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
      @endif
    </div>
  </div>
</div>
@endsection
