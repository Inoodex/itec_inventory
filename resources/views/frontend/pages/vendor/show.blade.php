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
    .stat-card-mini {
        border: 1px solid rgba(0, 0, 0, 0.05) !important;
    }
</style>
@endpush

@section('content')
<div class="content container-fluid">

    @php
        $vData = $vendor ?? $customer;
        $isActive = in_array($vData->status, ['active', '1', 1]);
        $totalPurchases = $purchases->count();
        $totalSpent = $purchases->sum('total_amount');
    @endphp

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1">Vendor Profile</h4>
                <p class="text-muted small mb-0">Detailed supplier contact profile and purchase order history</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('vendors.index') }}" class="btn btn-outline-secondary px-3 py-2 rounded-3 d-inline-flex align-items-center gap-2">
                    <i class="fe fe-arrow-left"></i>
                    <span>Back</span>
                </a>
                <a href="{{ route('vendors.edit', $vData->id) }}" class="btn btn-primary px-3 py-2 rounded-3 d-inline-flex align-items-center gap-2 shadow-sm">
                    <i class="fe fe-edit"></i>
                    <span>Edit Vendor</span>
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Vendor Summary Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center g-4">
                <div class="col-md-6 col-lg-4">
                    <div>
                        <h4 class="fw-bold text-dark mb-1">{{ $vData->name }}</h4>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            @if ($isActive)
                                <span class="badge badge-soft-success px-3 py-1 rounded-pill fs-7">
                                    <i class="fe fe-check-circle me-1"></i> Active Vendor
                                </span>
                            @else
                                <span class="badge badge-soft-danger px-3 py-1 rounded-pill fs-7">
                                    <i class="fe fe-x-circle me-1"></i> Inactive
                                </span>
                            @endif
                            <span class="text-muted small">ID #VND-{{ str_pad($vData->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-8 border-start border-light ps-lg-4">
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <div class="p-3 bg-light rounded-3 text-center stat-card-mini">
                                <small class="text-muted text-uppercase fw-semibold fs-7 d-block mb-1">Phone Number</small>
                                <span class="fw-bold text-dark">{{ $vData->phone }}</span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 bg-light rounded-3 text-center stat-card-mini">
                                <small class="text-muted text-uppercase fw-semibold fs-7 d-block mb-1">Email Address</small>
                                <span class="fw-bold text-dark text-truncate d-block" title="{{ $vData->email }}">{{ $vData->email ?: 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 bg-light rounded-3 text-center stat-card-mini">
                                <small class="text-muted text-uppercase fw-semibold fs-7 d-block mb-1">Vendor Since</small>
                                <span class="fw-bold text-dark">{{ $vData->created_at?->format('d M Y') ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address Row -->
            <div class="row mt-4 pt-3 border-top border-light">
                <div class="col-12">
                    <span class="fw-semibold text-secondary small d-block mb-1">Office / Warehouse Address:</span>
                    <p class="text-dark mb-0">{{ $vData->address }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 bg-white mb-0">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="avatar avatar-md bg-primary-light text-primary rounded-circle me-3 d-flex align-items-center justify-content-center">
                        <i class="fe fe-truck fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Total Purchase Orders</small>
                        <h5 class="fw-bold text-dark mb-0">{{ number_format($totalPurchases) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 bg-white mb-0">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="avatar avatar-md bg-success-light text-success rounded-circle me-3 d-flex align-items-center justify-content-center">
                        <i class="fe fe-dollar-sign fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Total Purchased Volume</small>
                        <h5 class="fw-bold text-dark mb-0">৳{{ number_format($totalSpent, 2) }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase Order History Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom border-light d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-dark mb-0">Vendor Purchase History</h5>
            <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill">{{ $totalPurchases }} Procurement Orders</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary fs-7 text-uppercase">
                        <tr>
                            <th class="ps-4">PO Number #</th>
                            <th>Date</th>
                            <th>Total Amount</th>
                            <th class="text-end pe-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases as $purchase)
                            <tr>
                                <td class="ps-4 fw-bold text-dark">
                                    #PO-{{ $purchase->id }}
                                </td>
                                <td>{{ $purchase->created_at?->format('d M Y, h:i A') }}</td>
                                <td class="fw-bold text-dark">৳{{ number_format($purchase->total_amount ?? 0, 2) }}</td>
                                <td class="text-end pe-4">
                                    <span class="badge badge-soft-success px-3 py-1 rounded-pill">Completed</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="fe fe-truck fs-1 mb-2 text-secondary d-block"></i>
                                    <span>No purchase order transactions recorded for this vendor yet.</span>
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
