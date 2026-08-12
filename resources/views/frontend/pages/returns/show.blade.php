@extends('frontend.layouts.app')

@push('styles')
<style>
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
    .badge-soft-info {
        background-color: rgba(13, 202, 240, 0.12) !important;
        color: #0dcaf0 !important;
        font-weight: 600;
    }
    .badge-soft-warning {
        background-color: rgba(255, 193, 7, 0.15) !important;
        color: #b58105 !important;
        font-weight: 600;
    }
    .badge-soft-secondary {
        background-color: rgba(108, 117, 125, 0.12) !important;
        color: #6c757d !important;
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
                <h4 class="card-title fw-bold text-dark mb-1">Return Details #{{ $return->id }}</h4>
                <p class="text-muted small mb-0">View return status, customer order details, and item refund breakdown</p>
            </div>
            <div>
                <a href="{{ route('returns.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3 shadow-sm">
                    <i class="fa fa-arrow-left me-2"></i>Back to Product Returns
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Status Banner -->
    @php
        $statusConfig = [
            'pending' => ['class' => 'alert-warning text-dark', 'icon' => 'fe-clock', 'msg' => 'This return request is pending approval.'],
            'approved' => ['class' => 'alert-info text-dark', 'icon' => 'fe-check-circle', 'msg' => 'Return approved. Waiting to complete and update inventory stock.'],
            'completed' => ['class' => 'alert-success text-dark', 'icon' => 'fe-check-square', 'msg' => 'Return process completed and inventory stock updated.'],
            'rejected' => ['class' => 'alert-danger text-dark', 'icon' => 'fe-x-circle', 'msg' => 'This return request has been rejected.']
        ][$return->status] ?? ['class' => 'alert-secondary', 'icon' => 'fe-help-circle', 'msg' => 'Unknown status'];
    @endphp

    <div class="alert {{ $statusConfig['class'] }} border-0 shadow-sm rounded-3 d-flex align-items-center p-3 mb-4">
        <i class="fe {{ $statusConfig['icon'] }} fs-4 me-3"></i>
        <div>
            <strong class="d-block">Status: {{ ucfirst($return->status) }}</strong>
            <span class="small">{{ $statusConfig['msg'] }}</span>
        </div>
    </div>

    <!-- Info Cards Row -->
    <div class="row g-4 mb-4">
        <!-- Return Info Card -->
        <div class="col-lg-6 col-12">
            <div class="card border-0 shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-3"><i class="fe fe-rotate-ccw me-2 text-primary"></i>Return Summary</h6>
                    <table class="table table-borderless align-middle mb-0">
                        <tr>
                            <td class="text-secondary small fw-semibold ps-0" style="width: 40%">Return Record ID:</td>
                            <td class="fw-bold text-dark">#{{ $return->id }}</td>
                        </tr>
                        <tr>
                            <td class="text-secondary small fw-semibold ps-0">Return Date:</td>
                            <td class="fw-bold text-dark">{{ $return->return_date ? $return->return_date->format('d M Y') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-secondary small fw-semibold ps-0">Created At:</td>
                            <td class="text-muted">{{ $return->created_at ? $return->created_at->format('d M Y, h:i A') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-secondary small fw-semibold ps-0">Total Refund:</td>
                            <td>
                                <span class="badge badge-soft-danger px-3 py-1 rounded-pill fs-7">
                                    ৳{{ number_format($return->total_refund_amount, 2) }}
                                </span>
                            </td>
                        </tr>
                        @if($return->reason)
                            <tr>
                                <td class="text-secondary small fw-semibold ps-0">General Reason:</td>
                                <td class="text-dark">{{ $return->reason }}</td>
                            </tr>
                        @endif
                        @if($return->notes)
                            <tr>
                                <td class="text-secondary small fw-semibold ps-0">Notes:</td>
                                <td class="text-muted">{{ $return->notes }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Sale & Customer Info Card -->
        <div class="col-lg-6 col-12">
            <div class="card border-0 shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-3"><i class="fe fe-user me-2 text-primary"></i>Sale & Customer Details</h6>
                    <table class="table table-borderless align-middle mb-0">
                        <tr>
                            <td class="text-secondary small fw-semibold ps-0" style="width: 40%">Order Number:</td>
                            <td>
                                @if($return->sale)
                                    <span class="fw-bold text-primary font-monospace">#{{ $return->sale->order_no }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-secondary small fw-semibold ps-0">Customer Name:</td>
                            <td class="fw-bold text-dark">{{ $return->customer->name ?? 'N/A' }}</td>
                        </tr>
                        @if($return->customer && $return->customer->phone)
                            <tr>
                                <td class="text-secondary small fw-semibold ps-0">Phone Number:</td>
                                <td class="text-muted"><i class="fe fe-phone me-1"></i>{{ $return->customer->phone }}</td>
                            </tr>
                        @endif
                        @if($return->processedBy)
                            <tr>
                                <td class="text-secondary small fw-semibold ps-0">Processed By:</td>
                                <td class="fw-semibold text-dark">{{ $return->processedBy->name }}</td>
                            </tr>
                            <tr>
                                <td class="text-secondary small fw-semibold ps-0">Processed Date:</td>
                                <td class="text-muted">{{ $return->processed_at?->format('d M Y, h:i A') }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Returned Items Table Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-4">
            <h6 class="fw-bold text-dark mb-3"><i class="fe fe-box me-2 text-primary"></i>Returned Items Breakdown</h6>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary fs-7 text-uppercase">
                        <tr>
                            <th class="ps-3">#</th>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Subtotal Refund</th>
                            <th>Return Reason</th>
                            <th>Item Condition</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @foreach($return->items as $item)
                            <tr>
                                <td class="ps-3 text-muted fw-semibold">{{ $loop->iteration }}</td>
                                <td>
                                    <span class="fw-bold text-dark d-block">{{ $item->product->name ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-soft-info px-2 py-1 rounded-2">{{ $item->quantity }}</span>
                                </td>
                                <td>৳{{ number_format($item->unit_price, 2) }}</td>
                                <td>
                                    <span class="fw-bold text-danger">৳{{ number_format($item->total_price, 2) }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-soft-secondary px-2 py-1 rounded-2 text-capitalize">
                                        {{ $item->reason_label }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $conditionClass = [
                                            'good' => 'badge-soft-success',
                                            'damaged' => 'badge-soft-warning',
                                            'defective' => 'badge-soft-danger'
                                        ][$item->condition] ?? 'badge-soft-secondary';
                                    @endphp
                                    <span class="badge {{ $conditionClass }} px-2 py-1 rounded-2 text-capitalize">
                                        {{ $item->condition_label }}
                                    </span>
                                </td>
                                <td class="text-muted small">{{ $item->notes ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-top">
                        <tr>
                            <td colspan="4" class="text-end fw-bold text-dark py-3">Total Calculated Refund:</td>
                            <td colspan="4" class="fw-bold text-danger fs-6 py-3">৳{{ number_format($return->total_refund_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Actions Bar Card -->
    @if($return->isPending() || $return->isApproved())
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h6 class="fw-bold text-dark mb-0"><i class="fe fe-settings me-2 text-primary"></i>Available Actions</h6>
                <div class="d-flex gap-2">
                    @if($return->isPending())
                        <form method="POST" action="{{ route('returns.approve', $return->id) }}" class="d-inline" onsubmit="return confirm('Approve this return request?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success px-4 py-2 rounded-3">
                                Approve Return
                            </button>
                        </form>

                        <button type="button" class="btn btn-outline-danger px-4 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            Reject Return
                        </button>
                    @endif

                    @if($return->isApproved())
                        <form method="POST" action="{{ route('returns.complete', $return->id) }}" class="d-inline" onsubmit="return confirm('Complete return and update inventory stock?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-primary px-4 py-2 rounded-3">
                                Complete & Update Stock
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        @if($return->isPending())
            <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow rounded-3">
                        <form method="POST" action="{{ route('returns.reject', $return->id) }}">
                            @csrf
                            @method('PATCH')
                            <div class="modal-header border-bottom">
                                <h5 class="modal-title fw-bold text-dark">Reject Return #{{ $return->id }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="mb-3">
                                    <label class="form-label small text-secondary fw-semibold mb-1">Rejection Reason <span class="text-danger">*</span></label>
                                    <textarea name="reason" class="form-control border-light-subtle" rows="3" placeholder="Enter reason for rejection..." required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer border-top pt-3">
                                <button type="button" class="btn btn-outline-secondary rounded-2 px-3" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-danger rounded-2 px-4">Reject Return</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endif

</div>
@endsection
