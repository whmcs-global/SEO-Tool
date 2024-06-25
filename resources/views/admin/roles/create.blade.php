@extends('layouts.admin')
@section('content')
<div class="container py-5">
    <div class="px-4 py-3 mx-auto bg-blue-200 max-w-7xl rounded-3xl">
        <div class="row align-items-center">
            <div class="col-md-6 mt-2 mb-2">
                <h2>Form</h2>
            </div>
        </div>
        <form action="{{ route('admin.roles.store') }}" method="post">
            @csrf
            <div class="form-group">
                <label for="RoleName" class="ml-2">Role Name</label>
                <input id="RoleName" type="text" name="roleName" class="form-control rounded-3xl" placeholder="Role" />
                @error('roleName')
                    <span class="text-sm text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group ml-2">
                <label>Permissions</label>
                <div class="mb-2">
                    <button type="button" id="selectAllButton" class="btn btn-secondary btn-sm">Select All</button>
                </div>
                <div class="row">
                    @foreach ($permissions as $group => $perms)
                        <div class="col-md-12 mt-3">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h4 class="card-title">{{ $group }}</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @foreach ($perms as $permission)
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="{{ $permission->name }}">
                                                    <label class="form-check-label" for="{{ $permission->name }}">
                                                        {{ $permission->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary rounded-3xl">Create</button>
                <a href="{{ url()->previous() }}" class="btn btn-secondary" >
                                        {{ __('Back') }}
                                </a>
            </div>
        </form>
        <div class="bg-blue-600 mt-4 rounded-3xl"></div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllButton = document.getElementById('selectAllButton');
        const checkboxes = document.querySelectorAll('input[type="checkbox"][name="permissions[]"]');

        selectAllButton.addEventListener('click', function() {
            const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);

            checkboxes.forEach(checkbox => {
                checkbox.checked = !allChecked;
            });

            selectAllButton.textContent = allChecked ? 'Select All' : 'Deselect All';
        });
    });
</script>
@endsection
