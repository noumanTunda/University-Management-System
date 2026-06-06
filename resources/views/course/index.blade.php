@extends("layouts.master")
@section("title","Course List")
@section("content")
  <!-- page content -->
  <div class="right_col" role="main">
    <div class="">
      <div class="page-title">
        <div class="title_left">
          <h3>Courses <small>Course List</small></h3>
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
              <h2>All Courses <small>List of Courses</small></h2>
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
              <p class="text-muted font-13 m-b-30">
                This is a list of all courses currently in the system.
              </p>
              <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Code</th>
                  <th>Department</th>
                  <th>Duration (Years)</th>
                  <th>Min. Credits</th>
                  <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($courses as $course)
                  <tr>
                    <td>{{$course->id}}</td>
                    <td>{{$course->name}}</td>
                    <td>{{$course->code}}</td>
                    <td>{{ $course->department ? $course->department->name : 'Unassigned' }}</td>
                    <td>{{$course->duration_years}}</td>
                    <td>{{$course->min_credits}}</td>
                    <td>
                      <a href="{{URL::route("course.edit",$course->id)}}" class="btn btn-xs btn-info"><i class="fa fa-pencil"></i> Edit </a>
                      <a href="{{URL::route("course.show",$course->id)}}" class="btn btn-xs btn-primary"><i class="fa fa-eye"></i> View </a>
                      <form action="{{ URL::route("course.destroy",$course->id) }}" method="POST" class="inline-form">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <button class="btn btn-xs btn-danger" onclick="return confirm(\'Are you sure you want to delete this course?\')"><i class="fa fa-trash"></i> Delete</button>
                      </form>
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
  <!-- /page content -->
@endsection

@section("extrastyle")
  <!-- Datatables -->
  <link href="{{ URL::asset("assets/css/dataTables.bootstrap.min.css")}}" rel="stylesheet">
  <link href="{{ URL::asset("assets/css/buttons.bootstrap.min.css")}}" rel="stylesheet">
  <link href="{{ URL::asset("assets/css/fixedHeader.bootstrap.min.css")}}" rel="stylesheet">
  <link href="{{ URL::asset("assets/css/responsive.bootstrap.min.css")}}" rel="stylesheet">
  <link href="{{ URL::asset("assets/css/scroller.bootstrap.min.css")}}" rel="stylesheet">
@endsection

@section("extrascript")
  <!-- Datatables -->
  <script src="{{ URL::asset("assets/js/jquery.dataTables.min.js")}}"></script>
  <script src="{{ URL::asset("assets/js/dataTables.bootstrap.min.js")}}"></script>
  <script src="{{ URL::asset("assets/js/dataTables.buttons.min.js")}}"></script>
  <script src="{{ URL::asset("assets/js/buttons.bootstrap.min.js")}}"></script>
  <script src="{{ URL::asset("assets/js/buttons.flash.min.js")}}"></script>
  <script src="{{ URL::asset("assets/js/buttons.html5.min.js")}}"></script>
  <script src="{{ URL::asset("assets/js/buttons.print.min.js")}}"></script>
  <script src="{{ URL::asset("assets/js/dataTables.fixedHeader.min.js")}}"></script>
  <script src="{{ URL::asset("assets/js/dataTables.keyTable.min.js")}}"></script>
  <script src="{{ URL::asset("assets/js/dataTables.responsive.min.js")}}"></script>
  <script src="{{ URL::asset("assets/js/responsive.bootstrap.min.js")}}"></script>
  <script src="{{ URL::asset("assets/js/dataTables.scroller.min.js")}}"></script>
  <script src="{{ URL::asset("assets/js/jszip.min.js")}}"></script>
  <script src="{{ URL::asset("assets/js/pdfmake.min.js")}}"></script>
  <script src="{{ URL::asset("assets/js/vfs_fonts.js")}}"></script>

  <script>
    $(document).ready(function() {
      var handleDataTableButtons = function() {
        if ($("#datatable-buttons").length) {
          TableManageButtons.init();
        }
      };

      TableManageButtons = function() {
        "use strict";
        return {
          init: function() {
            $("#datatable-buttons").DataTable({
              dom: "Blfrtip",
              buttons: [
                {
                  extend: "copy",
                  className: "btn-sm"
                },
                {
                  extend: "csv",
                  className: "btn-sm"
                },
                {
                  extend: "excel",
                  className: "btn-sm"
                },
                {
                  extend: "pdfHtml5",
                  className: "btn-sm"
                },
                {
                  extend: "print",
                  className: "btn-sm"
                },
              ],
              responsive: true
            });
          }
        };
      }();

      $("#datatable").dataTable();
      $("#datatable-keytable").DataTable({
        keys: true
      });

      $("#datatable-responsive").DataTable({
        responsive: true
      });

      $("#datatable-scroller").DataTable({
        ajax: "js/datatables/json/scroller-demo.json",
        deferRender: true,
        scrollY: 380,
        scrollCollapse: true,
        scroller: true
      });

      var cw = $(".form_date").width();
      $(".form_date").css("width",cw+"px");

      var all_check = $(".check_all_students");
      var student_checkbox = $(".student_checkbox");

      all_check.on("ifChecked",function (event) {
        student_checkbox.iCheck("check");
      });
      all_check.on("ifUnchecked",function (event) {
        student_checkbox.iCheck("uncheck");
      });


      var export_form = $(".export_form");

      export_form.on("submit",function (event) {


        var url = export_form.attr("action");

        var session = export_form.find("select[name=\"session\"]").val();
        var department = export_form.find("select[name=\"department_id\"]").val();
        var level_term = export_form.find("select[name=\"levelTerm\"]").val();


        if(session==="" || department==="" || level_term===""){
          alert("Session, Department and Level/Term is Required!");
          return false;
        }

        var ids = $(".student_checkbox:checked").map(function(){
          return $(this).val();
        }).get();

        if(ids.length === 0 ){
          alert("Please Select at least one student!");
          return false;
        }
        var data = "session="+session+"&department="+department+"&level_term="+level_term+"&students="+ids;

        var XHR = new XMLHttpRequest();
        XHR.open("GET", url+"?"+data, true);
        XHR.send();
        return false;
      });


      $("#datatable-buttons").DataTable({
        dom: "Blfrtip",
        buttons: [
          {
            extend: "copy",
            className: "btn-sm"
          },
          {
            extend: "csv",
            className: "btn-sm"
          },
          {
            extend: "excel",
            className: "btn-sm"
          },
          {
            extend: "pdfHtml5",
            className: "btn-sm"
          },
          {
            extend: "print",
            className: "btn-sm"
          },
        ],
        responsive: true
      });

      handleDataTableButtons();



    });
  </script>
  <!-- /Datatables -->
@endsection
