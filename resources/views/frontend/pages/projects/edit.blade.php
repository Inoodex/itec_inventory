@extends('frontend.layouts.app')

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header mb-4">
            <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h4 class="card-title fw-bold text-dark mb-1">Edit Project</h4>
                    <p class="text-muted small mb-0">Update project parameters, client information, and budget</p>
                </div>
                <div>
                    <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3 shadow-sm">
                        <i class="fa fa-arrow-left me-2"></i>Back to Projects
                    </a>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        <form action="{{ route('projects.update', $project->id) }}" method="post">
            @csrf
            @method('PUT')

            <!-- Client Information -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="content-page-header mb-3">
                        <h6>Client Information</h6>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="input-block mb-3">
                                <label class="form-label">Client Type <span class="text-danger">*</span></label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="client_type" id="newClient"
                                            value="new" {{ $project->client_id ? '' : 'checked' }}>
                                        <label class="form-check-label" for="newClient">New Client</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="client_type"
                                            id="existingClient" value="existing" {{ $project->client_id ? 'checked' : '' }}>
                                        <label class="form-check-label" for="existingClient">Existing Client</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <div class="input-block mb-3">
                                <label class="form-label">Project Name <span class="text-danger">*</span></label>
                                <input type="text" name="project_name" class="form-control"
                                    value="{{ old('project_name', $project->project_name) }}" required>
                            </div>
                        </div>
                    </div>

                    <!-- New Client Form -->
                    <div id="newClientForm" style="{{ $project->client_id ? 'display: none;' : '' }}">
                        <div class="row">
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <div class="input-block mb-3">
                                    <label class="form-label">Client Name <span class="text-danger">*</span></label>
                                    <input type="text" name="client_name" class="form-control" id="newClientName"
                                        value="{{ old('client_name', $project->client_name) }}"
                                        {{ $project->client_id ? '' : 'required' }}>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <div class="input-block mb-3">
                                    <label class="form-label">Phone <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" name="client_phone" id="newClientPhone"
                                        value="{{ old('client_phone', $project->client_phone) }}"
                                        {{ $project->client_id ? '' : 'required' }}>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <div class="input-block mb-3">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="client_email" id="newClientEmail"
                                        value="{{ old('client_email', $project->client_email) }}"
                                        {{ $project->client_id ? '' : 'required' }}>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <div class="input-block mb-3">
                                    <label class="form-label">Address <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="client_address" id="newClientAddress"
                                        value="{{ old('client_address', $project->client_address) }}"
                                        {{ $project->client_id ? '' : 'required' }}>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Existing Client Form -->
                    <div id="existingClientForm" style="{{ $project->client_id ? '' : 'display: none;' }}">
                        <div class="row">
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <div class="input-block mb-3">
                                    <label class="form-label">Select Client <span class="text-danger">*</span></label>
                                    <select name="existing_client_id" class="form-control" id="clientSelect"
                                        {{ $project->client_id ? 'required' : '' }}>
                                        <option value="">Select client</option>
                                        @foreach ($existingClients as $client)
                                            <option value="{{ $client->id }}"
                                                {{ old('existing_client_id', $project->client_id) == $client->id ? 'selected' : '' }}>
                                                {{ $client->name }} - {{ $client->phone }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Project Basic Information -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="content-page-header mb-3">
                        <h6>Project Basic Information</h6>
                    </div>

                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <div class="input-block mb-3">
                                <label class="form-label">Budget</label>
                                <input type="number" name="budget" class="form-control" step="0.01" min="0"
                                    value="{{ old('budget', $project->budget) }}">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <div class="input-block mb-3">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" class="form-control"
                                    value="{{ old('start_date', $project->start_date ? $project->start_date->format('Y-m-d') : '') }}">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <div class="input-block mb-3">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" class="form-control"
                                    value="{{ old('end_date', $project->end_date ? $project->end_date->format('Y-m-d') : '') }}">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <div class="input-block mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-control" required>
                                    <option value="pending"
                                        {{ old('status', $project->status) == 'pending' ? 'selected' : '' }}>Pending
                                    </option>
                                    <option value="in_progress"
                                        {{ old('status', $project->status) == 'in_progress' ? 'selected' : '' }}>In
                                        Progress</option>
                                    <option value="completed"
                                        {{ old('status', $project->status) == 'completed' ? 'selected' : '' }}>Completed
                                    </option>
                                    <option value="cancelled"
                                        {{ old('status', $project->status) == 'cancelled' ? 'selected' : '' }}>Cancelled
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-8 col-md-6 col-sm-12">
                            <div class="input-block mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="Project description">{{ old('description', $project->description) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    Update
                </button>
                <a href="{{ route('projects.show', $project->id) }}" class="btn btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const newClientRadio = document.getElementById('newClient');
            const existingClientRadio = document.getElementById('existingClient');
            const newClientForm = document.getElementById('newClientForm');
            const existingClientForm = document.getElementById('existingClientForm');

            // Get all new client input fields
            const newClientInputs = document.querySelectorAll('#newClientForm input');
            const existingClientSelect = document.getElementById('clientSelect');

            function toggleClientForms() {
                if (newClientRadio.checked) {
                    newClientForm.style.display = 'block';
                    existingClientForm.style.display = 'none';

                    // Make new client fields required
                    newClientInputs.forEach(input => {
                        input.required = true;
                    });

                    // Make existing client field not required
                    existingClientSelect.required = false;
                } else {
                    newClientForm.style.display = 'none';
                    existingClientForm.style.display = 'block';

                    // Make new client fields not required
                    newClientInputs.forEach(input => {
                        input.required = false;
                    });

                    // Make existing client field required
                    existingClientSelect.required = true;
                }
            }

            newClientRadio.addEventListener('change', toggleClientForms);
            existingClientRadio.addEventListener('change', toggleClientForms);

            // Initialize on page load
            toggleClientForms();
        });
    </script>
@endsection
