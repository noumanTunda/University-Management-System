@extends('layouts.master')
@section('title', 'Penalties & Special Fees')
@section('extrascript')
<link href="{{ URL::asset('assets/css/select2.min.css')}}" rel="stylesheet">
@endsection
@section('content')
<div class="right_col" role="main">
  <div class="">
    <div class="row">
      <div class="col-md-6 col-md-offset-3">
        <div class="x_panel">
          <div class="x_title">
            <h2><i class="fa fa-exclamation-triangle"></i> Penalties & Special Fees</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <form method="post" action="{{URL::route('gepg.allocate.specific')}}">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">

              <div class="form-group">
                <label>Academic Year <span class="required">*</span></label>
                <select class="form-control no-select2" name="academic_year" required>
                  <option value="">Select Year</option>
                  @foreach($years as $y)
                    <option value="{{$y->name}}">{{$y->name}}</option>
                  @endforeach
                </select>
              </div>

              <div class="form-group">
                <label>Student</label>
                <select class="form-control select2" name="student_id" id="studentSelect" required style="width:100%">
                  <option value="">Search and select student</option>
                </select>
              </div>

              <div class="form-group">
                <label>Description</label>
                <input type="text" name="description" class="form-control" placeholder="e.g. Late registration, Library fine, Lab breakage" required>
              </div>

              <div class="form-group">
                <label>Amount (TZS)</label>
                <input type="number" name="amount" class="form-control" required min="1" step="0.01" placeholder="0.00">
              </div>

              <hr>
              <button type="submit" class="btn btn-warning btn-lg btn-block">
                <i class="fa fa-barcode"></i> Generate Control Number
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('extrascript')
<script src="{{ URL::asset('assets/js/select2.full.min.js')}}"></script>
<script>
$(document).ready(function() {
  $('#studentSelect').select2({ placeholder: 'Search student by ID or name', allowClear: true });
  $.get('/gepg/students/all', function(res) {
    var sel = $('#studentSelect');
    $.each(res.students, function(i, s) {
      sel.append('<option value="'+s.id+'">'+s.idNo+' — '+s.firstName+' '+s.lastName+'</option>');
    });
  });
});
</script>
@endsection
