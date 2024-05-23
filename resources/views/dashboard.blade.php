@extends('layouts.admin')
@section('title')
Dashboard
@endsection
@section('content')
    @if(session('message'))
    <div class="alert alert-success" role="alert">
        <span class="font-weight-bold">Success alert!</span> {{ session('message') }}
    </div>
    @endif

    <div class="container py-5">
        @can('Add keyword')
        <div class="mb-3 row justify-content-end">
            <div class="col-auto">
                <a href="{{ route('keywords.create') }}" class="btn btn-primary rounded-pill">Add Keyword</a>
            </div>
        </div>
        @endcan
        <div class="bg-blue-200 card">
            <div class="p-0 card-body">
            @can('Keyword list')
            <table class="table table-hover">
                <thead class="thead-dark bg-primary">
                    <tr>
                        <th scope="col" style="width: 30%;">Keyword</th>
                        @role('Admin')
                        <th scope="col">Created By</th>
                        <th scope="col" style="width: 15%;">IP Address</th>
                        @endrole
                        @role('Super Admin')
                        <th scope="col">Created By</th>
                        <th scope="col" style="width: 15%;">IP Address</th>
                        @endrole
                        <th scope="col" style="width: 15%;">Created At</th>
                        <th scope="col" style="width: 15%;">Updated At</th>
                        <th scope="col" class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($keywords as $keyword)
                    <tr>
                        <td>{{ $keyword->keyword }}</td>
                        @role('Admin')
                        <td>{{ $keyword->user->name }}</td>
                        <td>{{ $keyword->ip_address}}</td>
                        @endrole
                        @role('Super Admin')
                        <td>{{ $keyword->user->name }}</td>
                        <td>{{ $keyword->ip_address}}</td>
                        @endrole
                        <td>{{ $keyword->created_at->format('M d, Y H:i A') }}</td>
                        <td>{{ $keyword->updated_at->format('M d, Y H:i A') }}</td>
                        <td class="text-right">
                            <div class="btn-group btn-group-sm" role="group" aria-label="Actions">
                                <a href="{{ route('keywords.analytics', $keyword) }}" class="btn btn-primary rounded-pill mr-2">
                                    <i class="fas fa-chart-line"></i> Analytics
                                </a>
                                @can('Edit keyword')
                                <a href="{{ route('keywords.edit', $keyword) }}" class="btn btn-secondary rounded-pill mr-2">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                @endcan
                                @can('Delete keyword')
                                <form method="POST" action="{{ route('keywords.destroy', $keyword) }}" class="d-inline mr-2" id="delete-form-{{ $keyword->id }}">
                                    @csrf
                                    @method('delete')
                                    <button type="button" class="btn btn-danger rounded-pill" onclick="confirmDelete({{ $keyword->id }})">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endcan
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
        }).then((willDelete) => {
        if (willDelete) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
    }
    </script>
@endpush