<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Service;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\User;

class PaymentController extends Controller
{
    public function payments(Request $request, $id, $payment_for){
        $payments = Payment::where('sale_id',$id)->where('payment_for', $payment_for)->get();
        
        // Get the bill (service or sale) information
        $bill = null;
        if($payment_for == '1'){
            $bill = Service::where('id', $id)->first();
        }
        if($payment_for == '2'){
            $bill = Sale::where('id', $id)->first();
        }
        
        return view('frontend.pages.payment.bill_payment', compact('payments','id', 'payment_for', 'bill'));
    }

    public function addPayment(Request $request){
        if($request->payment_for == '1'){
            $bill = Service::where('id', $request->id)->first();
        }
        if($request->payment_for == '2'){
            $bill = sale::where('id', $request->id)->first();
        }
        if(!$bill) return redirect()->back()->with(['error' => getNotify(10)]);



        $payment = new Payment;
        $payment->payment_for = $request->payment_for;
        $payment->customer_id = $bill->customer_id;
        $payment->sale_id = $bill->id;
        $payment->payment_method = $request->payment_method_id;
        $payment->amount = $request->amount;
        $payment->remarks = $request->remarks;
        $payment->save();

        $bill->paid_amount += $request->amount;
        $bill->due_amount = max(0,$bill->bill-$bill->paid_amount);
        $bill->update();

        return redirect()->back()->with(['success' => getNotify(1)]);
    }

    public function updatePayment(Request $request, $id){
        $payment = Payment::where('id',$id)->first();
        if(!$payment) return redirect()->back()->with(['error' => getNotify(10)]);
        if($payment->payment_for == '1'){
            $bill = Service::where('id', $payment->sale_id)->first();
        }
        if($payment->payment_for == '2'){
            $bill = Sale::where('id', $payment->sale_id)->first();
        }
        if(!$bill) return redirect()->back()->with(['error' => getNotify(10)]);

        $bill->paid_amount = max(0,$bill->paid_amount - $payment->amount);
        $bill->paid_amount += $request->amount;
        $bill->due_amount = max(0,$bill->bill - $bill->paid_amount);
        $bill->update();

        $payment->payment_method = $request->payment_method_id;
        $payment->amount = $request->amount;
        $payment->remarks = $request->remarks;
        $payment->update();

        return redirect()->back()->with(['success' => getNotify(2)]);

    }
    public function deletePayment(Request $request, $id){
        $payment = Payment::where('id',$id)->first();
        if(!$payment) return redirect()->back()->with(['error' => getNotify(10)]);
        if($payment->payment_for == '1'){
            $bill = Service::where('id', $payment->sale_id)->first();
        }
        if($payment->payment_for == '2'){
            $bill = Sale::where('id', $payment->sale_id)->first();
        }
        if(!$bill) return redirect()->back()->with(['error' => getNotify(10)]);

        $bill->paid_amount = max(0,$bill->paid_amount - $payment->amount);
        $bill->due_amount = max(0,$bill->bill - $bill->paid_amount);
        $bill->update();

        $payment->delete();

        return redirect()->back()->with(['success' => getNotify(3)]);

    }
}
