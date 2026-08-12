@extends('frontend.layouts.app')

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1">Add Sale Order</h4>
                <p class="text-muted small mb-0">Create a new retail sale order, add items to cart, and calculate payment breakdown</p>
            </div>
            <div>
                <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3 shadow-sm">
                    <i class="fa fa-arrow-left me-2"></i>Back to Sales
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <form action="{{ route('sales.store') }}" method="POST" target="_blank" onsubmit="reloadAfterSubmit()" id="createSaleForm">
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
                            <input type="text" name="name" class="form-control border-light-subtle" id="newClientName" placeholder="Enter Customer Name" required autocomplete="off">
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <label class="form-label small text-secondary fw-semibold mb-1">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control border-light-subtle" id="newClientPhone" placeholder="Enter Phone Number" required autocomplete="off">
                        </div>
                        <div class="col-lg-4 col-md-12 col-12">
                            <label class="form-label small text-secondary fw-semibold mb-1">Address <span class="text-danger">*</span></label>
                            <input type="text" name="address" class="form-control border-light-subtle" id="newClientAddress" placeholder="Enter Customer Address" required autocomplete="off">
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
                                @foreach ($existingClients as $client)
                                    <option value="{{ $client->id }}">
                                        {{ $client->name }} - {{ $client->phone }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Cart Items & Product Builder -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                    <h6 class="fw-bold text-dark mb-0"><i class="fe fe-shopping-cart me-2 text-primary"></i>Add Items to Cart</h6>
                    <span class="badge bg-light text-secondary border px-3 py-2"><i class="fas fa-barcode text-primary me-1"></i> Barcode & Serial Scanner Ready</span>
                </div>

                <!-- Instant Barcode / Serial Scanner Input -->
                <div class="p-3 bg-white rounded-3 mb-4 border shadow-sm" style="border-left: 4px solid #7638ff !important;">
                    <div class="row align-items-center g-2">
                        <div class="col-auto text-primary">
                            <i class="fas fa-barcode fs-3"></i>
                        </div>
                        <div class="col">
                            <label class="form-label small fw-bold text-secondary mb-1">Scan Product Barcode or Unit Serial Number:</label>
                            <input type="text" id="sales_barcode_scanner" class="form-control form-control-lg border-light-subtle font-monospace" placeholder="Scan Barcode / Serial Number with scanner gun and press Enter..." autocomplete="off" autofocus>
                        </div>
                        <div class="col-auto align-self-end">
                            <button type="button" onclick="triggerManualScan()" class="btn btn-primary btn-lg px-4 rounded-3"><i class="fas fa-search me-1"></i>Scan / Verify</button>
                        </div>
                    </div>
                    <div id="scan-feedback-alert" class="mt-2 small d-none"></div>
                </div>

                <!-- Manual Product Add Builder Card -->
                <div class="p-3 bg-light rounded-3 mb-4 border" id="form-group-item1">
                    <div class="row g-3 align-items-end">
                        <div class="col-lg-4 col-md-6 col-12">
                            <label class="form-label small text-secondary fw-semibold mb-1">Select Product (Manual) <span class="text-danger">*</span></label>
                            <select onchange="selectProduct(1)" id="product1" class="form-select select2 border-light-subtle">
                                <option value="">Select Product</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}"
                                        data-name="{{ $product->name }}{{ $product->model ? '('.$product->model.')' : '' }}"
                                        data-stock="{{ $product->inventory->current_stock ?? 0 }}"
                                        data-price="{{ $product->latestPurchase->unit_price ?? 0 }}"
                                        data-warranty="{{ $product->warranty ?? 0 }}"
                                        data-is-serialized="{{ $product->is_serialized }}"
                                        data-barcode="{{ $product->barcode }}">
                                        {{ $product->name }} {{ $product->model ? '('.$product->model.')' : '' }} {{ $product->barcode ? '['.$product->barcode.']' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-2 col-md-3 col-6">
                            <label class="form-label small text-secondary fw-semibold mb-1">Stock</label>
                            <input type="number" id="stock1" class="form-control border-light-subtle bg-white" readonly>
                        </div>

                        <div class="col-lg-2 col-md-3 col-6">
                            <label class="form-label small text-secondary fw-semibold mb-1">Warranty (Days)</label>
                            <input type="number" id="warranty1" class="form-control border-light-subtle bg-white" readonly>
                        </div>

                        <div class="col-lg-2 col-md-3 col-6">
                            <label class="form-label small text-secondary fw-semibold mb-1">Purchase Price</label>
                            <input type="number" id="purchase_price1" class="form-control border-light-subtle bg-white" readonly>
                        </div>

                        <div class="col-lg-2 col-md-3 col-6">
                            <label class="form-label small text-secondary fw-semibold mb-1">Selling Price <span class="text-danger">*</span></label>
                            <input oninput="calculateTotal()" onchange="calculateTotal()" type="number" id="unit_price1" class="form-control border-light-subtle" step="0.01" min="0" placeholder="0.00">
                        </div>

                        <div class="col-lg-2 col-md-3 col-6">
                            <label class="form-label small text-secondary fw-semibold mb-1">Quantity <span class="text-danger">*</span></label>
                            <input oninput="calculateTotal()" onchange="calculateTotal()" type="number" id="qty1" class="form-control border-light-subtle" min="1" value="1">
                        </div>

                        <div class="col-lg-2 col-md-3 col-6">
                            <label class="form-label small text-secondary fw-semibold mb-1">Line Total</label>
                            <input type="text" id="total1" class="form-control border-light-subtle bg-white fw-bold text-success" readonly value="0.00">
                        </div>

                        <div class="col-lg-2 col-md-6 col-12 ms-auto">
                            <button type="button" onclick="addItem()" class="btn btn-success w-100 rounded-3">
                                <i class="fe fe-plus me-1"></i>Add Product
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Added Items List Table -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="cartItemsTable">
                        <thead class="bg-light text-secondary fs-7 text-uppercase">
                            <tr>
                                <th style="width: 40%;">Product & Serial Number(s)</th>
                                <th style="width: 20%;">Unit Price</th>
                                <th style="width: 15%;">Quantity</th>
                                <th style="width: 20%;">Total Price</th>
                                <th style="width: 5%;" class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody id="item_container">
                            <!-- Dynamic Cart Rows Added Here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Section 3: Summary Breakdown -->
        <div id="summerySection" class="card border-0 shadow-sm rounded-3 mb-4 d-none">
            <div class="card-body p-4">
                <h6 class="fw-bold text-dark mb-3"><i class="fe fe-dollar-sign me-2 text-primary"></i>Payment Breakdown</h6>

                <div class="row g-3 align-items-end">
                    <div class="col-lg-2 col-md-4 col-6">
                        <label class="form-label small text-secondary fw-semibold mb-1">Sub Total</label>
                        <input onchange="calculateTotal()" type="number" id="subTotal" name="subTotal" class="form-control border-light-subtle bg-light" readonly>
                    </div>

                    <div class="col-lg-2 col-md-4 col-6">
                        <label class="form-label small text-secondary fw-semibold mb-1">Discount Amount</label>
                        <input onchange="calculateTotal()" type="number" id="discount" name="discount" class="form-control border-light-subtle" value="0" min="0" step="0.01">
                    </div>

                    <div class="col-lg-2 col-md-4 col-6">
                        <label class="form-label small text-secondary fw-semibold mb-1">VAT (%)</label>
                        <input onchange="calculateTotal()" type="number" id="vat" name="vat" class="form-control border-light-subtle" value="0" min="0" step="0.01">
                    </div>

                    <div class="col-lg-2 col-md-4 col-6">
                        <label class="form-label small text-secondary fw-semibold mb-1">Tax (%)</label>
                        <input onchange="calculateTotal()" type="number" id="tax" name="tax" class="form-control border-light-subtle" value="0" min="0" step="0.01">
                    </div>

                    <div class="col-lg-2 col-md-4 col-6">
                        <label class="form-label small text-secondary fw-semibold mb-1">Delivery Charge</label>
                        <input onchange="calculateTotal()" type="number" id="delivery_charge" name="delivery_charge" class="form-control border-light-subtle" value="0" min="0" step="0.01">
                    </div>

                    <div class="col-lg-2 col-md-4 col-6">
                        <label class="form-label small text-secondary fw-semibold mb-1">Grand Total</label>
                        <input type="number" id="grandTotal" name="grandTotal" class="form-control border-light-subtle bg-light fw-bold text-primary" readonly>
                    </div>

                    <div class="col-lg-3 col-md-6 col-6">
                        <label class="form-label small text-secondary fw-semibold mb-1">Current Payment</label>
                        <input type="number" name="advanced_payment" id="advancedPayment" class="form-control border-light-subtle" value="0" min="0" step="0.01">
                    </div>

                    <div class="col-lg-3 col-md-6 col-6">
                        <label class="form-label small text-secondary fw-semibold mb-1">Due Payment</label>
                        <input type="number" id="duePayment" name="duePayment" class="form-control border-light-subtle bg-light fw-bold text-danger" readonly>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 pt-4 mt-3 border-top">
                    <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3">Create Sale</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
var itemNumber = 2;
window.activeCartSerials = []; // Tracks all serial numbers currently added to cart

function playBeep(success = true) {
    try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);
        if (success) {
            osc.frequency.setValueAtTime(800, ctx.currentTime);
            osc.frequency.setValueAtTime(1200, ctx.currentTime + 0.08);
            gain.gain.setValueAtTime(0.2, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.2);
            osc.start(ctx.currentTime);
            osc.stop(ctx.currentTime + 0.2);
        } else {
            osc.frequency.setValueAtTime(300, ctx.currentTime);
            osc.frequency.setValueAtTime(200, ctx.currentTime + 0.1);
            gain.gain.setValueAtTime(0.3, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.3);
            osc.start(ctx.currentTime);
            osc.stop(ctx.currentTime + 0.3);
        }
    } catch(e) {}
}

function showScanAlert(message, isSuccess = true) {
    const alertBox = document.getElementById('scan-feedback-alert');
    if (!alertBox) return;
    alertBox.className = `mt-2 small alert ${isSuccess ? 'alert-success' : 'alert-danger'} py-2 px-3 mb-0 rounded-3 d-flex align-items-center gap-2`;
    alertBox.innerHTML = `<i class="fas ${isSuccess ? 'fa-check-circle' : 'fa-exclamation-triangle'}"></i> <span>${message}</span>`;
    alertBox.classList.remove('d-none');
    setTimeout(() => {
        alertBox.classList.add('d-none');
    }, 4000);
}

function triggerManualScan() {
    const input = document.getElementById('sales_barcode_scanner');
    if (input && input.value.trim()) {
        handleBarcodeScan(input.value.trim());
    }
}

function handleBarcodeScan(code) {
    const scannerInput = document.getElementById('sales_barcode_scanner');
    
    fetch(`{{ route('products.barcode_lookup') }}?code=${encodeURIComponent(code)}`)
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                playBeep(false);
                showScanAlert(data.message || `No item found for [${code}]`, false);
                if (scannerInput) scannerInput.select();
                return;
            }

            const p = data.product;

            // Scenario 1: Scanned a Specific Serial Number
            if (data.type === 'serial') {
                if (data.status !== 'available') {
                    playBeep(false);
                    showScanAlert(`Cannot sell Serial [${data.serial_number}] — Status is already ${data.status.toUpperCase()}!`, false);
                    if (scannerInput) scannerInput.select();
                    return;
                }

                if (window.activeCartSerials.includes(data.serial_number)) {
                    playBeep(false);
                    showScanAlert(`Serial [${data.serial_number}] is already added in the cart!`, false);
                    if (scannerInput) scannerInput.select();
                    return;
                }

                // Add to active serials
                window.activeCartSerials.push(data.serial_number);
                addProductToCart(p, 1, p.selling_price, data.serial_number);
                playBeep(true);
                showScanAlert(`Verified & Added: ${p.name} (SN: ${data.serial_number})`, true);
                if (scannerInput) {
                    scannerInput.value = '';
                    scannerInput.focus();
                }
            } 
            // Scenario 2: Scanned a Product Barcode
            else {
                if (p.is_serialized) {
                    playBeep(false);
                    showScanAlert(`Product [${p.name}] is Serialized. Please scan the unit serial barcode on the box!`, false);
                    if (scannerInput) scannerInput.select();
                } else {
                    if (p.stock <= 0) {
                        playBeep(false);
                        showScanAlert(`Product [${p.name}] is OUT OF STOCK!`, false);
                        if (scannerInput) scannerInput.select();
                        return;
                    }
                    addProductToCart(p, 1, p.selling_price, null);
                    playBeep(true);
                    showScanAlert(`Added: ${p.name}`, true);
                    if (scannerInput) {
                        scannerInput.value = '';
                        scannerInput.focus();
                    }
                }
            }
        })
        .catch(err => {
            playBeep(false);
            showScanAlert('Network error while looking up barcode.', false);
        });
}

function addProductToCart(product, qty = 1, price = 0, serialNumber = null) {
    const existingRow = document.querySelector(`.group-item.item${product.id}`);
    
    if (existingRow) {
        const itemNum = existingRow.dataset.itemnumber;
        const qtyInput = document.getElementById('qty' + itemNum);
        
        if (serialNumber) {
            // Append serial tag
            const serialContainer = document.getElementById('serial_tags_' + itemNum);
            if (serialContainer) {
                const tag = document.createElement('span');
                tag.className = 'badge bg-light text-dark border px-2 py-1 font-monospace fs-7 me-1 mb-1 d-inline-flex align-items-center gap-1';
                tag.innerHTML = `<i class="fas fa-barcode text-info"></i> ${serialNumber} <input type="hidden" name="item_serials[${product.id}][]" value="${serialNumber}">`;
                serialContainer.appendChild(tag);
            }
        }

        if (qtyInput) {
            qtyInput.value = parseInt(qtyInput.value || 0) + parseInt(qty);
        }
    } else {
        const unitPrice = (parseFloat(price) || 0).toFixed(2);
        const rowTotal = (parseFloat(unitPrice) * parseFloat(qty)).toFixed(2);
        const serialTagHtml = serialNumber ? `
            <div id="serial_tags_${itemNumber}" class="mt-1 d-flex flex-wrap">
                <span class="badge bg-light text-dark border px-2 py-1 font-monospace fs-7 me-1 mb-1 d-inline-flex align-items-center gap-1">
                    <i class="fas fa-barcode text-info"></i> ${serialNumber}
                    <input type="hidden" name="item_serials[${product.id}][]" value="${serialNumber}">
                </span>
            </div>
        ` : `<div id="serial_tags_${itemNumber}" class="mt-1 d-flex flex-wrap"></div>`;

        const html = `
            <tr class="item${product.id} group-item" data-itemnumber="${itemNumber}" id="form-group-item${itemNumber}">
                <td>
                    <input type="hidden" name="product[]" value="${product.id}">
                    <span class="fw-bold text-dark d-block">${product.name} ${product.model ? '('+product.model+')' : ''}</span>
                    ${serialTagHtml}
                </td>
                <td>
                    <input onchange="calculateTotal()" type="number" step="0.01" name="unit_price[]" id="unit_price${itemNumber}" class="form-control border-light-subtle unit-price" value="${unitPrice}">
                </td>
                <td>
                    <input onchange="calculateTotal()" type="number" name="qty[]" id="qty${itemNumber}" class="qty${product.id} form-control border-light-subtle qty" min="1" value="${qty}">
                </td>
                <td>
                    <input type="number" step="0.01" name="total" id="total${itemNumber}" class="form-control border-light-subtle bg-light total" readonly value="${rowTotal}">
                </td>
                <td class="text-end">
                    <button onclick="removeItem(${itemNumber}, ${product.id})" type="button" class="btn btn-outline-danger btn-sm px-3 rounded-2" title="Remove Item">
                        <i class="fa fa-times"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#item_container').append(html);
        itemNumber++;
    }

    toggleSummarySection();
    calculateTotal();
}

function removeItem(item, productId) {
    // Remove tracked serials for this row
    const serialInputs = document.querySelectorAll(`#form-group-item${item} input[name="item_serials[${productId}][]"]`);
    serialInputs.forEach(input => {
        const idx = window.activeCartSerials.indexOf(input.value);
        if (idx !== -1) window.activeCartSerials.splice(idx, 1);
    });

    document.getElementById('form-group-item' + item)?.remove();
    toggleSummarySection();
    calculateTotal();
}

function reloadAfterSubmit() {
    setTimeout(function() {
        window.location.reload();
    }, 500);
}

function selectProduct(item) {
    var selected = $('#product' + item + ' option:selected');
    var selectedPrice = selected.data('price') || 0;
    var selectedWarranty = selected.data('warranty') || 0;
    var selectedStock = selected.data('stock') || 0;

    if (document.getElementById('purchase_price' + item))
        document.getElementById('purchase_price' + item).value = selectedPrice;
    if (document.getElementById('warranty' + item))
        document.getElementById('warranty' + item).value = selectedWarranty;
    if (document.getElementById('stock' + item))
        document.getElementById('stock' + item).value = selectedStock;
    if (document.getElementById('unit_price' + item) && !document.getElementById('unit_price' + item).value)
        document.getElementById('unit_price' + item).value = selectedPrice;

    calculateTotal();
}

function updatePreviewTotal() {
    const previewUnitPrice = Number(document.getElementById('unit_price1')?.value || 0);
    const previewQty = Number(document.getElementById('qty1')?.value || 0);
    const previewTotal = previewUnitPrice * previewQty;
    const previewTotalInput = document.getElementById('total1');

    if (previewTotalInput) {
        previewTotalInput.value = previewTotal.toFixed(2);
    }
    return previewTotal;
}

function addItem() {
    var product = document.getElementById('product1').value;
    var qty = document.getElementById('qty1').value;

    if (product == "") {
        alert("Please select a product first.");
        return;
    }

    let selectedOption = document.getElementById('product1').options[document.getElementById('product1').selectedIndex];
    let selectedName = selectedOption.text;
    const price = document.getElementById('unit_price1').value;

    if (price.trim() === "") {
        alert("Please enter selling unit price.");
        return;
    }

    if (qty.trim() === "" || parseFloat(qty) <= 0) {
        alert("Please enter a valid quantity.");
        return;
    }

    const stock = parseFloat(document.getElementById('stock1').value) || 0;
    if (parseFloat(qty) > stock) {
        alert("Quantity exceeds available stock (" + stock + ")!");
        return;
    }

    addProductToCart({
        id: product,
        name: selectedName,
        model: ''
    }, qty, price, null);
}

function toggleSummarySection() {
    const hasItems = document.querySelectorAll('.group-item[data-itemnumber]').length > 0;
    document.getElementById('summerySection').classList.toggle('d-none', !hasItems);
}

function calculateTotal() {
    updatePreviewTotal();
    let subTotal = 0;

    const eles = document.getElementsByClassName('group-item');
    for (let i = 0; i < eles.length; i++) {
        const itemNum = eles[i].dataset.itemnumber;
        if (itemNum == 1) continue;

        const unit_price = parseFloat(document.getElementById('unit_price' + itemNum)?.value) || 0;
        const qty = parseFloat(document.getElementById('qty' + itemNum)?.value) || 0;
        const totalEle = document.getElementById('total' + itemNum);

        const total = qty * unit_price;
        if (totalEle) totalEle.value = total.toFixed(2);

        subTotal += total;
    }

    let discount = parseFloat(document.getElementById('discount').value) || 0;
    if (discount > subTotal) discount = subTotal;
    document.getElementById('discount').value = discount.toFixed(2);

    const vatPercent = parseFloat(document.getElementById('vat').value) || 0;
    const taxPercent = parseFloat(document.getElementById('tax').value) || 0;
    const deliveryCharge = parseFloat(document.getElementById('delivery_charge').value) || 0;

    const vatAmount = (subTotal * vatPercent) / 100;
    const taxAmount = (subTotal * taxPercent) / 100;

    const grandTotal = subTotal - discount + vatAmount + taxAmount + deliveryCharge;
    document.getElementById('subTotal').value = subTotal.toFixed(2);
    document.getElementById('grandTotal').value = grandTotal.toFixed(2);

    const advanced = parseFloat(document.getElementById('advancedPayment').value) || 0;
    const due = grandTotal - advanced;
    document.getElementById('duePayment').value = due.toFixed(2);

    toggleSummarySection();
}

document.addEventListener('DOMContentLoaded', function() {
    const scannerInput = document.getElementById('sales_barcode_scanner');
    if (scannerInput) {
        scannerInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const code = this.value.trim();
                if (code) {
                    handleBarcodeScan(code);
                }
            }
        });
    }

    const newClientRadio = document.getElementById('newClient');
    const existingClientRadio = document.getElementById('existingClient');
    const newClientForm = document.getElementById('newClientForm');
    const existingClientForm = document.getElementById('existingClientForm');
    const newClientInputs = document.querySelectorAll('#newClientForm input');

    function toggleClientForms() {
        if (newClientRadio.checked) {
            newClientForm.style.display = 'block';
            existingClientForm.style.display = 'none';
            newClientInputs.forEach(input => input.required = true);
            document.getElementById('clientSelect').required = false;
        } else {
            newClientForm.style.display = 'none';
            existingClientForm.style.display = 'block';
            newClientInputs.forEach(input => input.required = false);
            document.getElementById('clientSelect').required = true;
        }
    }

    if (newClientRadio && existingClientRadio) {
        newClientRadio.addEventListener('change', toggleClientForms);
        existingClientRadio.addEventListener('change', toggleClientForms);
        toggleClientForms();
    }

    document.getElementById('advancedPayment')?.addEventListener('input', calculateTotal);
    $('#unit_price1, #qty1').on('input change', function() {
        updatePreviewTotal();
    });
});
</script>
@endpush
