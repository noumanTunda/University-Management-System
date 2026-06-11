@extends('layouts.master')

@section('title', 'Enter Marks')

@section('content')
<div class="right_col" role="main">
  <div class="">
    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12">
        <div class="x_panel">
          <div class="x_title">
            <h2><i class="fa fa-pencil"></i> {{$comp->name}} — {{$comp->plan->subject->name}}</h2>
            <small>{{$comp->type}} | Max: {{$comp->max_score}} | Weight: {{$comp->weight}}%</small>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <form method="post" action="{{URL::route('assessment.marks.store', $comp->id)}}">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <table class="table table-striped table-bordered" id="marksTable">
                <thead><tr><th>#</th><th>Student ID</th><th>Name</th><th>Score (max {{$comp->max_score}})</th></tr></thead>
                <tbody>
                  @forelse($students as $i => $s)
                  <tr>
                    <td>{{$i+1}}</td>
                    <td>{{$s->idNo}}</td>
                    <td>{{$s->firstName}} {{$s->lastName}}</td>
                    <td>
                      <input type="number" step="0.01" class="form-control" name="scores[{{$s->id}}]"
                        value="{{ isset($marks[$s->id]) ? $marks[$s->id]->score : '' }}"
                        min="0" max="{{$comp->max_score}}" style="width:120px">
                    </td>
                  </tr>
                  @empty
                  <tr><td colspan="4" class="text-center">No students found.</td></tr>
                  @endforelse
                </tbody>
              </table>
              @if(count($students) > 0)
              <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-save"></i> Save Marks</button>
              @endif
              <a href="{{URL::route('assessment.components', $comp->plan->id)}}" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back</a>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
