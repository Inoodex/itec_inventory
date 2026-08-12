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
    .badge-soft-primary {
        background-color: rgba(118, 56, 255, 0.12) !important;
        color: #7638ff !important;
        font-weight: 600;
    }
    .badge-soft-warning {
        background-color: rgba(255, 193, 7, 0.15) !important;
        color: #b58105 !important;
        font-weight: 600;
    }
    .table-custom th, .table-custom td {
        white-space: nowrap;
    }
</style>
@endpush

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1">Monthly Revenue Summary</h4>
                <p class="text-muted small mb-0">Track monthly sales, purchases, operational expenses, and net profit performance</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a class="btn btn-outline-danger px-3 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2" href="{{ route('revenues.pdf') }}" target="_blank">
                    <i class="fe fe-file-text fs-6"></i>
                    <span>Export PDF</span>
                </a>
                <form method="POST" action="{{ route('revenues.generate') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2">
                        <i class="fe fe-refresh-cw fs-6"></i>
                        <span>Generate This Month</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Summary Stats Bar -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-primary-light text-primary rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-calendar fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Total Periods</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($revenues->count()) }} Months</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-info-light text-info rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-shopping-cart fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Total Sales</h6>
                        <h4 class="mb-0 fw-bold text-dark">৳{{ number_format($revenues->sum('total_sales'), 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-warning-light text-warning rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-shopping-bag fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Total Purchases</h6>
                        <h4 class="mb-0 fw-bold text-dark">৳{{ number_format($revenues->sum('total_purchases'), 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-success-light text-success rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-trending-up fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Net Profit</h6>
                        <h4 class="mb-0 fw-bold text-dark">৳{{ number_format($revenues->sum('net_profit'), 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Summary Stats Bar -->

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-3">
        <!-- Search Header -->
        <div class="card-header bg-white py-3 border-bottom border-light">
            <div class="row align-items-center g-3">
                <div class="col-12 col-md-6">
                    <div class="search-box-custom">
                        <input type="text" id="revenueSearchInput" class="form-control border-light-subtle" placeholder="Search year, month, sales, profit..." autocomplete="off">
                    </div>
                </div>
                <div class="col-12 col-md-6 text-md-end text-muted small">
                    Showing <span id="visibleRevenueCount" class="fw-bold text-dark">{{ $revenues->count() }}</span> records
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-custom align-middle mb-0" id="revenuesTable">
                    <thead class="bg-light text-secondary fs-7 text-uppercase">
                        <tr>
                            <th class="ps-4">Year</th>
                            <th>Month</th>
                            <th>Total Purchases</th>
                            <th>Total Sales</th>
                            <th>Total Expenses</th>
                            <th>Net Profit</th>
                            <th class="pe-4 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($revenues as $rev)
                            <tr class="revenue-row" data-search="{{ strtolower($rev->year . ' ' . $rev->month_name . ' ' . $rev->total_sales . ' ' . $rev->net_profit) }}">
                                <td class="ps-4 fw-bold text-dark">{{ $rev->year }}</td>
                                <td>
                                    <span class="badge badge-soft-info px-3 py-1 rounded-pill fs-7 text-uppercase">{{ $rev->month_name }}</span>
                                </td>
                                <td>৳{{ number_format($rev->total_purchases, 2) }}</td>
                                <td>
                                    <span class="text-primary fw-semibold">৳{{ number_format($rev->total_sales, 2) }}</span>
                                </td>
                                <td>৳{{ number_format($rev->total_expenses, 2) }}</td>
                                <td>
                                    @if($rev->net_profit >= 0)
                                        <span class="badge badge-soft-success px-3 py-1 rounded-pill fs-7">
                                            +৳{{ number_format($rev->net_profit, 2) }}
                                        </span>
                                    @else
                                        <span class="badge badge-soft-danger px-3 py-1 rounded-pill fs-7">
                                            -৳{{ number_format(abs($rev->net_profit), 2) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="pe-4 text-end">
                                    <a href="{{ route('revenues.export', $rev->id) }}" target="_blank" class="btn btn-sm btn-outline-danger rounded-2 px-3 shadow-none">
                                        <i class="fe fe-file-text me-1"></i>Export PDF
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="avatar avatar-xl bg-primary-light text-primary rounded-circle mb-3 d-flex align-items-center justify-content-center">
                                            <i class="fe fe-trending-up fs-1"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">No Revenue Data Generated</h5>
                                        <p class="text-muted small mb-3">Click 'Generate This Month' above to calculate revenue</p>
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
    const searchInput = document.getElementById('revenueSearchInput');
    const rows = document.querySelectorAll('.revenue-row');
    const visibleCountSpan = document.getElementById('visibleRevenueCount');

    function filterTable() {
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        let visibleCount = 0;

        rows.forEach(row => {
            const rowSearchText = row.dataset.search || '';
            if (query === '' || rowSearchText.includes(query)) {
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
});
</script>
@endsection
