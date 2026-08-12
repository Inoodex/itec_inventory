@extends('frontend.layouts.app')

@section('content')
<div class="content container-fluid">

    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title font-weight-bold" style="color: #1e293b;">Edit Account: {{ $chartOfAccount->account_code }}</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('chart-of-accounts.index') }}">Chart of Accounts</a></li>
                    <li class="breadcrumb-item active">Edit Account</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('chart-of-accounts.update', $chartOfAccount->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Account Code</label>
                                <input type="text" class="form-control" value="{{ $chartOfAccount->account_code }}" readonly disabled>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Account Name / Title <span class="text-danger">*</span></label>
                                <input type="text" name="account_name" class="form-control @error('account_name') is-invalid @enderror" value="{{ old('account_name', $chartOfAccount->account_name) }}" required>
                                @error('account_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Account Classification</label>
                                <input type="text" class="form-control text-uppercase" value="{{ $chartOfAccount->account_type }}" readonly disabled>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Parent Account</label>
                                <select name="parent_id" class="form-select @error('parent_id') is-invalid @enderror" {{ $chartOfAccount->is_system ? 'disabled' : '' }}>
                                    <option value="">-- No Parent (Root Account) --</option>
                                    @foreach($parentAccounts as $p)
                                        <option value="{{ $p->id }}" {{ old('parent_id', $chartOfAccount->parent_id) == $p->id ? 'selected' : '' }}>
                                            [{{ $p->account_code }}] {{ $p->account_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Opening Balance</label>
                                <input type="number" step="0.01" name="opening_balance" class="form-control" value="{{ old('opening_balance', $chartOfAccount->opening_balance) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Account Status</label>
                                <select name="is_active" class="form-select">
                                    <option value="1" {{ $chartOfAccount->is_active ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ !$chartOfAccount->is_active ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Account Description</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description', $chartOfAccount->description) }}</textarea>
                            </div>

                            <div class="col-12 text-end mt-4">
                                <a href="{{ route('chart-of-accounts.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i> Update Account</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
