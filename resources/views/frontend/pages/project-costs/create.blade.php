@extends('frontend.layouts.app')

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1">Add Project Cost</h4>
                <p class="text-muted small mb-0">Record operational expenditures and allocations for projects</p>
            </div>
            <div>
                <a href="{{ route('project-costs.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3 shadow-sm">
                    <i class="fa fa-arrow-left me-2"></i>Back to Project Costs
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">
            <form action="{{ route('project-costs.store') }}" method="POST">
                @csrf

                <div class="row g-3 mb-4">
                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Project <span class="text-danger">*</span></label>
                        <select name="project_id" class="form-select border-light-subtle" required>
                            <option value="">Select Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Cost Category <span class="text-danger">*</span></label>
                        <select name="cost_category_id" class="form-select border-light-subtle" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Amount (৳) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control border-light-subtle" step="0.01" placeholder="Enter amount" required>
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Cost Date <span class="text-danger">*</span></label>
                        <input type="date" name="cost_date" class="form-control border-light-subtle" value="{{ now()->format('Y-m-d') }}" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Description / Notes</label>
                        <textarea name="description" class="form-control border-light-subtle" rows="3" placeholder="Enter cost description or details"></textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 border-top pt-3">
                    <a href="{{ route('project-costs.index') }}" class="btn btn-outline-secondary px-4 rounded-3">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 rounded-3">Save Cost</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection