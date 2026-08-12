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
    .badge-soft-primary {
        background-color: rgba(118, 56, 255, 0.12) !important;
        color: #7638ff !important;
        font-weight: 600;
    }
    .badge-soft-success {
        background-color: rgba(25, 135, 84, 0.12) !important;
        color: #198754 !important;
        font-weight: 600;
    }
    .badge-soft-info {
        background-color: rgba(13, 202, 240, 0.12) !important;
        color: #0dcaf0 !important;
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
                <h4 class="card-title fw-bold text-dark mb-1">Welcome, {{ auth()->user()->name }}! 👋</h4>
                <p class="text-muted small mb-0">Employee Portal Dashboard & TA/DA Reimbursements</p>
            </div>
            <div>
                <a href="{{ route('employee.tada.create') }}" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2">
                    <i class="fe fe-plus-circle fs-6"></i>
                    <span>Submit TA/DA Request</span>
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Welcome Banner -->
    <div class="card border-0 bg-primary text-white shadow-sm rounded-3 mb-4">
        <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h5 class="fw-bold mb-1">Employee Account Active</h5>
                <p class="mb-0 text-white-50 small">Access your travel allowance requests, daily reimbursements, and status claims from your portal.</p>
            </div>
            <div>
                <a href="{{ route('employee.tada.index') }}" class="btn btn-light text-primary fw-bold px-4 py-2 rounded-3 shadow-sm">
                    My TA/DA List
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    @php
        $emp = auth()->user()->employee;
        $tadas = $emp ? \App\Models\TaDa::where('employee_id', $emp->id)->get() : collect();
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-xl-4 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-primary-light text-primary rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-file-text fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Total TA/DA Requests</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($tadas->count()) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-success-light text-success rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-dollar-sign fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Total Approved Amount</h6>
                        <h4 class="mb-0 fw-bold text-dark">৳{{ number_format($tadas->sum('amount'), 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-info-light text-info rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-check-circle fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Account Status</h6>
                        <h4 class="mb-0 fw-bold text-success">Active Staff</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent TA/DA Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom border-light d-flex justify-content-between align-items-center">
            <h6 class="fw-bold text-dark mb-0"><i class="fe fe-clock me-2 text-primary"></i>My Recent TA/DA Claims</h6>
            <a href="{{ route('employee.tada.index') }}" class="btn btn-sm btn-outline-primary rounded-2 px-3">View All</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary fs-7 text-uppercase">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Payment Type</th>
                            <th class="pe-4 text-end">Purpose</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($tadas->take(5) as $item)
                            <tr>
                                <td class="ps-4 text-muted fw-semibold">{{ $loop->iteration }}</td>
                                <td>
                                    <span class="text-secondary small">{{ \Carbon\Carbon::parse($item->date)->format('d M, Y') }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-soft-info px-3 py-1 rounded-pill fs-7">{{ $item->type }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-soft-primary px-3 py-1 rounded-pill fs-7">৳{{ number_format($item->amount, 2) }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-soft-success px-3 py-1 rounded-pill fs-7">{{ $item->payment_type }}</span>
                                </td>
                                <td class="pe-4 text-end text-muted small">
                                    {{ Str::limit($item->purpose, 30) ?? 'N/A' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted small">
                                    No TA/DA submissions found yet
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
