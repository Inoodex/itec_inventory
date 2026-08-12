<?php

namespace App\Http\Controllers;

use App\Models\TaDa;
use App\Models\Employee;
use Illuminate\Http\Request;

class TaDaController extends Controller
{
    public function index()
    {
        $taDas = TaDa::with('employee')->latest()->get();
        return view('frontend.pages.ta_da.index', compact('taDas'));
    }

public function create()
{
    $employees = Employee::where('status', 'active')->get();
    return view('frontend.pages.ta_da.create', compact('employees'));
}


    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'type' => 'required|in:TA,DA',
            'payment_type' => 'required|in:Advance,Claim',
        ]);

        TaDa::create($request->all());

        return redirect()->route('ta-da.index')->with('success', 'TA/DA record added successfully.');
    }

   public function edit($id)
{
    $tada = TaDa::findOrFail($id);
    $employees = Employee::all();
    return view('frontend.pages.ta_da.edit', compact('tada', 'employees'));
}


    public function update(Request $request, TaDa $taDa)
    {
        $request->validate([
            'employee_id' => 'required|integer',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'type' => 'required|in:TA,DA',
            'payment_type' => 'required|in:Advance,Claim',
        ]);

        $taDa->update($request->all());

        return redirect()->route('ta-da.index')->with('success', 'TA/DA record updated successfully.');
    }

    public function destroy(TaDa $taDa)
    {
        $taDa->delete();
        return redirect()->route('ta-da.index')->with('success', 'TA/DA record deleted successfully.');
    }
}