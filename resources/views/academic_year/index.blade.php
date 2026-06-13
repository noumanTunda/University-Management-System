@extends('layouts.master')
@section('title', 'Academic Years')
@section('extrastyle')
<link href="{{ URL::asset('assets/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
@endsection
@section('content')
<div class="right_col" role="main">
  <div class="">
    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12">
        <div class="x_panel">
          <div class="x_title">
            <h2><i class="fa fa-calendar"></i> Academic Years</h2>
            <a href="{{URL::route('academic.year.create')}}" class="btn btn-success pull-right"><i class="fa fa-plus"></i> New Year</a>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <table id="yearTable" class="table table-striped table-bordered">
              <thead><tr><th>Name</th><th>Status</th><th>Created</th><th width="120">Actions</th></tr></thead>
              <tbody>
                @forelse($years as $y)
                <tr>
                  <td><strong>{{$y->name}}</strong></td>
                  <td>
                    @if($y->is_active)
                      <span class="label label-success">Active</span>
                    @else
                      <span class="label label-default">Inactive</span>
                    @endif
                  </td>
                  <td>{{$y->created_at ? $y->created_at->format('d/m/Y') : '-'}}</td>
                  <td>
                    <a href="{{URL::route('academic.year.edit', $y->id)}}" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i></a>
                    <a href="{{URL::route('academic.year.destroy', $y->id)}}" class="btn btn-danger btn-xs" onclick="return confirm('Delete {{$y->name}}?')"><i class="fa fa-trash"></i></a>
                  </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center">No academic years yet. <a href="{{URL::route('academic.year.create')}}">Create one</a>.</td></tr>
                @endforelse
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
<script src="{{ URL::asset('assets/js/jquery.dataTables.min.js')}}"></script>
<script src="{{ URL::asset('assets/js/dataTables.bootstrap.min.js')}}"></script>
<script>
$(document).ready(function() {
    $('#yearTable').DataTable({
        pageLength: 25,
        order: [[0, 'desc']],
        language: { search: 'Search:', lengthMenu: 'Show _MENU_', info: 'Showing _START_ to _END_ of _TOTAL_' }
    });
});
</script>
@endsection
