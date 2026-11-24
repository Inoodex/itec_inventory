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
    .stat-card .card-body > div:last-child {
        min-width: 0;
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
    .stat-icon-danger  { background: rgba(220, 53, 69, 0.12); color: #dc3545; }
    .stat-icon-primary { background: rgba(118, 56, 255, 0.12); color: #7638ff; }
    .stat-icon-success { background: rgba(25, 135, 84, 0.12); color: #198754; }
    .stat-icon-warning { background: rgba(255, 193, 7, 0.16); color: #b58105; }
    
    html[data-layout-mode="dark"] .stat-card {
        background: #1b1e23;
        border-color: #2e3840 !important;
    }
    html[data-layout-mode="dark"] .stat-card:hover {
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.35) !important;
    }

    .table-custom tbody tr {
        transition: background-color 0.15s ease;
    }
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
    #vendorDueTable tbody td a.badge,
    .table tbody td a.badge,
    .table tbody td span.badge {
        display: inline-flex !important;
        width: fit-content !important;
        max-width: fit-content !important;
        align-items: center;
        vertical-align: middle;
    }
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
    .btn-action-pay {
        background-color: rgba(25, 135, 84, 0.12) !important;
        color: #198754 !important;
        border: 1px solid rgba(25, 135, 84, 0.30) !important;
        border-radius: 8px !important;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    .btn-action-pay:hover {
        background-color: #198754 !important;
        color: #ffffff !important;
        border-color: #198754 !important;
    }
    .table-custom th, .table-custom td {
        white-space: nowrap;
    }

    /* Master Table */
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

    .vendor-master-row {
        cursor: pointer;
        user-select: none;
        transition: background-color 0.15s ease;
    }
    .vendor-master-row:hover td {
        background-color: #f8f6ff !important;
    }
    .chevron-icon {
        transition: transform 0.22s ease;
        display: inline-block;
    }
    .chevron-icon.rotated {
        transform: rotate(90deg);
    }

    /* Dual-Role Smart Chip */
    .dual-role-chip {
        display: inline-flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 6px;
        background: #fffbf2;
        border: 1px solid #fed7aa !important;
        border-radius: 8px;
        padding: 4px 9px;
        margin-top: 5px;
        font-size: 11.5px;
        line-height: 1.3;
        transition: all 0.2s ease;
    }
    .dual-role-chip:hover {
        background: #fff7ed;
        border-color: #f97316 !important;
        box-shadow: 0 2px 8px rgba(249, 115, 22, 0.12);
    }
    .dual-role-chip .chip-label {
        color: #9a3412;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .dual-role-chip .chip-divider {
        color: #fdba74;
    }
    .dual-role-chip .chip-net {
        font-weight: 700;
    }
    .dual-role-chip .btn-chip-action {
        background: #ffffff;
        border: 1px solid #fdba74;
        color: #c2410c;
        border-radius: 6px;
        padding: 2px 8px;
        font-size: 11px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 3px;
        transition: all 0.15s ease;
    }
    .dual-role-chip .btn-chip-action:hover {
        background: #ea580c;
        color: #ffffff !important;
        border-color: #ea580c;
    }

    /* Nested Child Breakdown Table */
    .vendor-child-row td {
        background: #fafbfe !important;
        padding: 0 !important;
        border-bottom: 1px solid #e8ecf3 !important;
    }
    .child-table-wrapper {
        background: #ffffff;
        border: 1px solid #e8ecf5;
        border-radius: 12px;
        padding: 16px;
        margin: 10px 16px 18px 16px;
        box-shadow: 0 4px 14px rgba(82, 63, 105, 0.04);
    }
    .child-table-wrapper table thead th {
        background: #f4f6fa !important;
        color: #555e6d !important;
        font-weight: 600;
        font-size: 11.5px;
        text-transform: uppercase;
        border: 0 !important;
        padding: 10px 12px;
    }
    .child-table-wrapper table tbody td {
        background: #ffffff !important;
        border-bottom: 1px solid #edf0f5 !important;
        padding: 10px 12px;
        font-size: 13px;
    }

    @media (min-width: 992px) {
        #vendorDueTable th.col-money,
        #vendorDueTable td.col-money {
            text-align: right;
        }
    }

    #vendorDueTableResponsive {
        max-width: 100%;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: #b9c2d0 #eef1f6;
    }
    #vendorDueTableResponsive::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    #vendorDueTableResponsive::-webkit-scrollbar-track {
        background: #eef1f6;
        border-radius: 10px;
    }
    #vendorDueTableResponsive::-webkit-scrollbar-thumb {
        background: #b9c2d0;
        border-radius: 10px;
    }
    #vendorDueTableResponsive::-webkit-scrollbar-thumb:hover {
        background: #7638ff;
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
                <p class="text-muted small mb-0">Merged supplier dues, purchase bill breakdowns, and FIFO settlement</p>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <button type="button" class="btn btn-outline-primary px-3 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2" id="btnToggleAllVendorRows">
                    <i class="fe fe-maximize-2 fs-6"></i>
                    <span id="toggleAllVendorRowsText">Expand All</span>
                </button>
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
                    <div class="stat-icon stat-icon-primary me-3">
                        <i class="fe fe-users"></i>
                    </div>
                    <div class="flex-grow-1" style="min-width: 0;">
                        <h6 class="stat-label text-muted fw-normal mb-1">Vendors with Due</h6>
                        <h4 class="stat-value mb-0 fw-bold text-dark">{{ number_format($vendorGroups->count()) }}</h4>
                        <small class="text-muted fs-8">{{ $pendingBillsCount }} pending purchase bills</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-lg-6 col-md-6 col-12">
            <div class="card stat-card shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="stat-icon stat-icon-danger me-3">
                        <i class="fe fe-dollar-sign"></i>
                    </div>
                    <div class="flex-grow-1" style="min-width: 0;">
                        <h6 class="stat-label text-muted fw-normal mb-1">Total Outstanding Dues</h6>
                        <h4 class="stat-value mb-0 fw-bold text-danger">৳{{ number_format($totalDueAmount, 2) }}</h4>
                        <small class="text-muted fs-8">Payable to suppliers</small>
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
                        <h6 class="stat-label text-muted fw-normal mb-1">Total Purchase Bills</h6>
                        <h4 class="stat-value mb-0 fw-bold text-dark">৳{{ number_format($totalBillAmount, 2) }}</h4>
                        <small class="text-muted fs-8">Cumulative purchase volume</small>
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
                        <small class="text-muted fs-8">Disbursed to date</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card shadow-sm border-0 mb-4 rounded-3">
        <div class="card-body p-3">
            <div class="row g-2 align-items-center">
                <div class="col-lg-5 col-md-6 col-12">
                    <div class="search-box-custom">
                        <input type="text" id="vendorSearchInput" class="form-control border-light-subtle" placeholder="Search vendor name, phone, purchase no (#PUR-...), or product..." autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-12">
                    <form method="GET" action="{{ route('vendor-due.index') }}" class="d-flex gap-2">
                        <select name="vendor_id" class="form-select border-light-subtle" onchange="this.form.submit()">
                            <option value="">-- All Vendors (Server Filter) --</option>
                            @foreach($vendors as $v)
                                <option value="{{ $v->id }}" {{ request('vendor_id') == $v->id ? 'selected' : '' }}>
                                    {{ $v->name }} ({{ $v->phone }})
                                </option>
                            @endforeach
                        </select>
                        @if(request()->hasAny(['vendor_id', 'from', 'to']))
                            <a href="{{ route('vendor-due.index') }}" class="btn btn-outline-secondary rounded-3" title="Reset Filters">
                                <i class="fe fe-refresh-cw"></i>
                            </a>
                        @endif
                    </form>
                </div>
                <div class="col-lg-3 col-12 text-lg-end text-muted small">
                    Showing <span id="visibleVendorCount" class="fw-bold text-dark">{{ $vendorGroups->count() }}</span> of {{ $vendorGroups->count() }} vendors
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive" id="vendorDueTableResponsive">
                <table class="table table-custom align-middle mb-0" id="vendorDueTable">
                    <thead class="bg-light text-secondary fs-7 text-uppercase">
                        <tr>
                            <th class="ps-4" style="width: 50px;">#</th>
                            <th>Vendor / Supplier</th>
                            <th>Pending Bills</th>
                            <th class="col-money">Total Bill</th>
                            <th class="col-money">Total Paid</th>
                            <th class="col-money">Outstanding Due</th>
                            <th>Party Status</th>
                            <th class="text-center pe-4" style="width: 170px;">Action</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($vendorGroups as $group)
                            @php
                                $searchKeywords = strtolower($group->name . ' ' . $group->phone . ' ' . $group->items->pluck('id')->map(fn($id)=>'pur-'.str_pad($id, 5, '0', STR_PAD_LEFT))->implode(' ') . ' ' . $group->items->pluck('product.name')->implode(' '));
                            @endphp
                            <!-- Vendor Master Row -->
                            <tr class="vendor-master-row" data-group-id="{{ $group->vendor_id }}" data-search="{{ $searchKeywords }}">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <span class="chevron-icon me-2 text-primary" id="chevron-{{ $group->vendor_id }}">
                                            <i class="fe fe-chevron-right fs-6"></i>
                                        </span>
                                        <span class="text-muted fw-semibold">{{ $loop->iteration }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <span class="fw-bold text-dark d-block">{{ Str::limit($group->name, 20) }}</span>
                                        <div class="d-flex align-items-center gap-2">
                                            <small class="text-muted"><i class="fe fe-phone me-1"></i>{{ $group->phone }}</small>
                                        </div>

                                        <!-- @if($group->dual_customer)
                                            <div class="dual-role-chip" title="Dual-Role Partner: Click 'Ledger' to view offset statement">
                                                <span class="chip-label">
                                                    <i class="fe fe-user text-warning"></i> Also Customer: <strong>৳{{ number_format($group->customer_due, 2) }}</strong>
                                                </span>
                                                <span class="chip-divider">&bull;</span>
                                                <span class="chip-net {{ $group->net_due <= 0 ? 'text-primary' : 'text-danger' }}">
                                                    Net: ৳{{ number_format(abs($group->net_due), 2) }} {{ $group->net_due <= 0 ? 'Receivable' : 'Payable' }}
                                                </span>
                                                <a href="{{ route('party-ledger.show', ['type' => 'vendor', 'id' => $group->vendor_id]) }}" 
                                                   class="btn-chip-action" 
                                                   title="Open Combined Statement & Contra Settlement">
                                                    <i class="fe fe-book-open"></i> Ledger &rarr;
                                                </a>
                                            </div>
                                        @endif -->
                                    </div>
                                </td>
                                <td>
                                    @if($group->bills_count > 1)
                                        <span class="badge badge-soft-primary px-3 py-1 rounded-pill fs-7">
                                            <i class="fe fe-layers me-1"></i>{{ $group->bills_count }} Bills
                                        </span>
                                    @else
                                        <span class="badge bg-light text-secondary border px-3 py-1 rounded-pill fs-7">
                                            1 Bill
                                        </span>
                                    @endif
                                </td>
                                <td class="col-money">
                                    <span class="badge badge-soft-primary px-3 py-1 rounded-pill fs-7">
                                        ৳{{ number_format($group->total_bill, 2) }}
                                    </span>
                                </td>
                                <td class="col-money">
                                    <span class="badge badge-soft-success px-3 py-1 rounded-pill fs-7">
                                        ৳{{ number_format($group->total_paid, 2) }}
                                    </span>
                                </td>
                                <td class="col-money">
                                    <span class="badge badge-soft-danger px-3 py-1 rounded-pill fs-7 fw-bold">
                                        ৳{{ number_format($group->total_due, 2) }}
                                    </span>
                                </td>
                                <td>
                                    @if($group->dual_customer)
                                        <a href="{{ route('party-ledger.show', ['type' => 'vendor', 'id' => $group->vendor_id]) }}" 
                                           class="badge badge-soft-warning px-3 py-1 rounded-pill fs-7 text-decoration-none d-inline-flex"
                                           style="display: inline-flex !important; width: fit-content !important;"
                                           title="Dual-Role Partner (Supplier & Customer). Click to view combined ledger">
                                            <i class="fe fe-repeat me-1"></i>Dual-Role
                                        </a>
                                    @else
                                        <span class="badge bg-light text-secondary border px-3 py-1 rounded-pill fs-7 d-inline-flex" style="display: inline-flex !important; width: fit-content !important;">
                                            Supplier
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center pe-4" onclick="event.stopPropagation();">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <button type="button" 
                                                class="btn btn-action-pay btn-sm px-3 py-1 shadow-none d-inline-flex align-items-center gap-1 open-vendor-fifo-modal"
                                                data-vendor-id="{{ $group->vendor_id }}"
                                                data-vendor-name="{{ $group->name }}"
                                                data-vendor-phone="{{ $group->phone }}"
                                                data-total-due="{{ $group->total_due }}"
                                                data-bills="{{ json_encode($group->items->map(function($item) {
                                                    return [
                                                        'id' => $item->id,
                                                        'bill_no' => 'PUR-' . str_pad($item->id, 5, '0', STR_PAD_LEFT),
                                                        'product_name' => $item->product->name ?? 'N/A',
                                                        'date' => $item->created_at ? $item->created_at->format('d M Y') : 'N/A',
                                                        'total_price' => (float)$item->total_price,
                                                        'payment' => (float)$item->payment,
                                                        'due' => (float)$item->due,
                                                    ];
                                                })) }}"
                                                title="Pay Vendor Dues (FIFO)">
                                            <i class="fe fe-credit-card"></i>
                                            <span>Pay Due</span>
                                        </button>

                                        <button type="button" class="btn btn-action-icon btn-toggle-vendor-expand" data-group-id="{{ $group->vendor_id }}" title="Expand/Collapse Bills">
                                            <i class="fe fe-chevron-down" id="actionChevron-{{ $group->vendor_id }}"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Collapsible Child Bills Breakdown -->
                            <tr class="vendor-child-row d-none" id="childRow-{{ $group->vendor_id }}">
                                <td colspan="8" class="p-0 border-0">
                                    <div class="child-table-wrapper">
                                        @if($group->dual_customer)
                                            <div class="alert alert-warning py-2 px-3 border-0 rounded-3 mb-3 d-flex flex-wrap justify-content-between align-items-center gap-2" style="background: rgba(255, 193, 7, 0.12); border-left: 4px solid #ffc107 !important;">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-warning text-dark"><i class="fe fe-repeat me-1"></i>Dual-Role Partner</span>
                                                    <span class="small text-dark">
                                                        This supplier is also registered as a Customer (<strong>{{ $group->dual_customer->name }}</strong>).
                                                        Customer Due: <strong class="text-danger">৳{{ number_format($group->customer_due, 2) }}</strong> &bull;
                                                        Net Balance: <strong class="{{ $group->net_due <= 0 ? 'text-primary' : 'text-danger' }}">৳{{ number_format(abs($group->net_due), 2) }} ({{ $group->net_due <= 0 ? 'They owe you' : 'You owe them' }})</strong>
                                                    </span>
                                                </div>
                                                <!-- <a href="{{ route('party-ledger.show', ['type' => 'vendor', 'id' => $group->vendor_id]) }}" class="btn btn-warning btn-sm px-3 py-1 text-dark fw-bold rounded-2">
                                                    <i class="fe fe-book-open me-1"></i> Combined Ledger &amp; Contra Settle &rarr;
                                                </a> -->
                                            </div>
                                        @endif

                                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 px-1 gap-2">
                                            <div>
                                                <span class="small fw-bold text-dark text-uppercase">
                                                    <i class="fe fe-file-text me-1 text-primary"></i> Pending Purchase Bills for {{ $group->name }}
                                                </span>
                                                <small class="text-muted d-block fs-8">Sorted by date (Oldest to Newest FIFO sequence)</small>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                @if($group->dual_customer)
                                                    <a href="{{ route('party-ledger.show', ['type' => 'vendor', 'id' => $group->vendor_id]) }}" class="btn btn-outline-warning btn-sm px-2 py-1 rounded-2">
                                                        <i class="fe fe-book-open me-1"></i> Combined Ledger
                                                    </a>
                                                @endif
                                                <span class="badge bg-light text-dark border px-3 py-1 rounded-pill">
                                                    {{ $group->bills_count }} bill{{ $group->bills_count > 1 ? 's' : '' }} &bull; Total Due: <strong class="text-danger">৳{{ number_format($group->total_due, 2) }}</strong>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-sm align-middle mb-0">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 120px;">Queue (FIFO)</th>
                                                        <th>Bill / Purchase No</th>
                                                        <th>Date</th>
                                                        <th>Product / Item</th>
                                                        <th class="text-end">Bill Amount</th>
                                                        <th class="text-end">Paid Amount</th>
                                                        <th class="text-end">Due Balance</th>
                                                        <th class="text-center" style="width: 120px;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($group->items as $idx => $purchase)
                                                        <tr>
                                                            <td>
                                                                <span class="badge {{ $idx === 0 ? 'bg-primary text-white' : 'bg-light text-secondary border' }} rounded-pill px-2 py-1 fs-8">
                                                                    #{{ $idx + 1 }} {{ $idx === 0 ? '(Oldest)' : '' }}
                                                                </span>
                                                            </td>
                                                            <td class="fw-bold text-primary font-monospace">
                                                                #PUR-{{ str_pad($purchase->id, 5, '0', STR_PAD_LEFT) }}
                                                            </td>
                                                            <td>
                                                                <span class="text-secondary small">
                                                                    {{ $purchase->created_at ? $purchase->created_at->format('d M Y') : 'N/A' }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span class="text-dark">{{ $purchase->product->name ?? 'N/A' }}</span>
                                                                <span class="badge bg-light text-secondary border ms-1">Qty: {{ $purchase->quantity }}</span>
                                                            </td>
                                                            <td class="text-end fw-semibold text-dark">
                                                                ৳{{ number_format($purchase->total_price, 2) }}
                                                            </td>
                                                            <td class="text-end text-success fw-semibold">
                                                                ৳{{ number_format($purchase->payment, 2) }}
                                                            </td>
                                                            <td class="text-end text-danger fw-bold">
                                                                ৳{{ number_format($purchase->due, 2) }}
                                                            </td>
                                                            <td class="text-center">
                                                                <button type="button" 
                                                                        class="btn btn-outline-primary btn-sm px-2 py-1 rounded-2"
                                                                        data-bs-toggle="modal" 
                                                                        data-bs-target="#payVendorModal-{{ $purchase->id }}">
                                                                    <i class="fe fe-credit-card me-1"></i> Pay Bill
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="avatar avatar-xl bg-success-light text-success rounded-circle mb-3 d-flex align-items-center justify-content-center">
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

<!-- FIFO Merged Vendor Payment Modal -->
<div class="modal fade" id="vendorFifoPaymentModal" tabindex="-1" aria-labelledby="vendorFifoPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header bg-light py-3 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-sm bg-primary-light text-primary rounded-circle d-flex align-items-center justify-content-center">
                        <i class="fe fe-credit-card fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="vendorFifoPaymentModalLabel">Disburse Due Payment (FIFO)</h5>
                        <small class="text-muted">Auto-allocates payment to the oldest unpaid purchase bills first</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('vendor-due.fifo-pay') }}" method="POST" id="vendorFifoPaymentForm">
                @csrf
                <input type="hidden" name="vendor_id" id="fifoVendorId">

                <div class="modal-body p-4">
                    <!-- Vendor Summary Banner -->
                    <div class="p-3 mb-3 bg-light rounded-3 border d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <span class="text-muted small d-block">Supplier / Vendor</span>
                            <h5 class="fw-bold text-dark mb-0" id="fifoVendorName">---</h5>
                            <small class="text-muted" id="fifoVendorPhone"><i class="fe fe-phone me-1"></i>---</small>
                        </div>
                        <div class="text-end">
                            <span class="text-muted small d-block">Total Outstanding Due</span>
                            <h4 class="fw-bold text-danger mb-0" id="fifoVendorTotalDueDisplay">৳0.00</h4>
                        </div>
                    </div>

                    <!-- Payment Inputs -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6 col-12">
                            <label class="form-label small text-secondary fw-semibold mb-1">
                                Payment Amount <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-light-subtle">৳</span>
                                <input type="number" step="0.01" min="0.01" class="form-control border-light-subtle fw-bold text-primary" 
                                       name="payment_amount" id="fifoVendorPaymentAmount" required autocomplete="off">
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnPayFullVendorDue" title="Set Full Due">Full</button>
                            </div>
                        </div>

                        <div class="col-md-6 col-12">
                            <label class="form-label small text-secondary fw-semibold mb-1">
                                Payment Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control border-light-subtle" name="payment_date" value="{{ now()->format('Y-m-d') }}" required>
                        </div>

                        <div class="col-md-6 col-12">
                            <label class="form-label small text-secondary fw-semibold mb-1">
                                Payment Method <span class="text-danger">*</span>
                            </label>
                            <select class="form-select border-light-subtle" name="payment_method" id="fifoVendorPaymentMethod" required>
                                @foreach($paymentMethods ?? getPaymentMethodList() as $key => $label)
                                    <option value="{{ $key }}" {{ $key == 'cash' ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 col-12">
                            <label class="form-label small text-secondary fw-semibold mb-1">
                                Pay From Account <span class="text-danger">*</span>
                            </label>
                            <select name="account_id" id="fifoVendorAccountId" class="form-select border-light-subtle" required>
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

                        <div class="col-12">
                            <label class="form-label small text-secondary fw-semibold mb-1">
                                Payment Notes / Reference (Optional)
                            </label>
                            <input type="text" class="form-control border-light-subtle" name="notes" placeholder="e.g. Bank Asia Cheque #001234 / Cash Voucher / Reference" autocomplete="off">
                        </div>
                    </div>

                    <!-- FIFO Live Allocation Preview -->
                    <div class="border rounded-3 p-3 bg-white">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-2 gap-2">
                            <span class="fw-semibold text-secondary small text-uppercase">
                                <i class="fe fe-list me-1 text-primary"></i> FIFO Allocation Breakdown (Oldest First)
                            </span>
                            <span class="small text-muted" id="fifoVendorRemainingDueSummary">Remaining Due: <strong>৳0.00</strong></span>
                        </div>
                        <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                            <table class="table table-sm table-bordered align-middle mb-0">
                                <thead class="table-light">
                                    <tr class="small text-secondary">
                                        <th style="width: 80px;">Queue</th>
                                        <th>Bill #</th>
                                        <th>Date</th>
                                        <th>Product</th>
                                        <th class="text-end">Current Due</th>
                                        <th class="text-end">Allocated</th>
                                        <th class="text-center">Status After Payment</th>
                                    </tr>
                                </thead>
                                <tbody id="fifoVendorPreviewTbody">
                                    <!-- Populated via JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light py-3 border-top d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary px-4 py-2 rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 d-inline-flex align-items-center gap-2">
                        <i class="fe fe-check-circle"></i>
                        <span>Confirm & Disburse Payment</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Pay Single Purchase Due Modals -->
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
    const searchInput = document.getElementById('vendorSearchInput');
    const masterRows = document.querySelectorAll('.vendor-master-row');
    const visibleCountSpan = document.getElementById('visibleVendorCount');
    const btnToggleAll = document.getElementById('btnToggleAllVendorRows');
    const toggleAllText = document.getElementById('toggleAllVendorRowsText');
    let allExpanded = false;

    // Toggle single vendor row
    function toggleRow(groupId) {
        const childRow = document.getElementById('childRow-' + groupId);
        const chevron = document.getElementById('chevron-' + groupId);
        const actionChevron = document.getElementById('actionChevron-' + groupId);

        if (!childRow) return;

        const isHidden = childRow.classList.contains('d-none');
        if (isHidden) {
            childRow.classList.remove('d-none');
            if (chevron) chevron.classList.add('rotated');
            if (actionChevron) actionChevron.className = 'fe fe-chevron-up';
        } else {
            childRow.classList.add('d-none');
            if (chevron) chevron.classList.remove('rotated');
            if (actionChevron) actionChevron.className = 'fe fe-chevron-down';
        }
    }

    masterRows.forEach(row => {
        row.addEventListener('click', function(e) {
            if (e.target.closest('button') || e.target.closest('a') || e.target.closest('.dropdown')) {
                return;
            }
            const groupId = this.dataset.groupId;
            toggleRow(groupId);
        });
    });

    document.querySelectorAll('.btn-toggle-vendor-expand').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const groupId = this.dataset.groupId;
            toggleRow(groupId);
        });
    });

    // Toggle All Rows
    if (btnToggleAll) {
        btnToggleAll.addEventListener('click', function() {
            allExpanded = !allExpanded;
            masterRows.forEach(row => {
                if (row.style.display !== 'none') {
                    const groupId = row.dataset.groupId;
                    const childRow = document.getElementById('childRow-' + groupId);
                    const chevron = document.getElementById('chevron-' + groupId);
                    const actionChevron = document.getElementById('actionChevron-' + groupId);

                    if (childRow) {
                        if (allExpanded) {
                            childRow.classList.remove('d-none');
                            if (chevron) chevron.classList.add('rotated');
                            if (actionChevron) actionChevron.className = 'fe fe-chevron-up';
                        } else {
                            childRow.classList.add('d-none');
                            if (chevron) chevron.classList.remove('rotated');
                            if (actionChevron) actionChevron.className = 'fe fe-chevron-down';
                        }
                    }
                }
            });
            if (toggleAllText) {
                toggleAllText.textContent = allExpanded ? 'Collapse All' : 'Expand All';
            }
        });
    }

    // Filter & Search table
    function filterTable() {
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        let visibleCount = 0;

        masterRows.forEach(row => {
            const groupId = row.dataset.groupId;
            const searchData = row.dataset.search || '';
            const childRow = document.getElementById('childRow-' + groupId);

            const matchesSearch = query === '' || searchData.includes(query);

            if (matchesSearch) {
                row.style.display = '';
                visibleCount++;

                if (query !== '' && searchData.includes(query)) {
                    if (childRow) {
                        childRow.classList.remove('d-none');
                        const chevron = document.getElementById('chevron-' + groupId);
                        const actionChevron = document.getElementById('actionChevron-' + groupId);
                        if (chevron) chevron.classList.add('rotated');
                        if (actionChevron) actionChevron.className = 'fe fe-chevron-up';
                    }
                }
            } else {
                row.style.display = 'none';
                if (childRow) childRow.classList.add('d-none');
            }
        });

        if (visibleCountSpan) {
            visibleCountSpan.textContent = visibleCount;
        }
    }

    if (searchInput) searchInput.addEventListener('input', filterTable);

    // ==========================================
    // FIFO Merged Vendor Modal Logic
    // ==========================================
    let currentBills = [];
    let currentVendorTotalDue = 0;

    const vendorFifoModalEl = document.getElementById('vendorFifoPaymentModal');
    let vendorFifoModalInstance = null;
    if (vendorFifoModalEl && typeof bootstrap !== 'undefined') {
        vendorFifoModalInstance = new bootstrap.Modal(vendorFifoModalEl);
    }

    document.querySelectorAll('.open-vendor-fifo-modal').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const vendorId = this.dataset.vendorId;
            const vendorName = this.dataset.vendorName;
            const vendorPhone = this.dataset.vendorPhone;
            currentVendorTotalDue = parseFloat(this.dataset.totalDue) || 0;

            try {
                currentBills = JSON.parse(this.dataset.bills || '[]');
            } catch(err) {
                currentBills = [];
            }

            document.getElementById('fifoVendorId').value = vendorId;
            document.getElementById('fifoVendorName').textContent = vendorName;
            document.getElementById('fifoVendorPhone').innerHTML = '<i class="fe fe-phone me-1"></i>' + vendorPhone;
            document.getElementById('fifoVendorTotalDueDisplay').textContent = '৳' + currentVendorTotalDue.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            const amountInput = document.getElementById('fifoVendorPaymentAmount');
            amountInput.max = currentVendorTotalDue;
            amountInput.value = currentVendorTotalDue.toFixed(2);

            updateVendorFifoPreview(currentVendorTotalDue);

            if (vendorFifoModalInstance) {
                vendorFifoModalInstance.show();
            }
        });
    });

    const btnPayFullVendor = document.getElementById('btnPayFullVendorDue');
    if (btnPayFullVendor) {
        btnPayFullVendor.addEventListener('click', function() {
            const amountInput = document.getElementById('fifoVendorPaymentAmount');
            amountInput.value = currentVendorTotalDue.toFixed(2);
            updateVendorFifoPreview(currentVendorTotalDue);
        });
    }

    const fifoVendorAmountInput = document.getElementById('fifoVendorPaymentAmount');
    if (fifoVendorAmountInput) {
        fifoVendorAmountInput.addEventListener('input', function() {
            const val = parseFloat(this.value) || 0;
            updateVendorFifoPreview(val);
        });
    }

    function updateVendorFifoPreview(enteredAmount) {
        const tbody = document.getElementById('fifoVendorPreviewTbody');
        const remainingSummary = document.getElementById('fifoVendorRemainingDueSummary');
        if (!tbody) return;

        let remainingPayment = Math.max(0, enteredAmount);
        let rowsHtml = '';
        let totalRemainingVendorDue = 0;

        currentBills.forEach((bill, index) => {
            const billDue = parseFloat(bill.due) || 0;
            const allocated = Math.min(remainingPayment, billDue);
            const remBillDue = Math.max(0, billDue - allocated);
            totalRemainingVendorDue += remBillDue;

            let statusHtml = '';
            if (allocated >= billDue && billDue > 0) {
                statusHtml = '<span class="badge badge-soft-success px-2 py-1 rounded-pill"><i class="fe fe-check me-1"></i>Fully Cleared</span>';
            } else if (allocated > 0) {
                statusHtml = '<span class="badge badge-soft-warning px-2 py-1 rounded-pill">Partial (৳' + remBillDue.toFixed(2) + ' left)</span>';
            } else {
                statusHtml = '<span class="badge bg-light text-secondary border px-2 py-1 rounded-pill">Unpaid (৳' + billDue.toFixed(2) + ')</span>';
            }

            rowsHtml += `
                <tr>
                    <td><span class="badge ${index === 0 ? 'bg-primary' : 'bg-light text-secondary border'} rounded-pill">#${index + 1}</span></td>
                    <td class="fw-bold font-monospace">#${bill.bill_no}</td>
                    <td><small class="text-muted">${bill.date}</small></td>
                    <td><small class="text-dark">${bill.product_name}</small></td>
                    <td class="text-end">৳${billDue.toFixed(2)}</td>
                    <td class="text-end fw-bold text-success">৳${allocated.toFixed(2)}</td>
                    <td class="text-center">${statusHtml}</td>
                </tr>
            `;

            remainingPayment -= allocated;
        });

        tbody.innerHTML = rowsHtml;
        if (remainingSummary) {
            remainingSummary.innerHTML = 'Remaining Vendor Due: <strong class="' + (totalRemainingVendorDue <= 0 ? 'text-success' : 'text-danger') + '">৳' + totalRemainingVendorDue.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</strong>';
        }
    }

    // Account filtering for FIFO modal
    const fifoMethodSelect = document.getElementById('fifoVendorPaymentMethod');
    const fifoAccountSelect = document.getElementById('fifoVendorAccountId');

    function filterFifoAccounts() {
        if (!fifoMethodSelect || !fifoAccountSelect) return;
        const method = fifoMethodSelect.value;
        let firstAvailable = null;

        Array.from(fifoAccountSelect.options).forEach(opt => {
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

        const currentOpt = fifoAccountSelect.options[fifoAccountSelect.selectedIndex];
        if (!currentOpt || currentOpt.disabled || currentOpt.hidden) {
            if (firstAvailable) {
                fifoAccountSelect.value = firstAvailable.value;
            }
        }
    }

    if (fifoMethodSelect && fifoAccountSelect) {
        fifoMethodSelect.addEventListener('change', filterFifoAccounts);
        filterFifoAccounts();
    }

    @foreach($purchases as $p)
        filterVendorAccounts({{ $p->id }});
    @endforeach
});
</script>
@endpush
