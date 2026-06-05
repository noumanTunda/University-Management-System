<div id="paymentInfoContainer">
    <h3>Payment Information for {{ $student->firstName }} {{ $student->middleName }} {{ $student->lastName }} ({{ $student->idNo }})</h3>
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
    <form id="paymentInfoForm" class="form-horizontal form-label-left custom-error" novalidate method="post" action="{{ route('fees.paymentInfoStore', ['studentId' => $student->id]) }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="row">
            <div class="col-md-4">
                <div class="item form-group">
                    <label class="control-label" for="session">Session <span class="required">*</span></label>
                    {!! Form::select('session', $sessions, null, ['placeholder' => 'Pick a Session','class'=>'select2_single form-control','required'=>'required','id'=>'session']) !!}
                    <span class="text-danger">{{ $errors->first('session') }}</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="item form-group">
                    <label class="control-label" for="fee_id">Fee Type <span class="required">*</span></label>
                    <select name="fee_id" class="select2_single form-control" required>
                        <option value="" disabled selected>Pick a fee</option>
                        @foreach($fees as $fee)
                            <option value="{{ $fee->id }}">{{ $fee->title }} ({{ $fee->amount }})</option>
                        @endforeach
                    </select>
                    <span class="text-danger">{{ $errors->first('fee_id') }}</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="item form-group">
                    <label class="control-label" for="payableAmount">Payable Amount <span class="required">*</span></label>
                    <input type="number" step="0.01" name="payableAmount" class="form-control" required />
                    <span class="text-danger">{{ $errors->first('payableAmount') }}</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="item form-group">
                    <label class="control-label" for="payDate">Pay Date <span class="required">*</span></label>
                    <input type="date" name="payDate" class="form-control" required />
                    <span class="text-danger">{{ $errors->first('payDate') }}</span>
                </div>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-success" style="margin-top: 25px;">
                    <i class="fa fa-check"></i> Save Payment
                </button>
                <a href="{{ route('fees.collection.index') }}" class="btn btn-secondary" style="margin-top: 25px; margin-left:10px;">Back</a>
            </div>
        </div>
    </form>
</div>
