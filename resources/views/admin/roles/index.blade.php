@extends('layouts.admin')
@section('title')
Role
@endsection
@section('content')
@if(session('message'))
<div class="alert alert-success" role="alert">
  <span class="font-weight-bold">Success alert!</span> {{ session('message') }}
</div>
@endif

<div class="container py-5">
  <div class="mb-3 row justify-content-end">
    <div class="col-auto">
      {{-- @can('add role')--}}
      <a href="{{route('admin.roles.create')}}" class="btn btn-primary rounded-pill">Create Role</a>
      {{-- @endcan--}}
    </div>
  </div>

  <div class="bg-blue-200 card">
    <div class="p-0 card-body">
    <table class="table table-hover">
        <thead class="thead-dark bg-primary">
            <tr>
                <th scope="col">Name</th>
                <th scope="col" class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($roles as $role)
            <tr>
                <td>{{ $role->name }}</td>
                <td class="text-right">
                    <div class="btn-group btn-group-sm" role="group" aria-label="Actions">
                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary rounded-pill mr-2">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" class="d-inline">
                            @csrf
                            @method('delete')
                            <button type="submit" class="btn btn-danger rounded-pill" onclick="return confirm('Are you sure you want to delete this role?')">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
  </div>
</div>
@endsection
