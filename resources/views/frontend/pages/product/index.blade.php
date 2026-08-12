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

        .table-custom th,
        .table-custom td {
            white-space: nowrap;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">

        <!-- Page Header -->
        <div class="page-header mb-4">
            <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h4 class="card-title fw-bold text-dark mb-1">Product Catalog</h4>
                    <p class="text-muted small mb-0">Manage inventory items, brand models, tracking types, and catalog
                        status</p>
                </div>
                <div>
                    <a class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2"
                        href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#add-payment-modal">
                        <i class="fe fe-plus-circle fs-6"></i>
                        <span>Add New Product</span>
                    </a>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        <!-- Add Product Modal -->
        <div id="add-payment-modal" class="modal fade" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg rounded-3">
                    <div class="modal-header bg-light py-3 border-bottom">
                        <h5 class="modal-title fw-bold text-dark">Add New Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="brand_id" class="form-label fw-semibold small text-secondary">Brand Name
                                        <span class="text-danger">*</span></label>
                                    <select class="form-select select2" name="brand_id" id="brand_id" required>
                                        <option value="">Select Brand</option>
                                        @foreach ($brands as $brand)
                                            <option {{ $brand->id == old('brand_id') ? 'selected' : '' }}
                                                value="{{ $brand->id }}">{{ $brand->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="category_id"
                                        class="form-label fw-semibold small text-secondary">Category</label>
                                    <select class="form-select select2" name="category_id" id="category_id">
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $category)
                                            <option {{ $category->id == old('category_id') ? 'selected' : '' }}
                                                value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-semibold small text-secondary">Product Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control"
                                        placeholder="Enter product name" value="{{ old('name') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="model_name" class="form-label fw-semibold small text-secondary">Product
                                        Model Name <span class="text-danger">*</span></label>
                                    <input type="text" name="model_name" id="model_name" class="form-control"
                                        placeholder="Enter product model name" value="{{ old('model_name') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="barcode"
                                        class="form-label fw-semibold small text-secondary d-flex justify-content-between">
                                        <span>Vendor Barcode / SKU</span>
                                        <a href="javascript:void(0)" onclick="generateRandomBarcode('barcode')"
                                            class="text-primary text-decoration-none small"><i
                                                class="fas fa-magic me-1"></i>Auto-Generate</a>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i
                                                class="fas fa-barcode text-secondary"></i></span>
                                        <input type="text" name="barcode" id="barcode" class="form-control border-start-0"
                                            placeholder="Scan or enter vendor barcode" value="{{ old('barcode') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="warranty" class="form-label fw-semibold small text-secondary">Warranty
                                        (Days)</label>
                                    <input type="text" name="warranty" id="warranty" class="form-control"
                                        placeholder="e.g. 365" value="{{ old('warranty') }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="status" class="form-label fw-semibold small text-secondary">Status <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" name="status" required>
                                        <option value="1" selected>Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="photos" class="form-label fw-semibold small text-secondary">Product
                                        Photos</label>
                                    <input type="file" name="photos[]" id="photos" class="form-control" multiple
                                        accept="image/*">
                                </div>
                                <div class="col-12">
                                    <div class="form-check form-switch pt-2">
                                        <input class="form-check-input" type="checkbox" name="is_serialized"
                                            id="is_serialized" value="1">
                                        <label class="form-check-label fw-semibold text-dark" for="is_serialized">Serialized
                                            Product (Track individual items by Serial Number)</label>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                                <button type="button" class="btn btn-light px-4 rounded-3 text-secondary"
                                    data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary px-4 rounded-3 shadow-sm">Save Product</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Add Product Modal -->

        <!-- Summary Stats Bar -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6 col-12">
                <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                    <div class="card-body d-flex align-items-center">
                        <div
                            class="avatar avatar-lg bg-primary-light text-primary rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                            <i class="fe fe-box fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted fw-normal mb-1">Total Products</h6>
                            <h4 class="mb-0 fw-bold text-dark">{{ number_format($products->count()) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-12">
                <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                    <div class="card-body d-flex align-items-center">
                        <div
                            class="avatar avatar-lg bg-info-light text-info rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                            <i class="fas fa-barcode fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted fw-normal mb-1">Serialized Items</h6>
                            <h4 class="mb-0 fw-bold text-dark">
                                {{ number_format($products->filter(fn($p) => $p->is_serialized)->count()) }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-12">
                <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                    <div class="card-body d-flex align-items-center">
                        <div
                            class="avatar avatar-lg bg-success-light text-success rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                            <i class="fe fe-check-circle fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted fw-normal mb-1">Active Catalog</h6>
                            <h4 class="mb-0 fw-bold text-dark">
                                {{ number_format($products->filter(fn($p) => in_array($p->status, ['1', 1]))->count()) }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-12">
                <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                    <div class="card-body d-flex align-items-center">
                        <div
                            class="avatar avatar-lg bg-danger-light text-danger rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                            <i class="fe fe-x-circle fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted fw-normal mb-1">Inactive Catalog</h6>
                            <h4 class="mb-0 fw-bold text-dark">
                                {{ number_format($products->filter(fn($p) => !in_array($p->status, ['1', 1]))->count()) }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Summary Stats Bar -->

        <!-- Products Table Card -->
        <div class="card border-0 shadow-sm rounded-3">
            <!-- Search & Filter Controls -->
            <div class="card-header bg-white py-3 border-bottom border-light">
                <form action="{{ route('products.index') }}" method="GET" id="productFilterForm">
                    <div class="row align-items-center g-3">
                        <div class="col-12 col-md-4 col-lg-4">
                            <div class="search-box-custom">
                                <input type="text" name="search" class="form-control border-light-subtle"
                                    placeholder="Search by name, model..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-3 col-lg-3">
                            <select name="brand_id" class="form-select border-light-subtle"
                                onchange="document.getElementById('productFilterForm').submit()">
                                <option value="">All Brands</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-3 col-lg-3">
                            <select name="category_id" class="form-select border-light-subtle"
                                onchange="document.getElementById('productFilterForm').submit()">
                                <option value="">All Categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-2 col-lg-2 text-md-end text-muted small">
                            Showing <span class="fw-bold text-dark">{{ $products->count() }}</span> items
                        </div>
                    </div>
                </form>
            </div>

            <!-- Table Body -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-custom align-middle mb-0" id="productTable">
                        <thead class="bg-light text-secondary fs-7 text-uppercase">
                            <tr>
                                <th class="ps-4">#</th>
                                <th>Product & Model</th>
                                <th>Barcode/SKU</th>
                                <th>Brand</th>
                                <!-- <th>Category</th> -->
                                <th>Tracking</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @forelse ($products as $product)
                                @php
                                    $isActive = in_array($product->status, ['1', 1]);
                                @endphp
                                <tr>
                                    <td class="ps-4 text-muted fw-semibold">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td>
                                        <div>
                                            <a href="javascript:void(0)" data-bs-toggle="modal"
                                                data-bs-target="#view-product-modal{{ $product->id }}"
                                                class="fw-bold text-dark hover-primary mb-0 text-decoration-none d-block"
                                                title="{{ $product->name }}">
                                                {{ Str::limit($product->name, 25) }}
                                            </a>
                                            <small class="text-muted fs-7">Model:
                                                {{ Str::limit($product->model, 25) ?? 'N/A' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($product->barcode)
                                            <span
                                                class="badge bg-light text-dark border px-2 py-1 font-monospace fs-7 d-inline-flex align-items-center gap-1">
                                                <i class="fas fa-barcode text-primary"></i> {{ $product->barcode }}
                                            </span>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-dark fs-7">
                                            {{ Str::limit($product->brand->name ?? 'N/A', 20) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($product->is_serialized)
                                            <span class="badge badge-soft-info px-3 py-1 rounded-pill fs-7">
                                                <i class="fas fa-barcode me-1"></i> Serial
                                            </span>
                                        @else
                                            <span class="badge bg-light text-secondary border px-3 py-1 rounded-pill fs-7">
                                                Non-Serial
                                            </span>
                                        @endif
                                    </td>
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
                                            <a href="javascript:void(0)" class="btn-action-icon shadow-none"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                                <li>
                                                    <a class="dropdown-item py-2 d-flex align-items-center gap-2"
                                                        href="javascript:void(0)" data-bs-toggle="modal"
                                                        data-bs-target="#view-product-modal{{ $product->id }}">
                                                        <i class="fe fe-eye text-info"></i>
                                                        <span>View Details</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item py-2 d-flex align-items-center gap-2"
                                                        href="javascript:void(0)" data-bs-toggle="modal"
                                                        data-bs-target="#edit-product-modal{{ $product->id }}">
                                                        <i class="fe fe-edit text-primary"></i>
                                                        <span>Edit Product</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider opacity-50">
                                                </li>
                                                <li>
                                                    <a class="dropdown-item py-2 d-flex align-items-center gap-2 text-danger"
                                                        href="javascript:void(0)"
                                                        onclick="if(confirm('Are you sure you want to delete this product?')) { document.getElementById('productDelete{{ $product->id }}').submit(); }">
                                                        <i class="fe fe-trash-2 text-danger"></i>
                                                        <span>Delete Product</span>
                                                    </a>
                                                    <form id="productDelete{{ $product->id }}"
                                                        action="{{ route('products.destroy', $product->id) }}" method="POST"
                                                        class="d-none">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>

                                <!-- View Product Modal -->
                                <div id="view-product-modal{{ $product->id }}" class="modal fade" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content border-0 shadow-lg rounded-3">
                                            <div class="modal-header bg-light py-3 border-bottom d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar avatar-md bg-primary-light text-primary rounded-circle d-flex align-items-center justify-content-center">
                                                        <i class="fe fe-box fs-5"></i>
                                                    </div>
                                                    <div>
                                                        <h5 class="modal-title fw-bold text-dark mb-0">{{ $product->name }}</h5>
                                                        <small class="text-muted">Model: {{ $product->model ?? 'N/A' }}</small>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    @if ($isActive)
                                                        <span class="badge badge-soft-success px-3 py-1 rounded-pill fs-7">
                                                            <i class="fe fe-check-circle me-1"></i> Active
                                                        </span>
                                                    @else
                                                        <span class="badge badge-soft-danger px-3 py-1 rounded-pill fs-7">
                                                            <i class="fe fe-x-circle me-1"></i> Inactive
                                                        </span>
                                                    @endif
                                                    <button type="button" class="btn-close ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                            </div>
                                            <div class="modal-body p-4 text-start">
                                                <!-- Key Information Cards -->
                                                <div class="row g-3 mb-4">
                                                    <div class="col-md-4 col-6">
                                                        <div class="p-3 bg-light rounded-3 border">
                                                            <span class="text-muted small d-block mb-1">Barcode / SKU</span>
                                                            <span class="fw-bold font-monospace text-dark fs-6">
                                                                <i class="fas fa-barcode text-primary me-1"></i> {{ $product->barcode ?? 'N/A' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-6">
                                                        <div class="p-3 bg-light rounded-3 border">
                                                            <span class="text-muted small d-block mb-1">Brand & Category</span>
                                                            <span class="fw-bold text-dark fs-6 d-block">{{ $product->brand->name ?? 'N/A' }}</span>
                                                            <small class="text-secondary">{{ $product->category->name ?? 'Uncategorized' }}</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-6">
                                                        <div class="p-3 bg-light rounded-3 border">
                                                            <span class="text-muted small d-block mb-1">Current Stock</span>
                                                            <h5 class="fw-bold text-dark mb-0">
                                                                {{ number_format($product->inventory->current_stock ?? 0) }} Units
                                                            </h5>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-6">
                                                        <div class="p-3 bg-light rounded-3 border">
                                                            <span class="text-muted small d-block mb-1">Tracking Type</span>
                                                            @if($product->is_serialized)
                                                                <span class="badge badge-soft-info px-3 py-1 rounded-pill">
                                                                    <i class="fas fa-barcode me-1"></i> Serialized Tracking
                                                                </span>
                                                            @else
                                                                <span class="badge bg-white text-secondary border px-3 py-1 rounded-pill">
                                                                    Non-Serialized
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-6">
                                                        <div class="p-3 bg-light rounded-3 border">
                                                            <span class="text-muted small d-block mb-1">Warranty Period</span>
                                                            <span class="fw-bold text-dark fs-6">
                                                                <i class="fe fe-shield text-info me-1"></i> {{ $product->warranty ?? 0 }} Days
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-6">
                                                        <div class="p-3 bg-light rounded-3 border">
                                                            <span class="text-muted small d-block mb-1">Latest Cost Price</span>
                                                            <span class="fw-bold text-success fs-6">
                                                                ৳{{ number_format($product->latestPurchase->unit_price ?? 0, 2) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Serial Numbers Section (if serialized) -->
                                                @if($product->is_serialized)
                                                    <div class="mb-4">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <h6 class="fw-bold text-dark mb-0">
                                                                <i class="fas fa-qrcode text-info me-1"></i> Available Serials in Stock
                                                            </h6>
                                                            <span class="badge badge-soft-info">{{ $product->availableSerials->count() }} Available</span>
                                                        </div>
                                                        <div class="p-3 bg-light rounded-3 border" style="max-height: 180px; overflow-y: auto;">
                                                            @forelse($product->availableSerials as $serial)
                                                                <span class="badge bg-white text-dark border px-2 py-1 font-monospace fs-7 me-1 mb-1 d-inline-flex align-items-center gap-1">
                                                                    <i class="fas fa-barcode text-primary"></i> {{ $serial->serial_number }}
                                                                </span>
                                                            @empty
                                                                <span class="text-muted small">No available serial numbers currently in stock.</span>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- Photos Gallery Section (if photos exist) -->
                                                @if(!empty($product->photos) && is_array($product->photos))
                                                    <div class="mb-3">
                                                        <h6 class="fw-bold text-dark mb-2"><i class="fe fe-image me-1 text-primary"></i> Product Photos</h6>
                                                        <div class="d-flex flex-wrap gap-2">
                                                            @foreach($product->photos as $photo)
                                                                <a href="{{ asset('storage/' . $photo) }}" target="_blank" class="d-inline-block border rounded-3 overflow-hidden" style="width: 80px; height: 80px;">
                                                                    <img src="{{ asset('storage/' . $photo) }}" alt="Product Image" class="w-100 h-100 object-fit-cover">
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="modal-footer bg-light py-3 border-top d-flex justify-content-between">
                                                <button type="button" class="btn btn-light px-4 rounded-3 text-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="button" class="btn btn-primary px-4 rounded-3 shadow-sm d-inline-flex align-items-center gap-2"
                                                    data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#edit-product-modal{{ $product->id }}">
                                                    <i class="fe fe-edit"></i>
                                                    <span>Edit Product</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /View Product Modal -->

                                <!-- Edit Product Modal -->
                                <div id="edit-product-modal{{ $product->id }}" class="modal fade" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content border-0 shadow-lg rounded-3">
                                            <div class="modal-header bg-light py-3 border-bottom">
                                                <h5 class="modal-title fw-bold text-dark">Edit Product Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4 text-start">
                                                <form method="POST" action="{{ route('products.update', $product->id) }}"
                                                    enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="row g-3 mb-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-semibold small text-secondary">Brand
                                                                Name <span class="text-danger">*</span></label>
                                                            <select class="form-select select2" name="brand_id" required>
                                                                <option value="">Select Brand</option>
                                                                @foreach ($brands as $brand)
                                                                    <option {{ $brand->id == $product->brand_id ? 'selected' : '' }}
                                                                        value="{{ $brand->id }}">{{ $brand->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label
                                                                class="form-label fw-semibold small text-secondary">Category</label>
                                                            <select class="form-select select2" name="category_id">
                                                                <option value="">Select Category</option>
                                                                @foreach ($categories as $category)
                                                                    <option {{ $category->id == $product->category_id ? 'selected' : '' }} value="{{ $category->id }}">{{ $category->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-semibold small text-secondary">Product
                                                                Name <span class="text-danger">*</span></label>
                                                            <input type="text" name="name" class="form-control"
                                                                value="{{ old('name', $product->name) }}" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-semibold small text-secondary">Product
                                                                Model Name <span class="text-danger">*</span></label>
                                                            <input type="text" name="model_name" class="form-control"
                                                                value="{{ old('model_name', $product->model) }}" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label
                                                                class="form-label fw-semibold small text-secondary d-flex justify-content-between">
                                                                <span>Vendor Barcode / SKU</span>
                                                                <a href="javascript:void(0)"
                                                                    onclick="generateRandomBarcode('edit_barcode_{{ $product->id }}')"
                                                                    class="text-primary text-decoration-none small"><i
                                                                        class="fas fa-magic me-1"></i>Auto-Generate</a>
                                                            </label>
                                                            <div class="input-group">
                                                                <span class="input-group-text bg-light border-end-0"><i
                                                                        class="fas fa-barcode text-secondary"></i></span>
                                                                <input type="text" name="barcode"
                                                                    id="edit_barcode_{{ $product->id }}"
                                                                    class="form-control border-start-0"
                                                                    placeholder="Scan or enter vendor barcode"
                                                                    value="{{ old('barcode', $product->barcode) }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-semibold small text-secondary">Warranty
                                                                (Days)</label>
                                                            <input type="text" name="warranty" class="form-control"
                                                                value="{{ old('warranty', $product->warranty) }}">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-semibold small text-secondary">Status
                                                                <span class="text-danger">*</span></label>
                                                            <select class="form-select" name="status" required>
                                                                <option value="1" {{ $product->status == 1 ? 'selected' : '' }}>
                                                                    Active</option>
                                                                <option value="0" {{ $product->status == 0 ? 'selected' : '' }}>
                                                                    Inactive</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-check form-switch pt-2">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="is_serialized" id="is_serialized_{{ $product->id }}"
                                                                    value="1" {{ $product->is_serialized ? 'checked' : '' }}>
                                                                <label class="form-check-label fw-semibold text-dark"
                                                                    for="is_serialized_{{ $product->id }}">Serialized Product
                                                                    (Track by Serial Number)</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                                                        <button type="button"
                                                            class="btn btn-light px-4 rounded-3 text-secondary"
                                                            data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit"
                                                            class="btn btn-primary px-4 rounded-3 shadow-sm">Update
                                                            Product</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /Edit Product Modal -->
                            @empty
                                <tr id="emptyStateRow">
                                    <td colspan="7" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center justify-content-center">
                                            <div
                                                class="avatar avatar-xl bg-primary-light text-primary rounded-circle mb-3 d-flex align-items-center justify-content-center">
                                                <i class="fe fe-box fs-1"></i>
                                            </div>
                                            <h5 class="fw-bold text-dark mb-1">No Products Found</h5>
                                            <p class="text-muted small mb-3">Get started by creating your first product item</p>
                                            <a href="javascript:void(0)" data-bs-toggle="modal"
                                                data-bs-target="#add-payment-modal"
                                                class="btn btn-primary btn-sm px-3 rounded-2">
                                                Add Product
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
@endsection

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            function generateRandomBarcode(inputId) {
                const rand = Math.floor(100000 + Math.random() * 900000);
                const code = 'ITP-' + rand;
                const input = document.getElementById(inputId);
                if (input) {
                    input.value = code;
                }
            }

            $(document).ready(function () {
                $('.modal').on('shown.bs.modal', function () {
                    $(this).find('.select2').select2({
                        dropdownParent: $(this),
                        width: '100%'
                    });
                });
            });
        </script>
    @endpush