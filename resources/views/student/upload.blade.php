@extends('layouts.master')

@section('title', 'Bulk Student Upload')
@section('extrastyle')
<link href="{{ URL::asset('assets/css/select2.min.css')}}" rel="stylesheet">
@endsection

@section('content')
<div class="right_col" role="main">
    <div class="">
        <div class="clearfix"></div>
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Bulk Student Upload <small>Upload CSV file</small></h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <p class="text-muted">CSV uploads must include <strong>course_id</strong> so each student is assigned a course during admission.</p>
                        <form class="form-horizontal form-label-left" method="POST" enctype="multipart/form-data" action="{{ route('student.upload.store') }}">
                            {{ csrf_field() }}
                            <div class="item form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="students_file">CSV File <span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="file" name="students_file" id="students_file" required class="form-control" />
                                    <span class="text-danger">{{ $errors->first('students_file') }}</span>
                                </div>
                            </div>
                            <div class="ln_solid"></div>
                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-3">
                                    <button type="submit" class="btn btn-success"><i class="fa fa-upload"></i> Upload</button>
                                    <a href="{{ route('student.upload.template') }}" class="btn btn-primary"><i class="fa fa-download"></i> Download Template</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('extrascript')
<script src="{{ URL::asset('assets/js/select2.full.min.js')}}"></script>
@endsection