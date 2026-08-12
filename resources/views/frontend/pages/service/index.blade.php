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
    .badge-soft-warning {
        background-color: rgba(255, 193, 7, 0.15) !important;
        color: #b58105 !important;
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
    .search-box-custom input {
        border-radius: 8px;
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
                <h4 class="card-title fw-bold text-dark mb-1">Service List</h4>
                <p class="text-muted small mb-0">Track repair services, customer bills, payment status, and warranties</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a class="btn btn-outline-danger px-3 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2" href="{{ route('service.pdf', request()->query()) }}" target="_blank">
                    <i class="fe fe-file-text fs-6"></i>
                    <span>Export PDF</span>
                </a>
                <a class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2" href="{{ route('service.create') }}">
                    <i class="fe fe-plus-circle fs-6"></i>
                    <span>Add Service</span>
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
                        <i class="fas fa-tools fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Total Services</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($services->count()) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-info-light text-info rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fas fa-dollar-sign fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Total Billing</h6>
                        <h4 class="mb-0 fw-bold text-dark">৳{{ number_format($services->sum('bill'), 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-danger-light text-danger rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-alert-circle fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Total Outstanding Due</h6>
                        <h4 class="mb-0 fw-bold text-dark">৳{{ number_format($services->sum('due_amount'), 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-success-light text-success rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-check-circle fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Paid Services</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($services->where('due_amount', '<=', 0)->count()) }}</h4>
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
                        <input type="text" id="serviceSearchInput" class="form-control border-light-subtle" placeholder="Search by customer name, phone, product, email..." autocomplete="off">
                    </div>
                </div>
                <div class="col-12 col-md-3 col-lg-3">
                    <select id="serviceTypeFilterSelect" class="form-select border-light-subtle">
                        <option value="all">All Service Types</option>
                        <option value="paid">Paid Only</option>
                        <option value="due">Due Only</option>
                    </select>
                </div>
                <div class="col-12 col-md-3 col-lg-4 text-md-end text-muted small">
                    Showing <span id="visibleServiceCount" class="fw-bold text-dark">{{ $services->count() }}</span> of {{ $services->count() }} records
                </div>
            </div>
        </div>

        <!-- Table Body -->
        <div class="card-body p-0" style="overflow: visible;">
            <div class="table-responsive" style="overflow: visible !important;">
                <table class="table table-hover table-custom align-middle mb-0" id="serviceTable">
                    <thead class="bg-light text-secondary fs-7 text-uppercase">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Date</th>
                            <th>Customer Info</th>
                            <th>Product Details</th>
                            <th>Total Bill</th>
                            <th>Due Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($services as $service)
                            <tr class="service-row" data-search="{{ strtolower($service->name . ' ' . $service->phone . ' ' . (optional($service->product)->name ?? $service->product_name) . ' ' . $service->email . ' ' . $service->repaired_by) }}" data-type="{{ $service->due_amount > 0 ? 'due' : 'paid' }}">
                                <td class="ps-4 text-muted fw-semibold">{{ $loop->iteration }}</td>
                                <td>
                                    <span class="text-secondary small">
                                        {{ $service->created_at ? $service->created_at->format('d M Y') : 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <span class="fw-bold text-dark d-block">{{ $service->name }}</span>
                                        <small class="text-muted fs-7"><i class="fe fe-phone me-1"></i>{{ $service->phone ?? 'N/A' }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">
                                        {{ Str::limit(optional($service->product)->name ?? $service->product_name, 30) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-soft-primary px-3 py-1 rounded-pill fs-7">
                                        ৳{{ number_format($service->bill, 2) }}
                                    </span>
                                </td>
                                <td>
                                    @if ($service->due_amount > 0)
                                        <span class="badge badge-soft-danger px-3 py-1 rounded-pill fs-7">
                                            ৳{{ number_format($service->due_amount, 2) }}
                                        </span>
                                    @else
                                        <span class="badge badge-soft-success px-3 py-1 rounded-pill fs-7">
                                            Paid
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if ($service->due_amount <= 0)
                                        <span class="badge badge-soft-success px-3 py-2 rounded-pill fs-7">
                                            <i class="fe fe-check-circle me-1"></i> Completed
                                        </span>
                                    @else
                                        {!! $service->status_badge !!}
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="dropdown">
                                        <a href="javascript:void(0)" class="btn-action-icon shadow-none" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2"
                                                    href="{{ route('service.payments', ['id' => $service->id, 'payment_for' => '1']) }}">
                                                    <i class="fe fe-credit-card text-primary"></i>
                                                    <span>{{ $service->due_amount <= 0 ? 'View Payments' : 'Get Payment' }}</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" target="_blank"
                                                    href="{{ route('service.invoice', $service->id) }}">
                                                    <i class="fe fe-file-text text-info"></i>
                                                    <span>View Invoice</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2"
                                                    href="{{ route('service.edit', $service->id) }}">
                                                    <i class="fe fe-edit text-warning"></i>
                                                    <span>Edit Service</span>
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider opacity-50"></li>
                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2 text-danger" href="javascript:void(0)"
                                                    onclick="if (confirm('Are you sure you want to delete this service record?')) { document.getElementById('deleteService{{ $service->id }}').submit(); }">
                                                    <i class="fe fe-trash-2 text-danger"></i>
                                                    <span>Delete Service</span>
                                                </a>
                                                <form id="deleteService{{ $service->id }}" action="{{ route('service.destroy', $service->id) }}" method="POST" class="d-none">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="emptyStateRow">
                                <td colspan="8" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="avatar avatar-xl bg-primary-light text-primary rounded-circle mb-3 d-flex align-items-center justify-content-center">
                                            <i class="fe fe-tool fs-1"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">No Service Records Found</h5>
                                        <p class="text-muted small mb-3">Add a new service record to track repair jobs and customer payments</p>
                                        <a href="{{ route('service.create') }}" class="btn btn-primary btn-sm px-3 rounded-2">
                                            Add Service
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($services, 'hasPages') && $services->hasPages())
                <div class="p-3 border-top">
                    {{ $services->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('serviceSearchInput');
    const typeSelect = document.getElementById('serviceTypeFilterSelect');
    const rows = document.querySelectorAll('.service-row');
    const visibleCountSpan = document.getElementById('visibleServiceCount');

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
