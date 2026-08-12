@extends('frontend.layouts.app')

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1">Add Service</h4>
                <p class="text-muted small mb-0">Create a new repair service job and log initial customer payment</p>
            </div>
            <div>
                <a href="{{ route('service.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3 shadow-sm">
                    <i class="fa fa-arrow-left me-2"></i>Back to Services
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <form action="{{ route('service.store') }}" method="POST" id="createServiceForm">
        @csrf

        <!-- Section 1: Customer Info -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold text-dark mb-3"><i class="fe fe-user me-2 text-primary"></i>Customer Information</h6>
                
                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <label class="form-label small text-secondary fw-semibold mb-2">Customer Type <span class="text-danger">*</span></label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="client_type" id="newClient" value="new" checked>
                                <label class="form-check-label fw-semibold text-dark" for="newClient">New Customer</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="client_type" id="existingClient" value="existing">
                                <label class="form-check-label fw-semibold text-dark" for="existingClient">Existing Customer</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- New Client Form -->
                <div id="newClientForm">
                    <div class="row g-3">
                        <div class="col-lg-4 col-md-6 col-12">
                            <label class="form-label small text-secondary fw-semibold mb-1">Customer Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control border-light-subtle" id="newClientName" placeholder="Enter Customer Name" autocomplete="off" required>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <label class="form-label small text-secondary fw-semibold mb-1">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control border-light-subtle" id="newClientPhone" placeholder="Enter Phone Number" autocomplete="off" required>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <label class="form-label small text-secondary fw-semibold mb-1">Email Address</label>
                            <input type="email" name="email" class="form-control border-light-subtle" id="newClientEmail" placeholder="Enter Email Address" autocomplete="off">
                        </div>
                        <div class="col-12">
                            <label class="form-label small text-secondary fw-semibold mb-1">Address</label>
                            <textarea name="address" class="form-control border-light-subtle" id="newClientAddress" placeholder="Enter Customer Address" rows="2"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Existing Client Form -->
                <div id="existingClientForm" style="display: none;">
                    <div class="row g-3">
                        <div class="col-lg-6 col-md-8 col-12">
                            <label class="form-label small text-secondary fw-semibold mb-1">Select Existing Customer <span class="text-danger">*</span></label>
                            <select name="existing_client_id" class="form-select select2 border-light-subtle" id="clientSelect">
                                <option value="">Select Customer</option>
                                @foreach (App\Models\Customer::orderBy('name')->get() as $client)
                                    <option value="{{ $client->id }}">
                                        {{ $client->name }} ({{ $client->phone }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Service & Product Details -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold text-dark mb-3"><i class="fe fe-tool me-2 text-primary"></i>Service & Product Details</h6>
                <div class="row g-3">
                    <div class="col-lg-4 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Product Name <span class="text-danger">*</span></label>
                        <select name="product_id" class="form-select select2 border-light-subtle" id="product_select" required>
                            <option value="">Select Product</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" data-category-id="{{ $product->category_id }}">
                                    {{ $product->name }} {{ $product->model ? '('.$product->model.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-4 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Model / Serial Number</label>
                        <input type="text" name="product_number" class="form-control border-light-subtle" placeholder="Enter Model or Serial Number" autocomplete="off">
                    </div>

                    <div class="col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Service Details / Problem Description</label>
                        <textarea name="details" class="form-control border-light-subtle" placeholder="Describe the problem, diagnosis or repairs needed..." rows="3"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Payment Info -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold text-dark mb-3"><i class="fe fe-dollar-sign me-2 text-primary"></i>Payment Breakdown</h6>
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Total Price <span class="text-danger">*</span></label>
                        <input type="number" id="total" name="total" class="form-control border-light-subtle" value="0" step="0.01" required>
                    </div>

                    <div class="col-lg-3 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Discount Amount</label>
                        <input type="number" id="discount" name="discount" class="form-control border-light-subtle" value="0" step="0.01">
                    </div>

                    <div class="col-lg-3 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Final Bill Amount</label>
                        <input type="number" id="bill" name="bill" class="form-control border-light-subtle bg-light" value="0" step="0.01" readonly>
                    </div>

                    <div class="col-lg-3 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Paid Amount</label>
                        <input type="number" id="paid_amount" name="paid_amount" class="form-control border-light-subtle" value="0" step="0.01">
                    </div>

                    <div class="col-lg-3 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Outstanding Due</label>
                        <input type="number" id="due_amount" name="due_amount" class="form-control border-light-subtle bg-light fw-bold text-danger" value="0" step="0.01" readonly>
                    </div>

                    <div class="col-lg-3 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Payment Method</label>
                        <select class="form-select border-light-subtle" name="payment_method_id">
                            <option value="">Select Payment Method</option>
                            @foreach (paymentMethods() as $key => $name)
                                <option value="{{ $key }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Remarks / Notes</label>
                        <textarea name="remarks" class="form-control border-light-subtle" placeholder="Add any remarks or notes about this transaction..." rows="1"></textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 pt-4 mt-3 border-top">
                    <a href="{{ route('service.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3">Save Service</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Customer Type Toggle Logic
    const newClientRadio = $('#newClient');
    const existingClientRadio = $('#existingClient');
    const newClientForm = $('#newClientForm');
    const existingClientForm = $('#existingClientForm');

    function toggleClientForms() {
        if (newClientRadio.is(':checked')) {
            newClientForm.show();
            existingClientForm.hide();
            $('#newClientName, #newClientPhone').attr('required', true);
            $('#clientSelect').attr('required', false);
        } else {
            newClientForm.hide();
            existingClientForm.show();
            $('#newClientName, #newClientPhone').attr('required', false);
            $('#clientSelect').attr('required', true);
        }
    }

    newClientRadio.on('change', toggleClientForms);
    existingClientRadio.on('change', toggleClientForms);

    // Calculations
    function calculateTotals() {
        let total = parseFloat($('#total').val()) || 0;
        let discount = parseFloat($('#discount').val()) || 0;
        let bill = Math.max(0, total - discount);
        $('#bill').val(bill.toFixed(2));

        let paid = parseFloat($('#paid_amount').val()) || 0;
        let due = Math.max(0, bill - paid);
        $('#due_amount').val(due.toFixed(2));
    }

    $('#total, #discount, #paid_amount').on('input change', calculateTotals);
});
</script>
@endpush
