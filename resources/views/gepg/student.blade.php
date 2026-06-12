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
          <div class="x_title"><h2>Request Control Number</h2><div class="clearfix"></div></div>
          <div class="x_content">
            @if($fees->count() > 0)
            <p class="text-muted">Select a fee type below. If you don't already have a control number for it this year, one will be generated immediately.</p>
            <form method="post" action="{{URL::route('gepg.request')}}">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <div class="form-group">
                <label>Fee Type</label>
                <select class="form-control" name="fee_id" required>
                  <option value="">Select Fee Type</option>
                  @foreach($fees as $f)
                    <option value="{{$f->id}}">{{$f->title}} — TZS {{number_format($f->amount)}}</option>
                  @endforeach
                </select>
              </div>
              <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-barcode"></i> Generate Control Number</button>
            </form>
            @else
              <div class="alert alert-info">No fee types are currently available for your department.</div>
            @endif
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
