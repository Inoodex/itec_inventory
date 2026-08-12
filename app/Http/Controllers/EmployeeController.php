<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\DailyExpense;
use App\Models\ExpenseCategory;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
  public function index(Request $request)
{
    $query = Employee::query();

    if ($request->search) {
        $query->where(function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%')
              ->orWhere('phone', 'like', '%' . $request->search . '%')
              ->orWhere('employee_id', 'like', '%' . $request->search . '%');
        });
    }

    if ($request->status && $request->status != 'all') {
        $query->where('status', $request->status);
    }

    $employees = $query->latest()->get();

    return view('frontend.pages.employees.index', compact('employees'));
}


    public function create()
    {
        return view('frontend.pages.employees.create');
    }

public function show($id)
{
    $employee = Employee::findOrFail($id);
    return view('frontend.pages.employees.show', compact('employee'));
}

    public function store(StoreEmployeeRequest $request)
    {
        $request->validate([
            'employee_id' => 'required|unique:employees',
            'name' => 'required',
        ]);

        $employee = new Employee($request->except('image'));

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/employees/'), $filename);
            $employee->image = $filename;
        }

        $employee->save();

        return redirect()->route('employees.index')->with('success', 'Employee Added Successfully');
    }

    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        return view('frontend.pages.employees.edit', compact('employee'));
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $request->validate([
            'employee_id' => 'required|unique:employees,employee_id,'.$employee->id,
            'name' => 'required',
        ]);

        $employee->fill($request->except('image'));

        if ($request->hasFile('image')) {
            if ($employee->image && file_exists(public_path('uploads/employees/'.$employee->image))) {
                unlink(public_path('uploads/employees/'.$employee->image));
            }

            $file = $request->file('image');
            $filename = time().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/employees/'), $filename);
            $employee->image = $filename;
        }

        $employee->save();

        return redirect()->route('employees.index')->with('success', 'Employee Updated Successfully');
    }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);

        if ($employee->image && file_exists(public_path('uploads/employees/'.$employee->image))) {
            unlink(public_path('uploads/employees/'.$employee->image));
        }

        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Employee Deleted Successfully');
    }

    public function getAdvanceSumByMonth($id, Request $request)
{
    // Find the "Advance Salary" category dynamically
    $advanceCategory = ExpenseCategory::where('name', 'Advance Salary')->first();
    
    if (!$advanceCategory) {
        return response()->json(['sum' => 0]);
    }

    $query = DailyExpense::where('employee_id', $id)
                         ->where('expense_category_id', $advanceCategory->id);
    
    // Filter by month if provided
    if ($request->has('month') && $request->month) {
        $year = substr($request->month, 0, 4);
        $monthNum = substr($request->month, 5, 2);
        $query->whereYear('date', $year)
              ->whereMonth('date', $monthNum);
    }
    
    $advance = $query->sum('amount');
    
    return response()->json(['sum' => $advance]);
}
}