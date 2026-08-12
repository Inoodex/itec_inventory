<?php

namespace App\Http\Controllers;

use \DB;
use App\Models\Client;
use App\Models\Product;
use App\Models\Project;
use App\Models\Payment;
use App\Models\ProjectItem;
use App\Http\Requests\StoreProjectRequest;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    // public function index()
    // {
    //     $projects = Project::latest()->get();
    //     return view('frontend.pages.projects.index', compact('projects'));
    // }

    public function index()
{
    $projects = Project::with('client')->latest()->get();
    return view('frontend.pages.projects.index', compact('projects'));
}

public function create()
{
    $existingClients = Client::select('id', 'name', 'phone', 'email', 'address')->get();
    
    $products = Product::with(['inventory', 'latestPurchase'])->get();
    
    return view('frontend.pages.projects.create', compact('existingClients', 'products'));
}

public function store(StoreProjectRequest $request)
{
    // Validate the request
    $validationRules = [
        'project_name' => 'required|string|max:255',
        'client_type' => 'required|in:new,existing',
        'budget' => 'nullable|numeric|min:0',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'status' => 'required|in:pending,in_progress,completed,cancelled',
        'description' => 'nullable|string',
    ];

    // Conditional validation based on client type
    if ($request->client_type === 'new') {
        $validationRules['client_name'] = 'required|string|max:255';
        $validationRules['client_phone'] = 'required|string|max:20';
        $validationRules['client_email'] = 'required|email|max:255';
        $validationRules['client_address'] = 'required|string|max:500';
    } else {
        $validationRules['existing_client_id'] = 'required|exists:clients,id';
    }

    $request->validate($validationRules);

    DB::beginTransaction();

    try {
        $clientId = null;

        // Handle client data based on type
        if ($request->client_type === 'existing') {
            // Use existing client
            $clientId = $request->existing_client_id;
            $client = Client::findOrFail($clientId);
            $clientData = [
                'client_id' => $clientId,
                'client_name' => $client->name,
                'client_phone' => $client->phone,
                'client_email' => $client->email,
                'client_address' => $client->address,
            ];
        } else {
            // Create new client profile
            $client = Client::create([
                'name' => $request->client_name,
                'phone' => $request->client_phone,
                'email' => $request->client_email,
                'address' => $request->client_address,
                'company_name' => $request->client_name, // Use client name as company name if not provided
                'is_active' => true,
            ]);

            $clientId = $client->id;
            
            $clientData = [
                'client_id' => $clientId,
                'client_name' => $client->name,
                'client_phone' => $client->phone,
                'client_email' => $client->email,
                'client_address' => $client->address,
            ];
        }

        // Create the project
        $project = Project::create(array_merge([
            'project_name' => $request->project_name,
            'client_id' => $clientId, // This ensures client_id is never null
            'budget' => $request->budget ?? 0,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
            'description' => $request->description,
            'sub_total' => 0,
            'discount' => 0,
            'grand_total' => 0,
            'advanced_payment' => 0,
            'due_payment' => 0,
        ], $clientData));

        DB::commit();

        return redirect()->route('projects.show', $project->id)
            ->with('success', 'Project created successfully! ' . 
                   ($request->client_type === 'new' ? 'New client profile was also created.' : ''));

    } catch (\Exception $e) {
        DB::rollBack();
        
        return redirect()->back()
            ->with('error', 'Failed to create project: ' . $e->getMessage())
            ->withInput();
    }
}

public function edit($id)
{
    $project = Project::with(['client', 'items.product'])->findOrFail($id);
    $existingClients = Client::all();
    $products = Product::with(['inventory', 'latestPurchase'])->get();
    
    return view('frontend.pages.projects.edit', compact('project', 'existingClients', 'products'));
}


public function show($id)
{
    $project = Project::with(['items.product', 'costs'])->findOrFail($id);
    
    // Calculate sub total from project items
    $subTotal = $project->items->sum('total_price');
    
    // Calculate total costs from project_costs table
    $totalCosts = $project->costs->sum('amount');
    
    // Calculate grand total (sub total + total costs)
    $grandTotal = $subTotal + $totalCosts;
    
    return view('frontend.pages.projects.show', compact('project', 'subTotal', 'totalCosts', 'grandTotal'));
}

public function update(Request $request, $id)
{
    // Validate the request
    $validationRules = [
        'project_name' => 'required|string|max:255',
        'client_type' => 'required|in:new,existing',
        'budget' => 'nullable|numeric|min:0',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'status' => 'required|in:pending,in_progress,completed,cancelled',
        'description' => 'nullable|string',
    ];

    // Conditional validation based on client type
    if ($request->client_type === 'new') {
        $validationRules['client_name'] = 'required|string|max:255';
        $validationRules['client_phone'] = 'required|string|max:20';
        $validationRules['client_email'] = 'required|email|max:255';
        $validationRules['client_address'] = 'required|string|max:500';
    } else {
        $validationRules['existing_client_id'] = 'required|exists:clients,id';
    }

    $request->validate($validationRules);

    DB::beginTransaction();

    try {
        $project = Project::findOrFail($id);
        $clientId = $project->client_id;

        // Handle client data based on type
        if ($request->client_type === 'existing') {
            // Use existing client
            $clientId = $request->existing_client_id;
            $client = Client::findOrFail($clientId);
            $clientData = [
                'client_id' => $clientId,
                'client_name' => $client->name,
                'client_phone' => $client->phone,
                'client_email' => $client->email,
                'client_address' => $client->address,
            ];
        } else {
            // Check if we need to create a new client or update existing
            if ($project->client_id) {
                // Update existing client
                $client = Client::find($project->client_id);
                if ($client) {
                    $client->update([
                        'name' => $request->client_name,
                        'phone' => $request->client_phone,
                        'email' => $request->client_email,
                        'address' => $request->client_address,
                    ]);
                } else {
                    // Create new client if old one doesn't exist
                    $client = Client::create([
                        'name' => $request->client_name,
                        'phone' => $request->client_phone,
                        'email' => $request->client_email,
                        'address' => $request->client_address,
                        'company_name' => $request->client_name,
                        'is_active' => true,
                    ]);
                    $clientId = $client->id;
                }
            } else {
                // Create new client
                $client = Client::create([
                    'name' => $request->client_name,
                    'phone' => $request->client_phone,
                    'email' => $request->client_email,
                    'address' => $request->client_address,
                    'company_name' => $request->client_name,
                    'is_active' => true,
                ]);
                $clientId = $client->id;
            }
            
            $clientData = [
                'client_id' => $clientId,
                'client_name' => $client->name,
                'client_phone' => $client->phone,
                'client_email' => $client->email,
                'client_address' => $client->address,
            ];
        }

        // Update the project
        $project->update(array_merge([
            'project_name' => $request->project_name,
            'client_id' => $clientId, // Ensure client_id is never null
            'budget' => $request->budget ?? 0,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
            'description' => $request->description,
        ], $clientData));

        DB::commit();

        return redirect()->route('projects.show', $project->id)
            ->with('success', 'Project updated successfully!');

    } catch (\Exception $e) {
        DB::rollBack();
        
        return redirect()->back()
            ->with('error', 'Failed to update project: ' . $e->getMessage())
            ->withInput();
    }
}


    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }

    public function generateBill($id)
{
    $project = Project::with([
        'items.product', 
        'costs.costCategory',
        'client' // if you have client relationship
    ])->findOrFail($id);

    // Calculate totals
    $itemsSubTotal = $project->items->sum(function($item) {
        return $item->unit_price * $item->quantity;
    });
    
    $totalCosts = $project->costs->sum('amount');
    $grandTotal = $itemsSubTotal + $totalCosts;

    return view('frontend.pages.projects.bills.create', compact('project', 'itemsSubTotal', 'totalCosts', 'grandTotal'));
}

public function downloadBill($id)
{
    $project = Project::with([
        'items.product', 
        'costs.costCategory',
        'client'
    ])->findOrFail($id);

    $itemsSubTotal = $project->items->sum(function($item) {
        return $item->unit_price * $item->quantity;
    });
    
    $totalCosts = $project->costs->sum('amount');
    $grandTotal = $itemsSubTotal + $totalCosts;

    return view('bills.pdf', compact('project', 'itemsSubTotal', 'totalCosts', 'grandTotal'));
}

public function payments($projectId)
{
    $project = Project::with('client')->findOrFail($projectId);

    // Fetch payments linked to this project
    $payments = Payment::where('payment_for', 3)
                       ->where('project_id', $projectId)
                       ->get();

    return view('frontend.pages.projects.payments', compact('project', 'payments'));
}

public function processPayment(Request $request)
{
    $request->validate([
        'project_id' => 'required|exists:projects,id',
        'payment_amount' => 'required|numeric|min:0.01',
        'payment_method' => 'required|string',
    ]);

    $project = Project::findOrFail($request->project_id);

    // Create payment
    Payment::create([
        'payment_for' => 3, // project payment
        'project_id' => $project->id,
        'amount' => $request->payment_amount,
        'payment_method' => $request->payment_method,
        'status' => 'paid',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Update project payments
    $project->advanced_payment += $request->payment_amount;
    $project->due_payment = max($project->budget - $project->advanced_payment, 0);
    $project->save();

    return redirect()->back()->with('success', 'Payment processed successfully.');
}


}