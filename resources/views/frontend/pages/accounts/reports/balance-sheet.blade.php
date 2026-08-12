@extends('frontend.layouts.app')

@section('content')
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title font-weight-bold" style="color: #1e293b;">Balance Sheet</h3>
                </div>
                <div class="col-auto">
                    <a href="{{ route('reports.balance-sheet.pdf', ['as_of_date' => $asOfDate]) }}"
                        class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-file-pdf me-1"></i> Export Balance Sheet PDF
                    </a>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('reports.balance-sheet') }}" class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <label class="form-label fw-bold mb-0">As of Date:</label>
                        <input type="date" name="as_of_date" class="form-control" value="{{ $asOfDate }}">
                    </div>
                    <div class="col-md-4 mt-auto">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-balance-scale me-1"></i>
                            Run Balance Sheet</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Accounting Equation Equilibrium Alert -->
        <div class="alert {{ $isBalanced ? 'alert-success' : 'alert-danger' }} shadow-sm border-0 d-flex align-items-center mb-4"
            style="border-radius: 10px;">
            <i class="fas {{ $isBalanced ? 'fa-check-circle' : 'fa-exclamation-triangle' }} fa-2x me-3"></i>
            <div>
                <h5 class="alert-heading mb-0 fw-bold">
                    {{ $isBalanced ? 'Fundamental Accounting Equation Balanced: Assets = Liabilities + Equity' : 'Warning: Accounting Equation Out of Balance!' }}
                </h5>
                <small>{{ $isBalanced ? 'Total Assets (' . number_format($totalAssets, 2) . ') matches Total Liabilities & Equity (' . number_format($totalLiabAndEquity, 2) . ').' : 'Variance detected between Assets and (Liabilities + Equity).' }}</small>
            </div>
        </div>

        <div class="row">
            <!-- Assets (1000) -->
            <div class="col-lg-6 col-12 mb-4">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-bold text-success"><i class="fas fa-building me-2"></i> Assets (1000)
                        </h5>
                        <span class="badge bg-success">{{ number_format($totalAssets, 2) }}</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead style="background-color: #f8fafc; font-size: 11px; text-transform: uppercase;">
                                    <tr>
                                        <th>Asset Account</th>
                                        <th>Code</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($assetData as $ast)
                                        <tr>
                                            <td class="fw-semibold text-dark">{{ $ast['account']->account_name }}</td>
                                            <td><span
                                                    class="badge bg-light text-muted">{{ $ast['account']->account_code }}</span>
                                            </td>
                                            <td class="text-end fw-bold text-success">{{ number_format($ast['amount'], 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-muted">No asset balances found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot style="background-color: #1e293b; color: #ffffff; font-weight: 800;">
                                    <tr>
                                        <td colspan="2" class="text-uppercase">Total Assets:</td>
                                        <td class="text-end text-success">{{ number_format($totalAssets, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liabilities & Equity (2000 & 3000) -->
            <div class="col-lg-6 col-12 mb-4">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-bold text-danger"><i class="fas fa-coins me-2"></i> Liabilities &
                            Equity</h5>
                        <span class="badge bg-primary">{{ number_format($totalLiabAndEquity, 2) }}</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead style="background-color: #f8fafc; font-size: 11px; text-transform: uppercase;">
                                    <tr>
                                        <th>Account Name</th>
                                        <th>Code</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Liabilities Subheader -->
                                    <tr style="background-color: #f8fafc; font-weight: 700;">
                                        <td colspan="3" class="text-danger text-uppercase" style="font-size: 11px;">
                                            Liabilities (2000)</td>
                                    </tr>
                                    @foreach($liabilityData as $lia)
                                        <tr>
                                            <td class="ps-4 fw-semibold text-dark">{{ $lia['account']->account_name }}</td>
                                            <td><span
                                                    class="badge bg-light text-muted">{{ $lia['account']->account_code }}</span>
                                            </td>
                                            <td class="text-end fw-bold text-danger">{{ number_format($lia['amount'], 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr style="font-weight: 700;">
                                        <td colspan="2" class="ps-4 text-muted">Total Liabilities:</td>
                                        <td class="text-end text-danger">{{ number_format($totalLiabilities, 2) }}</td>
                                    </tr>

                                    <!-- Equity Subheader -->
                                    <tr style="background-color: #f8fafc; font-weight: 700;">
                                        <td colspan="3" class="text-primary text-uppercase" style="font-size: 11px;">Equity
                                            (3000)</td>
                                    </tr>
                                    @foreach($equityData as $eq)
                                        <tr>
                                            <td class="ps-4 fw-semibold text-dark">{{ $eq['account']->account_name }}</td>
                                            <td><span
                                                    class="badge bg-light text-muted">{{ $eq['account']->account_code }}</span>
                                            </td>
                                            <td class="text-end fw-bold text-primary">{{ number_format($eq['amount'], 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td class="ps-4 fw-semibold text-dark">Current Period Retained Earnings (Net Profit)
                                        </td>
                                        <td><span class="badge bg-light text-muted">PL</span></td>
                                        <td
                                            class="text-end fw-bold {{ $currentEarnings >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($currentEarnings, 2) }}
                                        </td>
                                    </tr>
                                    <tr style="font-weight: 700;">
                                        <td colspan="2" class="ps-4 text-muted">Total Equity:</td>
                                        <td class="text-end text-primary">{{ number_format($totalEquityWithEarnings, 2) }}
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot style="background-color: #1e293b; color: #ffffff; font-weight: 800;">
                                    <tr>
                                        <td colspan="2" class="text-uppercase">Total Liabilities & Equity:</td>
                                        <td class="text-end text-success">{{ number_format($totalLiabAndEquity, 2) }}</td>
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