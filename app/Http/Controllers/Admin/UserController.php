<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, Website, User_project};
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {
        $users = User::whereNotIn('id', [1])
                     ->with(['parent', 'User_project.website'])
                     ->get();
        
        return view('admin.users.index', compact('users'));
    }
    
    

    public function create(){

        $websites = Website::all();
        $roles = Role::whereNotIn('name', ['super admin'])->get();
        return view('admin.users.create', compact('roles','websites'));
    }

    public function edit(User $user){

        $websites = Website::all();
        $selected_project = User_project::where('user_id', $user->id)->pluck('website_id')->toArray();
        $roles = Role::whereNotIn('name', ['super admin'])->get();
        return view('admin.users.edit', compact('roles','user','websites','selected_project'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|exists:roles,name',
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->update([
            // 'parent_id' => auth()->user()->id,
            'name' => $validated['name'],
        ]);

        $user->syncRoles([$validated['role']]);
    
        if ($request->filled('password')) {
            $validatedPassword = $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
            $user->update([
                'password' => Hash::make($validatedPassword['password']),
            ]);
        }
        User_project::where('user_id', $user->id)->delete();
        $projects = $request->projects;
        if($projects){
            foreach ($projects as $project) {
                $user_project = new User_project();
                $user_project->user_id = $user->id;
                $user_project->website_id = $project;
                $user_project->save();
            }
            $first_project = $projects[0];
        }
        $user->update([
            'website_id' => $first_project ?? null,
        ]);
        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    // public function update(Request $request, User $user)
    // {
    //     $validated = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'role' => 'required|string|exists:roles,name',
    //         'password' => ['required', 'confirmed', Rules\Password::defaults()],
    //     ]);

    //     $user->update([
    //         'name' => $validated['name'],
    //         'password' => Hash::make($request->password),
    //     ]);

    //     if ($request->filled('password')) {
    //         $validatedPassword = $request->validate([
    //             'password' => 'required|string|min:8|confirmed',
    //         ]);
    //         $user->update([
    //             'password' => Hash::make($validatedPassword['password']),
    //         ]);
    //     }

    //     $user->syncRoles([$validated['role']]);

    //     return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    // }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'exists:roles,id'],
        ]);

        $user = User::create([
            'parent_id' => auth()->user()->id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $role = Role::findById($request->role);
        $user->assignRole($role->name);
        $projects = $request->projects;
        if($projects){
            foreach ($projects as $project) {
                $user_project = new User_project();
                $user_project->user_id = $user->id;
                $user_project->website_id = $project;
                $user_project->save();
            }
            $first_project = $projects[0];
        }
        $user->update([
            'website_id' => $first_project ?? null,
        ]);
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
        if ($user->hasRole('Super Admin')) {
            return back()->with('message', 'you are super admin.');
        }
        $user->delete();

        return back()->with('message', 'User deleted.');
    }
}
