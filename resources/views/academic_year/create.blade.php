@extends('layouts.master')
@section('title', 'New Academic Year')
@section('content')
<div class="right_col" role="main">
  <div class="">
    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-6 col-md-offset-3">
        <div class="x_panel">
          <div class="x_title"><h2><i class="fa fa-plus"></i> New Academic Year</h2><div class="clearfix"></div></div>
          <div class="x_content">
            <form method="post" action="{{URL::route('academic.year.store')}}">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <div class="form-group">
                <label>Academic Year <span class="required">*</span></label>
                <input type="text" name="name" class="form-control" placeholder="e.g. 2025-2026" value="{{old('name')}}" required>
                <span class="help-block">Format: YYYY-YYYY (e.g., 2025-2026)</span>
              </div>
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="is_active" value="1" {{old('is_active') ? 'checked' : ''}}>
                  Set as <strong>Active</strong> year (only one can be active)
                </label>
              </div>
              <hr>
              <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
              <a href="{{URL::route('academic.year.index')}}" class="btn btn-default">Cancel</a>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
