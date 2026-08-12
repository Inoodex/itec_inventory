@extends('frontend.layouts.app')

@push('styles')
<style>
    .form-section-title {
        font-size: 15px;
        font-weight: 700;
        color: #2c3038;
        border-bottom: 1px solid #f0f0f5;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }
    .form-control:focus, .form-select:focus {
        border-color: #7638ff;
        box-shadow: 0 0 0 0.2rem rgba(118, 56, 255, 0.15);
    }
</style>
@endpush

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1">Register Warranty Claim</h4>
                <p class="text-muted small mb-0">Search sold items by invoice or phone, then register an RMA inspection claim</p>
            </div>
            <div>
                <a href="{{ route('warranties.index') }}" class="btn btn-outline-secondary px-3 py-2 rounded-3 d-inline-flex align-items-center gap-2">
                    <i class="fe fe-arrow-left"></i>
                    <span>Back</span>
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Lookup Section Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-4">
            <div class="form-section-title d-flex justify-content-between align-items-center">
                <span>Step 1: Scan Serial Barcode or Search by Invoice # / Customer Phone</span>
                <span class="badge bg-light text-secondary border small px-2 py-1"><i class="fas fa-barcode text-primary me-1"></i> Scanner Ready</span>
            </div>

            <form action="{{ route('warranties.create') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-md-9">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-barcode text-primary"></i></span>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="form-control form-control-lg border-start-0 font-monospace" required autofocus
                            placeholder="Scan Unit Serial Barcode, Product Barcode, Invoice # (e.g. INV-1002), or Phone...">
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-lg w-100 rounded-3 shadow-sm">
                        <i class="fas fa-search me-1"></i> Verify Warranty
                    </button>
                </div>
            </form>

            @if(request()->filled('search') && $searchResults->isNotEmpty())
                <div class="mt-4">
                    <h6 class="fw-bold mb-3 text-dark">Select Item for Warranty Claim:</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-secondary fs-7 text-uppercase">
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Customer</th>
                                    <th>Product Name</th>
                                    <th>Sale Date</th>
                                    <th>Warranty Expiry</th>
                                    <th>Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                @foreach($searchResults as $item)
                                    <tr class="{{ $item->is_expired ? 'opacity-50' : '' }}">
                                        <td><strong class="text-primary">{{ $item->sale?->order_no ?? '#' . $item->order_id }}</strong></td>
                                        <td>{{ $item->sale?->customer?->name ?? 'Guest' }} ({{ $item->sale?->customer?->phone }})</td>
                                        <td>{{ $item->product?->name }}</td>
                                        <td>{{ $item->warranty_start_date }}</td>
                                        <td>{{ $item->warranty_expiry_date }}</td>
                                        <td>
                                            @if($item->is_expired)
                                                <span class="badge badge-soft-danger px-3 py-1 rounded-pill">
                                                    <i class="fe fe-x-circle me-1"></i> Expired {{ abs($item->warranty_days_remaining) }} days ago
                                                </span>
                                            @else
                                                <span class="badge badge-soft-success px-3 py-1 rounded-pill">
                                                    <i class="fe fe-check-circle me-1"></i> {{ $item->warranty_days_remaining }} Days Remaining
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if($item->is_expired)
                                                <span class="d-inline-block" data-bs-toggle="tooltip" title="Cannot claim — warranty expired on {{ $item->warranty_expiry_date }}">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-2" disabled style="pointer-events: none;">
                                                        <i class="fe fe-slash me-1"></i> Cannot Claim
                                                    </button>
                                                </span>
                                            @else
                                                <button type="button" class="btn btn-sm btn-outline-primary rounded-2 select-item-btn"
                                                    data-item-id="{{ $item->id }}"
                                                    data-product-name="{{ $item->product?->name }}"
                                                    data-invoice-no="{{ $item->sale?->order_no }}"
                                                    data-customer-name="{{ $item->sale?->customer?->name }}"
                                                    data-expiry-date="{{ $item->warranty_expiry_date }}">
                                                    Select Item
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @elseif(request()->filled('search'))
                <div class="alert alert-warning mt-3 mb-0 rounded-3 border-0">
                    <i class="fe fe-alert-triangle me-2"></i> No eligible sale items found matching "{{ request('search') }}".
                </div>
            @endif
        </div>
    </div>

    <!-- Registration Form Card -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">
            <form action="{{ route('warranties.store') }}" method="POST">
                @csrf
                <input type="hidden" name="sales_item_id" id="sales_item_id" value="{{ old('sales_item_id') }}" required>

                <div class="form-section-title">
                    Step 2: Register Claim Details
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6 col-lg-6">
                        <label class="form-label fw-semibold text-secondary small mb-1">Selected Product / Item <span class="text-danger">*</span></label>
                        <input type="text" id="selected_product_name" class="form-control bg-light" readonly
                            placeholder="Select an item above..." value="{{ old('selected_product_name') }}" required>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <label class="form-label fw-semibold text-secondary small mb-1">Serial / IMEI Number</label>
                        <input type="text" name="serial_number" value="{{ old('serial_number') }}"
                            class="form-control" placeholder="e.g. SN-8839201">
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <label class="form-label fw-semibold text-secondary small mb-1">Claim Date <span class="text-danger">*</span></label>
                        <input type="date" name="claim_date" value="{{ old('claim_date', date('Y-m-d')) }}"
                            class="form-control" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold text-secondary small mb-1">Problem / Issue Description <span class="text-danger">*</span></label>
                        <textarea name="problem_description" rows="3" class="form-control" required
                            placeholder="Describe the issue reported by the customer (e.g. Display flicker, Battery drain, No power)...">{{ old('problem_description') }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary small mb-1">Item Physical Condition / Notes</label>
                        <textarea name="condition_notes" rows="2" class="form-control"
                            placeholder="Condition notes upon intake (e.g. Minor scratches, Body crack, Original box included)...">{{ old('condition_notes') }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary small mb-1">Internal Remarks</label>
                        <textarea name="remarks" rows="2" class="form-control"
                            placeholder="Additional internal staff notes...">{{ old('remarks') }}</textarea>
                    </div>
                </div>

                <!-- Form Actions Footer -->
                <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top border-light">
                    <a href="{{ route('warranties.index') }}" class="btn btn-light px-4 py-2 rounded-3 text-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm">Register Claim</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.select-item-btn').forEach(button => {
        button.addEventListener('click', function() {
            const itemId = this.dataset.itemId;
            const productName = this.dataset.productName;
            const invoiceNo = this.dataset.invoiceNo;
            const customerName = this.dataset.customerName;

            document.getElementById('sales_item_id').value = itemId;
            document.getElementById('selected_product_name').value = `${productName} (Invoice: ${invoiceNo} - ${customerName})`;
            
            document.querySelectorAll('.select-item-btn').forEach(btn => btn.classList.replace('btn-primary', 'btn-outline-primary'));
            this.classList.replace('btn-outline-primary', 'btn-primary');
        });
    });

    // Initialize Bootstrap tooltips for expired warranty rows
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipTriggerList.forEach(el => new bootstrap.Tooltip(el));
});
</script>
@endsection
