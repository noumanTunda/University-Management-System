@extends('layouts.master')
@section('title', 'GePG Bills')
@section('extrastyle')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css">
<style>
td { vertical-align: middle !important; }
.dataTables_filter input { height: 34px; border-radius: 4px; border: 1px solid #ccc; padding: 6px 12px; }
.dataTables_length select { height: 34px; border-radius: 4px; border: 1px solid #ccc; padding: 6px 12px; }
</style>
@endsection
@section('content')
<div class="right_col" role="main">
  <div class="">
    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12">
        <div class="x_panel">
          <div class="x_title"><h2><i class="fa fa-barcode"></i> All Bills</h2><div class="clearfix"></div></div>
          <div class="x_content">
            <form method="get" action="{{URL::route('gepg.accountant')}}" class="form-inline" style="margin-bottom:15px">
              <div class="form-group">
                <label>Academic Year: &nbsp;</label>
                <select name="academic_year" class="form-control" onchange="this.form.submit()" style="min-width:200px">
                  <option value="">All Years</option>
                  @foreach($years as $y)
                    <option value="{{$y->name}}" {{$selectedYear == $y->name ? 'selected' : ''}}>{{$y->name}}</option>
                  @endforeach
                </select>
              </div>
              @if($selectedYear)
                <a href="{{URL::route('gepg.accountant')}}" class="btn btn-default btn-sm">Clear Filter</a>
              @endif
              <span class="pull-right text-muted" style="line-height:34px">
                @if($selectedYear)
                  Showing bills for <strong>{{$selectedYear}}</strong>
                @else
                  Showing bills for <strong>all academic years</strong>
                @endif
                &nbsp;|&nbsp; Total: <strong>{{$bills->count()}}</strong> bills
              </span>
            </form>
            <table id="billsTable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Student</th>
                  <th>Control No</th>
                  <th>Fee</th>
                  <th>Amount</th>
                  <th>Paid</th>
                  <th>Due</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
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
                    <a href="{{URL::route('gepg.bill.delete', $b->id)}}" class="btn btn-danger btn-xs" onclick="return confirm('Delete this bill? This cannot be undone.')"><i class="fa fa-trash"></i></a>
                    @endif
                  </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center">No bills found.</td></tr>
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
@section('extrascript')
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js"></script>
<script>
$(document).ready(function() {
  $('#billsTable').DataTable({
    order: [[0, 'asc']],
    pageLength: 25,
    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
    language: {
      search: 'Search bills:',
      lengthMenu: 'Show _MENU_ entries',
      info: 'Showing _START_ to _END_ of _TOTAL_ bills',
      emptyTable: 'No bills available'
    }
  });
});
</script>
@endsection
