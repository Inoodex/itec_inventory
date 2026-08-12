@extends('frontend.layouts.app')

@section('content')
<div class="content container-fluid">

    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title font-weight-bold" style="color: #1e293b;">Create Journal Voucher</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('journal-entries.index') }}">Journal Vouchers</a></li>
                    <li class="breadcrumb-item active">New Voucher</li>
                </ul>
            </div>
            <div class="col-auto">
                <span class="badge bg-primary fs-6 px-3 py-2">{{ $journalNo }}</span>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('journal-entries.store') }}" id="journalVoucherForm">
        @csrf

        <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Voucher Date <span class="text-danger">*</span></label>
                        <input type="date" name="entry_date" class="form-control" value="{{ old('entry_date', $today) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Reference / Source Type <span class="text-danger">*</span></label>
                        <select name="reference_type" class="form-select" required>
                            <option value="manual" selected>Manual Journal Voucher</option>
                            <option value="opening_balance">Opening Balance Adjustment</option>
                            <option value="adjustment">Year-End / Period Adjustment</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Auto Voucher Number</label>
                        <input type="text" class="form-control" value="{{ $journalNo }}" readonly disabled>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Narration / Description <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="2" placeholder="State the purpose, transaction justification, and audit notes..." required>{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Line Items Table -->
        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold" style="color: #0f172a;">Voucher Line Items (Debits & Credits)</h5>
                <button type="button" class="btn btn-sm btn-outline-primary" id="addRowBtn">
                    <i class="fas fa-plus me-1"></i> Add Split Line
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0" id="voucherItemsTable">
                        <thead style="background-color: #1e293b; color: #ffffff; font-size: 11px; text-transform: uppercase;">
                            <tr>
                                <th style="width: 35%;">Account Code & Title</th>
                                <th style="width: 25%;">Line Description</th>
                                <th style="width: 18%;" class="text-end">Debit</th>
                                <th style="width: 18%;" class="text-end">Credit</th>
                                <th style="width: 4%;" class="text-center">#</th>
                            </tr>
                        </thead>
                        <tbody id="itemsContainer">
                            <!-- Initial Row 1 -->
                            <tr class="item-row">
                                <td>
                                    <select name="items[0][account_id]" class="form-select form-select-sm account-select" required>
                                        <option value="">-- Select Account --</option>
                                        @foreach($accounts as $acc)
                                            <option value="{{ $acc->id }}">[{{ $acc->account_code }}] {{ $acc->account_name }} ({{ strtoupper($acc->account_type) }})</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="items[0][description]" class="form-control form-control-sm" placeholder="Line note...">
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" name="items[0][debit]" class="form-control form-control-sm text-end debit-input" placeholder="0.00" value="0.00">
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" name="items[0][credit]" class="form-control form-control-sm text-end credit-input" placeholder="0.00" value="0.00">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-link text-danger remove-row-btn"><i class="fas fa-times"></i></button>
                                </td>
                            </tr>
                            <!-- Initial Row 2 -->
                            <tr class="item-row">
                                <td>
                                    <select name="items[1][account_id]" class="form-select form-select-sm account-select" required>
                                        <option value="">-- Select Account --</option>
                                        @foreach($accounts as $acc)
                                            <option value="{{ $acc->id }}">[{{ $acc->account_code }}] {{ $acc->account_name }} ({{ strtoupper($acc->account_type) }})</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="items[1][description]" class="form-control form-control-sm" placeholder="Line note...">
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" name="items[1][debit]" class="form-control form-control-sm text-end debit-input" placeholder="0.00" value="0.00">
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" name="items[1][credit]" class="form-control form-control-sm text-end credit-input" placeholder="0.00" value="0.00">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-link text-danger remove-row-btn"><i class="fas fa-times"></i></button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot style="background-color: #f8fafc; font-weight: 800;">
                            <tr>
                                <td colspan="2" class="text-end text-uppercase">Total Amount:</td>
                                <td class="text-end text-primary" id="totalDebitCell">0.00</td>
                                <td class="text-end text-primary" id="totalCreditCell">0.00</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="text-end text-uppercase">Equilibrium Balance:</td>
                                <td colspan="2" class="text-center" id="balanceStatusCell">
                                    <span class="badge bg-success px-3 py-1">Balanced (0.00 Difference)</span>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white p-4 text-end">
                <a href="{{ route('journal-entries.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary px-4" id="submitVoucherBtn"><i class="fas fa-save me-1"></i> Post Journal Voucher</button>
            </div>
        </div>
    </form>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let rowIndex = 2;
    const itemsContainer = document.getElementById('itemsContainer');
    const totalDebitCell = document.getElementById('totalDebitCell');
    const totalCreditCell = document.getElementById('totalCreditCell');
    const balanceStatusCell = document.getElementById('balanceStatusCell');
    const submitBtn = document.getElementById('submitVoucherBtn');

    function calculateTotals() {
        let totalDebit = 0.00;
        let totalCredit = 0.00;

        document.querySelectorAll('.debit-input').forEach(input => {
            const val = parseFloat(input.value) || 0;
            totalDebit += val;
        });

        document.querySelectorAll('.credit-input').forEach(input => {
            const val = parseFloat(input.value) || 0;
            totalCredit += val;
        });

        totalDebitCell.textContent = totalDebit.toFixed(2);
        totalCreditCell.textContent = totalCredit.toFixed(2);

        const diff = Math.abs(totalDebit - totalCredit);

        if (totalDebit > 0 && diff < 0.001) {
            balanceStatusCell.innerHTML = '<span class="badge bg-success px-3 py-1">Balanced (0.00 Difference)</span>';
            submitBtn.disabled = false;
        } else {
            balanceStatusCell.innerHTML = '<span class="badge bg-danger px-3 py-1">Unbalanced (' + diff.toFixed(2) + ' Difference)</span>';
            submitBtn.disabled = true;
        }
    }

    document.getElementById('addRowBtn').addEventListener('click', function() {
        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.innerHTML = `
            <td>
                <select name="items[${rowIndex}][account_id]" class="form-select form-select-sm account-select" required>
                    <option value="">-- Select Account --</option>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}">[{{ $acc->account_code }}] {{ $acc->account_name }} ({{ strtoupper($acc->account_type) }})</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="text" name="items[${rowIndex}][description]" class="form-control form-control-sm" placeholder="Line note...">
            </td>
            <td>
                <input type="number" step="0.01" min="0" name="items[${rowIndex}][debit]" class="form-control form-control-sm text-end debit-input" placeholder="0.00" value="0.00">
            </td>
            <td>
                <input type="number" step="0.01" min="0" name="items[${rowIndex}][credit]" class="form-control form-control-sm text-end credit-input" placeholder="0.00" value="0.00">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-link text-danger remove-row-btn"><i class="fas fa-times"></i></button>
            </td>
        `;
        itemsContainer.appendChild(tr);
        rowIndex++;
        attachEvents();
    });

    function attachEvents() {
        document.querySelectorAll('.debit-input, .credit-input').forEach(input => {
            input.removeEventListener('input', calculateTotals);
            input.addEventListener('input', calculateTotals);
        });

        document.querySelectorAll('.remove-row-btn').forEach(btn => {
            btn.onclick = function() {
                if (document.querySelectorAll('.item-row').length > 2) {
                    this.closest('tr').remove();
                    calculateTotals();
                } else {
                    alert('A journal voucher must have at least 2 split lines.');
                }
            };
        });
    }

    attachEvents();
    calculateTotals();
});
</script>
@endsection
