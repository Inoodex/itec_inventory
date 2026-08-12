@extends('frontend.layouts.app')

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1">Create Product Return</h4>
                <p class="text-muted small mb-0">Select customer sale order, specify items to return, and process refund calculation</p>
            </div>
            <div>
                <a href="{{ route('returns.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3 shadow-sm">
                    <i class="fa fa-arrow-left me-2"></i>Back to Product Returns
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <form method="POST" action="{{ route('returns.store') }}" id="returnForm">
        @csrf

        <!-- Section 1: Sale Order Selection -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark mb-0"><i class="fe fe-shopping-cart me-2 text-primary"></i>Sale & Customer Selection</h6>
                    <span class="badge bg-light text-secondary border small px-2 py-1"><i class="fas fa-barcode text-primary me-1"></i> Scanner Ready</span>
                </div>

                <!-- Barcode / Serial Quick Scanner for Returns -->
                <div class="p-2 px-3 bg-light rounded-3 border mb-3 d-flex align-items-center gap-2">
                    <i class="fas fa-barcode text-primary fs-5"></i>
                    <div class="flex-grow-1">
                        <input type="text" id="return_barcode_scanner" class="form-control form-control-sm border-0 bg-transparent shadow-none" placeholder="Scan Unit Serial Number or Product Barcode to Auto-Select Sale Order..." autocomplete="off">
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-lg-6 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Select Sale Order <span class="text-danger">*</span></label>
                        <select name="sale_id" id="saleSelect" class="form-select border-light-subtle select2" required onchange="handleSaleChange(this.value)">
                            <option value="">Select Sale Order</option>
                            @foreach($sales as $s)
                                <option value="{{ $s->id }}" {{ request('sale_id') == $s->id ? 'selected' : '' }} data-customer="{{ $s->customer_id }}">
                                    #{{ $s->order_no }} - {{ $s->customer->name ?? 'No Customer' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-6 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Return Date <span class="text-danger">*</span></label>
                        <input type="date" name="return_date" class="form-control border-light-subtle" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Sale Items Display -->
        <div id="saleItemsSection" class="card border-0 shadow-sm rounded-3 mb-4 {{ $sale ? '' : 'd-none' }}">
            <div class="card-body p-4">
                <h6 class="fw-bold text-dark mb-3"><i class="fe fe-box me-2 text-primary"></i>Select Items to Return</h6>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-secondary fs-7 text-uppercase">
                            <tr>
                                <th>Product</th>
                                <th>Sold Qty</th>
                                <th>Return Qty</th>
                                <th>Unit Price</th>
                                <th>Return Reason</th>
                                <th>Condition</th>
                                <th>Notes</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody id="saleItemsTable">
                            @if($sale && $saleItems)
                                @foreach($saleItems as $item)
                                    <tr data-product-id="{{ $item->product_id }}" data-max-qty="{{ $item->quantity }}">
                                        <td>
                                            <span class="fw-bold text-dark d-block">{{ $item->product->name ?? 'N/A' }}</span>
                                            <input type="hidden" name="items[{{ $loop->index }}][product_id]" value="{{ $item->product_id }}">
                                        </td>
                                        <td>
                                            <span class="badge badge-soft-info px-2 py-1 rounded-2">{{ $item->quantity }}</span>
                                        </td>
                                        <td>
                                            <input type="number" name="items[{{ $loop->index }}][quantity]" class="form-control border-light-subtle qty-input" min="0" max="{{ $item->quantity }}" value="0" style="width: 90px;" oninput="calculateTotal()">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="items[{{ $loop->index }}][unit_price]" class="form-control border-light-subtle bg-light unit-price" value="{{ $item->unit_price }}" readonly style="width: 110px;">
                                        </td>
                                        <td>
                                            <select name="items[{{ $loop->index }}][return_reason]" class="form-select border-light-subtle">
                                                <option value="damaged">Damaged</option>
                                                <option value="wrong_item">Wrong Item</option>
                                                <option value="customer_changed_mind">Customer Changed Mind</option>
                                                <option value="defective">Defective</option>
                                                <option value="expired">Expired</option>
                                                <option value="other">Other</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="items[{{ $loop->index }}][condition]" class="form-select border-light-subtle">
                                                <option value="good">Good</option>
                                                <option value="damaged">Damaged</option>
                                                <option value="defective">Defective</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="items[{{ $loop->index }}][notes]" class="form-control border-light-subtle" placeholder="Item notes...">
                                        </td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-outline-danger btn-sm px-3 rounded-2" onclick="this.closest('tr').remove(); calculateTotal();">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-info border-0 rounded-3 mt-3 mb-0" id="noItemsMessage" style="display: {{ $sale && count($saleItems) > 0 ? 'none' : 'block' }}">
                    <i class="fe fe-info me-2"></i>Select a sale order above to display available purchased items.
                </div>
            </div>
        </div>

        <!-- Section 3: Reason & Summary Breakdown -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold text-dark mb-3"><i class="fe fe-file-text me-2 text-primary"></i>Return Details & Financial Summary</h6>

                <div class="row g-3 align-items-end mb-4">
                    <div class="col-lg-8 col-md-7 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">General Reason / Additional Notes</label>
                        <textarea name="reason" class="form-control border-light-subtle" rows="3" placeholder="Enter general reason or return instructions..."></textarea>
                    </div>

                    <div class="col-lg-4 col-md-5 col-12">
                        <div class="p-3 bg-light rounded-3 border text-center">
                            <span class="text-muted small d-block mb-1">Total Calculated Refund Amount</span>
                            <h3 class="fw-bold text-primary mb-0">৳<span id="totalRefund">0.00</span></h3>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 pt-4 border-top">
                    <a href="{{ route('returns.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3">Create Return</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    window.handleSaleChange = function(saleId) {
        const saleItemsSection = document.getElementById('saleItemsSection');
        const saleItemsTable = document.getElementById('saleItemsTable');
        const noItemsMessage = document.getElementById('noItemsMessage');

        if (!saleId) {
            saleItemsSection.classList.add('d-none');
            return;
        }

        if (saleItemsTable) saleItemsTable.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted">Loading purchased sale items...</td></tr>';
        if (saleItemsSection) saleItemsSection.classList.remove('d-none');

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        fetch(`/product-returns/sale-items/${saleId}`, {
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response error: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.items && data.items.length > 0) {
                    let html = '';
                    data.items.forEach((item, index) => {
                        html += `
                            <tr data-product-id="${item.product_id}" data-max-qty="${item.quantity}">
                                <td>
                                    <span class="fw-bold text-dark d-block">${item.product_name}</span>
                                    <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
                                    <input type="hidden" name="items[${index}][sales_item_id]" value="${item.id}">
                                </td>
                                <td><span class="badge badge-soft-info px-2 py-1 rounded-2">${item.quantity}</span></td>
                                <td>
                                    <input type="number" name="items[${index}][quantity]"
                                        class="form-control border-light-subtle qty-input" min="0" max="${item.quantity}"
                                        value="0" style="width: 90px;" oninput="calculateTotal()">
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="items[${index}][unit_price]"
                                        class="form-control border-light-subtle bg-light unit-price" value="${item.unit_price}" readonly style="width: 110px;">
                                </td>
                                <td>
                                    <select name="items[${index}][return_reason]" class="form-select border-light-subtle">
                                        <option value="damaged">Damaged</option>
                                        <option value="wrong_item">Wrong Item</option>
                                        <option value="customer_changed_mind">Customer Changed Mind</option>
                                        <option value="defective">Defective</option>
                                        <option value="expired">Expired</option>
                                        <option value="other">Other</option>
                                    </select>
                                </td>
                                <td>
                                    <select name="items[${index}][condition]" class="form-select border-light-subtle">
                                        <option value="good">Good</option>
                                        <option value="damaged">Damaged</option>
                                        <option value="defective">Defective</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="items[${index}][notes]"
                                        class="form-control border-light-subtle" placeholder="Notes...">
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-outline-danger btn-sm px-3 rounded-2" onclick="this.closest('tr').remove(); calculateTotal();">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    if (saleItemsTable) saleItemsTable.innerHTML = html;
                    if (saleItemsSection) saleItemsSection.classList.remove('d-none');
                    if (noItemsMessage) noItemsMessage.style.display = 'none';
                } else {
                    if (saleItemsTable) saleItemsTable.innerHTML = '';
                    if (saleItemsSection) saleItemsSection.classList.add('d-none');
                    if (noItemsMessage) {
                        noItemsMessage.style.display = 'block';
                        noItemsMessage.textContent = 'No items found for this sale order.';
                    }
                }
            })
            .catch(error => {
                if (saleItemsTable) saleItemsTable.innerHTML = '<tr><td colspan="8" class="text-center text-danger py-3">Error loading sale items.</td></tr>';
            });
    };

    window.calculateTotal = function() {
        let total = 0;
        document.querySelectorAll('#saleItemsTable tr').forEach(row => {
            const qtyInput = row.querySelector('.qty-input');
            const priceInput = row.querySelector('.unit-price');
            if (qtyInput && priceInput) {
                const qty = parseFloat(qtyInput.value || 0);
                const price = parseFloat(priceInput.value || 0);
                if (qty > 0) {
                    total += qty * price;
                }
            }
        });
        document.getElementById('totalRefund').textContent = total.toFixed(2);
    };

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('returnForm').addEventListener('submit', function(e) {
            const items = document.querySelectorAll('#saleItemsTable tr');
            let hasValidItem = false;

            items.forEach(row => {
                const qtyInput = row.querySelector('.qty-input');
                if (qtyInput) {
                    const qty = parseFloat(qtyInput.value || 0);
                    if (qty > 0) {
                        hasValidItem = true;
                    }
                }
            });

            if (!hasValidItem) {
                e.preventDefault();
                alert('Please select at least one item to return with quantity greater than 0.');
                return false;
            }
        });

        const saleSelect = document.getElementById('saleSelect');
        if (saleSelect && saleSelect.value) {
            handleSaleChange(saleSelect.value);
        }

        // Return Barcode / Serial Scanner
        const returnScanner = document.getElementById('return_barcode_scanner');
        if (returnScanner) {
            returnScanner.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const code = this.value.trim();
                    if (!code) return;

                    fetch(`{{ route('products.barcode_lookup') }}?code=${encodeURIComponent(code)}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.success && data.sale && data.sale.invoice_no) {
                                // Find sale in select dropdown by invoice_no
                                let found = false;
                                for (let opt of saleSelect.options) {
                                    if (opt.text.includes(data.sale.invoice_no)) {
                                        $(saleSelect).val(opt.value).trigger('change');
                                        handleSaleChange(opt.value);
                                        found = true;
                                        break;
                                    }
                                }
                                if (found) {
                                    returnScanner.value = '';
                                } else {
                                    alert(`Sale invoice [${data.sale.invoice_no}] found for Serial [${code}], but not listed in pending sales.`);
                                }
                            } else if (data.success && data.type === 'product') {
                                alert(`Product [${data.product.name}] found. Please select the customer's sale order below.`);
                            } else {
                                alert(data.message || `No sold unit found with serial/barcode [${code}].`);
                            }
                        })
                        .catch(() => alert('Error scanning return barcode.'));
                }
            });
        }
    });
</script>
@endpush
