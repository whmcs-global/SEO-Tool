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
        $permissions = [
            'Keyword Management' => Permission::whereIn('name', ['Keyword list', 'Add keyword', 'Edit keyword', 'Delete keyword'])->get(),
            'User Management' => Permission::whereIn('name', ['User list', 'Create user', 'Edit user', 'Delete user'])->get(),
            'Role Management' => Permission::whereIn('name', ['Role list', 'Create role', 'Edit role', 'Delete role'])->get(),
            'Backlink Management' => Permission::whereIn('name', ['Backlink list', 'Add backlink', 'Edit backlink', 'Delete backlink'])->get(),
            'Project Management' => Permission::whereIn('name', ['Add New Project','Edit Project','Delete Project','Project list'])->get(),
            'Google API' => Permission::whereIn('name', ['Google API'])->get(),
            'Data Export' => Permission::whereIn('name', ['Export GSC Data'])->get(),
        ];

        if(!auth()->user()->hasRole('Super Admin')){
            foreach ($permissions as $group => $perms) {
                $permissions[$group] = $perms->reject(function ($perm) {
                    return $perm->name === 'Google API';
                });
            }
        }

        // Filter out empty permission groups
        $permissions = array_filter($permissions, function ($perms) {
            return $perms->isNotEmpty();
        });

        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'roleName' => 'required|unique:roles,name',
        ]);

        $role = Role::create([
            'name' => $request->roleName,
        ]);

        $role->permissions()->attach($request->permissions);

        return redirect(route('admin.roles.index'))
            ->with('message', 'Role has successfully been created.');
    }

    public function edit(Role $role)
    {
        $permissions = [
            'Keyword Management' => Permission::whereIn('name', ['Keyword list', 'Add keyword', 'Edit keyword', 'Delete keyword'])->get(),
            'User Management' => Permission::whereIn('name', ['User list', 'Create user', 'Edit user', 'Delete user'])->get(),
            'Role Management' => Permission::whereIn('name', ['Role list', 'Create role', 'Edit role', 'Delete role'])->get(),
            'Backlink Management' => Permission::whereIn('name', ['Backlink list', 'Add backlink', 'Edit backlink', 'Delete backlink'])->get(),
            'Project Management' => Permission::whereIn('name', ['Add New Project','Edit Project','Delete Project','Project list'])->get(),
            'Google API' => Permission::whereIn('name', ['Google API'])->get(),
            'Data Export' => Permission::whereIn('name', ['Export GSC Data'])->get(),
        ];

        if (!auth()->user()->hasRole('Super Admin')) {
            foreach ($permissions as $group => $perms) {
                $permissions[$group] = $perms->reject(function ($perm) {
                    return $perm->name === 'Google API';
                });
            }
        }

        // Filter out empty permission groups
        $permissions = array_filter($permissions, function ($perms) {
            return $perms->isNotEmpty();
        });

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
