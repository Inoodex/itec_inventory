<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\User;
use App\Models\Customer;
use Input;
use Validator;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $customers = Customer::latest()->get();
        return view('frontend.pages.customer.index', compact('customers'));
    }

    public function downloadPdf()
    {
        ini_set('memory_limit', '512M');
        $customers = Customer::latest()->get();
        $html = view('pdf.customers', compact('customers'))->render();
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'Helvetica',
        ]);
        $mpdf->WriteHTML($html);
        return response($mpdf->Output('Customer_List_' . now()->format('Y_m_d_His') . '.pdf', 'I'), 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
       return view('frontend.pages.customer.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    
        $attributes = $request->all();
        $rules = [
            'name' => 'required',
            'phone' => 'required|numeric|unique:customers,phone',
            'email' => 'nullable|email',
            'address' => 'required|string',
        ];
        $validation = Validator::make($attributes, $rules);
        if ($validation->fails()) {
            return redirect()->back()->with(['error' => getNotify(4), 'error_code' => 'edit'])->withErrors($validation)->withInput();
        }

        $customer = new Customer;
        $customer->name = $request->name;
        $customer->phone = $request->phone;
        $customer->email = $request->email;
        $customer->address = $request->address;
        $customer->status = '1';
        $customer->save();
    
        return redirect()->route('customers.index')->with(['success' => getNotify(1)]);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $customer = Customer::findOrFail($id);
        $sales = \App\Models\Sale::where('customer_id', $id)->latest()->get();
        return view('frontend.pages.customer.show', compact('customer', 'sales'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $customer = Customer::findOrFail($id);
        return view('frontend.pages.customer.edit',compact('customer'));
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $attributes = $request->all();
        $rules = [
            'name' => 'required',
            'phone' => 'required|numeric|unique:customers,phone,'. $id,
            'email' => 'nullable|email',
            'address' => 'required|string',
        ];
        $validation = Validator::make($attributes, $rules);
        if ($validation->fails()) {
            return redirect()->back()->with(['error' => getNotify(4), 'error_code' => 'edit'])->withErrors($validation)->withInput();
        }

        $customer = Customer::findOrFail($id);
        $customer->name = $request->name;
        $customer->phone = $request->phone;
        $customer->email = $request->email;
        $customer->address = $request->address;
        $customer->save();
    
        return redirect()->route('customers.index')->with(['success' => getNotify(2)]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
       $customer = Customer::findOrFail($id);
       $customer->delete();
       return redirect()->back()->with(['success' => getNotify(3)]);
    }
}