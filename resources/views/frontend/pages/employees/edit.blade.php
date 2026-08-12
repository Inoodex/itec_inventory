@extends('frontend.layouts.app')

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1">Edit Employee</h4>
                <p class="text-muted small mb-0">Update staff details, designation, contact info, and basic salary</p>
            </div>
            <div>
                <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3 shadow-sm">
                    <i class="fa fa-arrow-left me-2"></i>Back to Employees
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">
            <form action="{{ route('employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Employee ID <span class="text-danger">*</span></label>
                        <input type="text" name="employee_id" value="{{ $employee->employee_id }}" class="form-control border-light-subtle font-monospace fw-bold" required>
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ $employee->name }}" class="form-control border-light-subtle" required>
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Email Address</label>
                        <input type="email" name="email" value="{{ $employee->email }}" class="form-control border-light-subtle">
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Phone Number</label>
                        <input type="text" name="phone" value="{{ $employee->phone }}" class="form-control border-light-subtle">
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Designation</label>
                        <input type="text" name="designation" value="{{ $employee->designation }}" class="form-control border-light-subtle">
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Join Date</label>
                        <input type="date" name="join_date" value="{{ $employee->join_date }}" class="form-control border-light-subtle">
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Basic Salary (৳)</label>
                        <input type="number" step="0.01" name="salary" value="{{ $employee->salary }}" class="form-control border-light-subtle">
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select border-light-subtle">
                            <option value="active" {{ $employee->status == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $employee->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    @if ($employee->image)
                        <div class="col-12">
                            <label class="form-label small text-secondary fw-semibold mb-1">Current Image</label>
                            <div>
                                <img src="{{ asset('uploads/employees/' . $employee->image) }}" width="80" height="80" class="rounded-circle object-fit-cover border shadow-sm">
                            </div>
                        </div>
                    @endif

                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">New Image (Optional)</label>
                        <input type="file" name="image" class="form-control border-light-subtle">
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 border-top pt-3 mt-4">
                    <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary px-4 rounded-3">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 rounded-3">Update Employee</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
