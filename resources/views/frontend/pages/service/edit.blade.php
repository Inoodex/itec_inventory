@extends('frontend.layouts.app')

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1">Edit Service</h4>
                <p class="text-muted small mb-0">Update repair service details, customer information, and pricing</p>
            </div>
            <div>
                <a href="{{ route('service.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3 shadow-sm">
                 <i class="fa fa-arrow-left me-2"></i>    
                Back to Services
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">
            <form action="{{ route('service.update', $service->id) }}" method="POST">
                @method('PUT')
                @csrf

                <!-- Section: Customer Details -->
                <h6 class="fw-bold text-dark mb-3"><i class="fe fe-user me-2 text-primary"></i>Customer Details</h6>
                <div class="row g-3 mb-4">
                    <div class="col-lg-4 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Customer Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control border-light-subtle" placeholder="Enter Customer Name" value="{{ old('name', $service->name) }}" required autocomplete="off">
                    </div>

                    <div class="col-lg-4 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Phone Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control border-light-subtle" placeholder="Phone Number" name="phone" value="{{ old('phone', $service->phone) }}" required autocomplete="off">
                    </div>

                    <div class="col-lg-4 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Email Address</label>
                        <input type="email" name="email" class="form-control border-light-subtle" placeholder="Enter Email Address" value="{{ old('email', $service->email) }}" autocomplete="off">
                    </div>

                    <div class="col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Address</label>
                        <textarea class="form-control border-light-subtle" placeholder="Enter Customer Address" name="address" rows="2" autocomplete="off">{{ old('address', $service->address) }}</textarea>
                    </div>
                </div>

                <hr class="my-4 opacity-50">

                <!-- Section: Product & Repair Details -->
                <h6 class="fw-bold text-dark mb-3"><i class="fe fe-tool me-2 text-primary"></i>Product & Repair Info</h6>
                <div class="row g-3 mb-4">
                    <div class="col-lg-4 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Product Name <span class="text-danger">*</span></label>
                        <select name="product_id" id="product_select" class="form-select select2 border-light-subtle" required>
                            <option value="">Select Product</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}"
                                    data-category-id="{{ $product->category_id }}"
                                    {{ (string) old('product_id', $service->product_id) === (string) $product->id || (!$service->product_id && $service->product_name === $product->name) ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-4 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Category</label>
                        <select id="category_select" class="form-select select2 border-light-subtle" disabled>
                            <option value="">Select Category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ (string) optional($service->product)->category_id === (string) $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-4 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Serial / IMEI Number</label>
                        <input type="text" class="form-control border-light-subtle" placeholder="Product IMEI or Serial Number" name="product_number" value="{{ old('product_number', $service->product_number) }}" autocomplete="off">
                    </div>

                    <div class="col-lg-4 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Warranty Duration (Days)</label>
                        <input type="number" class="form-control border-light-subtle" placeholder="Warranty duration in days" name="warranty_duration" value="{{ old('warranty_duration', $service->warranty_duration) }}" autocomplete="off">
                    </div>

                    <div class="col-lg-4 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Repaired By</label>
                        <select class="form-select border-light-subtle" name="repaired_by">
                            <option value="">Select Technician</option>
                            @foreach ($serviceMans as $key => $user)
                                <option value="{{ $key }}" {{ old('repaired_by', $service->repaired_by) == $key ? 'selected' : '' }}>
                                    {{ $user }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Service Details</label>
                        <textarea class="form-control border-light-subtle" placeholder="Enter problem diagnosis or service details..." name="details" rows="2" autocomplete="off">{{ old('details', $service->details) }}</textarea>
                    </div>
                </div>

                <hr class="my-4 opacity-50">

                <!-- Section: Financial Breakdown -->
                <h6 class="fw-bold text-dark mb-3"><i class="fe fe-dollar-sign me-2 text-primary"></i>Financial Details</h6>
                <div class="row g-3 mb-4">
                    <div class="col-lg-3 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Price Total <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" oninput="calculateDue()" class="form-control border-light-subtle" placeholder="Price" id="total" name="total" value="{{ old('total', $service->total) }}" required autocomplete="off">
                    </div>

                    <div class="col-lg-3 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Discount Amount</label>
                        <input type="number" step="0.01" oninput="calculateDue()" class="form-control border-light-subtle" placeholder="Discount" id="discount" name="discount" value="{{ old('discount', $service->discount) }}" autocomplete="off">
                    </div>

                    <div class="col-lg-3 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Final Bill Amount</label>
                        <input type="number" step="0.01" class="form-control border-light-subtle bg-light" id="bill" name="bill" value="{{ old('bill', $service->bill) }}" required readonly>
                    </div>

                    <div class="col-lg-3 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Paid Amount</label>
                        <input type="number" step="0.01" class="form-control border-light-subtle bg-light" id="paid_amount" name="paid_amount" value="{{ old('paid_amount', $service->paid_amount) }}" readonly>
                    </div>

                    <div class="col-lg-3 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Outstanding Due</label>
                        <input type="number" step="0.01" class="form-control border-light-subtle bg-light fw-bold text-danger" id="due_amount" name="due_amount" value="{{ old('due_amount', $service->due_amount) }}" readonly>
                    </div>

                    <div class="col-lg-9 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Remarks / Notes</label>
                        <textarea class="form-control border-light-subtle" placeholder="Add any service remarks..." name="remarks" rows="1">{{ old('remarks', $service->remarks) }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                    <a href="{{ route('service.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3">Update Service</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('#product_select').on('change', function() {
            const selected = this.options[this.selectedIndex];
            const categoryId = selected ? selected.getAttribute('data-category-id') : '';
            $('#category_select').val(categoryId).trigger('change');
        }).trigger('change');
    });

    function calculateDue() {
        var total = parseFloat(document.getElementById("total").value) || 0;
        var discount = parseFloat(document.getElementById("discount").value) || 0;
        var bill = Math.max(total - discount, 0);
        document.getElementById("bill").value = bill.toFixed(2);
        
        var paid_amount = parseFloat(document.getElementById("paid_amount").value) || 0;
        document.getElementById("due_amount").value = Math.max(0, bill - paid_amount).toFixed(2);
    }
</script>
@endpush
