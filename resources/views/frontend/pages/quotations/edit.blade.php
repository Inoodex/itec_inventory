@extends('frontend.layouts.app')

@section('title', 'Edit Quotation')

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1">Edit Quotation #{{ $quotation->quotation_number }}</h4>
                <p class="text-muted small mb-0">Update price quotation details and product items</p>
            </div>
            <div>
                <a href="{{ route('quotations.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3 shadow-sm">
                    <i class="fa fa-arrow-left me-2"></i>Back to Quotations
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('quotations.update', $quotation->id) }}" method="POST"
                                id="quotation-form">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="client_id" class="form-label">Client <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control select2" name="client_id" id="client_id" required>
                                                <option value="">Select Client</option>
                                                @foreach ($clients as $client)
                                                    <option value="{{ $client->id }}"
                                                        {{ $client->id == $quotation->client_id ? 'selected' : '' }}>
                                                        {{ $client->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="quotation_date" class="form-label">Quotation Date <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="quotation_date"
                                                id="quotation_date"
                                                value="{{ $quotation->quotation_date->format('Y-m-d') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="expiry_date" class="form-label">Expiry Date <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="expiry_date" id="expiry_date"
                                                value="{{ $quotation->expiry_date->format('Y-m-d') }}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="card-title">Products</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-centered" id="products-table">
                                                        <thead>
                                                            <tr>
                                                                <th>Product</th>
                                                                <th width="120">Quantity</th>
                                                                <th width="150">Unit Price</th>
                                                                <th width="150">Total </th>
                                                                <th width="50">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="products-tbody">
                                                            @if ($quotation->items->count() > 0)
                                                                @foreach ($quotation->items as $index => $item)
                                                                    <tr id="product-{{ $index }}">
                                                                        <td>
                                                                            <select class="form-control product-select"
                                                                                name="items[{{ $index }}][product_id]"
                                                                                required>
                                                                                <option value="">Select Product
                                                                                </option>
                                                                                @foreach ($products as $product)
                                                                                    <option value="{{ $product->id }}"
                                                                                        {{ $product->id == $item->product_id ? 'selected' : '' }}
                                                                                        data-price="{{ $product->price ?? 0 }}">
                                                                                        {{ $product->name }} -
                                                                                        {{ $product->brand->name }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <input type="number"
                                                                                class="form-control quantity"
                                                                                name="items[{{ $index }}][quantity]"
                                                                                value="{{ $item->quantity }}"
                                                                                min="1" required>
                                                                        </td>
                                                                        <td>
                                                                            <input type="number"
                                                                                class="form-control unit-price"
                                                                                name="items[{{ $index }}][unit_price]"
                                                                                value="{{ $item->unit_price }}"
                                                                                step="0.01" min="0" required>
                                                                        </td>
                                                                        <td>
                                                                            <span
                                                                                class="product-total">{{ number_format($item->total, 2) }}</span>
                                                                        </td>
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-outline-danger border-0 remove-product"
                                                                                data-row="product-{{ $index }}">
                                                                                <i class="fa fa-times"></i>
                                                                            </button>
                                                                    </tr>
                                                                @endforeach
                                                            @else
                                                                <tr id="no-products">
                                                                    <td colspan="5" class="text-center text-muted">No
                                                                        products added</td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td colspan="3" class="text-end"><strong>Sub
                                                                        Total:</strong></td>
                                                                <td><strong
                                                                        id="sub-total">{{ number_format($quotation->sub_total, 2) }}</strong>
                                                                </td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="3" class="text-end">
                                                                    <strong>Discount:</strong>
                                                                </td>
                                                                <td>
                                                                    <input type="number" class="form-control"
                                                                        name="discount_amount" id="discount-amount"
                                                                        value="{{ $quotation->discount_amount }}"
                                                                        min="0" step="0.01">
                                                                </td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="3" class="text-end"><strong>Total
                                                                        Amount:</strong></td>
                                                                <td><strong
                                                                        id="total-amount">{{ number_format($quotation->total_amount, 2) }}</strong>
                                                                </td>
                                                                <td></td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>

                                                <div class="row mt-3">
                                                    <div class="col-md-8">
                                                        <select class="form-control select2" id="product-select">
                                                            <option value="">Select Product</option>
                                                            @foreach ($products as $product)
                                                                <option value="{{ $product->id }}"
                                                                    data-price="{{ $product->price ?? 0 }}">
                                                                    {{ $product->name }} - {{ $product->brand->name }} -
                                                                    {{ number_format($product->price ?? 0, 2) }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-primary w-100"
                                                            id="add-product">Add Product</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="notes" class="form-label">Notes</label>
                                            <textarea class="form-control" name="notes" id="notes" rows="3" placeholder="Additional notes...">{{ $quotation->notes }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- PDF Print Options -->
                                <div class="card border-0 shadow-sm rounded-3 mb-4">
                                    <div class="card-body p-4">
                                        <h6 class="fw-bold text-dark mb-3"><i class="fe fe-printer me-2 text-primary"></i>PDF Print &amp; Display Options</h6>
                                        <div class="d-flex flex-wrap gap-4">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="show_signature" id="show_signature" value="1" {{ old('show_signature', $quotation->show_signature ?? true) ? 'checked' : '' }}>
                                                <label class="form-check-label fw-semibold text-dark" for="show_signature">
                                                    Include Authorized Signature
                                                </label>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="show_seal" id="show_seal" value="1" {{ old('show_seal', $quotation->show_seal ?? true) ? 'checked' : '' }}>
                                                <label class="form-check-label fw-semibold text-dark" for="show_seal">
                                                    Include Company Seal (Sill)
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-12 text-end">
                                        <button type="submit" class="btn btn-primary">Update Quotation</button>
                                        <a href="{{ route('quotations.index') }}" class="btn btn-secondary">Cancel</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        let productCounter = {{ $quotation->items->count() }};

        document.addEventListener('DOMContentLoaded', function() {
            // Add product button event listener
            document.getElementById('add-product').addEventListener('click', function() {
                addProduct();
            });

            // Initialize discount amount listener
            document.getElementById('discount-amount').addEventListener('input', updateTotals);

            // Initialize event listeners for existing rows
            document.querySelectorAll('#products-tbody tr').forEach(row => {
                if (row.id !== 'no-products') {
                    addRowEventListeners(row.id);
                }
            });
        });

        function addProduct() {
            const productSelect = document.getElementById('product-select');
            const selectedOption = productSelect.options[productSelect.selectedIndex];

            if (!selectedOption || !selectedOption.value) {
                alert('Please select a product');
                return;
            }

            const productId = selectedOption.value;
            const productName = selectedOption.text;
            const unitPrice = selectedOption.getAttribute('data-price') || 0;

            // Remove "no products" message
            const noProducts = document.getElementById('no-products');
            if (noProducts) noProducts.remove();

            // Add product row
            const tbody = document.getElementById('products-tbody');
            const rowId = 'product-' + productCounter;

            // Create options for product select
            let productOptions = '';
            @foreach ($products as $product)
                productOptions +=
                    `<option value="{{ $product->id }}" {{ $product->id == $item->product_id ? 'selected' : '' }} data-price="{{ $product->price ?? 0 }}">{{ $product->name }} - {{ $product->brand->name }}</option>`;
            @endforeach

            const row = document.createElement('tr');
            row.id = rowId;
            row.innerHTML = `
            <td>
                <select class="form-control product-select" name="items[${productCounter}][product_id]" required>
                    <option value="">Select Product</option>
                    ${productOptions}
                </select>
            </td>
            <td>
                <input type="number" class="form-control quantity" name="items[${productCounter}][quantity]" value="1" min="1" required>
            </td>
            <td>
                <input type="number" class="form-control unit-price" name="items[${productCounter}][unit_price]" value="${unitPrice}" step="0.01" min="0" required>
            </td>
            <td>
                <span class="product-total">${parseFloat(unitPrice).toFixed(2)}</span>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger remove-product" data-row="${rowId}">
                    <i class="mdi mdi-delete"></i>
                </button>
            </td>
        `;

            tbody.appendChild(row);
            productCounter++;

            // Reset select
            productSelect.value = '';

            // Update totals
            updateTotals();

            // Add event listeners to new inputs
            addRowEventListeners(rowId);
        }

        function addRowEventListeners(rowId) {
            const row = document.getElementById(rowId);
            if (!row) return;

            const quantityInput = row.querySelector('.quantity');
            const priceInput = row.querySelector('.unit-price');
            const productSelect = row.querySelector('.product-select');

            if (quantityInput && priceInput) {
                quantityInput.addEventListener('input', updateTotals);
                priceInput.addEventListener('input', updateTotals);
            }

            if (productSelect) {
                productSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const price = selectedOption.getAttribute('data-price') || 0;
                    const priceInput = row.querySelector('.unit-price');
                    if (priceInput) {
                        priceInput.value = price;
                        updateTotals();
                    }
                });
            }

            // Remove product button
            const removeBtn = row.querySelector('.remove-product');
            if (removeBtn) {
                removeBtn.addEventListener('click', function() {
                    const rowToRemove = document.getElementById(rowId);
                    if (rowToRemove) {
                        rowToRemove.remove();
                        updateTotals();

                        // Show "no products" message if empty
                        const tbody = document.getElementById('products-tbody');
                        if (tbody && tbody.children.length === 0) {
                            tbody.innerHTML =
                                '<tr id="no-products"><td colspan="5" class="text-center text-muted">No products added</td></tr>';
                        }
                    }
                });
            }
        }

        function updateTotals() {
            let subTotal = 0;
            const tbody = document.getElementById('products-tbody');

            if (!tbody) return;

            document.querySelectorAll('#products-tbody tr').forEach(row => {
                if (row.id !== 'no-products' && row.id) {
                    const quantityInput = row.querySelector('.quantity');
                    const priceInput = row.querySelector('.unit-price');
                    const totalSpan = row.querySelector('.product-total');

                    if (quantityInput && priceInput && totalSpan) {
                        const quantity = parseFloat(quantityInput.value) || 0;
                        const unitPrice = parseFloat(priceInput.value) || 0;
                        const total = quantity * unitPrice;

                        totalSpan.textContent = total.toFixed(2);
                        subTotal += total;
                    }
                }
            });

            const discountInput = document.getElementById('discount-amount');
            const discount = discountInput ? parseFloat(discountInput.value) || 0 : 0;
            const totalAmount = Math.max(0, subTotal - discount);

            const subTotalElement = document.getElementById('sub-total');
            const totalAmountElement = document.getElementById('total-amount');

            if (subTotalElement) subTotalElement.textContent = subTotal.toFixed(2);
            if (totalAmountElement) totalAmountElement.textContent = totalAmount.toFixed(2);
        }
    </script>
@endsection
