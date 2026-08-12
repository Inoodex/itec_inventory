@extends('frontend.layouts.app')

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1">Bill Details #{{ $bill->bill_number }}</h4>
                <p class="text-muted small mb-0">Reference No: <strong class="text-dark">{{ $bill->reference_number }}</strong></p>
            </div>
            <div>
                <a href="{{ route('bills.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3 shadow-sm me-2">
                    <i class="fa fa-arrow-left me-2"></i>Back to Bills
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Info Summary Cards Row -->
    <div class="row g-4 mb-4">
        <!-- Bill Information Card -->
        <div class="col-lg-6 col-12">
            <div class="card border-0 shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-3"><i class="fe fe-file-text me-2 text-primary"></i>Bill Information</h6>
                    <table class="table table-borderless align-middle mb-0">
                        <tr>
                            <td class="text-secondary small fw-semibold ps-0" style="width: 40%">Bill Number:</td>
                            <td class="fw-bold text-primary font-monospace">{{ $bill->bill_number }}</td>
                        </tr>
                        <tr>
                            <td class="text-secondary small fw-semibold ps-0">Reference Number:</td>
                            <td class="fw-bold text-dark">{{ $bill->reference_number }}</td>
                        </tr>
                        <tr>
                            <td class="text-secondary small fw-semibold ps-0">Bill Date:</td>
                            <td class="text-dark">{{ $bill->bill_date ? $bill->bill_date->format('d M Y') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-secondary small fw-semibold ps-0">Work Order:</td>
                            <td class="text-muted">{{ $bill->work_order_number ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-secondary small fw-semibold ps-0">Total Bill Amount:</td>
                            <td>
                                <span class="badge badge-soft-primary px-3 py-1 rounded-pill fs-7">
                                    ৳{{ number_format($bill->total_amount, 2) }}
                                </span>
                            </td>
                        </tr>
                        @if(!empty($amount_in_words))
                            <tr>
                                <td class="text-secondary small fw-semibold ps-0">Amount in Words:</td>
                                <td class="text-muted fst-italic">{{ $amount_in_words }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Client Information Card -->
        <div class="col-lg-6 col-12">
            <div class="card border-0 shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-3"><i class="fe fe-user me-2 text-primary"></i>Client Information</h6>
                    <table class="table table-borderless align-middle mb-0">
                        <tr>
                            <td class="text-secondary small fw-semibold ps-0" style="width: 40%">Client / Company:</td>
                            <td class="fw-bold text-dark">{{ $recipient_organization }}</td>
                        </tr>
                        <tr>
                            <td class="text-secondary small fw-semibold ps-0">Client Address:</td>
                            <td class="text-muted">{{ $recipient_address }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bill Items Table Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-dark mb-0"><i class="fe fe-box me-2 text-primary"></i>Bill Items Breakdown</h6>
                <span class="badge badge-soft-primary px-3 py-1 rounded-pill">{{ $bill->billItems->count() }} items</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary fs-7 text-uppercase">
                        <tr>
                            <th class="ps-4">#</th>
                            <th style="width: 50%;">Description</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end pe-4">Total</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @foreach ($bill->billItems as $index => $item)
                            <tr>
                                <td class="ps-4 text-muted fw-semibold">{{ $index + 1 }}</td>
                                <td>
                                    <div class="fw-semibold text-dark">{!! nl2br(e($item->description)) !!}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-soft-info px-2 py-1 rounded-2">{{ number_format($item->quantity) }} {{ $item->unit ?? 'Pcs' }}</span>
                                </td>
                                <td class="text-end text-muted">৳{{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-end fw-bold text-primary pe-4">৳{{ number_format($item->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-top">
                        <tr>
                            <td colspan="3" class="text-end fw-bold text-dark py-3">Subtotal:</td>
                            <td colspan="2" class="text-end fw-bold text-dark pe-4 py-3">৳{{ number_format($bill->subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end fw-bold text-dark py-2">Total Amount:</td>
                            <td colspan="2" class="text-end fw-bold text-primary fs-5 pe-4 py-2">৳{{ number_format($bill->total_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Terms & Conditions Card -->
    @if ($terms_conditions)
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold text-dark mb-3"><i class="fe fe-file-text me-2 text-primary"></i>Terms & Conditions</h6>
                <div class="p-3 bg-light rounded-3 text-secondary small">
                    {!! nl2br(e($terms_conditions)) !!}
                </div>
            </div>
        </div>
    @endif

    <!-- Bank & Company Details Row -->
    <div class="row g-4 mb-4">
        <!-- Bank Details Card -->
        <div class="col-lg-6 col-12">
            <div class="card border-0 shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-3"><i class="fe fe-credit-card me-2 text-primary"></i>Bank Details</h6>
                    <table class="table table-borderless align-middle mb-0">
                        <tr>
                            <td class="text-secondary small fw-semibold ps-0" style="width: 40%">Account Name:</td>
                            <td class="fw-bold text-dark">{{ $bank_details['account_name'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-secondary small fw-semibold ps-0">Bank Name:</td>
                            <td class="text-dark">{{ $bank_details['bank_name'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-secondary small fw-semibold ps-0">Branch:</td>
                            <td class="text-muted">{{ $bank_details['branch'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-secondary small fw-semibold ps-0">Account Number:</td>
                            <td class="fw-bold text-primary font-monospace">{{ $bank_details['account_number'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-secondary small fw-semibold ps-0">Account Type:</td>
                            <td class="text-muted">{{ $bank_details['account_type'] ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Company Details Card -->
        <div class="col-lg-6 col-12">
            <div class="card border-0 shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-3"><i class="fe fe-briefcase me-2 text-primary"></i>Company & Signatory</h6>
                    <table class="table table-borderless align-middle mb-0">
                        <tr>
                            <td class="text-secondary small fw-semibold ps-0" style="width: 40%">Company Name:</td>
                            <td class="fw-bold text-dark">{{ $company['name'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-secondary small fw-semibold ps-0">Signatory Name:</td>
                            <td class="fw-semibold text-dark">{{ $company['signatory_name'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-secondary small fw-semibold ps-0">Designation:</td>
                            <td class="text-muted">{{ $company['signatory_designation'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-secondary small fw-semibold ps-0">Phone:</td>
                            <td class="text-muted">{{ $company['phone'] ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-secondary small fw-semibold ps-0">Email:</td>
                            <td class="text-muted">{{ $company['email'] ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
