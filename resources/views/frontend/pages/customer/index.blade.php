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

    /* Responsive adjustments for laptop screens */
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
                <h4 class="card-title fw-bold text-dark mb-1">Customer Directory</h4>
                <p class="text-muted small mb-0">Manage customer records, contact information, and account status</p>
            </div>
            <div class="list-btn d-flex align-items-center gap-2">
                <a class="btn btn-outline-danger px-3 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2" href="{{ route('customers.pdf') }}" target="_blank">
                    <i class="fe fe-file-text fs-6"></i>
                    <span>Export PDF</span>
                </a>
                <a class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2" href="{{ route('customers.create') }}">
                    <i class="fe fe-plus-circle fs-6"></i>
                    <span>Add New Customer</span>
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
                        <i class="fe fe-users fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Total Customers</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($customers->count()) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-success-light text-success rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-user-check fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Active Accounts</h6>
                        <h4 class="mb-0 fw-bold text-dark">
                            {{ number_format($customers->filter(fn($c) => in_array($c->status, ['active', '1', 1]))->count()) }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-danger-light text-danger rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-user-x fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Inactive Accounts</h6>
                        <h4 class="mb-0 fw-bold text-dark">
                            {{ number_format($customers->filter(fn($c) => !in_array($c->status, ['active', '1', 1]))->count()) }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-info-light text-info rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-calendar fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">New This Month</h6>
                        <h4 class="mb-0 fw-bold text-dark">
                            {{ number_format($customers->filter(fn($c) => $c->created_at?->isCurrentMonth())->count()) }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Summary Stats Bar -->

    <!-- Customer Table Card -->
    <div class="card border-0 shadow-sm rounded-3">
        <!-- Search & Filter Controls -->
        <div class="card-header bg-white py-3 border-bottom border-light">
            <div class="row align-items-center g-3">
                <div class="col-12 col-md-6 col-lg-5">
                    <div class="search-box-custom">
                        <input type="text" id="customerSearchInput" class="form-control border-light-subtle" placeholder="Search by name, phone, email, address...">
                    </div>
                </div>
                <div class="col-12 col-md-3 col-lg-3">
                    <select id="statusFilterSelect" class="form-select border-light-subtle">
                        <option value="all">All Statuses</option>
                        <option value="active">Active Only</option>
                        <option value="inactive">Inactive Only</option>
                    </select>
                </div>
                <div class="col-12 col-md-3 col-lg-4 text-md-end text-muted small">
                    Showing <span id="visibleCustomerCount" class="fw-bold text-dark">{{ $customers->count() }}</span> of {{ $customers->count() }} records
                </div>
            </div>
        </div>

        <!-- Table Body -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-custom align-middle mb-0" id="customersTable">
                    <thead class="bg-light text-secondary fs-7 text-uppercase">
                        <tr>
                            <th class="ps-4" style="width: 50px;">#</th>
                            <th style="min-width: 180px;">Customer Name</th>
                            <th style="min-width: 160px;">Phone & Email</th>
                            <th style="min-width: 180px;">Address</th>
                            <th style="width: 100px;">Status</th>
                            <th class="text-end pe-4" style="width: 70px;">Action</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($customers as $key => $customer)
                            @php
                                $isActive = in_array($customer->status, ['active', '1', 1]);
                            @endphp
                            <tr class="customer-row" data-status="{{ $isActive ? 'active' : 'inactive' }}" data-search="{{ strtolower($customer->name . ' ' . $customer->phone . ' ' . $customer->email . ' ' . $customer->address) }}">
                                <td class="ps-4 text-muted fw-semibold">{{ $loop->iteration }}</td>
                                <td>
                                    <div>
                                        <a href="{{ route('customers.show', $customer->id) }}" class="fw-bold text-dark hover-primary mb-0 text-decoration-none d-block text-truncate" title="{{ $customer->name }}" style="max-width: 200px;">
                                            {{ Str::limit($customer->name, 25) }}
                                        </a>
                                        <small class="text-muted fs-7">Added {{ $customer->created_at?->format('d M Y') ?? 'N/A' }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <div class="fw-semibold text-dark fs-7 d-flex align-items-center gap-1">
                                            <span>{{ $customer->phone }}</span>
                                        </div>
                                        @if($customer->email)
                                            <div class="text-muted small d-flex align-items-center gap-1 text-truncate" style="max-width: 180px;" title="{{ $customer->email }}">
                                                <i class="fe fe-mail text-muted fs-8 me-1"></i>
                                                <span>{{ Str::limit($customer->email, 22) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="text-secondary small text-truncate d-inline-block" style="max-width: 220px;" title="{{ $customer->address }}">
                                        {{ Str::limit($customer->address, 30) ?: 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    @if ($isActive)
                                        <span class="badge badge-soft-success px-3 py-2 rounded-pill fs-7">
                                            <i class="fe fe-check-circle me-1"></i> Active
                                        </span>
                                    @else
                                        <span class="badge badge-soft-danger px-3 py-2 rounded-pill fs-7">
                                            <i class="fe fe-x-circle me-1"></i> Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="dropdown">
                                        <a href="javascript:void(0)" class="btn-action-icon shadow-none" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('customers.show', $customer->id) }}">
                                                    <i class="fe fe-eye text-info"></i>
                                                    <span>View Details</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('customers.edit', $customer->id) }}">
                                                    <i class="fe fe-edit text-primary"></i>
                                                    <span>Edit Profile</span>
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider opacity-50"></li>
                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2 text-danger" href="javascript:void(0)"
                                                    onclick="if (confirm('Are you sure you want to delete this customer?')) { document.getElementById('delete{{ $customer->id }}').submit(); }">
                                                    <i class="fe fe-trash-2 text-danger"></i>
                                                    <span>Delete Customer</span>
                                                </a>
                                                <form id="delete{{ $customer->id }}" action="{{ route('customers.destroy', $customer->id) }}" method="POST" class="d-none">
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
                                <td colspan="6" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="avatar avatar-xl bg-primary-light text-primary rounded-circle mb-3 d-flex align-items-center justify-content-center">
                                            <i class="fe fe-users fs-1"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">No Customers Found</h5>
                                        <p class="text-muted small mb-3">Get started by creating your first customer record</p>
                                        <a href="{{ route('customers.create') }}" class="btn btn-primary btn-sm px-3 rounded-2">
                                            <i class="fe fe-plus me-1"></i> Add Customer
                                        </a>
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
    const searchInput = document.getElementById('customerSearchInput');
    const statusSelect = document.getElementById('statusFilterSelect');
    const rows = document.querySelectorAll('.customer-row');
    const visibleCountSpan = document.getElementById('visibleCustomerCount');

    function filterTable() {
        const query = searchInput.value.toLowerCase().trim();
        const statusFilter = statusSelect.value;
        let visibleCount = 0;

        rows.forEach(row => {
            const rowSearchText = row.dataset.search;
            const rowStatus = row.dataset.status;

            const matchesSearch = query === '' || rowSearchText.includes(query);
            const matchesStatus = statusFilter === 'all' || rowStatus === statusFilter;

            if (matchesSearch && matchesStatus) {
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
    if (statusSelect) statusSelect.addEventListener('change', filterTable);
});
</script>
@endsection