<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Challan {{ $challan->reference_number }}</title>
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
            font-size: 24px;
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
            background: #f8fafc;
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
            margin-bottom: 25px;
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
                    <strong>Ref:</strong> {{ $challan->reference_number }}<br>
                    <strong>Date:</strong> {{ $challan->challan_date ? \Carbon\Carbon::parse($challan->challan_date)->format('d M Y') : date('d M Y') }}
                </div>
            </td>
            <td style="width:50%;" class="report-title">
                <h1>DELIVERY CHALLAN</h1>
                @if($challan->work_order_number)
                    <p>Work Order: {{ $challan->work_order_number }}</p>
                @endif
            </td>
        </tr>
    </table>

    <!-- Recipient Info Card -->
    <table class="recipient-card" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <div style="font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b; margin-bottom: 4px;">RECIPIENT / DELIVER TO</div>
                <div style="font-size: 13px; font-weight: 700; color: #0f172a;">{{ $recipient_organization ?? ($challan->client_name ?? 'N/A') }}</div>
                <div style="font-size: 12px; color: #475569; margin-top: 2px;">{{ $recipient_designation ?? 'Director (IT)' }}</div>
                <div style="font-size: 12px; color: #64748b; margin-top: 2px;">{{ $recipient_address ?? ($challan->client_address ?? 'N/A') }}</div>
                @if(!empty($attention_to))
                    <div style="font-size: 11px; color: #4f46e5; margin-top: 4px; font-weight: 600;">Attention: {{ $attention_to }}</div>
                @endif
            </td>
        </tr>
    </table>

    <!-- Subject Letter Note -->
    <div style="margin-bottom: 20px; font-size: 12px; color: #334155; line-height: 1.5;">
        <strong style="color: #0f172a;">Subject:</strong> <strong style="color: #0f172a;">{{ $subject ?? 'Delivery Challan' }}</strong>
        <div style="margin-top: 8px;">
            We assure you that we provide our best service at all times. Please receive the items listed below:
        </div>
    </div>

    <!-- Items Table -->
    <table class="items-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 8%; text-align: center;">#</th>
                <th style="width: 72%; text-align: left;">Product / Item Description</th>
                <th style="width: 20%; text-align: center;">Quantity</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($challan->challanItems as $index => $item)
                <tr style="background-color: {{ $index % 2 == 1 ? '#f8fafc' : '#ffffff' }};">
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="fw-bold">{!! nl2br(e($item->description)) !!}</td>
                    <td class="text-center fw-bold">{{ number_format($item->quantity) }} {{ $item->unit ?? 'No' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center" style="padding: 16px; color: #64748b;">No items found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Signatures & Seal (Customer & Authorized) -->
    @if(!isset($show_signature) || $show_signature || !isset($show_seal) || $show_seal)
        <table style="width: 100%; border-collapse: collapse; margin-top: 60px;">
            <tr>
                <td width="33%" align="center" style="vertical-align: bottom;">
                    <table align="center" style="width: 160px; margin: 0 auto 8px auto; border-collapse: collapse;">
                        <tr>
                            <td style="border-top: 1.5px solid #475569; height: 1px; font-size: 1px; line-height: 1px;">&nbsp;</td>
                        </tr>
                    </table>
                    <div style="font-size: 11px; font-weight: 600; color: #475569;">Customer's Signature</div>
                </td>
                <td width="34%" align="center" style="vertical-align: bottom;">
                    @if(!isset($show_seal) || $show_seal)
                        @php
                            $sealImg = $seal_image ?? null;
                            $sealSrc = ($sealImg && file_exists(public_path($sealImg))) ? public_path($sealImg) : (file_exists(public_path('sil.png')) ? public_path('sil.png') : null);
                        @endphp
                        @if($sealSrc)
                            <img src="{{ $sealSrc }}" style="max-height: 75px;" alt="Company Seal">
                        @endif
                    @endif
                </td>
                <td width="33%" align="center" style="vertical-align: bottom;">
                    @if(!isset($show_signature) || $show_signature)
                        @if(!empty($signature_image) && file_exists(public_path($signature_image)))
                            <div style="text-align: center; margin-bottom: 4px;">
                                <img src="{{ public_path($signature_image) }}" style="max-height: 50px;" alt="Signature">
                            </div>
                        @endif
                        <div style="display: inline-block; text-align: center;">
                            <div style="font-size: 12px; font-weight: 700; color: #0f172a;">
                                <span style="border-top: 1.5px solid #475569; padding-top: 4px; display: inline-block;">
                                    {{ $challan->signatory_name ?? 'N/A' }}
                                </span>
                            </div>
                            <div style="font-size: 11px; color: #475569; margin-top: 2px;">{{ $challan->signatory_designation ?? 'N/A' }}</div>
                            <div style="font-size: 11px; font-weight: 600; color: #1e293b; margin-top: 2px;">{{ $challan->company_name ?? 'N/A' }}</div>
                        </div>
                    @endif
                </td>
            </tr>
        </table>
    @endif
</body>
</html>
