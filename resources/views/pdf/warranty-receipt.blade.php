<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Warranty Receipt #{{ $claim->claim_no }}</title>
    @php
        if (empty($padBase64)) {
            $padPath = public_path('assets/invoice/final_pad.png');
            $padBase64 = file_exists($padPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($padPath)) : '';
        }
    @endphp
    <style>
        @page {
            @if(!empty($padBase64))
            background-image: url('{{ $padBase64 }}');
            background-image-resize: 6;
            @endif
            margin-top: 42mm;
            margin-bottom: 32mm;
            margin-left: 15mm;
            margin-right: 15mm;
            footer: warrantyFooter;
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
    </style>
</head>

<body>
    <!-- Header Title & Order Meta -->
    <table style="width: 100%; margin-bottom: 20px; border-bottom: 2px solid #e2e8f0; padding-bottom: 12px;">
        <tr>
            <td style="vertical-align: bottom;">
                <span style="display: inline-block; background-color: #e0e7ff; color: #4f46e5; font-size: 10px; font-weight: 700; padding: 4px 10px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.5px;">
                    Official RMA Receipt
                </span>
            </td>
            <td align="right" style="vertical-align: bottom;">
                <h1 style="font-size: 26px; color: #0f172a; margin-bottom: 4px; font-weight: 800;">WARRANTY RECEIPT</h1>
                <div style="font-size: 15px; font-weight: 700; color: #4f46e5;">Claim No: #{{ $claim->claim_no }}</div>
                <div style="font-size: 12px; color: #64748b; margin-top: 3px;">
                    Claim Date: {{ $claim->claim_date ? $claim->claim_date->format('d M Y') : date('d M Y') }} &bull;
                    Invoice Ref: <strong>#{{ $claim->sale?->order_no ?? $claim->sale_id }}</strong>
                </div>
            </td>
        </tr>
    </table>

    <!-- Customer Info Card -->
    <table style="width: 100%; border-collapse: separate; border-spacing: 0; background: transparent; border: 1px solid #cbd5e1; border-radius: 12px; margin-bottom: 22px;">
        <tr>
            <td style="padding: 14px 18px; width: 33.33%; vertical-align: top;">
                <div style="font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b; margin-bottom: 4px;">CUSTOMER / CLIENT</div>
                <div style="font-size: 13px; font-weight: 600; color: #0f172a;">{{ $claim->customer?->name ?? 'Guest Customer' }}</div>
            </td>
            <td style="padding: 14px 18px; width: 33.33%; vertical-align: top; border-left: 1px solid #cbd5e1;">
                <div style="font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b; margin-bottom: 4px;">PHONE NUMBER</div>
                <div style="font-size: 13px; font-weight: 600; color: #0f172a;">{{ $claim->customer?->phone ?? 'N/A' }}</div>
            </td>
            <td style="padding: 14px 18px; width: 33.33%; vertical-align: top; border-left: 1px solid #cbd5e1;">
                <div style="font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b; margin-bottom: 4px;">ADDRESS / LOCATION</div>
                <div style="font-size: 13px; color: #0f172a;">{{ $claim->customer?->address ?? 'N/A' }}</div>
            </td>
        </tr>
    </table>

    <!-- Equipment & Claim Item Details Table -->
    <table style="width: 100%; border-collapse: separate; border-spacing: 0; margin-bottom: 20px; border: 1px solid #cbd5e1; border-radius: 12px; overflow: hidden;">
        <thead>
            <tr>
                <th style="background-color: #1e293b; color: #ffffff; padding: 10px 14px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; text-align: center; width: 6%;">#</th>
                <th style="background-color: #1e293b; color: #ffffff; padding: 10px 14px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; text-align: left; width: 44%;">Equipment &amp; Model</th>
                <th style="background-color: #1e293b; color: #ffffff; padding: 10px 14px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; text-align: left; width: 22%;">Serial / IMEI No</th>
                <th style="background-color: #1e293b; color: #ffffff; padding: 10px 14px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; text-align: center; width: 14%;">Warranty Expiry</th>
                <th style="background-color: #1e293b; color: #ffffff; padding: 10px 14px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; text-align: center; width: 14%;">Claim Status</th>
            </tr>
        </thead>
        <tbody>
            <tr style="background-color: #ffffff;">
                <td style="padding: 12px 14px; font-size: 12px; color: #334155; border-bottom: 1px solid #f1f5f9; text-align: center;">1</td>
                <td style="padding: 12px 14px; font-size: 12px; color: #334155; border-bottom: 1px solid #f1f5f9;">
                    <strong style="color: #0f172a; font-size: 13px;">{{ $claim->product?->name }}</strong>
                    @if(!empty($claim->product?->model))
                        <div style="font-size: 10px; color: #64748b; margin-top: 2px;">Model: {{ $claim->product->model }}</div>
                    @endif
                </td>
                <td style="padding: 12px 14px; font-size: 12px; color: #0f172a; border-bottom: 1px solid #f1f5f9;">
                    <span style="font-family: monospace; font-size: 12px; font-weight: 700; background: #f1f5f9; padding: 2px 6px; border-radius: 4px;">
                        {{ $claim->serial_number ?: 'N/A' }}
                    </span>
                </td>
                <td style="padding: 12px 14px; font-size: 12px; color: #334155; border-bottom: 1px solid #f1f5f9; text-align: center;">
                    {{ $claim->warranty_expiry_date ? $claim->warranty_expiry_date->format('d M Y') : 'N/A' }}
                </td>
                <td style="padding: 12px 14px; font-size: 11px; border-bottom: 1px solid #f1f5f9; text-align: center;">
                    <span style="background-color: #fef3c7; color: #92400e; font-weight: 700; padding: 3px 8px; border-radius: 20px; text-transform: uppercase; font-size: 10px;">
                        {{ strtoupper(str_replace('_', ' ', $claim->status)) }}
                    </span>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Problem & Physical Condition Card -->
    <table style="width: 100%; border-collapse: separate; border-spacing: 0; background: #fdfefe; border: 1px solid #cbd5e1; border-radius: 12px; margin-bottom: 22px;">
        <tr>
            <td style="padding: 14px 18px; width: 60%; vertical-align: top;">
                <div style="font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b; margin-bottom: 4px;">REPORTED PROBLEM / DEFECT</div>
                <div style="font-size: 12px; color: #0f172a; line-height: 1.5;">
                    {{ $claim->problem_description }}
                </div>
            </td>
            <td style="padding: 14px 18px; width: 40%; vertical-align: top; border-left: 1px solid #cbd5e1; background: #f8fafc;">
                <div style="font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b; margin-bottom: 4px;">INTAKE PHYSICAL CONDITION</div>
                <div style="font-size: 12px; color: #334155; line-height: 1.4;">
                    {{ $claim->condition_notes ?: 'Standard condition upon intake' }}
                </div>
                @if(!empty($claim->remarks))
                    <div style="font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b; margin-top: 8px; margin-bottom: 2px;">REMARKS</div>
                    <div style="font-size: 11px; color: #475569;">{{ $claim->remarks }}</div>
                @endif
            </td>
        </tr>
    </table>

    <!-- Summary & Terms Grid (Matching Invoice Layout) -->
    <table style="width: 100%; border-collapse: separate; border-spacing: 0; margin-bottom: 25px;">
        <tr>
            <td style="width: 58%; vertical-align: top; padding-right: 8px;">
                <table style="width: 100%; border-collapse: separate; border-spacing: 0; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 12px;">
                    <tr>
                        <td style="padding: 14px 16px;">
                            <h3 style="font-size: 12px; font-weight: 700; color: #0f172a; margin-bottom: 6px;">Warranty Terms &amp; Conditions</h3>
                            <p style="font-size: 10.5px; color: #475569; line-height: 1.4; margin-bottom: 5px;">&bull; Covers manufacturer hardware defects. Physical damage, liquid contact, burn, or tampering voids warranty.</p>
                            <p style="font-size: 10.5px; color: #475569; line-height: 1.4; margin-bottom: 5px;">&bull; Inspection and repair turnaround time is typically 7 to 15 working days.</p>
                            <p style="font-size: 10.5px; color: #475569; line-height: 1.4;">&bull; Customer must present this original receipt to collect the repaired or replaced item.</p>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 42%; vertical-align: top; padding-left: 8px;">
                <table style="width: 100%; border-collapse: separate; border-spacing: 0; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 12px;">
                    <tr>
                        <td style="padding: 14px 16px;">
                            <h3 style="font-size: 12px; font-weight: 700; color: #0f172a; margin-bottom: 6px;">RMA Intake Summary</h3>
                            <table style="width: 100%; border-collapse: collapse; font-size: 11.5px;">
                                <tr>
                                    <td style="padding: 3px 0; color: #64748b;">Invoice Date:</td>
                                    <td style="padding: 3px 0; text-align: right; font-weight: 600; color: #0f172a;">
                                        {{ $claim->sale?->created_at ? $claim->sale->created_at->format('d M Y') : 'N/A' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px 0; color: #64748b;">Intake Date:</td>
                                    <td style="padding: 3px 0; text-align: right; font-weight: 600; color: #0f172a;">
                                        {{ $claim->claim_date ? $claim->claim_date->format('d M Y') : date('d M Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px 0; color: #64748b;">Received By:</td>
                                    <td style="padding: 3px 0; text-align: right; font-weight: 600; color: #0f172a;">
                                        {{ $claim->receiver?->name ?? 'Staff' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px 0; border-top: 1px solid #cbd5e1; font-weight: 700; color: #4f46e5;">Estimated Service:</td>
                                    <td style="padding: 5px 0; border-top: 1px solid #cbd5e1; text-align: right; font-weight: 700; color: #4f46e5;">
                                        7 - 15 Days
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Signatures (Exact Match to Invoice Signatures) -->
    <table style="width: 100%; border-collapse: collapse; margin-top: 50px;">
        <tr>
            <td width="50%" align="center" style="vertical-align: top;">
                <table align="center" style="width: 180px; margin: 0 auto 8px auto; border-collapse: collapse;">
                    <tr>
                        <td style="border-top: 1.5px solid #475569; height: 1px; font-size: 1px; line-height: 1px;">&nbsp;</td>
                    </tr>
                </table>
                <div style="font-size: 11px; font-weight: 600; color: #475569;">Customer Signature</div>
            </td>
            <td width="50%" align="center" style="vertical-align: top;">
                <table align="center" style="width: 180px; margin: 0 auto 8px auto; border-collapse: collapse;">
                    <tr>
                        <td style="border-top: 1.5px solid #475569; height: 1px; font-size: 1px; line-height: 1px;">&nbsp;</td>
                    </tr>
                </table>
                <div style="font-size: 11px; font-weight: 600; color: #4f46e5;">Authorized Signature</div>
            </td>
        </tr>
    </table>

    <!-- Footer fixed at bottom right above pad graphic -->
    <htmlpagefooter name="warrantyFooter">
        <div style="text-align: right; font-size: 10.5px; color: #64748b;">
            Received By: <strong style="color: #0f172a;">{{ $claim->receiver?->name ?? 'Staff' }}</strong> &bull; Printed: {{ date('d M Y, h:i A') }}
        </div>
    </htmlpagefooter>

</body>

</html>
