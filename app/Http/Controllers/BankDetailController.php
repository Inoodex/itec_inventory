<?php

namespace App\Http\Controllers;

use App\Models\BankDetail;
use Illuminate\Http\Request;

class BankDetailController extends Controller
{
    public function index()
    {
        $banks = BankDetail::latest()->get();
        return view('frontend.pages.bank-details.index', compact('banks'));
    }

    public function create()
    {
        return view('frontend.pages.bank-details.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'branch' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'account_type' => 'required|string|max:50',
            'routing_number' => 'nullable|string|max:255',
            'is_default' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
        ]);

        // If setting as default, remove default from others
        if ($request->is_default) {
            BankDetail::where('is_default', true)->update(['is_default' => false]);
        }

        BankDetail::create($request->all());

        return redirect()->route('bank-details.index')
            ->with('success', 'Bank details created successfully.');
    }

    public function edit(BankDetail $bankDetail)
    {
        return view('frontend.pages.bank-details.edit', compact('bankDetail'));
    }

    public function update(Request $request, BankDetail $bankDetail)
    {
        $request->validate([
            'account_name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'branch' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'account_type' => 'required|string|max:50',
            'is_default' => 'sometimes|boolean',
            'routing_number' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);

        // If setting as default, remove default from others
        if ($request->is_default) {
            BankDetail::where('is_default', true)->where('id', '!=', $bankDetail->id)->update(['is_default' => false]);
        }

        $bankDetail->update($request->all());

        return redirect()->route('bank-details.index')
            ->with('success', 'Bank details updated successfully.');
    }

    public function destroy(BankDetail $bankDetail)
    {
        // Check if this bank is used in any bills
        if ($bankDetail->bills()->exists()) {
            return redirect()->route('bank-details.index')
                ->with('error', 'Cannot delete bank details that are used in bills.');
        }

        // If deleting default, set another as default
        if ($bankDetail->is_default) {
            $newDefault = BankDetail::where('id', '!=', $bankDetail->id)->first();
            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
            }
        }

        $bankDetail->delete();

        return redirect()->route('bank-details.index')
            ->with('success', 'Bank details deleted successfully.');
    }

    public function setDefault(BankDetail $bankDetail)
    {
        BankDetail::where('is_default', true)->update(['is_default' => false]);
        $bankDetail->update(['is_default' => true]);

        return redirect()->route('bank-details.index')
            ->with('success', 'Default bank details updated successfully.');
    }
}