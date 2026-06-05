@extends('layouts.master')

@section('title', 'Add Student Payment')
@section('extrastyle')
<link href="{{ URL::asset('assets/css/select2.min.css')}}" rel="stylesheet">
<link href="{{ URL::asset('assets/css/pnotify.css')}}" rel="stylesheet">
@endsection

@section('content')
<div class="right_col" role="main">
    <div class="">
        <div class="clearfix"></div>
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <form id="paymentForm" class="form-horizontal form-label-left custom-error" novalidate method="post" action="{{ URL::route('fees.addPaymentStore') }}">
                        <div class="x_title">
                            <h2>Add Student Payment <small>Enter payment details</small></h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="item form-group">
                                        <label class="control-label" for="department_id">Department <span class="required">*</span></label>
                                        {!! Form::select('department_id', $departments, null, ['placeholder' => 'Pick a department','class'=>'select2_single form-control','required'=>'required','id'=>'department_id']) !!}
                                        <span class="text-danger">{{ $errors->first('department_id') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="item form-group">
                                        <label class="control-label" for="session">Session <span class="required">*</span></label>
                                        {!! Form::select('session', $sessions, null, ['placeholder' => 'Pick a Session','class'=>'select2_single form-control','required'=>'required','id'=>'session']) !!}
                                        <span class="text-danger">{{ $errors->first('session') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="item form-group">
                                        <label class="control-label" for="student_id">Student <span class="required">*</span></label>
                                        {!! Form::select('student_id', $students->pluck('firstName','id'), null, ['placeholder' => 'Pick a student','class'=>'select2_single form-control','required'=>'required','id'=>'student_id']) !!}
                                        <span class="text-danger">{{ $errors->first('student_id') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="item form-group">
                                        <label class="control-label" for="payableAmount">Payable Amount <span class="required">*</span></label>
                                        <input type="number" step="0.01" name="payableAmount" class="form-control" required />
                                        <span class="text-danger">{{ $errors->first('payableAmount') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="item form-group">
                                        <label class="control-label" for="payDate">Pay Date <span class="required">*</span></label>
                                        <input type="date" name="payDate" class="form-control" required />
                                        <span class="text-danger">{{ $errors->first('payDate') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <button id="btnsave" type="submit" class="btn btn-success" style="margin-top: 25px;">
                                        <i class="fa fa-check"></i> Save Payment
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extrascript')
<script src="{{ URL::asset('assets/js/validator.min.js')}}"></script>
<script src="{{ URL::asset('assets/js/select2.full.min.js')}}"></script>
<script>
$(document).ready(function(){
    $('.select2_single').select2({allowClear:true});
    $('form').on('submit', function(e){
        // simple client‑side validation handled by validator.js
        if(!validator.checkAll($(this))){
            e.preventDefault();
        }
    });
});
</script>
@endsection