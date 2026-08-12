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
                    <h3 class="page-title font-weight-bold" style="color: #1e293b;">Contra Transfers</h3>
                </div>
                <div class="col-auto">
                    <a href="{{ route('contra-entries.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-exchange-alt me-1"></i> New Contra Transfer
                    </a>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('contra-entries.index') }}" class="row g-2 align-items-center">
                    <div class="col-md-3">
                        <input type="date" name="from" class="form-control form-control-sm" value="{{ $from }}">
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="to" class="form-control form-control-sm" value="{{ $to }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100"><i class="fas fa-search me-1"></i>
                            Filter</button>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{ route('contra-entries.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Contra Table -->
        <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: visible !important;">
            <div class="card-body p-0" style="overflow: visible !important;">
                <div class="table-responsive" style="overflow: visible !important; min-height: 220px;">
                    <table class="table table-hover table-custom align-middle mb-0">
                        <thead
                            style="background-color: #1e293b; color: #ffffff; font-size: 11px; text-transform: uppercase;">
                            <tr>
                                <th style="width: 140px;" class="ps-3">Contra #</th>
                                <th style="width: 110px;">Date</th>
                                <th>Source Account (Credit)</th>
                                <th>Destination Account (Debit)</th>
                                <th>Description</th>
                                <th class="text-end" style="width: 140px;">Amount</th>
                                <th class="text-end pe-4" style="width: 80px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($contraEntries as $cn)
                                <tr>
                                    <td class="fw-bold text-primary ps-3">{{ $cn->contra_no }}</td>
                                    <td>{{ \Carbon\Carbon::parse($cn->date)->format('d M, Y') }}</td>
                                    <td>
                                        <span class="badge bg-danger-light text-danger me-1"><i
                                                class="fas fa-arrow-up"></i></span>
                                        <strong>[{{ $cn->fromAccount->account_code }}]</strong>
                                        {{ $cn->fromAccount->account_name }}
                                    </td>
                                    <td>
                                        <span class="badge bg-success-light text-success me-1"><i
                                                class="fas fa-arrow-down"></i></span>
                                        <strong>[{{ $cn->toAccount->account_code }}]</strong> {{ $cn->toAccount->account_name }}
                                    </td>
                                    <td class="text-muted">{{ $cn->description ?? '-' }}</td>
                                    <td class="text-end fw-bold text-dark">৳{{ number_format($cn->amount, 2) }}</td>
                                    <td class="text-end pe-4">
                                        <div class="dropdown">
                                            <a href="javascript:void(0)" class="btn-action-icon shadow-none"
                                                data-bs-toggle="dropdown" data-bs-popper-config='{"strategy":"fixed"}'
                                                aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                                @if($cn->journal_entry_id)
                                                    <li>
                                                        <a class="dropdown-item py-2 d-flex align-items-center gap-2"
                                                            href="{{ route('journal-entries.show', $cn->journal_entry_id) }}">
                                                            <i class="fe fe-eye text-info"></i>
                                                            <span>View Journal Voucher</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item py-2 d-flex align-items-center gap-2"
                                                            href="{{ route('journal-entries.pdf', $cn->journal_entry_id) }}">
                                                            <i class="fe fe-download text-primary"></i>
                                                            <span>Download Voucher PDF</span>
                                                        </a>
                                                    </li>
                                                @endif
                                                <li>
                                                    <a class="dropdown-item py-2 d-flex align-items-center gap-2"
                                                        href="{{ route('ledger.index', ['account_id' => $cn->from_account_id]) }}">
                                                        <i class="fe fe-book text-warning"></i>
                                                        <span>Source Ledger</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item py-2 d-flex align-items-center gap-2"
                                                        href="{{ route('ledger.index', ['account_id' => $cn->to_account_id]) }}">
                                                        <i class="fe fe-book text-success"></i>
                                                        <span>Dest Ledger</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">No contra transfers recorded.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($contraEntries->hasPages())
                    <div class="card-footer bg-white border-top py-3">
                        {{ $contraEntries->links() }}
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection