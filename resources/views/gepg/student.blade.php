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
              <thead><tr><th>Control No</th><th>Fee</th><th>Amount</th><th>Paid</th><th>Due</th><th>Status</th><th></th></tr></thead>
              <tbody>
                @forelse($bills as $b)
                @php $due = $b->amount - $b->paid_amount; @endphp
                <tr>
                  <td><code>{{$b->control_number}}</code></td>
                  <td>{{$b->bill_description}}</td>
                  <td class="text-right">{{number_format($b->amount, 2)}}</td>
                  <td class="text-right">{{number_format($b->paid_amount, 2)}}</td>
                  <td class="text-right {{$due > 0 ? 'text-danger' : ''}}"><strong>{{number_format($due, 2)}}</strong></td>
                  <td><span class="label label-{{$b->status=='Paid'?'success':($b->status=='Partial'?'warning':($b->status=='Pending'?'info':'default'))}}">{{$b->status}}</span></td>
                  <td>
                    @if($due > 0 && $b->status != 'Pending')
                      <a href="{{URL::route('gepg.pay.form', $b->id)}}" class="btn btn-success btn-xs"><i class="fa fa-money"></i> Pay</a>
                    @endif
                  </td>
                </tr>
                @empty
                <tr><td colspan="7">No bills yet.</td></tr>
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
