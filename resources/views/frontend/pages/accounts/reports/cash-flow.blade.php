@extends('frontend.layouts.app')

@section('content')
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title font-weight-bold" style="color: #1e293b;">Cash Flow Statement</h3>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('reports.cash-flow') }}" class="row g-2 align-items-center">
                    <div class="col-md-3">
                        <label class="form-label fw-bold mb-0">From Date:</label>
                        <input type="date" name="from_date" class="form-control" value="{{ $fromDate }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold mb-0">To Date:</label>
                        <input type="date" name="to_date" class="form-control" value="{{ $toDate }}">
                    </div>
                    <div class="col-md-3 mt-auto">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i>
                            Calculate Cash Flow</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary KPI Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-0"
                    style="border-radius: 12px; border-left: 5px solid #3b82f6 !important;">
                    <div class="card-body">
                        <span class="text-muted text-uppercase fw-semibold" style="font-size: 11px;">Opening Liquid
                            Funds</span>
                        <h3 class="fw-bold text-dark mt-2 mb-0">{{ number_format($openingCash, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0"
                    style="border-radius: 12px; border-left: 5px solid {{ $netCashFlow >= 0 ? '#10b981' : '#ef4444' }} !important;">
                    <div class="card-body">
                        <span class="text-muted text-uppercase fw-semibold" style="font-size: 11px;">Net Period Cash
                            Change</span>
                        <h3 class="fw-bold mt-2 mb-0 {{ $netCashFlow >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $netCashFlow >= 0 ? '+' . number_format($netCashFlow, 2) : '-' . number_format(abs($netCashFlow), 2) }}
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0"
                    style="border-radius: 12px; border-left: 5px solid #10b981 !important;">
                    <div class="card-body">
                        <span class="text-muted text-uppercase fw-semibold" style="font-size: 11px;">Ending Liquid Cash &
                            Bank</span>
                        <h3 class="fw-bold text-success mt-2 mb-0">{{ number_format($closingCash, 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cash Flow Breakdown Table -->
        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead
                            style="background-color: #1e293b; color: #ffffff; font-size: 11px; text-transform: uppercase;">
                            <tr>
                                <th>Cash Flow Activity Classification</th>
                                <th class="text-end" style="width: 200px;">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="background-color: #f8fafc; font-weight: 700;">
                                <td>Cash & Cash Equivalents at Beginning of Period</td>
                                <td class="text-end text-dark">{{ number_format($openingCash, 2) }}</td>
                            </tr>

                            <!-- Inflows -->
                            <tr>
                                <td class="ps-4 fw-semibold text-dark">
                                    <i class="fas fa-arrow-down text-success me-2"></i> Operating Cash Inflows (Sales,
                                    Services, Projects)
                                </td>
                                <td class="text-end fw-bold text-success">+{{ number_format($inflows, 2) }}</td>
                            </tr>

                            <!-- Outflows -->
                            <tr>
                                <td class="ps-4 fw-semibold text-dark">
                                    <i class="fas fa-arrow-up text-danger me-2"></i> Operating Cash Outflows (Purchases,
                                    Salaries, Expenses)
                                </td>
                                <td class="text-end fw-bold text-danger">-{{ number_format($outflows, 2) }}</td>
                            </tr>

                            <tr style="background-color: #f8fafc; font-weight: 700;">
                                <td>Net Cash Generated from / (Used in) Operating Activities</td>
                                <td class="text-end {{ $netCashFlow >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($netCashFlow, 2) }}
                                </td>
                            </tr>
                        </tbody>
                        <tfoot style="background-color: #1e293b; color: #ffffff; font-weight: 800; font-size: 14px;">
                            <tr>
                                <td class="text-uppercase">Cash & Cash Equivalents at End of Period:</td>
                                <td class="text-end text-success">{{ number_format($closingCash, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection