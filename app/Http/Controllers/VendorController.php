<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Service, User, Vendor, Purchase};
use App\Http\Controllers\Controller;
use Validator;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $vendors = Vendor::latest()->get();
        $customers = $vendors; // Alias for compatibility
        return view('frontend.pages.vendor.index', compact('vendors', 'customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
       return view('frontend.pages.vendor.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $attributes = $request->all();
        $rules = [
            'name' => 'required',
            'phone' => 'required|numeric|unique:vendors,phone',
            'email' => 'nullable|email',
            'address' => 'required|string',
        ];
        $validation = Validator::make($attributes, $rules);
        if ($validation->fails()) {
            return redirect()->back()->with(['error' => getNotify(4), 'error_code' => 'edit'])->withErrors($validation)->withInput();
        }

        $vendor = new Vendor;
        $vendor->name = $request->name;
        $vendor->phone = $request->phone;
        $vendor->email = $request->email;
        $vendor->address = $request->address;
        $vendor->status = '1';
        $vendor->save();

        return redirect()->route('vendors.index')->with('success', 'Vendor added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $vendor = Vendor::findOrFail($id);
        $customer = $vendor; // Alias for compatibility
        $purchases = Purchase::where('vendor_id', $id)->latest()->get();
        return view('frontend.pages.vendor.show', compact('vendor', 'customer', 'purchases'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $vendor = Vendor::findOrFail($id);
        $customer = $vendor; // Alias
        return view('frontend.pages.vendor.edit', compact('vendor', 'customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $attributes = $request->all();
        $rules = [
            'name' => 'required',
            'phone' => 'required|numeric|unique:vendors,phone,'. $id,
            'email' => 'nullable|email',
            'address' => 'required|string',
        ];
        $validation = Validator::make($attributes, $rules);
        if ($validation->fails()) {
            return redirect()->back()->with(['error' => getNotify(4), 'error_code' => 'edit'])->withErrors($validation)->withInput();
        }

        $vendor = Vendor::findOrFail($id);
        $vendor->name = $request->name;
        $vendor->phone = $request->phone;
        $vendor->email = $request->email;
        $vendor->address = $request->address;
        $vendor->save();

        return redirect()->route('vendors.index')->with('success', 'Vendor data updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
       $vendor = Vendor::findOrFail($id);
       $vendor->delete();
       return redirect()->back()->with(['success' => getNotify(3)]);
    }

    public function downloadPdf()
    {
        $vendors = Vendor::all();
        $html = view('pdf.vendors', compact('vendors'))->render();
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'Helvetica',
        ]);
        $mpdf->WriteHTML($html);
        return response($mpdf->Output('vendor-list.pdf', 'I'), 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}