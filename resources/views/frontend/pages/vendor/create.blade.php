@extends('frontend.layouts.app')

@push('styles')
<style>
    .form-section-title {
        font-size: 15px;
        font-weight: 700;
        color: #2c3038;
        border-bottom: 1px solid #f0f0f5;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }
    .form-control:focus, .form-select:focus {
        border-color: #7638ff;
        box-shadow: 0 0 0 0.2rem rgba(118, 56, 255, 0.15);
    }
</style>
@endpush

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1">Add New Vendor</h4>
                <p class="text-muted small mb-0">Create a new supplier/vendor record for purchase orders and inventory stocking</p>
            </div>
            <div>
                <a href="{{ route('vendors.index') }}" class="btn btn-outline-secondary px-3 py-2 rounded-3 d-inline-flex align-items-center gap-2">
                    <i class="fe fe-arrow-left"></i>
                    <span>Back</span>
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Full Width Form Card -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">

            <form method="POST" action="{{ route('vendors.store') }}">
                @csrf

                <!-- Section Title -->
                <div class="form-section-title">
                    Vendor Information
                </div>

                <div class="row g-3 mb-4">
                    <!-- Full Name -->
                    <div class="col-md-6 col-lg-4">
                        <label class="form-label fw-semibold text-secondary small mb-1">Vendor / Company Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="Enter vendor name" required>
                        @error('name')
                            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Phone Number -->
                    <div class="col-md-6 col-lg-4">
                        <label class="form-label fw-semibold text-secondary small mb-1">Phone Number <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" pattern="[0-9]{11}" maxlength="11" placeholder="Enter 11-digit phone number" required>
                        @error('phone')
                            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email Address -->
                    <div class="col-md-6 col-lg-4">
                        <label class="form-label fw-semibold text-secondary small mb-1">Email Address</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="Enter email address">
                        @error('email')
                            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="col-md-6 col-lg-4">
                        <label class="form-label fw-semibold text-secondary small mb-1">Account Status</label>
                        <select class="form-select" name="status">
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <!-- Address -->
                    <div class="col-12">
                        <label class="form-label fw-semibold text-secondary small mb-1">Office / Warehouse Address <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('address') is-invalid @enderror" name="address" rows="3" placeholder="Enter complete office/warehouse address..." required>{{ old('address') }}</textarea>
                        @error('address')
                            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions Footer -->
                <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top border-light">
                    <a href="{{ route('vendors.index') }}" class="btn btn-light px-4 py-2 rounded-3 text-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm">Save Vendor</button>
                </div>

            </form>

        </div>
    </div>
</div>
@endsection
