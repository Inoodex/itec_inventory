<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Warranty Claim Receipt - {{ $claim->claim_no }}</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #222;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #7638ff;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0 0 5px 0;
            color: #7638ff;
            font-size: 22px;
        }
        .header p {
            margin: 0;
            color: #666;
            font-size: 12px;
        }
        .receipt-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 20px;
            background: #f4efff;
            padding: 8px;
            border-radius: 4px;
            color: #7638ff;
        }
        .section-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .section-table th, .section-table td {
            padding: 8px 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .section-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
            width: 30%;
        }
        .terms {
            font-size: 10px;
            color: #555;
            margin-top: 30px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .footer-signatures {
            margin-top: 50px;
            width: 100%;
        }
        .footer-signatures td {
            width: 50%;
            text-align: center;
            vertical-align: bottom;
        }
        .signature-line {
            border-top: 1px dashed #777;
            width: 80%;
            margin: 0 auto 5px auto;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Inoodex Inventory</h2>
        <p>Official Warranty Claim Acknowledgement Receipt</p>
    </div>

    <div class="receipt-title">
        WARRANTY CLAIM RECEIPT #{{ $claim->claim_no }}
    </div>

    <table class="section-table">
        <tr>
            <th>Claim Number</th>
            <td><strong>{{ $claim->claim_no }}</strong></td>
        </tr>
        <tr>
            <th>Claim Date</th>
            <td>{{ $claim->claim_date->format('d F Y') }}</td>
        </tr>
        <tr>
            <th>Invoice Number</th>
            <td>{{ $claim->sale?->order_no ?? '#' . $claim->sale_id }}</td>
        </tr>
        <tr>
            <th>Customer Name</th>
            <td>{{ $claim->customer?->name ?? 'Guest Customer' }}</td>
        </tr>
        <tr>
            <th>Customer Phone</th>
            <td>{{ $claim->customer?->phone ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Product Name</th>
            <td><strong>{{ $claim->product?->name }}</strong></td>
        </tr>
        <tr>
            <th>Serial Number</th>
            <td>{{ $claim->serial_number ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Warranty Expiry Date</th>
            <td>{{ $claim->warranty_expiry_date->format('d F Y') }}</td>
        </tr>
        <tr>
            <th>Reported Problem</th>
            <td>{{ $claim->problem_description }}</td>
        </tr>
        <tr>
            <th>Physical Condition</th>
            <td>{{ $claim->condition_notes ?? 'Standard condition' }}</td>
        </tr>
        <tr>
            <th>Current Claim Status</th>
            <td><strong>{{ strtoupper(str_replace('_', ' ', $claim->status)) }}</strong></td>
        </tr>
        <tr>
            <th>Received By Staff</th>
            <td>{{ $claim->receiver?->name ?? 'Staff' }}</td>
        </tr>
    </table>

    <div class="terms">
        <strong>Terms & Conditions for Warranty Claims:</strong>
        <ol style="margin-top: 5px; padding-left: 15px;">
            <li>Physical damage, liquid damage, burn, or unauthorized tampering voids warranty.</li>
            <li>Estimated repair / inspection turnaround time is 7 to 15 working days.</li>
            <li>Customer must present this original claim receipt when receiving the repaired/replaced item.</li>
        </ol>
    </div>

    <table class="footer-signatures">
        <tr>
            <td>
                <div class="signature-line"></div>
                Customer Signature
            </td>
            <td>
                <div class="signature-line"></div>
                Authorized Signature & Seal
            </td>
        </tr>
    </table>
</body>
</html>
