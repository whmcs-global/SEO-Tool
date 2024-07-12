@extends('layouts.admin')

@section('title', 'Manage Projects')

@section('content')
    @if(session('message'))
        <div class="alert alert-success" role="alert">
            <span class="font-weight-bold"></span> {{ session('message') }}
        </div>
    @endif

    <div class="container py-5">
        <div class="mb-3 row justify-content-end">
            <div class="col-auto">
                @can('Add New Project')
                <a href="{{ route('admin.websites.create') }}" class="btn btn-primary rounded-pill">Create Project</a>
                @endcan
            </div>
        </div>
        @can('Project list')
        <div class="bg-blue-200 card">
            <div class="p-0 card-body">
                <table class="table table-hover">
                    <thead class="thead-dark bg-primary">
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Url</th>
                            @role('Super Admin')
                            <th scope="col">Added by</th>
                            @endrole
                            <th scope="col" class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($websites as $website)
                            <tr>
                                <td>{{ $website->name }}</td>
                                <td>
                                    <a href="{{ $website->url }}" target="_blank">{{ substr($website->url,0,50) }}</a>
                                </td>
                                @role('Super Admin')
                                <td>{{ $website->user->name }}</td>
                                @endrole
                                </td>
                                <td class="text-right">
                                    <div class="btn-group btn-group-sm" role="group" aria-label="Actions">
                                        @can('Edit Project')
                                        <a href="{{ route('admin.websites.edit', $website->id) }}" class="mr-2 btn btn-primary rounded-pill">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        @endcan
                                        @can('Delete Project')
                                        @if($website->id)
                                            <form method="POST" action="{{ route('admin.websites.delete', $website)}}" class="d-inline" id="delete-form-{{ $website->id }}">
                                                @csrf
                                                @method('delete')
                                                <button type="button" class="btn btn-danger rounded-pill" onclick="confirmDelete({{ $website->id }})">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endcan
    </div>
@endsection
