<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $expenseCategories = ExpenseCategory::latest()->get();
        return view('frontend.pages.expense-category.index', compact('expenseCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('frontend.pages.expense-category.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        ExpenseCategory::create([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->route('expense-categories.index')->with('success', 'Expense category created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $expenseCategory = ExpenseCategory::findOrFail($id);
        return view('frontend.pages.expense-category.edit', compact('expenseCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        $expenseCategory = ExpenseCategory::findOrFail($id);
        $expenseCategory->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->route('expense-categories.index')->with('success', 'Expense category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $expenseCategory = ExpenseCategory::findOrFail($id);
        $expenseCategory->delete();

        return redirect()->route('expense-categories.index')->with('success', 'Expense category deleted successfully.');
    }
}
