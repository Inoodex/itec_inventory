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

    @if (session('sweet_alert'))
        <script>
            Swal.fire({
                icon: '{{ session('sweet_alert.type') }}',
                title: '{{ session('sweet_alert.title') }}',
                text: '{{ session('sweet_alert.text') }}',
            });
        </script>
    @endif

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1">User Management</h4>
                <p class="text-muted small mb-0">Manage system users, assigned roles, contact details, and account statuses</p>
            </div>
            <div>
                <a href="{{ route('users.create') }}" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2">
                    <i class="fe fe-plus-circle fs-6"></i>
                    <span>Create User</span>
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
                        <h6 class="text-muted fw-normal mb-1">Total Users</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($users->count()) }}</h4>
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
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($users->where('status', '1')->count()) }}</h4>
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
                        <h6 class="text-muted fw-normal mb-1">Inactive Accounts</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($users->where('status', '!=', '1')->count()) }}</h4>
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
                        <input type="text" id="userSearchInput" class="form-control border-light-subtle" placeholder="Search user name, email, phone, role..." autocomplete="off">
                    </div>
                </div>
                <div class="col-12 col-md-6 text-md-end text-muted small">
                    Showing <span id="visibleUserCount" class="fw-bold text-dark">{{ $users->count() }}</span> records
                </div>
            </div>
        </div>

        <div class="card-body p-0" style="overflow: visible;">
            <div class="table-responsive" style="overflow: visible !important;">
                <table class="table table-hover table-custom align-middle mb-0" id="usersTable">
                    <thead class="bg-light text-secondary fs-7 text-uppercase">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Avatar</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role / Type</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($users as $item)
                            @php
                                $roleName = $item->roleName ?? 'N/A';
                            @endphp
                            <tr class="user-row" data-search="{{ strtolower($item->name . ' ' . $item->email . ' ' . $item->phone . ' ' . $roleName) }}">
                                <td class="ps-4 text-muted fw-semibold">{{ $loop->iteration }}</td>
                                <td>
                                    @if($item->images && file_exists(public_path('frontend/users/' . $item->images)))
                                        <img src="{{ asset('frontend/users/' . $item->images) }}" alt="{{ $item->name }}" class="rounded-circle object-fit-cover" width="40" height="40">
                                    @else
                                        <div class="avatar avatar-sm bg-primary-light text-primary rounded-circle fw-bold d-inline-flex align-items-center justify-content-center" width="40" height="40">
                                            {{ strtoupper(substr($item->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-bold text-dark d-block">{{ $item->name }}</span>
                                </td>
                                <td>
                                    <span class="text-secondary small">{{ $item->email }}</span>
                                </td>
                                <td>
                                    <span class="text-muted small">{{ $item->phone ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-soft-primary px-3 py-1 rounded-pill fs-7 text-capitalize">
                                        {{ $roleName }}
                                    </span>
                                </td>
                                <td>
                                    @if($item->status == '1')
                                        <span class="badge badge-soft-success px-3 py-1 rounded-pill fs-7">Active</span>
                                    @else
                                        <span class="badge badge-soft-danger px-3 py-1 rounded-pill fs-7">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <a href="javascript:void(0)" class="btn-action-icon shadow-none" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('users.edit', $item->id) }}">
                                                    <i class="fe fe-edit text-primary"></i>
                                                    <span>Edit User</span>
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider opacity-50"></li>
                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2 text-danger" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $item->id }}">
                                                    <i class="fe fe-trash-2 text-danger"></i>
                                                    <span>Delete User</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>

                                    <!-- Delete Modal -->
                                    <div id="deleteModal{{ $item->id }}" class="modal fade" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg rounded-3">
                                                <div class="modal-header border-bottom-0 pb-0">
                                                    <h5 class="modal-title fw-bold text-dark">Confirm Delete</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body py-4">
                                                    <p class="mb-0 text-muted">Are you sure you want to delete user <strong class="text-dark">{{ $item->name }}</strong>? This action cannot be undone.</p>
                                                </div>
                                                <div class="modal-footer border-top-0 pt-0">
                                                    <button type="button" class="btn btn-outline-secondary px-4 rounded-3" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('users.destroy', $item->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger px-4 rounded-3">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /Delete Modal -->
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="avatar avatar-xl bg-primary-light text-primary rounded-circle mb-3 d-flex align-items-center justify-content-center">
                                            <i class="fe fe-users fs-1"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">No Users Found</h5>
                                        <p class="text-muted small mb-3">Create your first system user to grant access</p>
                                        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm px-3 rounded-2">
                                            Create User
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
    const searchInput = document.getElementById('userSearchInput');
    const rows = document.querySelectorAll('.user-row');
    const visibleCountSpan = document.getElementById('visibleUserCount');

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