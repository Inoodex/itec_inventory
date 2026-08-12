<?php

namespace App\Http\Controllers;

use Input;
use Validator;
use Carbon\Carbon;
use App\Models\Sale;
use App\Models\User;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Service;
use App\Models\Admin\Category;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ServiceController extends Controller
{
    public function downloadPdf(Request $request)
    {
        $services = Service::leftjoin('users', 'users.id', '=', 'services.repaired_by');

        if ($request->from != "" && $request->to != "") {
            $from = date('Y-m-d 00:00:00', strtotime($request->from));
            $to = date('Y-m-d 23:59:59', strtotime($request->to));
            $services = $services->whereBetween('services.created_at', [$from, $to]);
        }

        if ($request->service_type != "") {
            if ($request->service_type == "paid") {
                $services = $services->where('services.due_amount', '=', '0');
            }
            if ($request->service_type == "due") {
                $services = $services->where('services.due_amount', '>', '0');
            }
        }

        if ($request->serach_by != "" && $request->key != "") {
            $services = $services->where('services.' . $request->serach_by, 'like', '%' . $request->key . '%');
        }

        $services = $services->where('services.status', '0')
            ->select('services.*', 'users.name as repaired_by')
            ->orderBy('id', 'desc')
            ->get();

        $services->load('product');
        $users = lib_serviceMan();

        $html = view('pdf.services', compact('services', 'users', 'request'))->render();
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'Helvetica',
        ]);
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('Services.pdf', 'I'), 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function index(Request $request)
    {

        // return $request->all();


        $services = Service::leftjoin('users','users.id','=','services.repaired_by');
        
        if ($request->from != "" && $request->to != "") {
            $from = date('Y-m-d 00:00:00', strtotime($request->from));
            $to = date('Y-m-d 23:59:59', strtotime($request->to));
            $services = $services->whereBetween('services.created_at', [$from, $to]);
        }

        if ($request->service_type != "") {
            if($request->service_type=="paid"){
                $services = $services->where('services.due_amount', '=', '0');
            }
            if($request->service_type=="due"){
                $services = $services->where('services.due_amount', '>', '0');
            }
        }

        if ($request->serach_by != "" && $request->key != "") {
           $services = $services->where('services.'.$request->serach_by, 'like', '%' . $request->key . '%');
        }


        $services = $services->where('services.status','0');
        $services = $services->select('services.*','users.name as repaired_by')->orderBy('id','desc')->get();
        $services->load('product');

        $users = lib_serviceMan();
        if($request->search_for == 'pdf'){
            $html = view('pdf.services', compact('services','users','request'))->render();
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'default_font' => 'Helvetica',
            ]);
            $mpdf->WriteHTML($html);
            return response($mpdf->Output('Services.pdf', 'I'), 200, [
                'Content-Type' => 'application/pdf',
            ]);
        }

        return view('frontend.pages.service.index',compact('services','users','request'));
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users  = User::get();
        $products = Product::with('category')->where('status', '1')->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('frontend.pages.service.create', compact('users', 'products', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->filled('phone')) {
            $request->merge([
                'phone' => preg_replace('/\D+/', '', (string) $request->phone),
            ]);
        }

        $attributes = $request->all();
        $rules = [
            'client_type' => 'required|in:new,existing',
            'name' => 'exclude_unless:client_type,new|required',
            'email' => 'nullable|email',
            'country_code' => 'nullable',
            'phone' => 'exclude_unless:client_type,new|required|numeric',
            'address' => 'nullable',
            'product_id' => 'required|exists:products,id',
            'product_number' => 'nullable',
            'total' => 'required|numeric',
            'bill' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'paid_amount' => 'nullable|numeric',
            'due_amount' => 'nullable|numeric',
            'remarks' => 'nullable|string',
            'payment_method_id' => [function ($attribute, $value, $fail) use ($request) {
                if ($request->paid_amount > 0 && !$value) {
                    $fail('The payment method is required when the paid amount is greater than 0.');
                }
            }],
            'warranty_duration' => 'nullable|numeric',
            'repaired_by' => 'nullable|numeric',
            'existing_client_id' => 'nullable|integer',
        ];

        $validation = Validator::make($attributes, $rules);
        if ($validation->fails()) {
            return redirect()->back()->with(['error' => getNotify(4)])->withErrors($validation)->withInput();
        }

        // Additional validation for existing client
        if ($request->client_type == 'existing' && !$request->existing_client_id) {
            return redirect()->back()->with(['error' => 'Please select an existing customer'])->withInput();
        }

        if ($request->client_type == 'existing') {
            $customer = Customer::find($request->existing_client_id);
            if (!$customer) {
                return redirect()->back()->with(['error' => 'The selected customer is invalid'])->withInput();
            }
        }

        $product = Product::findOrFail($request->product_id);

        if ($request->client_type == 'new') {
            $customerByPhone = Customer::where('phone', $request->phone)->first();
            $customerByEmail = Customer::where('email', $request->email)->first();
            if($request->email == "") $customerByEmail = null;

            if((!$customerByPhone && $customerByEmail)){
                $customer = $customerByEmail;
            }elseif(($customerByPhone && !$customerByEmail)){
                $customer = $customerByPhone;
            }elseif($customerByPhone && $customerByEmail && $customerByPhone->id == $customerByEmail->id){
                $customer = $customerByPhone;
            }elseif($customerByPhone && $customerByEmail && $customerByPhone->id != $customerByEmail->id){
                return redirect()->back()->with(['error' => 'The email is added for another customer.'])->withInput();
            } else {
                $customer = new Customer;
            }

            $customer->name = $request->name;
            if($request->email != "" )$customer->email = $request->email;
            $customer->country_code = $request->country_code;
            $customer->phone = $request->phone;
            $customer->address = $request->address;
            $customer->save();
        }

        $countryCode = $customer->country_code ?: $request->country_code;

        $service = new Service;
        $service->customer_id = $customer->id;
        $service->name = $customer->name;
        $service->country_code = $countryCode;
        $service->phone = $customer->phone;
        $service->email = $customer->email;
        $service->address = $customer->address;
        $service->product_id = $product->id;
        $service->product_name = $product->name;
        $service->product_number = $request->product_number;
        $service->total = $request->total??0;
        $service->discount = $request->discount??0;
        $service->bill = $request->bill??0;
        $service->paid_amount = $request->paid_amount??0;
        $service->due_amount = max(0,$request->bill-$request->paid_amount);
        $service->remarks = $request->remarks;
        $service->details = $request->details;
        $service->warranty_duration = $request->warranty_duration;
        $service->repaired_by = $request->repaired_by;
        $service->status = '0';
        $service->save();

        if($request->paid_amount > 0){
            $payment = new Payment;
            $payment->payment_for = '1';
            $payment->customer_id = $customer->id;
            $payment->sale_id = $service->id;
            $payment->payment_method = $request->payment_method_id ?: '1';
            $payment->amount = $request->paid_amount;
            $payment->remarks = $request->remarks;
            $payment->save();
        }

        return redirect()->route('service.index')->with(['success' => getNotify(1)]);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $service = Service::with('product')->where('id',$id)->first();
        if(!$service)abort(404);
        $products = Product::with('category')->where('status', '1')->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $serviceMans = lib_serviceMan();

        return view('frontend.pages.service.edit',compact('service','serviceMans','products','categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $service = Service::where('id',$id)->first();
        if(!$service)abort(404);

        if ($request->filled('phone')) {
            $request->merge([
                'phone' => preg_replace('/\D+/', '', (string) $request->phone),
            ]);
        }

        $attributes = $request->all();
        $rules = [
            'name' => 'required',
            'email' => 'nullable|email',
            'country_code' => 'nullable',
            'phone' => 'required|numeric',
            'address' => 'nullable',
            'product_id' => 'required|exists:products,id',
            'product_number' => 'nullable',
            'total' => 'required|numeric',
            'bill' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'remarks' => 'nullable|string',
            'warranty_duration' => 'nullable|numeric',
            'repaired_by' => 'nullable|numeric',
        ];
        $validation = Validator::make($attributes, $rules);
        if ($validation->fails()) {
            return redirect()->back()->with(['error' => getNotify(4)])->withErrors($validation)->withInput();
        }

        $product = Product::findOrFail($request->product_id);

        $customerByPhone = Customer::where('phone', $request->phone)->first();
        $customerByEmail = Customer::where('email', $request->email)->first();
        if($request->email == "") $customerByEmail = null;
        $customer =  new Customer;

        if((!$customerByPhone && $customerByEmail)){
            $customer = $customerByEmail;
        }elseif(($customerByPhone && !$customerByEmail)){
            $customer = $customerByPhone;
        }elseif($customerByPhone && $customerByEmail && $customerByPhone->id == $customerByEmail->id){
            $customer = $customerByPhone;
        }elseif($customerByPhone && $customerByEmail && $customerByPhone->id != $customerByEmail->id){
            return redirect()->back()->with(['error' => 'The email is added for another customer.'])->withInput();
        }

        $customer->name = $request->name;
        if($request->email != "" )$customer->email = $request->email;
        $customer->country_code = $request->country_code;
        $customer->phone = $request->phone;
        $customer->address = $request->address;
        $customer->save();

        $countryCode = $customer->country_code ?: $request->country_code;

        $service->customer_id = $customer->id;
        $service->name = $customer->name;
        $service->country_code = $countryCode;
        $service->phone = $customer->phone;
        $service->email = $customer->email;
        $service->address = $customer->address;
        $service->product_id = $product->id;
        $service->product_name = $product->name;
        $service->product_number = $request->product_number;
        $service->total = $request->total??0;
        $service->discount = $request->discount??0;
        $service->bill = $request->bill??0;
        $service->due_amount = max(0,$request->bill-$service->paid_amount);
        $service->remarks = $request->remarks;
        $service->details = $request->details;
        $service->warranty_duration = $request->warranty_duration;
        $service->repaired_by = $request->repaired_by;
        $service->update();

        return redirect()->back()->with(['success' => getNotify(2)]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $service = Service::where('id',$id)->first();
        if(!$service)abort(404);
        $service->delete();

        return redirect()->back()->with(['success' => getNotify(3)]);
    }

    public function makeInvoice(Request $request, $serviceId){
        $service = Service::with('product')->where('id',$serviceId)->first();
        if(!$service)abort(404);
        $serviceMans = lib_serviceMan();

        $items = collect([
            (object)[
                'name' => $service->product_name ?? 'Service Job',
                'qty' => 1,
                'unit_price' => $service->bill ?? 0,
                'total_price' => $service->bill ?? 0,
            ],
        ]);

        $html = view('pdf.service_invoice', compact('service', 'serviceMans', 'items'))->render();
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'Helvetica',
        ]);
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('Service_Invoice_' . ($service->service_no ?? $service->id) . '.pdf', 'I'), 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function complatedService(Request $request){
        $services = Service::leftjoin('users','users.id','=','services.repaired_by');

        $defaultFilter = true;

        if ($request->from != "" && $request->to != "") {
            $from = date('Y-m-d 00:00:00', strtotime($request->from));
            $to = date('Y-m-d 23:59:59', strtotime($request->to));
            $services = $services->whereBetween('services.created_at', [$from, $to]);
            $defaultFilter = false;
        }

        if ($request->service_type != "") {
            if($request->service_type=="paid"){
                $services = $services->where('services.due_amount', '=', '0');
                $defaultFilter = false;
            }
            if($request->service_type=="due"){
                $services = $services->where('services.due_amount', '>', '0');
                $defaultFilter = false;
            }
        }

        if ($request->serach_by != "" && $request->key != "") {
            $services = $services->where('services.'.$request->serach_by, 'like', '%' . $request->key . '%');
            $defaultFilter = false;
        }

        if($defaultFilter){
            $startOfMonth = date('Y-m-01 00:00:00');
            $endOfMonth = date('Y-m-t 23:59:59');
            $services = $services->whereBetween('services.created_at', [$startOfMonth, $endOfMonth]);
        }

        $services = $services->where('services.status','1');
        $services = $services->select('services.*','users.name as repaired_by')->orderBy('id','desc')->get();
        $services->load('product');

        $users = lib_serviceMan();

        if($request->search_for == 'pdf'){
            $html = view('pdf.services', compact('services','users','request'))->render();
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'default_font' => 'Helvetica',
            ]);
            $mpdf->WriteHTML($html);
            return response($mpdf->Output('Services.pdf', 'I'), 200, [
                'Content-Type' => 'application/pdf',
            ]);
        }
        //Report
        $todaysRevenue = Service::whereDate('created_at', Carbon::today())->where('status','1')->sum('bill');
        $thisWeeksRevenue = Service::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('status','1')->sum('bill');
        $thisMonthsRevenue = Service::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->where('status','1')->sum('bill');
        $thisYearsRevenue = Service::whereBetween('created_at', [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()])->where('status','1')->sum('bill');
        $totalServiceDues = Service::where('status','1')->where('due_amount', '>', 0)->sum('due_amount');

        $todaysSalesRevenue = Sale::whereDate('created_at', Carbon::today())->where('status','1')->sum('bill');
        $thisWeeksSalesRevenue = Sale::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('status','1')->sum('bill');
        $thisMonthsSalesRevenue = Sale::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->where('status','1')->sum('bill');
        $thisYearsSalesRevenue = Sale::whereBetween('created_at', [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()])->where('status','1')->sum('bill');
        $totalSalesDues = Sale::where('due_amount', '>', 0)->sum('due_amount');

        $todaysDailySalesRevenue = 0;
        $thisWeeksDailySalesRevenue = 0;
        $thisMonthsDailySalesRevenue = 0;
        $thisYearsDailySalesRevenue = 0;

        $monthlyRevenue = Service::selectRaw('MONTH(created_at) as month, SUM(bill) as total')
        ->whereYear('created_at', Carbon::now()->year)
        ->where('status','1')
        ->groupBy('month')
        ->pluck('total', 'month')
        ->mapWithKeys(function ($total, $month) {
            $monthName = Carbon::createFromFormat('m', $month)->format('M');
            return [$monthName => $total];
        });

        $yearlyRevenue = Service::selectRaw('YEAR(created_at) as year, SUM(bill) as total')
        ->whereRaw('YEAR(created_at) >= YEAR(CURDATE()) - 9')
        ->where('status','1')
        ->groupBy('year')
        ->pluck('total', 'year');
        return view('frontend.pages.service.complated',compact('services','users','request','todaysRevenue','thisWeeksRevenue','thisMonthsRevenue','thisYearsRevenue','monthlyRevenue','yearlyRevenue','todaysSalesRevenue','thisWeeksSalesRevenue','thisMonthsSalesRevenue','thisYearsSalesRevenue','totalServiceDues','totalSalesDues','todaysDailySalesRevenue','thisWeeksDailySalesRevenue','thisMonthsDailySalesRevenue','thisYearsDailySalesRevenue'));
    }

    // public function makeComplate(Request $request, string $id){
    //     $service = Service::where('id',$id)->first();
    //     if(!$service)abort(404);
    //     $service->status = '1';
    //     $service->complated_date = date('Y-m-d');
    //     $service->update();


    //     $serviceMans = lib_serviceMan();

    //     if($service->email){
    //         Mail::to($service->email)->send(new PlaceOrderMail($service, $serviceMans));
    //     }
    //     if($service->phone && env('TWILIO_SID') && env('TWILIO_AUTH_TOKEN')){
    //         $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));

    //         $man = getArrayData($serviceMans,$service->repaired_by);

    //         $message  = "Quick Phone Fix N More\n";
    //         $message .= "7157 Ogontaz Ave, Philadelphia PA 19138\n";
    //         $message .= "Hotline: +234 901 791 9699\n\n";

    //         $message .= "Service Completed\n\n";

    //         $message .= "Customer Info:\n";
    //         $message .= "Name: {$service->name}\n";
    //         $message .= "Phone: {$service->phone}\n";
    //         $message .= "Email: {$service->email}\n";
    //         $message .= "Address: {$service->address}\n";

    //         $message .= "Service Info:\n";
    //         $message .= "Product: " . ($service->product?->name ?? $service->product_name) . "\n";
    //         $message .= "IMEI: {$service->product_number}\n";
    //         if($service->details)$message .= "Details: {$service->details}\n";
    //         $message .= "Warranty: {$service->warranty_duration} days\n";
    //         $message .= "Price: \${$service->bill}\n";
    //         $message .= "Paid: \${$service->paid_amount}\n";
    //         $message .= "Due: \${$service->due_amount}\n";
    //         $message .= "Repaired By: {$man}\n";
    //         $message .= "Date: " . now()->format('Y-m-d g:i A') . "\n\n";
    //         $message .= "Thank You. Please come again.\n\n";

    //         $message .= "Note: Warranty For {$service->warranty_duration} Days. Warranty Does Not Cover Broken Or Water Damage. No Refund, Exchange Only.";


    //         try{
    //             $twilio->messages->create(
    //                 $service->country_code . $service->phone,
    //                 [
    //                     'from' => env('TWILIO_PHONE_NUMBER'),
    //                     'body' => $message
    //                 ]
    //             );
    //         } catch (\Twilio\Exceptions\RestException $e) {}
    //     }

    //     return redirect()->back()->with(['success' => getNotify(2)]);
    // } 

    public function payments(Request $request){ 

        $payments = Payment::where('payment_for', 1);
        $service = null;

        if ($request->id) {
            $service = Service::find($request->id);
            if ($service) {
                $payments = $payments->where('sale_id', $service->id);
            }
        }

        $defaultFilter = true;

        if ($request->from != "" && $request->to != "") {
            $from = date('Y-m-d 00:00:00', strtotime($request->from));
            $to = date('Y-m-d 23:59:59', strtotime($request->to));
            $payments = $payments->whereBetween('payments.created_at', [$from, $to]);
            $defaultFilter = false;
        }

        if ($request->payments_method != "") {
            $payments = $payments->where('payments.payment_method', $request->payments_method);
            $defaultFilter = false;
        }

        if($defaultFilter){
            $startOfMonth = date('Y-m-01 00:00:00');
            $endOfMonth = date('Y-m-t 23:59:59');
            $payments = $payments->whereBetween('payments.created_at', [$startOfMonth, $endOfMonth]);
        }

        $payments = $payments->get();

        if($request->search_for == 'pdf'){
            $html = view('pdf.service_payments', compact('payments', 'request', 'service'))->render();
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'default_font' => 'Helvetica',
            ]);
            $mpdf->WriteHTML($html);
            return response($mpdf->Output('service_Payments.pdf', 'I'), 200, [
                'Content-Type' => 'application/pdf',
            ]);
        }

        return view('frontend.pages.service.payments',compact('payments','request','service'));
    }

    public function storeRating(Request $request){
        // return $request->all();
        $service = Service::where('id',$request->service_id)->first();
        if(!$service)abort(404);
        $service->rating = $request->rating;
        $service->review_comments = $request->comments;
        $service->update();
        return redirect()->back()->with(['success' => getNotify(2)]);
    }

}
