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
    .badge-soft-primary {
        background-color: rgba(118, 56, 255, 0.12) !important;
        color: #7638ff !important;
        font-weight: 600;
    }
    .badge-soft-success {
        background-color: rgba(25, 135, 84, 0.12) !important;
        color: #198754 !important;
        font-weight: 600;
    }
    .badge-soft-info {
        background-color: rgba(13, 202, 240, 0.12) !important;
        color: #0dcaf0 !important;
        font-weight: 600;
    }
    .badge-soft-warning {
        background-color: rgba(255, 193, 7, 0.15) !important;
        color: #ffb000 !important;
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
                <h4 class="card-title fw-bold text-dark mb-1">Welcome back, {{ auth()->user()->name }}! </h4>
                <p class="text-muted small mb-0">Here is a real-time summary of sales, purchases, operational expenses, and projects</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('sales.create') }}" class="btn btn-primary px-3 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2">
                    <i class="fe fe-shopping-cart fs-6"></i>
                    <span>New Sale</span>
                </a>
                <a href="{{ route('purchase.create') }}" class="btn btn-outline-primary px-3 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2">
                    <i class="fe fe-plus-circle fs-6"></i>
                    <span>Add Purchase</span>
                </a>
                <a href="{{ route('dailyExpenses.create') }}" class="btn btn-outline-secondary px-3 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2">
                    <i class="fe fe-dollar-sign fs-6"></i>
                    <span>Add Expense</span>
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Top Quick Metrics Bar -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-primary-light text-primary rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-users fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Total Customers</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($totalCustomers ?? 0) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-info-light text-info rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-layers fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Total Projects</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($totalProjects ?? 0) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-success-light text-success rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-user-check fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Total Employees</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($totalEmployees ?? 0) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-warning-light text-warning rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-package fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Products Catalog</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($totalProducts ?? 0) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Top Quick Metrics Bar -->

    <!-- Accounting & Financial Balances (Double-Entry Live Health) -->
    @if(auth()->check() && auth()->user()->hasRole(['Super Admin', 'Admin', 'admin']))
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-dark mb-0"><i class="fe fe-shield me-2 text-primary"></i>Financial & Accounting Health</h5>
            <div class="d-flex gap-2">
                <a href="{{ route('chart-of-accounts.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                    <i class="fe fe-folder me-1"></i> Chart of Accounts
                </a>
                <a href="{{ route('journal-entries.create') }}" class="btn btn-sm btn-primary rounded-pill px-3">
                    <i class="fe fe-plus me-1"></i> New Voucher
                </a>
                <a href="{{ route('trial-balance.index') }}" class="btn btn-sm btn-outline-success rounded-pill px-3">
                    <i class="fe fe-check-square me-1"></i> Trial Balance
                </a>
            </div>
        </div>

        <div class="row g-3">
            <!-- Liquid Cash in Hand -->
            <div class="col-xl-3 col-md-6 col-12">
                <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0 border-start border-4 border-success">
                    <div class="card-body d-flex align-items-center">
                        <div class="avatar avatar-lg bg-success-light text-success rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                            <i class="fe fe-dollar-sign fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted fw-normal mb-1">Cash in Hand (1110)</h6>
                            <h4 class="mb-0 fw-bold text-dark">৳{{ number_format($liquidCash ?? 0, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Bank Balances -->
            <div class="col-xl-3 col-md-6 col-12">
                <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0 border-start border-4 border-info">
                    <div class="card-body d-flex align-items-center">
                        <div class="avatar avatar-lg bg-info-light text-info rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                            <i class="fe fe-credit-card fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted fw-normal mb-1">Total Bank Balance</h6>
                            <h4 class="mb-0 fw-bold text-dark">৳{{ number_format($bankBalance ?? 0, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Accounts Receivable -->
            <div class="col-xl-3 col-md-6 col-12">
                <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0 border-start border-4 border-warning">
                    <div class="card-body d-flex align-items-center">
                        <div class="avatar avatar-lg bg-warning-light text-warning rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                            <i class="fe fe-user-check fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted fw-normal mb-1">Receivables (AR - 1130)</h6>
                            <h4 class="mb-0 fw-bold text-dark">৳{{ number_format($receivables ?? 0, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Accounts Payable -->
            <div class="col-xl-3 col-md-6 col-12">
                <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0 border-start border-4 border-danger">
                    <div class="card-body d-flex align-items-center">
                        <div class="avatar avatar-lg bg-danger-light text-danger rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                            <i class="fe fe-truck fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted fw-normal mb-1">Payables (AP - 2110)</h6>
                            <h4 class="mb-0 fw-bold text-dark">৳{{ number_format($payables ?? 0, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-info-light text-info rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-layers fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Total Projects</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($totalProjects ?? 0) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-success-light text-success rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-user-check fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Total Employees</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($totalEmployees ?? 0) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-warning-light text-warning rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-package fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Products Catalog</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($totalProducts ?? 0) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Top Quick Metrics Bar -->

    <!-- Sales Overview Section -->
    <div class="mb-4">
        <h5 class="fw-bold text-dark mb-3"><i class="fe fe-trending-up me-2 text-success"></i>Sales Revenues</h5>
        <div class="row g-3">
            <div class="col-xl-3 col-md-6 col-12">
                <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="avatar avatar-lg bg-success-light text-success rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                            <i class="fe fe-dollar-sign fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted fw-normal mb-1">Today's Sales</h6>
                            <h4 class="mb-0 fw-bold text-dark">৳{{ number_format($todaysSalesRevenue, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-12">
                <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="avatar avatar-lg bg-success-light text-success rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                            <i class="fe fe-calendar fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted fw-normal mb-1">This Week Sales</h6>
                            <h4 class="mb-0 fw-bold text-dark">৳{{ number_format($thisWeeksSalesRevenue, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-12">
                <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="avatar avatar-lg bg-success-light text-success rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                            <i class="fe fe-bar-chart-2 fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted fw-normal mb-1">This Month Sales</h6>
                            <h4 class="mb-0 fw-bold text-dark">৳{{ number_format($thisMonthsSalesRevenue, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-12">
                <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="avatar avatar-lg bg-success-light text-success rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                            <i class="fe fe-award fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted fw-normal mb-1">This Year Sales</h6>
                            <h4 class="mb-0 fw-bold text-dark">৳{{ number_format($thisYearsSalesRevenue, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase Overview Section -->
    <div class="mb-4">
        <h5 class="fw-bold text-dark mb-3"><i class="fe fe-shopping-bag me-2 text-info"></i>Purchases Overview</h5>
        <div class="row g-3">
            <div class="col-xl-3 col-md-6 col-12">
                <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="avatar avatar-lg bg-info-light text-info rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                            <i class="fe fe-shopping-cart fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted fw-normal mb-1">Today's Purchase</h6>
                            <h4 class="mb-0 fw-bold text-dark">৳{{ number_format($todaysPurchaseRevenue, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-12">
                <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="avatar avatar-lg bg-info-light text-info rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                            <i class="fe fe-calendar fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted fw-normal mb-1">This Week Purchase</h6>
                            <h4 class="mb-0 fw-bold text-dark">৳{{ number_format($thisWeeksPurchaseRevenue, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-12">
                <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="avatar avatar-lg bg-info-light text-info rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                            <i class="fe fe-file-text fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted fw-normal mb-1">This Month Purchase</h6>
                            <h4 class="mb-0 fw-bold text-dark">৳{{ number_format($thisMonthsPurchaseRevenue, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-12">
                <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="avatar avatar-lg bg-info-light text-info rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                            <i class="fe fe-archive fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted fw-normal mb-1">This Year Purchase</h6>
                            <h4 class="mb-0 fw-bold text-dark">৳{{ number_format($thisYearsPurchaseRevenue, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Expenses Overview Section -->
    <div class="mb-4">
        <h5 class="fw-bold text-dark mb-3"><i class="fe fe-file-minus me-2 text-danger"></i>Daily Expenses</h5>
        <div class="row g-3">
            <div class="col-xl-3 col-md-6 col-12">
                <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="avatar avatar-lg bg-danger-light text-danger rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                            <i class="fe fe-credit-card fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted fw-normal mb-1">Today's Expense</h6>
                            <h4 class="mb-0 fw-bold text-dark">৳{{ number_format($todaysExpense, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-12">
                <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="avatar avatar-lg bg-danger-light text-danger rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                            <i class="fe fe-calendar fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted fw-normal mb-1">This Week Expense</h6>
                            <h4 class="mb-0 fw-bold text-dark">৳{{ number_format($thisWeeksExpense, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-12">
                <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="avatar avatar-lg bg-danger-light text-danger rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                            <i class="fe fe-pie-chart fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted fw-normal mb-1">This Month Expense</h6>
                            <h4 class="mb-0 fw-bold text-dark">৳{{ number_format($thisMonthsExpense, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-12">
                <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="avatar avatar-lg bg-danger-light text-danger rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                            <i class="fe fe-trending-down fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted fw-normal mb-1">This Year Expense</h6>
                            <h4 class="mb-0 fw-bold text-dark">৳{{ number_format($thisYearsExpense, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts & Analytics Section -->
    <div class="row g-3 mb-4">
        <!-- Monthly Sales Chart -->
        <div class="col-xl-7 col-12 d-flex">
            <div class="card border-0 shadow-sm rounded-3 flex-fill">
                <div class="card-header bg-white py-3 border-bottom border-light">
                    <h6 class="fw-bold text-dark mb-0"><i class="fe fe-bar-chart-2 me-2 text-primary"></i>Monthly Sales Revenue ({{ date('Y') }})</h6>
                </div>
                <div class="card-body">
                    <div id="monthly_sales_chart" style="min-height: 320px;"></div>
                </div>
            </div>
        </div>

        <!-- Yearly Sales Chart -->
        <div class="col-xl-5 col-12 d-flex">
            <div class="card border-0 shadow-sm rounded-3 flex-fill">
                <div class="card-header bg-white py-3 border-bottom border-light">
                    <h6 class="fw-bold text-dark mb-0"><i class="fe fe-trending-up me-2 text-success"></i>Yearly Sales Revenue</h6>
                </div>
                <div class="card-body">
                    <div id="yearly_sales_chart" style="min-height: 320px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Project Analytics Charts Section -->
    <div class="row g-3 mb-4">
        <!-- Project Status Breakdown Donut Chart -->
        <div class="col-xl-5 col-12 d-flex">
            <div class="card border-0 shadow-sm rounded-3 flex-fill">
                <div class="card-header bg-white py-3 border-bottom border-light">
                    <h6 class="fw-bold text-dark mb-0"><i class="fe fe-pie-chart me-2 text-info"></i>Projects by Status</h6>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div id="project_status_chart" style="min-height: 320px; width: 100%;"></div>
                </div>
            </div>
        </div>

        <!-- Project Budget vs Actual Costs Bar Chart -->
        <div class="col-xl-7 col-12 d-flex">
            <div class="card border-0 shadow-sm rounded-3 flex-fill">
                <div class="card-header bg-white py-3 border-bottom border-light">
                    <h6 class="fw-bold text-dark mb-0"><i class="fe fe-layers me-2 text-warning"></i>Project Budget vs Actual Costs</h6>
                </div>
                <div class="card-body">
                    <div id="project_budget_cost_chart" style="min-height: 320px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Tables -->
    <div class="row g-3">
        <!-- Recent Sales -->
        <div class="col-xl-6 col-12">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-bottom border-light d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-dark mb-0"><i class="fe fe-shopping-cart me-2 text-success"></i>Recent Sales Orders</h6>
                    <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-primary rounded-2 px-3">View All</a>
                </div>
                <div class="card-body p-0" style="overflow: visible;">
                    <div class="table-responsive" style="overflow: visible !important;">
                        <table class="table table-hover table-custom align-middle mb-0">
                            <thead class="bg-light text-secondary fs-7 text-uppercase">
                                <tr>
                                    <th class="ps-3">Invoice / Customer</th>
                                    <th>Payable Amount</th>
                                    <th>Date</th>
                                    <th class="pe-3 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                @forelse ($recentSales ?? [] as $sale)
                                    <tr>
                                        <td class="ps-3">
                                            <span class="fw-bold text-dark d-block">INV-{{ $sale->invoice_no ?? $sale->id }}</span>
                                            <span class="text-muted small">{{ $sale->customer->name ?? 'Walk-in Customer' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-soft-primary px-3 py-1 rounded-pill fs-7">৳{{ number_format($sale->payble ?? 0, 2) }}</span>
                                        </td>
                                        <td>
                                            <span class="text-secondary small">{{ $sale->created_at ? $sale->created_at->format('d M, Y') : 'N/A' }}</span>
                                        </td>
                                        <td class="pe-3 text-end">
                                            <a href="{{ route('sales.invoice', $sale->id) }}" class="btn btn-sm btn-outline-secondary rounded-2 px-2 py-1">
                                                <i class="fe fe-eye"></i> Invoice
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted small">No recent sales records found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Projects -->
        <div class="col-xl-6 col-12">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-bottom border-light d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-dark mb-0"><i class="fe fe-layers me-2 text-info"></i>Recent Projects</h6>
                    <a href="{{ route('projects.index') }}" class="btn btn-sm btn-outline-primary rounded-2 px-3">View All</a>
                </div>
                <div class="card-body p-0" style="overflow: visible;">
                    <div class="table-responsive" style="overflow: visible !important;">
                        <table class="table table-hover table-custom align-middle mb-0">
                            <thead class="bg-light text-secondary fs-7 text-uppercase">
                                <tr>
                                    <th class="ps-3">Project Name</th>
                                    <th>Client Name</th>
                                    <th>Budget</th>
                                    <th class="pe-3 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                @forelse ($recentProjects ?? [] as $proj)
                                    <tr>
                                        <td class="ps-3">
                                            <span class="fw-bold text-dark d-block">{{ $proj->project_name }}</span>
                                            <span class="badge badge-soft-info px-2 py-1 rounded-pill fs-7 text-capitalize">{{ str_replace('_', ' ', $proj->status) }}</span>
                                        </td>
                                        <td>
                                            <span class="text-dark small fw-semibold">{{ $proj->client->name ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-soft-success px-3 py-1 rounded-pill fs-7">৳{{ number_format($proj->budget ?? 0, 2) }}</span>
                                        </td>
                                        <td class="pe-3 text-end">
                                            <a href="{{ route('projects.show', $proj->id) }}" class="btn btn-sm btn-outline-secondary rounded-2 px-2 py-1">
                                                <i class="fe fe-eye"></i> Details
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted small">No recent project records found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Accounting: Recent Journal Vouchers & Connected Bank Accounts -->
    @if(auth()->check() && auth()->user()->hasRole(['Super Admin', 'Admin', 'admin']))
    <div class="row g-3 mb-4">
        <!-- Recent Journal Vouchers -->
        <div class="col-xl-8 col-12 d-flex">
            <div class="card border-0 shadow-sm rounded-3 flex-fill">
                <div class="card-header bg-white py-3 border-bottom border-light d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-dark mb-0"><i class="fe fe-file-text me-2 text-primary"></i>Recent Journal Vouchers</h6>
                    <a href="{{ route('journal-entries.index') }}" class="btn btn-sm btn-outline-primary rounded-2 px-3">View All Vouchers</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-custom align-middle mb-0">
                            <thead class="bg-light text-secondary fs-7 text-uppercase">
                                <tr>
                                    <th class="ps-3">Voucher #</th>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Total Debit/Credit</th>
                                    <th>Status</th>
                                    <th class="pe-3 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                @forelse ($recentJournalEntries ?? [] as $entry)
                                    <tr>
                                        <td class="ps-3 fw-bold text-primary">{{ $entry->journal_number }}</td>
                                        <td>{{ \Carbon\Carbon::parse($entry->entry_date)->format('M d, Y') }}</td>
                                        <td class="text-truncate" style="max-width: 200px;">{{ $entry->description ?? 'N/A' }}</td>
                                        <td class="fw-bold text-dark">৳{{ number_format($entry->total_debit, 2) }}</td>
                                        <td>
                                            <span class="badge {{ $entry->status == 'posted' ? 'badge-soft-success' : 'badge-soft-warning' }} px-2 py-1 rounded-pill">
                                                {{ ucfirst($entry->status) }}
                                            </span>
                                        </td>
                                        <td class="pe-3 text-end">
                                            <a href="{{ route('journal-entries.show', $entry->id) }}" class="btn btn-sm btn-outline-secondary rounded-2 px-2 py-1">
                                                <i class="fe fe-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted small">No journal vouchers posted yet</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Connected Bank Accounts -->
        <div class="col-xl-4 col-12 d-flex">
            <div class="card border-0 shadow-sm rounded-3 flex-fill">
                <div class="card-header bg-white py-3 border-bottom border-light d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-dark mb-0"><i class="fe fe-credit-card me-2 text-info"></i>Bank Account Balances</h6>
                    <a href="{{ route('chart-of-accounts.index') }}" class="btn btn-sm btn-outline-info rounded-2 px-2 py-1">COA</a>
                </div>
                <div class="card-body p-3">
                    @forelse($bankAccounts ?? [] as $bank)
                        <div class="d-flex justify-content-between align-items-center p-2 mb-2 bg-light rounded-3">
                            <div>
                                <h6 class="mb-0 fw-bold text-dark fs-7">{{ $bank->account_name }}</h6>
                                <span class="text-muted small">Code: {{ $bank->account_code }}</span>
                            </div>
                            <div class="text-end">
                                <span class="fw-bold text-success d-block">৳{{ number_format($bank->balance, 2) }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted small">No sub-bank accounts registered</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const monthlyRevData = @json($monthlyRevenue ?? []);
    const yearlyRevData = @json($yearlyRevenue ?? []);
    const projStatusData = @json($projectStatusCounts ?? []);
    const projNames = @json($projectChartNames ?? []);
    const projBudgets = @json($projectChartBudgets ?? []);
    const projCosts = @json($projectChartCosts ?? []);

    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const monthlyValues = months.map(m => monthlyRevData[m] || 0);

    const years = Object.keys(yearlyRevData).reverse();
    const yearlyValues = years.map(y => yearlyRevData[y] || 0);

    // 1. Monthly Sales Chart
    const monthlyEl = document.querySelector('#monthly_sales_chart');
    if (monthlyEl && typeof ApexCharts !== 'undefined') {
        new ApexCharts(monthlyEl, {
            series: [{ name: 'Sales Revenue (৳)', data: monthlyValues }],
            chart: { type: 'bar', height: 320, toolbar: { show: false } },
            colors: ['#7638ff'],
            plotOptions: { bar: { borderRadius: 6, columnWidth: '55%' } },
            dataLabels: { enabled: false },
            xaxis: { categories: months },
            yaxis: { labels: { formatter: val => '৳' + val.toLocaleString() } },
            tooltip: { y: { formatter: val => '৳' + val.toLocaleString() } }
        }).render();
    }

    // 2. Yearly Sales Chart
    const yearlyEl = document.querySelector('#yearly_sales_chart');
    if (yearlyEl && typeof ApexCharts !== 'undefined') {
        new ApexCharts(yearlyEl, {
            series: [{ name: 'Total Revenue (৳)', data: yearlyValues }],
            chart: { type: 'area', height: 320, toolbar: { show: false } },
            colors: ['#10b981'],
            stroke: { curve: 'smooth', width: 3 },
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.45, opacityTo: 0.05 } },
            xaxis: { categories: years },
            yaxis: { labels: { formatter: val => '৳' + val.toLocaleString() } },
            tooltip: { y: { formatter: val => '৳' + val.toLocaleString() } }
        }).render();
    }

    // 3. Project Status Donut Chart
    const projStatusEl = document.querySelector('#project_status_chart');
    if (projStatusEl && typeof ApexCharts !== 'undefined') {
        const statusLabels = Object.keys(projStatusData);
        const statusSeries = Object.values(projStatusData);

        new ApexCharts(projStatusEl, {
            series: statusSeries.length ? statusSeries : [1],
            labels: statusSeries.length ? statusLabels : ['No Projects'],
            chart: { type: 'donut', height: 320 },
            colors: ['#0dcaf0', '#198754', '#ffb000', '#dc3545'],
            legend: { position: 'bottom' },
            dataLabels: { enabled: true }
        }).render();
    }

    // 4. Project Budget vs Costs Bar Chart
    const projBudgetEl = document.querySelector('#project_budget_cost_chart');
    if (projBudgetEl && typeof ApexCharts !== 'undefined') {
        new ApexCharts(projBudgetEl, {
            series: [
                { name: 'Allocated Budget (৳)', data: projBudgets },
                { name: 'Actual Expenses (৳)', data: projCosts }
            ],
            chart: { type: 'bar', height: 320, toolbar: { show: false } },
            colors: ['#7638ff', '#ffb000'],
            plotOptions: { bar: { horizontal: false, columnWidth: '50%', borderRadius: 4 } },
            dataLabels: { enabled: false },
            xaxis: { categories: projNames.length ? projNames : ['Sample Project'] },
            yaxis: { labels: { formatter: val => '৳' + val.toLocaleString() } },
            tooltip: { y: { formatter: val => '৳' + val.toLocaleString() } }
        }).render();
    }
});
</script>
@endsection
