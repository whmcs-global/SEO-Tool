<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    //
    public function index(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $permissions = Permission::all();
        return view('admin.permissions.index', compact('permissions'));
    }


    public function create()
    {
        return view('admin.permissions.create');
    }

    public function store(Request $request)
    {
        //dd($request->roleName);
        $request->validate([
            'permissionName' => 'required',
        ]);
        Permission::create([
            'name' => $request->permissionName,
        ]);

        return redirect(route('admin.permissions.index'))
            ->with('message', 'Permission has successfully been created.');
    }

    public function edit(Permission $permission)
    {
        $roles = Role::whereNotIn('name', ['admin'])->get();
        return view('admin.permissions.edit', compact('permission', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
//        dd($request);
        $request->validate([
            'permissionName' => 'required',
        ]);
        $permission->update(['name' => $request->permissionName,]);

        return redirect(route('admin.permissions.index'))
            ->with('message', 'permission has successfully been updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect(route('admin.permissions.index'))
            ->with('message', 'permission has successfully been deleted.');
    }

    public function assignRole(Request $request, Permission $permission)
    {
        //        dd($request);
        if ($permission->hasRole($request->role)) {
            return redirect(route('admin.permissions.index'))
                ->with('message', 'Role exists ;(');
        }
        $permission->assignRole($request->role);
        return redirect(route('admin.permissions.index'))
            ->with('message', 'Role Assigned ;)');
    }

    public function removeRole(Permission $permission, Role $role)
    {
        if ($permission->hasRole($role)) {
            $permission->removeRole($role);
            return redirect(route('admin.permissions.index'))
                ->with('message', 'Role Deleted ;(');
        }
        return redirect(route('admin.permissions.index'))
            ->with('message', 'Role doesnt Exist ;)');
    }


}
