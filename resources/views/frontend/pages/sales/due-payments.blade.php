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
    .stat-icon-info    { background: rgba(13, 202, 240, 0.14); color: #0dcaf0; }
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
    #duePaymentsTable tbody td a.badge,
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

    /* ===== Master Table Styling ===== */
    #duePaymentsTable {
        border-collapse: separate;
        border-spacing: 0;
    }
    #duePaymentsTable thead {
        background: #f8f9fc !important;
        background-image: none !important;
        border-bottom: 2px solid #e8ecf3;
    }
    #duePaymentsTable thead th {
        color: #6c757d !important;
        font-weight: 600 !important;
        font-size: 12px;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        padding: 14px;
        border: 0 !important;
        background: transparent !important;
    }
    #duePaymentsTable tbody td {
        color: #3f4254 !important;
        font-size: 13.5px;
        padding: 14px;
        border: 0 !important;
        border-bottom: 1px solid #eef0f5 !important;
        vertical-align: middle;
    }

    .customer-master-row {
        cursor: pointer;
        user-select: none;
        transition: background-color 0.15s ease;
    }
    .customer-master-row:hover td {
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
    .customer-child-row td {
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

    /* Right-align money columns on laptop/desktop */
    @media (min-width: 992px) {
        #duePaymentsTable th.col-money,
        #duePaymentsTable td.col-money {
            text-align: right;
        }
    }

    /* Responsive overflow */
    #duePaymentsTableResponsive {
        max-width: 100%;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: #b9c2d0 #eef1f6;
    }
    #duePaymentsTableResponsive::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    #duePaymentsTableResponsive::-webkit-scrollbar-track {
        background: #eef1f6;
        border-radius: 10px;
    }
    #duePaymentsTableResponsive::-webkit-scrollbar-thumb {
        background: #b9c2d0;
        border-radius: 10px;
    }
    #duePaymentsTableResponsive::-webkit-scrollbar-thumb:hover {
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
                <h4 class="card-title fw-bold text-dark mb-1">Customer Due Payments</h4>
                <p class="text-muted small mb-0">Overview and FIFO settlement of outstanding customer & client dues</p>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <button type="button" class="btn btn-outline-primary px-3 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2" id="btnToggleAllRows">
                    <i class="fe fe-maximize-2 fs-6"></i>
                    <span id="toggleAllRowsText">Expand All</span>
                </button>
                <a href="{{ route('due-payments.pdf') }}" target="_blank" class="btn btn-outline-danger px-3 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2">
                    <i class="fe fe-file-text fs-6"></i>
                    <span>Export PDF</span>
                </a>
                <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3 shadow-sm">
                    <i class="fa fa-arrow-left me-2"></i>    
                    Back to Sales
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Summary Stats Bar -->
    <div class="row g-3 mb-4">
        <div class="col-xxl-3 col-lg-6 col-md-6 col-12">
            <div class="card stat-card shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="stat-icon stat-icon-primary me-3">
                        <i class="fe fe-users"></i>
                    </div>
                    <div class="flex-grow-1" style="min-width: 0;">
                        <h6 class="stat-label text-muted fw-normal mb-1">Customers with Due</h6>
                        <h4 class="stat-value mb-0 fw-bold text-dark">{{ number_format($customerGroups->count()) }}</h4>
                        <small class="text-muted fs-8">{{ $sales->count() }} pending invoices</small>
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
                        <h4 class="stat-value mb-0 fw-bold text-danger">৳{{ number_format($sales->sum('due_payment'), 2) }}</h4>
                        <small class="text-muted fs-8">Across all retail & project sales</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-lg-6 col-md-6 col-12">
            <div class="card stat-card shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="stat-icon stat-icon-info me-3">
                        <i class="fe fe-tag"></i>
                    </div>
                    <div class="flex-grow-1" style="min-width: 0;">
                        <h6 class="stat-label text-muted fw-normal mb-1">Retail Due Orders</h6>
                        <h4 class="stat-value mb-0 fw-bold text-dark">{{ number_format($sales->where('sale_type', 'retail')->count()) }}</h4>
                        <small class="text-muted fs-8">{{ $customerGroups->where('type', 'retail')->count() }} retail customers</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-lg-6 col-md-6 col-12">
            <div class="card stat-card shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="stat-icon stat-icon-warning me-3">
                        <i class="fe fe-briefcase"></i>
                    </div>
                    <div class="flex-grow-1" style="min-width: 0;">
                        <h6 class="stat-label text-muted fw-normal mb-1">Project Due Orders</h6>
                        <h4 class="stat-value mb-0 fw-bold text-dark">{{ number_format($sales->where('sale_type', 'project')->count()) }}</h4>
                        <small class="text-muted fs-8">{{ $customerGroups->where('type', 'project')->count() }} project clients</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Summary Stats Bar -->

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-3">
        <!-- Filter Controls -->
        <div class="card-header bg-white py-3 border-bottom border-light">
            <div class="row align-items-center g-3">
                <div class="col-12 col-md-6 col-lg-5">
                    <div class="search-box-custom">
                        <input type="text" id="dueSearchInput" class="form-control border-light-subtle" placeholder="Search customer, phone, or invoice no (#INV-...)" autocomplete="off">
                    </div>
                </div>
                <div class="col-12 col-md-3 col-lg-3">
                    <select id="dueTypeFilterSelect" class="form-select border-light-subtle">
                        <option value="all">All Party Types</option>
                        <option value="retail">Retail Customers</option>
                        <option value="project">Project Clients</option>
                    </select>
                </div>
                <div class="col-12 col-md-3 col-lg-4 text-md-end text-muted small">
                    Showing <span id="visibleDueCount" class="fw-bold text-dark">{{ $customerGroups->count() }}</span> of {{ $customerGroups->count() }} customers
                </div>
            </div>
        </div>

        <!-- Table Body -->
        <div class="card-body p-0">
            <div class="table-responsive" id="duePaymentsTableResponsive">
                <table class="table table-custom align-middle mb-0" id="duePaymentsTable">
                    <thead class="bg-light text-secondary fs-7 text-uppercase">
                        <tr>
                            <th class="ps-4" style="width: 50px;">#</th>
                            <th>Customer / Client</th>
                            <th>Pending Invoices</th>
                            <th class="col-money">Total Payable</th>
                            <th class="col-money">Total Paid</th>
                            <th class="col-money">Outstanding Due</th>
                            <th>Type</th>
                            <th class="text-center pe-4" style="width: 170px;">Action</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($customerGroups as $group)
                            @php
                                $searchKeywords = strtolower($group->name . ' ' . $group->phone . ' ' . $group->type . ' ' . $group->items->pluck('order_no')->implode(' '));
                            @endphp
                            <!-- Customer Master Row -->
                            <tr class="customer-master-row" data-group-id="{{ $group->id }}" data-search="{{ $searchKeywords }}" data-type="{{ strtolower($group->type) }}">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <span class="chevron-icon me-2 text-primary" id="chevron-{{ $group->id }}">
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

                                        <!-- @if(!empty($group->dual_vendor))
                                            <div class="dual-role-chip" title="Dual-Role Partner: Click 'Ledger' to view offset statement">
                                                <span class="chip-label">
                                                    <i class="fe fe-truck text-warning"></i> Also Vendor: <strong>৳{{ number_format($group->vendor_due, 2) }}</strong>
                                                </span>
                                                <span class="chip-divider">&bull;</span>
                                                <span class="chip-net {{ $group->net_due >= 0 ? 'text-primary' : 'text-danger' }}">
                                                    Net: ৳{{ number_format(abs($group->net_due), 2) }} {{ $group->net_due >= 0 ? 'Receivable' : 'Payable' }}
                                                </span>
                                                @if($group->customer_id)
                                                    <a href="{{ route('party-ledger.show', ['type' => 'customer', 'id' => $group->customer_id]) }}" 
                                                       class="btn-chip-action" 
                                                       title="Open Combined Statement & Contra Settlement">
                                                        <i class="fe fe-book-open"></i> Ledger &rarr;
                                                    </a>
                                                @endif
                                            </div>
                                        @endif -->
                                    </div>
                                </td>
                                <td>
                                    @if($group->invoices_count > 1)
                                        <span class="badge badge-soft-primary px-3 py-1 rounded-pill fs-7">
                                            <i class="fe fe-layers me-1"></i>{{ $group->invoices_count }} Invoices
                                        </span>
                                    @else
                                        <span class="badge bg-light text-secondary border px-3 py-1 rounded-pill fs-7">
                                            1 Invoice
                                        </span>
                                    @endif
                                </td>
                                <td class="col-money">
                                    <span class="badge badge-soft-primary px-3 py-1 rounded-pill fs-7">
                                        ৳{{ number_format($group->total_payable, 2) }}
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
                                    @if(!empty($group->dual_vendor))
                                        @if($group->customer_id)
                                            <a href="{{ route('party-ledger.show', ['type' => 'customer', 'id' => $group->customer_id]) }}" 
                                               class="badge badge-soft-warning px-3 py-1 rounded-pill fs-7 text-decoration-none d-inline-flex"
                                               style="display: inline-flex !important; width: fit-content !important;"
                                               title="Dual-Role Partner (Customer & Vendor). Click to view combined ledger">
                                                <i class="fe fe-repeat me-1"></i>Dual-Role
                                            </a>
                                        @else
                                            <span class="badge badge-soft-warning px-3 py-1 rounded-pill fs-7 d-inline-flex" style="display: inline-flex !important; width: fit-content !important;">
                                                <i class="fe fe-repeat me-1"></i>Dual-Role
                                            </span>
                                        @endif
                                    @elseif($group->type == 'project')
                                        <span class="badge badge-soft-warning px-3 py-1 rounded-pill text-capitalize fs-7 d-inline-flex" style="display: inline-flex !important; width: fit-content !important;">
                                            Project
                                        </span>
                                    @else
                                        <span class="badge badge-soft-info px-3 py-1 rounded-pill text-capitalize fs-7 d-inline-flex" style="display: inline-flex !important; width: fit-content !important;">
                                            Retail
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center pe-4" onclick="event.stopPropagation();">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        @if($group->type == 'retail' && $group->customer_id)
                                            <button type="button" 
                                                    class="btn btn-action-pay btn-sm px-3 py-1 shadow-none d-inline-flex align-items-center gap-1 open-fifo-modal"
                                                    data-customer-id="{{ $group->customer_id }}"
                                                    data-customer-name="{{ $group->name }}"
                                                    data-customer-phone="{{ $group->phone }}"
                                                    data-total-due="{{ $group->total_due }}"
                                                    data-invoices="{{ json_encode($group->items->map(function($item) {
                                                        return [
                                                            'id' => $item->id,
                                                            'order_no' => $item->order_no,
                                                            'date' => $item->created_at ? $item->created_at->format('d M Y') : 'N/A',
                                                            'payable' => (float)$item->payble,
                                                            'paid' => (float)$item->advanced_payment,
                                                            'due' => (float)$item->due_payment,
                                                        ];
                                                    })) }}"
                                                    title="Collect Merged Due (FIFO)">
                                                <i class="fe fe-credit-card"></i>
                                                <span>Collect Due</span>
                                            </button>
                                        @elseif($group->type == 'project')
                                            <a href="{{ route('projects.payments', $group->customer_id) }}" class="btn btn-action-pay btn-sm px-3 py-1 shadow-none d-inline-flex align-items-center gap-1" title="Pay Project Due">
                                                <i class="fe fe-credit-card"></i>
                                                <span>Pay Due</span>
                                            </a>
                                        @else
                                            <a href="{{ route('sales.payments', $group->items->first()->id) }}" class="btn btn-action-pay btn-sm px-3 py-1 shadow-none d-inline-flex align-items-center gap-1" title="Pay Invoice Due">
                                                <i class="fe fe-credit-card"></i>
                                                <span>Pay Due</span>
                                            </a>
                                        @endif

                                        <button type="button" class="btn btn-action-icon btn-toggle-expand" data-group-id="{{ $group->id }}" title="Expand/Collapse Invoices">
                                            <i class="fe fe-chevron-down" id="actionChevron-{{ $group->id }}"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Collapsible Child Sales Breakdown -->
                            <tr class="customer-child-row d-none" id="childRow-{{ $group->id }}">
                                <td colspan="8" class="p-0 border-0">
                                    <div class="child-table-wrapper">
                                        @if(!empty($group->dual_vendor))
                                            <div class="alert alert-warning py-2 px-3 border-0 rounded-3 mb-3 d-flex flex-wrap justify-content-between align-items-center gap-2" style="background: rgba(255, 193, 7, 0.12); border-left: 4px solid #ffc107 !important;">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-warning text-dark"><i class="fe fe-repeat me-1"></i>Dual-Role Partner</span>
                                                    <span class="small text-dark">
                                                        This customer is also registered as a Supplier/Vendor (<strong>{{ $group->dual_vendor->name }}</strong>).
                                                        Vendor Due: <strong class="text-danger">৳{{ number_format($group->vendor_due, 2) }}</strong> &bull;
                                                        Net Balance: <strong class="{{ $group->net_due >= 0 ? 'text-primary' : 'text-danger' }}">৳{{ number_format(abs($group->net_due), 2) }} ({{ $group->net_due >= 0 ? 'They owe you' : 'You owe them' }})</strong>
                                                    </span>
                                                </div>
                                                <!-- <a href="{{ route('party-ledger.show', ['type' => 'customer', 'id' => $group->customer_id]) }}" class="btn btn-warning btn-sm px-3 py-1 text-dark fw-bold rounded-2">
                                                    <i class="fe fe-book-open me-1"></i> Combined Ledger &amp; Contra Settle &rarr;
                                                </a> -->
                                            </div>
                                        @endif

                                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 px-1 gap-2">
                                            <div>
                                                <span class="small fw-bold text-dark text-uppercase">
                                                    <i class="fe fe-file-text me-1 text-primary"></i> Pending Invoices for {{ $group->name }}
                                                </span>
                                                <small class="text-muted d-block fs-8">Sorted by date (Oldest to Newest FIFO sequence)</small>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                @if(!empty($group->dual_vendor))
                                                    <a href="{{ route('party-ledger.show', ['type' => 'customer', 'id' => $group->customer_id]) }}" class="btn btn-outline-warning btn-sm px-2 py-1 rounded-2">
                                                        <i class="fe fe-book-open me-1"></i> Combined Ledger
                                                    </a>
                                                @endif
                                                <span class="badge bg-light text-dark border px-3 py-1 rounded-pill">
                                                    {{ $group->invoices_count }} invoice{{ $group->invoices_count > 1 ? 's' : '' }} &bull; Total Due: <strong class="text-danger">৳{{ number_format($group->total_due, 2) }}</strong>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-sm align-middle mb-0">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 120px;">Queue (FIFO)</th>
                                                        <th>Invoice / Order No</th>
                                                        <th>Date</th>
                                                        <th>Type</th>
                                                        <th class="text-end">Payable</th>
                                                        <th class="text-end">Paid</th>
                                                        <th class="text-end">Due</th>
                                                        <th class="text-center" style="width: 120px;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($group->items as $idx => $item)
                                                        <tr>
                                                            <td>
                                                                <span class="badge {{ $idx === 0 ? 'bg-primary text-white' : 'bg-light text-secondary border' }} rounded-pill px-2 py-1 fs-8">
                                                                    #{{ $idx + 1 }} {{ $idx === 0 ? '(Oldest)' : '' }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                @if($item->sale_type == 'project')
                                                                    <span class="fw-bold text-primary font-monospace">#{{ $item->order_no }}</span>
                                                                @else
                                                                    <a href="{{ route('sales.invoice.pdf', $item->id) }}" target="_blank" class="fw-bold text-primary font-monospace text-decoration-none" title="Download Invoice PDF">
                                                                        #{{ $item->order_no }} <i class="fe fe-external-link fs-8 ms-1"></i>
                                                                    </a>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <span class="text-secondary small">
                                                                    {{ $item->created_at ? $item->created_at->format('d M Y') : 'N/A' }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                @if($item->sale_type == 'project')
                                                                    <span class="badge badge-soft-warning px-2 py-1 rounded-pill text-capitalize fs-8">Project</span>
                                                                @else
                                                                    <span class="badge badge-soft-info px-2 py-1 rounded-pill text-capitalize fs-8">Retail</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-end fw-semibold text-dark">
                                                                ৳{{ number_format($item->payble, 2) }}
                                                            </td>
                                                            <td class="text-end text-success fw-semibold">
                                                                ৳{{ number_format($item->advanced_payment, 2) }}
                                                            </td>
                                                            <td class="text-end text-danger fw-bold">
                                                                ৳{{ number_format($item->due_payment, 2) }}
                                                            </td>
                                                            <td class="text-center">
                                                                @if($item->sale_type == 'project')
                                                                    <a href="{{ route('projects.payments', $item->id) }}" class="btn btn-outline-primary btn-sm px-2 py-1 rounded-2" title="Pay this project">
                                                                        <i class="fe fe-credit-card me-1"></i> Pay
                                                                    </a>
                                                                @else
                                                                    <a href="{{ route('sales.payments', $item->id) }}" class="btn btn-outline-primary btn-sm px-2 py-1 rounded-2" title="Pay this invoice">
                                                                        <i class="fe fe-credit-card me-1"></i> Pay
                                                                    </a>
                                                                @endif
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
                                        <h5 class="fw-bold text-dark mb-1">No Due Payments Outstanding</h5>
                                        <p class="text-muted small mb-0">All retail customer invoices and projects are fully settled!</p>
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

<!-- FIFO Merged Payment Modal -->
<div class="modal fade" id="fifoPaymentModal" tabindex="-1" aria-labelledby="fifoPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header bg-light py-3 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-sm bg-primary-light text-primary rounded-circle d-flex align-items-center justify-content-center">
                        <i class="fe fe-credit-card fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="fifoPaymentModalLabel">Collect Due Payment (FIFO)</h5>
                        <small class="text-muted">Auto-allocates payment to the oldest unpaid invoices first</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('due-payments.customer-pay') }}" method="POST" id="fifoPaymentForm">
                @csrf
                <input type="hidden" name="customer_id" id="fifoCustomerId">

                <div class="modal-body p-4">
                    <!-- Customer Summary Banner -->
                    <div class="p-3 mb-3 bg-light rounded-3 border d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <span class="text-muted small d-block">Customer</span>
                            <h5 class="fw-bold text-dark mb-0" id="fifoCustomerName">---</h5>
                            <small class="text-muted" id="fifoCustomerPhone"><i class="fe fe-phone me-1"></i>---</small>
                        </div>
                        <div class="text-end">
                            <span class="text-muted small d-block">Total Outstanding Due</span>
                            <h4 class="fw-bold text-danger mb-0" id="fifoTotalDueDisplay">৳0.00</h4>
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
                                       name="payment_amount" id="fifoPaymentAmount" required autocomplete="off">
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnPayFullDue" title="Set Full Due">Full</button>
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
                            <select class="form-select border-light-subtle" name="payment_method" id="fifoPaymentMethod" required>
                                @foreach($paymentMethods ?? getPaymentMethodList() as $key => $label)
                                    <option value="{{ $key }}" {{ $key == 'cash' ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 col-12">
                            <label class="form-label small text-secondary fw-semibold mb-1">
                                Deposit To Account <span class="text-danger">*</span>
                            </label>
                            <select name="account_id" id="fifoAccountId" class="form-select border-light-subtle" required>
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
                            <input type="text" class="form-control border-light-subtle" name="notes" placeholder="e.g. Money receipt #, cheque no, transaction ref..." autocomplete="off">
                        </div>
                    </div>

                    <!-- FIFO Live Allocation Preview -->
                    <div class="border rounded-3 p-3 bg-white">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-2 gap-2">
                            <span class="fw-semibold text-secondary small text-uppercase">
                                <i class="fe fe-list me-1 text-primary"></i> FIFO Allocation Breakdown (Oldest First)
                            </span>
                            <span class="small text-muted" id="fifoRemainingDueSummary">Remaining Customer Due: <strong>৳0.00</strong></span>
                        </div>
                        <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                            <table class="table table-sm table-bordered align-middle mb-0" id="fifoPreviewTable">
                                <thead class="table-light">
                                    <tr class="small text-secondary">
                                        <th style="width: 80px;">Queue</th>
                                        <th>Invoice #</th>
                                        <th>Date</th>
                                        <th class="text-end">Current Due</th>
                                        <th class="text-end">Allocated</th>
                                        <th class="text-center">Status After Payment</th>
                                    </tr>
                                </thead>
                                <tbody id="fifoPreviewTbody">
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
                        <span>Confirm & Collect Payment</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('dueSearchInput');
    const typeSelect = document.getElementById('dueTypeFilterSelect');
    const masterRows = document.querySelectorAll('.customer-master-row');
    const visibleCountSpan = document.getElementById('visibleDueCount');
    const btnToggleAll = document.getElementById('btnToggleAllRows');
    const toggleAllText = document.getElementById('toggleAllRowsText');
    let allExpanded = false;

    // Toggle single customer row
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

    // Row click listeners
    masterRows.forEach(row => {
        row.addEventListener('click', function(e) {
            // Prevent toggling if user clicked an action button or link inside row
            if (e.target.closest('button') || e.target.closest('a') || e.target.closest('.dropdown')) {
                return;
            }
            const groupId = this.dataset.groupId;
            toggleRow(groupId);
        });
    });

    // Expand button in action column
    document.querySelectorAll('.btn-toggle-expand').forEach(btn => {
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
        const typeFilter = typeSelect ? typeSelect.value : 'all';
        let visibleCount = 0;

        masterRows.forEach(row => {
            const groupId = row.dataset.groupId;
            const searchData = row.dataset.search || '';
            const groupType = row.dataset.type || '';
            const childRow = document.getElementById('childRow-' + groupId);

            const matchesSearch = query === '' || searchData.includes(query);
            const matchesType = typeFilter === 'all' || groupType === typeFilter;

            if (matchesSearch && matchesType) {
                row.style.display = '';
                visibleCount++;

                // Auto-expand if user specifically searched for an invoice number
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
    if (typeSelect) typeSelect.addEventListener('change', filterTable);

    // ==========================================
    // FIFO Merged Due Modal Logic
    // ==========================================
    let currentInvoices = [];
    let currentTotalDue = 0;

    const fifoModalEl = document.getElementById('fifoPaymentModal');
    let fifoModalInstance = null;
    if (fifoModalEl && typeof bootstrap !== 'undefined') {
        fifoModalInstance = new bootstrap.Modal(fifoModalEl);
    }

    document.querySelectorAll('.open-fifo-modal').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const customerId = this.dataset.customerId;
            const customerName = this.dataset.customerName;
            const customerPhone = this.dataset.customerPhone;
            currentTotalDue = parseFloat(this.dataset.totalDue) || 0;

            try {
                currentInvoices = JSON.parse(this.dataset.invoices || '[]');
            } catch(err) {
                currentInvoices = [];
            }

            document.getElementById('fifoCustomerId').value = customerId;
            document.getElementById('fifoCustomerName').textContent = customerName;
            document.getElementById('fifoCustomerPhone').innerHTML = '<i class="fe fe-phone me-1"></i>' + customerPhone;
            document.getElementById('fifoTotalDueDisplay').textContent = '৳' + currentTotalDue.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            const amountInput = document.getElementById('fifoPaymentAmount');
            amountInput.max = currentTotalDue;
            amountInput.value = currentTotalDue.toFixed(2);

            updateFifoPreview(currentTotalDue);

            if (fifoModalInstance) {
                fifoModalInstance.show();
            }
        });
    });

    const btnPayFull = document.getElementById('btnPayFullDue');
    if (btnPayFull) {
        btnPayFull.addEventListener('click', function() {
            const amountInput = document.getElementById('fifoPaymentAmount');
            amountInput.value = currentTotalDue.toFixed(2);
            updateFifoPreview(currentTotalDue);
        });
    }

    const fifoAmountInput = document.getElementById('fifoPaymentAmount');
    if (fifoAmountInput) {
        fifoAmountInput.addEventListener('input', function() {
            const val = parseFloat(this.value) || 0;
            updateFifoPreview(val);
        });
    }

    function updateFifoPreview(enteredAmount) {
        const tbody = document.getElementById('fifoPreviewTbody');
        const remainingSummary = document.getElementById('fifoRemainingDueSummary');
        if (!tbody) return;

        let remainingPayment = Math.max(0, enteredAmount);
        let rowsHtml = '';
        let totalRemainingCustomerDue = 0;

        currentInvoices.forEach((inv, index) => {
            const invDue = parseFloat(inv.due) || 0;
            const allocated = Math.min(remainingPayment, invDue);
            const remInvDue = Math.max(0, invDue - allocated);
            totalRemainingCustomerDue += remInvDue;

            let statusHtml = '';
            if (allocated >= invDue && invDue > 0) {
                statusHtml = '<span class="badge badge-soft-success px-2 py-1 rounded-pill"><i class="fe fe-check me-1"></i>Fully Paid</span>';
            } else if (allocated > 0) {
                statusHtml = '<span class="badge badge-soft-warning px-2 py-1 rounded-pill">Partial (৳' + remInvDue.toFixed(2) + ' left)</span>';
            } else {
                statusHtml = '<span class="badge bg-light text-secondary border px-2 py-1 rounded-pill">Unpaid (৳' + invDue.toFixed(2) + ')</span>';
            }

            rowsHtml += `
                <tr>
                    <td><span class="badge ${index === 0 ? 'bg-primary' : 'bg-light text-secondary border'} rounded-pill">#${index + 1}</span></td>
                    <td class="fw-bold font-monospace">#${inv.order_no}</td>
                    <td><small class="text-muted">${inv.date}</small></td>
                    <td class="text-end">৳${invDue.toFixed(2)}</td>
                    <td class="text-end fw-bold text-success">৳${allocated.toFixed(2)}</td>
                    <td class="text-center">${statusHtml}</td>
                </tr>
            `;

            remainingPayment -= allocated;
        });

        tbody.innerHTML = rowsHtml;
        if (remainingSummary) {
            remainingSummary.innerHTML = 'Remaining Customer Due: <strong class="' + (totalRemainingCustomerDue <= 0 ? 'text-success' : 'text-danger') + '">৳' + totalRemainingCustomerDue.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</strong>';
        }
    }

    // Dynamic Payment Method to Account Filtering
    const payMethodSelect = document.getElementById('fifoPaymentMethod');
    const payAccountSelect = document.getElementById('fifoAccountId');

    function filterPaymentAccounts() {
        if (!payMethodSelect || !payAccountSelect) return;
        const method = payMethodSelect.value;
        let firstAvailable = null;

        Array.from(payAccountSelect.options).forEach(opt => {
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

        const currentOpt = payAccountSelect.options[payAccountSelect.selectedIndex];
        if (!currentOpt || currentOpt.disabled || currentOpt.hidden) {
            if (firstAvailable) {
                payAccountSelect.value = firstAvailable.value;
            }
        }
    }

    if (payMethodSelect && payAccountSelect) {
        payMethodSelect.addEventListener('change', filterPaymentAccounts);
        filterPaymentAccounts();
    }
});
</script>
@endsection
