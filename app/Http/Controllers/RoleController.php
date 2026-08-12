<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = Role::get();
        return view('frontend.pages.role.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::get();
        return view('frontend.pages.role.create',compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // return $request;
        $rules = [
            'name' => 'required|string',
        ];

        $validatedData = $request->validate($rules);

        $user = new Role();
        $user->name = $validatedData['name'];
        $user->save();
        if ($request->permissions) {
            foreach ($request->permissions as $permission) {
                // return $permission;
                DB::insert('INSERT INTO role_has_permissions (role_id, permission_id) VALUES (?, ?)', [$user->id, $permission]);
            }
        }    
        session()->flash('sweet_alert', [
            'type' => 'success',
            'title' => 'Success!',
            'text' => 'User added success',
        ]);
        // Redirect or return a response as needed
        return redirect()->route('role.index')->with('success', 'Role created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
       
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $role = Role::find($id);
        $permissions = Permission::get();
        $roleHasPermissions = DB::table('role_has_permissions')
        ->where('role_id',$id)
        ->get();

        return view('frontend.pages.role.edit', compact('role','permissions','roleHasPermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // return $request;
        $rules = [
            'name' => 'required|string',
        ];
        
        $validatedData = $request->validate($rules);
        
        $role = Role::findOrFail($id); // Assuming $id is the id of the role being updated
        $role->name = $validatedData['name'];
        $role->save();
        if ($request->permissions) {
            // Sync permissions for the role
            $role->permissions()->sync($request->permissions);
        }        
        session()->flash('sweet_alert', [
            'type' => 'success',
            'title' => 'Success!',
            'text' => 'Role updated successfully',
        ]);
        
        // Redirect or return a response as needed
        return redirect()->route('role.index')->with('success', 'Role updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Role::findOrFail($id);
        $category->delete();
        session()->flash('sweet_alert', [
            'type' => 'success',
            'title' => 'Success!',
            'text' => 'Role Delete success',
        ]);
        return redirect()->route('role.index');
    }
}
