@extends('frontend.layouts.app')

@push('styles')
<style>
    .btn-action-icon {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #dbe2ea !important;
        border-radius: 8px !important;
        background-color: #ffffff !important;
        color: #555e6d !important;
        padding: 0;
        transition: all 0.2s ease;
    }
    .btn-action-icon:hover {
        background-color: #7638ff !important;
        color: #ffffff !important;
        border-color: #7638ff !important;
    }
    .dropdown-menu {
        z-index: 9999 !important;
    }
</style>
@endpush

@section('content')
<div class="content container-fluid">

    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title font-weight-bold" style="color: #1e293b;">Fiscal Years & Year-End Closing</h3>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newFiscalYearModal">
                    <i class="fas fa-plus-circle me-1"></i> New Fiscal Year
                </button>
            </div>
        </div>
    </div>

    <!-- Fiscal Years List -->
    <div class="card shadow-sm border-0" style="border-radius: 12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color: #1e293b; color: #ffffff; font-size: 11px; text-transform: uppercase;">
                        <tr>
                            <th class="ps-3">Fiscal Year Name</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th class="text-center">Status</th>
                            <th>Closed Info</th>
                            <th class="text-end pe-4" style="width: 80px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fiscalYears as $fy)
                            <tr>
                                <td class="fw-bold text-dark fs-6 ps-3">{{ $fy->year_name }}</td>
                                <td>{{ \Carbon\Carbon::parse($fy->start_date)->format('d M, Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($fy->end_date)->format('d M, Y') }}</td>
                                <td class="text-center">
                                    @if($fy->is_active && !$fy->is_closed)
                                        <span class="badge bg-success px-3 py-2">ACTIVE PERIOD</span>
                                    @elseif($fy->is_closed)
                                        <span class="badge bg-secondary px-3 py-2">CLOSED & LOCKED</span>
                                    @else
                                        <span class="badge bg-light text-dark border">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    @if($fy->is_closed)
                                        <small class="text-muted">Closed {{ $fy->closed_at ? \Carbon\Carbon::parse($fy->closed_at)->format('d M, Y') : '' }} by {{ $fy->closer->name ?? 'Admin' }}</small>
                                    @else
                                        <span class="text-success"><i class="fas fa-unlock me-1"></i> Open for Transactions</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="dropdown">
                                        <a href="javascript:void(0)" class="btn-action-icon shadow-none" data-bs-toggle="dropdown" data-bs-popper-config='{"strategy":"fixed"}' aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                            @if(!$fy->is_active && !$fy->is_closed)
                                                <li>
                                                    <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="javascript:void(0)"
                                                        onclick="document.getElementById('setActiveForm{{ $fy->id }}').submit();">
                                                        <i class="fe fe-check-circle text-primary"></i>
                                                        <span>Set Active</span>
                                                    </a>
                                                    <form id="setActiveForm{{ $fy->id }}" method="POST" action="{{ route('fiscal-years.set-active', $fy->id) }}" class="d-none">
                                                        @csrf
                                                    </form>
                                                </li>
                                            @endif

                                            @if(!$fy->is_closed)
                                                @if(!$fy->is_active)
                                                    <li><hr class="dropdown-divider opacity-50"></li>
                                                @endif
                                                <li>
                                                    <a class="dropdown-item py-2 d-flex align-items-center gap-2 text-danger" href="javascript:void(0)"
                                                        onclick="if(confirm('WARNING: Year-end closing will zero out all Revenue and Expense accounts into Retained Earnings and lock the period. Are you sure?')) { document.getElementById('closeYearForm{{ $fy->id }}').submit(); }">
                                                        <i class="fe fe-lock text-danger"></i>
                                                        <span>Close Fiscal Year</span>
                                                    </a>
                                                    <form id="closeYearForm{{ $fy->id }}" method="POST" action="{{ route('fiscal-years.close', $fy->id) }}" class="d-none">
                                                        @csrf
                                                    </form>
                                                </li>
                                            @else
                                                <li>
                                                    <span class="dropdown-item py-2 text-muted small"><i class="fe fe-check me-1"></i> Year Closed</span>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No fiscal periods configured.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- New Fiscal Year Modal -->
<div class="modal fade" id="newFiscalYearModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('fiscal-years.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold">Create New Fiscal Year</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Fiscal Year Name <span class="text-danger">*</span></label>
                        <input type="text" name="year_name" class="form-control" placeholder="e.g. 2027-2028, FY-2027" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Period Start Date <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Period End Date <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Fiscal Year</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
