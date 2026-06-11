@extends('layouts.master')
@section('title', 'Bulk Upload Marks')
@section('extrastyle')
<link href="{{ URL::asset('assets/css/select2.min.css')}}" rel="stylesheet">
@endsection

@section('content')
<div class="right_col" role="main">
  <div class="">
    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Bulk Upload Marks <small>Upload CSV file</small></h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <p class="text-muted">CSV columns: <strong>idNo, ca_score, ue_score</strong>. CA (max 40), UE (max 60).</p>

            <form class="form-horizontal form-label-left" method="POST" enctype="multipart/form-data" action="{{ route('exam.marks.upload.store') }}">
              {{ csrf_field() }}

              <div class="item form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="department_id">Department <span class="required">*</span></label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <select class="form-control" name="department_id" id="deptSelect" required>
                    <option value="">Select Department</option>
                    @foreach(\App\Department::orderBy('name')->lists('name', 'id') as $id => $name)
                      <option value="{{$id}}">{{$name}}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="item form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="academic_year">Academic Year <span class="required">*</span></label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <select class="form-control" name="academic_year_id" id="yearSelect" required>
                    <option value="">Select Year</option>
                    @foreach(\App\AcademicYear::orderBy('name', 'desc')->lists('name', 'id') as $id => $name)
                      <option value="{{$id}}">{{$name}}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="item form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="semester_id">Semester <span class="required">*</span></label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <select class="form-control" name="semester_id" id="semSelect" required disabled>
                    <option value="">Select Academic Year first</option>
                  </select>
                </div>
              </div>

              <div class="item form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="subject_id">Subject <span class="required">*</span></label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <select class="form-control" name="subject_id" id="subjSelect" required disabled>
                    <option value="">Select Department first</option>
                  </select>
                </div>
              </div>

              <div class="item form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="marks_file">CSV File <span class="required">*</span></label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <input type="file" name="marks_file" id="marks_file" required class="form-control" accept=".csv,.txt" />
                  <span class="text-danger">{{ $errors->first('marks_file') }}</span>
                </div>
              </div>

              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-6 col-md-offset-3">
                  <button type="submit" class="btn btn-success"><i class="fa fa-upload"></i> Upload</button>
                  <a href="{{ route('exam.marks.template') }}" class="btn btn-primary"><i class="fa fa-download"></i> Download Template</a>
                </div>
              </div>
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
  $('#deptSelect, #yearSelect').select2({ width: '100%' });

  $('#yearSelect').on('change', function() {
    var yearId = $(this).val();
    if (yearId) {
      $.get('/exam-marks/semesters/' + yearId, function(res) {
        var sel = $('#semSelect').prop('disabled', false).empty().append('<option value="">Select Semester</option>');
        if (res.success) {
          $.each(res.semesters, function(i, s) { sel.append('<option value="'+s.id+'">Semester '+s.semester_number+'</option>'); });
        }
      });
    } else {
      $('#semSelect').prop('disabled', true).empty().append('<option value="">Select Academic Year first</option>');
    }
  });

  $('#deptSelect').on('change', function() {
    var deptId = $(this).val();
    if (deptId) {
      $.get('/exam-marks/subjects/' + deptId, function(res) {
        var sel = $('#subjSelect').prop('disabled', false).empty().append('<option value="">Select Subject</option>');
        if (res.success) {
          $.each(res.subjects, function(i, s) { sel.append('<option value="'+s.id+'">'+s.name+' ('+s.code+')</option>'); });
        }
      });
    } else {
      $('#subjSelect').prop('disabled', true).empty().append('<option value="">Select Department first</option>');
    }
  });
});
</script>
@endsection
