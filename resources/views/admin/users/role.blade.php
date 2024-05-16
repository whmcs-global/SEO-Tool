@extends('layouts.admin')

@section('content')
<div class="container py-4">
    @if(session('message'))
    <div class="alert alert-success">{{ session('message') }}</div>
    @endif

        <div class="col-md-12">
            <div class="card">
                <div class="text-center card-header bg-primary">
                    <h3 class="mb-0 text-white">User Details</h3>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5 class="font-weight-bold">User Name:</h5>
                        <p>{{ $user->name }}</p>
                    </div>
                    <div class="mb-4">
                        <h5 class="font-weight-bold">User Email:</h5>
                        <p>{{ $user->email }}</p>
                    </div>

                    <div class="p-4 rounded-lg bg-light">
                        <h4 class="mb-3 font-weight-bold">Roles</h4>
                        @if ($user->roles->isNotEmpty())
                            <ul class="list-unstyled">
                                @foreach ($user->roles as $user_role)
                                    <li>
                                        <form method="POST" action="{{ route('admin.users.roles.remove', [$user->id, $user_role->id]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">{{ $user_role->name }}</button>
                                        </form>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p>No roles assigned.</p>
                        @endif

                        <form method="POST" action="{{ route('admin.users.roles', $user->id) }}" class="mt-3">
                            @csrf
                            <div class="form-group">
                                <label for="role">Select Role:</label>
                                <select id="role" name="role" class="form-control">
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('role')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <button type="submit" class="btn btn-success">Assign Role</button>
                        </form>
                    </div>

                    <div class="p-4 mt-4 rounded-lg bg-light">
                        <h4 class="mb-3 font-weight-bold">Permissions</h4>
                        @if ($user->permissions->isNotEmpty())
                            <ul class="list-unstyled">
                                @foreach ($user->permissions as $user_permission)
                                    <li>
                                        <form method="POST" action="{{ route('admin.users.permissions.revoke', [$user->id, $user_permission->id]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">{{ $user_permission->name }}</button>
                                        </form>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p>No permissions assigned.</p>
                        @endif

                        <form method="POST" action="{{ route('admin.users.permissions', $user) }}" class="mt-3">
                            @csrf
                            <div class="form-group">
                                <label for="permission">Select Permission:</label>
                                <select id="permission" name="permission" class="form-control">
                                    @foreach ($permissions as $permission)
                                        <option value="{{ $permission->name }}">{{ $permission->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <button type="submit" class="btn btn-success">Assign Permission</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
</div>
@endsection
