@extends('frontend.layouts.app')

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header mb-4">
            <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h4 class="card-title fw-bold text-dark mb-1">Create New Project</h4>
                    <p class="text-muted small mb-0">Define client details, project budget, and execution parameters</p>
                </div>
                <div>
                    <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3 shadow-sm">
                        <i class="fa fa-arrow-left me-2"></i>Back to Projects
                    </a>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        <form action="{{ route('projects.store') }}" method="post">
            @csrf

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
                                            value="new" checked>
                                        <label class="form-check-label" for="newClient">New Client</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="client_type"
                                            id="existingClient" value="existing">
                                        <label class="form-check-label" for="existingClient">Existing Client</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <div class="input-block mb-3">
                                <label class="form-label">Project Name <span class="text-danger">*</span></label>
                                <input type="text" name="project_name" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <!-- New Client Form -->
                    <div id="newClientForm">
                        <div class="row">
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <div class="input-block mb-3">
                                    <label class="form-label">Client Name <span class="text-danger">*</span></label>
                                    <input type="text" name="client_name" class="form-control" id="newClientName"
                                        required>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <div class="input-block mb-3">
                                    <label class="form-label">Phone <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" name="client_phone" id="newClientPhone"
                                        required>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <div class="input-block mb-3">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="client_email" id="newClientEmail"
                                        required>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <div class="input-block mb-3">
                                    <label class="form-label">Address <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="client_address" id="newClientAddress"
                                        required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Existing Client Form -->
                    <div id="existingClientForm" style="display: none;">
                        <div class="row">
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <div class="input-block mb-3">
                                    <label class="form-label">Select Client <span class="text-danger">*</span></label>
                                    <select name="existing_client_id" class="form-control" id="clientSelect">
                                        <option value="">Select client</option>
                                        @foreach ($existingClients as $client)
                                            <option value="{{ $client->id }}">
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
                                <input type="number" name="budget" class="form-control" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <div class="input-block mb-3">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <div class="input-block mb-3">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <div class="input-block mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-control" required>
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-8 col-md-6 col-sm-12">
                            <div class="input-block mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="2" placeholder="Project description"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-start">
                <button type="submit" class="btn btn-primary">Create</button>
                <a href="{{ route('projects.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    {{-- <script>
    // Client type toggle JavaScript
    document.addEventListener('DOMContentLoaded', function() {
        const newClientRadio = document.getElementById('newClient');
        const existingClientRadio = document.getElementById('existingClient');
        const newClientForm = document.getElementById('newClientForm');
        const existingClientForm = document.getElementById('existingClientForm');

        function toggleClientForms() {
            if (newClientRadio.checked) {
                newClientForm.style.display = 'block';
                existingClientForm.style.display = 'none';
            } else {
                newClientForm.style.display = 'none';
                existingClientForm.style.display = 'block';
            }
        }

        newClientRadio.addEventListener('change', toggleClientForms);
        existingClientRadio.addEventListener('change', toggleClientForms);
    });
</script> --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const newClientRadio = document.getElementById('newClient');
            const existingClientRadio = document.getElementById('existingClient');
            const newClientForm = document.getElementById('newClientForm');
            const existingClientForm = document.getElementById('existingClientForm');

            // Get all new client input fields
            const newClientInputs = document.querySelectorAll('#newClientForm input, #newClientForm select');

            function toggleClientForms() {
                if (newClientRadio.checked) {
                    newClientForm.style.display = 'block';
                    existingClientForm.style.display = 'none';

                    // Make new client fields required
                    newClientInputs.forEach(input => {
                        input.required = true;
                    });

                    // Make existing client field not required
                    document.getElementById('clientSelect').required = false;
                } else {
                    newClientForm.style.display = 'none';
                    existingClientForm.style.display = 'block';

                    // Make new client fields not required
                    newClientInputs.forEach(input => {
                        input.required = false;
                    });

                    // Make existing client field required
                    document.getElementById('clientSelect').required = true;
                }
            }

            newClientRadio.addEventListener('change', toggleClientForms);
            existingClientRadio.addEventListener('change', toggleClientForms);

            // Initialize on page load
            toggleClientForms();
        });
    </script>
@endsection
