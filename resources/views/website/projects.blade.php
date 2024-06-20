@extends('layouts.admin')

@section('title', 'Manage Projects')

@section('content')
    @if(session('message'))
        <div class="alert alert-success" role="alert">
            <span class="font-weight-bold">Success alert!</span> {{ session('message') }}
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
        <div class="bg-blue-200 card">
            <div class="p-0 card-body">
                <table class="table table-hover">
                    <thead class="thead-dark bg-primary">
                        <tr>
                            <th scope="col">Name</th>
                            <!-- <th scope="col" class="text-right">Actions</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($websites as $website)
                            <tr>
                                <td>{{ $website->name }}</td>
                                <!-- <td class="text-right">
                                    <div class="btn-group btn-group-sm" role="group" aria-label="Actions">
                                        <a href="" class="mr-2 btn btn-primary rounded-pill">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        @if($website->id)
                                            <form method="POST" action="" class="d-inline" id="delete-form-{{ $website->id }}">
                                                @csrf
                                                @method('delete')
                                                <button type="button" class="btn btn-danger rounded-pill" onclick="confirmDelete({{ $website->id }})">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td> -->
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
