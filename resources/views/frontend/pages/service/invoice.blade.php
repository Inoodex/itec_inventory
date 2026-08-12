<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Invoice</title>
    <link rel="stylesheet" href="{{ asset('assets/invoice/style.css') }}" />
</head>

<body>
    <button class="print-btn no-print" onclick="window.print()">
        Print Invoice
    </button>

    <div class="a4-container">
        <div class="invoice-header">
            <div class="header__left">
                <div class="logo">
                    <img src="{{ asset('assets/invoice/logo-transparent.webp') }}" alt="" />
                </div>
                <div class="company-info">
                    <h1>Intelligent Technology</h1>
                    <p>Phone: 01904400202</p>
                    <p>Email: info.itechbd@yahoo.com</p>
                </div>
            </div>
            <div class="invoice-title">
                <p>Date: {{ $service->created_at->format('d-m-Y') }}</p>
                <h1>INVOICE</h1>
            </div>
        </div>
        <!-- invoice address  -->
        <div class="address">
            <p>Address: House # 7, (3rd floor), Road # 4,</p>
            <p>Mirpur-10, Dhaka -1216, Bangladesh.</p>
        </div>

        <div class="invoice-details">
            <ul>
                <li class="details__item">
                    <div class="customer">
                        <p>Customer:</p>
                        <p class="customer__name">{{ $service->name }}</p>
                    </div>

                    <div class="right">
                        <p>Sales Invoice</p>
                    </div>
                </li>
                <li class="details__item">
                    <div class="customer">
                        <p>Phone:</p>
                        <p class="customer__name">{{ $service->phone }}</p>
                    </div>

                    <div class="right">
                        <p>Invoice Date:
                            {{ $service->created_at->format('d-m-Y') }}

                        </p>
                    </div>
                </li>
                <li class="details__item">
                    <div class="customer">
                        <p>Address:</p>
                        <p class="customer__name">{{ $service->address }}</p>
                    </div>

                    <div class="right"></div>
                </li>
            </ul>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Item Names</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td>{{ $loop->index + 1 }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->qty ?? 'N/A' }}</td>
                        <td>{{ $item->unit_price ?? 'N/A' }}</td>
                        <td>{{ $item->total_price ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals-section">
            <div class="conditions">
                <h2>Terms & Conditions</h2>
                <br>
                <p>
                    1. Products can be returned within 7 days in their original, unopened
                    condition.
                </p>
                <br>
                <p>2. Refunds or exchanges are offered, but perishable goods
                    cannot be returned.</p>
                <br>
                <p>Contact us at <strong>01904400205</strong> with a valid receipt
                    for assistance.</p>
                </p>
            </div>
            <table class="totals-table">
                <tr>
                    <td>Sub Total:</td>
                    <td class="text-right">{{ $service->bill }} Tk</td>
                </tr>
                <tr>
                    <td>Discount:</td>
                    <td class="text-right">{{ $service->discount }} Tk</td>
                </tr>
                <tr>
                    <td>Total:</td>
                    <td class="text-right">{{ max(0, $service->bill - $service->discount) }} Tk</td>
                </tr>
                <tr>
                    <td>Received:</td>
                    <td class="text-right">{{ $service->paid_amount }} Tk</td>
                </tr>
                <tr>
                    <td>Total Due:</td>
                    <td class="text-right">{{ $service->due_amount }} Tk</td>
                </tr>
            </table>
        </div>
        <div class="in-words">
            <strong>In Words:</strong>
            @php
                $totalAmount = max(0, ($service->bill ?? 0) - ($service->discount ?? 0));
            @endphp
            {{ numberToWords($totalAmount) }} Taka Only
        </div>

        <!-- signature  -->
        <div class="signature">
            <div class="signature__left">
                <p>Customer Signature</p>
            </div>
            <div class="signature__right">
                <p>Authorized Signature</p>
            </div>
        </div>
    </div>

    <script>
        // প্রিন্ট বাটনে ক্লিক করলে প্রিন্ট ডায়ালগ ওপেন হবে
        document
            .querySelector(".print-btn")
            .addEventListener("click", function() {
                window.print();
            });

        // স্বয়ংক্রিয়ভাবে প্রিন্ট ডায়ালগ ওপেন করতে চাইলে নিচের লাইনটি আনকমেন্ট করুন
        // window.print();
    </script>
</body>

</html>
