@extends('layouts.admin')
@section('content')
    <div class="container py-5">
      <div class="p-6 mx-auto bg-blue-200 max-w-7xl rounded-3xl">
        <div class="mb-4 row">
          <div class="col-auto">
            <h3 class="font-semibold">Edit Permission</h3>
          </div>
          <div class="col-auto ml-auto">
            {{-- @can('add role')--}}
            <a href="{{ route('admin.permissions.index') }}" class="btn btn-primary rounded-pill">back</a>
            {{-- @endcan--}}
          </div>
        </div>
        <div class="p-4 mb-4 border border-blue-400 rounded">
          <form action="{{ route('admin.permissions.update', $permission) }}" method="post">
            @csrf
            @method('put')
            <div class="form-group">
              <label for="permissionName" class="font-weight-bold">Permission Name</label>
              <input id="permissionName" type="text" name="permissionName" class="form-control rounded-pill" placeholder="Permission" value="{{ $permission->name }}" required>
              @error('permissionName')
                <span class="text-sm text-danger">{{ $message }}</span>
              @enderror
            </div>
            <button type="submit" class="mt-3 btn btn-primary rounded-pill">Update</button>
          </form>
        </div>
        <div class="p-2 mt-4 bg-blue-600 rounded"></div>
        <div class="p-4 mt-4 border border-blue-400 rounded">
          <h4 class="font-semibold">Roles with this Permission</h4>
          <div class="p-2 mt-4">
            @if($permission->roles)
              @foreach($permission->roles as $permission_role)
                <div class="mb-2 mr-2 d-inline-block">
                  <form method="POST" action="{{ route('admin.permissions.roles.remove', ['permission' => $permission, 'role' => $permission_role]) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger rounded-pill" onclick="return confirm('Are you sure you want to remove this role?')">{{ $permission_role->name }}</button>
                  </form>
                </div>
              @endforeach
            @endif
          </div>
          <form action="{{ route('admin.permissions.roles', $permission) }}" method="post" class="mt-4">
            @csrf
            <div class="form-group">
              <label for="role" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400">Add Role</label>
              <select id="role" name="role" class="form-control rounded-pill">
                @foreach($roles as $role)
                  <option value="{{ $role->name }}">{{ $role->name }}</option>
                @endforeach
              </select>
            </div>
            <button type="submit" class="mt-3 btn btn-primary rounded-pill">Assign</button>
            @error('role')
              <span class="text-sm text-danger">{{ $message }}</span>
            @enderror
          </form>
        </div>
      </div>
    </div>
@endsection
