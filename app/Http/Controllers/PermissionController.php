<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permissions = Permission::get();
        return view('frontend.pages.permission.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('frontend.pages.permission.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string',
        ];

        $validatedData = $request->validate($rules);

        $user = new Permission();
        $user->name = $validatedData['name'];
        $user->guard_name  = 'web';
        $user->save();

        session()->flash('sweet_alert', [
            'type' => 'success',
            'title' => 'Success!',
            'text' => 'User added success',
        ]);
        // Redirect or return a response as needed
        return redirect()->route('permission.index')->with('success', 'Permission created successfully');
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
    {   $permission = Permission::where('id',$id)->first();
        return view('frontend.pages.permission.edit',compact('permission'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $rules = [
            'name' => 'required|string',
        ];

        $validatedData = $request->validate($rules);

        $user = Permission::findOrFail($id);
        $user->name = $validatedData['name'];
        $user->save();

        session()->flash('sweet_alert', [
            'type' => 'success',
            'title' => 'Success!',
            'text' => 'User update success',
        ]);
        // Redirect or return a response as needed
        return redirect()->route('permission.index')->with('success', 'Permission update successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Permission::findOrFail($id);
        $category->delete();
        session()->flash('sweet_alert', [
            'type' => 'success',
            'title' => 'Success!',
            'text' => 'Permission Delete success',
        ]);
        return redirect()->route('permission.index');
    }
}