<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales pdf</title>
</head>
<body>
    <div style="text-align:center;">
        <H3 style="text-align:center; margin:0;">Quick Phone Fix N More</H3>
        <p style="text-align:center; margin:0; font-size:14px;">Service Payments List</p>
        @if($request->from && $request->to)
            <p style="text-align:center; margin:0; font-size:12px;">From: {{$request->from}}</p>
            <p style="text-align:center; margin:0; font-size:12px;">To: {{$request->to}}</p>
        @endif
        @if($request->payments_method)
            <p style="text-align:center; margin:0; font-size:12px; text-transform: capitalize">Payment Method: {{getArrayData(paymentMethods(), $request->payments_method)}}</p>
        @endif
        <p style="text-align:center; margin:0; font-size:12px;">Generated Date: {{date('Y-m-d')}}</p>
    </div>
    <div style="display: flex; justify-content: center; margin-top: 10px; overflow-x: auto;">
        <table style="min-width:500px; margin: 0 auto; text-align: center; font-size:10px; border-collapse: collapse; border: 1px solid #000; ">
            <thead>
                <tr role="row">
                    <th style="border: 1px solid #00;">#</th>
                    <th style="border: 1px solid #00;">Date</th>
                    <th style="border: 1px solid #00;">Payment Method</th>
                    <th style="border: 1px solid #00;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($payments as $payment)
                <tr role="row" class="odd">
                    <td style="border: 1px solid #00;">{{$loop->index+1}}</td>
                    <td style="border: 1px solid #00;">{{$payment->created_at->format('Y-m-d')}}</td>
                    <td style="border: 1px solid #00;">{{getArrayData(paymentMethods(), $payment->payment_method)}}</td>
                    <td style="border: 1px solid #00;">{{$payment->amount}}</td>
                </tr>
                @php
                      if(!isset($methodWise[$payment->payment_method])) $methodWise[$payment->payment_method] = 0;
                      $methodWise[$payment->payment_method] += $payment->amount;
                      if(!isset($total)) $total = 0;
                      $total += $payment->amount;
                  @endphp
                @endforeach
                @if(isset($methodWise))
                    @foreach ($methodWise as $key => $value)
                        <tr>
                            <th colspan="3" style="text-align:right;">{{getArrayData(paymentMethods(), $key)}}</th>
                            <th>{{$value}}</th>
                        </tr>
                    @endforeach
                    <tr>
                        <th colspan="3" style="text-align:right;">Total</th>
                        <th>{{$total}}</th>
                    </tr>

                @endif
            </tbody>
        </table>
    </div>

</body>
</html>