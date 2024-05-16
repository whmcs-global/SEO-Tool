@extends('layouts.admin')

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
      <table class="table mb-0 table-striped table-hover">
        <thead class="text-white bg-primary">
          <tr>
            <th scope="col" class="px-4 py-3">Name</th>
            <th scope="col" class="px-4 py-3 text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($roles as $role)
          <tr>
            <td class="px-4 py-3">{{ $role->name }}</td>
            <td class="px-4 py-3 text-right">
              <a href="{{route('admin.roles.edit', $role)}}" class="mr-2 btn btn-primary btn-sm rounded-pill">Edit</a>
              <form method="POST" action="{{route('admin.roles.destroy', $role)}}" class="d-inline-block">
                @csrf
                @method('delete')
                <button type="submit" class="btn btn-danger btn-sm rounded-pill" onclick="return confirm('Are you sure you want to delete this role?')">Delete</button>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
