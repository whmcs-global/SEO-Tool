@extends('layouts.admin')
@section('content')
    <div class="container py-5">
      <div class="p-6 mx-auto bg-blue-200 max-w-7xl rounded-3xl">
        <div class="mb-4 row">
          <div class="col">
            <h3 class="font-semibold">Add Permission</h3>
          </div>
        </div>
        <form action="{{ route('admin.permissions.store') }}" method="post">
          @csrf
          <div class="form-group">
            <label for="permissionName" class="font-weight-bold">Permission Name</label>
            <input id="permissionName" type="text" name="permissionName" class="form-control rounded-pill" placeholder="Enter permission name" required>
            @error('permissionName')
              <span class="text-sm text-danger">{{ $message }}</span>
            @enderror
          </div>
          <button type="submit" class="mt-3 btn btn-primary rounded-pill">Create</button>
        </form>
        <div class="p-2 mt-4 bg-blue-600 rounded"></div>
      </div>
    </div>
@endsection
