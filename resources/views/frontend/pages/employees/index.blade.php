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
                <h4 class="card-title fw-bold text-dark mb-1">Employee Directory</h4>
                <p class="text-muted small mb-0">Manage company staff, designations, contact numbers, and employment statuses</p>
            </div>
            <div>
                <a href="{{ route('employees.create') }}" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2">
                    <i class="fe fe-plus-circle fs-6"></i>
                    <span>Add Employee</span>
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
                        <i class="fe fe-users fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Total Employees</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($employees->count()) }}</h4>
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
                        <h6 class="text-muted fw-normal mb-1">Active Staff</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($employees->where('status', 'active')->count()) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-4 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-danger-light text-danger rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-x-circle fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Inactive Staff</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($employees->where('status', '!=', 'active')->count()) }}</h4>
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
                        <input type="text" id="employeeSearchInput" class="form-control border-light-subtle" placeholder="Search by name, ID, phone, designation..." value="{{ request('search') }}" autocomplete="off">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <select id="employeeStatusFilter" class="form-select border-light-subtle">
                        <option value="all">All Statuses</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-12 col-md-3 text-md-end text-muted small">
                    Showing <span id="visibleEmployeeCount" class="fw-bold text-dark">{{ $employees->count() }}</span> records
                </div>
            </div>
        </div>

        <div class="card-body p-0" style="overflow: visible;">
            <div class="table-responsive" style="overflow: visible !important;">
                <table class="table table-hover table-custom align-middle mb-0" id="employeesTable">
                    <thead class="bg-light text-secondary fs-7 text-uppercase">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Employee ID</th>
                            <th>Name</th>
                            <th>Designation</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th class="pe-4 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($employees as $key => $employee)
                            <tr class="employee-row" data-status="{{ strtolower($employee->status) }}" data-search="{{ strtolower($employee->name . ' ' . $employee->employee_id . ' ' . $employee->phone . ' ' . $employee->designation) }}">
                                <td class="ps-4 text-muted fw-semibold">{{ $key + 1 }}</td>
                                <td>
                                    <span class="badge badge-soft-primary px-3 py-1 rounded-pill fs-7 font-monospace fw-bold">{{ $employee->employee_id }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark d-block">{{ $employee->name }}</span>
                                </td>
                                <td>
                                    <span class="text-secondary small">{{ $employee->designation ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="text-muted small">{{ $employee->phone ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    @if ($employee->status == 'active')
                                        <span class="badge badge-soft-success px-3 py-1 rounded-pill fs-7">Active</span>
                                    @else
                                        <span class="badge badge-soft-danger px-3 py-1 rounded-pill fs-7">Inactive</span>
                                    @endif
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="dropdown d-inline-block">
                                        <a href="javascript:void(0)" class="btn-action-icon shadow-none" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('employees.edit', $employee->id) }}">
                                                    <i class="fe fe-edit text-primary"></i>
                                                    <span>Edit Employee</span>
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider opacity-50"></li>
                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2 text-danger" href="javascript:void(0)" onclick="if (confirm('Are you sure you want to delete this employee?')) { document.getElementById('empDelete{{ $employee->id }}').submit(); }">
                                                    <i class="fe fe-trash-2 text-danger"></i>
                                                    <span>Delete Employee</span>
                                                </a>
                                                <form id="empDelete{{ $employee->id }}" action="{{ route('employees.destroy', $employee->id) }}" method="POST" class="d-none">
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
                                <td colspan="7" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="avatar avatar-xl bg-primary-light text-primary rounded-circle mb-3 d-flex align-items-center justify-content-center">
                                            <i class="fe fe-users fs-1"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">No Employees Found</h5>
                                        <p class="text-muted small mb-3">Add team members to your employee directory</p>
                                        <a href="{{ route('employees.create') }}" class="btn btn-primary btn-sm px-3 rounded-2">
                                            Add Employee
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
    const searchInput = document.getElementById('employeeSearchInput');
    const statusFilter = document.getElementById('employeeStatusFilter');
    const rows = document.querySelectorAll('.employee-row');
    const visibleCountSpan = document.getElementById('visibleEmployeeCount');

    function filterTable() {
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const selectedStatus = statusFilter ? statusFilter.value : 'all';
        let visibleCount = 0;

        rows.forEach(row => {
            const rowSearchText = row.dataset.search || '';
            const rowStatus = row.dataset.status || '';

            const matchesSearch = (query === '' || rowSearchText.includes(query));
            const matchesStatus = (selectedStatus === 'all' || rowStatus === selectedStatus);

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
    if (statusFilter) statusFilter.addEventListener('change', filterTable);
});
</script>
@endsection
