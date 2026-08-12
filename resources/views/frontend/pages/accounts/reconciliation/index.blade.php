@extends('frontend.layouts.app')

@push('styles')
    <style>
        .btn-action-icon {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #dbe2ea !important;
            border-radius: 8px !important;
            background-color: #ffffff !important;
            color: #555e6d !important;
            padding: 0;
            transition: all 0.2s ease;
        }

        .btn-action-icon:hover {
            background-color: #7638ff !important;
            color: #ffffff !important;
            border-color: #7638ff !important;
        }

        .dropdown-menu {
            z-index: 9999 !important;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title font-weight-bold" style="color: #1e293b;">Bank Reconciliation</h3>
                </div>
            </div>
        </div>

        <!-- Account Selection & Statement Entry Card -->
        <div class="row mb-4">
            <div class="col-lg-6 col-12 mb-3">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold" style="color: #0f172a;">1. Select Account & As of Date</h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="GET" action="{{ route('reconciliation.index') }}">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Select Bank / Liquid Account</label>
                                <select name="account_id" class="form-select" onchange="this.form.submit()">
                                    <option value="">-- Choose Account to Reconcile --</option>
                                    @foreach($bankAccounts as $acc)
                                        <option value="{{ $acc->id }}" {{ $accountId == $acc->id ? 'selected' : '' }}>
                                            [{{ $acc->account_code }}] {{ $acc->account_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Statement Cutoff Date</label>
                                <input type="date" name="date" class="form-control" value="{{ $asOfDate }}"
                                    onchange="this.form.submit()">
                            </div>
                        </form>

                        @if($selectedAccount)
                            <div class="p-3 bg-light rounded mt-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted fw-semibold">General Ledger Book Balance:</span>
                                    <h4 class="text-primary fw-bold mb-0">৳{{ number_format($bookBalance, 2) }}</h4>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($selectedAccount)
                <div class="col-lg-6 col-12 mb-3">
                    <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="card-title mb-0 fw-bold" style="color: #0f172a;">2. Enter Bank Statement Balance</h5>
                        </div>
                        <div class="card-body p-4">
                            <form method="POST" action="{{ route('reconciliation.store') }}" id="reconForm">
                                @csrf
                                <input type="hidden" name="account_id" value="{{ $selectedAccount->id }}">
                                <input type="hidden" name="bank_statement_date" value="{{ $asOfDate }}">

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Ending Balance on Bank Statement <span
                                            class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="statement_balance" class="form-control fw-bold"
                                        placeholder="0.00" id="statementBalInput" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Audit Notes / Variance Justification</label>
                                    <textarea name="notes" class="form-control" rows="2"
                                        placeholder="e.g. Uncredited cheques or deposits in transit..."></textarea>
                                </div>

                                <div class="p-3 bg-light rounded mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted fw-semibold">Difference (Variance):</span>
                                        <h4 class="fw-bold mb-0 text-secondary" id="diffDisplay">৳0.00</h4>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-check-circle me-1"></i>
                                    Save Reconciliation Record</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Reconciliation History Table -->
        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title mb-0 fw-bold" style="color: #0f172a;">Reconciliation Audit History</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead
                            style="background-color: #1e293b; color: #ffffff; font-size: 11px; text-transform: uppercase;">
                            <tr>
                                <th class="ps-3">Account</th>
                                <th>Statement Date</th>
                                <th class="text-end">Statement Bal</th>
                                <th class="text-end">Book Bal</th>
                                <th class="text-end">Variance</th>
                                <th class="text-center">Status</th>
                                <th>Notes</th>
                                <th class="text-end pe-4" style="width: 80px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reconciliations as $rec)
                                <tr>
                                    <td class="fw-bold text-dark ps-3">[{{ $rec->account->account_code }}]
                                        {{ $rec->account->account_name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($rec->bank_statement_date)->format('d M, Y') }}</td>
                                    <td class="text-end fw-bold text-dark">৳{{ number_format($rec->statement_balance, 2) }}</td>
                                    <td class="text-end fw-semibold text-primary">৳{{ number_format($rec->book_balance, 2) }}
                                    </td>
                                    <td
                                        class="text-end fw-bold {{ abs($rec->difference) < 0.01 ? 'text-success' : 'text-danger' }}">
                                        ৳{{ number_format($rec->difference, 2) }}
                                    </td>
                                    <td class="text-center">
                                        @if(abs($rec->difference) < 0.01)
                                            <span class="badge bg-success">Reconciled</span>
                                        @else
                                            <span class="badge bg-warning">Variance</span>
                                        @endif
                                    </td>
                                    <td class="text-muted">{{ $rec->notes ?? '-' }}</td>
                                    <td class="text-end pe-4">
                                        <div class="dropdown">
                                            <a href="javascript:void(0)" class="btn-action-icon shadow-none"
                                                data-bs-toggle="dropdown" data-bs-popper-config='{"strategy":"fixed"}'
                                                aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                                <li>
                                                    <a class="dropdown-item py-2 d-flex align-items-center gap-2"
                                                        href="{{ route('ledger.index', ['account_id' => $rec->account_id]) }}">
                                                        <i class="fe fe-book text-info"></i>
                                                        <span>General Ledger</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">No reconciliation records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const statementInput = document.getElementById('statementBalInput');
            const diffDisplay = document.getElementById('diffDisplay');
            const bookBalance = {{ $bookBalance ?? 0 }};

            if (statementInput && diffDisplay) {
                statementInput.addEventListener('input', function () {
                    const statement = parseFloat(this.value) || 0;
                    const diff = statement - bookBalance;
                    diffDisplay.textContent = '৳' + diff.toFixed(2);
                    diffDisplay.className = Math.abs(diff) < 0.01 ? 'fw-bold mb-0 text-success' : 'fw-bold mb-0 text-danger';
                });
            }
        });
    </script>
@endsection