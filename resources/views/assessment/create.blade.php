@extends('layouts.master')

@section('title', 'New Assessment Plan')

@section('content')
<div class="right_col" role="main">
  <div class="">
    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-6 col-md-offset-3">
        <div class="x_panel">
          <div class="x_title">
            <h2><i class="fa fa-plus"></i> New Assessment Plan</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <form method="post" action="{{URL::route('assessment.store')}}">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <div class="form-group">
                <label>Subject <span class="required">*</span></label>
                <select class="form-control" name="subject_id" required>
                  <option value="">Select Subject</option>
                  @foreach($subjects as $s)
                    <option value="{{$s->id}}">{{$s->name}} ({{$s->code}}) - {{$s->department->name ?? ''}}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group">
                <label>Semester <span class="required">*</span></label>
                <select class="form-control" name="semester_id" required>
                  <option value="">Select Semester</option>
                  @foreach($semesters as $sem)
                    <option value="{{$sem->id}}">{{$sem->academicYear->name}} - Semester {{$sem->semester_number}}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group">
                <label>Use Template <small>(optional — pre-fills components)</small></label>
                <select class="form-control" name="template_id">
                  <option value="">No template (manual setup)</option>
                  @foreach($templates as $t)
                    <option value="{{$t->id}}">{{$t->name}} — CA {{$t->ca_weight}}% / UE {{$t->ue_weight}}%</option>
                  @endforeach
                </select>
              </div>
              <hr>
              <p class="text-muted">If you select a template, components will be pre-filled automatically.</p>
              <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-save"></i> Create Plan</button>
              <a href="{{URL::route('assessment.index')}}" class="btn btn-default">Cancel</a>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
