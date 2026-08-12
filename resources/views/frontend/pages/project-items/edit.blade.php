@extends('frontend.layouts.app')
@section('content')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        .content.container-fluid .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
            border-color: transparent transparent #888 transparent !important;
            border-width: 0 !important;
        }

        .content.container-fluid .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #888 transparent transparent transparent !important;
            border-style: solid;
            border-width: 0 !important;
            height: 0;
            left: 50%;
            margin-left: -4px;
            margin-top: -2px;
            position: absolute;
            top: 50%;
            width: 0;
        }

        .content.container-fluid .card select,
        .content.container-fluid .card input {
            border-color: #000 !important;
            border-width: 1px;
            box-shadow: none !important;
        }

        .content.container-fluid .card label {
            color: #000 !important;
            font-weight: 500;
        }

        .content.container-fluid .select2-container--default .select2-selection--single {
            background-color: #fff !important;
            border: 1px solid #000 !important;
            border-radius: 4px;
            height: 30px !important;
            display: flex;
            align-items: center;
            width: 100% !important;
            padding-left: 8px;
        }

        .content.container-fluid .select2-selection__rendered {
            line-height: 30px !important;
        }

        .content.container-fluid .select2-container {
            width: 100% !important;
        }

        .content.container-fluid .card {
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .content.container-fluid .card-body {
            padding: 20px;
        }

        .content.container-fluid .content-page-header h6 {
            color: #198754;
            font-weight: 600;
            margin-bottom: 15px;
        }
    </style>

    {{-- Debug info
<div class="alert alert-info">
    <strong>Debug Info:</strong><br>
    Project Item ID: {{ $projectItem->id }}<br>
    Current Product ID: {{ $projectItem->product_id }}<br>
    Current Product Name: {{ $projectItem->product->name ?? 'N/A' }}<br>
    Form Action: {{ route('project-items.update', $projectItem->id) }}
</div> --}}

    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header mb-4">
            <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h4 class="card-title fw-bold text-dark mb-1">Edit Project Item</h4>
                    <p class="text-muted small mb-0">Update item details, quantity, unit price, and project allocation</p>
                </div>
                <div>
                    <a href="{{ route('project-items.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3 shadow-sm">
                        <i class="fa fa-arrow-left me-2"></i>Back to Project Items
                    </a>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('project-items.update', $projectItem->id) }}" method="POST"
                            id="projectItemForm">
                            @csrf
                            @method('PUT')

                            <div class="row mb-4">
                                <div class="col-md-2">
                                    <label class="form-label">Project *</label>
                                    <input type="hidden" name="project_id" value="{{ $projectItem->project_id }}">
                                    <select class="form-control" disabled readonly>
                                        <option value="{{ $projectItem->project_id }}" selected>
                                            {{ $projectItem->project->project_name ?? 'N/A' }}
                                        </option>
                                    </select>

                                </div>

                                <!-- Add hidden product_id field -->
                                <input type="hidden" name="product_id" value="{{ $projectItem->product_id }}">
                            </div>

                            <!-- Rest of your form remains the same -->
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">Edit Item</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row align-items-end">
                                        <div class="col-md-4">
                                            <label>Product Details</label>
                                            <p class="mb-1"><strong>{{ $projectItem->product->name ?? 'N/A' }}</strong>
                                            </p>
                                            <p class="mb-1 text-muted small">Model:
                                                {{ $projectItem->product->model ?? 'N/A' }}</p>

                                        </div>

                                        <div class="col-md-2">
                                            <label>Unit Price *</label>
                                            <input type="number" name="unit_price" id="unit_price"
                                                class="form-control calculation-input"
                                                value="{{ number_format($projectItem->unit_price, 2, '.', '') }}"
                                                step="0.01" min="0" required>
                                        </div>

                                        <div class="col-md-2">
                                            <label>Quantity *</label>
                                            <input type="number" name="quantity" id="quantity"
                                                class="form-control calculation-input" value="{{ $projectItem->quantity }}"
                                                min="1" required>
                                        </div>

                                        <div class="col-md-2">
                                            <label>Total Price</label>
                                            <input type="number" name="total_price" id="total_price" class="form-control"
                                                value="{{ number_format($projectItem->total_price, 2, '.', '') }}"
                                                step="0.01" readonly>
                                        </div>
                                        <div class="col-md-2">
                                            <label>&nbsp;</label>
                                            <button type="submit" class="btn btn-success w-100">Update Item</button>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    console.log('Document ready - initializing auto-calculation...');
    
    // Initialize Select2 if needed
    $('.js-example-basic-single').select2({
        placeholder: "Select Product",
        allowClear: true
    });

    // Initialize auto-calculation
    initializeAutoCalculation();
    
    // Calculate initial total on page load
    setTimeout(function() {
        calculateTotal();
    }, 100);
});

function initializeAutoCalculation() {
    console.log('Initializing auto-calculation event listeners...');
    
    // Get the input fields
    const unitPriceInput = document.getElementById('unit_price');
    const quantityInput = document.getElementById('quantity');
    
    if (!unitPriceInput || !quantityInput) {
        console.error('Required input fields not found!');
        return;
    }
    
    console.log('Unit Price Input:', unitPriceInput);
    console.log('Quantity Input:', quantityInput);
    
    // Remove any existing event listeners first
    unitPriceInput.removeEventListener('input', calculateTotal);
    quantityInput.removeEventListener('input', calculateTotal);
    unitPriceInput.removeEventListener('change', calculateTotal);
    quantityInput.removeEventListener('change', calculateTotal);
    unitPriceInput.removeEventListener('keyup', calculateTotal);
    quantityInput.removeEventListener('keyup', calculateTotal);
    
    // Add multiple event listeners to ensure it triggers
    unitPriceInput.addEventListener('input', calculateTotal);
    quantityInput.addEventListener('input', calculateTotal);
    unitPriceInput.addEventListener('change', calculateTotal);
    quantityInput.addEventListener('change', calculateTotal);
    unitPriceInput.addEventListener('keyup', calculateTotal);
    quantityInput.addEventListener('keyup', calculateTotal);
    
    // Also trigger on paste events
    unitPriceInput.addEventListener('paste', function() {
        setTimeout(calculateTotal, 100);
    });
    quantityInput.addEventListener('paste', function() {
        setTimeout(calculateTotal, 100);
    });
    
    console.log('Event listeners attached successfully');
}

function calculateTotal() {
    console.log('calculateTotal function called');
    
    try {
        // Get current values
        const unitPrice = parseFloat(document.getElementById('unit_price').value) || 0;
        const quantity = parseInt(document.getElementById('quantity').value) || 0;
        
        console.log('Unit Price:', unitPrice, 'Quantity:', quantity);
        
        // Calculate total
        const total = unitPrice * quantity;
        console.log('Calculated Total:', total);
        
        // Update total price field
        const totalPriceInput = document.getElementById('total_price');
        if (totalPriceInput) {
            totalPriceInput.value = total.toFixed(2);
            console.log('Total price field updated to:', total.toFixed(2));
        }
        
        // Update summary fields
        const subTotalInput = document.getElementById('subTotal');
        const grandTotalInput = document.getElementById('grandTotal');
        
        if (subTotalInput) {
            subTotalInput.value = total.toFixed(2);
            console.log('SubTotal updated to:', total.toFixed(2));
        }
        
        if (grandTotalInput) {
            grandTotalInput.value = total.toFixed(2);
            console.log('GrandTotal updated to:', total.toFixed(2));
        }
        
        // Highlight changes
        highlightTotalChange(total);
        
    } catch (error) {
        console.error('Error in calculateTotal:', error);
    }
}

function highlightTotalChange(newTotal) {
    const originalTotal = {{ $projectItem->total_price }};
    const totalInput = document.getElementById('total_price');
    
    if (totalInput && Math.abs(newTotal - originalTotal) > 0.01) {
        totalInput.classList.add('calculation-highlight');
        console.log('Total changed - highlighting');
    } else if (totalInput) {
        totalInput.classList.remove('calculation-highlight');
    }
}

// Manual test function (can be removed in production)
function testCalculation() {
    console.log('=== MANUAL TEST ===');
    console.log('Unit Price value:', document.getElementById('unit_price').value);
    console.log('Quantity value:', document.getElementById('quantity').value);
    calculateTotal();
}

// Add test button for debugging
setTimeout(function() {
    const testButton = document.createElement('button');
    testButton.type = 'button';
    testButton.className = 'btn btn-warning btn-sm mb-3';
    testButton.innerHTML = '🔧 Test Calculation';
    testButton.onclick = testCalculation;
    document.querySelector('.card-body').prepend(testButton);
}, 500);
</script> --}}

    <script>
        // Simple auto-calculation that definitely works
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Simple calculator loaded');

            const unitPrice = document.getElementById('unit_price');
            const quantity = document.getElementById('quantity');
            const totalPrice = document.getElementById('total_price');
            const subTotal = document.getElementById('subTotal');
            const grandTotal = document.getElementById('grandTotal');

            function simpleCalculate() {
                const up = parseFloat(unitPrice.value) || 0;
                const qty = parseInt(quantity.value) || 0;
                const total = up * qty;

                console.log('Simple Calc:', up, 'x', qty, '=', total);

                if (totalPrice) totalPrice.value = total.toFixed(2);
                if (subTotal) subTotal.value = total.toFixed(2);
                if (grandTotal) grandTotal.value = total.toFixed(2);
            }

            // Add event listeners
            if (unitPrice && quantity) {
                unitPrice.addEventListener('input', simpleCalculate);
                quantity.addEventListener('input', simpleCalculate);
                unitPrice.addEventListener('change', simpleCalculate);
                quantity.addEventListener('change', simpleCalculate);

                // Initial calculation
                setTimeout(simpleCalculate, 100);
            }
        });
    </script>

    <style>
        .calculation-highlight {
            background-color: #fff3cd !important;
            border-color: #ffc107 !important;
            font-weight: bold;
        }

        /* Make readonly fields visually distinct */
        .form-control[disabled] {
            background-color: #f8f9fa !important;
            cursor: not-allowed;
        }
    </style>
@endsection
