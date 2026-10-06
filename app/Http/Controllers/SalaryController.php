<?php

namespace App\Http\Controllers;

use App\Models\Salary;
use App\Models\Employee;
use App\Models\TaDa;
use App\Models\ExpenseCategory;
use App\Models\DailyExpense;
use Illuminate\Http\Request;

class SalaryController extends Controller
{
    public function index()
    {
        $salaries = Salary::with('employee')->latest()->get();
        return view('frontend.pages.salary.index', compact('salaries'));
    }

    public function create()
    {
        $employees = Employee::orderBy('name')->get();
        $currentMonth = date('Y-m');
        $taDaData = [];
        return view('frontend.pages.salary.create', compact('employees', 'currentMonth', 'taDaData'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'month' => 'required|string|max:10',
            'basic_salary' => 'required|numeric|min:0',
            'advance' => 'nullable|numeric|min:0',
            'allowance' => 'nullable|numeric|min:0',
            'deduction' => 'nullable|numeric|min:0',
            'payment_status' => 'required|in:paid,unpaid',
            'payment_date' => 'nullable|date',
            'note' => 'nullable|string|max:500',
        ]);

        $basic = (float) $validated['basic_salary'];
        $allowance = (float) ($validated['allowance'] ?? 0);
        $deduction = (float) ($validated['deduction'] ?? 0);
        $advance = (float) ($validated['advance'] ?? 0);

        $validated['net_salary'] = max(0, $basic + $allowance - $deduction - $advance);

        Salary::create($validated);

        return redirect()->route('salary.index')->with('success', 'Salary record created successfully.');
    }

    public function edit($id)
    {
        $salary = Salary::findOrFail($id);
        $employees = Employee::orderBy('name')->get();
        return view('frontend.pages.salary.edit', compact('salary','employees'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'month' => 'required|string|max:10',
            'basic_salary' => 'required|numeric|min:0',
            'advance' => 'nullable|numeric|min:0',
            'allowance' => 'nullable|numeric|min:0',
            'deduction' => 'nullable|numeric|min:0',
            'payment_status' => 'required|in:paid,unpaid',
            'payment_date' => 'nullable|date',
            'note' => 'nullable|string|max:500',
        ]);

        $salary = Salary::findOrFail($id);

        $basic = (float) $validated['basic_salary'];
        $allowance = (float) ($validated['allowance'] ?? 0);
        $deduction = (float) ($validated['deduction'] ?? 0);
        $advance = (float) ($validated['advance'] ?? 0);

        $validated['net_salary'] = max(0, $basic + $allowance - $deduction - $advance);

        $salary->update($validated);

        return redirect()->route('salary.index')->with('success', 'Salary record updated successfully.');
    }

    public function destroy($id)
    {
        Salary::findOrFail($id)->delete();
        return redirect()->route('salary.index')->with('success', 'Salary record deleted successfully.');
    }

public function getTaDaDataAjax(Request $request)
{
    $employeeId = $request->employee_id;
    $month = $request->month;
    $year = substr($month, 0, 4);
    $monthNum = substr($month, 5, 2);
    
    $taDaRecords = TaDa::where('employee_id', $employeeId)
                      ->whereYear('date', $year)
                      ->whereMonth('date', $monthNum)
                      ->get();

    $totalAdvance = 0;
    $totalClaim = 0;

    foreach ($taDaRecords as $record) {
        if ($record->payment_type === 'Advance') {
            $totalAdvance += $record->remaining_amount;
        } elseif ($record->payment_type === 'Claim') {
            $totalClaim += $record->amount;
        }
    }

    return response()->json([
        'total_advance' => $totalAdvance,
        'total_claim' => $totalClaim,
        'records_count' => $taDaRecords->count()
    ]);
}
// public function getAdvanceSumByMonth($id, Request $request)
// {
//     // Find the "Advance Salary" category dynamically
//     $advanceCategory = ExpenseCategory::where('name', 'Advance Salary')->first();
    
//     if (!$advanceCategory) {
//         return response()->json(['sum' => 0]);
//     }

//     $query = DailyExpense::where('employee_id', $id)
//                          ->where('expense_category_id', $advanceCategory->id);
    
//     // Filter by month if provided
//     if ($request->has('month') && $request->month) {
//         $year = substr($request->month, 0, 4);
//         $monthNum = substr($request->month, 5, 2);
//         $query->whereYear('date', $year)
//               ->whereMonth('date', $monthNum);
//     }
    
//     $advance = $query->sum('amount');
    
//     return response()->json(['sum' => $advance]);
// }
}