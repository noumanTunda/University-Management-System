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
                                        <select name="student_id" id="student_id" class="select2_single form-control" required>
                                            <option value="" disabled selected>Pick a student</option>
                                            @foreach($students as $stu)
                                                <option value="{{ $stu->id }}">
                                                    {{ $stu->firstName }} {{ $stu->middleName }} {{ $stu->lastName }} ({{ $stu->idNo }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger">{{ $errors->first('student_id') }}</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Payable amount, Pay Date and action buttons -->
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
                                <div class="col-md-4" style="margin-top:25px;">
                                    <button id="btnsave" type="submit" class="btn btn-success">
                                        <i class="fa fa-check"></i> Save Payment
                                    </button>
                                    <button type="button" id="btnGetInfo" class="btn btn-info" style="margin-left:10px;">
                                        Get Payment Info
                                    </button>
                                </div>
                            </div>
                            <!-- Section to display fetched payment info -->
                            <div id="paymentInfo" class="row" style="margin-top:20px; display:none;">
                                <div class="col-md-12">
                                    <div class="alert alert-info">
                                        <strong>Total Due Amount:</strong> <span id="dueAmount">0</span>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label" for="fee_type">Fee Type <span class="required">*</span></label>
                                        <select name="fee_type" id="fee_type" class="form-control" required>
                                            <option value="" disabled selected>Pick a fee type</option>
                                        </select>
                                    </div>
                                    <!-- Table showing due amount per category -->
                                    <div id="dueBreakdown" style="margin-top:15px;">
                                        <h5>Due by Category</h5>
                                        <table class="table table-bordered" id="dueTable">
                                            <thead>
                                                <tr>
                                                    <th>Category</th>
                                                    <th>Due Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Total Due</th>
                                                    <th id="totalDueFooter">0</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
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
    $('#btnGetInfo').on('click', function(){
        var studentId = $('#student_id').val();
        var session = $('#session').val();
        if(!studentId){
            alert('Please select a student first');
            return;
        }
        var url = '{{ route('fees.paymentInfoData', ['studentId' => '__ID__']) }}';
        url = url.replace('__ID__', studentId);
        if(session){
            url += '?session=' + encodeURIComponent(session);
        }
        // Fetch payment info via AJAX
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(data){
                // Show total due amount
                $('#dueAmount').text(data.due);
                // Populate fee type dropdown
                var $feeSelect = $('#fee_type');
                $feeSelect.empty();
                $feeSelect.append('<option value="" disabled selected>Pick a fee type</option>');
                $.each(data.fees, function(i, fee){
                    var amt = Number(fee.amount).toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2});
                    $feeSelect.append('<option value="' + fee.id + '">' + fee.title + ' (' + amt + ')</option>');
                });
                // Populate due‑by‑category table
                var $tbody = $('#dueTable tbody');
                $tbody.empty();
                var total = 0;
                $.each(data.dueDetails, function(i, item){
                    var dueAmt = Number(item.due).toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2});
                    $tbody.append('<tr><td>' + item.title + '</td><td>' + dueAmt + '</td></tr>');
                    total += parseFloat(item.due) || 0;
                });
                var totalFormatted = Number(total).toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2});
                $('#totalDueFooter').text(totalFormatted);
                $('#paymentInfo').show();
            },
            error: function(){
                alert('Unable to fetch payment information.');
            }
        });
    });
    $('form').on('submit', function(e){
        if(!validator.checkAll($(this))){
            e.preventDefault();
        }
    });
});
</script>
@endsection