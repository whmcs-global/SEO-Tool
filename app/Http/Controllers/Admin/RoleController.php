<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        // dd('here');
            //    $roles=Role::all();
        $roles = Role::whereNotIn('name', ['super admin'])->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('admin.roles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'roleName' => 'required|unique:roles,name',
        ]);

        Role::create([
            'name' => $request->roleName,
        ]);

        return redirect(route('admin.roles.index'))
            ->with('message', 'Role has successfully been created.');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'roleName' => 'required',
        ]);
        $role->update(['name' => $request->roleName,]);

        return redirect(route('admin.roles.index'))
            ->with('message', 'Role has successfully been updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        //        dd($role);
        $role->delete();
        return back()->with('message', 'Role has successfully been Deleted.');
    }

    public function givePermission(Request $request, Role $role)
    {
        $request->validate([
            'roleName' => 'required',
        ]);
        $role->update(['name' => $request->roleName,]);

        $permissions = $request->input('permissions', []);

        $existingPermissions = $role->permissions->pluck('name')->toArray();
        $newPermissions = array_diff($permissions, $existingPermissions);
        $removedPermissions = array_diff($existingPermissions, $permissions);

        if (!empty($newPermissions)) {
            $role->givePermissionTo($newPermissions);
        }

        if (!empty($removedPermissions)) {
            $role->revokePermissionTo($removedPermissions);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Permissions updated successfully.');
    }


    public function revokePermission(Role $role, Permission $permission)
    {
        if ($role->hasPermissionTo($permission)) {
            $role->revokePermissionTo($permission);
            return redirect(route('admin.roles.index'))
                ->with('message', 'Permission revoked');
        }
        return redirect(route('admin.roles.index'))
            ->with('message', 'Permission not exist');
    }
}
