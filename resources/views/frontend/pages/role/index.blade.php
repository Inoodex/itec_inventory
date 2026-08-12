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
                <h4 class="card-title fw-bold text-dark mb-1">Role Management</h4>
                <p class="text-muted small mb-0">Define user access roles and configure authorization levels</p>
            </div>
            <div>
                <a href="{{ route('role.create') }}" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2">
                    <i class="fe fe-plus-circle fs-6"></i>
                    <span>Create Role</span>
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Summary Stats Bar -->
    <div class="row g-3 mb-4">
        <div class="col-xl-6 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-primary-light text-primary rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-shield fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Defined User Roles</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($users->count()) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Summary Stats Bar -->

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-3">
        <!-- Search Header -->
        <div class="card-header bg-white py-3 border-bottom border-light">
            <div class="row align-items-center g-3">
                <div class="col-12 col-md-6">
                    <div class="search-box-custom">
                        <input type="text" id="roleSearchInput" class="form-control border-light-subtle" placeholder="Search role name..." autocomplete="off">
                    </div>
                </div>
                <div class="col-12 col-md-6 text-md-end text-muted small">
                    Showing <span id="visibleRoleCount" class="fw-bold text-dark">{{ $users->count() }}</span> records
                </div>
            </div>
        </div>

        <div class="card-body p-0" style="overflow: visible;">
            <div class="table-responsive" style="overflow: visible !important;">
                <table class="table table-hover table-custom align-middle mb-0" id="rolesTable">
                    <thead class="bg-light text-secondary fs-7 text-uppercase">
                        <tr>
                            <th class="ps-4" style="width: 60px;">#</th>
                            <th style="width: 40%;">Role Name</th>
                            <th class="text-end pe-4" style="width: 100px;">Action</th>
                            <th class="border-start ps-4" style="width: 60px;">#</th>
                            <th style="width: 40%;">Role Name</th>
                              <th class="text-end pe-4" style="width: 100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($users->chunk(2) as $chunkIndex => $chunk)
                            <tr class="role-row" data-search="{{ strtolower($chunk->pluck('name')->implode(' ')) }}">
                                @foreach ($chunk as $itemIndex => $item)
                                    <td class="{{ !$loop->first ? 'border-start ps-4' : 'ps-4' }} text-muted fw-semibold" style="width: 60px;">
                                        {{ $loop->parent->index * 2 + $loop->iteration }}
                                    </td>
                                    <td>
                                        <span class="badge badge-soft-primary px-3 py-2 rounded-pill fs-7 fw-bold">
                                            {{ $item->name }}
                                        </span>
                                    </td>
                                    <td class="pe-4 text-end" style="width: 100px;">
                                        <div class="dropdown d-inline-block">
                                            <a href="javascript:void(0)" class="btn-action-icon shadow-none" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                                <li>
                                                    <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('role.edit', $item->id) }}">
                                                        <i class="fe fe-edit text-primary"></i>
                                                        <span>Edit Role</span>
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider opacity-50"></li>
                                                <li>
                                                    <a class="dropdown-item py-2 d-flex align-items-center gap-2 text-danger" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#deleteRoleModal{{ $item->id }}">
                                                        <i class="fe fe-trash-2 text-danger"></i>
                                                        <span>Delete Role</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>

                                        <!-- Delete Modal -->
                                        <div id="deleteRoleModal{{ $item->id }}" class="modal fade" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow-lg rounded-3 text-start">
                                                    <div class="modal-header border-bottom-0 pb-0">
                                                        <h5 class="modal-title fw-bold text-dark">Confirm Delete</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body py-4">
                                                        <p class="mb-0 text-muted">Are you sure you want to delete role <strong class="text-dark">{{ $item->name }}</strong>? Users assigned to this role may lose access permissions.</p>
                                                    </div>
                                                    <div class="modal-footer border-top-0 pt-0">
                                                        <button type="button" class="btn btn-outline-secondary px-4 rounded-3" data-bs-dismiss="modal">Cancel</button>
                                                        <form action="{{ route('role.destroy', $item->id) }}" method="POST" class="d-inline">
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
                                @endforeach
                                @if ($chunk->count() < 2)
                                    <td class="border-start"></td>
                                    <td></td>
                                    <td class="pe-4"></td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="avatar avatar-xl bg-primary-light text-primary rounded-circle mb-3 d-flex align-items-center justify-content-center">
                                            <i class="fe fe-shield fs-1"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">No Roles Found</h5>
                                        <p class="text-muted small mb-3">Create access roles to organize user permissions</p>
                                        <a href="{{ route('role.create') }}" class="btn btn-primary btn-sm px-3 rounded-2">
                                            Create Role
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
    const searchInput = document.getElementById('roleSearchInput');
    const rows = document.querySelectorAll('.role-row');
    const visibleCountSpan = document.getElementById('visibleRoleCount');

    function filterTable() {
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        let visibleCount = 0;

        rows.forEach(row => {
            const rowSearchText = row.dataset.search || '';
            if (query === '' || rowSearchText.includes(query)) {
                row.style.display = '';
                visibleCount += 2;
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
