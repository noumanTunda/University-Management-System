@extends('layouts.master')

@section('title', 'Assessment Components')

@section('content')
<div class="right_col" role="main">
  <div class="">
    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-7">
        <div class="x_panel">
          <div class="x_title">
            <h2><i class="fa fa-list"></i> Components for: {{$plan->subject->name}}</h2>
            <small>{{$plan->semester->academicYear->name}} - Semester {{$plan->semester->semester_number}}</small>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <table class="table table-bordered">
              <thead><tr><th>Name</th><th>Type</th><th>Max Score</th><th>Weight (%)</th><th>Actions</th></tr></thead>
              <tbody>
                @forelse($plan->components as $c)
                <tr>
                  <td>{{$c->name}}</td>
                  <td><span class="label {{$c->type=='CA'?'label-primary':'label-warning'}}">{{$c->type}}</span></td>
                  <td>{{$c->max_score}}</td>
                  <td>{{$c->weight}}%</td>
                  <td>
                    <a href="{{URL::route('assessment.marks', $c->id)}}" class="btn btn-success btn-xs"><i class="fa fa-pencil"></i> Marks</a>
                    <form method="POST" action="{{URL::route('assessment.component.destroy', $c->id)}}" style="display:inline">
                      <input type="hidden" name="_token" value="{{ csrf_token() }}">
                      <input type="hidden" name="_method" value="DELETE">
                      <button class="btn btn-danger btn-xs" onclick="return confirm('Delete this component? Existing marks will be lost.')"><i class="fa fa-trash"></i></button>
                    </form>
                  </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center">No components defined yet.</td></tr>
                @endforelse
              </tbody>
              <tfoot>
                <tr class="info">
                  <th colspan="3">Totals</th>
                  <th>{{$plan->components->sum('weight')}}% / 100%</th>
                  <th></th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
      <div class="col-md-5">
        <div class="x_panel">
          <div class="x_title">
            <h2><i class="fa fa-plus"></i> Add Component</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <form method="post" action="{{URL::route('assessment.component.store', $plan->id)}}">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <div class="form-group">
                <label>Name <span class="required">*</span></label>
                <input type="text" name="name" class="form-control" placeholder="e.g., Quiz 1, Assignment, Practical, Final Exam" required>
              </div>
              <div class="form-group">
                <label>Type <span class="required">*</span></label>
                <select name="type" class="form-control" required>
                  <option value="CA">CA (Course Work — counts toward 40%)</option>
                  <option value="UE">UE (University Exam — counts toward 60%)</option>
                </select>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Max Score <span class="required">*</span></label>
                    <input type="number" name="max_score" class="form-control" placeholder="100" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Weight (%) <span class="required">*</span></label>
                    <input type="number" name="weight" class="form-control" placeholder="10" required>
                  </div>
                </div>
              </div>
              <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Add Component</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
