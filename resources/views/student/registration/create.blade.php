@extends('layouts.master')

@section('title', 'Registration')
@section('extrastyle')
<link href="{{ URL::asset('assets/css/select2.min.css')}}" rel="stylesheet">
<link href="{{ URL::asset('assets/css/switchery.min.css')}}" rel="stylesheet">
@endsection

@section('content')
<div class="right_col" role="main">
  <div class="">
    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Registration<small> Student semester registration</small></h2>
            <p class="text-muted" style="margin-top: 8px;">Select Batch (admission year) → students load → pick Academic Year + Semester → register</p>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            @if (count($errors) > 0)
             <div class="alert alert-danger">
               <strong>Whoops!</strong> There were some problems with your input.<br><br>
               <ul>
                 @foreach ($errors->all() as $error)
                   <li>{{ $error }}</li>
                 @endforeach
               </ul>
             </div>
            @endif
            <form class="form-horizontal form-label-left" novalidate method="post" action="{{URL::route('student.registration.store')}}">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <div class="row">
                <div class="col-md-3">
                  <div class="item form-group">
                    <label for="department_id">Department <span class="required">*</span></label>
                    {!!Form::select('department_id', $departments, old('department_id'), ['placeholder' => 'Pick a department','class'=>'form-control','required'=>'required','id'=>'department_id'])!!}
                    <span class="text-danger">{{ $errors->first('department_id') }}</span>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="item form-group">
                    <label for="batch">Batch (Admission Year) <span class="required">*</span></label>
                    {!!Form::select('batch', $batches, old('batch'), ['placeholder' => 'Pick Batch','class'=>'form-control','required'=>'required','id'=>'batchSelect'])!!}
                    <span class="text-danger">{{ $errors->first('batch') }}</span>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="item form-group">
                    <label for="session">Register for Year <span class="required">*</span></label>
                    {!!Form::select('session', $sessions, null, ['placeholder' => 'Pick Academic Year','class'=>'form-control','required'=>'required' ,'id'=>'sessionSelect'])!!}
                    <span class="text-danger">{{ $errors->first('session') }}</span>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="item form-group">
                    <label for="levelTerm">Semester <span class="required">*</span></label>
                    {!!Form::select('levelTerm', $semesters, null, ['placeholder' => 'Pick Semester','class'=>'form-control','required'=>'required', 'id'=>'levelTerm'])!!}
                    <span class="text-danger">{{ $errors->first('levelTerm') }}</span>
                  </div>
                </div>
              </div>

              <div class="row" style="margin-top:10px">
                <div class="col-md-12">
                  <div class="table-responsive">
                    <table id="studentList" class="table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th>Id No</th>
                          <th>Name</th>
                          <th>Register?
                            <div class="pull-right">
                              <input type="checkbox" id="allcheck" class="js-switch allCheck" name="allcheck"> All Select
                            </div>
                          </th>
                        </tr>
                      </thead>
                      <tbody></tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div class="ln_solid"></div>
              <div class="row">
                <button id="btnsave" type="submit" class="btn btn-lg btn-success pull-right" style="display:none">
                  <i class="fa fa-check"></i> Submit
                </button>
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
<script src="{{ URL::asset('assets/js/switchery.min.js')}}"></script>
<script>
$(document).ready(function() {
  $('#btnsave').hide();

  $('#department_id, #batchSelect').select2({ placeholder: "Select", allowClear: true });
  $('#sessionSelect, #levelTerm').select2({ placeholder: "Select", allowClear: true });

  // Load students when batch is selected (department must also be selected)
  function loadStudents() {
    var dept = $('#department_id').val();
    var batch = $('#batchSelect').val();
    if (!dept || !batch) { $('#btnsave').hide(); return; }

    $.ajax({
      url:'/students-batch/'+dept+'/'+batch,
      type: 'get',
      dataType: 'json',
      success: function(data) {
        $("#studentList tbody").empty();
        if(data.students.length > 0) {
          $('#btnsave').show();
        } else {
          $('#btnsave').hide();
        }
        $.each(data.students, function(key, value) {
          addRow(value.id, value.firstName+' '+ (value.middleName||'') +' '+value.lastName, value.idNo);
        });
        var elems = Array.prototype.slice.call(document.querySelectorAll('.tb-switch'));
        elems.forEach(function(html) {
          var switchery = new Switchery(html);
        });
      },
      error: function() {
        new PNotify({ title: 'Error!', text: 'Could not load students.', type: 'error', styling: 'bootstrap3' });
      }
    });
  }

  $('#department_id').on('change', loadStudents);
  $('#batchSelect').on('change', loadStudents);
  // Auto-load if old values present
  if ($('#department_id').val() && $('#batchSelect').val()) { loadStudents(); }

  // All select toggle
  $('.allCheck').on('change', function() {
    $('.tb-switch').trigger('click');
  });
});

function addRow(id, stdname, idNo) {
  var table = document.getElementById('studentList');
  var row = table.insertRow(table.rows.length);
  var cell1 = row.insertCell(0);
  cell1.innerHTML = '<label>'+idNo+'</label><input name="ids[]" value="'+id+'" type="hidden">';
  var cell2 = row.insertCell(1);
  cell2.innerHTML = '<label>'+stdname+'</label>';
  var cell3 = row.insertCell(2);
  var chk = document.createElement("input");
  chk.type = "checkbox";
  chk.checked = false;
  chk.className = "js-switch tb-switch";
  chk.name = "registeredIds["+id+"]";
  cell3.appendChild(chk);
}
</script>
@endsection
