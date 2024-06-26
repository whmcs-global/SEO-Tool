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
        //        $roles=Role::all();
        $roles = Role::whereNotIn('name', ['admin'])->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('admin.roles.create');
    }

    public function store(Request $request)
    {
        //        dd($request->roleName);
        $validated = $request->validate([
            'roleName' => 'required',
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
        //        dd($request->all());

        if ($role->hasPermissionTo($request->permission)) {
            return redirect(route('admin.roles.index'))
                ->with('message', 'Permission exists');
        }

        $role->givePermissionTo($request->permission);
        return redirect(route('admin.roles.index'))
            ->with('message', 'Permission added');
    }
    public function revokePermission(Role $role, Permission $permission)
    {
        //        dd($permission);
        //        dd($role);
        //        dd($permission);
        //        dd($role);
        if ($role->hasPermissionTo($permission)) {
            $role->revokePermissionTo($permission);
            return redirect(route('admin.roles.index'))
                ->with('message', 'Permission revoked');
        }
        return redirect(route('admin.roles.index'))
            ->with('message', 'Permission not exist');
    }
}
