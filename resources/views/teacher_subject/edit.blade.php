@extends('layouts.master')

@section('title', 'Edit Teacher Subjects')

@section('extrastyle')
<link href="{{ URL::asset('assets/css/select2.min.css')}}" rel="stylesheet">
<style>
.select2-container { width: 100% !important; }
.teacher-badge { font-size: 16px; padding: 8px 15px; border-radius: 4px; background: #f5f5f5; display: inline-block; }
.teacher-badge i { margin-right: 6px; }
</style>
@endsection

@section('content')
<div class="right_col" role="main">
  <div class="">
    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        <div class="x_panel">
          <div class="x_title">
            <h2><i class="fa fa-edit"></i> Edit Subjects</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <div class="text-center mb-4" style="margin-bottom:20px">
              <div class="teacher-badge">
                <i class="fa fa-user"></i>
                <strong>{{$teacher->firstname}} {{$teacher->lastname}}</strong>
                <span class="label label-default" style="margin-left:8px">{{$teacher->login}}</span>
                <span class="label label-info">{{$teacher->group}}</span>
              </div>
            </div>
            <form method="post" action="{{URL::route('teacher.subject.update', $teacher->id)}}">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <input type="hidden" name="_method" value="PUT">
              <div class="form-group">
                <label>Academic Year <span class="required">*</span></label>
                <select class="form-control" name="academic_year" required>
                  <option value="">Select Academic Year</option>
                  @foreach($academicYears as $y)
                    <option value="{{$y->name}}">{{$y->name}}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group">
                <label>Select Subjects for this Teacher</label>
                <select class="form-control select2" name="subject_ids[]" id="subjectSelect" multiple>
                  @foreach($allSubjects as $s)
                    <option value="{{$s->id}}" @if($teacher->subjects()->wherePivot('subject_id', $s->id)->wherePivot('academic_year', $currentYearName ?? '')->exists()) selected @endif>
                      {{$s->name}} ({{$s->code}}) - {{$s->department ? $s->department->name : ''}}
                    </option>
                  @endforeach
                </select>
                <small class="text-muted">Type to search. Currently assigned subjects are pre-selected.</small>
              </div>
              <div class="form-group text-right" style="margin-top:20px">
                <a href="{{URL::route('teacher.subject.index')}}" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back</a>
                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Update Subjects</button>
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
<script>$(document).ready(function() { $('.select2').select2({ placeholder: 'Search subjects...', allowClear: true }); });</script>
@endsection
