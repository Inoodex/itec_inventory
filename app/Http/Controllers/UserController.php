<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Extra;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use App\Models\Employee;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('users.type', '1')
            ->select('users.*', 'roles.name as roleName')
            ->orderBy('users.id', 'desc')
            ->get();
        return view('frontend.pages.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::get();
        return view('frontend.pages.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     $rules = [
    //         // 'role_id' => 'required',
    //         'user_role' => 'required',
    //         'name' => 'required|string',
    //         'email' => 'required|email|unique:users,email',
    //         'phone' => 'unique:users,phone',
    //         'password' => 'required|string|min:6',
    //         'status' => 'required|in:0,1',
    //         // You might need additional validation rules for file uploads
    //     ];

    //     $validatedData = $request->validate($rules);

    //     $imageName = "";
    //     if ($request->hasFile('images')) {
    //         $image = $request->file('images');
    //         $destinationPath = public_path('frontend/users/');
    //         $imageName = now()->format('YmdHis') . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
    //         $image->move($destinationPath, $imageName);
    //     }
    //     // Create a new product instance
    //     $user = new User();
    //     $user->type = '1';
    //     $user->role_id = $validatedData['user_role'];
    //     $user->name = $validatedData['name'];
    //     $user->email = $validatedData['email'];
    //     $user->phone = $validatedData['phone'];
    //     $user->password = bcrypt($validatedData['password']); // Hash password
    //     $user->images = $imageName; // Hash password
    //     $user->status = $validatedData['status'];
    //     $user->save();

    //     $role = Role::where('id', $validatedData['user_role'])->first();
    //     $user->assignRole($role);

    //     session()->flash('sweet_alert', [
    //         'type' => 'success',
    //         'title' => 'Success!',
    //         'text' => 'User added success',
    //     ]);
    //     // Redirect or return a response as needed
    //     return redirect()->route('users.index')->with('success', 'User created successfully');
    // }

    public function store(Request $request)
{
    $rules = [
        'user_role' => 'required',
        'name' => 'required|string',
        'email' => 'required|email|unique:users,email',
        'phone' => 'unique:users,phone',
        'password' => 'required|string|min:6',
        'status' => 'required|in:0,1',
    ];

    $validatedData = $request->validate($rules);

    $imageName = "";
    if ($request->hasFile('images')) {
        $image = $request->file('images');
        $destinationPath = public_path('frontend/users/');
        $imageName = now()->format('YmdHis') . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
        $image->move($destinationPath, $imageName);
    }

    // Create User
    $user = new User();
    $user->type = '1';
    $user->role_id = $validatedData['user_role'];
    $user->name = $validatedData['name'];
    $user->email = $validatedData['email'];
    $user->phone = $validatedData['phone'];
    $user->password = bcrypt($validatedData['password']);
    $user->images = $imageName;
    $user->status = $validatedData['status'];
    $user->save();

    // Assign Role
    $role = Role::find($validatedData['user_role']);
    if ($role) {
        $user->assignRole($role);
    }

    // Create Employee record automatically if role is employee
    if ($role && $role->name === 'Employee') {
        Employee::create([
            'user_id' => $user->id,
            'employee_id' => 'EMP' . str_pad($user->id, 4, '0', STR_PAD_LEFT), // Example: EMP0001
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'status' => $user->status ? 'active' : 'inactive',
        ]);
    }

    session()->flash('sweet_alert', [
        'type' => 'success',
        'title' => 'Success!',
        'text' => 'User and employee record added successfully.',
    ]);

    return redirect()->route('users.index')->with('success', 'User created successfully');
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
        $roles = Role::get();
        $user = User::where('id', $id)->first();
        return view('frontend.pages.users.edit', compact('roles', 'user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
{
    $user = User::findOrFail($id);

    $rules = [
        'user_role' => 'required',
        'name' => 'required|string',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'phone' => 'nullable|unique:users,phone,' . $user->id,
        'password' => 'nullable|string|min:6',
        'status' => 'required|in:0,1',
    ];

    $validatedData = $request->validate($rules);

    if ($request->hasFile('images')) {
        $image = $request->file('images');
        $destinationPath = public_path('frontend/users/');
        $imageName = now()->format('YmdHis') . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
        $image->move($destinationPath, $imageName);
        $user->images = $imageName;
    }

    $user->name = $validatedData['name'];
    $user->email = $validatedData['email'];
    $user->phone = $validatedData['phone'];
    if (!empty($validatedData['password'])) {
        $user->password = bcrypt($validatedData['password']);
    }
    $user->role_id = $validatedData['user_role'];
    $user->status = $validatedData['status'];
    $user->save();

    // Update role
    $role = Role::find($validatedData['user_role']);
    if ($role) {
        $user->syncRoles([$role->name]);
    }

    // Update Employee record if exists
    if ($role && $role->name === 'Employee') {
        $employee = Employee::where('user_id', $user->id)->first();
        if ($employee) {
            $employee->update([
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'status' => $user->status ? 'active' : 'inactive',
            ]);
        } else {
            // Create employee if it doesn't exist
            Employee::create([
                'user_id' => $user->id,
                'employee_id' => 'EMP' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'status' => $user->status ? 'active' : 'inactive',
            ]);
        }
    }

    return redirect()->route('users.index')->with('success', 'User updated successfully');
}


    /**
     * Remove the specified resource from storage.
     */
  public function destroy(string $id)
{
    $user = User::find($id);

    if (!$user) {
        return redirect()->back()->with('error', 'User not found.');
    }

    // Delete related employee record if it exists
    if ($user->employee) {
        $user->employee->delete();
    }

    $user->delete();

    return redirect()->back()->with('success', 'User and related employee record deleted successfully.');
}


    public function pin()
    {
        $extras = Extra::where('status', '1')->get();
        return view('frontend.pages.users.pin', compact('extras'));
    }

    public function pinStore(Request $request)
    {
        $inputs = $request->all();

        foreach ($inputs as $key => $input) {
            if (Extra::where('name', $key)->exists()) {  // Avoid unnecessary queries
                Extra::where('name', $key)->update(['value' => $input]);
            }
        }
        return redirect()->back();
    }
}