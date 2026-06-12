@extends('layouts.master')

@section('title', 'GePG Bills')

@section('content')
<div class="right_col" role="main">
  <div class="">
    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12">
        <div class="x_panel">
          <div class="x_title"><h2>All Bills</h2><div class="clearfix"></div></div>
          <div class="x_content">
            <table class="table table-bordered table-striped">
              <thead><tr><th>Student</th><th>Control No</th><th>Fee</th><th>Amount</th><th>Paid</th><th>Due</th><th>Status</th><th>Actions</th></tr></thead>
              <tbody>
                @forelse($bills as $b)
                @php $due = $b->amount - $b->paid_amount; @endphp
                <tr>
                  <td>{{$b->student->firstName ?? '-'}} {{$b->student->lastName ?? ''}}</td>
                  <td><code>{{$b->control_number}}</code></td>
                  <td>{{$b->bill_description}}</td>
                  <td class="text-right">{{number_format($b->amount, 2)}}</td>
                  <td class="text-right">{{number_format($b->paid_amount, 2)}}</td>
                  <td class="text-right {{$due > 0 ? 'text-danger' : ''}}"><strong>{{number_format($due, 2)}}</strong></td>
                  <td><span class="label label-{{$b->status=='Paid'?'success':($b->status=='Partial'?'warning':($b->status=='Expired'?'danger':'info'))}}">{{$b->status}}</span></td>
                  <td>
                    <a href="{{URL::route('gepg.bill.edit', $b->id)}}" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i></a>
                    @if($b->status != 'Paid')
                    <form method="POST" action="{{URL::route('gepg.bill.paid', $b->id)}}" style="display:inline">
                      <input type="hidden" name="_token" value="{{ csrf_token() }}">
                      <button class="btn btn-success btn-xs" onclick="return confirm('Mark as paid?')"><i class="fa fa-check"></i></button>
                    </form>
                    @endif
                  </td>
                </tr>
                @empty
                <tr><td colspan="6">No bills.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
