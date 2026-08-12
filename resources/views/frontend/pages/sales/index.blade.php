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
    .badge-soft-secondary {
        background-color: rgba(108, 117, 125, 0.12) !important;
        color: #6c757d !important;
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
                <h4 class="card-title fw-bold text-dark mb-1">Sales List</h4>
                <p class="text-muted small mb-0">Manage retail sales, project orders, invoices, and customer transactions</p>
            </div>
            <div>
                <a class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2" href="{{ route('sales.create') }}">
                    <i class="fe fe-plus-circle fs-6"></i>
                    <span>Add Sale</span>
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
                    <div class="avatar avatar-lg bg-primary-light text-primary rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-shopping-cart fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Total Sales Orders</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($services->total()) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-success-light text-success rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-dollar-sign fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Total Sales Revenue</h6>
                        <h4 class="mb-0 fw-bold text-dark">৳{{ number_format($services->sum('payble'), 2) }}</h4>
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
                        <h6 class="text-muted fw-normal mb-1">Retail Orders</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($services->where('sale_type', 'retail')->count()) }}</h4>
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
                        <h6 class="text-muted fw-normal mb-1">Project Orders</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($services->where('sale_type', 'project')->count()) }}</h4>
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
                        <input type="text" id="salesSearchInput" class="form-control border-light-subtle" placeholder="Search by order no, customer name, phone, sales person..." autocomplete="off">
                    </div>
                </div>
                <div class="col-12 col-md-3 col-lg-3">
                    <select id="saleTypeFilterSelect" class="form-select border-light-subtle">
                        <option value="all">All Sale Types</option>
                        <option value="retail">Retail Only</option>
                        <option value="project">Project Only</option>
                    </select>
                </div>
                <div class="col-12 col-md-3 col-lg-4 text-md-end text-muted small">
                    Showing <span id="visibleSalesCount" class="fw-bold text-dark">{{ $services->count() }}</span> of {{ $services->total() }} records
                </div>
            </div>
        </div>

        <!-- Table Body -->
        <div class="card-body p-0" style="overflow: visible;">
            <div class="table-responsive" style="overflow: visible !important;">
                <table class="table table-hover table-custom align-middle mb-0" id="salesTable">
                    <thead class="bg-light text-secondary fs-7 text-uppercase">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Date</th>
                            <th>Order No</th>
                            <th>Customer Info</th>
                            <th>Payable Amount</th>
                            <th>Sale Type</th>
                            <th>Sales By</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($services as $service)
                            @php
                                $customerName = $service->sale_type == 'project' ? ($service->client->name ?? 'N/A') : ($service->customer->name ?? 'N/A');
                                $customerPhone = $service->sale_type == 'project' ? ($service->client->phone ?? 'N/A') : ($service->customer->phone ?? 'N/A');
                                $salesBy = $service->salesPerson->name ?? 'N/A';
                            @endphp
                            <tr class="sale-row" data-search="{{ strtolower($service->order_no . ' ' . $customerName . ' ' . $customerPhone . ' ' . $salesBy . ' ' . $service->sale_type) }}" data-type="{{ strtolower($service->sale_type) }}">
                                <td class="ps-4 text-muted fw-semibold">{{ $loop->iteration }}</td>
                                <td>
                                    <span class="text-secondary small">
                                        {{ $service->created_at ? $service->created_at->format('d M Y') : 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold text-primary font-monospace">#{{ $service->order_no }}</span>
                                </td>
                                <td>
                                    <div>
                                        <span class="fw-bold text-dark d-block">{{ $customerName }}</span>
                                        <small class="text-muted fs-7"><i class="fe fe-phone me-1"></i>{{ $customerPhone }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-soft-primary px-3 py-1 rounded-pill fs-7">
                                        ৳{{ number_format($service->payble, 2) }}
                                    </span>
                                </td>
                                <td>
                                    @if($service->sale_type == 'project')
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
                                    <span class="text-secondary small fw-semibold">
                                        {{ $salesBy }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="dropdown">
                                        <a href="javascript:void(0)" class="btn-action-icon shadow-none" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                            @if ($service->sale_type == 'retail')
                                                <li>
                                                    <a class="dropdown-item py-2 d-flex align-items-center gap-2" target="_blank"
                                                        href="{{ route('sales.invoice.pdf', $service->id) }}">
                                                        <i class="fe fe-download text-info"></i>
                                                        <span>Download PDF</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item py-2 d-flex align-items-center gap-2"
                                                        href="{{ route('sales.edit', $service->id) }}">
                                                        <i class="fe fe-edit text-warning"></i>
                                                        <span>Edit Sale</span>
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider opacity-50"></li>
                                            @endif
                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2 text-danger" href="javascript:void(0)"
                                                    onclick="if (confirm('Are you sure you want to delete this sales order?')) { document.getElementById('serviceDelete{{ $service->id }}').submit(); }">
                                                    <i class="fe fe-trash-2 text-danger"></i>
                                                    <span>Delete Sale</span>
                                                </a>
                                                <form id="serviceDelete{{ $service->id }}" action="{{ route('sales.destroy', $service->id) }}" method="POST" class="d-none">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="avatar avatar-xl bg-primary-light text-primary rounded-circle mb-3 d-flex align-items-center justify-content-center">
                                            <i class="fe fe-shopping-cart fs-1"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">No Sales Records Found</h5>
                                        <p class="text-muted small mb-3">Create a new sale order to record customer purchases</p>
                                        <a href="{{ route('sales.create') }}" class="btn btn-primary btn-sm px-3 rounded-2">
                                            Add Sale
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($services->hasPages())
                <div class="p-3 border-top d-flex justify-content-end">
                    {{ $services->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('salesSearchInput');
    const typeSelect = document.getElementById('saleTypeFilterSelect');
    const rows = document.querySelectorAll('.sale-row');
    const visibleCountSpan = document.getElementById('visibleSalesCount');

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
