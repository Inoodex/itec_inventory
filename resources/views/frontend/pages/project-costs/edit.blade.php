@extends('frontend.layouts.app')

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1">Edit Project Cost</h4>
                <p class="text-muted small mb-0">Update cost allocation, amount, category, and date</p>
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
            <form action="{{ route('project-costs.update', $projectCost->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3 mb-4">
                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Project <span class="text-danger">*</span></label>
                        <select name="project_id" class="form-select border-light-subtle @error('project_id') is-invalid @enderror" required>
                            <option value="">Select Project</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id', $projectCost->project_id) == $project->id ? 'selected' : '' }}>
                                    {{ $project->project_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('project_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Cost Category <span class="text-danger">*</span></label>
                        <select name="cost_category_id" class="form-select border-light-subtle @error('cost_category_id') is-invalid @enderror" required>
                            <option value="">Select Category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('cost_category_id', $projectCost->cost_category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('cost_category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Amount (৳) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control border-light-subtle @error('amount') is-invalid @enderror" step="0.01" value="{{ old('amount', $projectCost->amount) }}" required>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Date <span class="text-danger">*</span></label>
                        <input type="date" name="cost_date" class="form-control border-light-subtle @error('cost_date') is-invalid @enderror" value="{{ old('cost_date', $projectCost->cost_date->format('Y-m-d')) }}" required>
                        @error('cost_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Description / Notes</label>
                        <textarea name="description" class="form-control border-light-subtle @error('description') is-invalid @enderror" rows="3">{{ old('description', $projectCost->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 border-top pt-3">
                    <a href="{{ route('project-costs.index') }}" class="btn btn-outline-secondary px-4 rounded-3">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 rounded-3">Update Cost</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
