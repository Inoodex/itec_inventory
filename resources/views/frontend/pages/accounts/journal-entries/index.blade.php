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

        .card, .card-body, .table-responsive {
            overflow: visible !important;
        }

        .dropdown {
            position: relative;
        }

        .dropdown-menu {
            z-index: 9999 !important;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">

        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title font-weight-bold" style="color: #1e293b;">Journal Vouchers</h3>
                </div>
                <div class="col-auto">
                    <a href="{{ route('journal-entries.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus-circle me-1"></i> New Journal Voucher
                    </a>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('journal-entries.index') }}" class="row g-2 align-items-center">
                    <div class="col-md-3">
                        <select name="reference_type" class="form-select form-select-sm">
                            <option value="">All Source Types</option>
                            <option value="sale" {{ $refType === 'sale' ? 'selected' : '' }}>Sale</option>
                            <option value="purchase" {{ $refType === 'purchase' ? 'selected' : '' }}>Purchase</option>
                            <option value="service" {{ $refType === 'service' ? 'selected' : '' }}>Service Repair</option>
                            <option value="project" {{ $refType === 'project' ? 'selected' : '' }}>Project</option>
                            <option value="expense" {{ $refType === 'expense' ? 'selected' : '' }}>Daily Expense</option>
                            <option value="salary" {{ $refType === 'salary' ? 'selected' : '' }}>Salary</option>
                            <option value="return" {{ $refType === 'return' ? 'selected' : '' }}>Return</option>
                            <option value="contra" {{ $refType === 'contra' ? 'selected' : '' }}>Contra Transfer</option>
                            <option value="manual" {{ $refType === 'manual' ? 'selected' : '' }}>Manual Voucher</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All Statuses</option>
                            <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="posted" {{ $status === 'posted' ? 'selected' : '' }}>Posted</option>
                            <option value="reversed" {{ $status === 'reversed' ? 'selected' : '' }}>Reversed</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="from" class="form-control form-control-sm" value="{{ $from }}">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="to" class="form-control form-control-sm" value="{{ $to }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100"><i class="fas fa-search me-1"></i>
                            Filter</button>
                    </div>
                    <div class="col-md-1 text-end">
                        <a href="{{ route('journal-entries.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Vouchers Table -->
        <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: visible !important;">
            <div class="card-body p-0" style="overflow: visible !important;">
                <div class="table-responsive" style="overflow: visible !important;">
                    <table class="table table-hover align-middle mb-0">
                        <thead
                            style="background-color: #1e293b; color: #ffffff; font-size: 11px; text-transform: uppercase;">
                            <tr>
                                <th style="width: 140px;" class="ps-3">Voucher #</th>
                                <th style="width: 110px;">Date</th>
                                <th style="width: 120px;">Source Type</th>
                                <th>Narration / Description</th>
                                <th class="text-end" style="width: 120px;">Debit</th>
                                <th class="text-end" style="width: 120px;">Credit</th>
                                <th class="text-center" style="width: 100px;">Status</th>
                                <th class="text-end pe-4" style="width: 80px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($entries as $entry)
                                <tr>
                                    <td class="ps-3">
                                        <a href="{{ route('journal-entries.show', $entry->id) }}" class="fw-bold text-primary">
                                            {{ $entry->journal_no }}
                                        </a>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($entry->entry_date)->format('d M, Y') }}</td>
                                    <td>
                                        <span class="badge bg-secondary text-white text-uppercase" style="font-size: 10px;">
                                            {{ $entry->reference_type }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="text-dark">{{ Str::limit($entry->description, 30) ?? 'No description' }}</div>
                                        <small class="text-muted">{{ $entry->items->count() }} split line(s)</small>
                                    </td>
                                    <td class="text-end fw-bold text-dark">{{ number_format($entry->total_debit, 2) }}</td>
                                    <td class="text-end fw-bold text-dark">{{ number_format($entry->total_credit, 2) }}</td>
                                    <td class="text-center">
                                        @if($entry->status === 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif($entry->status === 'reversed')
                                            <span class="badge bg-danger">Reversed</span>
                                        @else
                                            <span class="badge bg-warning">Posted</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="dropdown">
                                            <a href="javascript:void(0)" class="btn-action-icon shadow-none"
                                                data-bs-toggle="dropdown" data-bs-popper-config='{"strategy":"fixed"}' aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                                <li>
                                                    <a class="dropdown-item py-2 d-flex align-items-center gap-2"
                                                        href="{{ route('journal-entries.show', $entry->id) }}">
                                                        <i class="fe fe-eye text-info"></i>
                                                        <span>View Voucher</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item py-2 d-flex align-items-center gap-2"
                                                        href="{{ route('journal-entries.pdf', $entry->id) }}">
                                                        <i class="fe fe-download text-primary"></i>
                                                        <span>Download PDF</span>
                                                    </a>
                                                </li>
                                                @if($entry->status !== 'reversed')
                                                    <li>
                                                        <hr class="dropdown-divider opacity-50">
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item py-2 d-flex align-items-center gap-2 text-danger"
                                                            href="javascript:void(0)"
                                                            onclick="if(confirm('Are you sure you want to reverse this journal entry? A counter-balancing voucher will be posted.')) { document.getElementById('reverseVoucher{{ $entry->id }}').submit(); }">
                                                            <i class="fe fe-rotate-ccw text-danger"></i>
                                                            <span>Reverse Entry</span>
                                                        </a>
                                                        <form id="reverseVoucher{{ $entry->id }}" method="POST"
                                                            action="{{ route('journal-entries.reverse', $entry->id) }}"
                                                            class="d-none">
                                                            @csrf
                                                        </form>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">No journal vouchers found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($entries->hasPages())
                    <div class="card-footer bg-white border-top py-3">
                        {{ $entries->links() }}
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection