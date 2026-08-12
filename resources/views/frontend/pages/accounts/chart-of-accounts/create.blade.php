@extends('frontend.layouts.app')

@section('content')
<div class="content container-fluid">

    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title font-weight-bold" style="color: #1e293b;">Create New Account</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('chart-of-accounts.index') }}">Chart of Accounts</a></li>
                    <li class="breadcrumb-item active">New Account</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('chart-of-accounts.store') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Account Code <span class="text-danger">*</span></label>
                                <input type="text" name="account_code" class="form-control @error('account_code') is-invalid @enderror" placeholder="e.g. 1160, 5250" value="{{ old('account_code') }}" required>
                                @error('account_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Account Name / Title <span class="text-danger">*</span></label>
                                <input type="text" name="account_name" class="form-control @error('account_name') is-invalid @enderror" placeholder="e.g. Petty Cash, Legal Fees" value="{{ old('account_name') }}" required>
                                @error('account_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Account Classification <span class="text-danger">*</span></label>
                                <select name="account_type" class="form-select @error('account_type') is-invalid @enderror" required>
                                    <option value="asset" {{ old('account_type') === 'asset' ? 'selected' : '' }}>Asset (1000)</option>
                                    <option value="liability" {{ old('account_type') === 'liability' ? 'selected' : '' }}>Liability (2000)</option>
                                    <option value="equity" {{ old('account_type') === 'equity' ? 'selected' : '' }}>Equity (3000)</option>
                                    <option value="revenue" {{ old('account_type') === 'revenue' ? 'selected' : '' }}>Revenue (4000)</option>
                                    <option value="expense" {{ old('account_type') === 'expense' ? 'selected' : '' }}>Expense (5000)</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Parent Account</label>
                                <select name="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                                    <option value="">-- No Parent (Root Account) --</option>
                                    @foreach($parentAccounts as $p)
                                        <option value="{{ $p->id }}" {{ old('parent_id') == $p->id ? 'selected' : '' }}>
                                            [{{ $p->account_code }}] {{ $p->account_name }} ({{ strtoupper($p->account_type) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Link to Bank Profile</label>
                                <select name="bank_detail_id" class="form-select">
                                    <option value="">-- Optional Bank Profile --</option>
                                    @foreach($bankDetails as $bank)
                                        <option value="{{ $bank->id }}" {{ old('bank_detail_id') == $bank->id ? 'selected' : '' }}>
                                            {{ $bank->bank_name }} ({{ $bank->account_number }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Opening Balance</label>
                                <input type="number" step="0.01" name="opening_balance" class="form-control" placeholder="0.00" value="{{ old('opening_balance', '0.00') }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Account Description</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="Optional notes regarding the purpose of this account...">{{ old('description') }}</textarea>
                            </div>

                            <div class="col-12 text-end mt-4">
                                <a href="{{ route('chart-of-accounts.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i> Save Account</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
