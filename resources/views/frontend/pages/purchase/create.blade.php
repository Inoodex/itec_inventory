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
    .badge-soft-success { background-color: rgba(25, 135, 84, 0.12) !important; color: #198754 !important; font-weight: 600; }
    .badge-soft-warning { background-color: rgba(255, 193, 7, 0.15) !important; color: #b58105 !important; font-weight: 600; }
    .badge-soft-danger { background-color: rgba(220, 53, 69, 0.12) !important; color: #dc3545 !important; font-weight: 600; }
    .badge-soft-info { background-color: rgba(13, 202, 240, 0.12) !important; color: #0dcaf0 !important; font-weight: 600; }
    .badge-soft-primary { background-color: rgba(118, 56, 255, 0.12) !important; color: #7638ff !important; font-weight: 600; }
    .table-custom tbody tr { transition: background-color 0.15s ease; }
    .table-custom tbody tr:hover { background-color: #fcfbff !important; }
    .form-section-box { border-left: 4px solid #7638ff !important; }
</style>
@endpush

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1">Create New Purchase</h4>
                <p class="text-muted small mb-0">Add products from vendors to increase inventory stock</p>
            </div>
            <div>
                <a href="{{ route('purchase.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3 d-inline-flex align-items-center gap-2">
                    <i class="fe fe-arrow-left"></i>
                    <span>Back to Purchases</span>
                </a>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('purchase.store.batch') }}" id="purchaseForm">
        @csrf

        <!-- Section 1: Vendor Selection -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold text-dark mb-3"><i class="fe fe-truck me-2 text-primary"></i>Vendor Information</h6>
                <div class="row g-3">
                    <div class="col-lg-6 col-md-8 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Select Vendor <span class="text-danger">*</span></label>
                        <select name="vendor_id" id="vendor_id" class="form-select select2 border-light-subtle" required>
                            <option value="">Select Vendor</option>
                            @foreach ($vendors as $vendor)
                                <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-4 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Purchase Date</label>
                        <input type="date" name="purchase_date" class="form-control border-light-subtle" value="{{ date('Y-m-d') }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Cart Items & Product Builder -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                    <h6 class="fw-bold text-dark mb-0"><i class="fe fe-shopping-cart me-2 text-primary"></i>Add Items to Cart</h6>
                    <span class="badge bg-light text-secondary border px-3 py-2"><i class="fas fa-barcode text-primary me-1"></i> Barcode &amp; Serial Scanner Ready</span>
                </div>

                <!-- Barcode Scanner -->
                <div class="form-section-box p-3 bg-white rounded-3 mb-4 border shadow-sm">
                    <div class="row align-items-center g-2">
                        <div class="col-auto text-primary">
                            <i class="fas fa-barcode fs-3"></i>
                        </div>
                        <div class="col">
                            <label class="form-label small fw-bold text-secondary mb-1">Scan Product Barcode:</label>
                            <input type="text" id="purchase_barcode_scanner" class="form-control form-control-lg border-light-subtle font-monospace" placeholder="Scan product barcode and press Enter..." autocomplete="off" autofocus>
                        </div>
                        <div class="col-auto align-self-end">
                            <button type="button" onclick="triggerManualBarcodeScan()" class="btn btn-primary btn-lg px-4 rounded-3"><i class="fas fa-search me-1"></i>Scan</button>
                        </div>
                    </div>
                    <div id="scan-feedback-alert" class="mt-2 small d-none"></div>
                </div>

                <!-- Manual Product Add Builder -->
                <div class="form-section-box p-3 bg-light rounded-3 mb-4 border" id="product-builder">
                    <div class="row g-3 align-items-end">
                        <div class="col-lg-4 col-md-6 col-12">
                            <label class="form-label small text-secondary fw-semibold mb-1">Select Product <span class="text-danger">*</span></label>
                            <select id="builder_product" class="form-select select2 border-light-subtle">
                                <option value="">Select Product</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}"
                                        data-name="{{ $product->name }}{{ $product->model ? ' ('.$product->model.')' : '' }}"
                                        data-barcode="{{ $product->barcode }}"
                                        data-is-serialized="{{ $product->is_serialized }}"
                                        data-price="{{ $product->latestPurchase->unit_price ?? 0 }}">
                                        {{ $product->name }} {{ $product->model ? ' ('.$product->model.')' : '' }} {{ $product->barcode ? '['.$product->barcode.']' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-2 col-md-3 col-6">
                            <label class="form-label small text-secondary fw-semibold mb-1">Unit Cost Price <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" id="builder_unit_price" class="form-control border-light-subtle" placeholder="0.00">
                        </div>

                        <div class="col-lg-2 col-md-3 col-6">
                            <label class="form-label small text-secondary fw-semibold mb-1">Quantity <span class="text-danger">*</span></label>
                            <input type="number" id="builder_qty" class="form-control border-light-subtle" min="1" value="1" placeholder="0">
                        </div>

                        <div class="col-lg-2 col-md-3 col-6">
                            <label class="form-label small text-secondary fw-semibold mb-1">Row Total</label>
                            <input type="number" step="0.01" id="builder_total" class="form-control border-light-subtle bg-white" readonly placeholder="0.00">
                        </div>

                        <div class="col-lg-2 col-md-3 col-6">
                            <button type="button" onclick="addItemToCart()" class="btn btn-primary w-100 rounded-3 py-2">
                                <i class="fe fe-plus-circle me-1"></i> Add to Cart
                            </button>
                        </div>
                    </div>

                    <!-- Serial Scanner Section -->
                    <div class="mt-3 p-3 bg-white rounded-3 border border-info-subtle d-none" id="serial-section">
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
                        <div id="serial-tags-container" class="d-flex flex-wrap gap-1 mb-2"></div>
                        <small class="text-muted d-block">Scan barcodes one-by-one or type and press Enter. Quantity will auto-increment.</small>
                    </div>
                </div>

                <!-- Cart Table -->
                <div class="table-responsive">
                    <table class="table table-hover table-custom align-middle mb-0" id="cartTable">
                        <thead class="bg-light text-secondary fs-7 text-uppercase">
                            <tr>
                                <th class="ps-3">#</th>
                                <th>Product</th>
                                <th>Serials</th>
                                <th style="width:130px">Unit Price</th>
                                <th style="width:100px">Qty</th>
                                <th style="width:130px">Total</th>
                                <th class="text-end pe-3" style="width:60px">Action</th>
                            </tr>
                        </thead>
                        <tbody id="cart_body">
                        </tbody>
                    </table>
                </div>

                <div id="empty-cart-msg" class="text-center py-4 text-muted">
                    <i class="fe fe-shopping-cart fs-1 text-light"></i>
                    <p class="mt-2 mb-0">No items added yet. Select a product above to start adding to cart.</p>
                </div>
            </div>
        </div>

        <!-- Section 3: Financial Summary -->
        <div class="card border-0 shadow-sm rounded-3 mb-4 d-none" id="summarySection">
            <div class="card-body p-4">
                <h6 class="fw-bold text-dark mb-3"><i class="fe fe-dollar-sign me-2 text-primary"></i>Financial Summary</h6>
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Sub Total</label>
                        <input type="number" step="0.01" id="subTotal" name="sub_total" class="form-control bg-light border-light-subtle" readonly value="0.00">
                    </div>
                    <div class="col-lg-3 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Discount</label>
                        <input type="number" step="0.01" id="discount" name="discount" class="form-control border-light-subtle" value="0.00" oninput="calculateGrandTotal()">
                    </div>
                    <div class="col-lg-3 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Grand Total (Payable)</label>
                        <input type="number" step="0.01" id="grandTotal" name="grand_total" class="form-control bg-light border-light-subtle fw-bold" readonly value="0.00">
                    </div>
                    <div class="col-lg-3 col-md-6 col-12"></div>
                    <div class="col-lg-3 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Payment Amount</label>
                        <input type="number" step="0.01" id="paymentAmount" name="payment" class="form-control border-light-subtle" value="0.00" oninput="calculateGrandTotal()">
                    </div>
                    <div class="col-lg-3 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Due Amount</label>
                        <input type="number" step="0.01" id="dueAmount" name="due" class="form-control bg-light border-light-subtle text-danger fw-bold" readonly value="0.00">
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4 d-flex justify-content-end gap-2">
                <a href="{{ route('purchase.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3">Cancel</a>
                <button type="submit" id="submitBtn" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm" disabled>
                    Submit Purchase
                </button>
            </div>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let cartItems = [];
    let itemCounter = 0;
    let currentSerials = [];

    const cartBody = document.getElementById('cart_body');
    const emptyMsg = document.getElementById('empty-cart-msg');
    const summarySection = document.getElementById('summarySection');
    const submitBtn = document.getElementById('submitBtn');
    const builderProduct = document.getElementById('builder_product');
    const builderUnitPrice = document.getElementById('builder_unit_price');
    const builderQty = document.getElementById('builder_qty');
    const builderTotal = document.getElementById('builder_total');
    const serialSection = document.getElementById('serial-section');

    function updateBuilderTotal() {
        const price = parseFloat(builderUnitPrice.value) || 0;
        const qty = parseFloat(builderQty.value) || 0;
        builderTotal.value = (price * qty).toFixed(2);
    }
    builderUnitPrice.addEventListener('input', updateBuilderTotal);
    builderQty.addEventListener('input', updateBuilderTotal);

    if (typeof $ !== 'undefined') {
        $('#builder_product').on('change', function() {
            const selected = this.options[this.selectedIndex];
            if (selected && selected.value) {
                const price = selected.getAttribute('data-price') || 0;
                const isSerialized = selected.getAttribute('data-is-serialized') == '1';
                builderUnitPrice.value = price;
                builderQty.value = 1;
                updateBuilderTotal();

                if (isSerialized) {
                    serialSection.classList.remove('d-none');
                } else {
                    serialSection.classList.add('d-none');
                }
                currentSerials = [];
                renderSerialTags();

                var urlTemplate = "{{ route('purchase.latest_price', ':id') }}";
                var url = urlTemplate.replace(':id', selected.value);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        if (response && response.price) {
                            builderUnitPrice.value = response.price;
                            updateBuilderTotal();
                        }
                    }
                });
            } else {
                serialSection.classList.add('d-none');
                currentSerials = [];
                renderSerialTags();
            }
        });
    }

    window.addScannedSerial = function() {
        const input = document.getElementById('scan_serial_input');
        if (!input) return;
        const val = input.value.trim();
        if (!val) return;
        if (currentSerials.includes(val)) {
            alert('Serial [' + val + '] is already added!');
            input.value = '';
            return;
        }
        currentSerials.push(val);
        input.value = '';
        renderSerialTags();
        builderQty.value = currentSerials.length;
        updateBuilderTotal();
        input.focus();
    };

    window.removeBuilderSerial = function(index) {
        currentSerials.splice(index, 1);
        renderSerialTags();
        builderQty.value = currentSerials.length || 1;
        updateBuilderTotal();
    };

    function renderSerialTags() {
        const container = document.getElementById('serial-tags-container');
        const badge = document.getElementById('scanned-serials-count');
        if (!container) return;
        container.innerHTML = '';
        currentSerials.forEach(function(sn, idx) {
            const tag = document.createElement('span');
            tag.className = 'badge bg-white text-dark border px-2 py-1 fs-7 d-inline-flex align-items-center gap-1';
            tag.innerHTML = '<span><i class="fas fa-barcode text-info me-1"></i>' + sn + '</span> <a href="javascript:void(0)" onclick="removeBuilderSerial(' + idx + ')" class="text-danger ms-1 fw-bold">&times;</a>';
            container.appendChild(tag);
        });
        if (badge) badge.innerText = currentSerials.length + ' Scanned';
    }

    const scanSerialInput = document.getElementById('scan_serial_input');
    if (scanSerialInput) {
        scanSerialInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') { e.preventDefault(); addScannedSerial(); }
        });
    }

    const barcodeScanner = document.getElementById('purchase_barcode_scanner');
    if (barcodeScanner) {
        barcodeScanner.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') { e.preventDefault(); triggerManualBarcodeScan(); }
        });
    }

    window.triggerManualBarcodeScan = function() {
        const code = barcodeScanner.value.trim();
        if (!code) return;
        fetch("{{ route('products.barcode_lookup') }}?code=" + encodeURIComponent(code))
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.success && data.product) {
                    if (typeof $ !== 'undefined') {
                        $('#builder_product').val(data.product.id).trigger('change');
                    }
                    barcodeScanner.value = '';
                } else {
                    showScanFeedback('Product not found with this barcode.', 'danger');
                }
            })
            .catch(function() { showScanFeedback('Error scanning barcode.', 'danger'); });
    };

    function showScanFeedback(msg, type) {
        const el = document.getElementById('scan-feedback-alert');
        if (!el) return;
        el.className = 'mt-2 small alert alert-' + type + ' py-1 px-2';
        el.textContent = msg;
        el.classList.remove('d-none');
        setTimeout(function() { el.classList.add('d-none'); }, 3000);
    }

    window.addItemToCart = function() {
        const productId = builderProduct.value;
        if (!productId) { alert('Please select a product first.'); return; }

        const selected = builderProduct.options[builderProduct.selectedIndex];
        const productName = selected.getAttribute('data-name') || selected.text.trim();
        const unitPrice = parseFloat(builderUnitPrice.value) || 0;
        const qty = parseInt(builderQty.value) || 0;
        const isSerialized = selected.getAttribute('data-is-serialized') == '1';

        if (unitPrice <= 0) { alert('Please enter a valid unit price.'); return; }
        if (qty <= 0) { alert('Please enter a valid quantity.'); return; }

        let serials = [];
        if (isSerialized) {
            serials = currentSerials.slice();
            if (serials.length !== qty) {
                alert('Scanned serials (' + serials.length + ') must match quantity (' + qty + ').');
                return;
            }
        }

        itemCounter++;
        const rowTotal = unitPrice * qty;
        cartItems.push({
            id: itemCounter,
            productId: productId,
            productName: productName,
            unitPrice: unitPrice,
            qty: qty,
            total: rowTotal,
            serials: serials,
        });

        renderCart();
        calculateGrandTotal();

        if (typeof $ !== 'undefined') { $('#builder_product').val('').trigger('change'); }
        builderUnitPrice.value = '';
        builderQty.value = 1;
        builderTotal.value = '';
        serialSection.classList.add('d-none');
        currentSerials = [];
        renderSerialTags();
    };

    window.removeCartItem = function(id) {
        cartItems = cartItems.filter(function(item) { return item.id !== id; });
        renderCart();
        calculateGrandTotal();
    };

    function renderCart() {
        cartBody.innerHTML = '';

        if (cartItems.length === 0) {
            emptyMsg.classList.remove('d-none');
            summarySection.classList.add('d-none');
            submitBtn.disabled = true;
            return;
        }

        emptyMsg.classList.add('d-none');
        summarySection.classList.remove('d-none');
        submitBtn.disabled = false;

        cartItems.forEach(function(item, idx) {
            var serialDisplay = item.serials.length > 0
                ? item.serials.map(function(s) { return '<span class="badge bg-white text-dark border px-1 py-0 fs-8">' + s + '</span>'; }).join(' ')
                : '<span class="text-muted small">—</span>';

            var serialHiddenInputs = item.serials.map(function(s) {
                return '<input type="hidden" name="items[' + idx + '][serial_numbers][]" value="' + s + '">';
            }).join('');

            var row = '<tr data-item-id="' + item.id + '">' +
                '<td class="ps-3 text-muted fw-semibold">' + (idx + 1) + '</td>' +
                '<td><span class="fw-bold text-dark">' + item.productName + '</span>' +
                '<input type="hidden" name="items[' + idx + '][product_id]" value="' + item.productId + '"></td>' +
                '<td><div class="d-flex flex-wrap gap-1">' + serialDisplay + '</div>' + serialHiddenInputs + '</td>' +
                '<td><input type="number" step="0.01" name="items[' + idx + '][unit_price]" value="' + item.unitPrice.toFixed(2) + '" class="form-control form-control-sm border-light-subtle cart-unit-price" onchange="updateCartRow(' + idx + ')"></td>' +
                '<td><input type="number" name="items[' + idx + '][quantity]" value="' + item.qty + '" min="1" class="form-control form-control-sm border-light-subtle cart-qty" onchange="updateCartRow(' + idx + ')"></td>' +
                '<td><input type="number" step="0.01" name="items[' + idx + '][total]" value="' + item.total.toFixed(2) + '" class="form-control form-control-sm border-light-subtle bg-light cart-total" readonly></td>' +
                '<td class="text-end pe-3"><button type="button" onclick="removeCartItem(' + item.id + ')" class="btn btn-outline-danger btn-sm px-2 rounded-2" title="Remove"><i class="fa fa-times"></i></button></td>' +
                '</tr>';
            cartBody.insertAdjacentHTML('beforeend', row);
        });
    }

    window.updateCartRow = function(idx) {
        if (!cartItems[idx]) return;
        var rows = cartBody.querySelectorAll('tr');
        var row = rows[idx];
        if (!row) return;

        var unitPriceInput = row.querySelector('.cart-unit-price');
        var qtyInput = row.querySelector('.cart-qty');
        var totalInput = row.querySelector('.cart-total');

        var newPrice = parseFloat(unitPriceInput.value) || 0;
        var newQty = parseInt(qtyInput.value) || 0;
        var newTotal = newPrice * newQty;

        totalInput.value = newTotal.toFixed(2);
        cartItems[idx].unitPrice = newPrice;
        cartItems[idx].qty = newQty;
        cartItems[idx].total = newTotal;

        calculateGrandTotal();
    };

    window.calculateGrandTotal = function() {
        var subTotal = 0;
        cartItems.forEach(function(item) { subTotal += item.total; });

        var discount = parseFloat(document.getElementById('discount').value) || 0;
        var grandTotal = subTotal - discount;
        var payment = parseFloat(document.getElementById('paymentAmount').value) || 0;
        var due = grandTotal - payment;

        document.getElementById('subTotal').value = subTotal.toFixed(2);
        document.getElementById('grandTotal').value = grandTotal.toFixed(2);
        document.getElementById('dueAmount').value = due.toFixed(2);
    };
});
</script>
@endpush
