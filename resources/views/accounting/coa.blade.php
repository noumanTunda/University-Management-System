@extends('layouts.master')
@section('title', 'Chart of Accounts')
@section('content')
<div class="right_col" role="main">
  <div class="">
    <div class="row">
      <div class="col-md-12">
        <div class="x_panel">
          <div class="x_title"><h2><i class="fa fa-book"></i> Chart of Accounts</h2><button class="btn btn-primary pull-right" data-toggle="modal" data-target="#accountModal"><i class="fa fa-plus"></i> New Account</button><div class="clearfix"></div></div>
          <div class="x_content">
            @foreach($groups as $group)
              <h4>{{$group}}s</h4>
              <table class="table table-striped table-bordered">
                <thead><tr><th>Code</th><th>Name</th><th>Balance (TZS)</th></tr></thead>
                <tbody>
                  @foreach($accounts->where('type', $group) as $a)
                  <tr><td>{{$a->code}}</td><td>{{$a->name}}</td><td class="text-right">{{number_format($a->balance, 2)}}</td></tr>
                  @endforeach
                </tbody>
              </table>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="accountModal"><div class="modal-dialog"><div class="modal-content">
  <form method="post" action="{{URL::route('accounting.coa.store')}}">
    <input type="hidden" name="_token" value="{{csrf_token()}}">
    <div class="modal-header"><h4>New Account</h4></div>
    <div class="modal-body">
      <div class="form-group"><label>Code</label><input name="code" class="form-control" required></div>
      <div class="form-group"><label>Name</label><input name="name" class="form-control" required></div>
      <div class="form-group"><label>Type</label><select name="type" class="form-control"><option value="Asset">Asset</option><option value="Liability">Liability</option><option value="Income">Income</option><option value="Expense">Expense</option></select></div>
      <div class="form-group"><label>Description</label><textarea name="description" class="form-control"></textarea></div>
    </div>
    <div class="modal-footer"><button type="submit" class="btn btn-success">Save</button></div>
  </form>
</div></div></div>
@endsection
