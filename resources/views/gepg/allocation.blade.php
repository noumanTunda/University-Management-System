@extends('layouts.master')
@section('title', 'Allocate Fees')
@section('extrastyle')
<link href="{{ URL::asset('assets/css/select2.min.css')}}" rel="stylesheet">
@endsection
@section('content')
<div class="right_col" role="main">
  <div class="">
    <div class="row">
      <div class="col-md-12">
        <div class="x_panel">
          <div class="x_title"><h2><i class="fa fa-money"></i> Fee Allocation — Generate Control Numbers</h2><div class="clearfix"></div></div>
          <div class="x_content">
            <div class="row">
              <!-- Bulk allocation by course -->
              <div class="col-md-6">
                <div class="panel panel-primary">
                  <div class="panel-heading"><strong>Bulk Allocation by Course</strong></div>
                  <div class="panel-body">
                    <form method="post" action="{{URL::route('gepg.allocate.bulk')}}">
                      <input type="hidden" name="_token" value="{{ csrf_token() }}">
                      <div class="form-group">
                        <label>Academic Year</label>
                        <select class="form-control" name="academic_year_id" required>
                          <option value="">Select Year</option>
                          @foreach($years as $y)
                            <option value="{{$y->id}}">{{$y->name}}</option>
                          @endforeach
                        </select>
                      </div>
                      <div class="form-group">
                        <label>Course</label>
                        <select class="form-control" id="courseSelect" required>
                          <option value="">Select Course</option>
                          @foreach($courses as $c)
                            <option value="{{$c->id}}">{{$c->name}} ({{$c->department->name ?? ''}})</option>
                          @endforeach
                        </select>
                      </div>
                      <div class="form-group">
                        <label>Students</label>
                        <div class="checkbox" style="margin-bottom:5px">
                          <label><input type="checkbox" id="selectAllStudents"> <strong>Select All</strong></label>
                        </div>
                        <select class="form-control" id="studentSelect" name="student_ids[]" multiple required style="width:100%">
                        </select>
                      </div>
                      <div class="form-group">
                        <label>Fee Type</label>
                        <select class="form-control" name="fee_id" required>
                          <option value="">Select Fee</option>
                          @foreach($fees as $f)
                            <option value="{{$f->id}}">{{$f->title}} — TZS {{number_format($f->amount)}}</option>
                          @endforeach
                        </select>
                      </div>
                      <button type="submit" class="btn btn-primary"><i class="fa fa-barcode"></i> Generate Control Numbers</button>
                    </form>
                  </div>
                </div>
              </div>

              <!-- Specific fee (penalties, permissions) -->
              <div class="col-md-6">
                <div class="panel panel-warning">
                  <div class="panel-heading"><strong>Specific Fee (Penalties, Permissions, etc.)</strong></div>
                  <div class="panel-body">
                    <form method="post" action="{{URL::route('gepg.allocate.specific')}}">
                      <input type="hidden" name="_token" value="{{ csrf_token() }}">
                      <div class="form-group">
                        <label>Student ID</label>
                        <input type="text" class="form-control" id="studentIdLookup" placeholder="Type student ID No">
                        <select class="form-control" name="student_id" id="specificStudentSelect" required style="margin-top:5px">
                          <option value="">Search student above first</option>
                        </select>
                      </div>
                      <div class="form-group">
                        <label>Description</label>
                        <input type="text" name="description" class="form-control" placeholder="e.g. Late registration penalty" required>
                      </div>
                      <div class="form-group">
                        <label>Amount (TZS)</label>
                        <input type="number" name="amount" class="form-control" required min="1" step="0.01">
                      </div>
                      <button type="submit" class="btn btn-warning"><i class="fa fa-barcode"></i> Generate Control Number</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
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
  // Bulk: load students when course changes
  $('#courseSelect').on('change', function() {
    var courseId = $(this).val();
    if (courseId) {
      $.get('/gepg/students/' + courseId, function(res) {
        var sel = $('#studentSelect').empty();
        $.each(res.students, function(i, s) {
          sel.append('<option value="'+s.id+'">'+s.idNo+' — '+s.firstName+' '+s.lastName+'</option>');
        });
      });
    }
  });

  // Specific: search student by ID No
  var allStudents = [];
  $.get('/gepg/students/all', function(res) { allStudents = res.students; });

  $('#studentIdLookup').on('keyup', function() {
    var q = $(this).val().toLowerCase();
    var sel = $('#specificStudentSelect').empty().append('<option value="">Select</option>');
    $.each(allStudents, function(i, s) {
      if (s.idNo.toLowerCase().indexOf(q) > -1 || s.firstName.toLowerCase().indexOf(q) > -1 || s.lastName.toLowerCase().indexOf(q) > -1) {
        sel.append('<option value="'+s.id+'">'+s.idNo+' — '+s.firstName+' '+s.lastName+'</option>');
      }
    });
  });
});
</script>
@endsection
