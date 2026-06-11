@extends('layouts.master')

@section('title', 'Assessment Plans')

@section('content')
<div class="right_col" role="main">
  <div class="">
    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12">
        <div class="x_panel">
          <div class="x_title">
            <h2><i class="fa fa-tasks"></i> Assessment Plans</h2>
            <a href="{{URL::route('assessment.create')}}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> New Plan</a>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <table class="table table-striped table-bordered">
              <thead><tr><th>Subject</th><th>Semester</th><th>Components</th><th>CA/UE</th><th>Actions</th></tr></thead>
              <tbody>
                @forelse($plans as $p)
                <tr>
                  <td>{{$p->subject->name}} ({{$p->subject->code}})</td>
                  <td>{{$p->semester->academicYear->name}} - S{{$p->semester->semester_number}}</td>
                  <td>{{$p->components->count()}}</td>
                  <td>{{$p->ca_weight}}% / {{$p->ue_weight}}%</td>
                  <td>
                    <a href="{{URL::route('assessment.components', $p->id)}}" class="btn btn-info btn-xs"><i class="fa fa-cog"></i> Components</a>
                    <a href="{{URL::route('assessment.compute', $p->id)}}" class="btn btn-success btn-xs" onclick="return confirm('Compute grades from all marks?')"><i class="fa fa-calculator"></i> Compute</a>
                  </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center">No assessment plans yet. <a href="{{URL::route('assessment.create')}}">Create one</a>.</td></tr>
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
