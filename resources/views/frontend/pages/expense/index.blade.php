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
</style>
@endpush

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1">Daily Expenses</h4>
                <p class="text-muted small mb-0">Track company operational expenses, payment methods, employee disbursements, and categories</p>
            </div>
            <div>
                <a href="{{ route('dailyExpenses.create') }}" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2">
                    <i class="fe fe-plus-circle fs-6"></i>
                    <span>Add Daily Expense</span>
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
                        <i class="fe fe-file-text fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Total Expense Claims</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($dailyExpense->count()) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-danger-light text-danger rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-dollar-sign fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Total Amount Spent</h6>
                        <h4 class="mb-0 fw-bold text-dark">৳{{ number_format($dailyExpense->sum('amount'), 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-success-light text-success rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-archive fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Cash Payments</h6>
                        <h4 class="mb-0 fw-bold text-dark">৳{{ number_format($dailyExpense->where('spend_method', 'cash')->sum('amount'), 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-info-light text-info rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-credit-card fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Card / Bank Payments</h6>
                        <h4 class="mb-0 fw-bold text-dark">৳{{ number_format($dailyExpense->whereIn('spend_method', ['card', 'bank_transfer'])->sum('amount'), 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Summary Stats Bar -->

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-4">
            <h6 class="fw-bold text-dark mb-3"><i class="fe fe-filter me-2 text-primary"></i>Filter Daily Expenses</h6>
            <form action="{{ route('dailyExpenses.index') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-2 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">From Date</label>
                        <input type="date" name="from" class="form-control border-light-subtle" value="{{ old('from', $request->from ?? '') }}">
                    </div>

                    <div class="col-lg-2 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">To Date</label>
                        <input type="date" name="to" class="form-control border-light-subtle" value="{{ old('to', $request->to ?? '') }}">
                    </div>

                    <div class="col-lg-2 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Spend Method</label>
                        <select name="spend_method" class="form-select border-light-subtle">
                            <option value="">All Methods</option>
                            <option value="cash" {{ $request->spend_method == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="card" {{ $request->spend_method == 'card' ? 'selected' : '' }}>Card</option>
                            <option value="bank_transfer" {{ $request->spend_method == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-6 col-12">
                        <label class="form-label small text-secondary fw-semibold mb-1">Category</label>
                        <select name="expense_category_id" class="form-select border-light-subtle">
                            <option value="">All Categories</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $request->expense_category_id == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-12 col-12 d-flex gap-2">
                        <button type="submit" name="search_for" value="filter" class="btn btn-primary flex-fill rounded-3 py-2">Filter</button>
                        <a href="{{ route('dailyExpenses.index') }}" class="btn btn-outline-secondary px-3 py-2 rounded-3">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-3">
        <!-- Live Search Header -->
        <div class="card-header bg-white py-3 border-bottom border-light">
            <div class="row align-items-center g-3">
                <div class="col-12 col-md-6">
                    <div class="search-box-custom">
                        <input type="text" id="dailyExpenseSearchInput" class="form-control border-light-subtle" placeholder="Search employee, category, remarks, amount..." value="{{ old('key', $request->key ?? '') }}" autocomplete="off">
                    </div>
                </div>
                <div class="col-12 col-md-6 text-md-end text-muted small">
                    Showing <span id="visibleDailyExpenseCount" class="fw-bold text-dark">{{ $dailyExpense->count() }}</span> records
                </div>
            </div>
        </div>

        <div class="card-body p-0" style="overflow: visible;">
            <div class="table-responsive" style="overflow: visible !important;">
                <table class="table table-hover table-custom align-middle mb-0" id="dailyExpensesTable">
                    <thead class="bg-light text-secondary fs-7 text-uppercase">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Date</th>
                            <th>Employee</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Spend Method</th>
                            <th>Remarks</th>
                            <th class="pe-4 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($dailyExpense as $item)
                            @php
                                $empName = $item->employee->name ?? 'N/A';
                                $catName = $item->category_name ?? 'N/A';
                                $dateFormatted = \Carbon\Carbon::parse($item->date)->format('d M, Y');
                                $spendFormatted = ucfirst(str_replace('_', ' ', $item->spend_method));
                            @endphp
                            <tr class="daily-expense-row" data-search="{{ strtolower($empName . ' ' . $catName . ' ' . $item->remarks . ' ' . $item->amount . ' ' . $spendFormatted) }}">
                                <td class="ps-4 text-muted fw-semibold">{{ $loop->iteration }}</td>
                                <td>
                                    <span class="text-secondary small">{{ $dateFormatted }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark d-block">{{ $empName }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-soft-info px-3 py-1 rounded-pill fs-7">{{ $catName }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-soft-primary px-3 py-1 rounded-pill fs-7">৳{{ number_format($item->amount, 2) }}</span>
                                </td>
                                <td>
                                    <span class="text-dark small fw-semibold">{{ $spendFormatted }}</span>
                                </td>
                                <td>
                                    <span class="text-muted small">{{ Str::limit($item->remarks, 30) }}</span>
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="dropdown d-inline-block">
                                        <a href="javascript:void(0)" class="btn-action-icon shadow-none" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('dailyExpenses.edit', $item->id) }}">
                                                    <i class="fe fe-edit text-primary"></i>
                                                    <span>Edit Expense</span>
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider opacity-50"></li>
                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2 text-danger" href="javascript:void(0)" onclick="if(confirm('Are you sure you want to delete this daily expense record?')) document.getElementById('del{{ $item->id }}').submit();">
                                                    <i class="fe fe-trash-2 text-danger"></i>
                                                    <span>Delete Expense</span>
                                                </a>
                                                <form id="del{{ $item->id }}" method="POST" action="{{ route('dailyExpenses.destroy', $item->id) }}" class="d-none">
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
                                <td colspan="8" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="avatar avatar-xl bg-primary-light text-primary rounded-circle mb-3 d-flex align-items-center justify-content-center">
                                            <i class="fe fe-file-text fs-1"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">No Daily Expenses Found</h5>
                                        <p class="text-muted small mb-3">Add daily expense transactions to record operational spending</p>
                                        <a href="{{ route('dailyExpenses.create') }}" class="btn btn-primary btn-sm px-3 rounded-2">
                                            Add Daily Expense
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('dailyExpenseSearchInput');
    const rows = document.querySelectorAll('.daily-expense-row');
    const visibleCountSpan = document.getElementById('visibleDailyExpenseCount');

    function filterTable() {
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        let visibleCount = 0;

        rows.forEach(row => {
            const rowSearchText = row.dataset.search || '';
            if (query === '' || rowSearchText.includes(query)) {
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
});
</script>
@endsection
