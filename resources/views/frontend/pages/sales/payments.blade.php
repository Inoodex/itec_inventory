@extends('frontend.layouts.app')

@push('styles')
<style>
    .kpi-card {
        border: 1px solid rgba(0, 0, 0, 0.05) !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.06) !important;
    }
    .table-custom tbody tr {
        transition: background-color 0.15s ease;
    }
    .table-custom tbody tr:hover {
        background-color: #fcfbff !important;
    }
</style>
@endpush

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1">
                    @if (isset($sale))
                        Collect Due Payment - Order #{{ $sale->order_no }}
                    @else
                        Sales Payments History
                    @endif
                </h4>
                <p class="text-muted small mb-0">
                    @if (isset($sale))
                        Customer: <strong class="text-dark">{{ $sale->customer->name ?? 'N/A' }}</strong> ({{ $sale->customer->phone ?? 'N/A' }})
                    @else
                        Overview of customer payments received for sales orders
                    @endif
                </p>
            </div>
            <div>
                <a href="{{ route('due-payments.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3 shadow-sm">
                    <i class="fa fa-arrow-left me-2"></i>Back to Due Payments
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    @if (isset($sale))
        <!-- Section 1: Financial Overview KPI Bar -->
        <div class="row g-3 mb-4">
            <div class="col-xl-2 col-md-4 col-6">
                <div class="card kpi-card bg-white shadow-sm rounded-3 h-100 mb-0">
                    <div class="card-body p-3">
                        <span class="text-muted small d-block mb-1">Total Bill</span>
                        <h5 class="fw-bold text-dark mb-0">৳{{ number_format($sale->bill, 2) }}</h5>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 col-6">
                <div class="card kpi-card bg-white shadow-sm rounded-3 h-100 mb-0">
                    <div class="card-body p-3">
                        <span class="text-muted small d-block mb-1">Discount</span>
                        <h5 class="fw-bold text-info mb-0">৳{{ number_format($sale->discount, 2) }}</h5>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-4 col-6">
                <div class="card kpi-card bg-white shadow-sm rounded-3 h-100 mb-0">
                    <div class="card-body p-3">
                        <span class="text-muted small d-block mb-1">Payable Amount</span>
                        <h5 class="fw-bold text-primary mb-0">৳{{ number_format($sale->payble, 2) }}</h5>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-6">
                <div class="card kpi-card bg-white shadow-sm rounded-3 h-100 mb-0">
                    <div class="card-body p-3">
                        <span class="text-muted small d-block mb-1">Paid Amount</span>
                        <h5 class="fw-bold text-success mb-0">৳{{ number_format($sale->advanced_payment, 2) }}</h5>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-6 col-12">
                <div class="card kpi-card bg-white shadow-sm rounded-3 h-100 mb-0">
                    <div class="card-body p-3">
                        <span class="text-muted small d-block mb-1">Outstanding Due</span>
                        <h5 class="fw-bold text-danger mb-0">৳{{ number_format($sale->due_payment, 2) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Process Payment Form -->
        @if ($sale->due_payment > 0)
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-3"><i class="fe fe-credit-card me-2 text-primary"></i>Receive Payment Entry</h6>

                    <form action="{{ route('sales.process-payment') }}" method="POST">
                        @csrf
                        <input type="hidden" name="sale_id" value="{{ $sale->id }}">

                        <div class="row g-3">
                            <div class="col-lg-4 col-md-6 col-12">
                                <label class="form-label small text-secondary fw-semibold mb-1">Payment Amount <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control border-light-subtle" name="payment_amount" id="payment_amount" max="{{ $sale->due_payment }}" min="0.01" value="{{ $sale->due_payment }}" required oninput="updateRemaining(this.value)">
                            </div>

                            <div class="col-lg-4 col-md-6 col-12">
                                <label class="form-label small text-secondary fw-semibold mb-1">Payment Method <span class="text-danger">*</span></label>
                                <select class="form-select border-light-subtle" name="payment_method" required>
                                    <option value="">Select Payment Method</option>
                                    <option value="cash" selected>Cash</option>
                                    <option value="card">Card</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="bkash">bKash / Mobile Banking</option>
                                </select>
                            </div>

                            <div class="col-lg-4 col-md-12 col-12">
                                <label class="form-label small text-secondary fw-semibold mb-1">Payment Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control border-light-subtle" name="payment_date" value="{{ now()->format('Y-m-d') }}" required>
                            </div>

                            <div class="col-lg-8 col-md-7 col-12">
                                <label class="form-label small text-secondary fw-semibold mb-1">Payment Notes (Optional)</label>
                                <input type="text" class="form-control border-light-subtle" name="notes" placeholder="Add transaction reference or notes..." autocomplete="off">
                            </div>

                            <div class="col-lg-4 col-md-5 col-12">
                                <label class="form-label small text-secondary fw-semibold mb-1">Remaining Due After Payment</label>
                                <div class="p-2 bg-light border rounded-3 text-center">
                                    <h5 class="fw-bold mb-0 text-primary" id="remainingAmount">৳0.00</h5>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-4 mt-3 border-top">
                            <a href="{{ route('due-payments.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4 py-2 rounded-3">Process Payment</button>
                        </div>
                    </form>
                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body p-5 text-center">
                    <div class="avatar avatar-xl bg-success-light text-success rounded-circle mb-3 mx-auto d-flex align-items-center justify-content-center">
                        <i class="fe fe-check-circle fs-1"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Order Fully Paid</h5>
                    <p class="text-muted small mb-0">This sale order has no remaining due balance.</p>
                </div>
            </div>
        @endif
    @endif

</div>
@endsection

@push('scripts')
@if (isset($sale) && $sale->due_payment > 0)
<script>
    function updateRemaining(amount) {
        const dueAmount = {{ $sale->due_payment }};
        const paymentAmount = parseFloat(amount) || 0;
        const remaining = dueAmount - paymentAmount;

        const remainingElement = document.getElementById('remainingAmount');
        if (remainingElement) {
            remainingElement.textContent = '৳' + Math.max(0, remaining).toFixed(2);
            if (remaining <= 0) {
                remainingElement.className = 'fw-bold mb-0 text-success';
            } else {
                remainingElement.className = 'fw-bold mb-0 text-danger';
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateRemaining({{ $sale->due_payment }});
    });
</script>
@endif
@endpush
