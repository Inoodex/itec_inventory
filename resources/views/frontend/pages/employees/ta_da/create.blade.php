@extends('frontend.layouts.app')
@section('content')
    <div class="row justify-content-center p-3">
        <div class="col-md-8">
            <div class="card p-4 shadow">
                <h4 class="mb-3">Submit TA/DA Request</h4>

                <form method="POST" action="{{ route('employee.tada.store') }}">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Date</label>
                            <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>


                        <div class="col-md-6 mb-3">
                            <label>Amount</label>
                            <input type="number" step="any" name="amount" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Type</label>
                            <select name="type" class="form-control" required>
                                <option value="TA">TA</option>
                                <option value="DA">DA</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Payment Type</label>
                            <select name="payment_type" class="form-control" required>
                                <option value="Advance">Advance</option>
                                <option value="Claim">Claim</option>
                            </select>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label>Purpose / Notes</label>
                            <textarea name="purpose" class="form-control" rows="3" placeholder="Enter Purpose or Notes"></textarea>
                        </div>

                        <div class="col-12">
                            <button class="btn btn-primary">Submit</button>
                            <a href="{{ route('employee.tada.index') }}" class="btn btn-secondary">Back</a>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection
