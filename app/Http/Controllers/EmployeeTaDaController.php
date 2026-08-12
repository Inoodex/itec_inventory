<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaDa;
use Illuminate\Support\Facades\Auth;

class EmployeeTaDaController extends Controller
{
    // public function index()
    // {
    //     $tadas = TaDa::where('user_id', Auth::id())->latest()->get();

    //     return view('frontend.pages.employees.ta_da.index', compact('tadas'));
    // }

    public function index()
{
    $employee = auth()->user()->employee;
    $tadas = TaDa::where('employee_id', $employee->id)->get();

    return view('frontend.pages.employees.ta_da.index', compact('tadas'));
}

public function edit($id)
{
    $tadas = TaDa::where('id', $id)
              ->where('employee_id', auth()->user()->employee->id)
              ->firstOrFail();

    return view('frontend.pages.employees.ta_da.edit', compact('tadas'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'used_amount' => 'required|numeric|min:0|max:' . TaDa::findOrFail($id)->amount,
    ]);

    $tadas = TaDa::findOrFail($id);
    $tadas->used_amount = $request->used_amount;
    $tadas->remaining_amount = $tadas->amount - $tadas->used_amount;
    $tadas->save();

    return redirect()->route('employee.tada.index')->with('success', 'Amount submitted successfully.');
}


    public function create()
    {
        
        return view('frontend.pages.employees.ta_da.create');
    }

 public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'type' => 'required|in:TA,DA',
            'payment_type' => 'required|in:Advance,Claim',
            'purpose' => 'nullable|string',
        ]);

        TaDa::create([
            'user_id' => Auth::id(),
            'employee_id' => Auth::user()->employee->id,            
            'date' => $request->date,
            'amount' => $request->amount,
            'type' => $request->type,
            'payment_type' => $request->payment_type,
            'purpose' => $request->purpose,
        ]);

        return redirect()->route('employee.tada.index')->with('success', 'TA/DA request submitted.');
    }

// public function update(Request $request, $id)
// {
//     $tada = TaDa::findOrFail($id);

//     $request->validate([
//         'actual_amount' => 'required|numeric|min:0',
//     ]);

//     $difference = $tada->approved_amount - $request->actual_amount;

//     $tada->update([
//         'actual_amount' => $request->actual_amount,
//         'difference' => $difference,
//         'status' => 'submitted',
//     ]);
//     return redirect()->route('employee.tada.index')->with('success', 'TA/DA submitted successfully!');
// }

}