@extends('layouts.master')

@section('title', 'Pay Fee')
@section('extrastyle')
<link href="{{ URL::asset('assets/css/select2.min.css')}}" rel="stylesheet">
@endsection

@section('content')
<div class="right_col" role="main">
    <div class="x_panel">
        <div class="x_title">
            <h2>Pay Fee for Student <small>#{{ $feeCol->students_id }}</small></h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <form method="POST" action="{{ route('fees.pay', $feeCol->id) }}">
                {{ csrf_field() }}
                <div class="form-group">
                    <label>Payable Amount</label>
                    <input type="text" class="form-control" value="{{ number_format($feeCol->payableAmount,2) }}" readonly>
                </div>
                <div class="form-group">
                    <label>Already Paid</label>
                    <input type="text" class="form-control" value="{{ number_format($feeCol->paidAmount,2) }}" readonly>
                </div>
                <div class="form-group">
                    <label>Due Amount</label>
                    <input type="text" class="form-control" value="{{ number_format($feeCol->payableAmount - $feeCol->paidAmount,2) }}" readonly>
                </div>
                <div class="form-group">
                    <label for="payAmount">Pay Amount</label>
                    <input type="number" step="0.01" name="payAmount" id="payAmount" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success"><i class="glyphicon glyphicon-ok"></i> Submit Payment</button>
                <a href="{{ route('fees.collection.index') }}" class="btn btn-default">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection

@section('extrascript')
<script src="{{ URL::asset('assets/js/validator.min.js') }}"></script>
@endsection