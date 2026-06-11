@extends("layouts.master")
@section("title","Course Details")
@section("content")
  <!-- page content -->
  <div class="right_col" role="main">
    <div class="">
      <div class="page-title">
        <div class="title_left">
          <h3>Course <small>Details</small></h3>
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
              <h2>Course: {{ $course->name }} <small>Code: {{ $course->code }}</small></h2>
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
              <div class="col-md-3 col-sm-3 col-xs-12 profile_left">
                <h3>{{ $course->name }}</h3>

                <ul class="list-unstyled user_data">
                  <li><i class="fa fa-briefcase user-profile-icon"></i> {{ $course->department ? $course->department->name : 'Unassigned' }}
                  </li>
                  <li>
                    <i class="fa fa-clock-o user-profile-icon"></i> Duration: {{ $course->duration_years }} Years
                  </li>
                  <li>
                    <i class="fa fa-bookmark user-profile-icon"></i> Minimum Credits: {{ $course->min_credits }}
                  </li>
                </ul>

                <a href="{{URL::route("course.edit",$course->id)}}" class="btn btn-success"><i class="fa fa-edit m-right-xs"></i>Edit Course</a>
                <br />

              </div>
              <div class="col-md-9 col-sm-9 col-xs-12">

                <div class="profile_title">
                  <div class="col-md-6">
                    <h2>Course Subjects</h2>
                  </div>
                  <div class="col-md-6">
                    <div id="reportrange" class="pull-right" style="margin-top: 5px; background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #E6E9ED"></div>
                  </div>
                </div>
                <!-- start of user-activity-graph -->
                <div class="x_content">
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>Subject Code</th>
                        <th>Subject Name</th>
                        <th>Credit</th>
                        <th>Semester</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($course->subjects as $subject)
                        <tr>
                          <td>{{ $subject->code }}</td>
                          <td>{{ $subject->name }}</td>
                          <td>{{ $subject->credit }}</td>
                          <td>
                            @php
                              $semesterLabels = [1=>'Semester 1',2=>'Semester 2'];
                            @endphp
                            {{ isset($semesterLabels[$subject->pivot->semester]) ? $semesterLabels[$subject->pivot->semester] : $subject->pivot->semester }}
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
                <!-- end of user-activity-graph -->

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /page content -->
@endsection
