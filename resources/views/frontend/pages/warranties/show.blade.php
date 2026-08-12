@extends('frontend.layouts.app')

@push('styles')
<style>
    .badge-soft-success {
        background-color: rgba(25, 135, 84, 0.12) !important;
        color: #198754 !important;
        font-weight: 600;
    }
    .badge-soft-warning {
        background-color: rgba(255, 193, 7, 0.15) !important;
        color: #b58105 !important;
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
    .badge-soft-primary {
        background-color: rgba(118, 56, 255, 0.12) !important;
        color: #7638ff !important;
        font-weight: 600;
    }
    .badge-soft-dark {
        background-color: rgba(33, 37, 41, 0.12) !important;
        color: #212529 !important;
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
                <h4 class="card-title fw-bold text-dark mb-1">Warranty Claim {{ $claim->claim_no }}</h4>
                <p class="text-muted small mb-0">Timeline history, inspection details, and RMA status tracking</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('warranties.index') }}" class="btn btn-outline-secondary px-3 py-2 rounded-3 d-inline-flex align-items-center gap-2">
                    <i class="fe fe-arrow-left"></i>
                    <span>Back</span>
                </a>
                <a href="{{ route('warranties.print', $claim->id) }}" target="_blank" class="btn btn-outline-success px-3 py-2 rounded-3 d-inline-flex align-items-center gap-2">
                    <i class="fe fe-printer"></i>
                    <span>Print Receipt</span>
                </a>
                <button type="button" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                    <i class="fe fe-edit fs-6"></i>
                    <span>Update Status</span>
                </button>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row g-4">
        <!-- Main Details -->
        <div class="col-lg-8">
            <!-- Claim Card -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom border-light d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold text-dark">Claim Information</h5>
                    @php
                        $badgeClasses = [
                            'pending'          => 'badge-soft-warning',
                            'under_inspection' => 'badge-soft-info',
                            'sent_to_vendor'   => 'badge-soft-primary',
                            'repaired'         => 'badge-soft-success',
                            'replaced'         => 'badge-soft-success',
                            'rejected'         => 'badge-soft-danger',
                            'completed'        => 'badge-soft-dark',
                        ];
                    @endphp
                    <span class="badge {{ $badgeClasses[$claim->status] ?? 'bg-secondary' }} px-3 py-2 rounded-pill fs-7">
                        {{ ucfirst(str_replace('_', ' ', $claim->status)) }}
                    </span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="text-muted small fw-semibold">Claim Number</label>
                            <div class="fw-bold fs-5 text-dark">{{ $claim->claim_no }}</div>
                        </div>
                        <div class="col-sm-6">
                            <label class="text-muted small fw-semibold">Claim Date</label>
                            <div class="fw-semibold text-dark">{{ $claim->claim_date->format('d F Y') }}</div>
                        </div>

                        <div class="col-sm-6">
                            <label class="text-muted small fw-semibold">Product Name</label>
                            <div class="fw-bold text-dark">{{ $claim->product?->name }}</div>
                        </div>
                        <div class="col-sm-6">
                            <label class="text-muted small fw-semibold">Serial / IMEI Number</label>
                            <div class="fw-semibold text-dark">{{ $claim->serial_number ?? 'N/A' }}</div>
                        </div>

                        <div class="col-sm-6">
                            <label class="text-muted small fw-semibold">Warranty Expiry Date</label>
                            <div class="fw-semibold {{ $claim->is_valid_warranty ? 'text-success' : 'text-danger' }}">
                                {{ $claim->warranty_expiry_date->format('d F Y') }}
                                @if(!$claim->is_valid_warranty) (Expired) @endif
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="text-muted small fw-semibold">Action Taken</label>
                            <div class="fw-semibold text-capitalize text-dark">{{ $claim->action_taken ?? 'None' }}</div>
                        </div>

                        @if($claim->replacement_serial_number)
                            <div class="col-12">
                                <div class="p-3 bg-light rounded-3 border border-success-subtle">
                                    <strong class="text-success me-2">Replacement Serial:</strong>
                                    <span class="font-monospace text-dark fw-bold">{{ $claim->replacement_serial_number }}</span>
                                </div>
                            </div>
                        @endif

                        <div class="col-12"><hr class="my-2 border-light"></div>

                        <div class="col-12">
                            <label class="text-muted small fw-semibold mb-1">Problem / Issue Description</label>
                            <p class="mb-0 text-dark bg-light p-3 rounded-3">{{ $claim->problem_description }}</p>
                        </div>

                        @if($claim->condition_notes)
                            <div class="col-12">
                                <label class="text-muted small fw-semibold mb-1">Physical Condition Notes</label>
                                <p class="mb-0 text-dark bg-light p-3 rounded-3">{{ $claim->condition_notes }}</p>
                            </div>
                        @endif

                        @if($claim->remarks)
                            <div class="col-12">
                                <label class="text-muted small fw-semibold mb-1">Staff Remarks</label>
                                <p class="mb-0 text-dark bg-light p-3 rounded-3">{{ $claim->remarks }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Activity Log / Timeline Card -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-bottom border-light">
                    <h5 class="card-title mb-0 fw-bold text-dark">Claim Status History & Timeline</h5>
                </div>
                <div class="card-body p-4">
                    <ul class="list-group list-group-flush">
                        @forelse($claim->logs as $log)
                            <li class="list-group-item px-0 py-3 border-bottom border-light">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge {{ $badgeClasses[$log->status] ?? 'bg-secondary' }} px-3 py-1 rounded-pill fs-7">
                                        {{ ucfirst(str_replace('_', ' ', $log->status)) }}
                                    </span>
                                    <small class="text-muted">{{ $log->created_at->format('d M Y, h:i A') }}</small>
                                </div>
                                <div class="fw-semibold text-dark">{{ $log->note }}</div>
                                <small class="text-muted fs-8">Updated by: {{ $log->user?->name ?? 'System' }}</small>
                            </li>
                        @empty
                            <li class="list-group-item text-center py-4 text-muted border-0">No timeline history recorded.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <!-- Customer Card -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom border-light">
                    <h5 class="card-title mb-0 fw-bold text-dark">Customer Details</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="text-muted small fw-semibold">Customer Name</label>
                        <div class="fw-bold text-dark fs-6">{{ $claim->customer?->name ?? 'Guest Customer' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small fw-semibold">Phone Number</label>
                        <div class="fw-semibold text-dark">{{ $claim->customer?->phone ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-0">
                        <label class="text-muted small fw-semibold">Address</label>
                        <div class="text-secondary small">{{ $claim->customer?->address ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <!-- Sale Card -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-bottom border-light">
                    <h5 class="card-title mb-0 fw-bold text-dark">Sale Invoice Details</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="text-muted small fw-semibold">Invoice Number</label>
                        <div class="fw-bold text-primary fs-6">{{ $claim->sale?->order_no ?? '#' . $claim->sale_id }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small fw-semibold">Sale Date</label>
                        <div class="fw-semibold text-dark">{{ $claim->sale?->created_at?->format('d M Y') }}</div>
                    </div>
                    <div class="mb-0">
                        <label class="text-muted small fw-semibold">Received By Staff</label>
                        <div class="fw-semibold text-dark">{{ $claim->receiver?->name ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('warranties.update', $claim->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header bg-light py-3 border-bottom">
                    <h5 class="modal-title fw-bold text-dark">Update Status - {{ $claim->claim_no }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="pending" {{ $claim->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="under_inspection" {{ $claim->status == 'under_inspection' ? 'selected' : '' }}>Under Inspection</option>
                            <option value="sent_to_vendor" {{ $claim->status == 'sent_to_vendor' ? 'selected' : '' }}>Sent to Vendor</option>
                            <option value="repaired" {{ $claim->status == 'repaired' ? 'selected' : '' }}>Repaired</option>
                            <option value="replaced" {{ $claim->status == 'replaced' ? 'selected' : '' }}>Replaced</option>
                            <option value="rejected" {{ $claim->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="completed" {{ $claim->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Action Taken</label>
                        <input type="text" name="action_taken" value="{{ old('action_taken', $claim->action_taken) }}"
                            class="form-control" placeholder="e.g. Sent to Official Asus Center / Replaced Display">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Replacement Serial Number (If Replaced)</label>
                        <input type="text" name="replacement_serial_number" value="{{ old('replacement_serial_number', $claim->replacement_serial_number) }}"
                            class="form-control" placeholder="Enter new serial number for replaced item">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Update Note / Log Remarks <span class="text-danger">*</span></label>
                        <textarea name="note" rows="3" class="form-control" required
                            placeholder="Enter notes explaining this status update...">{{ old('note') }}</textarea>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2 p-3 border-top bg-light">
                    <button type="button" class="btn btn-light px-4 rounded-3 text-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 rounded-3 shadow-sm">Save Status Update</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
