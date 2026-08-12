@extends('frontend.layouts.app')

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1">{{ isset($client) ? 'Edit Client' : 'Add New Client' }}</h4>
                <p class="text-muted small mb-0">Update client details, contact phone, email, and billing address</p>
            </div>
            <div>
                <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3 shadow-sm">
                    <i class="fa fa-arrow-left me-2"></i>Back to Clients
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">
            <form action="{{ isset($client) ? route('clients.update', $client->id) : route('clients.store') }}" method="POST">
                @csrf
                @if (isset($client))
                    @method('PUT')
                @endif

                <div class="row g-3 mb-4">
                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control border-light-subtle" value="{{ old('name', $client->name ?? '') }}" placeholder="Enter client full name" required>
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Phone Number <span class="text-danger">*</span></label>
                        <input type="tel" name="phone" class="form-control border-light-subtle" value="{{ old('phone', $client->phone ?? '') }}" placeholder="Enter phone number" required>
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control border-light-subtle" value="{{ old('email', $client->email ?? '') }}" placeholder="Enter email address" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Billing Address <span class="text-danger">*</span></label>
                        <textarea name="address" class="form-control border-light-subtle" rows="3" placeholder="Enter complete address" required>{{ old('address', $client->address ?? '') }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 border-top pt-3">
                    <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary px-4 rounded-3">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 rounded-3">
                        {{ isset($client) ? 'Update Client' : 'Create Client' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
