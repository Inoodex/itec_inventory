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
    .badge-soft-primary {
        background-color: rgba(118, 56, 255, 0.12) !important;
        color: #7638ff !important;
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
                <h4 class="card-title fw-bold text-dark mb-1">Purchase List</h4>
                <p class="text-muted small mb-0">Manage stock purchases, vendor payments, unit costs, and serial numbers</p>
            </div>
            <div>
                <button type="button" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#add-purchase-modal">
                    <i class="fe fe-plus-circle fs-6"></i>
                    <span>Add Purchase</span>
                </button>
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
                        <i class="fe fe-shopping-cart fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Total Orders</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($purchases->total()) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-info-light text-info rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-dollar-sign fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Total Amount</h6>
                        <h4 class="mb-0 fw-bold text-dark">৳{{ number_format($purchases->sum('total_price'), 2) }}</h4>
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
                        <h6 class="text-muted fw-normal mb-1">Total Paid</h6>
                        <h4 class="mb-0 fw-bold text-dark">৳{{ number_format($purchases->sum('payment'), 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-danger-light text-danger rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-alert-circle fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Outstanding Due</h6>
                        <h4 class="mb-0 fw-bold text-dark">৳{{ number_format($purchases->sum('due'), 2) }}</h4>
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
            <form action="{{ route('purchase.index') }}" method="GET" id="purchaseFilterForm">
                <div class="row align-items-center g-3">
                    <div class="col-12 col-md-4 col-lg-4">
                        <div class="search-box-custom">
                            <input type="text" name="search" class="form-control border-light-subtle" placeholder="Search product name, vendor..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-12 col-md-3 col-lg-3">
                        <select name="product_id" class="form-select border-light-subtle select2" onchange="document.getElementById('purchaseFilterForm').submit()">
                            <option value="">All Products</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-3 col-lg-3">
                        <select name="vendor_id" class="form-select border-light-subtle select2" onchange="document.getElementById('purchaseFilterForm').submit()">
                            <option value="">All Vendors</option>
                            @foreach ($vendors as $vendor)
                                <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                    {{ $vendor->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-2 col-lg-2 text-md-end text-muted small">
                        Showing <span class="fw-bold text-dark">{{ $purchases->count() }}</span> entries
                    </div>
                </div>
            </form>
        </div>

        <!-- Table Body -->
        <div class="card-body p-0" style="overflow: visible;">
            <div class="table-responsive" style="overflow: visible !important;">
                <table class="table table-hover table-custom align-middle mb-0" id="purchaseTable">
                    <thead class="bg-light text-secondary fs-7 text-uppercase">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Date</th>
                            <th>Product & Model</th>
                            <th>Vendor</th>
                            <th>Qty</th>
                            <th>Unit Price</th>
                            <th>Total Price</th>
                            <th>Payment</th>
                            <th>Due</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($purchases as $purchase)
                            <tr>
                                <td class="ps-4 text-muted fw-semibold">{{ $loop->iteration }}</td>
                                <td>
                                    <span class="text-secondary small">
                                        {{ $purchase->created_at ? $purchase->created_at->format('d M Y') : 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <span class="fw-bold text-dark d-block" title="{{ $purchase->product->name ?? '' }}">
                                            {{ Str::limit($purchase->product->name ?? 'N/A', 25) }}
                                        </span>
                                        <small class="text-muted fs-7">Model: {{ $purchase->product->model ?? 'N/A' }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">
                                        {{ Str::limit($purchase->vendor->name ?? 'N/A', 20) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-soft-info px-3 py-1 rounded-pill fs-7">
                                        {{ $purchase->quantity }} Units
                                    </span>
                                </td>
                                <td>৳{{ number_format($purchase->unit_price, 2) }}</td>
                                <td class="fw-bold text-dark">৳{{ number_format($purchase->total_price, 2) }}</td>
                                <td class="text-success fw-semibold">৳{{ number_format($purchase->payment, 2) }}</td>
                                <td>
                                    @if($purchase->due > 0)
                                        <span class="badge badge-soft-danger px-3 py-1 rounded-pill fs-7">
                                            ৳{{ number_format($purchase->due, 2) }}
                                        </span>
                                    @else
                                        <span class="badge badge-soft-success px-3 py-1 rounded-pill fs-7">
                                            Paid
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
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#edit-purchase-{{ $purchase->id }}">
                                                    <i class="fe fe-edit text-primary"></i>
                                                    <span>Edit Purchase</span>
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider opacity-50"></li>
                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2 text-danger" href="javascript:void(0)"
                                                    onclick="if (confirm('Are you sure you want to delete this purchase record?')) { document.getElementById('deletePurchase{{ $purchase->id }}').submit(); }">
                                                    <i class="fe fe-trash-2 text-danger"></i>
                                                    <span>Delete Purchase</span>
                                                </a>
                                                <form id="deletePurchase{{ $purchase->id }}" action="{{ route('purchase.destroy', $purchase->id) }}" method="POST" class="d-none">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="emptyStateRow">
                                <td colspan="10" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="avatar avatar-xl bg-primary-light text-primary rounded-circle mb-3 d-flex align-items-center justify-content-center">
                                            <i class="fe fe-shopping-cart fs-1"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">No Purchase Records Found</h5>
                                        <p class="text-muted small mb-3">Add a new purchase to update product inventory and vendor bills</p>
                                        <button type="button" class="btn btn-primary btn-sm px-3 rounded-2" data-bs-toggle="modal" data-bs-target="#add-purchase-modal">
                                            Add Purchase
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($purchases->hasPages())
                <div class="p-3 border-top">
                    {{ $purchases->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Add Purchase Modal -->
<div class="modal fade" id="add-purchase-modal" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header bg-light py-3 border-bottom">
                <h5 class="modal-title fw-bold text-dark">Add New Purchase</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('purchase.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <!-- Quick Barcode Scanner for Product -->
                        <div class="col-12">
                            <div class="p-2 px-3 bg-light rounded-3 border d-flex align-items-center gap-2">
                                <i class="fas fa-barcode text-primary fs-5"></i>
                                <div class="flex-grow-1">
                                    <input type="text" id="purchase_barcode_scanner" class="form-control form-control-sm border-0 bg-transparent shadow-none" placeholder="Scan Vendor Product Barcode to Auto-Select..." autocomplete="off">
                                </div>
                                <span class="badge bg-white text-secondary border small px-2 py-1">Scanner Ready</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="product_id" class="form-label fw-semibold small text-secondary">Product <span class="text-danger">*</span></label>
                            <select class="form-select select2" name="product_id" id="product_id" required>
                                <option value="">Select Product</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" data-barcode="{{ $product->barcode }}" data-is-serialized="{{ $product->is_serialized }}" title="{{ $product->name }}">
                                        {{ Str::limit($product->name, 45) }} ({{ $product->model ?? 'N/A' }}) {{ $product->barcode ? '[' . $product->barcode . ']' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="vendor" class="form-label fw-semibold small text-secondary">Vendor <span class="text-danger">*</span></label>
                            <select id="vendor" name="vendor_id" class="form-select select2" required>
                                <option value="">Select Vendor</option>
                                @foreach ($vendors as $vendor)
                                    <option value="{{ $vendor->id }}">
                                        {{ $vendor->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12" id="serial-section" style="display: none;">
                            <div class="p-3 bg-light rounded-3 border border-info-subtle">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-semibold text-info mb-0">
                                        <i class="fas fa-barcode me-1"></i> Scan Received Unit Serials
                                    </label>
                                    <span class="badge badge-soft-info" id="scanned-serials-count">0 Scanned</span>
                                </div>
                                
                                <div class="input-group mb-2">
                                    <span class="input-group-text bg-white"><i class="fas fa-qrcode text-info"></i></span>
                                    <input type="text" id="scan_serial_input" class="form-control" placeholder="Scan each box serial barcode here..." autocomplete="off">
                                    <button type="button" class="btn btn-outline-info" onclick="addScannedSerial()"><i class="fas fa-plus me-1"></i>Add</button>
                                </div>

                                <div id="serial-tags-container" class="d-flex flex-wrap gap-1 mb-2">
                                    <!-- Scanned tags will appear here -->
                                </div>

                                <div id="serial-inputs-container" class="mb-2">
                                    <!-- Hidden / text inputs -->
                                </div>
                                <small class="text-muted d-block" id="serial-help-text">Scan barcodes one-by-one or type and press Enter. Quantity will auto-increment.</small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-secondary">Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" class="form-control" min="1" required placeholder="0">
                        </div>

                        <div class="col-md-4">
                            <label for="unit_price" class="form-label fw-semibold small text-secondary">Unit Cost Price <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="unit_price" id="unit_price" class="form-control" required placeholder="0.00">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-secondary">Sub Price</label>
                            <input type="number" step="0.01" name="sub_price" class="form-control bg-light" readonly placeholder="0.00">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-secondary">Payable Total Price <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="total_price" class="form-control" required placeholder="0.00">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-secondary">Payment Amount <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="payment" class="form-control" required placeholder="0.00">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-secondary">Outstanding Due</label>
                            <input type="number" step="0.01" name="due" class="form-control bg-light" readonly placeholder="0.00">
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2 p-3 border-top bg-light">
                    <button type="button" class="btn btn-light px-4 rounded-3 text-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 rounded-3 shadow-sm">Submit Purchase</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Purchase Modals -->
@foreach ($purchases as $purchase)
<div class="modal fade" id="edit-purchase-{{ $purchase->id }}" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header bg-light py-3 border-bottom">
                <h5 class="modal-title fw-bold text-dark">Edit Purchase</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('purchase.update', $purchase->id) }}">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit-product_id-{{ $purchase->id }}" class="form-label fw-semibold small text-secondary">Product <span class="text-danger">*</span></label>
                            <select id="edit-product_id-{{ $purchase->id }}" name="product_id" class="form-select select2" required>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" {{ $product->id == $purchase->product_id ? 'selected' : '' }} title="{{ $product->name }}">
                                        {{ Str::limit($product->name, 45) }} ({{ $product->model ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="edit-vendor-{{ $purchase->id }}" class="form-label fw-semibold small text-secondary">Vendor <span class="text-danger">*</span></label>
                            <select id="edit-vendor-{{ $purchase->id }}" name="vendor_id" class="form-select select2" required>
                                <option value="">Select Vendor</option>
                                @foreach ($vendors as $vendor)
                                    <option value="{{ $vendor->id }}" {{ $vendor->id == $purchase->vendor_id ? 'selected' : '' }}>
                                        {{ $vendor->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="edit-quantity-{{ $purchase->id }}" class="form-label fw-semibold small text-secondary">Quantity</label>
                            <input id="edit-quantity-{{ $purchase->id }}" name="quantity" value="{{ $purchase->quantity }}" class="form-control" placeholder="Quantity" />
                        </div>

                        <div class="col-md-4">
                            <label for="edit-unit_price-{{ $purchase->id }}" class="form-label fw-semibold small text-secondary">Unit Cost Price</label>
                            <input id="edit-unit_price-{{ $purchase->id }}" name="unit_price" value="{{ $purchase->unit_price }}" class="form-control" placeholder="Unit Price" />
                        </div>

                        <div class="col-md-4">
                            <label for="edit-sub_price-{{ $purchase->id }}" class="form-label fw-semibold small text-secondary">Sub Price</label>
                            <input id="edit-sub_price-{{ $purchase->id }}" name="sub_price" value="{{ $purchase->sub_price }}" class="form-control bg-light" readonly placeholder="Sub Price" />
                        </div>

                        <div class="col-md-4">
                            <label for="edit-total_price-{{ $purchase->id }}" class="form-label fw-semibold small text-secondary">Payable Total Price</label>
                            <input id="edit-total_price-{{ $purchase->id }}" name="total_price" value="{{ $purchase->total_price }}" class="form-control" placeholder="Total Price" />
                        </div>

                        <div class="col-md-4">
                            <label for="edit-payment-{{ $purchase->id }}" class="form-label fw-semibold small text-secondary">Payment</label>
                            <input id="edit-payment-{{ $purchase->id }}" name="payment" value="{{ $purchase->payment }}" class="form-control" placeholder="Payment" />
                        </div>

                        <div class="col-md-4">
                            <label for="edit-due-{{ $purchase->id }}" class="form-label fw-semibold small text-secondary">Outstanding Due</label>
                            <input id="edit-due-{{ $purchase->id }}" name="due" value="{{ $purchase->due }}" class="form-control bg-light" readonly placeholder="Due" />
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2 p-3 border-top bg-light">
                    <button type="button" class="btn btn-light px-4 rounded-3 text-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 rounded-3 shadow-sm">Update Purchase</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const quantityInput = document.querySelector('#add-purchase-modal input[name="quantity"]');
        const unitPriceInput = document.querySelector('#add-purchase-modal input[name="unit_price"]');
        const subPriceInput = document.querySelector('#add-purchase-modal input[name="sub_price"]');
        const totalPriceInput = document.querySelector('#add-purchase-modal input[name="total_price"]');
        const paymentInput = document.querySelector('#add-purchase-modal input[name="payment"]');
        const dueInput = document.querySelector('#add-purchase-modal input[name="due"]');

        function calculateSubPrice() {
            if (!quantityInput || !unitPriceInput) return;
            const quantity = parseFloat(quantityInput.value) || 0;
            const unitPrice = parseFloat(unitPriceInput.value) || 0;
            const sub = (quantity * unitPrice).toFixed(2);
            if (subPriceInput) subPriceInput.value = sub;
            if (totalPriceInput && !totalPriceInput.value) totalPriceInput.value = sub;
        }

        function calculateDue() {
            if (!totalPriceInput || !paymentInput) return;
            const total = parseFloat(totalPriceInput.value) || 0;
            const payment = parseFloat(paymentInput.value) || 0;
            if (dueInput) dueInput.value = (total - payment).toFixed(2);
        }

        if (quantityInput) quantityInput.addEventListener('input', calculateSubPrice);
        if (unitPriceInput) unitPriceInput.addEventListener('input', calculateSubPrice);
        if (totalPriceInput) totalPriceInput.addEventListener('input', calculateDue);
        if (paymentInput) paymentInput.addEventListener('input', calculateDue);

        // Barcode Product Auto-Select
        const barcodeScanner = document.getElementById('purchase_barcode_scanner');
        if (barcodeScanner) {
            barcodeScanner.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const code = this.value.trim();
                    if (!code) return;

                    fetch(`{{ route('products.barcode_lookup') }}?code=${encodeURIComponent(code)}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.success && data.product) {
                                $('#product_id').val(data.product.id).trigger('change');
                                barcodeScanner.value = '';
                                
                                if (data.product.is_serialized) {
                                    setTimeout(() => {
                                        const scanSerial = document.getElementById('scan_serial_input');
                                        if (scanSerial) scanSerial.focus();
                                    }, 200);
                                } else {
                                    if (quantityInput) quantityInput.focus();
                                }
                            } else {
                                alert(data.message || 'Product not found with this barcode.');
                            }
                        })
                        .catch(() => alert('Error scanning barcode.'));
                }
            });
        }

        // Serial Scanner Array & Tag Manager
        window.scannedSerials = [];

        window.addScannedSerial = function() {
            const input = document.getElementById('scan_serial_input');
            if (!input) return;
            const val = input.value.trim();
            if (!val) return;

            if (window.scannedSerials.includes(val)) {
                alert(`Serial [${val}] is already added in this purchase!`);
                input.value = '';
                return;
            }

            window.scannedSerials.push(val);
            input.value = '';
            renderScannedSerials();
            input.focus();
        };

        window.removeScannedSerial = function(index) {
            window.scannedSerials.splice(index, 1);
            renderScannedSerials();
        };

        function renderScannedSerials() {
            const container = document.getElementById('serial-tags-container');
            const inputsContainer = document.getElementById('serial-inputs-container');
            const badge = document.getElementById('scanned-serials-count');
            
            if (!container || !inputsContainer) return;
            
            container.innerHTML = '';
            inputsContainer.innerHTML = '';

            window.scannedSerials.forEach((sn, idx) => {
                // Tag UI
                const tag = document.createElement('span');
                tag.className = 'badge bg-white text-dark border px-2 py-1 fs-7 d-inline-flex align-items-center gap-1';
                tag.innerHTML = `<span><i class="fas fa-barcode text-info me-1"></i>${sn}</span> <a href="javascript:void(0)" onclick="removeScannedSerial(${idx})" class="text-danger ms-1 fw-bold">&times;</a>`;
                container.appendChild(tag);

                // Hidden Input for submission
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'serial_numbers[]';
                hiddenInput.value = sn;
                inputsContainer.appendChild(hiddenInput);
            });

            if (badge) badge.innerText = `${window.scannedSerials.length} Scanned`;
            
            // Auto update quantity input to match scanned serials count
            if (quantityInput && window.scannedSerials.length > 0) {
                quantityInput.value = window.scannedSerials.length;
                calculateSubPrice();
            }
        }

        const scanSerialInput = document.getElementById('scan_serial_input');
        if (scanSerialInput) {
            scanSerialInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    addScannedSerial();
                }
            });
        }

        // Serial Number UI Logic on product change
        const serialSection = document.getElementById('serial-section');
        const productSelect = document.getElementById('product_id');

        function updateSerialUI() {
            if (!productSelect || !serialSection) return;
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const isSerialized = selectedOption ? selectedOption.getAttribute('data-is-serialized') == '1' : false;

            if (isSerialized) {
                serialSection.style.display = 'block';
            } else {
                serialSection.style.display = 'none';
                window.scannedSerials = [];
                renderScannedSerials();
            }
        }

        if (productSelect) productSelect.addEventListener('change', updateSerialUI);

        // Fetch Latest Product Price via AJAX
        if (typeof $ !== 'undefined') {
            $('#product_id').on('change', function() {
                var productId = $(this).val();
                if (productId) {
                    var urlTemplate = "{{ route('purchase.latest_price', ':id') }}";
                    var url = urlTemplate.replace(':id', productId);
                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function(response) {
                            $('#unit_price').val(response.price || 0);
                            calculateSubPrice();
                        },
                        error: function() {
                            $('#unit_price').val(0);
                        }
                    });
                }
            });
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        @foreach ($purchases as $purchase)
            const qInput_{{ $purchase->id }} = document.getElementById('edit-quantity-{{ $purchase->id }}');
            const uInput_{{ $purchase->id }} = document.getElementById('edit-unit_price-{{ $purchase->id }}');
            const sInput_{{ $purchase->id }} = document.getElementById('edit-sub_price-{{ $purchase->id }}');
            const tInput_{{ $purchase->id }} = document.getElementById('edit-total_price-{{ $purchase->id }}');
            const pInput_{{ $purchase->id }} = document.getElementById('edit-payment-{{ $purchase->id }}');
            const dInput_{{ $purchase->id }} = document.getElementById('edit-due-{{ $purchase->id }}');

            function calcEditSub_{{ $purchase->id }}() {
                if (!qInput_{{ $purchase->id }} || !uInput_{{ $purchase->id }}) return;
                const q = parseFloat(qInput_{{ $purchase->id }}.value) || 0;
                const u = parseFloat(uInput_{{ $purchase->id }}.value) || 0;
                if (sInput_{{ $purchase->id }}) sInput_{{ $purchase->id }}.value = (q * u).toFixed(2);
            }

            function calcEditDue_{{ $purchase->id }}() {
                if (!tInput_{{ $purchase->id }} || !pInput_{{ $purchase->id }}) return;
                const t = parseFloat(tInput_{{ $purchase->id }}.value) || 0;
                const p = parseFloat(pInput_{{ $purchase->id }}.value) || 0;
                if (dInput_{{ $purchase->id }}) dInput_{{ $purchase->id }}.value = (t - p).toFixed(2);
            }

            if (qInput_{{ $purchase->id }}) qInput_{{ $purchase->id }}.addEventListener('input', calcEditSub_{{ $purchase->id }});
            if (uInput_{{ $purchase->id }}) uInput_{{ $purchase->id }}.addEventListener('input', calcEditSub_{{ $purchase->id }});
            if (tInput_{{ $purchase->id }}) tInput_{{ $purchase->id }}.addEventListener('input', calcEditDue_{{ $purchase->id }});
            if (pInput_{{ $purchase->id }}) pInput_{{ $purchase->id }}.addEventListener('input', calcEditDue_{{ $purchase->id }});
        @endforeach
    });
</script>
@endpush
@endsection
