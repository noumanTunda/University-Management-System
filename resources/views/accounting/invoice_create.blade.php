@extends('layouts.master')
@section('title', 'New Invoice')
@section('content')
<div class="right_col" role="main">
  <div class="">
    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        <div class="x_panel">
          <div class="x_title"><h2>New Fee Invoice</h2><div class="clearfix"></div></div>
          <div class="x_content">
            <form method="post" action="{{URL::route('accounting.invoice.store')}}">
              <input type="hidden" name="_token" value="{{csrf_token()}}">
              <div class="form-group"><label>Student</label>
                <select class="form-control" name="student_id" required>
                  <option value="">Select Student</option>
                  @foreach($students as $s)<option value="{{$s->id}}">{{$s->idNo}} - {{$s->firstName}} {{$s->lastName}}</option>@endforeach
                </select>
              </div>
              <div class="form-group"><label>Due Date</label><input type="date" name="due_date" class="form-control" required></div>
              <hr><h4>Invoice Items</h4>
              <table class="table table-bordered" id="itemsTable">
                <thead><tr><th>Description</th><th>Amount (TZS)</th><th>Account</th><th></th></tr></thead>
                <tbody id="itemsBody">
                  <tr>
                    <td><input name="items[0][description]" class="form-control" value="Tuition Fee" required></td>
                    <td><input name="items[0][amount]" class="form-control" value="" required step="0.01"></td>
                    <td><select name="items[0][account_id]" class="form-control">
                      @foreach($accounts as $a)<option value="{{$a->id}}">{{$a->code}} - {{$a->name}}</option>@endforeach
                    </select></td>
                    <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">&times;</button></td>
                  </tr>
                </tbody>
              </table>
              <button type="button" class="btn btn-default btn-sm" id="addItem"><i class="fa fa-plus"></i> Add Item</button>
              <hr>
              <button type="submit" class="btn btn-success pull-right"><i class="fa fa-save"></i> Create Invoice</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('extrascript')
<script>
var idx = 1;
$('#addItem').on('click', function() {
  var h = '<tr><td><input name="items['+idx+'][description]" class="form-control" required></td>';
  h += '<td><input name="items['+idx+'][amount]" class="form-control" required step="0.01"></td>';
  h += '<td><select name="items['+idx+'][account_id]" class="form-control">@foreach($accounts as $a)<option value="{{$a->id}}">{{$a->code}}</option>@endforeach</select></td>';
  h += '<td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest(\'tr\').remove()">&times;</button></td></tr>';
  $('#itemsBody').append(h); idx++;
});
</script>
@endsection
