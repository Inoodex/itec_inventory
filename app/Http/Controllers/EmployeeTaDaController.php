<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaDa;
use Illuminate\Support\Facades\Auth;

class EmployeeTaDaController extends Controller
{
    public function index()
    {
        $employee = auth()->user()->employee;
        if (!$employee) {
            return redirect()->route('index')->with('error', 'No employee profile linked with your account.');
        }

        $tadas = TaDa::where('employee_id', $employee->id)->latest()->get();

        return view('frontend.pages.employees.ta_da.index', compact('tadas'));
    }

    public function create()
    {
        $employee = auth()->user()->employee;
        if (!$employee) {
            return redirect()->route('index')->with('error', 'No employee profile linked with your account.');
        }

        return view('frontend.pages.employees.ta_da.create');
    }

    public function store(Request $request)
    {
        $employee = auth()->user()->employee;
        if (!$employee) {
            return redirect()->route('index')->with('error', 'No employee profile linked with your account.');
        }

        $validated = $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:TA,DA',
            'payment_type' => 'required|in:Advance,Claim',
            'purpose' => 'nullable|string|max:500',
        ]);

        TaDa::create([
            'user_id' => Auth::id(),
            'employee_id' => $employee->id,
            'date' => $validated['date'],
            'amount' => $validated['amount'],
            'used_amount' => 0,
            'remaining_amount' => $validated['amount'],
            'type' => $validated['type'],
            'payment_type' => $validated['payment_type'],
            'purpose' => $validated['purpose'] ?? null,
        ]);

        return redirect()->route('employee.tada.index')->with('success', 'TA/DA request submitted.');
    }

    public function edit($id)
    {
        $employee = auth()->user()->employee;
        if (!$employee) {
            return redirect()->route('index')->with('error', 'No employee profile linked with your account.');
        }

        $tadas = TaDa::where('id', $id)
            ->where('employee_id', $employee->id)
            ->firstOrFail();

        return view('frontend.pages.employees.ta_da.edit', compact('tadas'));
    }

    public function update(Request $request, $id)
    {
        $employee = auth()->user()->employee;
        if (!$employee) {
            return redirect()->route('index')->with('error', 'No employee profile linked with your account.');
        }

        $tadas = TaDa::where('id', $id)
            ->where('employee_id', $employee->id)
            ->firstOrFail();

        $validated = $request->validate([
            'used_amount' => 'required|numeric|min:0|max:' . $tadas->amount,
        ]);

        $usedAmount = (float) $validated['used_amount'];
        $tadas->used_amount = $usedAmount;
        $tadas->remaining_amount = max(0, (float) $tadas->amount - $usedAmount);
        $tadas->save();

        return redirect()->route('employee.tada.index')->with('success', 'Amount submitted successfully.');
    }
}