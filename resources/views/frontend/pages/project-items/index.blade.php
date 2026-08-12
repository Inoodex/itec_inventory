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
    .table-custom tbody tr {
        transition: background-color 0.15s ease;
    }
    .table-custom tbody tr:hover {
        background-color: #fcfbff !important;
    }
    .badge-soft-primary {
        background-color: rgba(118, 56, 255, 0.12) !important;
        color: #7638ff !important;
        font-weight: 600;
    }
    .badge-soft-info {
        background-color: rgba(13, 202, 240, 0.12) !important;
        color: #0dcaf0 !important;
        font-weight: 600;
    }
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
    .table-custom th, .table-custom td {
        white-space: nowrap;
    }
    .table-responsive {
        overflow: visible !important;
    }
    .collapse-icon {
        transition: transform 0.2s ease;
    }
</style>
@endpush

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1">Project Items</h4>
                <p class="text-muted small mb-0">Manage items, products, unit prices, and quantities allocated to projects</p>
            </div>
            <div>
                <a href="{{ route('project-items.create') }}" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2">
                    <i class="fe fe-plus-circle fs-6"></i>
                    <span>Add Project Item</span>
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Summary Stats Bar -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-primary-light text-primary rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-package fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Allocated Item Records</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($projectItems->count()) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-info-light text-info rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-box fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Total Item Quantity</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($projectItems->sum('quantity')) }} Pcs</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-success-light text-success rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-dollar-sign fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Total Items Value</h6>
                        <h4 class="mb-0 fw-bold text-dark">৳{{ number_format($projectItems->sum('total'), 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-warning-light text-warning rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-layers fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Projects Allocated</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($projectItems->pluck('project_id')->unique()->count()) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Summary Stats Bar -->

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-3">
        <!-- Live Search Header -->
        <div class="card-header bg-white py-3 border-bottom border-light">
            <div class="row align-items-center g-3">
                <div class="col-12 col-md-6">
                    <div class="search-box-custom">
                        <input type="text" id="projectItemSearchInput" class="form-control border-light-subtle" placeholder="Search project name, product, quantity, total..." autocomplete="off">
                    </div>
                </div>
                <div class="col-12 col-md-6 text-md-end text-muted small">
                    Showing <span id="visibleProjectItemCount" class="fw-bold text-dark">{{ $projectItems->count() }}</span> items
                </div>
            </div>
        </div>

        <div class="card-body p-0" style="overflow: visible;">
            <div class="table-responsive" style="overflow: visible !important;">
                <table class="table table-hover table-custom align-middle mb-0" id="projectItemsTable">
                    <thead class="bg-light text-secondary fs-7 text-uppercase">
                        <tr>
                            <th class="ps-4" style="width: 40px;"></th>
                            <th>Project Name</th>
                            <th>Items Summary</th>
                            <th>Total Qty</th>
                            <th>Total Amount</th>
                            <th class="pe-4 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @php
                            $grouped = $projectItems->groupBy('project_id');
                        @endphp

                        @forelse ($grouped as $projectId => $items)
                            @php
                                $project = $items->first()->project;
                                $totalQty = $items->sum('quantity');
                                $totalAmount = $items->sum('total');
                                $itemCount = $items->count();
                                $rowId = 'project-collapse-' . $projectId;
                                $projName = $project->project_name ?? 'N/A';
                            @endphp

                            <!-- Main Project Row (Collapsible) -->
                            <tr class="project-group-row fw-bold" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#{{ $rowId }}" aria-expanded="false" data-search="{{ strtolower($projName . ' ' . $totalQty . ' ' . $totalAmount) }}">
                                <td class="ps-4 text-primary">
                                    <i class="fas fa-chevron-right collapse-icon"></i>
                                </td>
                                <td>
                                    <span class="text-dark d-block fw-bold">{{ $projName }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-soft-info px-3 py-1 rounded-pill fs-7">{{ $itemCount }} item{{ $itemCount > 1 ? 's' : '' }}</span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ number_format($totalQty) }} Pcs</span>
                                </td>
                                <td>
                                    <span class="badge badge-soft-primary px-3 py-1 rounded-pill fs-7">৳{{ number_format($totalAmount, 2) }}</span>
                                </td>
                                <td class="pe-4 text-end" onclick="event.stopPropagation();">
                                    <a href="{{ route('projects.show', $project->id) }}" class="btn btn-sm btn-outline-primary rounded-2 px-3">
                                        <i class="fe fe-eye me-1"></i>View Project
                                    </a>
                                </td>
                            </tr>

                            <!-- Collapsible Child Rows (Items) -->
                            <tr class="collapse" id="{{ $rowId }}">
                                <td colspan="6" class="p-0 border-0">
                                    <div class="bg-light p-3 border-top border-bottom">
                                        <table class="table table-sm table-hover align-middle mb-0 bg-white rounded-3 overflow-hidden shadow-sm">
                                            <thead class="bg-primary text-white fs-7 text-uppercase">
                                                <tr>
                                                    <th class="ps-3">#</th>
                                                    <th>Product Name & Model</th>
                                                    <th>Unit Price</th>
                                                    <th>Qty</th>
                                                    <th>Total Amount</th>
                                                    <th class="pe-3 text-end">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($items as $subIdx => $item)
                                                    <tr class="project-sub-item-row" data-search="{{ strtolower($item->product->name . ' ' . ($item->product->model ?? '') . ' ' . $item->quantity . ' ' . $item->total) }}">
                                                        <td class="ps-3 text-muted fw-semibold">{{ $subIdx + 1 }}</td>
                                                        <td>
                                                            <span class="fw-bold text-dark d-block">{{ $item->product->name }}</span>
                                                            @if ($item->product->model)
                                                                <span class="text-muted small font-monospace">{{ $item->product->model }}</span>
                                                            @endif
                                                        </td>
                                                        <td>৳{{ number_format($item->unit_price, 2) }}</td>
                                                        <td>
                                                            <span class="badge badge-soft-info px-2 py-1 rounded-pill fs-7">{{ $item->quantity }} Pcs</span>
                                                        </td>
                                                        <td>
                                                            <span class="fw-bold text-dark">৳{{ number_format($item->total, 2) }}</span>
                                                        </td>
                                                        <td class="pe-3 text-end">
                                                            <div class="dropdown d-inline-block">
                                                                <a href="javascript:void(0)" class="btn-action-icon shadow-none" data-bs-toggle="dropdown" aria-expanded="false">
                                                                    <i class="fas fa-ellipsis-v"></i>
                                                                </a>
                                                                <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                                                    <li>
                                                                        <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('project-items.edit', $item->id) }}">
                                                                            <i class="fe fe-edit text-primary"></i>
                                                                            <span>Edit Item</span>
                                                                        </a>
                                                                    </li>
                                                                    <li><hr class="dropdown-divider opacity-50"></li>
                                                                    <li>
                                                                        <a class="dropdown-item py-2 d-flex align-items-center gap-2 text-danger" href="javascript:void(0)" onclick="if (confirm('Are you sure you want to delete this item?')) { document.getElementById('serviceDelete{{ $item->id }}').submit(); }">
                                                                            <i class="fe fe-trash-2 text-danger"></i>
                                                                            <span>Delete Item</span>
                                                                        </a>
                                                                        <form id="serviceDelete{{ $item->id }}" action="{{ route('project-items.destroy', $item->id) }}" method="POST" class="d-none">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                        </form>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="avatar avatar-xl bg-primary-light text-primary rounded-circle mb-3 d-flex align-items-center justify-content-center">
                                            <i class="fe fe-package fs-1"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">No Project Items Found</h5>
                                        <p class="text-muted small mb-3">Add items and materials to your projects</p>
                                        <a href="{{ route('project-items.create') }}" class="btn btn-primary btn-sm px-3 rounded-2">
                                            Add Project Item
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($projectItems->hasPages())
                <div class="p-3 border-top">
                    {{ $projectItems->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Rotate chevron when collapsed/expanded
    document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(trigger => {
        trigger.addEventListener('click', function() {
            const icon = this.querySelector('.collapse-icon');
            const target = document.querySelector(this.getAttribute('data-bs-target'));
            if (target) {
                target.addEventListener('shown.bs.collapse', () => { if(icon) icon.style.transform = 'rotate(90deg)'; });
                target.addEventListener('hidden.bs.collapse', () => { if(icon) icon.style.transform = 'rotate(0deg)'; });
            }
        });
    });

    const searchInput = document.getElementById('projectItemSearchInput');
    const groupRows = document.querySelectorAll('.project-group-row');

    function filterTable() {
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';

        groupRows.forEach(groupRow => {
            const groupSearchText = groupRow.dataset.search || '';
            const targetId = groupRow.getAttribute('data-bs-target');
            const collapseTarget = document.querySelector(targetId);
            
            let hasMatchingSubItem = false;
            if (collapseTarget) {
                const subRows = collapseTarget.querySelectorAll('.project-sub-item-row');
                subRows.forEach(subRow => {
                    const subSearchText = subRow.dataset.search || '';
                    if (query === '' || subSearchText.includes(query)) {
                        subRow.style.display = '';
                        hasMatchingSubItem = true;
                    } else {
                        subRow.style.display = 'none';
                    }
                });
            }

            if (query === '' || groupSearchText.includes(query) || hasMatchingSubItem) {
                groupRow.style.display = '';
            } else {
                groupRow.style.display = 'none';
            }
        });
    }

    if (searchInput) searchInput.addEventListener('input', filterTable);
});
</script>
@endsection
