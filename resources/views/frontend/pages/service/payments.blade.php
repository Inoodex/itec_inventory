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
    .table-custom tbody tr:hover {
        background-color: #fcfbff !important;
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
    .badge-soft-warning {
        background-color: rgba(255, 193, 7, 0.15) !important;
        color: #b58105 !important;
        font-weight: 600;
    }
    .badge-soft-primary {
        background-color: rgba(118, 56, 255, 0.12) !important;
        color: #7638ff !important;
        font-weight: 600;
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
                    Service Payments 
                    @if(isset($service))
                        <span class="text-muted fs-6 font-normal">#{{ $service->name }}</span>
                    @endif
                </h4>
                <p class="text-muted small mb-0">Manage payment transactions and record due collections</p>
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

    @if (isset($service))
        <!-- Service Financial Summary Card -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3 border-bottom border-light">
                <h6 class="mb-0 fw-bold text-dark"><i class="fe fe-user me-2 text-primary"></i>Customer & Service Summary</h6>
            </div>
            <div class="card-body">
                <div class="row g-3 align-items-center">
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded-3">
                            <span class="text-muted small d-block mb-1">Customer Name & Contact</span>
                            <span class="fw-bold text-dark d-block">{{ $service->name }}</span>
                            <small class="text-muted"><i class="fe fe-phone me-1"></i>{{ $service->phone ?? 'N/A' }}</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded-3">
                            <span class="text-muted small d-block mb-1">Total Bill</span>
                            <span class="badge badge-soft-primary px-3 py-2 rounded-pill fs-6">
                                ৳{{ number_format($service->bill, 2) }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded-3">
                            <span class="text-muted small d-block mb-1">Paid Amount</span>
                            <span class="badge badge-soft-success px-3 py-2 rounded-pill fs-6">
                                ৳{{ number_format($service->paid_amount, 2) }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded-3">
                            <span class="text-muted small d-block mb-1">Outstanding Due</span>
                            @if ($service->due_amount > 0)
                                <span class="badge badge-soft-danger px-3 py-2 rounded-pill fs-6">
                                    ৳{{ number_format($service->due_amount, 2) }}
                                </span>
                            @else
                                <span class="badge badge-soft-success px-3 py-2 rounded-pill fs-6">
                                    0.00 (Fully Paid)
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                @if ($service->remarks)
                    <div class="alert bg-primary-light text-primary border-0 rounded-3 mt-3 mb-0">
                        <strong><i class="fe fe-file-text me-1"></i>Service Remarks:</strong> {{ $service->remarks }}
                    </div>
                @endif

                @if ($service->due_amount > 0)
                    <hr class="my-4 opacity-50">
                    <h6 class="fw-bold text-dark mb-3"><i class="fe fe-plus-circle me-2 text-success"></i>Collect Payment</h6>
                    <form method="POST" action="{{ route('add.payment') }}" class="row g-3 align-items-end">
                        @csrf
                        <input type="hidden" name="id" value="{{ $service->id }}" />
                        <input type="hidden" name="payment_for" value="1" />
                        
                        <div class="col-md-4">
                            <label class="form-label small text-secondary fw-semibold mb-1">Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_method_id" class="form-select border-light-subtle" required>
                                <option value="">Select Method</option>
                                @foreach (paymentMethods() as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-secondary fw-semibold mb-1">Amount <span class="text-danger">*</span></label>
                            <input type="number" name="amount" step="0.01" min="0.01" max="{{ max(0, $service->due_amount) }}" class="form-control border-light-subtle" placeholder="Enter amount" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-secondary fw-semibold mb-1">Remarks</label>
                            <input type="text" name="remarks" class="form-control border-light-subtle" placeholder="Add payment remarks...">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-success w-100 rounded-3 py-2">Submit Payment</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    @endif

    <!-- Payment Transactions Table Card -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom border-light">
            <h6 class="mb-0 fw-bold text-dark"><i class="fe fe-list me-2 text-primary"></i>Payment History</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-custom align-middle mb-0">
                    <thead class="bg-light text-secondary fs-7 text-uppercase">
                        <tr>
                            <th class="ps-4" style="width: 60px;">#</th>
                            <th>Date</th>
                            <th>Payment Method</th>
                            <th>Amount</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($payments as $payment)
                            <tr>
                                <td class="ps-4 text-muted fw-semibold">{{ $loop->iteration }}</td>
                                <td>
                                    <span class="text-secondary small">
                                        {{ $payment->created_at ? $payment->created_at->format('d M Y, h:i A') : 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-soft-info px-3 py-1 rounded-pill text-capitalize">
                                        {{ getArrayData(paymentMethods(), $payment->payment_method) ?: ($payment->payment_method == 'cash' ? 'Cash' : $payment->payment_method) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">
                                        ৳{{ number_format($payment->amount, 2) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-muted small">
                                        {{ $payment->remarks ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No payment records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
