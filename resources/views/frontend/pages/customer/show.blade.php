@extends('frontend.layouts.app')

@push('styles')
<style>
    .avatar-initial-lg {
        width: 64px;
        height: 64px;
        background: linear-gradient(135deg, #7638ff 0%, #9a65ff 100%);
        color: #ffffff;
        font-weight: 700;
        font-size: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        box-shadow: 0 4px 12px rgba(118, 56, 255, 0.25);
    }
    .badge-soft-success {
        background-color: rgba(25, 135, 84, 0.12) !important;
        color: #198754 !important;
        font-weight: 600;
    }
    .badge-soft-danger {
        background-color: rgba(220, 53, 69, 0.12) !important;
        color: #dc3545 !important;
        font-weight: 600;
    }
    .stat-card-mini {
        border: 1px solid rgba(0, 0, 0, 0.05) !important;
    }
</style>
@endpush

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1">Customer Profile</h4>
                <p class="text-muted small mb-0">Detailed customer information and purchase transaction history</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary px-3 py-2 rounded-3 d-inline-flex align-items-center gap-2">
                    <i class="fe fe-arrow-left"></i>
                    <span>Back</span>
                </a>
                <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-primary px-3 py-2 rounded-3 d-inline-flex align-items-center gap-2 shadow-sm">
                    <i class="fe fe-edit"></i>
                    <span>Edit Customer</span>
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    @php
        $isActive = in_array($customer->status, ['active', '1', 1]);
        $totalOrders = $sales->count();
        $totalSpent = $sales->sum('payable_amount');
        $dueAmount = $sales->sum('due_amount');
    @endphp

    <!-- Customer Summary Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center g-4">
                <div class="col-md-6 col-lg-4">
                    <div>
                        <h4 class="fw-bold text-dark mb-1">{{ $customer->name }}</h4>
                        <div class="d-flex align-items-center gap-2">
                            @if ($isActive)
                                <span class="badge badge-soft-success px-3 py-1 rounded-pill fs-7">
                                    <i class="fe fe-check-circle me-1"></i> Active Customer
                                </span>
                            @else
                                <span class="badge badge-soft-danger px-3 py-1 rounded-pill fs-7">
                                    <i class="fe fe-x-circle me-1"></i> Inactive
                                </span>
                            @endif
                            <span class="text-muted small">ID #CUST-{{ str_pad($customer->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-8 border-start border-light ps-lg-4">
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <div class="p-3 bg-light rounded-3 text-center stat-card-mini">
                                <small class="text-muted text-uppercase fw-semibold fs-7 d-block mb-1">Phone Number</small>
                                <span class="fw-bold text-dark">{{ $customer->phone }}</span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 bg-light rounded-3 text-center stat-card-mini">
                                <small class="text-muted text-uppercase fw-semibold fs-7 d-block mb-1">Email Address</small>
                                <span class="fw-bold text-dark text-truncate d-block" title="{{ $customer->email }}">{{ $customer->email ?: 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 bg-light rounded-3 text-center stat-card-mini">
                                <small class="text-muted text-uppercase fw-semibold fs-7 d-block mb-1">Customer Since</small>
                                <span class="fw-bold text-dark">{{ $customer->created_at?->format('d M Y') ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address Row -->
            <div class="row mt-4 pt-3 border-top border-light">
                <div class="col-12">
                    <span class="fw-semibold text-secondary small d-block mb-1">Billing & Delivery Address:</span>
                    <p class="text-dark mb-0">{{ $customer->address }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales & Financial Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white mb-0">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="avatar avatar-md bg-primary-light text-primary rounded-circle me-3 d-flex align-items-center justify-content-center">
                        <i class="fe fe-shopping-bag fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Total Orders</small>
                        <h5 class="fw-bold text-dark mb-0">{{ number_format($totalOrders) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white mb-0">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="avatar avatar-md bg-success-light text-success rounded-circle me-3 d-flex align-items-center justify-content-center">
                        <i class="fe fe-dollar-sign fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Total Purchased Amount</small>
                        <h5 class="fw-bold text-dark mb-0">৳{{ number_format($totalSpent, 2) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white mb-0">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="avatar avatar-md bg-danger-light text-danger rounded-circle me-3 d-flex align-items-center justify-content-center">
                        <i class="fe fe-alert-circle fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Outstanding Due</small>
                        <h5 class="fw-bold text-dark mb-0">৳{{ number_format($dueAmount, 2) }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Order History Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom border-light d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-dark mb-0">Sales & Order History</h5>
            <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill">{{ $totalOrders }} Transactions</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary fs-7 text-uppercase">
                        <tr>
                            <th class="ps-4">Order #</th>
                            <th>Date</th>
                            <th>Payable Amount</th>
                            <th>Paid Amount</th>
                            <th>Due Amount</th>
                            <th>Payment Status</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                            <tr>
                                <td class="ps-4 fw-bold text-dark">
                                    <a href="{{ route('sales.invoice', $sale->id) }}" class="text-primary text-decoration-none">
                                        #{{ $sale->order_no ?? $sale->id }}
                                    </a>
                                </td>
                                <td>{{ $sale->created_at?->format('d M Y, h:i A') }}</td>
                                <td class="fw-bold text-dark">৳{{ number_format($sale->payable_amount ?? 0, 2) }}</td>
                                <td class="text-success">৳{{ number_format($sale->advanced_payment ?? 0, 2) }}</td>
                                <td class="text-danger">৳{{ number_format($sale->due_amount ?? 0, 2) }}</td>
                                <td>
                                    @if(($sale->due_amount ?? 0) <= 0)
                                        <span class="badge badge-soft-success px-3 py-1 rounded-pill">Paid</span>
                                    @elseif(($sale->advanced_payment ?? 0) > 0)
                                        <span class="badge bg-warning-light text-warning px-3 py-1 rounded-pill">Partial</span>
                                    @else
                                        <span class="badge badge-soft-danger px-3 py-1 rounded-pill">Due</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('sales.invoice', $sale->id) }}" class="btn btn-sm btn-light border rounded-2 px-3">
                                        View Invoice
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fe fe-shopping-bag fs-1 mb-2 text-secondary d-block"></i>
                                    <span>No sales transactions recorded for this customer yet.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
