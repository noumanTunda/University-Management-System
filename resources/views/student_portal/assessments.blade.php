@extends('layouts.master')
@section('title', 'My Results')
@section('content')
<div class="right_col" role="main">
  <div class="">
    <h3><i class="fa fa-bar-chart"></i> My Assessment Results</h3>
    <div class="row">
      <div class="col-md-12">
        @forelse($grouped as $key => $rows)
          @php
            list($year, $sem) = explode('|', $key);
            $collapseId = 'sem_' . str_replace(['/', ' '], '_', $year) . '_' . $sem;
            $totalCredits = 0; $totalPoints = 0; $count = 0;
          @endphp
          <div class="panel panel-default">
            <div class="panel-heading" role="tab" style="cursor:pointer" data-toggle="collapse" data-target="#{{$collapseId}}" aria-expanded="true">
              <h4 class="panel-title">
                <i class="fa fa-chevron-down"></i> <strong>{{$year}}</strong> — Semester {{$sem}}
                <span class="pull-right">
                  @foreach($rows as $r)
                    @php if($r->grade_point) { $totalPoints += $r->grade_point; $count++; } @endphp
                  @endforeach
                  <small>{{count($rows)}} subjects</small>
                </span>
              </h4>
            </div>
            <div id="{{$collapseId}}" class="panel-collapse collapse in">
              <div class="panel-body">
                <table class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Subject Code</th>
                      <th>Subject Name</th>
                      <th>CA (40%)</th>
                      <th>UE (60%)</th>
                      <th>Total</th>
                      <th>Grade</th>
                      <th>GP</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($rows as $i => $r)
                    <tr>
                      <td>{{$i+1}}</td>
                      <td>{{$r->subject->code ?? '-'}}</td>
                      <td>{{$r->subject->name ?? '-'}}</td>
                      <td>{{number_format($r->ca_score ?? 0, 1)}}</td>
                      <td>{{number_format($r->ue_score ?? 0, 1)}}</td>
                      <td><strong>{{number_format(($r->ca_score ?? 0) + ($r->ue_score ?? 0), 1)}}</strong></td>
                      <td><span class="label label-{{$r->status=='Pass'?'success':'danger'}}">{{$r->grade_letter ?? '-'}}</span></td>
                      <td>{{number_format($r->grade_point ?? 0, 2)}}</td>
                      <td>{{$r->status ?? '-'}}</td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        @empty
          <div class="alert alert-info">No results found.</div>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection

@section('extrascript')
<script>
$(document).ready(function() {
    $('.panel-heading').on('click', function() {
        var icon = $(this).find('.fa-chevron-down, .fa-chevron-right');
        icon.toggleClass('fa-chevron-down fa-chevron-right');
    });
});
</script>
@endsection
