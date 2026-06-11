@extends('layouts.master')
@section('title', 'New Template')
@section('content')
<div class="right_col" role="main">
  <div class="">
    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        <div class="x_panel">
          <div class="x_title"><h2>New Assessment Template</h2><div class="clearfix"></div></div>
          <div class="x_content">
            <form method="post" action="{{URL::route('assessment.template.store')}}">
              <input type="hidden" name="_token" value="{{csrf_token()}}">
              <div class="form-group"><label>Template Name *</label><input name="name" class="form-control" required></div>
              <div class="form-group"><label>Description</label><input name="description" class="form-control"></div>
              <div class="row">
                <div class="col-md-6"><div class="form-group"><label>CA Weight (%) *</label><input name="ca_weight" class="form-control" value="40" required step="0.01"></div></div>
                <div class="col-md-6"><div class="form-group"><label>UE Weight (%) *</label><input name="ue_weight" class="form-control" value="60" required step="0.01"></div></div>
              </div>
              <hr><h4>Components</h4>
              <table class="table table-bordered" id="compTable">
                <thead><tr><th>Name</th><th>Type</th><th>Max Score</th><th>Weight (%)</th><th></th></tr></thead>
                <tbody id="compBody">
                  <tr>
                    <td><input name="components[0][name]" class="form-control" value="Test 1" required></td>
                    <td><select name="components[0][type]" class="form-control"><option value="CA" selected>CA</option><option value="UE">UE</option></select></td>
                    <td><input name="components[0][max_score]" class="form-control" value="20" required step="0.01"></td>
                    <td><input name="components[0][weight]" class="form-control" value="20" required step="0.01"></td>
                    <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">×</button></td>
                  </tr>
                </tbody>
              </table>
              <button type="button" class="btn btn-default btn-sm" id="addRow"><i class="fa fa-plus"></i> Add Component</button>
              <hr>
              <button type="submit" class="btn btn-success pull-right"><i class="fa fa-save"></i> Save Template</button>
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
var compIdx = 1;
$('#addRow').on('click', function() {
  var html = '<tr>';
  html += '<td><input name="components['+compIdx+'][name]" class="form-control" required></td>';
  html += '<td><select name="components['+compIdx+'][type]" class="form-control"><option value="CA">CA</option><option value="UE">UE</option></select></td>';
  html += '<td><input name="components['+compIdx+'][max_score]" class="form-control" required step="0.01"></td>';
  html += '<td><input name="components['+compIdx+'][weight]" class="form-control" required step="0.01"></td>';
  html += '<td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest(\'tr\').remove()">×</button></td>';
  html += '</tr>';
  $('#compBody').append(html);
  compIdx++;
});
</script>
@endsection
