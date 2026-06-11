@extends('layouts.master')
@section('title', 'Bulk Upload Marks')

@section('content')
<div class="right_col" role="main">
  <div class="">
    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        <div class="x_panel">
          <div class="x_title">
            <h2><i class="fa fa-upload"></i> Bulk Upload Marks</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <form method="post" action="{{URL::route('exam.marks.upload.store')}}" enctype="multipart/form-data">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">

              <div class="form-group">
                <label>Subject <span class="required">*</span></label>
                <select class="form-control" name="subject_id" required>
                  <option value="">Select Subject</option>
                  @foreach(\App\Subject::orderBy('name')->get() as $s)
                    <option value="{{$s->id}}">{{$s->name}} ({{$s->code}})</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group">
                <label>Semester <span class="required">*</span></label>
                <select class="form-control" name="semester_id" required>
                  <option value="">Select Semester</option>
                  @foreach(\App\Semester::with('academicYear')->orderBy('id')->get() as $sem)
                    <option value="{{$sem->id}}">{{$sem->academicYear->name}} - Semester {{$sem->semester_number}}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group">
                <label>CSV File <span class="required">*</span></label>
                <input type="file" name="marks_file" class="form-control" accept=".csv,.txt" required>
                <p class="help-block">
                  CSV format: <code>idNo, ca_score, ue_score</code>
                  <br>Example: <code>T24-03-00000, 28, 45</code>
                  <br>CA max 40, UE max 60
                </p>
              </div>
              <button type="submit" class="btn btn-primary"><i class="fa fa-upload"></i> Upload Marks</button>
              <a href="{{URL::route('exam.marks.create')}}" class="btn btn-default">Cancel</a>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
