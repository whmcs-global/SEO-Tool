<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {
        $users = User::whereNotIn('id', [1])->get();

        return view('admin.users.index', compact('users'));
    }

    public function create(){
        
        $roles = Role::whereNotIn('name', ['super admin'])->get();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'exists:roles,id'],
        ]);
    
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
    
        $role = Role::findById($request->role);
        $user->assignRole($role->name);
    
        return redirect(route('admin.users.index'));
    }

    public function show(User $user)
    {
        // $roles = Role::all();
        $roles = Role::whereNotIn('name', ['super admin'])->get();
        $permissions = Permission::all();

        return view('admin.users.role', compact('user', 'roles', 'permissions'));
    }

    // public function assignRole(Request $request, User $user)
    // {
    //     if ($user->hasRole($request->role)) {
    //         return back()->with('message', 'Role exists.');
    //     }
    //     $user->assignRole($request->role);
    //     return back()->with('message', 'Role assigned.');
    // }

    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|exists:roles,name'
        ]);

        $role = Role::where('name', $request->role)->firstOrFail();
        
        if ($user->hasRole($role->name)) {
            return back()->with('message', 'Role already exists.');
        }
        $user->roles()->detach();

        $user->assignRole($role->name);

        return back()->with('message', 'Role assigned successfully.');
    }

    public function removeRole(User $user, Role $role)
    {
        if ($user->hasRole($role)) {
            $user->removeRole($role);
            return back()->with('message', 'Role removed.');
        }

        return back()->with('message', 'Role not exists.');
    }

    public function givePermission(Request $request, User $user)
    {
        $permissions = $request->input('permissions', []);

        $existingPermissions = $user->permissions->pluck('name')->toArray();
        $newPermissions = array_diff($permissions, $existingPermissions);
        $removedPermissions = array_diff($existingPermissions, $permissions);

        if (!empty($newPermissions)) {
            $user->givePermissionTo($newPermissions);
        }

        if (!empty($removedPermissions)) {
            $user->revokePermissionTo($removedPermissions);
        }

        return redirect()->back()->with('success', 'Permissions updated successfully.');
    }

    public function revokePermission(User $user, Permission $permission)
    {
        if ($user->hasPermissionTo($permission)) {
            $user->revokePermissionTo($permission);
            return back()->with('message', 'Permission revoked.');
        }
        return back()->with('message', 'Permission does not exists.');
    }

    public function destroy(User $user)
    {
        if ($user->hasRole('admin')) {
            return back()->with('message', 'you are admin.');
        }
        $user->delete();

        return back()->with('message', 'User deleted.');
    }
}
