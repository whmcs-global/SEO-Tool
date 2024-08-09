@extends('layouts.admin')
@section('title')
    Permission
@endsection

@section('content')
    @if(session('message'))
        <div class="alert alert-success" role="alert">
            <span class="font-weight-bold">Success alert!</span> {{ session('message') }}
        </div>
    @endif

    <div class="container py-5">
        <!-- <div class="mb-3 row justify-content-end">
            <div class="col-auto">
                {{-- @can('add role')--}}
                <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary rounded-pill">Create Permission</a>
                {{-- @endcan--}}
            </div>
        </div> -->
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
                        @foreach($permissions as $permission)
                            <tr>
                                <td>{{ $permission->name }}</td>
                                <td class="text-right">
                                    <div class="btn-group btn-group-sm" role="group" aria-label="Actions">
                                        <a href="{{ route('admin.permissions.edit', $permission) }}" class="btn btn-primary rounded-pill mr-2">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form method="POST" action="{{ route('admin.permissions.destroy', $permission) }}" class="d-inline" id="delete-form-{{ $permission->id }}">
                                            @csrf
                                            @method('delete')
                                            <button type="button" class="btn btn-danger rounded-pill" onclick="confirmDelete({{ $permission->id }})">
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

@push('scripts')
    <script>
        function confirmDelete(id) {
            swal({
                title: 'Are you sure?',
                text: 'You won\'t be able to revert this!',
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
@endpush
