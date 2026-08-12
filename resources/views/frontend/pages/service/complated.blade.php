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
    .badge-soft-success {
        background-color: rgba(25, 135, 84, 0.12) !important;
        color: #198754 !important;
        font-weight: 600;
    }
    .badge-soft-danger {
        background-color: rgba(220, 53, 69, 0.12) !important;
        color: #dc3545 !important;
        font-weight: 600;
    }
    .badge-soft-warning {
        background-color: rgba(255, 193, 7, 0.15) !important;
        color: #b58105 !important;
        font-weight: 600;
    }
    .badge-soft-primary {
        background-color: rgba(118, 56, 255, 0.12) !important;
        color: #7638ff !important;
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
</style>
@endpush

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1">Completed Services Report</h4>
                <p class="text-muted small mb-0">Overview of completed repair jobs, revenue stats, customer reviews, and warranties</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a class="btn btn-outline-danger px-3 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2" href="{{ route('service.complated', array_merge(request()->query(), ['search_for' => 'pdf'])) }}" target="_blank">
                    <i class="fe fe-file-text fs-6"></i>
                    <span>Export PDF</span>
                </a>
                <a class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2" href="{{ route('service.create') }}">
                    <i class="fe fe-plus-circle fs-6"></i>
                    <span>Add Service</span>
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Revenue Stats Bar -->
    <div class="row g-3 mb-4">
        <div class="col-xl-2 col-md-4 col-sm-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="avatar avatar-sm bg-primary-light text-primary rounded-circle">
                            <i class="fas fa-dollar-sign"></i>
                        </span>
                        <a href="{{ route('service.complated', ['from' => date('Y-m-d'), 'to' => date('Y-m-d')]) }}" class="text-muted fs-7" title="Filter Today">
                            <i class="fe fe-filter"></i>
                        </a>
                    </div>
                    <span class="text-muted small d-block mb-1">Today's Revenue</span>
                    <h5 class="fw-bold text-dark mb-0">৳{{ number_format($todaysRevenue ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="avatar avatar-sm bg-info-light text-info rounded-circle">
                            <i class="fas fa-dollar-sign"></i>
                        </span>
                        <a href="{{ route('service.complated', ['from' => now()->startOfWeek()->format('Y-m-d'), 'to' => now()->endOfWeek()->format('Y-m-d')]) }}" class="text-muted fs-7" title="Filter This Week">
                            <i class="fe fe-filter"></i>
                        </a>
                    </div>
                    <span class="text-muted small d-block mb-1">This Week</span>
                    <h5 class="fw-bold text-dark mb-0">৳{{ number_format($thisWeeksRevenue ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-4 col-sm-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="avatar avatar-sm bg-success-light text-success rounded-circle">
                            <i class="fas fa-dollar-sign"></i>
                        </span>
                        <a href="{{ route('service.complated', ['from' => now()->startOfMonth()->format('Y-m-d'), 'to' => now()->endOfMonth()->format('Y-m-d')]) }}" class="text-muted fs-7" title="Filter This Month">
                            <i class="fe fe-filter"></i>
                        </a>
                    </div>
                    <span class="text-muted small d-block mb-1">This Month</span>
                    <h5 class="fw-bold text-dark mb-0">৳{{ number_format($thisMonthsRevenue ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-4 col-sm-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="avatar avatar-sm bg-purple-light text-purple rounded-circle">
                            <i class="fas fa-dollar-sign"></i>
                        </span>
                        <a href="{{ route('service.complated', ['from' => now()->startOfYear()->format('Y-m-d'), 'to' => now()->endOfYear()->format('Y-m-d')]) }}" class="text-muted fs-7" title="Filter This Year">
                            <i class="fe fe-filter"></i>
                        </a>
                    </div>
                    <span class="text-muted small d-block mb-1">This Year</span>
                    <h5 class="fw-bold text-dark mb-0">৳{{ number_format($thisYearsRevenue ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="avatar avatar-sm bg-danger-light text-danger rounded-circle">
                            <i class="fas fa-exclamation-triangle"></i>
                        </span>
                        <a href="{{ route('service.complated', ['service_type' => 'due']) }}" class="text-muted fs-7" title="Filter Dues">
                            <i class="fe fe-filter"></i>
                        </a>
                    </div>
                    <span class="text-muted small d-block mb-1">Total Dues</span>
                    <h5 class="fw-bold text-danger mb-0">৳{{ number_format($totalServiceDues ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>
    </div>
    <!-- /Revenue Stats Bar -->

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-3">
        <!-- Filter Controls -->
        <div class="card-header bg-white py-3 border-bottom border-light">
            <div class="row align-items-center g-3">
                <div class="col-12 col-md-6 col-lg-5">
                    <div class="search-box-custom">
                        <input type="text" id="completedSearchInput" class="form-control border-light-subtle" placeholder="Search customer name, phone, product, invoice..." autocomplete="off">
                    </div>
                </div>
                <div class="col-12 col-md-3 col-lg-3">
                    <select id="completedTypeFilterSelect" class="form-select border-light-subtle">
                        <option value="all">All Payment Statuses</option>
                        <option value="paid">Paid Only</option>
                        <option value="due">Due Only</option>
                    </select>
                </div>
                <div class="col-12 col-md-3 col-lg-4 text-md-end text-muted small">
                    Showing <span id="visibleCompletedCount" class="fw-bold text-dark">{{ $services->count() }}</span> of {{ $services->count() }} records
                </div>
            </div>
        </div>

        <!-- Table Body -->
        <div class="card-body p-0" style="overflow: visible;">
            <div class="table-responsive" style="overflow: visible !important;">
                <table class="table table-hover table-custom align-middle mb-0">
                    <thead class="bg-light text-secondary fs-7 text-uppercase">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Completed Date</th>
                            <th>Customer Info</th>
                            <th>Product Details</th>
                            <th>Total Bill</th>
                            <th>Paid Amount</th>
                            <th>Due Amount</th>
                            <th>Customer Review</th>
                            <th>Warranty (Days)</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($services as $service)
                            @php
                                $warrantyPeriod = (int) ($service->warranty_duration ?? 0);
                                $createdAt = \Carbon\Carbon::parse($service->created_at);
                                $warrantyExpiresAt = $createdAt->copy()->addDays($warrantyPeriod);
                                $remainingDays = $warrantyPeriod > 0 ? now()->diffInDays($warrantyExpiresAt, false) : 0;
                            @endphp
                            <tr class="completed-row" data-search="{{ strtolower($service->name . ' ' . $service->phone . ' ' . (optional($service->product)->name ?? $service->product_name) . ' ' . $service->complated_date) }}" data-type="{{ $service->due_amount > 0 ? 'due' : 'paid' }}">
                                <td class="ps-4 text-muted fw-semibold">{{ $loop->iteration }}</td>
                                <td>
                                    <span class="text-secondary small fw-semibold">
                                        {{ $service->complated_date ?? ($service->created_at ? $service->created_at->format('d M Y') : 'N/A') }}
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <span class="fw-bold text-dark d-block">{{ $service->name }}</span>
                                        <small class="text-muted fs-7"><i class="fe fe-phone me-1"></i>{{ $service->phone ?? 'N/A' }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">
                                        {{ Str::limit(optional($service->product)->name ?? $service->product_name, 25) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-soft-primary px-3 py-1 rounded-pill fs-7">
                                        ৳{{ number_format($service->bill, 2) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-soft-success px-3 py-1 rounded-pill fs-7">
                                        ৳{{ number_format($service->paid_amount, 2) }}
                                    </span>
                                </td>
                                <td>
                                    @if ($service->due_amount > 0)
                                        <span class="badge badge-soft-danger px-3 py-1 rounded-pill fs-7">
                                            ৳{{ number_format($service->due_amount, 2) }}
                                        </span>
                                    @else
                                        <span class="badge badge-soft-success px-3 py-1 rounded-pill fs-7">
                                            0.00
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="rating-stars">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= ($service->rating ?? 0))
                                                <i class="fas fa-star text-warning fs-7"></i>
                                            @else
                                                <i class="far fa-star text-muted opacity-25 fs-7"></i>
                                            @endif
                                        @endfor
                                    </span>
                                </td>
                                <td>
                                    @if ($remainingDays > 0)
                                        <span class="badge badge-soft-info px-2 py-1 rounded-2 fs-7">
                                            {{ max(0, $remainingDays) }} Days Left
                                        </span>
                                    @else
                                        <span class="badge badge-soft-danger px-2 py-1 rounded-2 fs-7">
                                            Expired
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="dropdown">
                                        <a href="javascript:void(0)" class="btn-action-icon shadow-none" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2"
                                                    href="{{ route('service.payments', ['id' => $service->id, 'payment_for' => '1']) }}">
                                                    <i class="fe fe-credit-card text-primary"></i>
                                                    <span>{{ $service->due_amount <= 0 ? 'View Payments' : 'Get Payment' }}</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2 review-btnn" href="javascript:void(0)" onclick="openRatingDialog(this)" data-id="{{ $service->id }}" data-comments="{{ $service->review_comments }}">
                                                    <i class="fe fe-star text-warning"></i>
                                                    <span>Review Customer</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" target="_blank"
                                                    href="{{ route('service.invoice', $service->id) }}">
                                                    <i class="fe fe-file-text text-info"></i>
                                                    <span>View Invoice</span>
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider opacity-50"></li>
                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2 text-danger" href="javascript:void(0)"
                                                    onclick="if (confirm('Are you sure you want to delete this service record?')) { document.getElementById('serviceDelete{{ $service->id }}').submit(); }">
                                                    <i class="fe fe-trash-2 text-danger"></i>
                                                    <span>Delete Record</span>
                                                </a>
                                                <form id="serviceDelete{{ $service->id }}" action="{{ route('service.destroy', $service->id) }}" method="POST" class="d-none">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">No completed service records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Customer Rating Modal Functionality
function openRatingDialog(ele) {
    let serviceId = ele.getAttribute("data-id");
    let comments = ele.getAttribute("data-comments") || "";
    let ratingBox = document.createElement("div");
    ratingBox.style.position = "fixed";
    ratingBox.style.top = "50%";
    ratingBox.style.left = "50%";
    ratingBox.style.transform = "translate(-50%, -50%)";
    ratingBox.style.background = "white";
    ratingBox.style.padding = "24px";
    ratingBox.style.borderRadius = "12px";
    ratingBox.style.boxShadow = "0px 10px 30px rgba(0,0,0,0.2)";
    ratingBox.style.zIndex = "1050";
    ratingBox.style.textAlign = "center";
    ratingBox.style.minWidth = "320px";

    let starsHtml = `<h6 class="fw-bold mb-3">Rate Customer Service</h6>
        <div id="star-container" style="font-size: 32px; margin-bottom: 15px; color: #ffc107; cursor: pointer;">`;
    for (let i = 1; i <= 5; i++) {
        starsHtml += `<span class="star me-1" data-value="${i}">&#9734;</span>`;
    }
    starsHtml += `</div>
        <input id="comments" type="text" class="form-control border-light-subtle mb-3" placeholder="Write review comments..." value="${comments}">
        <div class="d-flex justify-content-center gap-2">
            <button id="close-rating" class="btn btn-outline-secondary btn-sm rounded-2 px-3">Close</button>
            <button id="submit-rating" class="btn btn-primary btn-sm rounded-2 px-4">Submit Review</button>
        </div>`;

    ratingBox.innerHTML = starsHtml;
    document.body.appendChild(ratingBox);

    let selectedRating = 0;
    let stars = ratingBox.querySelectorAll(".star");
    let submitBtn = ratingBox.querySelector("#submit-rating");
    let closeBtn = ratingBox.querySelector("#close-rating");

    stars.forEach(star => {
        star.addEventListener("click", function () {
            selectedRating = parseInt(this.getAttribute("data-value"));
            stars.forEach(s => {
                let val = parseInt(s.getAttribute("data-value"));
                s.innerHTML = val <= selectedRating ? "&#9733;" : "&#9734;";
            });
        });
    });

    submitBtn.addEventListener("click", function () {
        if (selectedRating === 0) {
            alert('Please select a star rating.');
            return;
        }

        let commentsVal = ratingBox.querySelector("#comments").value;
        let form = document.createElement("form");
        form.method = "POST";
        form.action = "{{ route('submit.rating') }}";

        let csrfInput = document.createElement("input");
        csrfInput.type = "hidden";
        csrfInput.name = "_token";
        csrfInput.value = "{{ csrf_token() }}";

        let serviceInput = document.createElement("input");
        serviceInput.type = "hidden";
        serviceInput.name = "service_id";
        serviceInput.value = serviceId;

        let ratingInput = document.createElement("input");
        ratingInput.type = "hidden";
        ratingInput.name = "rating";
        ratingInput.value = selectedRating;

        let commentsInput = document.createElement("input");
        commentsInput.type = "hidden";
        commentsInput.name = "comments";
        commentsInput.value = commentsVal;

        form.appendChild(csrfInput);
        form.appendChild(serviceInput);
        form.appendChild(ratingInput);
        form.appendChild(commentsInput);
        document.body.appendChild(form);
        form.submit();
    });

    closeBtn.addEventListener("click", function () {
        document.body.removeChild(ratingBox);
    });
}

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('completedSearchInput');
    const typeSelect = document.getElementById('completedTypeFilterSelect');
    const rows = document.querySelectorAll('.completed-row');
    const visibleCountSpan = document.getElementById('visibleCompletedCount');

    function filterTable() {
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const typeFilter = typeSelect ? typeSelect.value : 'all';
        let visibleCount = 0;

        rows.forEach(row => {
            const rowSearchText = row.dataset.search || '';
            const rowType = row.dataset.type || '';

            const matchesSearch = query === '' || rowSearchText.includes(query);
            const matchesType = typeFilter === 'all' || rowType === typeFilter;

            if (matchesSearch && matchesType) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        if (visibleCountSpan) {
            visibleCountSpan.textContent = visibleCount;
        }
    }

    if (searchInput) searchInput.addEventListener('input', filterTable);
    if (typeSelect) typeSelect.addEventListener('change', filterTable);
});
</script>
@endsection
