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
    .stat-card .stat-value {
        font-size: 1.3rem;
        line-height: 1.2;
    }
    .stat-card .stat-label {
        font-size: 0.8rem;
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
    .stat-icon-danger { background: rgba(220, 53, 69, 0.12); color: #dc3545; }
    .stat-icon-primary { background: rgba(13, 110, 253, 0.12); color: #0d6efd; }
    .stat-icon-success { background: rgba(25, 135, 84, 0.12); color: #198754; }
    .stat-icon-warning { background: rgba(255, 193, 7, 0.16); color: #b58105; }
    
    html[data-layout-mode="dark"] .stat-card {
        background: #1b1e23;
        border-color: #2e3840 !important;
    }
    html[data-layout-mode="dark"] .stat-card:hover {
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.35) !important;
    }

    #vendorDueTable {
        border-collapse: separate;
        border-spacing: 0;
    }
    #vendorDueTable thead {
        background: #f8f9fc !important;
        border-bottom: 2px solid #e8ecf3;
    }
    #vendorDueTable thead th {
        color: #6c757d !important;
        font-weight: 600 !important;
        font-size: 12px;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        padding: 14px;
        border: 0 !important;
        background: transparent !important;
    }
    #vendorDueTable tbody td {
        color: #3f4254 !important;
        font-size: 13.5px;
        padding: 14px;
        border: 0 !important;
        border-bottom: 1px solid #eef0f5 !important;
        vertical-align: middle;
    }
    #vendorDueTable tbody tr:hover td {
        background-color: #f8f6ff !important;
    }

    .btn-action-pay {
        padding: 6px 14px !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        border-radius: 8px !important;
        background-color: #e8fadf !important;
        color: #198754 !important;
        border: 1px solid rgba(25, 135, 84, 0.30) !important;
        transition: all 0.2s ease;
    }
    .btn-action-pay:hover {
        background-color: #198754 !important;
        color: #ffffff !important;
        border-color: #198754 !important;
    }
</style>
@endpush

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1"><i class="fe fe-truck me-2 text-danger"></i>Vendor Due Payments</h4>
                <p class="text-muted small mb-0">Overview and settlement of outstanding supplier & procurement dues</p>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <a href="{{ route('vendor-due.pdf', request()->query()) }}" target="_blank" class="btn btn-outline-danger px-3 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2">
                    <i class="fe fe-file-text fs-6"></i>
                    <span>Export PDF</span>
                </a>
                <a href="{{ route('purchase.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3 shadow-sm">
                    <i class="fa fa-arrow-left me-2"></i>    
                    Back to Purchases
                </a>
            </div>
        </div>
    </div>

    <!-- Summary Stats Bar -->
    <div class="row g-3 mb-4">
        <div class="col-xxl-3 col-lg-6 col-md-6 col-12">
            <div class="card stat-card shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="stat-icon stat-icon-danger me-3">
                        <i class="fe fe-dollar-sign"></i>
                    </div>
                    <div class="flex-grow-1" style="min-width: 0;">
                        <h6 class="stat-label text-muted fw-normal mb-1">Total Outstanding Dues</h6>
                        <h4 class="stat-value mb-0 fw-bold text-danger">৳{{ number_format($totalDueAmount, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-lg-6 col-md-6 col-12">
            <div class="card stat-card shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="stat-icon stat-icon-warning me-3">
                        <i class="fe fe-file-text"></i>
                    </div>
                    <div class="flex-grow-1" style="min-width: 0;">
                        <h6 class="stat-label text-muted fw-normal mb-1">Pending Purchase Bills</h6>
                        <h4 class="stat-value mb-0 fw-bold text-dark">{{ number_format($pendingBillsCount) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-lg-6 col-md-6 col-12">
            <div class="card stat-card shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="stat-icon stat-icon-primary me-3">
                        <i class="fe fe-users"></i>
                    </div>
                    <div class="flex-grow-1" style="min-width: 0;">
                        <h6 class="stat-label text-muted fw-normal mb-1">Suppliers with Due</h6>
                        <h4 class="stat-value mb-0 fw-bold text-dark">{{ number_format($uniqueVendorsCount) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-lg-6 col-md-6 col-12">
            <div class="card stat-card shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="stat-icon stat-icon-success me-3">
                        <i class="fe fe-check-circle"></i>
                    </div>
                    <div class="flex-grow-1" style="min-width: 0;">
                        <h6 class="stat-label text-muted fw-normal mb-1">Already Paid Amount</h6>
                        <h4 class="stat-value mb-0 fw-bold text-success">৳{{ number_format($totalPaidAmount, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card shadow-sm border-0 mb-4 rounded-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('vendor-due.index') }}" class="row g-2 align-items-center">
                <div class="col-lg-4 col-md-6 col-12">
                    <label class="form-label small fw-semibold text-secondary mb-1">Filter by Vendor</label>
                    <select name="vendor_id" class="form-select border-light-subtle">
                        <option value="">-- All Vendors --</option>
                        @foreach($vendors as $v)
                            <option value="{{ $v->id }}" {{ request('vendor_id') == $v->id ? 'selected' : '' }}>
                                {{ $v->name }} ({{ $v->phone }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-3 col-md-6 col-6">
                    <label class="form-label small fw-semibold text-secondary mb-1">From Date</label>
                    <input type="date" name="from" class="form-control border-light-subtle" value="{{ request('from') }}">
                </div>

                <div class="col-lg-3 col-md-6 col-6">
                    <label class="form-label small fw-semibold text-secondary mb-1">To Date</label>
                    <input type="date" name="to" class="form-control border-light-subtle" value="{{ request('to') }}">
                </div>

                <div class="col-lg-2 col-md-6 col-12 d-flex align-items-end gap-1 mt-auto">
                    <button type="submit" class="btn btn-primary w-100 rounded-3">
                        <i class="fe fe-filter me-1"></i> Filter
                    </button>
                    @if(request()->hasAny(['vendor_id', 'from', 'to']))
                        <a href="{{ route('vendor-due.index') }}" class="btn btn-outline-secondary rounded-3" title="Reset Filters">
                            <i class="fe fe-refresh-cw"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0" id="vendorDueTable">
                    <thead>
                        <tr>
                            <th class="ps-4">#Bill No</th>
                            <th>Vendor / Supplier</th>
                            <th>Product / Item</th>
                            <th>Date</th>
                            <th class="text-end">Bill Amount</th>
                            <th class="text-end">Paid Amount</th>
                            <th class="text-end">Due Balance</th>
                            <th class="text-center pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases as $purchase)
                            <tr>
                                <td class="ps-4 fw-bold text-dark">
                                    #PUR-{{ str_pad($purchase->id, 5, '0', STR_PAD_LEFT) }}
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark">{{ $purchase->vendor->name ?? 'Unknown Vendor' }}</span>
                                        <small class="text-muted"><i class="fe fe-phone me-1"></i>{{ $purchase->vendor->phone ?? 'N/A' }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-dark">{{ $purchase->product->name ?? 'N/A' }}</span>
                                    <span class="badge bg-light text-secondary border ms-1">Qty: {{ $purchase->quantity }}</span>
                                </td>
                                <td>
                                    <span class="text-muted small">{{ $purchase->created_at ? $purchase->created_at->format('d M, Y') : 'N/A' }}</span>
                                </td>
                                <td class="text-end fw-semibold text-dark">
                                    ৳{{ number_format($purchase->total_price, 2) }}
                                </td>
                                <td class="text-end text-success fw-semibold">
                                    ৳{{ number_format($purchase->payment, 2) }}
                                </td>
                                <td class="text-end text-danger fw-bold fs-6">
                                    ৳{{ number_format($purchase->due, 2) }}
                                </td>
                                <td class="text-center pe-4">
                                    <button type="button" 
                                            class="btn btn-action-pay shadow-sm"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#payVendorModal-{{ $purchase->id }}">
                                        <i class="fe fe-credit-card me-1"></i> Pay Due
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="py-4">
                                        <div class="avatar avatar-xl bg-success-light text-success rounded-circle mb-3 mx-auto d-flex align-items-center justify-content-center">
                                            <i class="fe fe-check-circle fs-1"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">No Outstanding Vendor Dues</h5>
                                        <p class="text-muted small mb-0">All procurement purchases and supplier bills have been fully paid!</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Pay Vendor Due Modals -->
@foreach($purchases as $purchase)
<div class="modal fade" id="payVendorModal-{{ $purchase->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header bg-light py-3 border-bottom">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="fe fe-credit-card me-2 text-primary"></i>Pay Supplier Due — #PUR-{{ str_pad($purchase->id, 5, '0', STR_PAD_LEFT) }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('vendor-due.process-payment') }}">
                @csrf
                <input type="hidden" name="purchase_id" value="{{ $purchase->id }}">

                <div class="modal-body p-4">
                    <!-- Vendor Summary Card -->
                    <div class="p-3 bg-light rounded-3 border mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <span class="text-muted small d-block">Supplier / Vendor:</span>
                            <h6 class="fw-bold text-dark mb-0">{{ $purchase->vendor->name ?? 'Vendor' }} ({{ $purchase->vendor->phone ?? '' }})</h6>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Item / Qty:</span>
                            <span class="fw-semibold text-dark">{{ $purchase->product->name ?? 'Product' }} (Qty: {{ $purchase->quantity }})</span>
                        </div>
                        <div class="text-end">
                            <span class="text-muted small d-block">Total Due Balance:</span>
                            <h5 class="fw-bold text-danger mb-0">৳{{ number_format($purchase->due, 2) }}</h5>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-lg-4 col-md-6 col-12">
                            <label class="form-label small text-secondary fw-semibold mb-1">Payment Amount <span class="text-danger">*</span></label>
                            <input type="number" 
                                   step="0.01" 
                                   class="form-control border-light-subtle fw-bold text-primary" 
                                   name="payment_amount" 
                                   id="modal_pay_amount_{{ $purchase->id }}" 
                                   max="{{ $purchase->due }}" 
                                   min="0.01" 
                                   value="{{ $purchase->due }}" 
                                   required 
                                   oninput="calcVendorRemaining({{ $purchase->id }}, {{ $purchase->due }}, this.value)">
                        </div>

                        <div class="col-lg-4 col-md-6 col-12">
                            <label class="form-label small text-secondary fw-semibold mb-1">Payment Method <span class="text-danger">*</span></label>
                            <select class="form-select border-light-subtle" name="payment_method" id="vendor_payment_method_{{ $purchase->id }}" onchange="filterVendorAccounts({{ $purchase->id }})" required>
                                @foreach($paymentMethods ?? getPaymentMethodList() as $key => $label)
                                    <option value="{{ $key }}" {{ $key == 'cash' ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-4 col-md-6 col-12">
                            <label class="form-label small text-secondary fw-semibold mb-1">Pay From Account <span class="text-danger">*</span></label>
                            <select name="account_id" id="vendor_account_id_{{ $purchase->id }}" class="form-select border-light-subtle" required>
                                @foreach($paymentAccounts ?? getPaymentAccounts() as $acc)
                                    @php
                                        $accType = (str_starts_with($acc->account_code, '1110') || stripos($acc->account_name, 'Cash') !== false) ? 'cash' : 'bank';
                                    @endphp
                                    <option value="{{ $acc->id }}" data-type="{{ $accType }}" {{ $acc->account_code == '1110' ? 'selected' : '' }}>
                                        [{{ $acc->account_code }}] {{ $acc->account_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-4 col-md-6 col-12">
                            <label class="form-label small text-secondary fw-semibold mb-1">Payment Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control border-light-subtle" name="payment_date" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="col-lg-8 col-md-6 col-12">
                            <label class="form-label small text-secondary fw-semibold mb-1">Cheque / Trx ID / Notes (Optional)</label>
                            <input type="text" class="form-control border-light-subtle" name="notes" placeholder="e.g. Bank Asia Cheque #001234 / Cash Voucher">
                        </div>

                        <div class="col-12">
                            <div class="p-2 bg-light border rounded-3 d-flex justify-content-between align-items-center">
                                <span class="small text-secondary fw-semibold"><i class="fas fa-calculator me-1"></i> Remaining Supplier Due After Payment:</span>
                                <span class="fw-bold text-danger fs-6" id="vendor_remaining_{{ $purchase->id }}">৳0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light py-2 border-top gap-2">
                    <button type="button" class="btn btn-outline-secondary px-4 py-2 rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm">
                         Disburse Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection

@push('scripts')
<script>
function calcVendorRemaining(purchaseId, totalDue, paidAmount) {
    const paid = parseFloat(paidAmount) || 0;
    const remaining = Math.max(0, totalDue - paid);
    const elem = document.getElementById('vendor_remaining_' + purchaseId);
    if (elem) {
        elem.innerText = '৳' + remaining.toFixed(2);
    }
}

function filterVendorAccounts(purchaseId) {
    const methodSelect = document.getElementById('vendor_payment_method_' + purchaseId);
    const accountSelect = document.getElementById('vendor_account_id_' + purchaseId);
    if (!methodSelect || !accountSelect) return;

    const method = methodSelect.value;
    let firstAvailable = null;

    Array.from(accountSelect.options).forEach(opt => {
        const type = opt.getAttribute('data-type');
        let visible = true;
        if (method === 'cash') {
            visible = (type === 'cash');
        } else if (method === 'bank_transfer') {
            visible = (type === 'bank');
        }

        if (visible) {
            opt.hidden = false;
            opt.disabled = false;
            opt.style.display = '';
            if (!firstAvailable) firstAvailable = opt;
        } else {
            opt.hidden = true;
            opt.disabled = true;
            opt.style.display = 'none';
        }
    });

    const currentOpt = accountSelect.options[accountSelect.selectedIndex];
    if (!currentOpt || currentOpt.disabled || currentOpt.hidden) {
        if (firstAvailable) {
            accountSelect.value = firstAvailable.value;
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    @foreach($purchases as $p)
        filterVendorAccounts({{ $p->id }});
    @endforeach
});
</script>
@endpush
