@if(count($students) == 0)
<div class="alert alert-warning">No students found.</div>
@else
<form method="post" action="{{URL::route('exam.marks.store')}}">
  <input type="hidden" name="_token" value="{{ csrf_token() }}">
  <input type="hidden" name="plan_id" value="{{$planId}}">
  <input type="hidden" name="subject_id" value="{{$subjectId}}">
  <input type="hidden" name="semester_id" value="{{$semesterId}}">

  <div class="table-responsive">
    <table class="table table-striped table-bordered">
      <thead>
        <tr>
          <th>#</th>
          <th>ID No</th>
          <th>Name</th>
          @foreach($components as $comp)
            <th class="{{$comp['type']=='CA'?'info':'warning'}}">
              {{$comp['name']}}
              <br><small>Max: {{$comp['max_score']}} ({{$comp['weight']}}%)</small>
            </th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        @foreach($students as $i => $s)
        <tr>
          <td>{{$i+1}}</td>
          <td>{{$s->idNo}}</td>
          <td>{{$s->firstName}} {{$s->lastName}}</td>
          @foreach($components as $comp)
            @php
              $mark = isset($marks[$s->id]) ? $marks[$s->id]->firstWhere('assessment_component_id', $comp['id']) : null;
              $val = $mark ? $mark->score : '';
            @endphp
            <td>
              @if($planId > 0)
                <input type="number" step="0.01" class="form-control" name="scores[{{$comp['id']}}][{{$s->id}}]"
                  value="{{$val}}" min="0" max="{{$comp['max_score']}}" style="width:100px">
              @else
                @if($comp['type'] == 'CA')
                  <input type="number" step="0.01" class="form-control" name="ca[{{$s->id}}]"
                    value="{{$val ?: ''}}" min="0" max="40" style="width:100px">
                @else
                  <input type="number" step="0.01" class="form-control" name="ue[{{$s->id}}]"
                    value="{{$val ?: ''}}" min="0" max="60" style="width:100px">
                @endif
              @endif
            </td>
          @endforeach
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-save"></i> Save All Marks</button>
  <div class="clearfix"></div>
</form>
@endif
