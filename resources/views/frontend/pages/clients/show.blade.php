@extends('frontend.layouts.app')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Client Details</h3>
            </div>
            <div class="col-auto">
                <div class="">
                    <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-primary me-2">
                        Edit
                    </a>
                    <a href="{{ route('clients.index') }}" class="btn btn-secondary">
                         Back
                    </a>
                </div>
            </div>
        </div>
    </div>

   <div class="row">
    <div class="col-md-12">
        <!-- Client Basic Info Card -->
        <div class="card mb-4">
     
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Full Name</label>
                            <p class="mb-0 fw-semibold">{{ $client->name }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Phone</label>
                            <p class="mb-0">{{ $client->phone }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Address</label>
                            <p class="mb-0">{{ $client->address }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Email</label>
                            <p class="mb-0">{{ $client->email }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Total Projects</label>
                            <p class="mb-0 fw-semibold text-primary">{{ $client->projects->count() }}</p>
                        </div>
                        <div class="col-md-6">
                        <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Notes</label>
                            <p class="mb-0">{{ $client->notes ?? 'No notes' }}</p>
                        </div>
                    </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Projects Card -->
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Projects</h5>
                <a href="{{ route('projects.create') }}?client_id={{ $client->id }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> New Project
                </a>
            </div>
            <div class="card-body">
                @if($client->projects->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="bg-light">
                                <tr>
                                    <th>Project Name</th>
                                    <th>Budget</th>
                                    <th>Status</th>
                                    <th>Start Date</th>
                                    <th width="80">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($client->projects as $project)
                                <tr>
                                    <td>
                                            {{ $project->project_name }}
                                        </a>
                                    </td>
                                    <td>{{ number_format($project->budget, 2) }}</td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'in_progress' => 'primary', 
                                                'completed' => 'success',
                                                'cancelled' => 'danger'
                                            ];
                                            $statusText = [
                                                'pending' => 'Pending',
                                                'in_progress' => 'In Progress',
                                                'completed' => 'Completed',
                                                'cancelled' => 'Cancelled'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$project->status] ?? 'secondary' }}">
                                            {{ $statusText[$project->status] ?? ucfirst($project->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $project->start_date ? $project->start_date->format('M d, Y') : '—' }}</td>
                                    <td>
                                        <a href="{{ route('projects.show', $project->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="text-muted mb-3">
                            <i class="fas fa-folder-open fa-3x mb-3"></i>
                            <p class="mb-2">No projects found for this client</p>
                        </div>
                        <a href="{{ route('projects.create') }}?client_id={{ $client->id }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Create First Project
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
</div>
@endsection