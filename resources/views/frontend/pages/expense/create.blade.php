@extends('frontend.layouts.app')

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1">Add Daily Expense</h4>
                <p class="text-muted small mb-0">Record operational expense transactions and payment methods</p>
            </div>
            <div>
                <a href="{{ route('dailyExpenses.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3 shadow-sm">
                    <i class="fa fa-arrow-left me-2"></i>Back to Daily Expenses
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">
            <form action="{{ route('dailyExpenses.store') }}" method="post">
                @csrf

                <div class="row g-3 mb-4">
                    <div class="col-lg-4 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control border-light-subtle" value="{{ old('date', now()->format('Y-m-d')) }}" required autocomplete="off">
                    </div>

                    <div class="col-lg-4 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Employee</label>
                        <select id="employeeSelect" class="form-select border-light-subtle" name="employee_id">
                            <option value="">Select Employee</option>
                            @foreach ($employees as $emp)
                                <option value="{{ $emp->id }}" data-salary="{{ $emp->salary }}">
                                    {{ $emp->name }} ({{ $emp->employee_id }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-4 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Expense Category <span class="text-danger">*</span></label>
                        <select name="expense_category_id" class="form-select border-light-subtle" required>
                            <option value="">Select Category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('expense_category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-4 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Amount <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" class="form-control border-light-subtle" placeholder="Enter amount (৳)" value="{{ old('amount') }}" required autocomplete="off">
                    </div>

                    <div class="col-lg-4 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Spend Method <span class="text-danger">*</span></label>
                        <select name="spend_method" class="form-select border-light-subtle" required>
                            <option value="">Select Spend Method</option>
                            <option value="cash" {{ old('spend_method') == 'cash' ? 'selected' : '' }}>Cash Payment</option>
                            <option value="card" {{ old('spend_method') == 'card' ? 'selected' : '' }}>Card Payment</option>
                            <option value="bank_transfer" {{ old('spend_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer / Other</option>
                        </select>
                    </div>

                    <div class="col-lg-4 col-md-12 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Remarks <span class="text-danger">*</span></label>
                        <textarea name="remarks" class="form-control border-light-subtle" rows="2" placeholder="Enter remarks or details" required>{{ old('remarks') }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 border-top pt-3">
                    <a href="{{ route('dailyExpenses.index') }}" class="btn btn-outline-secondary px-4 rounded-3">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 rounded-3">Save Daily Expense</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
