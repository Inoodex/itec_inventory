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
    .badge-soft-primary {
        background-color: rgba(118, 56, 255, 0.12) !important;
        color: #7638ff !important;
        font-weight: 600;
    }
    .badge-soft-info {
        background-color: rgba(13, 202, 240, 0.12) !important;
        color: #0dcaf0 !important;
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
                <h4 class="card-title fw-bold text-dark mb-1">Purchase Report</h4>
                <p class="text-muted small mb-0">Detailed breakdown of product purchases, quantities, and vendor expenses</p>
            </div>
            <div>
                <a href="{{ route('purchase.report.pdf', request()->all()) }}" class="btn btn-outline-danger px-4 py-2 rounded-3 shadow-sm" target="_blank">
                    <i class="fe fe-file-text me-2"></i>Export PDF
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Summary Stats Bar -->
    <div class="row g-3 mb-4">
        <div class="col-xl-4 col-md-4 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-primary-light text-primary rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-shopping-bag fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Total Products Purchased</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($purchases->count()) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-4 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-info-light text-info rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-box fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Total Quantity</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($purchases->sum('total_qty')) }} Pcs</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-4 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-success-light text-success rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-dollar-sign fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Total Purchase Value</h6>
                        <h4 class="mb-0 fw-bold text-dark">৳{{ number_format($purchases->sum('total_amount'), 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Summary Stats Bar -->

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-4">
            <h6 class="fw-bold text-dark mb-3"><i class="fe fe-filter me-2 text-primary"></i>Filter Report Data</h6>
            <form action="{{ route('purchase.report.get') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-3 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Product Name</label>
                        <select name="item_name" class="form-select border-light-subtle">
                            <option value="">All Products</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" {{ request('item_name') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Vendor</label>
                        <select name="vendor_id" class="form-select border-light-subtle">
                            <option value="">All Vendors</option>
                            @foreach ($vendors as $vendor)
                                <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                    {{ $vendor->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">From Date</label>
                        <input type="date" class="form-control border-light-subtle" name="from" value="{{ request('from') }}">
                    </div>

                    <div class="col-lg-2 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">To Date</label>
                        <input type="date" class="form-control border-light-subtle" name="to" value="{{ request('to') }}">
                    </div>

                    <div class="col-lg-2 col-md-12 col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill rounded-3 py-2">Filter</button>
                        <a href="{{ route('purchase.report') }}" class="btn btn-outline-secondary px-3 py-2 rounded-3">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-3">
        <!-- Live Search Header -->
        <div class="card-header bg-white py-3 border-bottom border-light">
            <div class="row align-items-center g-3">
                <div class="col-12 col-md-6">
                    <div class="search-box-custom">
                        <input type="text" id="purchaseSearchInput" class="form-control border-light-subtle" placeholder="Search product name, total qty, amount..." autocomplete="off">
                    </div>
                </div>
                <div class="col-12 col-md-6 text-md-end text-muted small">
                    Showing <span id="visiblePurchaseCount" class="fw-bold text-dark">{{ $purchases->count() }}</span> records
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-custom align-middle mb-0" id="purchasesReportTable">
                    <thead class="bg-light text-secondary fs-7 text-uppercase">
                        <tr>
                            <th class="ps-4" style="width: 80px;">#</th>
                            <th>Product Name</th>
                            <th>Total Quantity</th>
                            <th class="pe-4">Total Amount</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($purchases as $index => $purchase)
                            @php
                                $productName = $purchase->product->name ?? 'N/A';
                            @endphp
                            <tr class="purchase-row" data-search="{{ strtolower($productName . ' ' . $purchase->total_qty . ' ' . $purchase->total_amount) }}">
                                <td class="ps-4 text-muted fw-semibold">{{ $index + 1 }}</td>
                                <td>
                                    <span class="fw-bold text-dark">{{ $productName }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-soft-info px-3 py-1 rounded-pill fs-7">
                                        {{ number_format($purchase->total_qty) }} Pcs
                                    </span>
                                </td>
                                <td class="pe-4">
                                    <span class="badge badge-soft-primary px-3 py-1 rounded-pill fs-7">
                                        ৳{{ number_format($purchase->total_amount, 2) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="avatar avatar-xl bg-primary-light text-primary rounded-circle mb-3 d-flex align-items-center justify-content-center">
                                            <i class="fe fe-shopping-bag fs-1"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">No Purchase Data Found</h5>
                                        <p class="text-muted small mb-0">Adjust your date range or filter criteria</p>
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
    const searchInput = document.getElementById('purchaseSearchInput');
    const rows = document.querySelectorAll('.purchase-row');
    const visibleCountSpan = document.getElementById('visiblePurchaseCount');

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
