@extends('layouts.master')
@section('title', 'Trial Balance')
@section('content')
<div class="right_col" role="main">
  <div class="">
    <div class="row">
      <div class="col-md-12">
        <div class="x_panel">
          <div class="x_title"><h2><i class="fa fa-balance-scale"></i> Trial Balance</h2><div class="clearfix"></div></div>
          <div class="x_content">
            <table class="table table-striped table-bordered">
              <thead><tr><th>Code</th><th>Account</th><th>Type</th><th>Debit (TZS)</th><th>Credit (TZS)</th></tr></thead>
              <tbody>
                @foreach($accounts as $a)
                <tr>
                  <td>{{$a->code}}</td><td>{{$a->name}}</td><td>{{$a->type}}</td>
                  <td class="text-right">{{$a->balance > 0 ? number_format($a->balance, 2) : ''}}</td>
                  <td class="text-right">{{$a->balance < 0 ? number_format(abs($a->balance), 2) : ''}}</td>
                </tr>
                @endforeach
              </tbody>
              <tfoot>
                <tr class="success"><th colspan="3">Total</th><th class="text-right">{{number_format($totalDebit, 2)}}</th><th class="text-right">{{number_format($totalCredit, 2)}}</th></tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
