@extends('layouts.master')
@section('title', 'Fee Allocation')
@section('extrastyle')
<link href="{{ URL::asset('assets/css/select2.min.css')}}" rel="stylesheet">
<style>
.fee-item-row { display:flex; align-items:center; padding:8px 10px; border:1px solid #e5e5e5; margin-bottom:6px; background:#fff; border-radius:4px; }
.fee-item-row .fee-name { flex:1; font-weight:600; }
.fee-item-row .fee-amount { width:120px; text-align:right; font-weight:600; margin-right:10px; }
.fee-item-row .btn-remove { color:#d9534f; cursor:pointer; font-size:18px; line-height:1; }
#feeListBody { min-height:60px; }
</style>
@endsection
@section('content')
<div class="right_col" role="main">
  <div class="">
    <div class="row">
      <div class="col-md-12">
        <div class="x_panel">
          <div class="x_title">
            <h2><i class="fa fa-money"></i> Fee Allocation</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <form method="post" action="{{URL::route('gepg.allocate.bulk')}}" id="allocationForm">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <input type="hidden" name="fee_data" id="feeDataInput" value="">

              <div class="row">
                <!-- Left: Selectors -->
                <div class="col-md-7">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Academic Year <span class="required">*</span></label>
                        <select class="form-control" name="academic_year_id" id="yearSelect" required>
                          <option value="">Select Year</option>
                          @foreach($years as $y)
                            <option value="{{$y->id}}">{{$y->name}}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Course <span class="required">*</span></label>
                        <select class="form-control" id="courseSelect" required>
                          <option value="">Select Course</option>
                          <option value="all">All Courses</option>
                          @foreach($courses as $c)
                            <option value="{{$c->id}}">{{$c->name}} ({{$c->department->name ?? ''}})</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label>Students <span class="required">*</span></label>
                    <div style="margin-bottom:5px">
                      <button type="button" id="btnSelectAll" class="btn btn-sm btn-primary"><i class="glyphicon glyphicon-check"></i> Select All</button>
                      <button type="button" id="btnClearAll" class="btn btn-sm btn-default"><i class="glyphicon glyphicon-remove"></i> Clear</button>
                      <span id="studentCount" class="text-muted" style="margin-left:10px"></span>
                    </div>
                    <select class="form-control" id="studentSelect" name="student_ids[]" multiple required style="width:100%;height:200px">
                    </select>
                  </div>
                </div>

                <!-- Right: Fee selector + list -->
                <div class="col-md-5">
                  <div class="form-group">
                    <label>Fee Type</label>
                    <div class="input-group">
                      <select class="form-control" id="feeSelect">
                        <option value="">Select Fee Type</option>
                        @foreach($fees as $f)
                          <option value="{{$f->id}}" data-amount="{{$f->amount}}" data-title="{{$f->title}}">{{$f->title}} — TZS {{number_format($f->amount)}}</option>
                        @endforeach
                      </select>
                      <span class="input-group-btn">
                        <button type="button" class="btn btn-primary" id="btnAddFee"><i class="glyphicon glyphicon-plus"></i> Add</button>
                      </span>
                    </div>
                  </div>

                  <div class="panel panel-default">
                    <div class="panel-heading"><strong>Fee Items</strong> <span class="badge pull-right" id="feeCount">0</span></div>
                    <div class="panel-body" id="feeListBody" style="padding:8px;max-height:300px;overflow-y:auto">
                      <div class="text-muted text-center" style="padding:20px">No fees added yet. Select a fee type and click "Add".</div>
                    </div>
                    <div class="panel-footer">
                      <div class="row">
                        <div class="col-xs-6"><strong>Total per student:</strong></div>
                        <div class="col-xs-6 text-right"><strong id="totalAmount">TZS 0.00</strong></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <hr>
              <div class="row">
                <div class="col-md-12">
                  <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    <span id="summaryText">Select academic year, course, students, and add fee types to begin.</span>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12 text-right">
                  <button type="submit" class="btn btn-lg btn-success" id="btnGenerate" disabled>
                    <i class="fa fa-barcode"></i> Generate Control Numbers
                  </button>
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
  var feeItems = [];
  var feeIdx = 0;

  // ─── Load students when course + year selected ───
  function loadStudents() {
    var courseId = $('#courseSelect').val();
    var yearId = $('#yearSelect').val();
    if (courseId && yearId) {
      $.get('/gepg/students/' + courseId + '/' + yearId, function(res) {
        var sel = $('#studentSelect').empty();
        if (res.success && res.students.length > 0) {
          $.each(res.students, function(i, s) {
            sel.append('<option value="'+s.id+'">'+s.idNo+' — '+s.firstName+' '+s.lastName+'</option>');
          });
          $('#studentCount').text(res.students.length + ' students');
        } else {
          $('#studentCount').text('No registered students found');
        }
        updateSummary();
      });
    } else {
      $('#studentSelect').empty();
      $('#studentCount').text('');
      updateSummary();
    }
  }

  // ─── Load fees by course department ───
  function loadFees() {
    var courseId = $('#courseSelect').val();
    if (courseId) {
      $.get('/gepg/fees-course/' + courseId, function(res) {
        var sel = $('#feeSelect').empty().append('<option value="">Select Fee Type</option>');
        if (res.success) {
          $.each(res.fees, function(i, f) {
            sel.append('<option value="'+f.id+'" data-amount="'+f.amount+'" data-title="'+f.title+'">'+f.title+' — TZS '+f.amount.toLocaleString()+'</option>');
          });
        }
      });
    }
  }

  $('#yearSelect').on('change', loadStudents);
  $('#courseSelect').on('change', function() { loadStudents(); loadFees(); });

  // ─── Student select / deselect all ───
  $('#btnSelectAll').on('click', function() {
    $('#studentSelect option').prop('selected', true);
    updateSummary();
  });
  $('#btnClearAll').on('click', function() {
    $('#studentSelect option').prop('selected', false);
    updateSummary();
  });
  $('#studentSelect').on('change', updateSummary);

  // ─── Fee item: add ───
  $('#btnAddFee').on('click', function() {
    var sel = $('#feeSelect option:selected');
    if (!sel.val()) { alert('Please select a fee type.'); return; }
    var id = sel.val();
    var title = sel.data('title');
    var amount = parseFloat(sel.data('amount'));
    // Check duplicate
    for (var i = 0; i < feeItems.length; i++) {
      if (feeItems[i].id == id) { alert('This fee type is already added.'); return; }
    }
    feeItems.push({ id: id, title: title, amount: amount });
    renderFeeList();
    $('#feeSelect').val('');
  });

  // ─── Fee item: remove ───
  function removeFee(idx) {
    feeItems.splice(idx, 1);
    renderFeeList();
  }

  // ─── Render fee list ───
  function renderFeeList() {
    var body = $('#feeListBody');
    body.empty();
    if (feeItems.length === 0) {
      body.html('<div class="text-muted text-center" style="padding:20px">No fees added yet.</div>');
      $('#feeCount').text('0');
      $('#totalAmount').text('TZS 0.00');
      updateSummary();
      return;
    }
    var total = 0;
    $.each(feeItems, function(i, f) {
      total += f.amount;
      body.append(
        '<div class="fee-item-row">' +
        '<span class="fee-name">' + f.title + '</span>' +
        '<span class="fee-amount">TZS ' + f.amount.toLocaleString() + '</span>' +
        '<span class="btn-remove" onclick="removeFee(' + i + ')">&times;</span>' +
        '</div>'
      );
    });
    $('#feeCount').text(feeItems.length);
    $('#totalAmount').text('TZS ' + total.toLocaleString());
    updateSummary();
  }

  // ─── Update summary ───
  function updateSummary() {
    var students = $('#studentSelect option:selected').length;
    var totalStudents = $('#studentSelect option').length;
    var fees = feeItems.length;
    var total = 0;
    $.each(feeItems, function(i, f) { total += f.amount; });
    var btn = $('#btnGenerate');
    var summary = $('#summaryText');

    if (!totalStudents) {
      summary.html('Select academic year and course to load students.');
      btn.prop('disabled', true);
    } else if (!students) {
      summary.html(totalStudents + ' students available. Select students to allocate fees to.');
      btn.prop('disabled', true);
    } else if (!fees) {
      summary.html(students + ' student(s) selected. Add fee types to allocate.');
      btn.prop('disabled', true);
    } else {
      summary.html('<strong>' + students + '</strong> student(s) × <strong>' + fees + '</strong> fee type(s) = <strong>' + (students * fees) + '</strong> control numbers. Total per student: <strong>TZS ' + total.toLocaleString() + '</strong>');
      btn.prop('disabled', false);
    }
  }

  // ─── Form submit: serialize fee data ───
  $('#allocationForm').on('submit', function(e) {
    if (feeItems.length === 0) { alert('Add at least one fee type.'); e.preventDefault(); return; }
    if ($('#studentSelect option:selected').length === 0) { alert('Select at least one student.'); e.preventDefault(); return; }
    // Build hidden inputs for fee items
    $.each(feeItems, function(i, f) {
      $('<input>').attr({ type: 'hidden', name: 'fees['+i+'][id]', value: f.id }).appendTo('#allocationForm');
      $('<input>').attr({ type: 'hidden', name: 'fees['+i+'][title]', value: f.title }).appendTo('#allocationForm');
      $('<input>').attr({ type: 'hidden', name: 'fees['+i+'][amount]', value: f.amount }).appendTo('#allocationForm');
    });
    return true;
  });

  // Expose removeFee globally
  window.removeFee = removeFee;
});
</script>
@endsection
