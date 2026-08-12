<?php

namespace App\Http\Controllers;

use App\Models\CompanyDetail;
use Illuminate\Http\Request;

class CompanyDetailController extends Controller
{
    public function index()
    {
        $companies = CompanyDetail::latest()->get();
        return view('frontend.pages.company-details.index', compact('companies'));
    }

    public function create()
    {
        return redirect()->route('company-details.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'signatory_name' => 'required|string|max:255',
            'signatory_designation' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'is_default' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
            'signature_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'seal_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
        ]);

        if ($request->hasFile('signature_image')) {
            $imageName = time() . '_sig_' . uniqid() . '.' . $request->file('signature_image')->getClientOriginalExtension();
            $request->file('signature_image')->move(public_path('uploads/signatures'), $imageName);
            $data['signature_image'] = 'uploads/signatures/' . $imageName;
        }

        if ($request->hasFile('seal_image')) {
            $sealName = time() . '_seal_' . uniqid() . '.' . $request->file('seal_image')->getClientOriginalExtension();
            $request->file('seal_image')->move(public_path('uploads/seals'), $sealName);
            $data['seal_image'] = 'uploads/seals/' . $sealName;
        }

        // If setting as default, remove default from others
        if (!empty($data['is_default'])) {
            CompanyDetail::where('is_default', true)->update(['is_default' => false]);
        }

        CompanyDetail::create($data);

        return redirect()->route('company-details.index')
            ->with('success', 'Company details created successfully.');
    }

    public function edit(CompanyDetail $companyDetail)
    {
        return redirect()->route('company-details.index');
    }

    public function update(Request $request, CompanyDetail $companyDetail)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'signatory_name' => 'required|string|max:255',
            'signatory_designation' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'is_default' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
            'signature_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'seal_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
        ]);

        if ($request->hasFile('signature_image')) {
            if ($companyDetail->signature_image && file_exists(public_path($companyDetail->signature_image))) {
                @unlink(public_path($companyDetail->signature_image));
            }
            $imageName = time() . '_sig_' . uniqid() . '.' . $request->file('signature_image')->getClientOriginalExtension();
            $request->file('signature_image')->move(public_path('uploads/signatures'), $imageName);
            $data['signature_image'] = 'uploads/signatures/' . $imageName;
        }

        if ($request->hasFile('seal_image')) {
            if ($companyDetail->seal_image && file_exists(public_path($companyDetail->seal_image))) {
                @unlink(public_path($companyDetail->seal_image));
            }
            $sealName = time() . '_seal_' . uniqid() . '.' . $request->file('seal_image')->getClientOriginalExtension();
            $request->file('seal_image')->move(public_path('uploads/seals'), $sealName);
            $data['seal_image'] = 'uploads/seals/' . $sealName;
        }

        // If setting as default, remove default from others
        if (!empty($data['is_default'])) {
            CompanyDetail::where('is_default', true)->where('id', '!=', $companyDetail->id)->update(['is_default' => false]);
        }

        $companyDetail->update($data);

        return redirect()->route('company-details.index')
            ->with('success', 'Company details updated successfully.');
    }

    public function destroy(CompanyDetail $companyDetail)
    {
        // Check if this company is used in any bills
        if ($companyDetail->bills()->exists()) {
            return redirect()->route('company-details.index')
                ->with('error', 'Cannot delete company details that are used in bills.');
        }

        // If deleting default, set another as default
        if ($companyDetail->is_default) {
            $newDefault = CompanyDetail::where('id', '!=', $companyDetail->id)->first();
            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
            }
        }

        $companyDetail->delete();

        return redirect()->route('company-details.index')
            ->with('success', 'Company details deleted successfully.');
    }

    public function setDefault(CompanyDetail $companyDetail)
    {
        CompanyDetail::where('is_default', true)->update(['is_default' => false]);
        $companyDetail->update(['is_default' => true]);

        return redirect()->route('company-details.index')
            ->with('success', 'Default company details updated successfully.');
    }
}