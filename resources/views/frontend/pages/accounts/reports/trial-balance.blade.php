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
                    <h3 class="page-title font-weight-bold" style="color: #1e293b;">Trial Balance</h3>
                </div>
                <div class="col-auto">
                    <a href="{{ route('trial-balance.pdf', ['as_of_date' => $asOfDate]) }}"
                        class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-file-pdf me-1"></i> Export Trial Balance PDF
                    </a>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('trial-balance.index') }}" class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <label class="form-label fw-bold mb-0">As of Date:</label>
                        <input type="date" name="as_of_date" class="form-control" value="{{ $asOfDate }}">
                    </div>
                    <div class="col-md-4 mt-auto">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-calculator me-1"></i>
                            Calculate</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Status Banner -->
        <div class="alert {{ $isBalanced ? 'alert-success' : 'alert-danger' }} shadow-sm border-0 d-flex align-items-center mb-4"
            style="border-radius: 10px;">
            <i class="fas {{ $isBalanced ? 'fa-check-circle' : 'fa-exclamation-triangle' }} fa-2x me-3"></i>
            <div>
                <h5 class="alert-heading mb-0 fw-bold">
                    {{ $isBalanced ? 'Books in Perfect Equilibrium' : 'Warning: Unbalanced Trial Balance!' }}
                </h5>
                <small>{{ $isBalanced ? 'Total debits exactly equal total credits across all active accounts.' : 'A variance was detected in journal balances.' }}</small>
            </div>
        </div>

        <!-- Trial Balance Table -->
        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead
                            style="background-color: #1e293b; color: #ffffff; font-size: 11px; text-transform: uppercase;">
                            <tr>
                                <th style="width: 150px;" class="ps-3">Account Code</th>
                                <th>Account Title & Details</th>
                                <th style="width: 140px;">Class</th>
                                <th class="text-end" style="width: 160px;">Debit</th>
                                <th class="text-end" style="width: 160px;">Credit</th>
                                <th class="text-end pe-4" style="width: 80px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rows as $r)
                                <tr>
                                    <td class="fw-bold ps-3" style="color: #334155;">{{ $r['account']->account_code }}</td>
                                    <td>
                                        <a href="{{ route('ledger.index', ['account_id' => $r['account']->id]) }}"
                                            class="fw-semibold text-primary">
                                            {{ $r['account']->account_name }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary text-uppercase" style="font-size: 9px;">
                                            {{ $r['account']->account_type }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold text-dark">
                                        {{ $r['debit'] > 0 ? '৳' . number_format($r['debit'], 2) : '-' }}
                                    </td>
                                    <td class="text-end fw-bold text-dark">
                                        {{ $r['credit'] > 0 ? '৳' . number_format($r['credit'], 2) : '-' }}
                                    </td>
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
                                                        href="{{ route('ledger.index', ['account_id' => $r['account']->id]) }}">
                                                        <i class="fe fe-book text-info"></i>
                                                        <span>General Ledger</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item py-2 d-flex align-items-center gap-2"
                                                        href="{{ route('chart-of-accounts.edit', $r['account']->id) }}">
                                                        <i class="fe fe-edit text-primary"></i>
                                                        <span>Edit Account</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">No active account balances found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot style="color: #ffffff; font-weight: 800; font-size: 14px;">
                            <tr>
                                <td colspan="3" class="text-end text-uppercase ps-3">Total Trial Balance:</td>
                                <td class="text-end text-success">৳{{ number_format($totalDebit, 2) }}</td>
                                <td class="text-end text-success">৳{{ number_format($totalCredit, 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection