@extends("layouts.master")
@section("title","Edit Course")
@section("content")
  <!-- page content -->
  <div class="right_col" role="main">
    <div class="">
      <div class="page-title">
        <div class="title_left">
          <h3>Courses <small>Edit Course</small></h3>
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
              <h2>Edit Course Information <small>Update the course details</small></h2>
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
              <form id="demo-form2" data-parsley-validate class="form-horizontal form-label-left" action="{{URL::route("course.update", $course->id)}}" method="post">
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="form-group">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Course Name <span class="required">*</span>
                  </label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="text" id="name" name="name" value="{{old("name", $course->name)}}" required="required" class="form-control col-md-7 col-xs-12">
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
                    <input type="text" id="code" name="code" value="{{old("code", $course->code)}}" required="required" class="form-control col-md-7 col-xs-12">
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
                    {{Form::select("department_id",$departments,old("department_id", $course->department_id),["class"=>"form-control","id"=>"department_id","placeholder"=>"Select Department","required"=>"required"])}} 
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
                    <input type="number" id="duration_years" name="duration_years" value="{{old("duration_years", $course->duration_years)}}" min="1" required="required" class="form-control col-md-7 col-xs-12">
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
                      @php $subjectIndex = 0; @endphp
                      @foreach($course->subjects as $subject)
                        <div class="subject-entry form-inline" style="margin-bottom: 5px;">
                          {{ Form::select("subjects[".$subjectIndex."][id]", $allSubjects, old("subjects.".$subjectIndex.".id", $subject->id), ["class" => "form-control", "placeholder" => "Select Subject", "required" => "required"]) }}
                          <input type="number" name="subjects[{{$subjectIndex}}][semester]" value="{{ old("subjects.".$subjectIndex.".semester", $subject->pivot->semester) }}" class="form-control" placeholder="Semester (1 or 2)" min="1" max="2" required="required" style="width: 150px;">
                          <button type="button" class="btn btn-danger btn-sm remove-subject">Remove</button>
                        </div>
                        @php $subjectIndex++; @endphp
                      @endforeach
                      @if(old("subjects"))
                        @foreach(old("subjects") as $key => $subjectData)
                          @if(!array_key_exists($key, $course->subjects->toArray())) {{-- Only add if not already in $course->subjects --}}
                            <div class="subject-entry form-inline" style="margin-bottom: 5px;">
                              {{ Form::select("subjects[".$key."][id]", $allSubjects, $subjectData["id"], ["class" => "form-control", "placeholder" => "Select Subject", "required" => "required"]) }}
                              <input type="number" name="subjects[{{$key}}][semester]" value="{{ $subjectData["semester"] }}" class="form-control" placeholder="Semester (1 or 2)" min="1" max="2" required="required" style="width: 150px;">
                              <button type="button" class="btn btn-danger btn-sm remove-subject">Remove</button>
                            </div>
                            @php $subjectIndex++; @endphp
                          @endif
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
                    <button type="submit" class="btn btn-success">Update</button>
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

@section("extrascript")
  <script>
    $(document).ready(function() {
      var subjectIndex = {{ count(old("subjects", $course->subjects)) }};
      $("#add_subject").on("click", function() {
        var newSubjectEntry = `
          <div class="subject-entry form-inline" style="margin-bottom: 5px;">
            {{ Form::select("subjects[` + subjectIndex + `][id]", $allSubjects, null, ["class" => "form-control", "placeholder" => "Select Subject", "required" => "required"]) }}
            <input type="number" name="subjects[` + subjectIndex + `][semester]" class="form-control" placeholder="Semester (1 or 2)" min="1" max="2" required="required" style="width: 150px;">
            <button type="button" class="btn btn-danger btn-sm remove-subject">Remove</button>
          </div>
        `;
        $("#subjects_container").append(newSubjectEntry);
        subjectIndex++;
      });

      $(document).on("click", ".remove-subject", function() {
        $(this).closest(".subject-entry").remove();
      });
    });
  </script>
@endsection
