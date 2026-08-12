<?php

namespace App\Http\Controllers;

use App\Models\CostCategory;
use Illuminate\Http\Request;

class CostCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = CostCategory::latest()->get();
        return view('frontend.pages.cost-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect()->route('cost-categories.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:cost_categories,name',
            'description' => 'nullable|string',
        ]);

        CostCategory::create($request->all());

        return redirect()->route('cost-categories.index')
            ->with('success', 'Cost category created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
   public function edit($id)
    {
        return redirect()->route('cost-categories.index');
    }

    /**
     * Update the specified resource in storage.
     */
     public function update(Request $request, $id)
    {
        $category = CostCategory::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:cost_categories,name,' . $id,
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? 1 : 0
        ]);

        return redirect()->route('cost-categories.index')
            ->with('success', 'Cost category updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $category = CostCategory::findOrFail($id);
        $category->delete();

        return redirect()->route('cost-categories.index')
            ->with('success', 'Cost category deleted successfully!');
    }

    /**
     * Toggle category status
     */
    public function toggleStatus($id)
    {
        $category = CostCategory::findOrFail($id);
        $category->update(['is_active' => !$category->is_active]);

        $status = $category->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Cost category {$status} successfully!");
    }
}