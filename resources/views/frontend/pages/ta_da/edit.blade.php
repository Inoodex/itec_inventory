@extends('frontend.layouts.app')

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1">Edit TA/DA Record</h4>
                <p class="text-muted small mb-0">Update allowance details, amount, and payment type</p>
            </div>
            <div>
                <a href="{{ route('ta-da.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3 shadow-sm">
                    <i class="fa fa-arrow-left me-2"></i>Back to TA/DA List
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">
            <form action="{{ route('ta-da.update', $tada->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3 mb-4">
                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Employee <span class="text-danger">*</span></label>
                        <select name="employee_id" class="form-select border-light-subtle" required>
                            <option value="">Select Employee</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" {{ $tada->employee_id == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->employee_id }} - {{ $employee->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control border-light-subtle" value="{{ $tada->date }}" required>
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Allowance Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select border-light-subtle" required>
                            <option value="TA" {{ $tada->type == 'TA' ? 'selected' : '' }}>TA (Travel Allowance)</option>
                            <option value="DA" {{ $tada->type == 'DA' ? 'selected' : '' }}>DA (Daily Allowance)</option>
                        </select>
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Amount (৳) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" step="0.01" class="form-control border-light-subtle" value="{{ $tada->amount }}" required>
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Payment Type <span class="text-danger">*</span></label>
                        <select name="payment_type" class="form-select border-light-subtle" required>
                            <option value="Advance" {{ $tada->payment_type == 'Advance' ? 'selected' : '' }}>Advance</option>
                            <option value="Claim" {{ $tada->payment_type == 'Claim' ? 'selected' : '' }}>Claim</option>
                        </select>
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Purpose / Notes</label>
                        <textarea name="purpose" class="form-control border-light-subtle" rows="3" placeholder="Enter purpose or travel details">{{ old('purpose', $tada->purpose ?? '') }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 border-top pt-3">
                    <a href="{{ route('ta-da.index') }}" class="btn btn-outline-secondary px-4 rounded-3">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 rounded-3">Update Record</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
