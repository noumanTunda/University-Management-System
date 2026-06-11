@extends('layouts.master')

@section('title', 'Assign Subjects to Teachers')

@section('extrastyle')
<link href="{{ URL::asset('assets/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
<link href="{{ URL::asset('assets/css/sweetalert.css')}}" rel="stylesheet">
<style>
.multiselect { height: 200px; }
</style>
@endsection

@section('content')
        <div class="right_col" role="main">
          <div class="">
            <div class="clearfix"></div>
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Assign Subjects to Teachers</h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <form class="form-horizontal form-label-left" method="post" action="{{URL::route('teacher.subject.store')}}">
                      <input type="hidden" name="_token" value="{{ csrf_token() }}">
                      <div class="row">
                        <div class="col-md-4">
                          <div class="form-group">
                            <label class="control-label" for="user_id">Teacher <span class="required">*</span></label>
                            <select class="form-control" name="user_id" id="teacherSelect" required>
                              <option value="">Select Teacher</option>
                              @foreach($teachers as $t)
                                <option value="{{$t->id}}">{{$t->firstname}} {{$t->lastname}} ({{$t->login}})</option>
                              @endforeach
                            </select>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group">
                            <label class="control-label" for="department_id">Department</label>
                            <select class="form-control" id="deptSelect">
                              <option value="">All Departments</option>
                              @foreach($departments as $d)
                                <option value="{{$d->id}}">{{$d->name}}</option>
                              @endforeach
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label">Assigned Subjects</label>
                            <select class="form-control" id="currentSubjects" multiple disabled>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label">Select Subjects <span class="required">*</span></label>
                            <select class="form-control multiselect" name="subject_ids[]" id="subjectSelect" multiple required>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-save"></i> Save Assignments</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Current Assignments</h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <table class="table table-striped table-bordered">
                      <thead>
                        <tr><th>Teacher</th><th>Group</th><th>Assigned Subjects</th></tr>
                      </thead>
                      <tbody>
                        @foreach($teachers as $t)
                        <tr>
                          <td>{{$t->firstname}} {{$t->lastname}}</td>
                          <td>{{$t->group}}</td>
                          <td>
                            @if($t->subjects->count() > 0)
                              @foreach($t->subjects as $s)
                                <span class="label label-info">{{$s->name}} ({{$s->code}})</span>
                              @endforeach
                            @else
                              <span class="label label-default">No subjects assigned</span>
                            @endif
                          </td>
                        </tr>
                        @endforeach
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
<script>
$(document).ready(function() {
    var allSubjects = {};

    $('#deptSelect').change(function() {
        var deptId = $(this).val();
        if (deptId) {
            $.get('/teacher-subject/subjects/' + deptId, function(res) {
                if (res.success) {
                    allSubjects[deptId] = res.subjects;
                    populateSubjectSelect(deptId);
                }
            });
        } else {
            $('#subjectSelect').empty();
        }
    });

    function populateSubjectSelect(deptId) {
        var sel = $('#subjectSelect');
        sel.empty();
        if (allSubjects[deptId]) {
            $.each(allSubjects[deptId], function(i, s) {
                sel.append('<option value="' + s.id + '">' + s.name + ' (' + s.code + ')</option>');
            });
        }
    }

    $('#teacherSelect').change(function() {
        var id = $(this).val();
        var box = $('#currentSubjects');
        box.empty();
        if (!id) return;
        @foreach($teachers as $t)
            @if($t->subjects->count() > 0)
                if ({{$t->id}} == id) {
                    @foreach($t->subjects as $s)
                        box.append('<option value="{{$s->id}}">{{$s->name}} ({{$s->code}})</option>');
                    @endforeach
                }
            @endif
        @endforeach
    });
});
</script>
@endsection
