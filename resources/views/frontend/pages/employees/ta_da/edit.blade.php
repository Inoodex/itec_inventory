@extends('frontend.layouts.app')
@section('content')
    <div class="row justify-content-center p-3">
        <div class="col-md-8">
            <div class="card p-4 shadow">
                <h4 class="mb-3">Submit TA/DA Request</h4>

                <form action="{{ route('employee.tada.update', $tadas->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label>Date</label>
                        <input type="date" class="form-control"
                            value="{{ \Carbon\Carbon::parse($tadas->date)->format('Y-m-d') }}" readonly>
                    </div>

                    <div class="mb-3">
                        <label>Type</label>
                        <input type="text" class="form-control" value="{{ $tadas->type }}" readonly>
                    </div>

                    <div class="mb-3">
                        <label>Payment Type</label>
                        <input type="text" class="form-control" value="{{ $tadas->payment_type }}" readonly>
                    </div>

                    <div class="mb-3">
                        <label>Total Amount</label>
                        <input type="number" class="form-control" value="{{ $tadas->amount }}" readonly>
                    </div>

                    <div class="mb-3">
                        <label>Purpose / Notes</label>
                        <textarea class="form-control" readonly>{{ $tadas->purpose }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label>Used Amount</label>
                        <input type="number" name="used_amount" class="form-control"
                            value="{{ old('used_amount', $tadas->used_amount) }}" required>
                    </div>

                    <div class="mb-3">
                        <label>Remaining Amount</label>
                        <input type="number" class="form-control" value="{{ $tadas->amount - $tadas->used_amount }}"
                            readonly>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>


            </div>
        </div>
    </div>
@endsection
