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
        @if(session('error'))
            <div class="alert alert-danger">
                <strong>Error alert!</strong> {{ session('error') }}
            </div>
        @endif

        <div class="py-12">
        <div class="mb-3 row justify-content-end">
            @can('Create user')
            <div class="col-auto">
                <a href="{{ route('admin.user.create') }}" class="btn btn-primary rounded-pill">Create User</a>
            </div>
            @endcan
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
                                    <th scope="col">Added By</th>
                                    <th scope="col">Projects</th>
                                    <th scope="col" class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @if($user->parent)
                                                {{ $user->parent->name }} <br>
                                                <small>({{ $user->parent->email }})</small>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            @foreach($user->User_project as $project)
                                                <div>
                                                    {{ $project->website->name }} ({{ $project->website->url }})
                                                </div>
                                            @endforeach
                                        </td>
                                        <td class="text-right">
                                            <div class="btn-group btn-group-sm" role="group" aria-label="Actions">
                                                @can('Edit user')
                                                <a href="{{ route('admin.user.edit', $user) }}" class="mr-2 btn btn-primary rounded-pill">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                @endcan
                                                @can('Delete user')
                                                <button type="button" class="btn btn-danger rounded-pill" onclick="confirmDelete({{ $user->id }})">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                                @endcan
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

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteUserModalLabel">Delete User and Transfer Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="delete-user-form" method="POST" action="">
                        @csrf
                        @method('delete')
                        <p>Select a user to transfer data to before deletion:</p>
                        <div class="form-group">
                            <label for="transfer_user_id">Select User</label>
                            <select class="form-control" id="transfer_user_id" name="transfer_user_id">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" id="delete_user_id" name="delete_user_id">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="submitDelete()">Delete and Transfer</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function confirmDelete(id) {
            $('#delete_user_id').val(id);
            $('#deleteUserModal').modal('show');
            $('#transfer_user_id option').show();
            $('#transfer_user_id option').each(function() {
                if ($(this).val() == id) {
                    $(this).hide();
                }
            });
        }

        function submitDelete() {
            var form = document.getElementById('delete-user-form');
            form.action = '/admin/users/' + $('#delete_user_id').val() + '/delete-and-transfer';
            form.submit();
        }
    </script>
@endpush
