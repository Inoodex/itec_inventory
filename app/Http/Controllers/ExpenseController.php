<?php

namespace App\Http\Controllers;

use Input;
use Validator;
use App\Models\Sale;
use App\Models\User;
use App\Models\Payment;
use App\Models\Product;
use Twilio\Rest\Client;
use App\Models\Employee;
use App\Models\DailyExpense;
use App\Models\Salary;
use Illuminate\Http\Request;
use App\Mail\CreateSalesMail;
use Illuminate\Support\Facades\Mail;
use App\Models\ExpenseCategory;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
   

    public function index(Request $request)
    {
        // Start the query on daily_expenses, joining to categories only:
        $query = DailyExpense::leftJoin('expense_categories', 'expense_categories.id', '=', 'daily_expenses.expense_category_id');

        // Date filtering
        $defaultFilter = true;
        if ($request->from && $request->to) {
            $from = date('Y-m-d 00:00:00', strtotime($request->from));
            $to   = date('Y-m-d 23:59:59', strtotime($request->to));
            $query->whereBetween('daily_expenses.created_at', [$from, $to]);
            $defaultFilter = false;
        }

        // Spend method filter
        if ($request->spend_method) {
            $query->where('daily_expenses.spend_method', $request->spend_method);
            $defaultFilter = false;
        }

        // Expense category filter
        if ($request->expense_category_id) {
            $query->where('daily_expenses.expense_category_id', $request->expense_category_id);
            $defaultFilter = false;
        }

        // Remarks search
        if ($request->key) {
            $query->where('daily_expenses.remarks', 'like', '%' . $request->key . '%');
            $defaultFilter = false;
        }

        // Default to current month
        if ($defaultFilter) {
            $startOfMonth = now()->startOfMonth()->startOfDay();
            $endOfMonth   = now()->endOfMonth()->endOfDay();
            $query->whereBetween('daily_expenses.created_at', [$startOfMonth, $endOfMonth]);
        }

        // Select what we need
        $dailyExpense = $query
            ->with('employee')
            ->select(
                'daily_expenses.*',
                'expense_categories.name as category_name'
            )
            ->orderBy('daily_expenses.id', 'desc')
            ->get();

        // Pull only the active categories for the filter dropdown
        $categories = ExpenseCategory::where('status', 1)->orderBy('name')->get();

        // PDF export shortcut
        if ($request->search_for === 'pdf') {
            $html = view('pdf.daily_expense', compact('dailyExpense', 'request', 'categories'))->render();
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'default_font' => 'Helvetica',
            ]);
            $mpdf->WriteHTML($html);
            return response($mpdf->Output('daily_expense.pdf', 'I'), 200, [
                'Content-Type' => 'application/pdf',
            ]);
        }

        // Render index view
        return view('frontend.pages.expense.index', compact('dailyExpense','request','categories'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
            $users = User::leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->select('users.*', 'roles.name as roleName')
                ->orderBy('users.id', 'desc')
                ->get();

            $employees = Employee::where('status', 'active')->get();

            $categories = ExpenseCategory::all();

            return view('frontend.pages.expense.create', compact('users', 'categories', 'employees'));
    }


    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     $attributes = $request->all();

    //     $rules = [
    //         'date' => 'required|date',
    //         'expense_category_id' => 'required|exists:expense_categories,id',
    //         'amount' => 'required|numeric|min:0.01',
    //         'spend_method' => 'required|in:cash,card,bank_transfer',
    //         'remarks' => 'nullable|string|max:1000',
    //     ];

    //     $validation = Validator::make($attributes, $rules);

    //     if ($validation->fails()) {
    //         return redirect()->back()
    //             ->with(['error' => getNotify(4)])
    //             ->withErrors($validation)
    //             ->withInput();
    //     }

    //     $expense = new DailyExpense();
    //     $expense->date = $request->date;
    //     $expense->expense_category_id = $request->expense_category_id;
    //     $expense->amount = $request->amount;
    //     $expense->spend_method = $request->spend_method;
    //     $expense->remarks = $request->remarks;
    //     $expense->save();

    //     // return redirect()->back()->with(['success' => getNotify(1)]);
    //     return redirect()->route('dailyExpenses.index')->with('success', 'Created successfully.');

    // }

  public function store(Request $request)
{
    $request->validate([
        'employee_id' => 'required|exists:employees,id',
        'date' => 'required|date',
        'amount' => 'required|numeric',
        'spend_method' => 'required|in:cash,card,bank_transfer',
        'remarks' => 'nullable|string',
        'expense_category_id' => 'required|exists:expense_categories,id',
    ]);

    DailyExpense::create([
        'user_id' => auth()->id(),
        'employee_id' => $request->employee_id,
        'date' => $request->date,
        'expense_category_id' => $request->expense_category_id,
        'amount' => $request->amount,
        'spend_method' => $request->spend_method,
        'remarks' => $request->remarks,
    ]);

    // return redirect()->back()->with('success', 'Advance salary request submitted.');
    return redirect()->route('dailyExpenses.index')->with('success', 'Created successfully.');
}




    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $expense = DailyExpense::where('id', $id)->first();        
        $users = User::leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select('users.*', 'roles.name as roleName')
            ->orderBy('users.id', 'desc')
            ->get();
        $employees = Employee::where('status', 'active')->get();
        $categories = ExpenseCategory::where('status', 1)->orderBy('name')->get();
        return view('frontend.pages.expense.edit', compact('users', 'expense', 'categories', 'employees'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $attributes = $request->all();
        $rules = [
            'date'                => 'required|date',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount'              => 'required|numeric|min:0.01',
            'spend_method'        => 'required|in:cash,card,bank_transfer',
            'remarks'             => 'nullable|string|max:1000',
        ];

        $validation = Validator::make($attributes, $rules);
        if ($validation->fails()) {
            return redirect()->back()
                ->with(['error' => getNotify(4)])
                ->withErrors($validation)
                ->withInput();
        }

        // Find the existing expense entry
        $expense = DailyExpense::findOrFail($id);

        // Update the expense details
        $expense->date                = $request->date;
        $expense->expense_category_id = $request->expense_category_id;
        $expense->amount              = $request->amount;
        $expense->spend_method        = $request->spend_method;
        $expense->remarks             = $request->remarks;
        $expense->save();

        // return redirect()->route('dailyExpenses.index')
        //     ->with(['success' => getNotify(2)]);
        return redirect()->route('dailyExpenses.index')->with('success', 'Updated successfully.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $expense = DailyExpense::findOrFail($id);
        $expense->delete();
        return redirect()->back()->with(['success' => getNotify(3)]);
    }

    public function getAdvanceSum($employeeId)
{
    $advanceCategory = ExpenseCategory::where('name', 'Advance Salary')->first();

    if (!$advanceCategory) {
        return response()->json(['sum' => 0]);
    }

    $sum = DailyExpense::where('employee_id', $employeeId)
        ->where('expense_category_id', $advanceCategory->id)
        ->sum('amount');

    return response()->json(['sum' => $sum]);
}

}