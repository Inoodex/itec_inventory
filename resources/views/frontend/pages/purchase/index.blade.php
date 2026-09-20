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
    .stat-card .card-body > div:last-child {
        min-width: 0;
        flex: 1 1 auto;
    }
    .stat-card h4 {
        font-size: clamp(16px, 2.2vw, 24px);
        line-height: 1.25;
        word-break: break-word;
        overflow-wrap: anywhere;
        white-space: normal !important;
    }
    .stat-card .text-muted {
        white-space: nowrap;
    }
    .stat-card .avatar {
        flex-shrink: 0;
    }
</style>
@endpush

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1">Purchase Invoices</h4>
                <p class="text-muted small mb-0">Manage stock purchase orders, vendor invoices, unit costs, and serial numbers</p>
            </div>
            <div>
                <a href="{{ route('purchase.create') }}" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2">
                    <i class="fe fe-plus-circle fs-6"></i>
                    <span>Create Purchase</span>
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
                        <i class="fe fe-shopping-cart fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Total Purchases</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($totalOrdersCount ?? $paginatedInvoices->total()) }}</h4>
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
                        <h6 class="text-muted fw-normal mb-1">Total Spent</h6>
                        <h4 class="mb-0 fw-bold text-dark">৳{{ number_format($totalAmountSum ?? 0, 2) }}</h4>
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
                        <h4 class="mb-0 fw-bold text-dark">৳{{ number_format($totalPaidSum ?? 0, 2) }}</h4>
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
                        <h4 class="mb-0 fw-bold text-dark">৳{{ number_format($totalDueSum ?? 0, 2) }}</h4>
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
                            <input type="text" name="search" class="form-control border-light-subtle" placeholder="Search invoice no, product, vendor..." value="{{ request('search') }}">
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
                        Showing <span class="fw-bold text-dark">{{ $paginatedInvoices->count() }}</span> of {{ $paginatedInvoices->total() }} orders
                    </div>
                </div>
            </form>
        </div>

        <!-- Table Body -->
        <div class="card-body p-0" style="overflow: visible;">
            <div class="table-responsive">
                <table class="table table-hover table-custom align-middle mb-0" id="purchaseTable">
                    <thead class="bg-light text-secondary fs-7 text-uppercase">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Invoice No</th>
                            <th>Vendor</th>
                            <th>Purchased Items</th>
                            <th>Total Qty</th>
                            <th>Total Amount</th>
                            <th>Payment</th>
                            <th>Due</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($paginatedInvoices as $inv)
                            @php
                                $invNo = $inv->purchase_no;
                                $items = $itemsByInvoice->get($invNo) ?? collect();
                                $firstItem = $items->first();
                                $vendor = $firstItem ? $firstItem->vendor : null;
                                $date = $firstItem && $firstItem->created_at ? $firstItem->created_at->format('d M Y') : 'N/A';
                                $totalQty = $items->sum('quantity');
                                $totalPrice = $items->sum('total_price');
                                $totalPayment = $items->sum('payment');
                                $totalDue = $items->sum('due');
                                $modalId = 'view-purchase-' . md5($invNo);
                            @endphp
                            @if($firstItem)
                            <tr>
                                <td class="ps-4 text-muted fw-semibold">{{ $loop->iteration }}</td>
                                <td>
                                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#{{ $modalId }}"
                                       class="badge badge-soft-primary px-3 py-1 rounded-pill fs-7 text-decoration-none d-inline-flex align-items-center gap-1"
                                       style="cursor: pointer;"
                                       title="Click to view order details">
                                        <i class="fe fe-file-text"></i>
                                        <span>#{{ $invNo }}</span>
                                    </a>
                                    <p class="mb-0 fs-7 text-muted" style="padding-left: 20px;">
                                        {{ $date }}
                                    </p>
                                </td>
                                <td>
                                    <div>
                                        <span class="fw-semibold text-dark d-block">
                                            {{ Str::limit($vendor->name ?? 'N/A', 22) }}
                                        </span>
                                        @if(!empty($vendor->company_name))
                                            <small class="text-muted fs-7">{{ Str::limit($vendor->company_name, 22) }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($items->count() === 1)
                                        <div>
                                            <span class="fw-bold text-dark d-block" title="{{ $firstItem->product->name ?? '' }}">
                                                {{ Str::limit($firstItem->product->name ?? 'N/A', 28) }}
                                            </span>
                                            @if(!empty($firstItem->product->model))
                                                <small class="text-muted fs-7">Model: {{ $firstItem->product->model }}</small>
                                            @endif
                                        </div>
                                    @else
                                        <div>
                                            <span class="badge badge-soft-info px-2.5 py-1 rounded-pill fs-7 mb-1">
                                                <i class="fe fe-package me-1"></i>{{ $items->count() }} Products
                                            </span>
                                            <div class="text-secondary small" style="line-height: 1.35; max-width: 250px;">
                                                @foreach ($items->take(2) as $item)
                                                    <span class="d-block text-truncate">• {{ $item->product->name ?? 'Product' }} ({{ $item->quantity }} pcs)</span>
                                                @endforeach
                                                @if($items->count() > 2)
                                                    <span class="text-muted fs-7">+{{ $items->count() - 2 }} more products...</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-secondary border px-3 py-1 rounded-pill fs-7 fw-semibold">
                                        {{ $totalQty }} Units
                                    </span>
                                </td>
                                <td class="fw-bold text-dark">৳{{ number_format($totalPrice, 2) }}</td>
                                <td class="text-success fw-semibold">৳{{ number_format($totalPayment, 2) }}</td>
                                <td>
                                    @if($totalDue > 0)
                                        <span class="badge badge-soft-danger px-3 py-1 rounded-pill fs-7">
                                            ৳{{ number_format($totalDue, 2) }}
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
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" target="_blank"
                                                    href="{{ route('purchase.invoice.pdf', $firstItem->id) }}">
                                                    <i class="fe fe-download text-info"></i>
                                                    <span>Download PDF</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#{{ $modalId }}">
                                                    <i class="fe fe-eye text-primary"></i>
                                                    <span>View Details</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('purchase.edit', $firstItem->id) }}">
                                                    <i class="fe fe-edit text-warning"></i>
                                                    <span>Edit Purchase</span>
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider opacity-50"></li>
                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2 text-danger" href="javascript:void(0)"
                                                    onclick="if (confirm('Are you sure you want to delete this purchase order? All associated items will be removed.')) { document.getElementById('deletePurchase{{ $firstItem->id }}').submit(); }">
                                                    <i class="fe fe-trash-2 text-danger"></i>
                                                    <span>Delete Order</span>
                                                </a>
                                                <form id="deletePurchase{{ $firstItem->id }}" action="{{ route('purchase.destroy', $firstItem->id) }}" method="POST" class="d-none">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endif
                        @empty
                            <tr id="emptyStateRow">
                                <td colspan="10" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="avatar avatar-xl bg-primary-light text-primary rounded-circle mb-3 d-flex align-items-center justify-content-center">
                                            <i class="fe fe-shopping-cart fs-1"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">No Purchase Records Found</h5>
                                        <p class="text-muted small mb-3">Create a new purchase to update product inventory and vendor bills</p>
                                        <a href="{{ route('purchase.create') }}" class="btn btn-primary btn-sm px-3 rounded-2">
                                            Create Purchase
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($paginatedInvoices->hasPages())
                <div class="p-3 border-top">
                    {{ $paginatedInvoices->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Purchase Order Details Modals -->
@foreach ($paginatedInvoices as $inv)
    @php
        $invNo = $inv->purchase_no;
        $items = $itemsByInvoice->get($invNo) ?? collect();
        $firstItem = $items->first();
        $vendor = $firstItem ? $firstItem->vendor : null;
        $creator = $firstItem ? $firstItem->creator : null;
        $modalId = 'view-purchase-' . md5($invNo);
        $totalSub = $items->sum(fn($i) => $i->sub_price ?? ($i->unit_price * $i->quantity));
        $totalNet = $items->sum('total_price');
        $totalPaid = $items->sum('payment');
        $totalDue = $items->sum('due');
        $totalDiscount = max(0, $totalSub - $totalNet);
    @endphp
    @if($firstItem)
    <div class="modal fade" id="{{ $modalId }}" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header bg-light py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0">Purchase Order Details</h5>
                        <span class="badge badge-soft-primary px-3 py-1 rounded-pill fs-7 mt-1">#{{ $invNo }}</span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Order Meta Info -->
                    <div class="row g-3 p-3 bg-light rounded-3 mb-4">
                        <div class="col-md-4">
                            <span class="text-muted small d-block">Vendor / Supplier:</span>
                            <strong class="text-dark">{{ $vendor->name ?? 'N/A' }}</strong>
                            @if(!empty($vendor->phone))
                                <span class="d-block small text-muted"><i class="fe fe-phone me-1"></i>{{ $vendor->phone }}</span>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted small d-block">Purchase Date:</span>
                            <strong class="text-dark">{{ $firstItem->created_at ? $firstItem->created_at->format('d M Y, h:i A') : 'N/A' }}</strong>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted small d-block">Purchased By:</span>
                            <strong class="text-dark">{{ $creator->name ?? 'System' }}</strong>
                        </div>
                    </div>

                    <!-- Line Items Table -->
                    <h6 class="fw-bold text-dark mb-2">Purchased Products ({{ $items->count() }})</h6>
                    <div class="table-responsive mb-3 border rounded-3 overflow-hidden">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="bg-light fs-7 text-uppercase text-secondary">
                                <tr>
                                    <th class="ps-3 py-2">#</th>
                                    <th class="py-2">Product</th>
                                    <th class="text-center py-2">Qty</th>
                                    <th class="text-end py-2">Unit Price</th>
                                    <th class="text-end pe-3 py-2">Total Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items as $idx => $item)
                                    <tr>
                                        <td class="ps-3 text-muted">{{ $idx + 1 }}</td>
                                        <td>
                                            <strong class="text-dark">{{ $item->product->name ?? 'N/A' }}</strong>
                                            @if(!empty($item->product->model))
                                                <small class="text-muted d-block">Model: {{ $item->product->model }}</small>
                                            @endif
                                            @if($item->serials && $item->serials->count() > 0)
                                                <div class="mt-1 p-1 px-2 bg-light rounded fs-8 text-secondary font-monospace">
                                                    <strong>Serials:</strong> {{ $item->serials->pluck('serial_number')->implode(', ') }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-center fw-semibold">{{ $item->quantity }} Pcs</td>
                                        <td class="text-end">৳{{ number_format($item->unit_price, 2) }}</td>
                                        <td class="text-end pe-3 fw-bold text-dark">৳{{ number_format($item->total_price, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Financial Summary Box -->
                    <div class="row justify-content-end">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <div class="d-flex justify-content-between py-1 small">
                                    <span class="text-muted">Sub Total:</span>
                                    <span class="fw-semibold text-dark">৳{{ number_format($totalSub, 2) }}</span>
                                </div>
                                @if($totalDiscount > 0)
                                <div class="d-flex justify-content-between py-1 small text-danger">
                                    <span>Discount:</span>
                                    <span class="fw-semibold">-৳{{ number_format($totalDiscount, 2) }}</span>
                                </div>
                                @endif
                                <div class="d-flex justify-content-between py-1 border-top border-bottom my-1">
                                    <strong class="text-primary">Payable Amount:</strong>
                                    <strong class="text-primary">৳{{ number_format($totalNet, 2) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between py-1 small text-success">
                                    <span>Paid Amount:</span>
                                    <span class="fw-bold">৳{{ number_format($totalPaid, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between py-1 small">
                                    <span class="fw-bold {{ $totalDue > 0 ? 'text-danger' : 'text-success' }}">Due Balance:</span>
                                    <span class="fw-bold {{ $totalDue > 0 ? 'text-danger' : 'text-success' }}">৳{{ number_format($totalDue, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center p-3 border-top bg-light">
                    <div class="d-flex gap-2">
                        <a href="{{ route('purchase.invoice.pdf', $firstItem->id) }}" target="_blank" class="btn btn-info px-3 rounded-3 text-white d-inline-flex align-items-center gap-2">
                            <i class="fe fe-download"></i>
                            <span>Download PDF Invoice</span>
                        </a>
                        <a href="{{ route('purchase.edit', $firstItem->id) }}" class="btn btn-warning px-3 rounded-3 text-white d-inline-flex align-items-center gap-2">
                            <i class="fe fe-edit"></i>
                            <span>Edit Order</span>
                        </a>
                    </div>
                    <button type="button" class="btn btn-secondary px-4 rounded-3" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endif
@endforeach

@endsection
