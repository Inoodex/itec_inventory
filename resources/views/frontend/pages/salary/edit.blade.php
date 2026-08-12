@extends('frontend.layouts.app')

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1">Edit Salary Record</h4>
                <p class="text-muted small mb-0">Update salary disbursement status, allowances, and notes</p>
            </div>
            <div>
                <a href="{{ route('salary.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3 shadow-sm">
                    <i class="fa fa-arrow-left me-2"></i>Back to Salary List
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('salary.update', $salary->id) }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Employee</label>
                        <select class="form-select border-light-subtle" disabled style="background-color: #f8f9fa;">
                            @foreach ($employees as $emp)
                                <option value="{{ $emp->id }}" {{ $salary->employee_id == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->name }} ({{ $emp->employee_id }})
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="employee_id" value="{{ $salary->employee_id }}">
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Month</label>
                        <select class="form-select border-light-subtle" disabled style="background-color: #f8f9fa;">
                            @php
                                $months = [
                                    '01' => 'January', '02' => 'February', '03' => 'March',
                                    '04' => 'April', '05' => 'May', '06' => 'June',
                                    '07' => 'July', '08' => 'August', '09' => 'September',
                                    '10' => 'October', '11' => 'November', '12' => 'December',
                                ];
                            @endphp
                            @foreach ($months as $key => $month)
                                <option value="{{ date('Y', strtotime($salary->month)) }}-{{ $key }}" {{ substr($salary->month, 5, 2) == $key ? 'selected' : '' }}>
                                    {{ $month }} {{ date('Y', strtotime($salary->month)) }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="month" value="{{ $salary->month }}">
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Basic Salary</label>
                        <input type="number" step="any" class="form-control border-light-subtle" value="{{ $salary->basic_salary }}" disabled style="background-color: #f8f9fa;">
                        <input type="hidden" name="basic_salary" value="{{ $salary->basic_salary }}">
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Advance</label>
                        <input type="number" step="any" class="form-control border-light-subtle" value="{{ $salary->advance }}" disabled style="background-color: #f8f9fa;">
                        <input type="hidden" name="advance" value="{{ $salary->advance }}">
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Allowance</label>
                        <input type="number" step="any" name="allowance" class="form-control border-light-subtle" value="{{ $salary->allowance }}">
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Deduction</label>
                        <input type="number" step="any" name="deduction" class="form-control border-light-subtle" value="{{ $salary->deduction }}">
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Payment Status <span class="text-danger">*</span></label>
                        <select name="payment_status" class="form-select border-light-subtle">
                            <option value="paid" {{ $salary->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="unpaid" {{ $salary->payment_status == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                        </select>
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Payment Date</label>
                        <input type="date" name="payment_date" class="form-control border-light-subtle" value="{{ old('payment_date', $salary->payment_date ?? date('Y-m-d')) }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Notes</label>
                        <textarea name="note" class="form-control border-light-subtle" rows="3" placeholder="Enter notes or comments">{{ old('note', $salary->note ?? '') }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 border-top pt-3 mt-4">
                    <a href="{{ route('salary.index') }}" class="btn btn-outline-secondary px-4 rounded-3">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 rounded-3">Update Salary</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
