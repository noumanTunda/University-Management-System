@extends('layouts.master')
@section('title', 'Assessment Plans')
@section('extrastyle')
<link href="{{ URL::asset('assets/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
<style>
.plan-table-wrapper { transition: all 0.3s ease; }
.plan-table-wrapper.collapsed .plan-table-body { display: none; }
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
            <h2><i class="fa fa-tasks"></i> Assessment Plans</h2>
            <div class="nav navbar-right panel_toolbox">
              <a href="javascript:void(0)" id="togglePlanTable" class="btn btn-default btn-xs" title="Collapse/Expand"><i class="fa fa-chevron-up"></i></a>
              <a href="{{URL::route('assessment.create')}}" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> New Plan</a>
            </div>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <div class="plan-table-wrapper" id="planTableWrapper">
              <table id="planTable" class="table table-striped table-bordered">
                <thead><tr><th>Subject</th><th>Code</th><th>Semester</th><th>Components</th><th>CA / UE</th><th width="160">Actions</th></tr></thead>
                <tbody>
                  @forelse($plans as $p)
                  <tr>
                    <td>{{$p->subject->name ?? '-'}}</td>
                    <td>{{$p->subject->code ?? '-'}}</td>
                    <td>{{$p->semester->academicYear->name ?? '-'}} - S{{$p->semester->semester_number ?? '-'}}</td>
                    <td><span class="badge badge-info">{{$p->components->count()}}</span></td>
                    <td><span class="label label-primary">CA {{$p->ca_weight}}%</span> <span class="label label-warning">UE {{$p->ue_weight}}%</span></td>
                    <td>
                      <a href="{{URL::route('assessment.components', $p->id)}}" class="btn btn-info btn-xs" title="Components"><i class="fa fa-cog"></i></a>
                      <a href="{{URL::route('assessment.compute', $p->id)}}" class="btn btn-success btn-xs" title="Compute Grades" onclick="return confirm('Compute grades from all marks?')"><i class="fa fa-calculator"></i></a>
                    </td>
                  </tr>
                  @empty
                  <tr><td colspan="6" class="text-center">No assessment plans yet. <a href="{{URL::route('assessment.create')}}">Create one</a>.</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    @if(count($templates) > 0)
    <div class="row" style="margin-top:20px">
      <div class="col-md-12">
        <div class="x_panel">
          <div class="x_title">
            <h2><i class="fa fa-file-text-o"></i> Templates <small>Reusable assessment structures</small></h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            @foreach($templates as $t)
            <div class="panel panel-default">
              <div class="panel-heading"><strong>{{$t->template_name}}</strong> <small>{{$t->description}}</small> <span class="label label-info pull-right">CA {{$t->ca_weight}}% / UE {{$t->ue_weight}}%</span></div>
              <div class="panel-body">
                <table class="table table-bordered table-sm">
                  <tr><th>Component</th><th>Type</th><th>Max</th><th>Weight</th></tr>
                  @foreach($t->components as $c)
                  <tr><td>{{$c->name}}</td><td><span class="label label-{{$c->type=='CA'?'primary':'warning'}}">{{$c->type}}</span></td><td>{{$c->max_score}}</td><td>{{$c->weight}}%</td></tr>
                  @endforeach
                </table>
              </div>
            </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
    @endif
  </div>
</div>
@endsection

@section('extrascript')
<script src="{{ URL::asset('assets/js/jquery.dataTables.min.js')}}"></script>
<script src="{{ URL::asset('assets/js/dataTables.bootstrap.min.js')}}"></script>
<script>
$(document).ready(function() {
    $('#planTable').DataTable({
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
        order: [[0, 'asc']],
        language: {
            search: 'Search plans:',
            lengthMenu: 'Show _MENU_ entries',
            info: 'Showing _START_ to _END_ of _TOTAL_ plans',
            emptyTable: 'No plans available'
        }
    });

    // Collapse / Expand toggle
    var wrapper = $('#planTableWrapper');
    var toggleBtn = $('#togglePlanTable');
    toggleBtn.on('click', function() {
        wrapper.toggleClass('collapsed');
        var isCollapsed = wrapper.hasClass('collapsed');
        toggleBtn.find('i').toggleClass('fa-chevron-up fa-chevron-down');
        if (isCollapsed) {
            wrapper.find('.plan-table-body').slideUp(200);
        } else {
            wrapper.find('.plan-table-body').slideDown(200);
        }
    });
});
</script>
@endsection
