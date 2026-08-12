<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Bill {{ $bill->reference_number }}</title>
    @php
        $padPath = public_path('assets/invoice/final_pad.png');
        $padBase64 = file_exists($padPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($padPath)) : '';
    @endphp
    <style>
        @page {
            @if($padBase64)
            background-image: url('{{ $padBase64 }}');
            background-image-resize: 6;
            @endif
            margin-top: 45mm;
            margin-bottom: 25mm;
            margin-left: 15mm;
            margin-right: 15mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Helvetica, Arial, sans-serif;
        }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #0f172a;
            line-height: 1.4;
        }

        .header-table {
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 12px;
        }

        .ref-box {
            font-size: 11px;
            color: #475569;
        }

        .report-title {
            text-align: right;
        }

        .report-title h1 {
            font-size: 26px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .report-title p {
            font-size: 12px;
            color: #64748b;
            margin-top: 3px;
        }

        .recipient-card {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            /* background: #f8fafc; */
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .recipient-card td {
            padding: 14px 18px;
            vertical-align: top;
        }

        .items-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 20px;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            overflow: hidden;
        }

        .items-table th {
            background-color: #1e293b;
            color: #ffffff;
            padding: 10px 12px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .items-table td {
            padding: 10px 12px;
            font-size: 12px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
        }

        .card-box {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 11px;
            color: #334155;
            margin-bottom: 20px;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
    </style>
</head>
<body>
    <!-- Top Reference & Header -->
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width:50%; vertical-align: top;">
                <div class="ref-box">
                    <strong>Ref:</strong> {{ $bill->reference_number }}<br>
                    <strong>Date:</strong> {{ $bill->bill_date ? \Carbon\Carbon::parse($bill->bill_date)->format('d M Y') : date('d M Y') }}
                </div>
            </td>
            <td style="width:50%;" class="report-title">
                <h1>OFFICIAL BILL</h1>
                @if($bill->work_order_number)
                    <p>Work Order: {{ $bill->work_order_number }}</p>
                @endif
            </td>
        </tr>
    </table>

    <!-- Recipient Info Card -->
    <table class="recipient-card" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <div style="font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b; margin-bottom: 4px;">RECIPIENT / BILL TO</div>
                <div style="font-size: 13px; font-weight: 700; color: #0f172a;">{{ $recipient_organization }}</div>
                <div style="font-size: 12px; color: #475569; margin-top: 2px;">{{ $recipient_designation ?: 'Director (IT)' }}</div>
                <div style="font-size: 12px; color: #64748b; margin-top: 2px;">{{ $recipient_address }}</div>
                @if(!empty($attention_to))
                    <div style="font-size: 11px; color: #4f46e5; margin-top: 4px; font-weight: 600;">Attention: {{ $attention_to }}</div>
                @endif
            </td>
        </tr>
    </table>

    <!-- Subject Letter Note -->
    <div style="margin-bottom: 20px; font-size: 12px; color: #334155; line-height: 1.5;">
        <strong style="color: #0f172a;">Subject:</strong> <strong style="color: #0f172a;">{{ $subject ?? 'Bill for Supplying of Products/Services' }}</strong>
        <div style="margin-top: 8px;">
            Dear Sir,<br>
            Regarding the mentioned subject, we have successfully delivered the products/services in accordance with your specifications. Kindly proceed with the settlement of the bill below.
        </div>
    </div>

    <!-- Items Table -->
    <table class="items-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 6%; text-align: center;">#</th>
                <th style="width: 48%; text-align: left;">Product / Service Description</th>
                <th style="width: 14%; text-align: center;">Qty</th>
                <th style="width: 16%; text-align: right;">Unit Price</th>
                <th style="width: 16%; text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($bill->billItems as $index => $item)
                <tr style="background-color: {{ $index % 2 == 1 ? '#f8fafc' : '#ffffff' }};">
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="fw-bold">{!! nl2br(e($item->description)) !!}</td>
                    <td class="text-center">{{ number_format($item->quantity) }} {{ $item->unit ?? 'No' }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right fw-bold">{{ number_format($item->total, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 16px; color: #64748b;">No bill items found</td>
                </tr>
            @endforelse
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="4" class="text-right" style="padding: 10px 12px; color: #0f172a;">Total Payable Amount:</td>
                <td class="text-right" style="padding: 10px 12px; color: #4f46e5; font-size: 13px;">{{ number_format($bill->total_amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Amount in Words Card -->
    <div class="card-box" style="margin-bottom: 20px;">
        <strong style="color: #0f172a; text-transform: uppercase;">Amount In Words:</strong>
        <span style="font-style: italic; font-weight: 600; color: #4f46e5; margin-left: 6px;">{{ $amount_in_words }}</span>
    </div>

    <!-- Bank Details & Terms Grid -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 25px;">
        <tr>
            <!-- Bank Details (Left) -->
            @if(!empty($bank_details))
                <td style="width: 50%; vertical-align: top; padding-right: 10px;">
                    <table style="width: 100%; border-collapse: separate; border-spacing: 0; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 12px;">
                        <tr>
                            <td style="background-color: #1e293b; color: #ffffff; padding: 8px 14px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px;">
                                BANK SETTLEMENT DETAILS
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 12px 14px;">
                                <table style="width: 100%; font-size: 11px; color: #334155; border-collapse: collapse;" cellpadding="3">
                                    <tr>
                                        <td style="width: 40%; color: #64748b; font-weight: 600;">Account Name:</td>
                                        <td style="font-weight: 700; color: #0f172a;">{{ $bank_details['account_name'] ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="color: #64748b; font-weight: 600;">Bank Name:</td>
                                        <td style="font-weight: 600; color: #1e293b;">{{ $bank_details['bank_name'] ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="color: #64748b; font-weight: 600;">Branch:</td>
                                        <td>{{ $bank_details['branch'] ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="color: #64748b; font-weight: 600;">Account No:</td>
                                        <td style="font-weight: 800; color: #4f46e5; font-size: 12px;">{{ $bank_details['account_number'] ?? 'N/A' }}</td>
                                    </tr>
                                    @if(!empty($bank_details['account_type']))
                                        <tr>
                                            <td style="color: #64748b; font-weight: 600;">Account Type:</td>
                                            <td>{{ $bank_details['account_type'] }}</td>
                                        </tr>
                                    @endif
                                    @if(!empty($bank_details['routing_number']))
                                        <tr>
                                            <td style="color: #64748b; font-weight: 600;">Routing No:</td>
                                            <td>{{ $bank_details['routing_number'] }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            @endif

            <!-- Terms & Conditions (Right) -->
            @if(!empty($terms_conditions))
                <td style="width: 50%; vertical-align: top; padding-left: 10px;">
                    <table style="width: 100%; border-collapse: separate; border-spacing: 0; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 12px;">
                        <tr>
                            <td style="background-color: #1e293b; color: #ffffff; padding: 8px 14px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px;">
                                TERMS &amp; CONDITIONS
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 12px 14px; font-size: 11px; color: #475569; line-height: 1.6;">
                                @php $termIndex = 1; @endphp
                                @foreach (explode("\n", $terms_conditions) as $term)
                                    @if (trim($term) !== '')
                                        <div style="margin-bottom: 4px;">
                                            <strong style="color: #4f46e5;">{{ $termIndex++ }}.</strong> {{ trim($term) }}
                                        </div>
                                    @endif
                                @endforeach
                            </td>
                        </tr>
                    </table>
                </td>
            @endif
        </tr>
    </table>

    <!-- Signature & Seal Block -->
    @if(!isset($show_signature) || $show_signature || !isset($show_seal) || $show_seal)
        <table style="width: 100%; border-collapse: collapse; margin-top: 40px;">
            <tr>
                <td width="50%" align="left" style="vertical-align: bottom;">
                    @if(!isset($show_seal) || $show_seal)
                        @php
                            $sealImg = $company['seal_image'] ?? null;
                            $sealSrc = ($sealImg && file_exists(public_path($sealImg))) ? public_path($sealImg) : (file_exists(public_path('sil.png')) ? public_path('sil.png') : null);
                        @endphp
                        @if($sealSrc)
                            <img src="{{ $sealSrc }}" style="max-height: 80px;" alt="Company Seal">
                        @endif
                    @endif
                </td>
                <td width="50%" align="right" style="vertical-align: bottom;">
                    @if(!isset($show_signature) || $show_signature)
                        @php
                            $sigImg = $company['signature_image'] ?? null;
                        @endphp
                        @if($sigImg && file_exists(public_path($sigImg)))
                            <div style="text-align: right; margin-bottom: 4px;">
                                <img src="{{ public_path($sigImg) }}" style="max-height: 50px;" alt="Signature">
                            </div>
                        @endif
                        <div style="display: inline-block; text-align: left;">
                            <div style="font-size: 12px; font-weight: 700; color: #0f172a;">
                                <span style="border-top: 1.5px solid #475569; padding-top: 4px; display: inline-block;">
                                    {{ $company['signatory_name'] ?? 'N/A' }}
                                </span>
                            </div>
                            <div style="font-size: 11px; color: #475569; margin-top: 2px;">{{ $company['signatory_designation'] ?? 'N/A' }}</div>
                            <div style="font-size: 11px; font-weight: 600; color: #1e293b; margin-top: 2px;">{{ $company['name'] ?? 'N/A' }}</div>
                        </div>
                    @endif
                </td>
            </tr>
        </table>
    @endif
</body>
</html>