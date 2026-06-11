@extends('layouts.master')

@section('title', 'Exam Types')

@section('content')
<div class="right_col" role="main">
  <div class="">
    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-5">
        <div class="x_panel">
          <div class="x_title">
            <h2>New Exam Type</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <form method="post" action="{{URL::route('exam_type.store')}}">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <div class="form-group">
                <label>Exam Name <span class="required">*</span></label>
                <input type="text" name="name" class="form-control" required>
              </div>
              <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="2"></textarea>
              </div>
              <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Create</button>
            </form>
          </div>
        </div>
      </div>
      <div class="col-md-7">
        <div class="x_panel">
          <div class="x_title">
            <h2>Exam Types</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <table class="table table-striped table-bordered">
              <thead><tr><th>Name</th><th>Description</th><th>Actions</th></tr></thead>
              <tbody>
                @foreach($examTypes as $et)
                <tr>
                  <td>{{$et->name}}</td>
                  <td>{{$et->description}}</td>
                  <td>
                    <form class="deleteForm" method="POST" action="{{URL::route('exam_type.destroy',$et->id)}}" style="display:inline">
                      <input name="_method" type="hidden" value="DELETE">
                      <input type="hidden" name="_token" value="{{ csrf_token() }}">
                      <button type="submit" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i></button>
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
@endsection

@section('extrascript')
<script src="{{ URL::asset('assets/js/sweetalert.min.js')}}"></script>
<script>
$('.deleteForm').submit(function(e) {
  e.preventDefault();
  var form = this;
  swal({title:"Delete Exam Type?",text:"Are you sure?",type:"warning",showCancelButton:true,confirmButtonColor:"#cc3f44",confirmButtonText:"Yes",closeOnConfirm:true},function(isConfirm){if(isConfirm)form.submit();});
});
</script>
@endsection
