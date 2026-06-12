@extends('layouts.master')
@section('title', 'Pay Bill')

@section('content')
<div class="right_col" role="main">
  <div class="">
    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-6 col-md-offset-3">
        <div class="x_panel">
          <div class="x_title"><h2><i class="fa fa-money"></i> Simulate GePG Payment</h2><div class="clearfix"></div></div>
          <div class="x_content">
            <div class="well">
              <strong>Control Number:</strong> <code>{{$bill->control_number}}</code><br>
              <strong>Description:</strong> {{$bill->bill_description}}<br>
              <strong>Total Amount:</strong> TZS {{number_format($bill->amount, 2)}}<br>
              <strong>Already Paid:</strong> TZS {{number_format($bill->paid_amount, 2)}}<br>
              <strong>Due Amount:</strong> <span class="text-danger">TZS {{number_format($dueAmount, 2)}}</span><br>
              <strong>Status:</strong> <span class="label label-{{$bill->status=='Paid'?'success':($bill->status=='Partial'?'warning':'info')}}">{{$bill->status}}</span>
            </div>

            @if($dueAmount > 0)
            <form method="post" action="{{URL::route('gepg.pay.store', $bill->id)}}">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">

              <div class="form-group">
                <label>Payment Method</label>
                <select class="form-control" disabled>
                  <option>Simulated GePG</option>
                </select>
              </div>

              <div class="form-group">
                <label>Amount to Pay (TZS)</label>
                <input type="number" name="amount" id="payAmount" class="form-control" 
                       value="{{$dueAmount}}" min="1" max="{{$dueAmount}}" step="0.01" required>
              </div>

              <div class="form-group">
                <label>Phone Number (for receipt)</label>
                <input type="text" name="payer_mobile" class="form-control" placeholder="e.g. 0712345678" required>
              </div>

              <div class="form-group">
                <label>Confirm Amount</label>
                <input type="number" id="confirmAmount" class="form-control" 
                       placeholder="Re-enter the amount to confirm">
                <span id="amountMatch" style="display:none;color:green"><i class="fa fa-check"></i> Amounts match</span>
                <span id="amountMismatch" style="display:none;color:red"><i class="fa fa-times"></i> Amounts do not match</span>
              </div>

              <button type="submit" id="payBtn" class="btn btn-success btn-lg btn-block" disabled>
                <i class="fa fa-check-circle"></i> Confirm Payment
              </button>
            </form>
            @else
              <div class="alert alert-success">This bill is fully paid.</div>
            @endif

            <a href="{{URL::route('gepg.student')}}" class="btn btn-default pull-right" style="margin-top:10px">Back to Bills</a>
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
  $('#confirmAmount').on('keyup', function() {
    var payAmt = parseFloat($('#payAmount').val());
    var confAmt = parseFloat($(this).val());
    if (confAmt === payAmt && confAmt > 0) {
      $('#amountMatch').show();
      $('#amountMismatch').hide();
      $('#payBtn').prop('disabled', false);
    } else {
      $('#amountMatch').hide();
      $('#amountMismatch').show();
      $('#payBtn').prop('disabled', true);
    }
  });
});
</script>
@endsection
