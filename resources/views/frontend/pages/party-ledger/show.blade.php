@extends('frontend.layouts.app')

@push('styles')
<style>
    .stat-card {
        background: linear-gradient(145deg, #ffffff 0%, #f8f9fd 100%);
        border: 1px solid rgba(118, 56, 255, 0.10) !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(82, 63, 105, 0.12) !important;
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 20px;
    }
    .stat-icon-danger  { background: rgba(220, 53, 69, 0.12); color: #dc3545; }
    .stat-icon-primary { background: rgba(118, 56, 255, 0.12); color: #7638ff; }
    .stat-icon-success { background: rgba(25, 135, 84, 0.12); color: #198754; }
    .stat-icon-warning { background: rgba(255, 193, 7, 0.16); color: #b58105; }

    .badge-soft-success {
        background-color: rgba(25, 135, 84, 0.12) !important;
        color: #198754 !important;
        font-weight: 600;
    }
    .badge-soft-danger {
        background-color: rgba(220, 53, 69, 0.12) !important;
        color: #dc3545 !important;
        font-weight: 600;
    }
    .badge-soft-info {
        background-color: rgba(13, 202, 240, 0.12) !important;
        color: #0dcaf0 !important;
        font-weight: 600;
    }
    .badge-soft-warning {
        background-color: rgba(255, 193, 7, 0.15) !important;
        color: #b58105 !important;
        font-weight: 600;
    }
    .badge-soft-primary {
        background-color: rgba(118, 56, 255, 0.12) !important;
        color: #7638ff !important;
        font-weight: 600;
    }

    #ledgerTable thead th {
        background: #f8f9fc !important;
        color: #6c757d !important;
        font-weight: 600 !important;
        font-size: 12px;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        padding: 13px;
        border: 0 !important;
    }
    #ledgerTable tbody td {
        color: #3f4254 !important;
        font-size: 13px;
        padding: 12px 14px;
        border: 0 !important;
        border-bottom: 1px solid #eef0f5 !important;
        vertical-align: middle;
    }
    #ledgerTable tbody tr:hover td {
        background-color: #f8f6ff !important;
    }
</style>
@endpush

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1">
                    <i class="fe fe-book-open me-2 text-primary"></i>Combined Customer &amp; Vendor Ledger
                </h4>
                <p class="text-muted small mb-0">Unified statement of accounts, dual-role transactions, and contra netting</p>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                @if($maxContraSettle > 0 && $customer && $vendor)
                    <button type="button" class="btn btn-warning px-3 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2 text-dark fw-semibold" data-bs-toggle="modal" data-bs-target="#contraSettleModal">
                        <i class="fe fe-repeat fs-6"></i>
                        <span>Settle Contra Due (Netting)</span>
                    </button>
                @endif
                <a href="{{ route('due-payments.index') }}" class="btn btn-outline-primary px-3 py-2 rounded-3 shadow-sm">
                    <i class="fe fe-users me-1"></i> Customer Dues
                </a>
                <a href="{{ route('vendor-due.index') }}" class="btn btn-outline-secondary px-3 py-2 rounded-3 shadow-sm">
                    <i class="fe fe-truck me-1"></i> Vendor Dues
                </a>
            </div>
        </div>
    </div>

    <!-- Party Info Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center g-3">
                <div class="col-lg-7 col-md-12">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar avatar-xl bg-primary-light text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0">
                            <i class="fe fe-briefcase fs-3"></i>
                        </div>
                        <div>
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                <h4 class="fw-bold text-dark mb-0">{{ $customer->name ?? $vendor->name ?? 'Party Statement' }}</h4>
                                @if($customer)
                                    <span class="badge badge-soft-info px-2 py-1 rounded-pill"><i class="fe fe-user me-1"></i>Customer #{{ $customer->id }}</span>
                                @endif
                                @if($vendor)
                                    <span class="badge badge-soft-warning px-2 py-1 rounded-pill"><i class="fe fe-truck me-1"></i>Vendor #{{ $vendor->id }}</span>
                                @endif
                                @if($customer && $vendor)
                                    <span class="badge bg-success text-white px-2 py-1 rounded-pill"><i class="fe fe-check-circle me-1"></i>Dual Role Linked</span>
                                @endif
                            </div>
                            <div class="text-muted small d-flex flex-wrap align-items-center gap-3">
                                <span><i class="fe fe-phone me-1 text-primary"></i>{{ $customer->phone ?? $vendor->phone ?? 'N/A' }}</span>
                                @if(!empty($customer->email ?? $vendor->email))
                                    <span><i class="fe fe-mail me-1 text-info"></i>{{ $customer->email ?? $vendor->email }}</span>
                                @endif
                                @if(!empty($customer->address ?? $vendor->address))
                                    <span><i class="fe fe-map-pin me-1 text-secondary"></i>{{ $customer->address ?? $vendor->address }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 col-md-12">
                    <div class="p-3 bg-light rounded-3 border">
                        <form method="GET" action="{{ route('party-ledger.show', ['type' => $type, 'id' => $customer->id ?? $vendor->id]) }}" class="row g-2 align-items-center">
                            @if($type === 'customer')
                                <div class="col-8">
                                    <label class="form-label small text-secondary fw-semibold mb-1">Linked Vendor Profile</label>
                                    <select name="vendor_id" class="form-select form-select-sm border-light-subtle" onchange="this.form.submit()">
                                        <option value="">-- Auto-matched by Phone --</option>
                                        @foreach($allVendors as $v)
                                            <option value="{{ $v->id }}" {{ ($vendor && $vendor->id == $v->id) ? 'selected' : '' }}>
                                                {{ $v->name }} ({{ $v->phone }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @else
                                <div class="col-8">
                                    <label class="form-label small text-secondary fw-semibold mb-1">Linked Customer Profile</label>
                                    <select name="customer_id" class="form-select form-select-sm border-light-subtle" onchange="this.form.submit()">
                                        <option value="">-- Auto-matched by Phone --</option>
                                        @foreach($allCustomers as $c)
                                            <option value="{{ $c->id }}" {{ ($customer && $customer->id == $c->id) ? 'selected' : '' }}>
                                                {{ $c->name }} ({{ $c->phone }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="col-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary btn-sm w-100">Sync Link</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial KPI Summary Bar -->
    <div class="row g-3 mb-4">
        <!-- Customer Receivable Due -->
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-2">
                        <div class="stat-icon stat-icon-primary me-3">
                            <i class="fe fe-arrow-down-left"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Receivable from Customer</span>
                            <h4 class="fw-bold text-primary mb-0">৳{{ number_format($customerDue, 2) }}</h4>
                        </div>
                    </div>
                    <div class="border-top pt-2 text-muted fs-8 d-flex justify-content-between">
                        <span>Sales: ৳{{ number_format($customerTotalSales, 2) }}</span>
                        <span>Paid: ৳{{ number_format($customerTotalPaid, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vendor Payable Due -->
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-2">
                        <div class="stat-icon stat-icon-danger me-3">
                            <i class="fe fe-arrow-up-right"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Payable to Vendor</span>
                            <h4 class="fw-bold text-danger mb-0">৳{{ number_format($vendorDue, 2) }}</h4>
                        </div>
                    </div>
                    <div class="border-top pt-2 text-muted fs-8 d-flex justify-content-between">
                        <span>Purchases: ৳{{ number_format($vendorTotalPurchases, 2) }}</span>
                        <span>Paid: ৳{{ number_format($vendorTotalPaid, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- NET OUTSTANDING DUE -->
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card shadow-sm rounded-3 h-100 mb-0 border-2 {{ $netBalance > 0 ? 'border-primary' : ($netBalance < 0 ? 'border-danger' : 'border-success') }}">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-2">
                        <div class="stat-icon {{ $netBalance > 0 ? 'stat-icon-primary' : ($netBalance < 0 ? 'stat-icon-danger' : 'stat-icon-success') }} me-3">
                            <i class="fe fe-layers"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">NET DUE BALANCE</span>
                            <h4 class="fw-bold mb-0 {{ $netBalance > 0 ? 'text-primary' : ($netBalance < 0 ? 'text-danger' : 'text-success') }}">
                                ৳{{ number_format(abs($netBalance), 2) }}
                            </h4>
                        </div>
                    </div>
                    <div class="border-top pt-2 text-muted fs-8">
                        @if($netBalance > 0)
                            <span class="text-primary fw-semibold"><i class="fe fe-info me-1"></i>Net Receivable (They owe us)</span>
                        @elseif($netBalance < 0)
                            <span class="text-danger fw-semibold"><i class="fe fe-alert-circle me-1"></i>Net Payable (We owe them)</span>
                        @else
                            <span class="text-success fw-semibold"><i class="fe fe-check me-1"></i>Fully Settled &amp; Balanced</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Max Contra Settlable -->
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-2">
                        <div class="stat-icon stat-icon-warning me-3">
                            <i class="fe fe-repeat"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Available Contra Offset</span>
                            <h4 class="fw-bold text-dark mb-0">৳{{ number_format($maxContraSettle, 2) }}</h4>
                        </div>
                    </div>
                    <div class="border-top pt-2 text-muted fs-8">
                        @if($maxContraSettle > 0)
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#contraSettleModal" class="text-warning fw-bold text-decoration-none">
                                <i class="fe fe-zap me-1"></i> Offset ৳{{ number_format($maxContraSettle, 2) }} Contra
                            </a>
                        @else
                            <span class="text-muted">No common offset available</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chronological Unified Ledger Table Card -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-dark mb-0"><i class="fe fe-list me-2 text-primary"></i>Combined Transaction Timeline</h5>
                <small class="text-muted">Chronological history of sales invoices, receipts, procurement bills, and vendor disbursements</small>
            </div>
            <span class="badge bg-light text-secondary border px-3 py-1 rounded-pill">
                {{ $ledgerEntries->count() }} Total Transactions
            </span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0" id="ledgerTable">
                    <thead>
                        <tr>
                            <th class="ps-4">Date</th>
                            <th>Role</th>
                            <th>Transaction Type</th>
                            <th>Reference #</th>
                            <th>Description</th>
                            <th class="text-end">Customer Dr (+)</th>
                            <th class="text-end">Customer Cr (-)</th>
                            <th class="text-end">Vendor Cr (+)</th>
                            <th class="text-end">Vendor Dr (-)</th>
                            <th class="text-center pe-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ledgerEntries as $entry)
                            <tr>
                                <td class="ps-4 text-secondary small">
                                    {{ $entry->date ? $entry->date->format('d M Y') : 'N/A' }}
                                </td>
                                <td>
                                    @if($entry->party_role === 'Customer')
                                        <span class="badge badge-soft-info px-2 py-1 rounded-pill">Customer</span>
                                    @else
                                        <span class="badge badge-soft-warning px-2 py-1 rounded-pill">Vendor</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $entry->type }}</span>
                                </td>
                                <td>
                                    @if($entry->link)
                                        <a href="{{ $entry->link }}" target="_blank" class="fw-bold text-primary font-monospace text-decoration-none">
                                            #{{ $entry->ref_no }} <i class="fe fe-external-link fs-8"></i>
                                        </a>
                                    @else
                                        <span class="fw-bold text-dark font-monospace">#{{ $entry->ref_no }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted small">{{ Str::limit($entry->description, 35) }}</span>
                                </td>
                                <td class="text-end fw-semibold text-primary">
                                    {{ $entry->party_role === 'Customer' && $entry->debit > 0 ? '৳' . number_format($entry->debit, 2) : '—' }}
                                </td>
                                <td class="text-end fw-semibold text-success">
                                    {{ $entry->party_role === 'Customer' && $entry->credit > 0 ? '৳' . number_format($entry->credit, 2) : '—' }}
                                </td>
                                <td class="text-end fw-semibold text-danger">
                                    {{ $entry->party_role === 'Vendor' && $entry->credit > 0 ? '৳' . number_format($entry->credit, 2) : '—' }}
                                </td>
                                <td class="text-end fw-semibold text-success">
                                    {{ $entry->party_role === 'Vendor' && $entry->debit > 0 ? '৳' . number_format($entry->debit, 2) : '—' }}
                                </td>
                                <td class="text-center pe-4">
                                    @if($entry->status === 'Paid' || $entry->status === 'Completed')
                                        <span class="badge badge-soft-success px-2 py-1 rounded-pill">Paid</span>
                                    @elseif($entry->status === 'Partial')
                                        <span class="badge badge-soft-warning px-2 py-1 rounded-pill">Partial</span>
                                    @else
                                        <span class="badge badge-soft-danger px-2 py-1 rounded-pill">Unpaid</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5">
                                    <p class="text-muted mb-0">No transactions recorded for this combined party profile.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Contra Netting Settlement Modal -->
@if($maxContraSettle > 0 && $customer && $vendor)
<div class="modal fade" id="contraSettleModal" tabindex="-1" aria-labelledby="contraSettleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header bg-light py-3 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-sm bg-warning-light text-warning rounded-circle d-flex align-items-center justify-content-center">
                        <i class="fe fe-repeat fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="contraSettleModalLabel">Contra Due Settlement (Netting)</h5>
                        <small class="text-muted">Offsets Customer Due directly against Vendor Due via double-entry adjustment</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('party-ledger.contra-settle') }}" method="POST">
                @csrf
                <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                <input type="hidden" name="vendor_id" value="{{ $vendor->id }}">

                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 rounded-3 mb-4">
                        <div class="d-flex gap-2">
                            <i class="fe fe-info fs-5 mt-1"></i>
                            <div>
                                <h6 class="fw-bold mb-1">How Contra Netting Works:</h6>
                                <p class="small mb-0">
                                    Customer <strong>{{ $customer->name }}</strong> owes you <strong>৳{{ number_format($customerDue, 2) }}</strong>, and as a Vendor you owe them <strong>৳{{ number_format($vendorDue, 2) }}</strong>.
                                    Applying a contra settlement reduces both balances simultaneously by the same amount without moving cash. An auto-journal entry (Debit Accounts Payable 2110, Credit Accounts Receivable 1130) will be recorded.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6 col-12">
                            <label class="form-label small text-secondary fw-semibold mb-1">
                                Contra Settlement Amount <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-light-subtle">৳</span>
                                <input type="number" step="0.01" min="0.01" max="{{ $maxContraSettle }}" 
                                       class="form-control border-light-subtle fw-bold text-primary" 
                                       name="settlement_amount" value="{{ $maxContraSettle }}" required>
                            </div>
                            <small class="text-muted">Maximum settlable offset: <strong>৳{{ number_format($maxContraSettle, 2) }}</strong></small>
                        </div>

                        <div class="col-md-6 col-12">
                            <label class="form-label small text-secondary fw-semibold mb-1">
                                Settlement Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control border-light-subtle" name="settlement_date" value="{{ now()->format('Y-m-d') }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label small text-secondary fw-semibold mb-1">
                                Settlement Notes / Reference
                            </label>
                            <input type="text" class="form-control border-light-subtle" name="notes" placeholder="e.g. Mutual contra offset agreement #01" autocomplete="off">
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light py-3 border-top d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary px-4 py-2 rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning px-4 py-2 rounded-3 shadow-sm text-dark fw-bold d-inline-flex align-items-center gap-2">
                        <i class="fe fe-check-circle"></i>
                        <span>Confirm Contra Netting</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
