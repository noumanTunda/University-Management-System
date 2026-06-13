@extends('layouts.master')

@section('title', 'Exam Marks Entry')

@section('content')
<div class="right_col" role="main">
  <div class="">
    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12">
        <div class="x_panel">
          <div class="x_title">
            <h2><i class="fa fa-pencil"></i> Exam Marks Entry</h2>
            <a href="{{URL::route('exam.marks.upload.form')}}" class="btn btn-info btn-sm pull-right"><i class="fa fa-upload"></i> Bulk Upload CSV</a>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  <label>Department <span class="required">*</span></label>
                  <select class="form-control" id="deptSelect" required>
                    <option value="">Select Department</option>
                    @foreach($departments as $id => $name)
                      <option value="{{$id}}">{{$name}}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label>Academic Year <span class="required">*</span></label>
                  <select class="form-control" id="yearSelect" required>
                    <option value="">Select Year</option>
                    @foreach($years as $id => $name)
                      <option value="{{$id}}">{{$name}}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label>Semester <span class="required">*</span></label>
                  <select class="form-control" id="semSelect" required disabled>
                    <option value="">Select Year first</option>
                  </select>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label>Subject <span class="required">*</span></label>
                  <select class="form-control" id="subjSelect" required disabled>
                    <option value="">Select Dept first</option>
                  </select>
                </div>
              </div>
            </div>
            <hr>
            <div id="marksContainer">
              <div class="alert alert-info">Select department, year, semester, and subject. Marks load automatically.</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('extrascript')
<script>
$(document).ready(function() {
    $('#deptSelect').on('change', function() {
        var deptId = $(this).val();
        if (deptId) {
            $.get('/exam-marks/subjects/' + deptId, function(res) {
                if (res.success) {
                    var sel = $('#subjSelect').prop('disabled', false).empty().append('<option value="">Select Subject</option>');
                    $.each(res.subjects, function(i, s) { sel.append('<option value="'+s.id+'">'+s.name+' ('+s.code+')</option>'); });
                }
            });
        }
    });

    $('#yearSelect').on('change', function() {
        var yearId = $(this).val();
        if (yearId) {
            $.get('/exam-marks/semesters/' + yearId, function(res) {
                if (res.success) {
                    var sel = $('#semSelect').prop('disabled', false).empty().append('<option value="">Select Semester</option>');
                    $.each(res.semesters, function(i, s) { sel.append('<option value="'+s.id+'">Semester '+s.semester_number+'</option>'); });
                }
            });
        }
    });

    function loadMarks() {
        var subjectId = $('#subjSelect').val();
        var semId = $('#semSelect').val();
        if (!subjectId || !semId) {
            $('#marksContainer').html('<div class="alert alert-warning">Please select both a subject and a semester first.</div>');
            return;
        }
        $('#marksContainer').html('<div class="alert alert-info"><i class="fa fa-spinner fa-spin"></i> Fetching students and marks...</div>');
        $.get('/exam-marks/entry/' + subjectId + '/' + semId, function(html) {
            $('#marksContainer').html(html);
        }).fail(function(jqXHR) {
            var msg = 'Error loading data (HTTP ' + jqXHR.status + ')';
            if (jqXHR.responseText && jqXHR.responseText.length < 500) {
                msg += '<br><pre>' + jqXHR.responseText + '</pre>';
            }
            $('#marksContainer').html('<div class="alert alert-danger">' + msg + '</div>');
        });
    }

    $('#subjSelect, #semSelect').on('change', loadMarks);
});
</script>
@endsection
