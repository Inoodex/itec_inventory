@extends('frontend.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            Payment for Project: {{ $project->project_name }}
                            <small class="text-muted"> - {{ $project->client->name ?? 'N/A' }}</small>
                        </h4>
                    </div>
                    <div class="card-body">

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Project Details</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3"><strong>Project Name:</strong>
                                                {{ $project->project_name }}</div>
                                            <div class="col-md-3"><strong>Client Name:</strong>
                                                {{ $project->client->name ?? 'N/A' }}</div>
                                            <div class="col-md-3"><strong>Phone:</strong>
                                                {{ $project->client->phone ?? 'N/A' }}</div>
                                            <div class="col-md-3"><strong>Start Date:</strong> {{ $project->start_date }}
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row text-center">
                                            <div class="col-md-3 border-end">
                                                <h6 class="text-muted mb-1">Total Budget</h6>
                                                <h4 class="text-primary">৳{{ number_format($project->budget, 2) }}</h4>
                                            </div>
                                            <div class="col-md-3 border-end">
                                                <h6 class="text-muted mb-1">Advanced Payment</h6>
                                                <h4 class="text-success">৳{{ number_format($project->advanced_payment, 2) }}
                                                </h4>
                                            </div>
                                            <div class="col-md-3 border-end">
                                                <h6 class="text-muted mb-1">Due Payment</h6>
                                                <h4 class="text-danger">৳{{ number_format($project->due_payment, 2) }}</h4>
                                            </div>
                                            <div class="col-md-3">
                                                <h6 class="text-muted mb-1">Status</h6>
                                                <h4>{{ ucfirst($project->status) }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Make Payment Form -->
                        @if ($project->due_payment > 0)
                            <div class="card mb-4">
                                <div class="card-header bg-light text-white">
                                    <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i> Make Payment</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('projects.process-payment') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="project_id" value="{{ $project->id }}">

                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Payment Amount *</label>
                                                <input type="number" class="form-control form-control-sm"
                                                    name="payment_amount" id="payment_amount"
                                                    max="{{ $project->due_payment }}" min="0.01" step="0.01"
                                                    value="{{ $project->due_payment }}" required
                                                    oninput="updateRemaining(this.value)">
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Payment Method *</label>
                                                <select class="form-select form-select-sm" name="payment_method" required>
                                                    <option value="">Select Method</option>
                                                    <option value="cash">Cash</option>
                                                    <option value="card">Card</option>
                                                    <option value="bank_transfer">Bank Transfer</option>
                                                </select>
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Payment Date</label>
                                                <input type="date" class="form-control form-control-sm"
                                                    name="payment_date" value="{{ now()->format('Y-m-d') }}">
                                            </div>
                                        </div>

                                        <div class="row g-3 mt-2">
                                            <div class="col-md-8">
                                                <label class="form-label fw-bold">Notes (Optional)</label>
                                                <textarea class="form-control form-control-sm" name="notes" rows="2" placeholder="Enter payment notes..."></textarea>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Remaining After Payment</label>
                                                <div class="alert alert-info py-1 text-center mb-0">
                                                    <h5 class="mb-0" id="remainingAmount">৳0.00</h5>
                                                </div>
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-success btn-sm w-100 mt-3">
                                            <i class="fas fa-check-circle me-2"></i> Process Payment
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-success text-center">
                                <h4 class="mb-0">
                                    <i class="fas fa-check-circle me-2"></i>
                                    This project is fully paid! No due amount remaining.
                                </h4>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($project->due_payment > 0)
        <script>
            function updateRemaining(amount) {
                const dueAmount = {{ $project->due_payment }};
                const paymentAmount = parseFloat(amount) || 0;
                const remaining = dueAmount - paymentAmount;

                document.getElementById('remainingAmount').textContent = '৳' + remaining.toFixed(2);

                const remainingElement = document.getElementById('remainingAmount');
                if (remaining === 0) {
                    remainingElement.parentElement.className = 'alert alert-success py-2';
                } else if (remaining < 0) {
                    remainingElement.parentElement.className = 'alert alert-danger py-2';
                } else {
                    remainingElement.parentElement.className = 'alert alert-info py-2';
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                updateRemaining({{ $project->due_payment }});
            });
        </script>
    @endif
@endsection
