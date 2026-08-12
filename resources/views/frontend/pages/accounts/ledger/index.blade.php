@extends('frontend.layouts.app')

@push('styles')
    <style>
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

        .dropdown-menu {
            z-index: 9999 !important;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title font-weight-bold" style="color: #1e293b;">General Ledger</h3>
                </div>
                @if($selectedAccount)
                    <div class="col-auto">
                        <a href="{{ route('ledger.pdf', ['account_id' => $accountId, 'from_date' => $fromDate, 'to_date' => $toDate]) }}"
                            class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-file-pdf me-1"></i> Export Ledger PDF
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Filter Form -->
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('ledger.index') }}" class="row g-2 align-items-center">
                    <div class="col-md-5">
                        <select name="account_id" class="form-select form-select-sm" required>
                            <option value="">-- Select Master Account --</option>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}" {{ $accountId == $acc->id ? 'selected' : '' }}>
                                    [{{ $acc->account_code }}] {{ $acc->account_name }} ({{ strtoupper($acc->account_type) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="from_date" class="form-control form-control-sm" value="{{ $fromDate }}">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="to_date" class="form-control form-control-sm" value="{{ $toDate }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100"><i class="fas fa-filter me-1"></i> View
                            Ledger</button>
                    </div>
                    <div class="col-md-1 text-end">
                        <a href="{{ route('ledger.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        @if($selectedAccount)
            <!-- Ledger Summary Header Card -->
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; background-color: #1e293b; color: #ffffff;">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <span class="badge bg-secondary text-uppercase mb-2">{{ $selectedAccount->account_type }}</span>
                            <h4 class="text-white fw-bold mb-1">[{{ $selectedAccount->account_code }}]
                                {{ $selectedAccount->account_name }}</h4>
                            <p class="text-white-50 mb-0 font-monospace" style="font-size: 12px;">Period:
                                {{ \Carbon\Carbon::parse($fromDate)->format('d M, Y') }} —
                                {{ \Carbon\Carbon::parse($toDate)->format('d M, Y') }}</p>
                        </div>
                        <div class="col-md-3 text-md-end mt-3 mt-md-0 border-start border-secondary">
                            <span class="text-white-50 text-uppercase fw-semibold" style="font-size: 11px;">Opening
                                Balance</span>
                            <h4 class="text-white fw-bold mt-1 mb-0">৳{{ number_format($openingBalance, 2) }}</h4>
                        </div>
                        <div class="col-md-3 text-md-end mt-3 mt-md-0 border-start border-secondary">
                            <span class="text-white-50 text-uppercase fw-semibold" style="font-size: 11px;">Ending
                                Balance</span>
                            <h3 class="text-success fw-bold mt-1 mb-0">৳{{ number_format($closingBalance, 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ledger Entries Table -->
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead style="background-color: #f8fafc; font-size: 11px; text-transform: uppercase;">
                                <tr>
                                    <th style="width: 120px;" class="ps-3">Date</th>
                                    <th style="width: 140px;">Voucher #</th>
                                    <th>Narration / Details</th>
                                    <th class="text-end" style="width: 130px;">Debit</th>
                                    <th class="text-end" style="width: 130px;">Credit</th>
                                    <th class="text-end" style="width: 150px;">Running Balance</th>
                                    <th class="text-end pe-4" style="width: 80px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Opening Balance Row -->
                                <tr style="background-color: #f1f5f9; font-weight: 700;">
                                    <td class="ps-3">{{ \Carbon\Carbon::parse($fromDate)->format('d M, Y') }}</td>
                                    <td>-</td>
                                    <td>Opening Balance Brought Forward</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end text-dark">৳{{ number_format($openingBalance, 2) }}</td>
                                    <td></td>
                                </tr>

                                @php
                                    $running = $openingBalance;
                                    $periodDebit = 0;
                                    $periodCredit = 0;
                                @endphp

                                @forelse($ledgerItems as $item)
                                    @php
                                        $jv = $item->journalEntry;
                                        $d = (float) $item->debit;
                                        $c = (float) $item->credit;
                                        $periodDebit += $d;
                                        $periodCredit += $c;

                                        if ($selectedAccount->isDebitNormal()) {
                                            $running += ($d - $c);
                                        } else {
                                            $running += ($c - $d);
                                        }
                                    @endphp
                                    <tr>
                                        <td class="ps-3">{{ \Carbon\Carbon::parse($jv->entry_date)->format('d M, Y') }}</td>
                                        <td>
                                            <a href="{{ route('journal-entries.show', $jv->id) }}" class="fw-bold text-primary">
                                                {{ $jv->journal_no }}
                                            </a>
                                        </td>
                                        <td>
                                            <div class="text-dark">{{ $item->description ?? $jv->description ?? '-' }}</div>
                                            <span class="badge bg-light text-muted text-uppercase"
                                                style="font-size: 9px;">{{ $jv->reference_type }}</span>
                                        </td>
                                        <td class="text-end fw-semibold text-dark">
                                            {{ $d > 0 ? '৳' . number_format($d, 2) : '-' }}
                                        </td>
                                        <td class="text-end fw-semibold text-dark">
                                            {{ $c > 0 ? '৳' . number_format($c, 2) : '-' }}
                                        </td>
                                        <td class="text-end fw-bold {{ $running < 0 ? 'text-danger' : 'text-primary' }}">
                                            ৳{{ number_format($running, 2) }}
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="dropdown">
                                                <a href="javascript:void(0)" class="btn-action-icon shadow-none"
                                                    data-bs-toggle="dropdown" data-bs-popper-config='{"strategy":"fixed"}'
                                                    aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                                    <li>
                                                        <a class="dropdown-item py-2 d-flex align-items-center gap-2"
                                                            href="{{ route('journal-entries.show', $jv->id) }}">
                                                            <i class="fe fe-eye text-info"></i>
                                                            <span>View Voucher</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item py-2 d-flex align-items-center gap-2"
                                                            href="{{ route('journal-entries.pdf', $jv->id) }}">
                                                            <i class="fe fe-download text-primary"></i>
                                                            <span>Download PDF</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">No journal transactions recorded for
                                            this account during the selected date range.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot style="background-color: #f8fafc; font-weight: 800;">
                                <tr>
                                    <td colspan="3" class="text-end text-uppercase ps-3">Total Period Activity:</td>
                                    <td class="text-end text-dark">৳{{ number_format($periodDebit, 2) }}</td>
                                    <td class="text-end text-dark">৳{{ number_format($periodCredit, 2) }}</td>
                                    <td class="text-end text-success">৳{{ number_format($running, 2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <div class="card shadow-sm border-0 py-5 text-center" style="border-radius: 12px;">
                <div class="card-body">
                    <i class="fas fa-book fa-3x text-muted mb-3"></i>
                    <h5 class="fw-bold text-dark">Select an Account to View Ledger</h5>
                    <p class="text-muted">Pick any asset, liability, equity, revenue, or expense account above to inspect
                        chronological double-entry lines.</p>
                </div>
            </div>
        @endif

    </div>
@endsection