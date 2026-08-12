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

    .table-custom th, .table-custom td {
        white-space: nowrap;
    }
    .table-responsive {
        overflow: visible !important;
    }
    .img-thumbnail-custom {
        width: 32px !important;
        height: 32px !important;
        max-width: 32px !important;
        max-height: 32px !important;
        object-fit: cover !important;
        border-radius: 6px !important;
        border: 1px solid #e9ecef !important;
        display: inline-block !important;
    }
    .img-preview-edit-modal {
        width: 44px !important;
        height: 44px !important;        
        max-width: 44px !important;
        max-height: 44px !important;
        object-fit: cover !important;
        border-radius: 8px !important;
        border: 1px solid #dbe2ea !important;
    }
</style>
@endpush

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1">Category Directory</h4>
                <p class="text-muted small mb-0">Organize product categories, hierarchy, and catalog classification</p>
            </div>
            <div>
                <a class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#add-category-modal">
                    <i class="fe fe-plus-circle fs-6"></i>
                    <span>Add New Category</span>
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Add Category Modal -->
    <div id="add-category-modal" class="modal fade" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header bg-light py-3 border-bottom">
                    <h5 class="modal-title fw-bold text-dark">Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form method="POST" action="{{ route('categories.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="cat_name" class="form-label fw-semibold small text-secondary">Category Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="cat_name" placeholder="e.g. Laptops & Computers" required>
                            </div>
                            <div class="col-md-6">
                                <label for="cat_image" class="form-label fw-semibold small text-secondary">Category Image</label>
                                <input type="file" class="form-control" name="image" id="cat_image" accept="image/*">
                            </div>
                            <div class="col-md-6">
                                <label for="cat_order" class="form-label fw-semibold small text-secondary">Sort Order</label>
                                <input type="number" class="form-control" name="order_by" id="cat_order" value="0">
                            </div>
                            <div class="col-md-6">
                                <label for="cat_status" class="form-label fw-semibold small text-secondary">Status <span class="text-danger">*</span></label>
                                <select class="form-select" name="status" id="cat_status" required>
                                    <option value="1" selected>Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="cat_desc" class="form-label fw-semibold small text-secondary">Description</label>
                                <textarea class="form-control" name="description" id="cat_desc" rows="3" placeholder="Enter brief category description..."></textarea>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                            <button type="button" class="btn btn-light px-4 rounded-3 text-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary px-4 rounded-3 shadow-sm">Save Category</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /Add Category Modal -->

    <!-- Summary Stats Bar -->
    <div class="row g-3 mb-4">
        <div class="col-xl-4 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-primary-light text-primary rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-layers fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Total Categories</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($categories->count()) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-success-light text-success rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-check-circle fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Active Categories</h6>
                        <h4 class="mb-0 fw-bold text-dark">
                            {{ number_format($categories->filter(fn($c) => in_array($c->status, ['1', 1, 'active']))->count()) }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-12 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-danger-light text-danger rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-x-circle fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Inactive Categories</h6>
                        <h4 class="mb-0 fw-bold text-dark">
                            {{ number_format($categories->filter(fn($c) => !in_array($c->status, ['1', 1, 'active']))->count()) }}
                        </h4>
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
            <div class="row align-items-center g-3">
                <div class="col-12 col-md-6 col-lg-5">
                    <div class="search-box-custom">
                        <input type="text" id="categorySearchInput" class="form-control border-light-subtle" placeholder="Search by category name, slug...">
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
                    Showing <span id="visibleCategoryCount" class="fw-bold text-dark">{{ $categories->count() }}</span> of {{ $categories->count() }} records
                </div>
            </div>
        </div>

        <!-- Table Body -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-custom align-middle mb-0" id="categoryTable">
                    <thead class="bg-light text-secondary fs-7 text-uppercase">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Image</th>
                            <th>Category Name</th>
                            <!-- <th>Slug</th> -->
                            <!-- <th>Sort Order</th> -->
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($categories as $category)
                            @php
                                $isActive = in_array($category->status, ['1', 1, 'active']);
                            @endphp
                            <tr class="category-row" data-status="{{ $isActive ? 'active' : 'inactive' }}" data-search="{{ strtolower($category->name . ' ' . $category->slug) }}">
                                <td class="ps-4 text-muted fw-semibold">{{ $loop->iteration }}</td>
                                <td>
                                    @if ($category->image)
                                        <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" class="img-thumbnail-custom" style="width:32px!important; height:32px!important; max-width:32px!important;">
                                    @else
                                        <div class="img-thumbnail-custom bg-light d-flex align-items-center justify-content-center text-muted fs-7" style="width:32px!important; height:32px!important;">
                                            <i class="fe fe-image"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#edit-category-modal{{ $category->id }}" class="fw-bold text-dark hover-primary mb-0 text-decoration-none d-block" title="{{ $category->name }}">
                                        {{ Str::limit($category->name, 40) }}
                                    </a>
                                </td>
                                <!-- <td>
                                    <span class="text-secondary small d-inline-block">
                                        {{ $category->slug }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1 rounded-2">
                                        {{ $category->order_by ?? 0 }}
                                    </span>
                                </td> -->
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
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#edit-category-modal{{ $category->id }}">
                                                    <i class="fe fe-edit text-primary"></i>
                                                    <span>Edit Category</span>
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider opacity-50"></li>
                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2 text-danger" href="javascript:void(0)"
                                                    onclick="if (confirm('Are you sure you want to delete this category?')) { document.getElementById('deleteCat{{ $category->id }}').submit(); }">
                                                    <i class="fe fe-trash-2 text-danger"></i>
                                                    <span>Delete Category</span>
                                                </a>
                                                <form id="deleteCat{{ $category->id }}" action="{{ route('categories.destroy', $category->id) }}" method="POST" class="d-none">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>

                            <!-- Edit Category Modal -->
                            <div id="edit-category-modal{{ $category->id }}" class="modal fade" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content border-0 shadow-lg rounded-3">
                                        <div class="modal-header bg-light py-3 border-bottom">
                                            <h5 class="modal-title fw-bold text-dark">Edit Category Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4 text-start">
                                            <form method="POST" action="{{ route('categories.update', $category->id) }}" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <div class="row g-3 mb-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold small text-secondary">Category Name <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="name" value="{{ old('name', $category->name) }}" required>
                                                    </div>
                                                <div class="col-md-6">
                                                        <label class="form-label fw-semibold small text-secondary">Category Image</label>
                                                        <div class="d-flex align-items-center gap-2">
                                                            @if($category->image)
                                                                <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" class="img-preview-edit-modal flex-shrink-0" style="width:40px!important; height:40px!important; max-width:40px!important; max-height:40px!important;">
                                                            @endif
                                                            <input type="file" class="form-control" name="image" accept="image/*">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold small text-secondary">Sort Order</label>
                                                        <input type="number" class="form-control" name="order_by" value="{{ old('order_by', $category->order_by) }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold small text-secondary">Status <span class="text-danger">*</span></label>
                                                        <select class="form-select" name="status" required>
                                                            <option value="1" {{ $category->status == 1 ? 'selected' : '' }}>Active</option>
                                                            <option value="0" {{ $category->status == 0 ? 'selected' : '' }}>Inactive</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label fw-semibold small text-secondary">Description</label>
                                                        <textarea class="form-control" name="description" rows="3">{{ old('description', $category->description) }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                                                    <button type="button" class="btn btn-light px-4 rounded-3 text-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary px-4 rounded-3 shadow-sm">Update Category</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /Edit Category Modal -->
                        @empty
                            <tr id="emptyStateRow">
                                <td colspan="7" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="avatar avatar-xl bg-primary-light text-primary rounded-circle mb-3 d-flex align-items-center justify-content-center">
                                            <i class="fe fe-layers fs-1"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">No Categories Found</h5>
                                        <p class="text-muted small mb-3">Get started by creating your first product category</p>
                                        <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#add-category-modal" class="btn btn-primary btn-sm px-3 rounded-2">
                                            Add Category
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
    const searchInput = document.getElementById('categorySearchInput');
    const statusSelect = document.getElementById('statusFilterSelect');
    const rows = document.querySelectorAll('.category-row');
    const visibleCountSpan = document.getElementById('visibleCategoryCount');

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
