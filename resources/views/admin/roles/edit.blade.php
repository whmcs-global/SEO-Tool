@extends('layouts.admin')

@section('content')
<div class="container py-5">
  <div class="p-6 mx-auto bg-blue-200 border border-blue-400 max-w-7xl rounded-3xl">
    <div class="mb-4 row">
      <div class="col-auto mt-2 mb-2">
        <h3 class="font-semibold">Form</h3>
      </div>
      <div class="col-auto mt-2 mb-2 ml-auto">
        {{-- @can('add role')--}}
        <a href="{{route('admin.roles.index')}}" class="btn btn-primary rounded-pill">back</a>
        {{-- @endcan--}}
      </div>
    </div>
    <form action="{{route('admin.roles.update', $role)}}" method="post">
      @csrf
      @method('put')
      <div class="form-group">
        <label for="RoleName" class="ml-2 font-weight-bold">Role Name</label>
        <input id="RoleName" type="text" name="roleName" class="form-control rounded-pill" placeholder="role" value="{{$role->name}}" />
        @error('roleName')
        <span class="text-sm text-danger">{{$message}}</span>
        @enderror
      </div>
      <button type="submit" class="mb-4 ml-2 btn btn-primary rounded-pill">Update</button>
    </form>
    <div class="p-2 mb-4 bg-blue-600 rounded"></div>
    <div class="p-2 mt-4 border border-blue-400 rounded">
      <h4 class="font-semibold">Role Permissions</h4>
      <div class="p-2 mt-4">
        @if($role->permissions)
        @foreach($role->permissions as $role_permission)
        <div class="mb-2 mr-2 d-inline-block">
          <form method="POST" action="{{ route('admin.roles.permissions.revoke', ['role' => $role, 'permission' => $role_permission]) }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger rounded-pill">{{ $role_permission->name }}</button>
          </form>
        </div>
        @endforeach
        @endif
      </div>
      <form action="{{ route('admin.roles.permissions', $role) }}" method="post" class="mt-4">
        @csrf
        <div class="form-group">
          <label for="permission" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400">Permission</label>
          <select id="permission" name="permission" class="form-control rounded-pill">
            @foreach($permissions as $permission)
            <option value="{{ $permission->name }}">{{ $permission->name }}</option>
            @endforeach
          </select>
        </div>
        <button type="submit" class="mt-3 btn btn-primary rounded-pill">Assign</button>
        @error('permission')
        <span class="text-sm text-danger">{{ $message }}</span>
        @enderror
      </form>
    </div>
  </div>
</div>
@endsection
