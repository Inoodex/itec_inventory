@extends('frontend.layouts.app')

@push('styles')
<style>
    .stat-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid rgba(0, 0, 0, 0.05) !important;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08) !important;
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
    .table-custom th, .table-custom td {
        white-space: nowrap;
    }
    .table-responsive {
        overflow: visible !important;
    }
</style>
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
            <div class="d-flex align-items-center gap-2">
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
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-danger-light text-danger rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-alert-circle fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Total Due Orders</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($sales->count()) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-danger-light text-danger rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-dollar-sign fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Total Outstanding Dues</h6>
                        <h4 class="mb-0 fw-bold text-danger">৳{{ number_format($sales->sum('due_payment'), 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-info-light text-info rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-tag fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Retail Dues</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($sales->where('sale_type', 'retail')->count()) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-warning-light text-warning rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-briefcase fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Project Dues</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($sales->where('sale_type', 'project')->count()) }}</h4>
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
        <div class="card-body p-0" style="overflow: visible;">
            <div class="table-responsive" style="overflow: visible !important;">
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
                                <td class="ps-4 text-muted fw-semibold">{{ $loop->iteration }}</td>
                                <td>
                                    <span class="text-secondary small fw-semibold">
                                        {{ $sale->created_at ? $sale->created_at->format('d M Y') : 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold text-primary font-monospace">#{{ $sale->order_no }}</span>
                                </td>
                                <td>
                                    <div>
                                        <span class="fw-bold text-dark d-block">{{ $customerName }}</span>
                                        <small class="text-muted fs-7"><i class="fe fe-phone me-1"></i>{{ $customerPhone }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-soft-primary px-3 py-1 rounded-pill fs-7">
                                        ৳{{ number_format($sale->payble, 2) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-soft-success px-3 py-1 rounded-pill fs-7">
                                        ৳{{ number_format($sale->advanced_payment, 2) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-soft-danger px-3 py-1 rounded-pill fs-7">
                                        ৳{{ number_format($sale->due_payment, 2) }}
                                    </span>
                                </td>
                                <td>
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
                                <td>
                                    @if ($sale->due_payment > 0)
                                        @if ($sale->sale_type == 'project')
                                            <a href="{{ route('projects.payments', $sale->id) }}" class="btn btn-sm btn-outline-success rounded-2 px-3">
                                                <i class="fe fe-credit-card me-1"></i> Pay Now
                                            </a>
                                        @else
                                            <a href="{{ route('sales.payments', $sale->id) }}" class="btn btn-sm btn-outline-success rounded-2 px-3">
                                                <i class="fe fe-credit-card me-1"></i> Pay Now
                                            </a>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
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
