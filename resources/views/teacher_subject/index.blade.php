@extends('layouts.master')
@section('title', 'Assign Subjects to Teachers')
@section('extrastyle')
<link href="{{ URL::asset('assets/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
<link href="{{ URL::asset('assets/css/select2.min.css')}}" rel="stylesheet">
<link href="{{ URL::asset('assets/css/sweetalert.css')}}" rel="stylesheet">
<style>
.select2-container { width: 100% !important; }
</style>
@endsection

@section('content')
<div class="right_col" role="main">
  <div class="">
    <div class="clearfix"></div>
    <!-- Assignment Form -->
    <div class="row">
      <div class="col-md-12">
        <div class="x_panel">
          <div class="x_title">
            <h2><i class="fa fa-plus-circle"></i> Assign Subjects</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <form method="post" action="{{URL::route('teacher.subject.store')}}">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <div class="row">
                <div class="col-md-5">
                  <div class="form-group">
                    <label>Academic Years <span class="required">*</span></label>
                    <div style="margin-bottom:5px">
                      <button type="button" id="btnSelectAllYears" class="btn btn-xs btn-info"><i class="fa fa-check-square-o"></i> All Years</button>
                      <button type="button" id="btnClearYears" class="btn btn-xs btn-default"><i class="fa fa-square-o"></i> Clear</button>
                    </div>
                    <select class="form-control select2" name="academic_year_ids[]" id="yearSelect" multiple required >
                      @foreach($academicYears as $y)
                        <option value="{{$y->id}}" @if($y->id == $currentYearId) selected @endif>{{$y->name}}</option>
                      @endforeach
                    </select>
                    <small class="text-muted">Ctrl+click to select multiple years, or use All Years button.</small>
                  </div>
                </div>
                <div class="col-md-7">
                  <div class="form-group">
                    <label>Subjects <span class="required">*</span></label>
                    <select class="form-control select2" name="subject_ids[]" id="subjectSelect" multiple required>
                      <option value="" disabled>Search and select subjects...</option>
                      @foreach($allSubjects as $s)
                        <option value="{{$s->id}}" data-dept="{{$s->department ? $s->department->name : ''}}">
                          {{$s->name}} ({{$s->code}}) - {{$s->department ? $s->department->name : ''}}
                        </option>
                      @endforeach
                    </select>
                    <small class="text-muted">Type to search subjects. Selected subjects appear as tags.</small>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-5">
                  <div class="form-group">
                    <label>Teacher <span class="required">*</span></label>
                    <select class="form-control select2" name="user_id" id="teacherSelect" required>
                      <option value="">Search Teacher...</option>
                      @foreach($teachers as $t)
                        <option value="{{$t->id}}">{{$t->firstname}} {{$t->lastname}} ({{$t->login}})</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="col-md-7" style="padding-top:25px">
                  <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-save"></i> Assign Subjects</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Current Assignments Table -->
    <div class="row">
      <div class="col-md-12">
        <div class="x_panel">
          <div class="x_title">
            <h2><i class="fa fa-list"></i> Current Assignments</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <table id="assignTable" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>Teacher</th>
                  <th>Subject</th>
                  <th>Code</th>
                  <th>Department</th>
                  <th>Academic Year</th>
                  <th width="100">Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($assignments as $a)
                <tr>
                  <td>{{$a->teacher_name}} {{$a->teacher_lastname}}</td>
                  <td>{{$a->subject_name}}</td>
                  <td>{{$a->subject_code}}</td>
                  <td>{{$a->department_name ?? '-'}}</td>
                  <td><span class="label label-default">{{$a->academic_year_name}}</span></td>
                  <td>
                    <a href="{{URL::route('teacher.subject.edit', $a->user_id)}}" class="btn btn-warning btn-xs" title="Edit Teacher"><i class="fa fa-edit"></i></a>
                    <a href="{{URL::route('teacher.subject.delete', [$a->user_id, $a->subject_id, $a->academic_year_id])}}" class="btn btn-danger btn-xs btn-delete-assignment" title="Remove"><i class="fa fa-trash"></i></a>
                  </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center">No assignments found.</td></tr>
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
<script src="{{ URL::asset('assets/js/jquery.dataTables.min.js')}}"></script>
<script src="{{ URL::asset('assets/js/dataTables.bootstrap.min.js')}}"></script>
<script src="{{ URL::asset('assets/js/select2.full.min.js')}}"></script>
<script src="{{ URL::asset('assets/js/sweetalert.min.js')}}"></script>
<script>
$(document).ready(function() {
    $('#teacherSelect, #subjectSelect').select2({ placeholder: 'Search and select...', allowClear: true });
    $('#yearSelect').select2({ placeholder: 'Select academic years...' });

    // Select All / Clear academic years
    $('#btnSelectAllYears').on('click', function() {
        $('#yearSelect option').prop('selected', true);
        $('#yearSelect').trigger('change');
    });
    $('#btnClearYears').on('click', function() {
        $('#yearSelect option').prop('selected', false);
        $('#yearSelect').trigger('change');
    });

    $('#assignTable').DataTable({
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
        order: [[0, 'asc']],
        language: {
            search: 'Search assignments:',
            lengthMenu: 'Show _MENU_ entries',
            info: 'Showing _START_ to _END_ of _TOTAL_ assignments',
            emptyTable: 'No assignments available'
        }
    });

});
</script>
@endsection
