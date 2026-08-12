@extends('frontend.layouts.app')

@section('content')
<div class="content container-fluid">

    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title font-weight-bold" style="color: #1e293b;">New Contra Transfer</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('contra-entries.index') }}">Contra Transfers</a></li>
                    <li class="breadcrumb-item active">New Transfer</li>
                </ul>
            </div>
            <div class="col-auto">
                <span class="badge bg-info fs-6 px-3 py-2">{{ $contraNo }}</span>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('contra-entries.store') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Transfer Date <span class="text-danger">*</span></label>
                                <input type="date" name="date" class="form-control" value="{{ old('date', $today) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Transfer Amount <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0.01" name="amount" class="form-control fw-bold" placeholder="0.00" value="{{ old('amount') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-danger"><i class="fas fa-arrow-up me-1"></i> From Account (Source / Credit) <span class="text-danger">*</span></label>
                                <select name="from_account_id" class="form-select @error('from_account_id') is-invalid @enderror" required>
                                    <option value="">-- Select Source Account --</option>
                                    @foreach($liquidAccounts as $acc)
                                        <option value="{{ $acc->id }}" {{ old('from_account_id') == $acc->id ? 'selected' : '' }}>
                                            [{{ $acc->account_code }}] {{ $acc->account_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Funds will be credited (deducted) from this account.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-success"><i class="fas fa-arrow-down me-1"></i> To Account (Destination / Debit) <span class="text-danger">*</span></label>
                                <select name="to_account_id" class="form-select @error('to_account_id') is-invalid @enderror" required>
                                    <option value="">-- Select Destination Account --</option>
                                    @foreach($liquidAccounts as $acc)
                                        <option value="{{ $acc->id }}" {{ old('to_account_id') == $acc->id ? 'selected' : '' }}>
                                            [{{ $acc->account_code }}] {{ $acc->account_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Funds will be debited (added) to this account.</small>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Transfer Description / Justification <span class="text-danger">*</span></label>
                                <textarea name="description" class="form-control" rows="3" placeholder="e.g. Cash deposit to Main Operating Bank Account..." required>{{ old('description') }}</textarea>
                            </div>

                            <div class="col-12 text-end mt-4">
                                <a href="{{ route('contra-entries.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary px-4"><i class="fas fa-exchange-alt me-1"></i> Execute Contra Transfer</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
