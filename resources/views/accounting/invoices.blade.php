@extends('layouts.master')
@section('title', 'Fee Invoices')
@section('content')
<div class="right_col" role="main">
  <div class="">
    <div class="row">
      <div class="col-md-12">
        <div class="x_panel">
          <div class="x_title"><h2><i class="fa fa-file-text"></i> Fee Invoices</h2><a href="{{URL::route('accounting.invoice.create')}}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> New Invoice</a><div class="clearfix"></div></div>
          <div class="x_content">
            <table class="table table-striped table-bordered">
              <thead><tr><th>Invoice</th><th>Student</th><th>Date</th><th>Due</th><th>Total</th><th>Paid</th><th>Status</th></tr></thead>
              <tbody>
                @forelse($invoices as $i)
                <tr>
                  <td>{{$i->invoice_no}}</td><td>{{$i->student->idNo ?? '-'}} - {{$i->student->firstName ?? ''}} {{$i->student->lastName ?? ''}}</td>
                  <td>{{$i->invoice_date->format('d/m/Y')}}</td><td>{{$i->due_date->format('d/m/Y')}}</td>
                  <td class="text-right">{{number_format($i->total_amount, 2)}}</td>
                  <td class="text-right">{{number_format($i->paid_amount, 2)}}</td>
                  <td><span class="label label-{{$i->status=='Paid'?'success':($i->status=='Pending'?'warning':($i->status=='Overdue'?'danger':'info'))}}">{{$i->status}}</span></td>
                </tr>
                @empty <tr><td colspan="7">No invoices.</td></tr> @endforelse
              </tbody>
            </table>
            <div class="text-center">{!! $invoices->render() !!}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
