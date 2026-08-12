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
                <h4 class="card-title fw-bold text-dark mb-1">Bank Accounts</h4>
                <p class="text-muted small mb-0">Manage company bank accounts, routing details, and default billing accounts</p>
            </div>
            <div>
                <a href="{{ route('bank-details.create') }}" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2">
                    <i class="fe fe-plus-circle fs-6"></i>
                    <span>Add Bank Account</span>
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
                        <i class="fe fe-credit-card fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Total Bank Accounts</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($banks->count()) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-4 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-success-light text-success rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-check-circle fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Active Accounts</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($banks->where('is_active', true)->count()) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-4 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-info-light text-info rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-award fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Default Account</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ $banks->where('is_default', true)->first()->bank_name ?? 'None' }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Summary Stats Bar -->

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-3">
        <!-- Live Search Header -->
        <div class="card-header bg-white py-3 border-bottom border-light">
            <div class="row align-items-center g-3">
                <div class="col-12 col-md-6">
                    <div class="search-box-custom">
                        <input type="text" id="bankSearchInput" class="form-control border-light-subtle" placeholder="Search bank name, account name, number, branch..." autocomplete="off">
                    </div>
                </div>
                <div class="col-12 col-md-6 text-md-end text-muted small">
                    Showing <span id="visibleBankCount" class="fw-bold text-dark">{{ $banks->count() }}</span> records
                </div>
            </div>
        </div>

        <div class="card-body p-0" style="overflow: visible;">
            <div class="table-responsive" style="overflow: visible !important;">
                <table class="table table-hover table-custom align-middle mb-0" id="banksTable">
                    <thead class="bg-light text-secondary fs-7 text-uppercase">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Account Name</th>
                            <th>Bank Name</th>
                            <th>Branch</th>
                            <th>Account Number</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Default</th>
                            <th class="pe-4 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($banks as $bank)
                            <tr class="bank-row" data-search="{{ strtolower($bank->account_name . ' ' . $bank->bank_name . ' ' . $bank->branch . ' ' . $bank->account_number . ' ' . $bank->account_type) }}">
                                <td class="ps-4 text-muted fw-semibold">{{ $loop->iteration }}</td>
                                <td>
                                    <span class="fw-bold text-dark d-block">{{ $bank->account_name }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold text-primary">{{ $bank->bank_name }}</span>
                                </td>
                                <td>
                                    <span class="text-muted small">{{ $bank->branch }}</span>
                                </td>
                                <td>
                                    <span class="font-monospace fw-bold text-dark">{{ $bank->account_number }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-soft-info px-3 py-1 rounded-pill fs-7 text-capitalize">{{ $bank->account_type }}</span>
                                </td>
                                <td>
                                    @if($bank->is_active)
                                        <span class="badge badge-soft-success px-3 py-1 rounded-pill fs-7">Active</span>
                                    @else
                                        <span class="badge badge-soft-danger px-3 py-1 rounded-pill fs-7">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($bank->is_default)
                                        <span class="badge badge-soft-primary px-3 py-1 rounded-pill fs-7">Default</span>
                                    @else
                                        <form action="{{ route('bank-details.set-default', $bank->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-primary rounded-2 py-0 px-2 fs-7">Set Default</button>
                                        </form>
                                    @endif
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="dropdown d-inline-block">
                                        <a href="javascript:void(0)" class="btn-action-icon shadow-none" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('bank-details.edit', $bank->id) }}">
                                                    <i class="fe fe-edit text-primary"></i>
                                                    <span>Edit Details</span>
                                                </a>
                                            </li>
                                            @if(!$bank->is_default)
                                                <li>
                                                    <form action="{{ route('bank-details.set-default', $bank->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item py-2 d-flex align-items-center gap-2 border-0 bg-transparent">
                                                            <i class="fe fe-check-circle text-success"></i>
                                                            <span>Make Default</span>
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                            <li><hr class="dropdown-divider opacity-50"></li>
                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2 text-danger" href="javascript:void(0)"
                                                    onclick="if (confirm('Are you sure you want to delete this bank account?')) { document.getElementById('bankDelete{{ $bank->id }}').submit(); }">
                                                    <i class="fe fe-trash-2 text-danger"></i>
                                                    <span>Delete Account</span>
                                                </a>
                                                <form id="bankDelete{{ $bank->id }}" action="{{ route('bank-details.destroy', $bank->id) }}" method="POST" class="d-none">
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
                                <td colspan="9" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="avatar avatar-xl bg-primary-light text-primary rounded-circle mb-3 d-flex align-items-center justify-content-center">
                                            <i class="fe fe-credit-card fs-1"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">No Bank Accounts Found</h5>
                                        <p class="text-muted small mb-3">Add company bank details for bills and invoices</p>
                                        <a href="{{ route('bank-details.create') }}" class="btn btn-primary btn-sm px-3 rounded-2">
                                            Add Bank Account
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
    const searchInput = document.getElementById('bankSearchInput');
    const rows = document.querySelectorAll('.bank-row');
    const visibleCountSpan = document.getElementById('visibleBankCount');

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
