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
                <h4 class="card-title fw-bold text-dark mb-1">Product Returns</h4>
                <p class="text-muted small mb-0">Manage customer product return requests, approvals, and stock refunds</p>
            </div>
            <div>
                <a href="{{ route('returns.create') }}" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2">
                    <i class="fe fe-plus-circle fs-6"></i>
                    <span>New Return</span>
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
                        <i class="fe fe-rotate-ccw fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Total Return Requests</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($returns->count()) }}</h4>
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
                        <h6 class="text-muted fw-normal mb-1">Total Refund Amount</h6>
                        <h4 class="mb-0 fw-bold text-dark">৳{{ number_format($returns->sum('total_refund_amount'), 2) }}</h4>
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
                        <h6 class="text-muted fw-normal mb-1">Pending Approval</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($returns->where('status', 'pending')->count()) }}</h4>
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
                        <h6 class="text-muted fw-normal mb-1">Completed Returns</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($returns->where('status', 'completed')->count()) }}</h4>
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
                        <input type="text" id="returnSearchInput" class="form-control border-light-subtle" placeholder="Search by return ID, order no, customer name..." autocomplete="off">
                    </div>
                </div>
                <div class="col-12 col-md-3 col-lg-3">
                    <select id="returnStatusFilterSelect" class="form-select border-light-subtle">
                        <option value="all">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="completed">Completed</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div class="col-12 col-md-3 col-lg-4 text-md-end text-muted small">
                    Showing <span id="visibleReturnCount" class="fw-bold text-dark">{{ $returns->count() }}</span> of {{ $returns->count() }} records
                </div>
            </div>
        </div>

        <!-- Table Body -->
        <div class="card-body p-0" style="overflow: visible;">
            <div class="table-responsive" style="overflow: visible !important;">
                <table class="table table-hover table-custom align-middle mb-0" id="returnsTable">
                    <thead class="bg-light text-secondary fs-7 text-uppercase">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Return Date</th>
                            <th>Order No</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Refund Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($returns as $return)
                            @php
                                $orderNo = $return->sale->order_no ?? 'N/A';
                                $customerName = $return->customer->name ?? 'N/A';
                                $statusClass = [
                                    'pending' => 'badge-soft-warning',
                                    'approved' => 'badge-soft-info',
                                    'completed' => 'badge-soft-success',
                                    'rejected' => 'badge-soft-danger'
                                ][$return->status] ?? 'badge-soft-secondary';
                            @endphp
                            <tr class="return-row" data-search="{{ strtolower('#' . $return->id . ' ' . $orderNo . ' ' . $customerName . ' ' . $return->status) }}" data-status="{{ strtolower($return->status) }}">
                                <td class="ps-4 text-muted fw-semibold">#{{ $return->id }}</td>
                                <td>
                                    <span class="text-secondary small fw-semibold">
                                        {{ $return->return_date ? $return->return_date->format('d M Y') : 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold text-primary font-monospace">#{{ $orderNo }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark d-block">{{ $customerName }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-soft-primary px-2 py-1 rounded-2 fs-7">
                                        {{ $return->items->count() }} {{ Str::plural('Item', $return->items->count()) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-soft-danger px-3 py-1 rounded-pill fs-7">
                                        ৳{{ number_format($return->total_refund_amount, 2) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $statusClass }} px-3 py-1 rounded-pill text-capitalize fs-7">
                                        {{ ucfirst($return->status) }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="dropdown">
                                        <a href="javascript:void(0)" class="btn-action-icon shadow-none" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('returns.show', $return->id) }}">
                                                    <i class="fe fe-eye text-primary"></i>
                                                    <span>View Details</span>
                                                </a>
                                            </li>
                                            @if($return->isPending())
                                                <li>
                                                    <form method="POST" action="{{ route('returns.approve', $return->id) }}" onsubmit="return confirm('Approve this product return request?')">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="dropdown-item py-2 d-flex align-items-center gap-2 text-success">
                                                            <i class="fe fe-check-circle text-success"></i>
                                                            <span>Approve Return</span>
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item py-2 d-flex align-items-center gap-2 text-danger" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $return->id }}">
                                                        <i class="fe fe-x-circle text-danger"></i>
                                                        <span>Reject Return</span>
                                                    </a>
                                                </li>
                                            @endif
                                            @if($return->isApproved())
                                                <li>
                                                    <form method="POST" action="{{ route('returns.complete', $return->id) }}" onsubmit="return confirm('Complete return and update inventory stock?')">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="dropdown-item py-2 d-flex align-items-center gap-2 text-primary">
                                                            <i class="fe fe-box text-primary"></i>
                                                            <span>Complete & Update Stock</span>
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                            @if($return->isPending() || $return->isRejected())
                                                <li><hr class="dropdown-divider opacity-50"></li>
                                                <li>
                                                    <form method="POST" action="{{ route('returns.destroy', $return->id) }}" onsubmit="return confirm('Are you sure you want to delete this return record?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item py-2 d-flex align-items-center gap-2 text-danger">
                                                            <i class="fe fe-trash-2 text-danger"></i>
                                                            <span>Delete Record</span>
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>

                                    <!-- Reject Modal -->
                                    @if($return->isPending())
                                        <div class="modal fade text-start" id="rejectModal{{ $return->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow rounded-3">
                                                    <form method="POST" action="{{ route('returns.reject', $return->id) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <div class="modal-header border-bottom">
                                                            <h5 class="modal-title fw-bold text-dark">Reject Return #{{ $return->id }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body p-4">
                                                            <div class="mb-3">
                                                                <label class="form-label small text-secondary fw-semibold mb-1">Rejection Reason <span class="text-danger">*</span></label>
                                                                <textarea name="reason" class="form-control border-light-subtle" rows="3" placeholder="Enter reason for rejection..." required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-top pt-3">
                                                            <button type="button" class="btn btn-outline-secondary rounded-2 px-3" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-danger rounded-2 px-4">Reject Return</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="avatar avatar-xl bg-primary-light text-primary rounded-circle mb-3 d-flex align-items-center justify-content-center">
                                            <i class="fe fe-rotate-ccw fs-1"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">No Product Returns Found</h5>
                                        <p class="text-muted small mb-3">Record customer product returns to adjust stock and refunds</p>
                                        <a href="{{ route('returns.create') }}" class="btn btn-primary btn-sm px-3 rounded-2">
                                            New Return
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
    const searchInput = document.getElementById('returnSearchInput');
    const statusSelect = document.getElementById('returnStatusFilterSelect');
    const rows = document.querySelectorAll('.return-row');
    const visibleCountSpan = document.getElementById('visibleReturnCount');

    function filterTable() {
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const statusFilter = statusSelect ? statusSelect.value : 'all';
        let visibleCount = 0;

        rows.forEach(row => {
            const rowSearchText = row.dataset.search || '';
            const rowStatus = row.dataset.status || '';

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
