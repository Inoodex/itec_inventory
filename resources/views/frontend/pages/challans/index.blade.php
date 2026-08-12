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
                <h4 class="card-title fw-bold text-dark mb-1">Challan Management</h4>
                <p class="text-muted small mb-0">Overview of generated sales delivery challans and project consignment notes</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('challans.pdf', request()->query()) }}" class="btn btn-outline-danger px-4 py-2 rounded-3 shadow-sm" target="_blank">
                    <i class="fe fe-file-text me-2"></i>Export PDF
                </a>
                <a href="{{ route('challans.create') }}" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2">
                    <i class="fe fe-plus-circle fs-6"></i>
                    <span>Create Challan</span>
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
                        <i class="fe fe-truck fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Total Delivery Challans</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($challans->total()) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-4 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-info-light text-info rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-shopping-cart fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Sales Challans</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($challans->where('type', 'sale')->count()) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-4 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-warning-light text-warning rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-briefcase fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Project Challans</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($challans->where('type', 'project')->count()) }}</h4>
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
                        <input type="text" id="challanSearchInput" class="form-control border-light-subtle" placeholder="Search challan number, type..." autocomplete="off">
                    </div>
                </div>
                <div class="col-12 col-md-3 col-lg-3">
                    <select id="challanTypeFilterSelect" class="form-select border-light-subtle">
                        <option value="all">All Challan Types</option>
                        <option value="sale">Sales Challan</option>
                        <option value="project">Project Challan</option>
                    </select>
                </div>
                <div class="col-12 col-md-3 col-lg-4 text-md-end text-muted small">
                    Showing <span id="visibleChallanCount" class="fw-bold text-dark">{{ $challans->count() }}</span> of {{ $challans->total() }} records
                </div>
            </div>
        </div>

        <!-- Table Body -->
        <div class="card-body p-0" style="overflow: visible;">
            <div class="table-responsive" style="overflow: visible !important;">
                <table class="table table-hover table-custom align-middle mb-0" id="challansTable">
                    <thead class="bg-light text-secondary fs-7 text-uppercase">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Challan No.</th>
                            <th>Challan Date</th>
                            <th>Challan Type</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($challans as $challan)
                            <tr class="challan-row" data-search="{{ strtolower($challan->challan_number . ' ' . $challan->type) }}" data-type="{{ strtolower($challan->type) }}">
                                <td class="ps-4 text-muted fw-semibold">{{ $loop->iteration }}</td>
                                <td>
                                    <span class="fw-bold text-primary font-monospace">{{ $challan->challan_number }}</span>
                                </td>
                                <td>
                                    <span class="text-secondary small fw-semibold">
                                        {{ $challan->challan_date ? $challan->challan_date->format('d M Y') : 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    @if($challan->type == 'project')
                                        <span class="badge badge-soft-warning px-3 py-1 rounded-pill text-capitalize fs-7">
                                            Project Challan
                                        </span>
                                    @else
                                        <span class="badge badge-soft-info px-3 py-1 rounded-pill text-capitalize fs-7">
                                            Sales Challan
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <a href="javascript:void(0)" class="btn-action-icon shadow-none" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('challans.preview', $challan->id) }}" target="_blank">
                                                    <i class="fe fe-file-text text-success"></i>
                                                    <span>Preview PDF</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('challans.show', $challan->id) }}">
                                                    <i class="fe fe-eye text-primary"></i>
                                                    <span>View Details</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('challans.download', $challan->id) }}" target="_blank">
                                                    <i class="fe fe-download text-info"></i>
                                                    <span>Download PDF</span>
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider opacity-50"></li>
                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2 text-danger" href="javascript:void(0)"
                                                    onclick="if (confirm('Are you sure you want to delete this challan?')) { document.getElementById('serviceDelete{{ $challan->id }}').submit(); }">
                                                    <i class="fe fe-trash-2 text-danger"></i>
                                                    <span>Delete Challan</span>
                                                </a>
                                                <form id="serviceDelete{{ $challan->id }}" action="{{ route('challans.destroy', $challan->id) }}" method="POST" class="d-none">
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
                                <td colspan="5" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="avatar avatar-xl bg-primary-light text-primary rounded-circle mb-3 d-flex align-items-center justify-content-center">
                                            <i class="fe fe-truck fs-1"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">No Delivery Challans Found</h5>
                                        <p class="text-muted small mb-3">Create delivery challans for completed orders</p>
                                        <a href="{{ route('challans.create') }}" class="btn btn-primary btn-sm px-3 rounded-2">
                                            Create Challan
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($challans->hasPages())
                <div class="p-3 border-top d-flex justify-content-end">
                    {{ $challans->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('challanSearchInput');
    const typeSelect = document.getElementById('challanTypeFilterSelect');
    const rows = document.querySelectorAll('.challan-row');
    const visibleCountSpan = document.getElementById('visibleChallanCount');

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
