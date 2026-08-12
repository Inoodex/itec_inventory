@extends('frontend.layouts.app')

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1">Edit Sale Order</h4>
                <p class="text-muted small mb-0">Update customer information, cart items, and payment breakdown</p>
            </div>
            <div>
                <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3 shadow-sm">
                    <i class="fa fa-arrow-left me-2"></i>Back to Sales
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <form action="{{ route('sales.update', $sales->id) }}" method="POST" id="editSaleForm">
        @csrf
        @method('PUT')

        <!-- Section 1: Customer Information -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold text-dark mb-3"><i class="fe fe-user me-2 text-primary"></i>Customer Information</h6>
                <div class="row g-3">
                    <div class="col-lg-4 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Customer Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control border-light-subtle" value="{{ old('name', $customer->name) }}" required autocomplete="off">
                    </div>
                    <div class="col-lg-4 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Phone Number <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control border-light-subtle" value="{{ old('phone', $customer->phone) }}" required autocomplete="off">
                    </div>
                    <div class="col-lg-4 col-md-12 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Address <span class="text-danger">*</span></label>
                        <input type="text" name="address" class="form-control border-light-subtle" value="{{ old('address', $customer->address) }}" required autocomplete="off">
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Cart Items -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold text-dark mb-3"><i class="fe fe-shopping-cart me-2 text-primary"></i>Cart Items</h6>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="salesItemsTable">
                        <thead class="bg-light text-secondary fs-7 text-uppercase">
                            <tr>
                                <th style="width: 40%;">Product Name</th>
                                <th style="width: 20%;">Unit Price</th>
                                <th style="width: 15%;">Quantity</th>
                                <th style="width: 20%;">Total Price</th>
                                <th style="width: 5%;" class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody id="item_container">
                            @foreach ($items as $index => $item)
                                <tr class="group-item item{{ $item->product_id }}" data-itemnumber="{{ $index + 1 }}" id="form-group-item{{ $index + 1 }}">
                                    <td>
                                        <input type="hidden" name="product[]" value="{{ $item->product_id }}">
                                        <select class="form-select d-none" id="product{{ $index + 1 }}" disabled>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}"
                                                    data-price="{{ $product->latestPurchase->unit_price ?? 0 }}"
                                                    {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                                    {{ $product->name }} {{ $product->model ? '('.$product->model.')' : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="fw-bold text-dark d-block">
                                            @foreach ($products as $product)
                                                @if ($product->id == $item->product_id)
                                                    {{ $product->name }} {{ $product->model ? '('.$product->model.')' : '' }}
                                                @endif
                                            @endforeach
                                        </span>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="unit_price[]" class="form-control border-light-subtle unit-price" id="unit_price{{ $index + 1 }}" value="{{ $item->unit_price }}" onchange="calculateTotal()">
                                    </td>
                                    <td>
                                        <input type="number" name="qty[]" class="form-control border-light-subtle qty qty{{ $item->product_id }}" id="qty{{ $index + 1 }}" value="{{ $item->qty }}" min="1" onchange="calculateTotal()">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="total[]" class="form-control border-light-subtle bg-light total" id="total{{ $index + 1 }}" value="{{ $item->total_price }}" readonly>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-outline-danger btn-sm px-3 rounded-2" onclick="removeItem({{ $index + 1 }})" title="Remove Item">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Section 3: Summary Breakdown -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold text-dark mb-3"><i class="fe fe-dollar-sign me-2 text-primary"></i>Payment Breakdown</h6>

                <div id="summerySection" class="row g-3 align-items-end">
                    <div class="col-lg-2 col-md-4 col-6">
                        <label class="form-label small text-secondary fw-semibold mb-1">Sub Total</label>
                        <input type="number" step="0.01" id="subTotal" class="form-control border-light-subtle bg-light" name="subTotal" value="{{ $sales->bill }}" readonly>
                    </div>

                    <div class="col-lg-2 col-md-4 col-6">
                        <label class="form-label small text-secondary fw-semibold mb-1">Discount Amount</label>
                        <input type="number" step="any" id="discount" class="form-control border-light-subtle" name="discount" value="{{ $sales->discount }}" onchange="calculateTotal()">
                    </div>

                    <div class="col-lg-3 col-md-4 col-6">
                        <label class="form-label small text-secondary fw-semibold mb-1">Grand Total</label>
                        <input type="number" step="0.01" id="grandTotal" class="form-control border-light-subtle bg-light fw-bold text-primary" name="grandTotal" value="{{ $sales->payble }}" readonly>
                    </div>

                    <div class="col-lg-2 col-md-4 col-6">
                        <label class="form-label small text-secondary fw-semibold mb-1">Advance Payment</label>
                        <input type="number" step="0.01" id="advancedPayment" class="form-control border-light-subtle" name="advanced_payment" value="{{ $sales->advanced_payment }}" min="0">
                    </div>

                    <div class="col-lg-3 col-md-4 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Outstanding Due</label>
                        <input type="number" step="0.01" id="duePayment" class="form-control border-light-subtle bg-light fw-bold text-danger" name="due_payment" value="{{ $sales->due_payment }}" readonly>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 pt-4 mt-3 border-top">
                    <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3">Update Sale</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    var itemNumber = {{ count($items) + 1 }};

    function removeItem(num) {
        $('#form-group-item' + num).remove();
        calculateTotal();
    }

    function formatNumber(num) {
        if (num % 1 === 0) {
            return num;
        } else {
            return num.toFixed(2);
        }
    }

    function calculateTotal() {
        var eles = document.getElementsByClassName('group-item');
        var subTotal = 0;

        for (var i = 0; i < eles.length; i++) {
            var itemNum = eles[i].dataset.itemnumber;
            var unit_price = parseFloat(document.getElementById('unit_price' + itemNum).value) || 0;
            var qty = parseFloat(document.getElementById('qty' + itemNum).value) || 0;
            var totalEle = document.getElementById('total' + itemNum);

            var total = qty * unit_price;
            totalEle.value = formatNumber(total);

            subTotal += total;
        }

        var discount = parseFloat(document.getElementById('discount').value) || 0;
        if (discount > subTotal) discount = subTotal;
        document.getElementById('discount').value = formatNumber(discount);

        var grandTotal = subTotal - discount;
        document.getElementById('subTotal').value = formatNumber(subTotal);
        document.getElementById('grandTotal').value = formatNumber(grandTotal);

        var advanced = parseFloat(document.getElementById('advancedPayment').value) || 0;
        var due = grandTotal - advanced;
        document.getElementById('duePayment').value = formatNumber(due);
    }

    document.addEventListener('DOMContentLoaded', function() {
        $('#advancedPayment, #discount, .unit-price, .qty').on('input change', calculateTotal);
    });
</script>
@endpush
