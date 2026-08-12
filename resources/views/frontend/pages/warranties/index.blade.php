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
    .badge-soft-dark {
        background-color: rgba(33, 37, 41, 0.12) !important;
        color: #212529 !important;
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
                <h4 class="card-title fw-bold text-dark mb-1">Warranty Management</h4>
                <p class="text-muted small mb-0">Track RMA claims, inspection status, vendor repairs, and replacements</p>
            </div>
            <div>
                <a class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2" href="{{ route('warranties.create') }}">
                    <i class="fe fe-plus-circle fs-6"></i>
                    <span>Add Warranty Claim</span>
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
                        <i class="fe fe-shield fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Total Claims</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($stats['total']) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-warning-light text-warning rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-clock fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Pending Inspection</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($stats['pending'] + $stats['under_inspection']) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-info-light text-info rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-refresh-cw fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Repaired / Replaced</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($stats['repaired'] + $stats['replaced']) }}</h4>
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
                        <h6 class="text-muted fw-normal mb-1">Completed</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($stats['completed']) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Summary Stats Bar -->

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-3">
        <!-- Search & Filter Controls -->
        <div class="card-header bg-white py-3 border-bottom border-light">
            <form action="{{ route('warranties.index') }}" method="GET" id="warrantyFilterForm">
                <div class="row align-items-center g-3">
                    <div class="col-12 col-md-4 col-lg-4">
                        <div class="search-box-custom">
                            <input type="text" name="search" class="form-control border-light-subtle" placeholder="Search Claim #, Serial #, Customer..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-12 col-md-3 col-lg-3">
                        <select name="status" class="form-select border-light-subtle" onchange="document.getElementById('warrantyFilterForm').submit()">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="under_inspection" {{ request('status') == 'under_inspection' ? 'selected' : '' }}>Under Inspection</option>
                            <option value="sent_to_vendor" {{ request('status') == 'sent_to_vendor' ? 'selected' : '' }}>Sent to Vendor</option>
                            <option value="repaired" {{ request('status') == 'repaired' ? 'selected' : '' }}>Repaired</option>
                            <option value="replaced" {{ request('status') == 'replaced' ? 'selected' : '' }}>Replaced</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-3 col-lg-3">
                        <div class="input-group">
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control border-light-subtle" placeholder="From">
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control border-light-subtle" placeholder="To">
                        </div>
                    </div>
                    <div class="col-12 col-md-2 col-lg-2 text-md-end text-muted small">
                        Showing <span class="fw-bold text-dark">{{ $claims->count() }}</span> entries
                    </div>
                </div>
            </form>
        </div>

        <!-- Table Body -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-custom align-middle mb-0" id="warrantyTable">
                    <thead class="bg-light text-secondary fs-7 text-uppercase">
                        <tr>
                            <th class="ps-4">Claim No</th>
                            <th>Customer & Invoice</th>
                            <th>Product & Serial</th>
                            <th>Claim Date</th>
                            <th>Warranty Expiry</th>
                            <th>Status</th>
                            <!-- <th class="text-end pe-4">Action</th> -->
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($claims as $claim)
                            <tr>
                                <td class="ps-4">
                                    <a href="{{ route('warranties.show', $claim->id) }}" class="fw-bold text-primary text-decoration-none">
                                        {{ $claim->claim_no }}
                                    </a>
                                </td>
                                <td>
                                    <div>
                                        <span class="fw-bold text-dark d-block" title="{{ $claim->customer?->name }}">
                                            {{ Str::limit($claim->customer?->name ?? 'Guest Customer', 25) }}
                                        </span>
                                        <small class="text-muted fs-7">
                                            Inv: {{ $claim->sale?->order_no ?? '#' . $claim->sale_id }} | {{ $claim->customer?->phone }}
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <span class="fw-semibold text-dark d-block" title="{{ $claim->product?->name }}">
                                            {{ Str::limit($claim->product?->name ?? 'N/A', 25) }}
                                        </span>
                                        @if($claim->serial_number)
                                            <span class="badge bg-light text-dark border fs-8">S/N: {{ $claim->serial_number }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="text-secondary small">
                                        {{ $claim->claim_date ? $claim->claim_date->format('d M Y') : 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    @if($claim->is_valid_warranty)
                                        <span class="badge badge-soft-success px-3 py-1 rounded-pill fs-7">
                                            <i class="fe fe-check-circle me-1"></i> {{ $claim->warranty_expiry_date ? $claim->warranty_expiry_date->format('d M Y') : 'N/A' }}
                                        </span>
                                    @else
                                        <span class="badge badge-soft-danger px-3 py-1 rounded-pill fs-7">
                                            <i class="fe fe-x-circle me-1"></i> {{ $claim->warranty_expiry_date ? $claim->warranty_expiry_date->format('d M Y') : 'Expired' }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $badgeClasses = [
                                            'pending'          => 'badge-soft-warning',
                                            'under_inspection' => 'badge-soft-info',
                                            'sent_to_vendor'   => 'badge-soft-primary',
                                            'repaired'         => 'badge-soft-success',
                                            'replaced'         => 'badge-soft-success',
                                            'rejected'         => 'badge-soft-danger',
                                            'completed'        => 'badge-soft-dark',
                                        ];
                                    @endphp
                                    <span class="badge {{ $badgeClasses[$claim->status] ?? 'bg-secondary' }} px-3 py-2 rounded-pill fs-7">
                                        {{ ucfirst(str_replace('_', ' ', $claim->status)) }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="dropdown">
                                        <a href="javascript:void(0)" class="btn-action-icon shadow-none" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('warranties.show', $claim->id) }}">
                                                    <i class="fe fe-eye text-primary"></i>
                                                    <span>View Timeline</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2 text-success" href="{{ route('warranties.print', $claim->id) }}" target="_blank">
                                                    <i class="fe fe-printer text-success"></i>
                                                    <span>Print Receipt</span>
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider opacity-50"></li>
                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2 text-danger" href="javascript:void(0)"
                                                    onclick="if (confirm('Are you sure you want to delete this claim?')) { document.getElementById('deleteClaim{{ $claim->id }}').submit(); }">
                                                    <i class="fe fe-trash-2 text-danger"></i>
                                                    <span>Delete Claim</span>
                                                </a>
                                                <form id="deleteClaim{{ $claim->id }}" action="{{ route('warranties.destroy', $claim->id) }}" method="POST" class="d-none">
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
                                <td colspan="7" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="avatar avatar-xl bg-primary-light text-primary rounded-circle mb-3 d-flex align-items-center justify-content-center">
                                            <i class="fe fe-shield fs-1"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">No Warranty Claims Found</h5>
                                        <p class="text-muted small mb-3">Create a warranty claim to start tracking RMA inspections</p>
                                        <a href="{{ route('warranties.create') }}" class="btn btn-primary btn-sm px-3 rounded-2">
                                            Add Claim
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($claims->hasPages())
                <div class="p-3 border-top">
                    {{ $claims->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
