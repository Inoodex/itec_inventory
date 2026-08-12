@extends('frontend.layouts.app')

@section('content')
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title font-weight-bold" style="color: #1e293b;">Journal Voucher:
                        {{ $journalEntry->journal_no }}</h3>
                </div>
                <div class="col-auto d-flex gap-2">
                    <a href="{{ route('journal-entries.pdf', $journalEntry->id) }}" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-file-pdf me-1"></i> Print / PDF
                    </a>
                    @if($journalEntry->status !== 'reversed')
                        <a href="{{ route('journal-entries.reverse', $journalEntry->id) }}"
                            class="btn btn-outline-warning btn-sm">
                            <i class="fas fa-undo me-1"></i> Reversal Entry (Storno)
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Voucher Details Card -->
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-3">
                        <span class="text-muted text-uppercase fw-semibold" style="font-size: 11px;">Voucher Date</span>
                        <h5 class="fw-bold text-dark mt-1">
                            {{ \Carbon\Carbon::parse($journalEntry->entry_date)->format('d F, Y') }}</h5>
                    </div>
                    <div class="col-md-3">
                        <span class="text-muted text-uppercase fw-semibold" style="font-size: 11px;">Reference Type</span>
                        <h5 class="mt-1">
                            <span
                                class="badge bg-secondary text-white text-uppercase">{{ $journalEntry->reference_type }}</span>
                            @if($journalEntry->reference_id)
                                <small class="text-muted">#{{ $journalEntry->reference_id }}</small>
                            @endif
                        </h5>
                    </div>
                    <div class="col-md-3">
                        <span class="text-muted text-uppercase fw-semibold" style="font-size: 11px;">Status</span>
                        <h5 class="mt-1">
                            @if($journalEntry->status === 'approved')
                                <span class="badge bg-success">Approved</span>
                            @elseif($journalEntry->status === 'reversed')
                                <span class="badge bg-danger">Reversed</span>
                            @else
                                <span class="badge bg-warning">Posted</span>
                            @endif
                        </h5>
                    </div>
                    <div class="col-md-3">
                        <span class="text-muted text-uppercase fw-semibold" style="font-size: 11px;">Auditor / Prepared
                            By</span>
                        <h5 class="fw-bold text-dark mt-1">{{ $journalEntry->creator->name ?? 'System' }}</h5>
                    </div>
                    @if($journalEntry->description)
                        <div class="col-12 mt-3 pt-3 border-top">
                            <span class="text-muted text-uppercase fw-semibold" style="font-size: 11px;">Transaction
                                Narration</span>
                            <p class="mb-0 text-dark mt-1">{{ $journalEntry->description }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Line Items Table -->
        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead
                            style="background-color: #1e293b; color: #ffffff; font-size: 11px; text-transform: uppercase;">
                            <tr>
                                <th style="width: 15%;">Account Code</th>
                                <th style="width: 35%;">Account Title & Classification</th>
                                <th style="width: 26%;">Description</th>
                                <th style="width: 12%;" class="text-end">Debit</th>
                                <th style="width: 12%;" class="text-end">Credit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($journalEntry->items as $item)
                                <tr>
                                    <td class="fw-bold" style="color: #334155;">{{ $item->account->account_code }}</td>
                                    <td>
                                        <span class="fw-semibold text-dark">{{ $item->account->account_name }}</span>
                                        <span class="badge bg-light text-muted ms-1 text-uppercase"
                                            style="font-size: 9px;">{{ $item->account->account_type }}</span>
                                    </td>
                                    <td class="text-muted">{{ $item->description ?? '-' }}</td>
                                    <td class="text-end fw-bold text-dark">
                                        {{ $item->debit > 0 ? number_format($item->debit, 2) : '-' }}
                                    </td>
                                    <td class="text-end fw-bold text-dark">
                                        {{ $item->credit > 0 ? number_format($item->credit, 2) : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot style="background-color: #f8fafc; font-weight: 800;">
                            <tr>
                                <td colspan="3" class="text-end text-uppercase">Total Voucher Amount:</td>
                                <td class="text-end text-primary">{{ number_format($journalEntry->total_debit, 2) }}</td>
                                <td class="text-end text-primary">{{ number_format($journalEntry->total_credit, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- Reversal Modal -->
    <div class="modal fade" id="reverseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('journal-entries.reverse', $journalEntry->id) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold">Storno Reversal Voucher</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted">
                            In double-entry accounting, vouchers are immutable. Reversing will post an offsetting Storno
                            entry swapping all debits and credits.
                        </p>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Reason for Reversal <span class="text-danger">*</span></label>
                            <textarea name="reason" class="form-control" rows="3"
                                placeholder="State error correction justification..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Confirm & Post Reversal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection