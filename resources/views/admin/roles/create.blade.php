@extends('layouts.admin')
@section('content')
<div class="container py-5">
    <div class="px-4 py-3 mx-auto bg-blue-200 max-w-7xl rounded-3xl">
      <div class="row">
        <div class="col-auto mt-2 mb-2">
          Form
        </div>
        <div class="col-auto mt-2 mb-2 ml-auto">
          {{-- @can('add role')--}}
          <a href="{{route('admin.roles.index')}}" class="btn btn-primary rounded-3xl">back</a>
          {{-- @endcan--}}
        </div>
      </div>
      <form action="{{route('admin.roles.store')}}" method="post">
        @csrf
        <div class="form-group">
          <label for="RoleName" class="ml-2">Role Name</label>
          <input id="RoleName" type="text" name="roleName" class="form-control rounded-3xl" placeholder="role" />
          @error('roleName')
          <span class="text-sm text-danger">{{$message}}</span>
          @enderror
        </div>
        <button type="submit" class="mb-4 ml-2 btn btn-primary rounded-3xl">Create</button>
      </form>
      <div class="bg-blue-600"></div>
    </div>
  </div>
@endsection
