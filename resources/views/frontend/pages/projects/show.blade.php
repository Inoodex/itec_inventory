@extends('frontend.layouts.app')
@section('content')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .content.container-fluid .card {
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .content.container-fluid .card-body {
            padding: 20px;
        }

        .content.container-fluid .content-page-header h6 {
            color: #198754;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .content.container-fluid .info-block {
            margin-bottom: 15px;
        }

        .content.container-fluid .info-label {
            color: #000 !important;
            font-weight: 500;
            margin-bottom: 5px;
        }

        .content.container-fluid .info-value {
            color: #333;
            padding: 8px 12px;
            background-color: #f8f9fa;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .content.container-fluid .view-btn-sm {
            background: #198754;
            color: #fff;
            padding: 4px 12px;
            font-size: 14px;
            border: 1px solid #198754;
            border-radius: 4px;
            transition: 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .content.container-fluid .view-btn-sm:hover {
            background: transparent;
            color: #198754;
            border: 1px solid #198754;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
        }

        .items-table th {
            background-color: #198754;
            color: white;
            padding: 10px;
            text-align: left;
        }

        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .items-table tr:hover {
            background-color: #f5f5f5;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .sidebar-menu a,
        .nav-sidebar a,
        .sidebar-nav a,
        [class*="sidebar"] a {
            text-decoration: none !important;
        }
    </style>

    <div class="content container-fluid pt-0">
        <!-- Action Buttons -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h4>Project Details</h4>

                    <!-- Optional: Download as PDF -->
                    {{-- <a href="{{ route('projects.bill.download', $project->id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-download"></i> Download PDF
                        </a> --}}

                    <div class="d-flex gap-2">
                        {{-- <a href="{{ route('projects.bills.create', $project->id) }}" class="btn btn-success btn-m"
                            target="_blank">
                            Generate Bill
                        </a> --}}
                        <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-info btn-m">
                            Edit
                        </a>
                        <a href="{{ route('projects.index') }}" class="btn btn-secondary btn-m">
                            Back
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <div class="page-header mb-3">
                    <div class="content-page-header mb-3">
                        <h6>Client Information</h6>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            {{-- <div class="col-lg-4 col-md-6 col-sm-12">
                                <div class="info-block mb-3">
                                    <label class="info-label">Client Type</label>
                                    <div class="info-value">
                                        {{ $project->client_id ? 'Existing Client' : 'New Client' }}
                                    </div>
                                </div>
                            </div> --}}

                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <div class="info-block mb-3">
                                    <label class="info-label">Project Name</label>
                                    <div class="info-value">
                                        {{ $project->project_name }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <div class="info-block mb-3">
                                    <label class="info-label">Client/Company Name</label>
                                    <div class="info-value">
                                        {{ $project->client_name ?? $project->client->name }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <div class="info-block mb-3">
                                    <label class="info-label">Phone</label>
                                    <div class="info-value">
                                        {{ $project->client_phone ?? $project->client->phone }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <div class="info-block mb-3">
                                    <label class="info-label">Email</label>
                                    <div class="info-value">
                                        {{ $project->client_email ?? $project->client->email }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <div class="info-block mb-3">
                                    <label class="info-label">Address</label>
                                    <div class="info-value">
                                        {{ $project->client_address ?? $project->client->address }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <div class="page-header mb-3">
                    <div class="content-page-header mb-3">
                        <h6>Project Information</h6>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <div class="info-block mb-3">
                                    <label class="info-label">Budget</label>
                                    <div class="info-value">
                                        {{ number_format($project->budget, 2) }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <div class="info-block mb-3">
                                    <label class="info-label">Start Date</label>
                                    <div class="info-value">
                                        {{ $project->start_date ? $project->start_date->format('M d, Y') : 'Not set' }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <div class="info-block mb-3">
                                    <label class="info-label">End Date</label>
                                    <div class="info-value">
                                        {{ $project->end_date ? $project->end_date->format('M d, Y') : 'Not set' }}
                                    </div>
                                </div>
                            </div>

                            {{-- <div class="col-lg-4 col-md-6 col-sm-12">
                                <div class="info-block mb-3">
                                    <label class="info-label">Status</label>
                                    <div class="info-value">
                                        <span class=" status-{{ $project->status }}">
                                            {{ ucfirst($project->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div> --}}

                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <div class="info-block mb-3">
                                    <label class="info-label">Status</label>
                                    <div class="info-value">
                                        <span class="client status-{{ $project->status }}">
                                            {{ $project->status_text }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-8 col-md-6 col-sm-12">
                                <div class="info-block mb-3">
                                    <label class="info-label">Description</label>
                                    <div class="info-value">
                                        {{ $project->description ?: 'No description provided' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- <div class="card mb-0">
            <div class="card-body">
                <div class="page-header mb-3">
                    <div class="content-page-header mb-3">
                        <h6>Project Items</h6>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        @if ($project->items->count() > 0)
                            <div class="table-responsive">
                                <table class="items-table">
                                    <thead>
                                        <tr>
                                            <th>Product Name</th>
                                            <th>Unit Price</th>
                                            <th>Quantity</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($project->items as $item)
                                            <tr>
                                                <td>{{ $item->product->name }}({{ $item->product->model }})</td>
                                                <td>{{ number_format($item->unit_price, 2) }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>{{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" style="text-align: right; font-weight: bold;">Sub Total:</td>
                                            <td style="font-weight: bold;">{{ number_format($project->sub_total, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" style="text-align: right; font-weight: bold;">Grand Total:
                                            </td>
                                            <td style="font-weight: bold;">{{ number_format($project->grand_total, 2) }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <p class="text-muted">No items added to this project.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div> --}}

        <div class="card mb-0">
            <div class="card-body">
                <div class="page-header mb-3">
                    <div class="content-page-header mb-3">
                        <h6>Project Items</h6>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        @if ($project->items->count() > 0)
                            @php
                                $itemsSubTotal = $project->items->sum(function ($item) {
                                    return $item->unit_price * $item->quantity;
                                });
                                $totalCosts = $project->costs->sum('amount');
                                $grandTotal = $itemsSubTotal + $totalCosts;
                            @endphp

                            <div class="table-responsive">
                                <table class="items-table">
                                    <thead>
                                        <tr>
                                            <th>Product Name</th>
                                            <th>Unit Price</th>
                                            <th>Quantity</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($project->items as $item)
                                            <tr>
                                                <td>{{ $item->product->name }}({{ $item->product->model }})</td>
                                                <td>{{ number_format($item->unit_price, 2) }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>{{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" style="text-align: right; font-weight: bold;">Sub Total:</td>
                                            <td style="font-weight: bold;">{{ number_format($itemsSubTotal, 2) }}</td>
                                        </tr>

                                        <!-- Costs below Sub Total -->
                                        @foreach ($project->costs as $cost)
                                            <tr>
                                                {{-- <td>{{ $cost->costCategory->name ?? 'Additional Cost' }}</td> --}}
                                                <td>{{ $cost->category->name ?? ($cost->costCategory->title ?? 'No Category') }}
                                                </td>
                                                <td>{{ number_format($cost->amount, 2) }}</td>
                                                <td>1</td>
                                                <td>{{ number_format($cost->amount, 2) }}</td>
                                            </tr>
                                        @endforeach

                                        <tr>
                                            <td colspan="3" style="text-align: right; font-weight: bold;">Grand Total:
                                            </td>
                                            <td style="font-weight: bold;">{{ number_format($grandTotal, 2) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <p class="text-muted">No items added to this project.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
