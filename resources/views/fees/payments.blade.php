@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Payments for {{ $student->firstName }} {{ $student->lastName }}</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Payable Amount</th>
                <th>Paid Amount</th>
                <th>Due Amount</th>
                <th>Pay Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($feeCollections as $index => $fee)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $fee->payableAmount }}</td>
                    <td>{{ $fee->paidAmount }}</td>
                    <td>{{ $fee->dueAmount }}</td>
                    <td>{{ $fee->payDate }}</td>
                    <td>
                        <a href="{{ route('fees.payForm', $fee->id) }}" class="btn btn-sm btn-primary">Add Installment</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <a href="{{ route('fees.collection.index') }}" class="btn btn-secondary">Back to Collections</a>
</div>
@endsection