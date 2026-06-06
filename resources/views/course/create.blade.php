@extends("layouts.master")
@section("title","Create Course")
@section("content")
  <!-- page content -->
  <div class="right_col" role="main">
    <div class="">
      <div class="page-title">
        <div class="title_left">
          <h3>Courses <small>Create New Course</small></h3>
        </div>

        <div class="title_right">
          <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
            <div class="input-group">
              <input type="text" class="form-control" placeholder="Search for...">
              <span class="input-group-btn">
                      <button class="btn btn-default" type="button">Go!</button>
                    </span>
            </div>
          </div>
        </div>
      </div>

      <div class="clearfix"></div>

      <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
          <div class="x_panel">
            <div class="x_title">
              <h2>New Course Entry <small>Fill out the form to create a new course</small></h2>
              <ul class="nav navbar-right panel_toolbox">
                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                </li>
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                  <ul class="dropdown-menu" role="menu">
                    <li><a href="#">Settings 1</a>
                    </li>
                    <li><a href="#">Settings 2</a>
                    </li>
                  </ul>
                </li>
                <li><a class="close-link"><i class="fa fa-close"></i></a>
                </li>
              </ul>
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
              <br />
              <form id="demo-form2" data-parsley-validate class="form-horizontal form-label-left" action="{{URL::route("course.store")}}" method="post">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="form-group">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Course Name <span class="required">*</span>
                  </label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="text" id="name" name="name" value="{{old("name")}}" required="required" class="form-control col-md-7 col-xs-12">
                    @if ($errors->has("name"))
                      <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first("name") }}</strong>
                                    </span>
                    @endif
                  </div>
                </div>
                <div class="form-group">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12" for="code">Course Code <span class="required">*</span>
                  </label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="text" id="code" name="code" value="{{old("code")}}" required="required" class="form-control col-md-7 col-xs-12">
                    @if ($errors->has("code"))
                      <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first("code") }}</strong>
                                    </span>
                    @endif
                  </div>
                </div>
                <div class="form-group">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12" for="department_id">Department <span class="required">*</span>
                  </label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    {{Form::select("department_id",$departments,old("department_id"),['class'=>'form-control','id'=>'department_id','placeholder'=>'Select Department','required'=>'required'])}}
                    @if ($errors->has("department_id"))
                      <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first("department_id") }}</strong>
                                    </span>
                    @endif
                  </div>
                </div>
                <div class="form-group">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12" for="duration_years">Duration (Years) <span class="required">*</span>
                  </label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="number" id="duration_years" name="duration_years" value="{{old("duration_years")}} diligence="1" min="1" required="required" class="form-control col-md-7 col-xs-12">
                    @if ($errors->has("duration_years"))
                      <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first("duration_years") }}</strong>
                                    </span>
                    @endif
                  </div>
                </div>

                <div class="form-group">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Subjects</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <div id="subjects_container">
                      @if(old('subjects'))
                        @foreach(old('subjects') as $key => $subject)
                          <div class="subject-entry form-inline" style="margin-bottom: 5px;">
                            {{ Form::select('subjects['.$key.'][id]', $subjects, $subject['id'], ['class' => 'form-control', 'placeholder' => 'Select Subject', 'required' => 'required']) }}
                            <input type="number" name="subjects[{{$key}}][semester]" value="{{ $subject['semester'] }}" class="form-control" placeholder="Semester (1 or 2)" min="1" max="2" required="required" style="width: 150px;">
                            <button type="button" class="btn btn-danger btn-sm remove-subject">Remove</button>
                            @if ($errors->has('subjects.'.$key.'.id'))
                              <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('subjects.'.$key.'.id') }}</strong>
                              </span>
                            @endif
                            @if ($errors->has('subjects.'.$key.'.semester'))
                              <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('subjects.'.$key.'.semester') }}</strong>
                              </span>
                            @endif
                          </div>
                        @endforeach
                      @endif
                    </div>
                    <button type="button" id="add_subject" class="btn btn-success btn-sm">Add Subject</button>
                  </div>
                </div>

                <div class="ln_solid"></div>
                <div class="form-group">
                  <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    <button class="btn btn-primary" type="reset">Reset</button>
                    <button type="submit" class="btn btn-success">Submit</button>
                  </div>
                </div>

              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /page content -->
@endsection

@section('extrascript')
  <script>
    $(document).ready(function() {
      var subjectIndex = {{ count(old('subjects', [])) }};
      $('#add_subject').on('click', function() {
        var newSubjectEntry = `
          <div class="subject-entry form-inline" style="margin-bottom: 5px;">
            {{ Form::select('subjects[` + subjectIndex + `][id]', $subjects, null, ['class' => 'form-control', 'placeholder' => 'Select Subject', 'required' => 'required']) }}
            <input type="number" name="subjects[` + subjectIndex + `][semester]" class="form-control" placeholder="Semester (1 or 2)" min="1" max="2" required="required" style="width: 150px;">
            <button type="button" class="btn btn-danger btn-sm remove-subject">Remove</button>
          </div>
        `;
        $('#subjects_container').append(newSubjectEntry);
        subjectIndex++;
      });

      $(document).on('click', '.remove-subject', function() {
        $(this).closest('.subject-entry').remove();
      });
    });
  </script>
@endsection
