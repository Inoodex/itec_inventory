@extends('frontend.layouts.app')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
                <h4 class="card-title fw-bold text-dark mb-1">Inventory Stock List</h4>
                <p class="text-muted small mb-0">Monitor product stock levels, opening quantities, available stock, and serial tracking</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a class="btn btn-outline-danger px-3 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2" href="{{ route('inventory.pdf') }}" target="_blank">
                    <i class="fe fe-file-text fs-6"></i>
                    <span>Export PDF</span>
                </a>
                <a class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#add-product-modal">
                    <i class="fe fe-plus-circle fs-6"></i>
                    <span>Add Opening Stock</span>
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Add Opening Stock Modal -->
    <div id="add-product-modal" class="modal fade" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header bg-light py-3 border-bottom">
                    <h5 class="modal-title fw-bold text-dark">Add Opening Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form method="POST" action="{{ route('inventory.store') }}">
                        @csrf
                        <div class="row g-3 mb-3">
                            <div class="col-12">
                                <label for="product_id" class="form-label fw-semibold small text-secondary">Select Product <span class="text-danger">*</span></label>
                                <select name="product_id" id="product_id" class="form-select select2" required>
                                    <option value="" disabled selected>-- Select Product --</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">
                                            {{ $product->name }} ({{ $product->model ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="opening_stock" class="form-label fw-semibold small text-secondary">Opening Stock <span class="text-danger">*</span></label>
                                <input type="number" name="opening_stock" id="opening_stock" class="form-control" min="0" value="0" required>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                            <button type="button" class="btn btn-light px-4 rounded-3 text-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary px-4 rounded-3 shadow-sm">Save Stock Entry</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /Add Opening Stock Modal -->

    <!-- Summary Stats Bar -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-primary-light text-primary rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-database fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Total Tracked Items</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($inventories->count()) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-success-light text-success rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-box fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Available Units</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($inventories->sum('current_stock')) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-warning-light text-warning rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-alert-triangle fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Low Stock Warning</h6>
                        <h4 class="mb-0 fw-bold text-dark">
                            {{ number_format($inventories->filter(fn($i) => $i->current_stock <= 5 && $i->current_stock > 0)->count()) }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-danger-light text-danger rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-x-circle fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Out of Stock</h6>
                        <h4 class="mb-0 fw-bold text-dark">
                            {{ number_format($inventories->filter(fn($i) => $i->current_stock == 0)->count()) }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Summary Stats Bar -->

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-3">
        <!-- Search Controls -->
        <div class="card-header bg-white py-3 border-bottom border-light">
            <div class="row align-items-center g-3">
                <div class="col-12 col-md-6 col-lg-5">
                    <div class="search-box-custom">
                        <input type="text" id="inventorySearchInput" class="form-control border-light-subtle" placeholder="Search product name, model...">
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-7 text-md-end text-muted small">
                    Showing <span id="visibleInventoryCount" class="fw-bold text-dark">{{ $inventories->count() }}</span> of {{ $inventories->count() }} inventory entries
                </div>
            </div>
        </div>

        <!-- Table Body -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-custom align-middle mb-0" id="inventoryTable">
                    <thead class="bg-light text-secondary fs-7 text-uppercase">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Product & Model</th>
                            <th>Opening Stock</th>
                            <th>Current Stock</th>
                            <th>Serial Tracking</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($inventories as $inventory)
                            @php
                                $stock = $inventory->current_stock ?? 0;
                                $searchString = strtolower(($inventory->product?->name ?? '') . ' ' . ($inventory->product?->model ?? ''));
                            @endphp
                            <tr class="inventory-row" data-search="{{ $searchString }}">
                                <td class="ps-4 text-muted fw-semibold">{{ $loop->iteration }}</td>
                                <td>
                                    <div>
                                        <span class="fw-bold text-dark d-block text-truncate" title="{{ $inventory->product?->name }}">
                                            {{ Str::limit($inventory->product?->name ?? 'Product Not Found', 40) }}
                                        </span>
                                        <small class="text-muted fs-7">Model: {{ $inventory->product?->model ?? 'N/A' }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-3 py-1 rounded-2">
                                        {{ $inventory->opening_stock ?? 0 }}
                                    </span>
                                </td>
                                <td>
                                    @if ($stock > 5)
                                        <span class="badge badge-soft-success px-3 py-2 rounded-pill fs-7">
                                            <i class="fe fe-check-circle me-1"></i> {{ $stock }} Units Available
                                        </span>
                                    @elseif($stock > 0)
                                        <span class="badge badge-soft-warning px-3 py-2 rounded-pill fs-7">
                                            <i class="fe fe-alert-triangle me-1"></i> {{ $stock }} Units Low
                                        </span>
                                    @else
                                        <span class="badge badge-soft-danger px-3 py-2 rounded-pill fs-7">
                                            <i class="fe fe-x-circle me-1"></i> Out of Stock
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if ($inventory->product?->is_serialized)
                                        <a href="javascript:void(0)" class="badge badge-soft-info px-3 py-2 rounded-pill fs-7 view-serials-btn text-decoration-none"
                                            data-product-id="{{ $inventory->product_id }}"
                                            data-product-name="{{ $inventory->product->name }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#view-serials-modal">
                                            <i class="fas fa-barcode me-1"></i> View Serial List
                                        </a>
                                    @else
                                        <span class="badge bg-light text-secondary border px-3 py-1 rounded-pill fs-7">
                                            Non-Serial
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr id="emptyStateRow">
                                <td colspan="5" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="avatar avatar-xl bg-primary-light text-primary rounded-circle mb-3 d-flex align-items-center justify-content-center">
                                            <i class="fe fe-database fs-1"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">No Inventory Records Found</h5>
                                        <p class="text-muted small mb-3">Add opening stock to begin tracking inventory items</p>
                                        <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#add-product-modal" class="btn btn-primary btn-sm px-3 rounded-2">
                                            Add Opening Stock
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

<!-- View Serials Modal -->
<div id="view-serials-modal" class="modal fade" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header bg-light py-3 border-bottom">
                <h5 class="modal-title fw-bold text-dark">Available Serials - <span id="modal-product-name" class="text-primary"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-secondary fs-7 text-uppercase">
                            <tr>
                                <th class="ps-3">#</th>
                                <th>Serial Number</th>
                                <th>Vendor</th>
                                <th>Purchase Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="serials-table-body">
                            <!-- AJAX content -->
                        </tbody>
                    </table>
                    <div id="serials-loader" class="text-center py-4" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div id="serials-empty-msg" class="text-center py-4" style="display: none;">
                        <span class="text-muted">No available serial numbers found for this product.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('inventorySearchInput');
    const rows = document.querySelectorAll('.inventory-row');
    const visibleCountSpan = document.getElementById('visibleInventoryCount');

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const query = searchInput.value.toLowerCase().trim();
            let visibleCount = 0;

            rows.forEach(row => {
                const rowSearchText = row.dataset.search;
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
        });
    }

    $('.modal').on('shown.bs.modal', function () {
        $(this).find('.select2').select2({
            dropdownParent: $(this),
            width: '100%'
        });
    });

    $(document).on('click', '.view-serials-btn', function () {
        const productId = $(this).data('product-id');
        const productName = $(this).data('product-name');

        $('#modal-product-name').text(productName);
        $('#serials-table-body').empty();
        $('#serials-loader').show();
        $('#serials-empty-msg').hide();

        let url = '{{ route("inventory.serials", ":id") }}';
        url = url.replace(':id', productId);

        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                $('#serials-loader').hide();
                const serials = response.serials;

                if (serials && serials.length > 0) {
                    serials.forEach((serial, index) => {
                        const vendorName = serial.purchase && serial.purchase.vendor ? serial.purchase.vendor.name : 'N/A';
                        const purchaseDate = serial.created_at ? new Date(serial.created_at).toLocaleDateString() : 'N/A';

                        $('#serials-table-body').append(`
                            <tr>
                                <td class="ps-3 text-muted fw-semibold">${index + 1}</td>
                                <td><strong class="text-primary font-monospace">${serial.serial_number}</strong></td>
                                <td>${vendorName}</td>
                                <td>${purchaseDate}</td>
                                <td><span class="badge badge-soft-success px-3 py-1 rounded-pill">${serial.status}</span></td>
                            </tr>
                        `);
                    });
                } else {
                    $('#serials-empty-msg').show();
                }
            },
            error: function () {
                $('#serials-loader').hide();
                alert('Error fetching serial numbers. Please try again.');
            }
        });
    });
});
</script>
@endpush
@endsection