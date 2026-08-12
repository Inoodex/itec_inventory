@extends('frontend.layouts.app')

@section('content')
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title font-weight-bold" style="color: #1e293b;">Profit & Loss Statement (P&L)</h3>
                </div>
                <div class="col-auto">
                    <a href="{{ route('reports.profit-loss.pdf', ['from_date' => $fromDate, 'to_date' => $toDate]) }}"
                        class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-file-pdf me-1"></i> Export P&L PDF
                    </a>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('reports.profit-loss') }}" class="row g-2 align-items-center">
                    <div class="col-md-3">
                        <label class="form-label fw-bold mb-0">From:</label>
                        <input type="date" name="from_date" class="form-control" value="{{ $fromDate }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold mb-0">To:</label>
                        <input type="date" name="to_date" class="form-control" value="{{ $toDate }}">
                    </div>
                    <div class="col-md-3 mt-auto">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-chart-line me-1"></i>
                            Run P&L</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Net Income Summary Card -->
        <div class="card shadow-sm border-0 mb-4 text-center"
            style="border-radius: 12px; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #ffffff;">
            <div class="card-body py-4">
                <span class="text-white-50 text-uppercase fw-semibold" style="font-size: 12px; letter-spacing: 0.5px;">Net
                    Operating Income / (Loss) for Period</span>
                <h2 class="fw-bold mt-2 mb-0 {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ $netProfit >= 0 ? '+' . number_format($netProfit, 2) : '-' . number_format(abs($netProfit), 2) }}
                </h2>
                <small class="text-white-50">Period: {{ \Carbon\Carbon::parse($fromDate)->format('d M, Y') }} —
                    {{ \Carbon\Carbon::parse($toDate)->format('d M, Y') }}</small>
            </div>
        </div>

        <div class="row">
            <!-- Operating Revenues (4000) -->
            <div class="col-lg-6 col-12 mb-4">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-bold text-success"><i class="fas fa-arrow-down me-2"></i> Operating
                            Revenues (4000)</h5>
                        <span class="badge bg-success">{{ number_format($totalRevenue, 2) }}</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead style="background-color: #f8fafc; font-size: 11px; text-transform: uppercase;">
                                    <tr>
                                        <th>Revenue Account</th>
                                        <th>Code</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($revenueData as $rev)
                                        <tr>
                                            <td class="fw-semibold text-dark">{{ $rev['account']->account_name }}</td>
                                            <td><span
                                                    class="badge bg-light text-muted">{{ $rev['account']->account_code }}</span>
                                            </td>
                                            <td class="text-end fw-bold text-success">{{ number_format($rev['amount'], 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-muted">No operating revenues recorded.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot style="background-color: #f8fafc; font-weight: 800;">
                                    <tr>
                                        <td colspan="2" class="text-uppercase">Total Operating Revenue:</td>
                                        <td class="text-end text-success">{{ number_format($totalRevenue, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Operating Expenses (5000) -->
            <div class="col-lg-6 col-12 mb-4">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-bold text-danger"><i class="fas fa-arrow-up me-2"></i> Operating
                            Expenses (5000)</h5>
                        <span class="badge bg-danger">{{ number_format($totalExpense, 2) }}</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead style="background-color: #f8fafc; font-size: 11px; text-transform: uppercase;">
                                    <tr>
                                        <th>Expense Account</th>
                                        <th>Code</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($expenseData as $exp)
                                        <tr>
                                            <td class="fw-semibold text-dark">{{ $exp['account']->account_name }}</td>
                                            <td><span
                                                    class="badge bg-light text-muted">{{ $exp['account']->account_code }}</span>
                                            </td>
                                            <td class="text-end fw-bold text-danger">{{ number_format($exp['amount'], 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-muted">No operating expenses recorded.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot style="background-color: #f8fafc; font-weight: 800;">
                                    <tr>
                                        <td colspan="2" class="text-uppercase">Total Operating Expenses:</td>
                                        <td class="text-end text-danger">{{ number_format($totalExpense, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection