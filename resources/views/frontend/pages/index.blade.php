@extends('frontend.layouts.app')

@section('content')
@php
    $netToday = ($todaysSalesRevenue ?? 0) - ($todaysPurchaseRevenue ?? 0) - ($todaysExpense ?? 0);
    $netMonth = ($thisMonthsSalesRevenue ?? 0) - ($thisMonthsPurchaseRevenue ?? 0) - ($thisMonthsExpense ?? 0);
    $netYear  = ($thisYearsSalesRevenue ?? 0) - ($thisYearsPurchaseRevenue ?? 0) - ($thisYearsExpense ?? 0);

    $user = auth()->user();
    $userName = $user->name ?? 'Admin';
    $appName = config('app.name', 'Inoodex Inventory');
@endphp

<div class="dash-body dreams-pos-theme premium-dashboard">

    <!-- 1. HEADER WELCOME & ALERT SECTION -->
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h2 class="dp-welcome-title mb-0">Welcome, {{ $userName }}</h2>
                <span class="dp-live-badge"><span class="dp-pulse-dot"></span> Live Analytics</span>
            </div>
            <p class="dp-welcome-sub mb-0">You have <b class="text-warning">{{ number_format($todayOrdersCount ?? 0) }} Orders</b>, Today</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="dp-date-badge">
                <i class="fe fe-calendar me-1"></i> {{ now()->subDays(6)->format('d/m/Y') }} - {{ now()->format('d/m/Y') }}
            </div>
        </div>
    </div>

    <!-- 2. ULTRA-PREMIUM 4 QUICK ACTION CARDS WITH MINI GRAPH SPARKLINES -->
    <div class="row g-3 mb-4">
        <!-- Action 1: Add Sales -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dp-action-card qa-card-orange h-100">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <span class="qa-tag">Sales Metric</span>
                        <h4 class="qa-val mb-1">৳{{ number_format($thisMonthsSalesRevenue ?? 0, 0) }}</h4>
                        <span class="qa-subtext">This Month Sales</span>
                    </div>
                    <div class="qa-icon-box ib-orange"><i class="fe fe-file-text"></i></div>
                </div>
                <!-- Mini Sparkline Graph -->
                <div id="sparkline_sales" class="qa-sparkline-wrap"></div>
                <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top border-white border-opacity-10">
                    <span class="qa-growth-badge gb-orange">{{ $salesGrowthPct >= 0 ? '+'.$salesGrowthPct : $salesGrowthPct }}% vs Mo</span>
                    <a href="{{ route('sales.create') }}" class="dp-btn-glow btn-orange">
                        <i class="fe fe-plus me-1"></i> Add Sales
                    </a>
                </div>
            </div>
        </div>

        <!-- Action 2: Add Purchase -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dp-action-card qa-card-teal h-100">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <span class="qa-tag">Purchase Metric</span>
                        <h4 class="qa-val mb-1">৳{{ number_format($thisMonthsPurchaseRevenue ?? 0, 0) }}</h4>
                        <span class="qa-subtext">This Month Purchase</span>
                    </div>
                    <div class="qa-icon-box ib-teal"><i class="fe fe-shopping-bag"></i></div>
                </div>
                <!-- Mini Sparkline Graph -->
                <div id="sparkline_purchase" class="qa-sparkline-wrap"></div>
                <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top border-white border-opacity-10">
                    <span class="qa-growth-badge gb-teal">{{ $purchaseGrowthPct >= 0 ? '+'.$purchaseGrowthPct : $purchaseGrowthPct }}% vs Mo</span>
                    <a href="{{ route('purchase.create') }}" class="dp-btn-glow btn-teal">
                        <i class="fe fe-plus me-1"></i> Add Purchase
                    </a>
                </div>
            </div>
        </div>

        <!-- Action 3: Add Service -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dp-action-card qa-card-sky h-100">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <span class="qa-tag">Service Jobs</span>
                        <h4 class="qa-val mb-1">{{ number_format($totalProjects ?? 0) }} Jobs</h4>
                        <span class="qa-subtext">Active Services</span>
                    </div>
                    <div class="qa-icon-box ib-sky"><i class="fe fe-layers"></i></div>
                </div>
                <!-- Mini Sparkline Graph -->
                <div id="sparkline_service" class="qa-sparkline-wrap"></div>
                <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top border-white border-opacity-10">
                    <span class="qa-growth-badge gb-sky">Active Orders</span>
                    <a href="{{ route('service.create') }}" class="dp-btn-glow btn-sky">
                        <i class="fe fe-plus me-1"></i> Add Service
                    </a>
                </div>
            </div>
        </div>

        <!-- Action 4: Add Expense -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dp-action-card qa-card-rose h-100">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <span class="qa-tag">Expenses</span>
                        <h4 class="qa-val mb-1">৳{{ number_format($thisMonthsExpense ?? 0, 0) }}</h4>
                        <span class="qa-subtext">This Month Expense</span>
                    </div>
                    <div class="qa-icon-box ib-rose"><i class="fe fe-file-minus"></i></div>
                </div>
                <!-- Mini Sparkline Graph -->
                <div id="sparkline_expense" class="qa-sparkline-wrap"></div>
                <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top border-white border-opacity-10">
                    <span class="qa-growth-badge gb-rose">{{ $expenseGrowthPct >= 0 ? '+'.$expenseGrowthPct : $expenseGrowthPct }}% vs Mo</span>
                    <a href="{{ route('dailyExpenses.create') }}" class="dp-btn-glow btn-rose">
                        <i class="fe fe-plus me-1"></i> Add Expense
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Alert Banner -->
    @if(isset($lowStockProducts) && count($lowStockProducts) > 0)
    <div class="alert dp-alert-banner alert-dismissible fade show d-flex align-items-center justify-content-between p-3 mb-4" role="alert">
        <div class="d-flex align-items-center gap-2">
            <span class="dp-alert-icon"><i class="fe fe-alert-circle"></i></span>
            <span class="fs-sm">
                Your Product <b class="text-dark">{{ $lowStockProducts->first()->name ?? 'Item' }}</b> is running Low, already below {{ $lowStockProducts->first()->inventory->qty ?? 5 }} Pcs.
                <a href="{{ route('products.index') }}" class="text-warning text-decoration-underline ms-1 fw-bold">Add Stock</a>
            </span>
        </div>
        <button type="button" class="btn-close btn-close-white ms-auto shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- 3. SECONDARY 4 METRIC CARDS ROW -->
    <div class="row g-3 mb-4">
        <!-- Profit -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dp-metric-card">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <h4 class="dp-metric-val">৳{{ number_format($netMonth, 0) }}</h4>
                        <span class="dp-metric-sub">Net Profit</span>
                    </div>
                    <div class="dp-m-icon-bg"><i class="fe fe-trending-up"></i></div>
                </div>
                <div class="d-flex align-items-center justify-content-between mt-3 pt-2 border-top border-secondary border-opacity-25">
                    <span class="{{ $salesGrowthPct >= 0 ? 'dp-badge-green' : 'dp-badge-red' }}"><i class="fe fe-trending-up"></i> {{ $salesGrowthPct >= 0 ? '+'.$salesGrowthPct : $salesGrowthPct }}% vs Last Month</span>
                    <a href="{{ route('sales.index') }}" class="dp-view-link">View All</a>
                </div>
            </div>
        </div>

        <!-- Invoice Due -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dp-metric-card">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <h4 class="dp-metric-val">৳{{ number_format($totalInvoiceDue ?? 0, 0) }}</h4>
                        <span class="dp-metric-sub">Invoice Due</span>
                    </div>
                    <div class="dp-m-icon-bg"><i class="fe fe-clock"></i></div>
                </div>
                <div class="d-flex align-items-center justify-content-between mt-3 pt-2 border-top border-secondary border-opacity-25">
                    <span class="dp-badge-green"><i class="fe fe-check-circle"></i> Active Pending</span>
                    <a href="{{ route('sales.index') }}" class="dp-view-link">View All</a>
                </div>
            </div>
        </div>

        <!-- Total Expenses -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dp-metric-card">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <h4 class="dp-metric-val">৳{{ number_format($thisMonthsExpense ?? 0, 0) }}</h4>
                        <span class="dp-metric-sub">Total Expenses</span>
                    </div>
                    <div class="dp-m-icon-bg"><i class="fe fe-dollar-sign"></i></div>
                </div>
                <div class="d-flex align-items-center justify-content-between mt-3 pt-2 border-top border-secondary border-opacity-25">
                    <span class="{{ $expenseGrowthPct <= 0 ? 'dp-badge-green' : 'dp-badge-red' }}"><i class="fe fe-trending-up"></i> {{ $expenseGrowthPct >= 0 ? '+'.$expenseGrowthPct : $expenseGrowthPct }}% vs Last Month</span>
                    <a href="{{ route('dailyExpenses.index') }}" class="dp-view-link">View All</a>
                </div>
            </div>
        </div>

        <!-- Total Payment Returns -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dp-metric-card">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <h4 class="dp-metric-val">৳{{ number_format($totalSalesReturnAmount ?? 0, 0) }}</h4>
                        <span class="dp-metric-sub">Total Payment Returns</span>
                    </div>
                    <div class="dp-m-icon-bg"><i class="fe fe-refresh-cw"></i></div>
                </div>
                <div class="d-flex align-items-center justify-content-between mt-3 pt-2 border-top border-secondary border-opacity-25">
                    <span class="dp-badge-red"><i class="fe fe-rotate-ccw"></i> Refunds Logged</span>
                    <a href="{{ route('returns.index') }}" class="dp-view-link">View All</a>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. MIDDLE SECTION: CHART + OVERALL INFO (2 COLUMNS) -->
    <div class="row g-3 mb-4">
        <!-- Sales & Purchase Chart -->
        <div class="col-12 col-lg-8">
            <div class="dp-panel-card h-100">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 pb-2 border-bottom border-secondary border-opacity-25">
                    <div class="d-flex align-items-center gap-2">
                        <span class="dp-panel-icon"><i class="fe fe-bar-chart-2"></i></span>
                        <h5 class="dp-panel-title mb-0">Sales &amp; Purchase Overview</h5>
                    </div>
                    <div class="dp-tabs-wrap">
                        <button class="dp-tab-btn active">1M</button>
                        <button class="dp-tab-btn">1Y</button>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-4 mb-3 fs-xs">
                    <div class="d-flex align-items-center gap-2">
                        <span class="dp-legend-dot bg-warning"></span>
                        <span class="text-secondary">Total Purchase (<b>৳{{ number_format($thisMonthsPurchaseRevenue ?? 0, 0) }}</b>)</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="dp-legend-dot bg-orange"></span>
                        <span class="text-secondary">Total Sales (<b>৳{{ number_format($thisMonthsSalesRevenue ?? 0, 0) }}</b>)</span>
                    </div>
                </div>

                <div id="sales_purchase_chart" class="dp-chart-area" style="min-height: 280px;"></div>
            </div>
        </div>

        <!-- Overall Info & Customers Overview -->
        <div class="col-12 col-lg-4">
            <div class="d-flex flex-column gap-3 h-100">
                <!-- Overall Information -->
                <div class="dp-panel-card">
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom border-secondary border-opacity-25">
                        <span class="dp-panel-icon"><i class="fe fe-info"></i></span>
                        <h5 class="dp-panel-title mb-0">Overall Information</h5>
                    </div>
                    <div class="row g-2 text-center">
                        <div class="col-4">
                            <div class="dp-sub-kpi">
                                <i class="fe fe-truck text-info mb-1"></i>
                                <span class="dp-sk-label">Suppliers</span>
                                <h5 class="dp-sk-val mb-0">{{ number_format($totalVendors ?? 0) }}</h5>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="dp-sub-kpi">
                                <i class="fe fe-users text-warning mb-1"></i>
                                <span class="dp-sk-label">Customer</span>
                                <h5 class="dp-sk-val mb-0">{{ number_format($totalCustomers ?? 0) }}</h5>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="dp-sub-kpi">
                                <i class="fe fe-shopping-bag text-teal mb-1"></i>
                                <span class="dp-sk-label">Orders</span>
                                <h5 class="dp-sk-val mb-0">{{ number_format($totalSalesCount ?? 0) }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customers Overview -->
                <div class="dp-panel-card flex-grow-1">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-secondary border-opacity-25">
                        <h5 class="dp-panel-title mb-0">Customers Overview</h5>
                    </div>
                    <div class="d-flex align-items-center justify-content-around my-2">
                        <div id="customers_donut_chart" style="width: 110px; height: 110px;"></div>
                        <div class="d-flex flex-column gap-2">
                            <div>
                                <h5 class="mb-0 fw-bold">{{ number_format($newCustomersCount ?? 0) }}</h5>
                                <span class="text-secondary fs-xs">First Time</span>
                                <span class="dp-badge-green ms-1">{{ $newCustomerPct ?? 0 }}%</span>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">{{ number_format($returningCustomersCount ?? 0) }}</h5>
                                <span class="text-secondary fs-xs">Return</span>
                                <span class="dp-badge-green ms-1">{{ $returningCustomerPct ?? 0 }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. 3-COLUMN SECTION (TOP SELLING PRODUCTS, LOW STOCK, RECENT SALES) -->
    <div class="row g-3 mb-4">
        <!-- Top Selling Products -->
        <div class="col-12 col-lg-4">
            <div class="dp-panel-card h-100">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-secondary border-opacity-25">
                    <div class="d-flex align-items-center gap-2">
                        <span class="dp-panel-icon bg-rose-soft text-rose"><i class="fe fe-award"></i></span>
                        <h5 class="dp-panel-title mb-0">Top Selling Products</h5>
                    </div>
                    <span class="badge dp-badge-dark">Top</span>
                </div>
                <div class="dp-list-wrap">
                    @forelse($topProducts as $tp)
                    <div class="dp-list-item">
                        <div class="dp-item-img"><i class="fe fe-box"></i></div>
                        <div class="flex-grow-1 min-w-0">
                            <h6 class="dp-item-title mb-0 truncate">{{ $tp->product_name }}</h6>
                            <span class="dp-item-sub">৳{{ number_format($tp->total_revenue, 0) }} · {{ $tp->total_qty }}+ Sales</span>
                        </div>
                    </div>
                    @empty
                    <p class="text-secondary text-center py-4 fs-xs">No products sold yet</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Low Stock Products -->
        <div class="col-12 col-lg-4">
            <div class="dp-panel-card h-100">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-secondary border-opacity-25">
                    <div class="d-flex align-items-center gap-2">
                        <span class="dp-panel-icon bg-warning-soft text-warning"><i class="fe fe-alert-triangle"></i></span>
                        <h5 class="dp-panel-title mb-0">Low Stock Products</h5>
                    </div>
                    <a href="{{ route('products.index') }}" class="dp-view-link">View All</a>
                </div>
                <div class="dp-list-wrap">
                    @forelse($lowStockProducts as $lp)
                    <div class="dp-list-item">
                        <div class="dp-item-img bg-warning-soft text-warning"><i class="fe fe-package"></i></div>
                        <div class="flex-grow-1 min-w-0">
                            <h6 class="dp-item-title mb-0 truncate">{{ $lp->name }}</h6>
                            <span class="dp-item-sub">ID: #{{ $lp->id }}</span>
                        </div>
                        <div class="text-end">
                            <span class="dp-stock-text">In Stock</span>
                            <b class="text-danger d-block fs-xs">{{ (int) ($lp->inventory->qty ?? 5) }}</b>
                        </div>
                    </div>
                    @empty
                    <p class="text-secondary text-center py-4 fs-xs">No low stock items</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Sales -->
        <div class="col-12 col-lg-4">
            <div class="dp-panel-card h-100">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-secondary border-opacity-25">
                    <div class="d-flex align-items-center gap-2">
                        <span class="dp-panel-icon bg-emerald-soft text-emerald"><i class="fe fe-shopping-cart"></i></span>
                        <h5 class="dp-panel-title mb-0">Recent Sales</h5>
                    </div>
                    <a href="{{ route('sales.index') }}" class="dp-view-link">View All</a>
                </div>
                <div class="dp-list-wrap">
                    @forelse($recentSales as $rs)
                    <div class="dp-list-item">
                        <div class="dp-item-img bg-emerald-soft text-emerald"><i class="fe fe-file"></i></div>
                        <div class="flex-grow-1 min-w-0">
                            <h6 class="dp-item-title mb-0 truncate">Order #{{ $rs->order_no ?? $rs->id }}</h6>
                            <span class="dp-item-sub">Sales · ৳{{ number_format($rs->payble, 0) }}</span>
                        </div>
                        <span class="dp-status-badge sb-completed">Completed</span>
                    </div>
                    @empty
                    <p class="text-secondary text-center py-4 fs-xs">No recent sales</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- 6. 2-COLUMN SECTION (SALES STATICS CHART & RECENT TRANSACTIONS TABLE) -->
    <div class="row g-3 mb-4">
        <!-- Sales Statics -->
        <div class="col-12 col-lg-6">
            <div class="dp-panel-card h-100">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-secondary border-opacity-25">
                    <div class="d-flex align-items-center gap-2">
                        <span class="dp-panel-icon bg-warning-soft text-warning"><i class="fe fe-bar-chart"></i></span>
                        <h5 class="dp-panel-title mb-0">Sales Statics</h5>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-4 mb-3 fs-xs">
                    <div>
                        <span class="text-emerald fw-bold me-1">৳{{ number_format($thisMonthsSalesRevenue ?? 0, 0) }}</span>
                        <span class="dp-badge-green">{{ $salesGrowthPct >= 0 ? '+'.$salesGrowthPct : $salesGrowthPct }}%</span>
                        <span class="text-secondary d-block">Revenue</span>
                    </div>
                    <div>
                        <span class="text-orange fw-bold me-1">৳{{ number_format($thisMonthsExpense ?? 0, 0) }}</span>
                        <span class="dp-badge-red">{{ $expenseGrowthPct >= 0 ? '+'.$expenseGrowthPct : $expenseGrowthPct }}%</span>
                        <span class="text-secondary d-block">Expense</span>
                    </div>
                </div>

                <div id="sales_statics_chart" style="min-height: 250px;"></div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="col-12 col-lg-6">
            <div class="dp-panel-card h-100">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-secondary border-opacity-25">
                    <div class="d-flex align-items-center gap-2">
                        <span class="dp-panel-icon bg-sky-soft text-sky"><i class="fe fe-list"></i></span>
                        <h5 class="dp-panel-title mb-0">Recent Transactions</h5>
                    </div>
                    <a href="{{ route('sales.index') }}" class="dp-view-link">View All</a>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table dp-table mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Status</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTransactions as $rt)
                            <tr>
                                <td class="fs-xs text-secondary">{{ $rt->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="dp-user-avatar">{{ strtoupper(substr($rt->customer->name ?? 'C', 0, 1)) }}</div>
                                        <div>
                                            <b class="d-block fs-xs text-white">{{ $rt->customer->name ?? 'Walk-in Customer' }}</b>
                                            <span class="text-secondary fs-xxs">#{{ $rt->id }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="dp-status-badge sb-completed">Completed</span></td>
                                <td class="text-end fw-bold text-white fs-xs">৳{{ number_format($rt->payble, 0) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-secondary py-3 fs-xs">No transactions available</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- 7. 3-COLUMN SECTION (TOP CUSTOMERS, TOP CATEGORIES, ORDER STATISTICS) -->
    <div class="row g-3 mb-4">
        <!-- Top Customers -->
        <div class="col-12 col-lg-4">
            <div class="dp-panel-card h-100">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-secondary border-opacity-25">
                    <div class="d-flex align-items-center gap-2">
                        <span class="dp-panel-icon bg-violet-soft text-violet"><i class="fe fe-users"></i></span>
                        <h5 class="dp-panel-title mb-0">Top Customers</h5>
                    </div>
                    <a href="{{ route('customers.index') }}" class="dp-view-link">View All</a>
                </div>
                <div class="dp-list-wrap">
                    @forelse($topCustomers as $tc)
                    <div class="dp-list-item">
                        <div class="dp-user-avatar lg">{{ strtoupper(substr($tc->name ?? 'C', 0, 1)) }}</div>
                        <div class="flex-grow-1 min-w-0">
                            <h6 class="dp-item-title mb-0 truncate">{{ $tc->name }}</h6>
                            <span class="dp-item-sub">{{ $tc->phone ?? 'Customer' }}</span>
                        </div>
                    </div>
                    @empty
                    <p class="text-secondary text-center py-4 fs-xs">No customer data</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Top Categories -->
        <div class="col-12 col-lg-4">
            <div class="dp-panel-card h-100">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-secondary border-opacity-25">
                    <div class="d-flex align-items-center gap-2">
                        <span class="dp-panel-icon bg-amber-soft text-amber"><i class="fe fe-pie-chart"></i></span>
                        <h5 class="dp-panel-title mb-0">Top Categories</h5>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-around my-3">
                    <div id="category_donut_chart" style="width: 120px; height: 120px;"></div>
                    <div class="fs-xs space-y-1">
                        @forelse($expenseBreakdown->take(3) as $eb)
                        <div><span class="dp-legend-dot bg-warning"></span> {{ $eb->category_name }} <b class="d-block text-white">৳{{ number_format($eb->total, 0) }}</b></div>
                        @empty
                        <div><span class="dp-legend-dot bg-warning"></span> General <b class="d-block text-white">Categories</b></div>
                        @endforelse
                    </div>
                </div>

                <div class="pt-2 border-top border-secondary border-opacity-25 fs-xs text-secondary">
                    <div class="d-flex justify-content-between py-1">
                        <span>Total Number Of Categories</span>
                        <b class="text-white">{{ $totalCategories ?? 0 }}</b>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span>Total Number Of Products</span>
                        <b class="text-white">{{ $totalProducts ?? 0 }}</b>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Statistics Heatmap -->
        <div class="col-12 col-lg-4">
            <div class="dp-panel-card h-100">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-secondary border-opacity-25">
                    <div class="d-flex align-items-center gap-2">
                        <span class="dp-panel-icon bg-sky-soft text-sky"><i class="fe fe-grid"></i></span>
                        <h5 class="dp-panel-title mb-0">Order Statistics</h5>
                    </div>
                </div>
                <div id="order_statistics_heatmap" style="min-height: 220px;"></div>
            </div>
        </div>
    </div>

    <!-- 8. FOOTER -->
    <div class="dp-footer d-flex flex-column flex-sm-row align-items-center justify-content-between gap-2 mt-4 pt-3 border-top border-secondary border-opacity-25 fs-xs text-secondary">
        <span>2014 - {{ date('Y') }} &copy; <b>{{ $appName }}</b>. All Rights Reserved.</span>
        <span>Designed &amp; Developed By <b class="text-white">Inoodex</b></span>
    </div>

</div>

<!-- ULTRA PREMIUM CUSTOM STYLING -->
<style>
    .dreams-pos-theme {
        --dp-bg: #f4f6fb;
        --dp-card-bg: #ffffff;
        --dp-border: rgba(15, 23, 42, 0.08);
        --dp-text: #0f172a;
        --dp-subtext: #64748b;
        --dp-soft: #f1f5f9;
        --dp-tabs-bg: #e2e8f0;
        --dp-tab-active: #ffffff;
        --dp-heading: #0f172a;
        --dp-muted: #475569;
        --dp-icon-bg: rgba(15, 23, 42, 0.05);
        --dp-shadow: 0 8px 22px rgba(15, 23, 42, 0.06);
        --dp-shadow-lg: 0 14px 32px rgba(15, 23, 42, 0.10);
        --dp-shadow-xl: 0 18px 40px rgba(15, 23, 42, 0.14);
        --dp-grad-end: rgba(255, 255, 255, 0.92);
        --dp-grid: rgba(15, 23, 42, 0.08);
        --dp-label: #64748b;
        --dp-green: #059669;
        --dp-red: #dc2626;
        --dp-orange-ic: #ea580c;
        --dp-teal-ic: #0d9488;
        --dp-sky-ic: #0284c7;
        --dp-rose-ic: #e11d48;
        --dp-violet-ic: #7c3aed;
        --dp-orange-txt: #ea580c;
        --dp-teal-txt: #0f766e;
        --dp-sky-txt: #0369a1;
        --dp-rose-txt: #be123c;

        font-family: 'Inter', sans-serif;
        color: var(--dp-text);
        padding: clamp(14px, 2vw, 24px);
        padding-top: 5px;
    }

    html[data-layout-mode="dark"] .dreams-pos-theme {
        --dp-bg: #0f172a;
        --dp-card-bg: #151e32;
        --dp-border: rgba(255, 255, 255, 0.08);
        --dp-text: #f8fafc;
        --dp-subtext: #94a3b8;
        --dp-soft: #0b1120;
        --dp-tabs-bg: #0f172a;
        --dp-tab-active: #334155;
        --dp-heading: #ffffff;
        --dp-muted: #cbd5e1;
        --dp-icon-bg: rgba(255, 255, 255, 0.06);
        --dp-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        --dp-shadow-lg: 0 10px 30px rgba(0, 0, 0, 0.35);
        --dp-shadow-xl: 0 16px 36px rgba(0, 0, 0, 0.5);
        --dp-grad-end: rgba(15, 23, 42, 0.95);
        --dp-grid: rgba(255, 255, 255, 0.06);
        --dp-label: #94a3b8;
        --dp-green: #34d399;
        --dp-red: #f87171;
        --dp-orange-ic: #f97316;
        --dp-teal-ic: #2dd4bf;
        --dp-sky-ic: #38bdf8;
        --dp-rose-ic: #fb7185;
        --dp-violet-ic: #c4b5fd;
        --dp-orange-txt: #ffedd5;
        --dp-teal-txt: #ccfbf1;
        --dp-sky-txt: #e0f2fe;
        --dp-rose-txt: #ffe4e6;
    }

    body:has(.dreams-pos-theme) {
        background-color: #f4f6fb !important;
    }

    html[data-layout-mode="dark"] body:has(.dreams-pos-theme) {
        background-color: #0f172a !important;
    }

    .dp-welcome-title { font-weight: 800; font-size: 1.65rem; color: var(--dp-heading); letter-spacing: -0.5px; }
    .dp-welcome-sub { font-size: 0.85rem; color: var(--dp-subtext); }

    .dp-live-badge {
        background: rgba(16, 185, 129, 0.12);
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.25);
        font-size: 0.7rem;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .dp-pulse-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: #34d399;
        box-shadow: 0 0 8px #34d399;
        animation: pulse 1.8s infinite;
    }
    @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.4; } 100% { opacity: 1; } }

    .dp-date-badge {
        background: var(--dp-card-bg);
        border: 1px solid var(--dp-border);
        border-radius: 10px;
        padding: 8px 16px;
        font-size: 0.8rem;
        color: var(--dp-muted);
        font-weight: 600;
        box-shadow: var(--dp-shadow);
    }

    /* ULTRA PREMIUM ACTION CARDS WITH MINI SPARKLINES */
    .dp-action-card {
        border-radius: 20px;
        padding: 18px;
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(12px);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        box-shadow: var(--dp-shadow-lg);
    }
    .dp-action-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--dp-shadow-xl);
    }

    .qa-card-orange {
        background: linear-gradient(135deg, rgba(249, 115, 22, 0.22) 0%, var(--dp-grad-end) 100%);
        border: 1px solid rgba(249, 115, 22, 0.4);
    }
    .qa-card-teal {
        background: linear-gradient(135deg, rgba(13, 148, 136, 0.22) 0%, var(--dp-grad-end) 100%);
        border: 1px solid rgba(13, 148, 136, 0.4);
    }
    .qa-card-sky {
        background: linear-gradient(135deg, rgba(56, 189, 248, 0.22) 0%, var(--dp-grad-end) 100%);
        border: 1px solid rgba(56, 189, 248, 0.4);
    }
    .qa-card-rose {
        background: linear-gradient(135deg, rgba(244, 63, 94, 0.22) 0%, var(--dp-grad-end) 100%);
        border: 1px solid rgba(244, 63, 94, 0.4);
    }

    .qa-tag { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; color: var(--dp-subtext); letter-spacing: 0.5px; }
    .qa-val { font-weight: 900; font-size: 1.45rem; color: var(--dp-heading); }
    .qa-subtext { font-size: 0.75rem; color: var(--dp-subtext); }

    .qa-icon-box {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
    }
    .ib-orange { background: rgba(249, 115, 22, 0.25); color: var(--dp-orange-ic); }
    .ib-teal { background: rgba(13, 148, 136, 0.25); color: var(--dp-teal-ic); }
    .ib-sky { background: rgba(56, 189, 248, 0.25); color: var(--dp-sky-ic); }
    .ib-rose { background: rgba(244, 63, 94, 0.25); color: var(--dp-rose-ic); }

    .qa-sparkline-wrap { height: 45px; margin-top: 5px; margin-bottom: -5px; }

    .qa-growth-badge { font-size: 0.7rem; font-weight: 700; padding: 2px 8px; border-radius: 6px; }
    .gb-orange { background: rgba(249, 115, 22, 0.2); color: var(--dp-orange-txt); }
    .gb-teal { background: rgba(13, 148, 136, 0.2); color: var(--dp-teal-txt); }
    .gb-sky { background: rgba(56, 189, 248, 0.2); color: var(--dp-sky-txt); }
    .gb-rose { background: rgba(244, 63, 94, 0.2); color: var(--dp-rose-txt); }

    .dp-btn-glow {
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 0.74rem;
        font-weight: 800;
        color: #fff;
        text-decoration: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        transition: transform .2s ease, filter .2s ease;
    }
    .dp-btn-glow:hover { transform: scale(1.05); color: #fff; filter: brightness(1.15); }
    .btn-orange { background: linear-gradient(135deg, #f97316, #ea580c); }
    .btn-teal { background: linear-gradient(135deg, #0d9488, #0f766e); }
    .btn-sky { background: linear-gradient(135deg, #0284c7, #0369a1); }
    .btn-rose { background: linear-gradient(135deg, #e11d48, #be123c); }

    /* METRIC CARDS & PANELS */
    .dp-metric-card, .dp-panel-card {
        background: var(--dp-card-bg);
        border: 1px solid var(--dp-border);
        border-radius: 18px;
        padding: 18px;
        box-shadow: var(--dp-shadow);
    }
    .dp-metric-val { font-weight: 900; font-size: 1.45rem; color: var(--dp-heading); margin-bottom: 2px; }
    .dp-metric-sub { font-size: 0.78rem; color: var(--dp-subtext); font-weight: 600; }
    .dp-m-icon-bg {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: var(--dp-icon-bg);
        color: var(--dp-heading);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }
    .dp-badge-green { background: rgba(16, 185, 129, 0.15); color: var(--dp-green); padding: 3px 8px; border-radius: 6px; font-size: 0.72rem; font-weight: 700; }
    .dp-badge-red { background: rgba(239, 68, 68, 0.15); color: var(--dp-red); padding: 3px 8px; border-radius: 6px; font-size: 0.72rem; font-weight: 700; }
    .dp-view-link { font-size: 0.75rem; color: var(--dp-subtext); text-decoration: underline; font-weight: 600; }

    .dp-panel-icon {
        width: 32px; height: 32px; border-radius: 8px; background: var(--dp-icon-bg); color: var(--dp-sky-ic); display: inline-flex; align-items: center; justify-content: center; font-size: 0.95rem;
    }
    .dp-panel-title { font-weight: 800; font-size: 0.95rem; color: var(--dp-heading); }
    .dp-tabs-wrap { display: flex; gap: 4px; background: var(--dp-tabs-bg); padding: 3px; border-radius: 8px; }
    .dp-tab-btn { background: transparent; border: none; color: var(--dp-subtext); font-size: 0.72rem; font-weight: 700; padding: 4px 10px; border-radius: 6px; }
    .dp-tab-btn.active { background: var(--dp-tab-active); color: var(--dp-heading); box-shadow: var(--dp-shadow); }

    .dp-sub-kpi { background: var(--dp-soft); border: 1px solid var(--dp-border); border-radius: 12px; padding: 12px 8px; }
    .dp-sk-label { font-size: 0.7rem; color: var(--dp-subtext); display: block; }
    .dp-sk-val { font-weight: 800; font-size: 1.1rem; color: var(--dp-heading); }

    .dp-list-wrap { display: flex; flex-direction: column; gap: 10px; }
    .dp-list-item { display: flex; align-items: center; gap: 12px; padding: 10px; border-radius: 12px; background: var(--dp-soft); border: 1px solid var(--dp-border); }
    .dp-list-item > .flex-grow-1 { flex: 1 1 auto; min-width: 0; }
    .dp-list-item > .text-end { flex-shrink: 0; white-space: nowrap; }
    .dp-item-img { width: 38px; height: 38px; border-radius: 10px; background: rgba(249, 115, 22, 0.15); color: var(--dp-orange-ic); display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
    .dp-item-title { font-weight: 700; font-size: 0.82rem; color: var(--dp-heading); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .dp-item-sub { font-size: 0.72rem; color: var(--dp-subtext); display: block; }
    .dp-stock-text { font-size: 0.6rem; color: var(--dp-subtext); display: block; text-transform: uppercase; letter-spacing: 0.02em; line-height: 1.2; }
    .dp-list-item .text-end { display: flex; flex-direction: column; align-items: flex-end; justify-content: center; gap: 1px; }

    .bg-rose-soft { background: rgba(244, 63, 94, 0.15); }
    .text-rose { color: var(--dp-rose-ic); }
    .bg-warning-soft { background: rgba(245, 158, 11, 0.15); }
    .bg-emerald-soft { background: rgba(16, 185, 129, 0.15); }
    .text-emerald { color: var(--dp-green); }
    .bg-sky-soft { background: rgba(56, 189, 248, 0.15); }
    .text-sky { color: var(--dp-sky-ic); }
    .bg-violet-soft { background: rgba(167, 139, 250, 0.15); }
    .text-violet { color: var(--dp-violet-ic); }
    .dp-badge-dark { background: var(--dp-soft); color: var(--dp-subtext); font-weight: 600; font-size: 0.7rem; }

    .dp-status-badge { padding: 3px 8px; border-radius: 999px; font-size: 0.68rem; font-weight: 700; }
    .sb-completed { background: rgba(16, 185, 129, 0.2); color: var(--dp-green); }

    .dp-table { --bs-table-bg: transparent; --bs-table-color: var(--dp-muted); }
    .dp-table th { font-size: 0.7rem; text-transform: uppercase; color: var(--dp-subtext); border-bottom-color: var(--dp-border); }
    .dp-table td { border-bottom-color: var(--dp-border); vertical-align: middle; }

    .dp-user-avatar { width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #6366f1, #3b82f6); color: #fff; font-weight: 800; font-size: 0.8rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .dp-user-avatar.lg { width: 38px; height: 38px; font-size: 0.9rem; }
    .truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .fs-xxs { font-size: 0.68rem; }

    /* Text color on light cards */
    .dreams-pos-theme .text-white { color: var(--dp-heading); }
    .dreams-pos-theme .dp-alert-banner .text-white { color: #fff; }
    .dreams-pos-theme .dp-btn-glow .text-white,
    .dreams-pos-theme .dp-user-avatar { color: #fff; }

    /* MOBILE RESPONSIVE POLISH */
    @media (max-width: 575.98px) {
        .dreams-pos-theme {
            padding: 12px 12px 20px;
        }
        .dp-welcome-title { font-size: 1.35rem; }
        .dp-live-badge { font-size: 0.62rem; padding: 2px 8px; }
        .dp-date-badge {
            font-size: 0.68rem;
            padding: 6px 10px;
            max-width: 100%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .qa-val { font-size: 1.25rem; }
        .dp-action-card, .dp-metric-card, .dp-panel-card { padding: 15px; }
        .dp-action-card .d-flex.justify-content-between,
        .dp-metric-card .d-flex.justify-content-between {
            flex-wrap: wrap;
            gap: 8px;
        }
        .dp-chart-legend,
        .d-flex.gap-4.mb-3.fs-xs { flex-wrap: wrap; gap: 6px 14px !important; }
        .dp-panel-card .d-flex.align-items-center.justify-content-around { flex-wrap: wrap; gap: 8px; }
        .dp-footer { text-align: center; gap: 4px !important; }
        .dp-metric-val { font-size: 1.3rem; }
        .dp-sk-val { font-size: 1rem; }
        .dp-tabs-wrap { padding: 2px; }
        .dp-tab-btn { font-size: 0.68rem; padding: 3px 8px; }
        .dp-table { white-space: nowrap; }
        .dp-table th, .dp-table td { padding: 0.45rem 0.3rem; font-size: 0.68rem; }
        .dp-table .dp-user-avatar { width: 26px; height: 26px; font-size: 0.7rem; }
        .dp-table td:nth-child(2) .fs-xs,
        .dp-table td:nth-child(2) .fs-xxs {
            display: block;
            max-width: 130px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .dp-list-item { gap: 10px; padding: 8px; }
        .dp-item-img { width: 34px; height: 34px; }
    }

    @media (min-width: 576px) and (max-width: 767.98px) {
        .dp-action-card .d-flex.justify-content-between,
        .dp-metric-card .d-flex.justify-content-between {
            flex-wrap: wrap;
            gap: 8px;
        }
    }
</style>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const monthlyRevData = @json(array_values($monthlyRevenue ?? []));
    const monthlyPurchData = @json(array_values($monthlyPurchase ?? []));
    const monthlyExpData = @json(array_values($monthlyExpense ?? []));
    const monthlyProjData = @json(array_values($monthlyProjects ?? []));
    const newCustPct = {{ $newCustomerPct ?? 50 }};
    const retCustPct = {{ $returningCustomerPct ?? 50 }};
    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    const days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

    let charts = [];

    function isDarkMode() {
        return document.documentElement.getAttribute('data-layout-mode') === 'dark';
    }

    function themeColors() {
        return {
            mode: isDarkMode() ? 'dark' : 'light',
            label: isDarkMode() ? '#94a3b8' : '#64748b',
            grid: isDarkMode() ? 'rgba(255,255,255,0.06)' : 'rgba(15,23,42,0.08)'
        };
    }

    function renderSparkline(elementId, dataArray, colorHex) {
        const el = document.querySelector(elementId);
        if (!el || typeof ApexCharts === 'undefined') return;
        const data = dataArray && dataArray.length ? dataArray : [10, 15, 8, 22, 18, 25, 30];
        const chart = new ApexCharts(el, {
            series: [{ data: data }],
            chart: {
                type: 'area',
                height: 45,
                sparkline: { enabled: true },
                background: 'transparent'
            },
            stroke: { curve: 'smooth', width: 2 },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.45,
                    opacityTo: 0.05,
                    stops: [0, 100]
                }
            },
            colors: [colorHex],
            tooltip: { enabled: false }
        });
        charts.push(chart);
    }

    function initCharts() {
        if (typeof ApexCharts === 'undefined') return;
        const tc = themeColors();

        // 4 ACTION CARDS MINI SPARKLINES
        renderSparkline('#sparkline_sales', monthlyRevData, '#f97316');
        renderSparkline('#sparkline_purchase', monthlyPurchData, '#2dd4bf');
        renderSparkline('#sparkline_service', monthlyProjData, '#38bdf8');
        renderSparkline('#sparkline_expense', monthlyExpData, '#fb7185');

        // 1. Sales & Purchase Stacked Bar Chart
        const spChartEl = document.querySelector('#sales_purchase_chart');
        if (spChartEl) {
            charts.push(new ApexCharts(spChartEl, {
                series: [{
                    name: 'Total Sales',
                    data: monthlyRevData.length ? monthlyRevData : [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
                }, {
                    name: 'Total Purchase',
                    data: monthlyPurchData.length ? monthlyPurchData : [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
                }],
                chart: {
                    type: 'bar',
                    height: 280,
                    stacked: true,
                    toolbar: { show: false },
                    background: 'transparent'
                },
                colors: ['#f97316', '#fbbf24'],
                plotOptions: {
                    bar: { columnWidth: '40%', borderRadius: 6 }
                },
                dataLabels: { enabled: false },
                legend: { show: false },
                grid: { borderColor: tc.grid },
                xaxis: {
                    categories: months,
                    labels: { style: { colors: tc.label, fontSize: '11px' } }
                },
                yaxis: { labels: { style: { colors: tc.label, fontSize: '11px' } } },
                theme: { mode: tc.mode }
            }));
        }

        // 2. Customers Donut Chart
        const donutEl = document.querySelector('#customers_donut_chart');
        if (donutEl) {
            charts.push(new ApexCharts(donutEl, {
                series: [newCustPct, retCustPct],
                chart: { type: 'donut', height: 110 },
                colors: ['#0d9488', '#f97316'],
                dataLabels: { enabled: false },
                legend: { show: false },
                stroke: { width: 0 }
            }));
        }

        // 3. Sales Statics Dual Chart
        const staticsEl = document.querySelector('#sales_statics_chart');
        if (staticsEl) {
            charts.push(new ApexCharts(staticsEl, {
                series: [{
                    name: 'Revenue',
                    data: monthlyRevData.length ? monthlyRevData : [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
                }, {
                    name: 'Expense',
                    data: monthlyExpData.length ? monthlyExpData.map(v => -Math.abs(v)) : [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
                }],
                chart: { type: 'bar', height: 250, toolbar: { show: false } },
                colors: ['#7c3aed', '#ea580c'],
                plotOptions: { bar: { columnWidth: '35%', borderRadius: 5, borderRadiusApplication: 'end', borderRadiusWhenStacked: 'last' } },
                fill: {
                    type: 'gradient',
                    gradient: {
                        type: 'vertical',
                        shadeIntensity: 1,
                        gradientToColors: ['#06b6d4', '#ef4444'],
                        inverseColors: false,
                        opacityFrom: 1,
                        opacityTo: 0.9,
                        stops: [0, 100]
                    }
                },
                dataLabels: { enabled: false },
                legend: { show: false },
                grid: { borderColor: tc.grid },
                xaxis: {
                    categories: months,
                    labels: { style: { colors: tc.label, fontSize: '10px' } }
                },
                yaxis: { labels: { style: { colors: tc.label, fontSize: '10px' } } }
            }));
        }

        // 4. Category Donut Chart
        const catDonutEl = document.querySelector('#category_donut_chart');
        if (catDonutEl) {
            charts.push(new ApexCharts(catDonutEl, {
                series: [45, 30, 25],
                chart: { type: 'donut', height: 120 },
                colors: ['#f59e0b', '#f97316', '#38bdf8'],
                dataLabels: { enabled: false },
                legend: { show: false },
                stroke: { width: 0 }
            }));
        }

        // 5. Order Heatmap Matrix
        const heatmapEl = document.querySelector('#order_statistics_heatmap');
        if (heatmapEl) {
            charts.push(new ApexCharts(heatmapEl, {
                series: [
                    { name: '10 Am', data: [20, 30, 40, 20, 50, 30, 40] },
                    { name: '12 Am', data: [10, 40, 20, 60, 30, 20, 10] },
                    { name: '2 Pm', data: [30, 20, 50, 40, 70, 40, 30] },
                    { name: '4 Pm', data: [40, 50, 30, 30, 40, 50, 60] }
                ],
                chart: { type: 'heatmap', height: 220, toolbar: { show: false } },
                colors: ['#f97316'],
                dataLabels: { enabled: false },
                xaxis: {
                    categories: days,
                    labels: { style: { colors: tc.label, fontSize: '10px' } }
                },
                yaxis: { labels: { style: { colors: tc.label, fontSize: '10px' } } },
                grid: { borderColor: tc.grid }
            }));
        }

        charts.forEach(function (c) {
            try { c.render(); } catch (e) {}
        });
    }

    function destroyCharts() {
        charts.forEach(function (c) {
            try { c.destroy(); } catch (e) {}
        });
        charts = [];
    }

    initCharts();

    // Re-render charts when the theme mode is toggled via the customizer
    new MutationObserver(function () {
        destroyCharts();
        initCharts();
    }).observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['data-layout-mode']
    });
});
</script>
@endpush