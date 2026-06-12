@extends('layouts.master')

@section('title', 'Assign Subjects to Teachers')

@section('extrastyle')
<link href="{{ URL::asset('assets/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
<link href="{{ URL::asset('assets/css/select2.min.css')}}" rel="stylesheet">
<style>
.select2-container { width: 100% !important; }
</style>
@endsection

@section('content')
<div class="right_col" role="main">
  <div class="">
    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Assign Subjects to Teacher</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <form method="post" action="{{URL::route('teacher.subject.store')}}">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <div class="row">
                <div class="col-md-5">
                  <div class="form-group">
                    <label>Academic Year <span class="required">*</span></label>
                    <select class="form-control" name="academic_year" required>
                      <option value="">Select Year</option>
                      @foreach($academicYears as $y)
                        <option value="{{$y->name}}" {{$y->is_current ? "selected" : ""}}>{{$y->name}}</option>
                      @endforeach
                    </select>
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
                <div class="col-md-12">
                  <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-save"></i> Assign Subjects</button>
                </div>
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
            <table id="assignTable" class="table table-striped table-bordered">
              <thead>
                <tr><th>Teacher</th><th>Group</th><th>Assigned Subjects</th><th width="120">Actions</th></tr>
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
                        @if($s->pivot->academic_year)
                          <span class="label label-default" style="font-size:10px">{{$s->pivot->academic_year}}</span>
                        @endif
                      @endforeach
                    @else
                      <span class="label label-default">No subjects assigned</span>
                    @endif
                  </td>
                  <td>
                    <div class="btn-group btn-group-xs">
                      <a href="{{URL::route('teacher.subject.edit', $t->id)}}" class="btn btn-warning" title="Edit"><i class="glyphicon glyphicon-edit"></i></a>
                      <form method="POST" action="{{URL::route('teacher.subject.destroy', $t->id)}}" style="display:inline" class="deleteForm">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-danger" title="Remove All"><i class="glyphicon glyphicon-trash"></i></button>
                      </form>
                    </div>
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
<script src="{{ URL::asset('assets/js/select2.full.min.js')}}"></script>
<script src="{{ URL::asset('assets/js/sweetalert.min.js')}}"></script>
    <script>
    $(document).ready(function() {
        $('.select2').select2({ placeholder: 'Search and select...', allowClear: true });
        $('.deleteForm').submit(function(e) {
            e.preventDefault();
            var form = this;
            swal({title:"Remove All Subjects?",text:"This will unassign all subjects from this teacher.",type:"warning",showCancelButton:true,confirmButtonColor:"#cc3f44",confirmButtonText:"Yes",closeOnConfirm:true},function(isConfirm){if(isConfirm)form.submit();});
        });
    });
    </script>
    @endsection
