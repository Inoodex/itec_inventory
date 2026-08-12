<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectCost;
use App\Models\CostCategory;
use Illuminate\Http\Request;

class ProjectCostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projectCosts = ProjectCost::with(['project', 'category'])
            ->latest()
            ->paginate(10);
            
        return view('frontend.pages.project-costs.index', compact('projectCosts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projects = Project::where('status', '!=', 'completed')->get();
        $categories = CostCategory::where('is_active', true)->get();
        
        return view('frontend.pages.project-costs.create', compact('projects', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'cost_category_id' => 'required|exists:cost_categories,id',
            'amount' => 'required|numeric|min:0',
            'cost_date' => 'required|date',
            'description' => 'nullable|string'
        ]);

        ProjectCost::create($request->all());

        return redirect()->route('project-costs.index')
            ->with('success', 'Project cost created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $projectCost = ProjectCost::with(['project', 'category'])->findOrFail($id);
        return view('frontend.pages.project-costs.show', compact('projectCost'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $projectCost = ProjectCost::findOrFail($id);
        $projects = Project::where('status', '!=', 'completed')->get();
        $categories = CostCategory::where('is_active', true)->get();
        
        return view('frontend.pages.project-costs.edit', compact('projectCost', 'projects', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $projectCost = ProjectCost::findOrFail($id);

        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'cost_category_id' => 'required|exists:cost_categories,id',
            'amount' => 'required|numeric|min:0',
            'cost_date' => 'required|date',
            'description' => 'nullable|string'
        ]);

        $projectCost->update($request->all());

        return redirect()->route('project-costs.index')
            ->with('success', 'Project cost updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $projectCost = ProjectCost::findOrFail($id);
        $projectCost->delete();

        return redirect()->route('project-costs.index')
            ->with('success', 'Project cost deleted successfully!');
    }
}