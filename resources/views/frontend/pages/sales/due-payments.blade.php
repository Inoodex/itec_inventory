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
    .stat-icon-danger { background: rgba(220, 53, 69, 0.12); color: #dc3545; }
    .stat-icon-info   { background: rgba(13, 202, 240, 0.14); color: #0dcaf0; }
    .stat-icon-warning{ background: rgba(255, 193, 7, 0.16); color: #b58105; }
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
    .table-custom tbody tr:hover {
        background-color: #fcfbff !important;
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
        border-color: rgba(25, 135, 84, 0.30) !important;
    }
    .btn-action-pay:hover {
        background-color: #198754 !important;
        color: #ffffff !important;
        border-color: #198754 !important;
    }
    .table-custom th, .table-custom td {
        white-space: nowrap;
    }

    /* ===== Table Redesign ===== */
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
    #duePaymentsTable tbody tr:last-child td {
        border-bottom: 0 !important;
    }
    #duePaymentsTable tbody tr:hover td {
        background-color: #f8f6ff !important;
    }
    /* Right-align money columns on laptop/desktop */
    @media (min-width: 992px) {
        #duePaymentsTable th:nth-child(5),
        #duePaymentsTable td:nth-child(5),
        #duePaymentsTable th:nth-child(6),
        #duePaymentsTable td:nth-child(6),
        #duePaymentsTable th:nth-child(7),
        #duePaymentsTable td:nth-child(7) {
            text-align: right;
        }
    }
    html[data-layout-mode="dark"] #duePaymentsTable thead {
        background: #20242b !important;
        border-bottom-color: #2a303a;
    }
    html[data-layout-mode="dark"] #duePaymentsTable thead th {
        color: #aeb8c4 !important;
    }
    html[data-layout-mode="dark"] #duePaymentsTable tbody td {
        color: #e6e9ef !important;
        border-bottom-color: #2a303a !important;
    }
    html[data-layout-mode="dark"] #duePaymentsTable tbody tr:hover td {
        background-color: #262b33 !important;
    }

    /* ===== Responsive: Laptop & Mobile ===== */
    .table-responsive {
        overflow-x: auto;
    }

    /* Mobile & Tablet: convert table rows into stacked cards */
    @media (max-width: 991.98px) {
        #duePaymentsTable thead {
            display: none;
        }
        #duePaymentsTable,
        #duePaymentsTable tbody,
        #duePaymentsTable tr,
        #duePaymentsTable td {
            display: block;
            width: 100%;
        }
        #duePaymentsTable tr {
            background: #ffffff;
            border: 1px solid #e8ecf1 !important;
            border-radius: 12px;
            margin-bottom: 12px;
            padding: 8px 10px;
            box-shadow: 0 2px 10px rgba(82, 63, 105, 0.05);
        }
        #duePaymentsTable td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            border: 0 !important;
            border-bottom: 1px solid #f2f4f7 !important;
            padding: 9px 10px !important;
            height: auto !important;
            white-space: normal !important;
            text-align: right;
        }
        #duePaymentsTable td:last-child {
            border-bottom: 0 !important;
        }
        #duePaymentsTable td::before {
            content: attr(data-label);
            font-weight: 600;
            color: #6c757d;
            flex-shrink: 0;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        #duePaymentsTable td[data-label=""]::before,
        #duePaymentsTable td.table-empty-state::before {
            content: none;
        }
        #duePaymentsTable td.table-empty-state {
            display: block !important;
            text-align: center !important;
        }
        #duePaymentsTable td .badge {
            white-space: nowrap;
        }
    }

    html[data-layout-mode="dark"] #duePaymentsTable tr {
        background: #1b1e23;
        border-color: #2e3840 !important;
    }
    html[data-layout-mode="dark"] #duePaymentsTable td {
        border-bottom-color: #2a303a !important;
    }

    /* ===== ROOT CAUSE FIX =====
       .page-wrapper was being pushed wider than .main-wrapper by the table
       (page-wrapper measured 1270px vs main-wrapper's 1112px). Clipping
       overflow at that level HIDES content instead of scrolling it — that's
       why no scrollbar appeared. The real fix: stop the table from widening
       .page-wrapper in the first place, by giving every ancestor down to the
       table wrapper `min-width: 0` (flex/block children default to
       shrink-to-fit their content's natural width, which is what let a wide
       table balloon every parent). Only #duePaymentsTableResponsive should
       ever scroll horizontally. */
    .main-wrapper,
    .page-wrapper,
    .page-wrapper > .content.container-fluid,
    .content.container-fluid .card,
    .content.container-fluid .card-body {
        min-width: 0;
        max-width: 100%;
    }

    /* ===== Table wrapper: this is the ONLY element that should scroll ===== */
    #duePaymentsTableResponsive {
        max-width: 100%;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: #b9c2d0 #eef1f6;
    }
    #duePaymentsTableResponsive::-webkit-scrollbar {
        width: 9px;
        height: 9px;
    }
    #duePaymentsTableResponsive::-webkit-scrollbar-track {
        background: #eef1f6;
        border-radius: 10px;
    }
    #duePaymentsTableResponsive::-webkit-scrollbar-thumb {
        background: #b9c2d0;
        border-radius: 10px;
        border: 2px solid #eef1f6;
    }
    #duePaymentsTableResponsive::-webkit-scrollbar-thumb:hover {
        background: #7638ff;
    }
    #duePaymentsTable th:last-child,
    #duePaymentsTable td:last-child {
        min-width: 60px;
        padding-right: 16px !important;
        white-space: nowrap;
        text-align: center;
    }

    /* Small breathing room so the last column never touches/clips the card edge */
    #duePaymentsTableResponsive {
        padding-right: 2px;
    }

    @media (min-width: 992px) {
        #duePaymentsTableResponsive {
            overflow-x: auto !important;
        }
        /* Base laptop/desktop sizing */
        #duePaymentsTable {
            width: 100%;
            min-width: 950px; /* lowered from 1100px so it fits inside narrower laptop widths without forcing scroll */
        }
        #duePaymentsTable th {
            white-space: nowrap;
            padding: 12px 14px;
            font-size: 12.5px;
            letter-spacing: 0.4px;
        }
        #duePaymentsTable td {
            padding: 12px 14px;
            font-size: 13px;
        }
        #duePaymentsTable th:nth-child(4),
        #duePaymentsTable td:nth-child(4) {
            min-width: 180px;
            white-space: normal;
        }
        #duePaymentsTable td .badge,
        #duePaymentsTable td .font-monospace {
            white-space: nowrap;
        }
    }

    /* ===== Tuned breakpoints for the widths actually being tested ===== */

    /* ~1270px viewport: comfortable, just tighten badge padding slightly */
    @media (min-width: 1121px) and (max-width: 1300px) {
        #duePaymentsTable th,
        #duePaymentsTable td {
            padding: 11px 12px;
            font-size: 12.5px;
        }
        #duePaymentsTable .badge {
            padding: 0.25rem 0.6rem !important;
            font-size: 11px;
        }
    }

    /* ~1112px viewport: tighter columns, smaller badges, no wasted space */
    @media (min-width: 1021px) and (max-width: 1120px) {
        #duePaymentsTable {
            min-width: 900px;
        }
        #duePaymentsTable th,
        #duePaymentsTable td {
            padding: 10px 10px;
            font-size: 12px;
        }
        #duePaymentsTable .badge {
            padding: 0.22rem 0.55rem !important;
            font-size: 10.5px;
        }
        #duePaymentsTable th:nth-child(4),
        #duePaymentsTable td:nth-child(4) {
            min-width: 150px;
        }
    }

    /* ~1018px viewport: narrowest laptop tier — drop the Date column to
       reclaim space instead of forcing horizontal scroll */
    @media (min-width: 992px) and (max-width: 1020px) {
        #duePaymentsTable {
            min-width: 0;
        }
        #duePaymentsTable th:nth-child(2),
        #duePaymentsTable td:nth-child(2) {
            display: none; /* Date column hidden at this tier; still visible via row detail if needed */
        }
        #duePaymentsTable th,
        #duePaymentsTable td {
            padding: 9px 8px;
            font-size: 11.5px;
        }
        #duePaymentsTable .badge {
            padding: 0.2rem 0.5rem !important;
            font-size: 10px;
        }
        #duePaymentsTable th:nth-child(4),
        #duePaymentsTable td:nth-child(4) {
            min-width: 130px;
        }
    }
    /* Below laptop breakpoint: rows become stacked cards, no horizontal scroll needed */
    @media (max-width: 991.98px) {
        #duePaymentsTableResponsive {
            overflow-x: visible !important;
        }
    }
    html[data-layout-mode="dark"] #duePaymentsTableResponsive {
        scrollbar-color: #3a4550 #1b1e23;
    }
    html[data-layout-mode="dark"] #duePaymentsTableResponsive::-webkit-scrollbar-track {
        background: #1b1e23;
    }
    html[data-layout-mode="dark"] #duePaymentsTableResponsive::-webkit-scrollbar-thumb {
        background: #3a4550;
        border-color: #1b1e23;
    }</style>
@endpush

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1">Due Payments</h4>
                <p class="text-muted small mb-0">Overview of outstanding customer dues for retail sales and project orders</p>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
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
                    <div class="stat-icon stat-icon-danger me-3">
                        <i class="fe fe-alert-circle"></i>
                    </div>
                    <div class="flex-grow-1" style="min-width: 0;">
                        <h6 class="stat-label text-muted fw-normal mb-1">Total Due Orders</h6>
                        <h4 class="stat-value mb-0 fw-bold text-dark">{{ number_format($sales->count()) }}</h4>
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
                        <h6 class="stat-label text-muted fw-normal mb-1">Retail Dues</h6>
                        <h4 class="stat-value mb-0 fw-bold text-dark">{{ number_format($sales->where('sale_type', 'retail')->count()) }}</h4>
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
                        <h6 class="stat-label text-muted fw-normal mb-1">Project Dues</h6>
                        <h4 class="stat-value mb-0 fw-bold text-dark">{{ number_format($sales->where('sale_type', 'project')->count()) }}</h4>
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
                        <input type="text" id="dueSearchInput" class="form-control border-light-subtle" placeholder="Search order no, customer name, phone..." autocomplete="off">
                    </div>
                </div>
                <div class="col-12 col-md-3 col-lg-3">
                    <select id="dueTypeFilterSelect" class="form-select border-light-subtle">
                        <option value="all">All Order Types</option>
                        <option value="retail">Retail Dues</option>
                        <option value="project">Project Dues</option>
                    </select>
                </div>
                <div class="col-12 col-md-3 col-lg-4 text-md-end text-muted small">
                    Showing <span id="visibleDueCount" class="fw-bold text-dark">{{ $sales->count() }}</span> of {{ $sales->count() }} records
                </div>
            </div>
        </div>

        <!-- Table Body -->
        <div class="card-body p-0">
            <div class="table-responsive" id="duePaymentsTableResponsive">
                <table class="table table-hover table-custom align-middle mb-0" id="duePaymentsTable">
                    <thead class="bg-light text-secondary fs-7 text-uppercase">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Date</th>
                            <th>Order No</th>
                            <th>Customer / Client</th>
                            <th>Total Amount</th>
                            <th>Paid Amount</th>
                            <th>Due Amount</th>
                            <th>Type</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($sales as $sale)
                            @php
                                $customerName = $sale->sale_type == 'project' ? ($sale->client->name ?? 'N/A') : ($sale->customer->name ?? 'N/A');
                                $customerPhone = $sale->sale_type == 'project' ? ($sale->client->phone ?? 'N/A') : ($sale->customer->phone ?? 'N/A');
                            @endphp
                            <tr class="due-row" data-search="{{ strtolower($sale->order_no . ' ' . $customerName . ' ' . $customerPhone . ' ' . $sale->sale_type) }}" data-type="{{ strtolower($sale->sale_type) }}">
                                <td class="ps-4 text-muted fw-semibold" data-label="">{{ $loop->iteration }}</td>
                                <td data-label="Date">
                                    <span class="text-secondary small fw-semibold">
                                        {{ $sale->created_at ? $sale->created_at->format('d M Y') : 'N/A' }}
                                    </span>
                                </td>
                                <td data-label="Order No">
                                    <span class="fw-bold text-primary font-monospace">#{{ $sale->order_no }}</span>
                                </td>
                                <td data-label="Customer / Client">
                                    <div>
                                        <span class="fw-bold text-dark d-block">{{ $customerName }}</span>
                                        <small class="text-muted fs-7"><i class="fe fe-phone me-1"></i>{{ $customerPhone }}</small>
                                    </div>
                                </td>
                                <td data-label="Total Amount">
                                    <span class="badge badge-soft-primary px-3 py-1 rounded-pill fs-7">
                                        ৳{{ number_format($sale->payble, 2) }}
                                    </span>
                                </td>
                                <td data-label="Paid Amount">
                                    <span class="badge badge-soft-success px-3 py-1 rounded-pill fs-7">
                                        ৳{{ number_format($sale->advanced_payment, 2) }}
                                    </span>
                                </td>
                                <td data-label="Due Amount">
                                    <span class="badge badge-soft-danger px-3 py-1 rounded-pill fs-7">
                                        ৳{{ number_format($sale->due_payment, 2) }}
                                    </span>
                                </td>
                                <td data-label="Type">
                                    @if($sale->sale_type == 'project')
                                        <span class="badge badge-soft-warning px-3 py-1 rounded-pill text-capitalize fs-7">
                                            Project
                                        </span>
                                    @else
                                        <span class="badge badge-soft-info px-3 py-1 rounded-pill text-capitalize fs-7">
                                            Retail
                                        </span>
                                    @endif
                                </td>
                                <td data-label="Action">
                                    @if ($sale->due_payment > 0)
                                        @if ($sale->sale_type == 'project')
                                            <a href="{{ route('projects.payments', $sale->id) }}" class="btn-action-icon btn-action-pay" title="Pay Now">
                                                <i class="fe fe-credit-card"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('sales.payments', $sale->id) }}" class="btn-action-icon btn-action-pay" title="Pay Now">
                                                <i class="fe fe-credit-card"></i>
                                            </a>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 table-empty-state">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="avatar avatar-xl bg-success-light text-success rounded-circle mb-3 d-flex align-items-center justify-content-center">
                                            <i class="fe fe-check-circle fs-1"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">No Due Payments Outstanding</h5>
                                        <p class="text-muted small mb-0">All retail sales and projects are fully paid</p>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('dueSearchInput');
    const typeSelect = document.getElementById('dueTypeFilterSelect');
    const rows = document.querySelectorAll('.due-row');
    const visibleCountSpan = document.getElementById('visibleDueCount');

    function filterTable() {
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const typeFilter = typeSelect ? typeSelect.value : 'all';
        let visibleCount = 0;

        rows.forEach(row => {
            const rowSearchText = row.dataset.search || '';
            const rowType = row.dataset.type || '';

            const matchesSearch = query === '' || rowSearchText.includes(query);
            const matchesType = typeFilter === 'all' || rowType === typeFilter;

            if (matchesSearch && matchesType) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        if (visibleCountSpan) {
            visibleCountSpan.textContent = visibleCount;
        }
    }

    if (searchInput) searchInput.addEventListener('input', filterTable);
    if (typeSelect) typeSelect.addEventListener('change', filterTable);
});
</script>
@endsection