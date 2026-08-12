@extends('frontend.layouts.app')

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1">{{ isset($bankDetail) ? 'Edit' : 'Add' }} Bank Account</h4>
                <p class="text-muted small mb-0">Configure company bank account information, branch, and routing number</p>
            </div>
            <div>
                <a href="{{ route('bank-details.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3 shadow-sm">
                    <i class="fa fa-arrow-left me-2"></i>Back to Bank Accounts
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">
            <form action="{{ isset($bankDetail) ? route('bank-details.update', $bankDetail->id) : route('bank-details.store') }}" method="POST">
                @csrf
                @if (isset($bankDetail))
                    @method('PUT')
                @endif

                <div class="row g-3 mb-4">
                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Account Name <span class="text-danger">*</span></label>
                        <input type="text" name="account_name" class="form-control border-light-subtle" value="{{ old('account_name', $bankDetail->account_name ?? '') }}" placeholder="Enter account holder name" required>
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Bank Name <span class="text-danger">*</span></label>
                        <input type="text" name="bank_name" class="form-control border-light-subtle" value="{{ old('bank_name', $bankDetail->bank_name ?? '') }}" placeholder="Enter bank name" required>
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Branch <span class="text-danger">*</span></label>
                        <input type="text" name="branch" class="form-control border-light-subtle" value="{{ old('branch', $bankDetail->branch ?? '') }}" placeholder="Enter branch location" required>
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Account Number <span class="text-danger">*</span></label>
                        <input type="text" name="account_number" class="form-control border-light-subtle font-monospace" value="{{ old('account_number', $bankDetail->account_number ?? '') }}" placeholder="Enter account number" required>
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Account Type <span class="text-danger">*</span></label>
                        <select name="account_type" class="form-select border-light-subtle" required>
                            <option value="">Select Account Type</option>
                            <option value="Current" {{ old('account_type', $bankDetail->account_type ?? '') == 'Current' ? 'selected' : '' }}>Current</option>
                            <option value="Savings" {{ old('account_type', $bankDetail->account_type ?? '') == 'Savings' ? 'selected' : '' }}>Savings</option>
                            <option value="Salary" {{ old('account_type', $bankDetail->account_type ?? '') == 'Salary' ? 'selected' : '' }}>Salary</option>
                        </select>
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Routing Number <span class="text-danger">*</span></label>
                        <input type="text" name="routing_number" class="form-control border-light-subtle font-monospace" value="{{ old('routing_number', $bankDetail->routing_number ?? '') }}" placeholder="Enter routing number" required>
                    </div>

                    <div class="col-12 mt-4">
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input type="checkbox" name="is_default" value="1" {{ old('is_default', $bankDetail->is_default ?? false) ? 'checked' : '' }} class="form-check-input" id="is_default">
                                <label class="form-check-label text-dark fw-semibold" for="is_default">Set as Default Billing Account</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $bankDetail->is_active ?? true) ? 'checked' : '' }} class="form-check-input" id="is_active">
                                <label class="form-check-label text-dark fw-semibold" for="is_active">Active Status</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 border-top pt-3">
                    <a href="{{ route('bank-details.index') }}" class="btn btn-outline-secondary px-4 rounded-3">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 rounded-3">
                        {{ isset($bankDetail) ? 'Update Account' : 'Save Account' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
