<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Client::query();

        // Filter by search term
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $clients = $query->with('projects')->latest()->paginate(10)->withQueryString();
        return view('frontend.pages.clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    // public function create()
    // {
    //     return view('frontend.pages.clients.create');
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:clients,email',
            'address' => 'required|string|max:500',
        ]);

        try {
            Client::create($request->all());
            return redirect()->route('clients.index')->with('success', 'Client created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create client: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $client = Client::with(['projects'])->findOrFail($id);
        return view('frontend.pages.clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $client = Client::findOrFail($id);
        return view('frontend.pages.clients.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:clients,email,' . $id,
            'address' => 'required|string|max:500',
        ]);

        try {
            $client->update($request->all());
            return redirect()->route('clients.show', $client->id)->with('success', 'Client updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update client: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $client = Client::findOrFail($id);

        // Check if client has projects
        if ($client->projects()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete client with associated projects.');
        }

        try {
            $client->delete();
            return redirect()->route('clients.index')->with('success', 'Client deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete client: ' . $e->getMessage());
        }
    }
}