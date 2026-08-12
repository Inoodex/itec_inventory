@extends('frontend.layouts.app')

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1">Quotation Details #{{ $quotation->quotation_number }}</h4>
                <p class="text-muted small mb-0">View formal quote estimate and line item summary</p>
            </div>
            <div>
                <a href="{{ route('quotations.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3 shadow-sm me-2">
                    <i class="fa fa-arrow-left me-2"></i>Back to Quotations
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->
                    <div class="card-body" id="quotation-content">
                        <!-- Quotation Header -->
                        <div class="row mb-4">
                            <div class="col-6">
                                <h2 class="text-primary">QUOTATION</h2>
                                <p class="mb-1"><strong>Date:</strong>
                                    {{ \Carbon\Carbon::parse($quotation->created_at)->format('F d, Y') }}</p>
                                <p class="mb-1"><strong>Quotation #:</strong>
                                    QTN-{{ str_pad($quotation->id, 5, '0', STR_PAD_LEFT) }}</p>
                            </div>
                            <div class="col-6 text-end">
                                <h4 class="text-primary">{{ $quotation->company_name }}</h4>
                                <p class="mb-1">{{ $quotation->company_phone }}</p>
                                <p class="mb-1">{{ $quotation->company_email }}</p>
                                <p class="mb-0">{{ $quotation->company_website }}</p>
                            </div>
                        </div>

                        <hr>

                        <!-- Client Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <p>To,</p>
                                <p>{{ $client_designation ?? 'Director (IT)' }}</p>
                                <p>{{ $client_name ?? 'N/A' }}</p>
                                <p>{{ $client_address ?? 'N/A' }}</p>
                                @if ($attention_to)
                                    <p>Attention: {{ $attention_to }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Subject -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary">Subject: {{ $quotation->subject }}</h5>
                            </div>
                        </div>

                        <!-- Body Content -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div style="white-space: pre-line;">{{ $quotation->body_content }}</div>
                            </div>
                        </div>

                        <!-- Products Table -->
                        @if ($quotation->items && count($quotation->items) > 0)
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3">Quotation Details</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="5%" class="text-center">S/L</th>
                                                    <th width="45%">Product Description</th>
                                                    <th width="10%" class="text-center">QTY</th>
                                                    <th width="20%" class="text-end">Unit Price (৳)</th>
                                                    <th width="20%" class="text-end">Total Price (৳)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($quotation->items as $index => $item)
                                                    <tr>
                                                        <td class="text-center">{{ $index + 1 }}.</td>
                                                        <td>
                                                            <strong>{{ $item->product->name ?? 'N/A' }}</strong>
                                                            @if ($item->product->brand->name ?? false)
                                                                <br><small class="text-muted">Brand:
                                                                    {{ $item->product->brand->name }}</small>
                                                            @endif
                                                            @if ($item->product->model ?? false)
                                                                <br><small class="text-muted">Model:
                                                                    {{ $item->product->model }}</small>
                                                            @endif
                                                            @if ($item->description)
                                                                <br><small
                                                                    class="text-muted">{{ $item->description }}</small>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">{{ $item->quantity }}</td>
                                                        <td class="text-end">৳ {{ number_format($item->unit_price, 2) }}
                                                        </td>
                                                        <td class="text-end">৳
                                                            {{ number_format($item->quantity * $item->unit_price, 2) }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="table-light">
                                                <tr>
                                                    <td colspan="3" class="text-end"><strong>Sub Total:</strong></td>
                                                    <td colspan="2" class="text-end"><strong>৳
                                                            {{ number_format($quotation->subtotal, 2) }}</strong></td>
                                                </tr>
                                                @if ($quotation->discount_amount > 0)
                                                    <tr>
                                                        <td colspan="3" class="text-end"><strong>Discount:</strong></td>
                                                        <td colspan="2" class="text-end"><strong>-৳
                                                                {{ number_format($quotation->discount_amount, 2) }}</strong>
                                                        </td>
                                                    </tr>
                                                @endif
                                                <tr class="table-primary">
                                                    <td colspan="3" class="text-end"><strong>Total Amount:</strong></td>
                                                    <td colspan="2" class="text-end"><strong>৳
                                                            {{ number_format($quotation->total_amount, 2) }}</strong></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Additional Enclosed -->
                        @if ($quotation->additional_enclosed)
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-primary">Enclosed Documents:</h5>
                                    <div style="white-space: pre-line;">{{ $quotation->additional_enclosed }}</div>
                                </div>
                            </div>
                        @endif

                        <!-- Terms and Conditions -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary">Terms and Conditions:</h5>
                                <div style="white-space: pre-line;">{{ $quotation->terms_conditions }}</div>
                            </div>
                        </div>

                        <!-- Signatory Section -->
                        <div class="row mt-5">
                            <div class="col-6">
                                <p><strong>Prepared By:</strong><br>
                                    {{ $quotation->signatory_name }}<br>
                                    {{ $quotation->signatory_designation }}</p>
                            </div>
                            <div class="col-6 text-end">
                                <div class="border-top pt-3 mt-4" style="width: 200px; margin-left: auto;">
                                    <p class="text-center mb-0"><strong>Authorized Signature</strong></p>
                                    <p class="text-center mb-0">{{ $quotation->company_name }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include PDF generation libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // PDF Generation
            document.getElementById('generatePdf').addEventListener('click', function() {
                const element = document.getElementById('quotation-content');
                const button = this;

                // Show loading state
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i> Generating PDF...';
                button.disabled = true;

                html2canvas(element, {
                    scale: 2,
                    useCORS: true,
                    logging: false
                }).then(canvas => {
                    const imgData = canvas.toDataURL('image/png');
                    const pdf = new jspdf.jsPDF('p', 'mm', 'a4');
                    const imgProps = pdf.getImageProperties(imgData);
                    const pdfWidth = pdf.internal.pageSize.getWidth();
                    const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

                    let heightLeft = pdfHeight;
                    let position = 0;

                    pdf.addImage(imgData, 'PNG', 0, position, pdfWidth, pdfHeight);
                    heightLeft -= pdf.internal.pageSize.getHeight();

                    // Add new pages if content is too long
                    while (heightLeft >= 0) {
                        position = heightLeft - pdfHeight;
                        pdf.addPage();
                        pdf.addImage(imgData, 'PNG', 0, position, pdfWidth, pdfHeight);
                        heightLeft -= pdf.internal.pageSize.getHeight();
                    }

                    pdf.save(
                        'quotation-{{ $quotation->id }}-{{ \Carbon\Carbon::parse($quotation->created_at)->format('Ymd') }}.pdf'
                    );

                    // Restore button state
                    button.innerHTML = originalText;
                    button.disabled = false;
                }).catch(error => {
                    console.error('PDF generation error:', error);
                    alert('Error generating PDF. Please try again.');
                    button.innerHTML = originalText;
                    button.disabled = false;
                });
            });
        });
    </script>

    <style>
        #quotation-content {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .text-primary {
            color: #2c5aa0 !important;
        }
    </style>
@endsection
