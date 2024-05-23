@extends('layouts.admin')

@section('content')
<div class="container py-5">
    <h4 class="mb-4 font-weight-bold">{{ $role->name }} Role Permissions</h4>
    <form action="{{ route('admin.roles.permissions', $role) }}" method="post" novalidate>
        @csrf
        <div class="mb-3">
            <label for="RoleName" class="form-label">Role Name</label>
            <input id="RoleName" type="text" name="roleName" class="form-control" placeholder="Role Name" value="{{ $role->name }}" required>
            @error('roleName')
            <div class="invalid-feedback d-block">
                {{ $message }}
            </div>
            @enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Permissions</label>
            <div class="row">
                @foreach($permissions as $permission)
                    <div class="mb-2 col-md-4 col-sm-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="permission_{{ $permission->id }}" @if($role->permissions->contains($permission)) checked @endif>
                            <label class="form-check-label" for="permission_{{ $permission->id }}">
                                {{ $permission->name }}
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
            @error('permissions')
            <div class="text-danger">
                {{ $message }}
            </div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary rounded-pill">Submit</button>
    </form>
</div>
@endsection
