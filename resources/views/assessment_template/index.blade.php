@extends('layouts.master')
@section('title', 'Assessment Templates')
@section('content')
<div class="right_col" role="main">
  <div class="">
    <div class="row">
      <div class="col-md-12">
        <div class="x_panel">
          <div class="x_title">
            <h2><i class="fa fa-file-text-o"></i> Assessment Templates</h2>
            <a href="{{URL::route('assessment.template.create')}}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> New Template</a>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            @foreach($templates as $t)
            <div class="panel panel-default">
              <div class="panel-heading">
                <strong>{{$t->name}}</strong>
                <small>{{$t->description}}</small>
                <span class="label label-info pull-right">CA {{$t->ca_weight}}% / UE {{$t->ue_weight}}%</span>
              </div>
              <div class="panel-body">
                <table class="table table-bordered table-sm">
                  <tr><th>Component</th><th>Type</th><th>Max Score</th><th>Weight</th></tr>
                  @foreach($t->components as $c)
                  <tr>
                    <td>{{$c->name}}</td>
                    <td><span class="label label-{{$c->type=='CA'?'primary':'warning'}}">{{$c->type}}</span></td>
                    <td>{{$c->max_score}}</td>
                    <td>{{$c->weight}}%</td>
                  </tr>
                  @endforeach
                </table>
                <a href="{{URL::route('assessment.template.edit', $t->id)}}" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i></a>
                <form method="POST" action="{{URL::route('assessment.template.destroy', $t->id)}}" style="display:inline" onsubmit="return confirm('Delete this template?')">
                  <input type="hidden" name="_token" value="{{csrf_token()}}">
                  <button class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></button>
                </form>
              </div>
            </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
