@extends('layouts.master')
@section('title', 'Create Missing Student Accounts')
@section('extrastyle')
<link href="{{ URL::asset('assets/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
<style>
.select-all-bar { background:#f5f5f5; padding:8px 12px; border-radius:4px; margin-bottom:10px; }
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
            <h2><i class="fa fa-user-plus"></i> Create Accounts for Missing Students</h2>
            <a href="{{URL::route('user.index')}}" class="btn btn-default pull-right"><i class="fa fa-arrow-left"></i> Back to Users</a>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            @if(count($missing) == 0)
              <div class="alert alert-success">
                <i class="fa fa-check-circle"></i> All students already have user accounts. No missing accounts to create.
              </div>
            @else
              <form method="post" action="{{URL::route('user.create.missing')}}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="select-all-bar">
                  <div class="row">
                    <div class="col-md-6">
                      <label class="checkbox-inline">
                        <input type="checkbox" id="selectAll"> <strong>Select All</strong>
                      </label>
                      <span class="text-muted">({{count($missing)}} student(s) without accounts)</span>
                    </div>
                    <div class="col-md-6 text-right">
                      <button type="submit" class="btn btn-success"><i class="fa fa-user-plus"></i> Create Accounts for Selected</button>
                    </div>
                  </div>
                </div>

                <table id="missingTable" class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th width="40"><input type="checkbox" id="selectAllHead"></th>
                      <th>Reg No</th>
                      <th>First Name</th>
                      <th>Last Name</th>
                      <th>Email</th>
                      <th>Department</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($missing as $s)
                    <tr>
                      <td><input type="checkbox" name="student_ids[]" value="{{$s->id}}" class="studentCheck"></td>
                      <td><code>{{$s->idNo}}</code></td>
                      <td>{{$s->firstName}}</td>
                      <td>{{$s->lastName}}</td>
                      <td>{{$s->email ?: $s->idNo . '@student.osums.edu'}}</td>
                      <td>{{$s->department->name ?? '-'}}</td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>

                <div class="text-right" style="margin-top:10px">
                  <button type="submit" class="btn btn-success btn-lg"><i class="fa fa-user-plus"></i> Create Accounts for Selected</button>
                </div>
              </form>
            @endif
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
<script>
$(document).ready(function() {
    $('#missingTable').DataTable({
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
        order: [[1, 'asc']],
        language: {
            search: 'Search students:',
            lengthMenu: 'Show _MENU_ entries',
            info: 'Showing _START_ to _END_ of _TOTAL_ students',
            emptyTable: 'No missing students'
        }
    });

    // Select All functionality
    function toggleSelectAll(checked) {
        $('.studentCheck').prop('checked', checked);
    }

    $('#selectAll, #selectAllHead').on('change', function() {
        toggleSelectAll($(this).prop('checked'));
    });

    // Uncheck "Select All" if any individual is unchecked
    $(document).on('change', '.studentCheck', function() {
        var allChecked = $('.studentCheck:checked').length === $('.studentCheck').length;
        $('#selectAll, #selectAllHead').prop('checked', allChecked);
    });
});
</script>
@endsection
