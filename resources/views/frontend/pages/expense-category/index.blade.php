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
                <h4 class="card-title fw-bold text-dark mb-1">Expense Categories</h4>
                <p class="text-muted small mb-0">Manage expense classification categories for financial tracking</p>
            </div>
            <div>
                <button type="button" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#add-expense-category-modal">
                    <i class="fe fe-plus-circle fs-6"></i>
                    <span>Add Category</span>
                </button>
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
                        <i class="fe fe-folder fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Total Categories</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($expenseCategories->count()) }}</h4>
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
                        <h6 class="text-muted fw-normal mb-1">Active Categories</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($expenseCategories->where('status', 1)->count()) }}</h4>
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
                        <h6 class="text-muted fw-normal mb-1">Inactive Categories</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($expenseCategories->where('status', 0)->count()) }}</h4>
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
                        <input type="text" id="expenseCategorySearchInput" class="form-control border-light-subtle" placeholder="Search category name..." autocomplete="off">
                    </div>
                </div>
                <div class="col-12 col-md-6 text-md-end text-muted small">
                    Showing <span id="visibleExpenseCategoryCount" class="fw-bold text-dark">{{ $expenseCategories->count() }}</span> records
                </div>
            </div>
        </div>

        <div class="card-body p-0" style="overflow: visible;">
            <div class="table-responsive" style="overflow: visible !important;">
                <table class="table table-hover table-custom align-middle mb-0" id="expenseCategoriesTable">
                    <thead class="bg-light text-secondary fs-7 text-uppercase">
                        <tr>
                            <th class="ps-4" style="width: 80px;">#</th>
                            <th>Category Name</th>
                            <th>Status</th>
                            <th class="pe-4 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($expenseCategories as $category)
                            <tr class="expense-category-row" data-search="{{ strtolower($category->name) }}">
                                <td class="ps-4 text-muted fw-semibold">{{ $loop->iteration }}</td>
                                <td>
                                    <span class="fw-bold text-dark">{{ $category->name }}</span>
                                </td>
                                <td>
                                    @if ($category->status)
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
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#edit-expense-category-modal{{ $category->id }}">
                                                    <i class="fe fe-edit text-primary"></i>
                                                    <span>Edit Category</span>
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider opacity-50"></li>
                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2 text-danger" href="javascript:void(0)" onclick="if(confirm('Are you sure you want to delete this category?')) document.getElementById('deleteForm{{ $category->id }}').submit();">
                                                    <i class="fe fe-trash-2 text-danger"></i>
                                                    <span>Delete Category</span>
                                                </a>
                                                <form id="deleteForm{{ $category->id }}" method="POST" action="{{ route('expense-categories.destroy', $category->id) }}" class="d-none">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </li>
                                        </ul>
                                    </div>

                                    <!-- Modal: Edit Category -->
                                    <div id="edit-expense-category-modal{{ $category->id }}" class="modal fade" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg rounded-3 text-start">
                                                <div class="modal-header border-bottom pb-3">
                                                    <h5 class="modal-title fw-bold text-dark">Edit Expense Category</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form method="POST" action="{{ route('expense-categories.update', $category->id) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body p-4">
                                                        <div class="mb-3">
                                                            <label class="form-label small text-secondary fw-semibold mb-1">Category Name <span class="text-danger">*</span></label>
                                                            <input type="text" name="name" class="form-control border-light-subtle" value="{{ $category->name }}" placeholder="Enter category name" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label small text-secondary fw-semibold mb-1">Status <span class="text-danger">*</span></label>
                                                            <select name="status" class="form-select border-light-subtle" required>
                                                                <option value="1" {{ $category->status == 1 ? 'selected' : '' }}>Active</option>
                                                                <option value="0" {{ $category->status == 0 ? 'selected' : '' }}>Inactive</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-top pt-3">
                                                        <button type="button" class="btn btn-outline-secondary px-4 rounded-3" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary px-4 rounded-3">Update Category</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /Modal: Edit Category -->
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="avatar avatar-xl bg-primary-light text-primary rounded-circle mb-3 d-flex align-items-center justify-content-center">
                                            <i class="fe fe-folder fs-1"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">No Expense Categories Found</h5>
                                        <p class="text-muted small mb-3">Add categories to organize your business expenses</p>
                                        <button type="button" class="btn btn-primary btn-sm px-3 rounded-2" data-bs-toggle="modal" data-bs-target="#add-expense-category-modal">
                                            Add Category
                                        </button>
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

<!-- Modal: Add Category -->
<div id="add-expense-category-modal" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header border-bottom pb-3">
                <h5 class="modal-title fw-bold text-dark">Add Expense Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('expense-categories.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small text-secondary fw-semibold mb-1">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control border-light-subtle" placeholder="Enter category name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-secondary fw-semibold mb-1">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select border-light-subtle" required>
                            <option value="1" selected>Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top pt-3">
                    <button type="button" class="btn btn-outline-secondary px-4 rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 rounded-3">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('expenseCategorySearchInput');
    const rows = document.querySelectorAll('.expense-category-row');
    const visibleCountSpan = document.getElementById('visibleExpenseCategoryCount');

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
