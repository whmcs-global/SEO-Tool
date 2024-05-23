@extends('layouts.admin')
@section('title')
    Users
@endsection

@section('content')
    <div class="container-fluid">
        @if(session('message'))
            <div class="alert alert-success">
                <strong>Success alert!</strong> {{ session('message') }}
            </div>
        @endif

        <div class="py-12">
        <div class="mb-3 row justify-content-end">
            <div class="col-auto">
                <a href="{{ route('admin.user.create') }}" class="btn btn-primary rounded-pill">Create User</a>
            </div>
        </div>
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="clearfix"></div>
                <div class="overflow-hidden bg-info">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-dark bg-primary">
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col" class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td class="text-right">
                                            <div class="btn-group btn-group-sm" role="group" aria-label="Actions">
                                                <a href="{{ route('admin.user.edit', $user) }}" class="btn btn-primary rounded-pill mr-2">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-primary rounded-pill mr-2">
                                                    <i class="fas fa-user-lock"></i> Roles & Permission
                                                </a>
                                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline" id="delete-form-{{ $user->id }}">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="button" class="btn btn-danger rounded-pill" onclick="confirmDelete({{ $user->id }})">
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