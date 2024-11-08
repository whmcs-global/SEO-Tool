@extends('layouts.admin')

@section('title', 'Manage Projects')

@section('content')
    @if (session('message'))
        <div class="alert alert-success" role="alert">
            <span class="font-weight-bold"></span> {{ session('message') }}
        </div>
    @endif

    <div class="container py-5">
        <div class="mb-3 row justify-content-end">
            <div class="col-auto">
                @can('Add New Project')
                    <a href="{{ route('admin.websites.create') }}" class="btn btn-primary rounded-pill">Create New Project</a>
                @endcan
            </div>
        </div>

        @can('Project list')
            <div class="card">
                <div class="card-header">
                    <h4>Manage Projects</h4>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-md">
                            <thead>
                                <tr>
                                    <th>Project Name</th>
                                    <th>Url</th>
                                    @role('Super Admin')
                                        <th>Added by</th>
                                    @endrole
                                    <th>Assigned Users</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($websites as $website)
                                    <tr>
                                        <td>{{ $website->name }}</td>
                                        <td>
                                            <a href="{{ $website->url }}" target="_blank">{{ substr($website->url, 0, 50) }}</a>
                                        </td>
                                        @role('Super Admin')
                                            <td>{{ $website->user->name }}</td>
                                        @endrole
                                        <td>
                                            @if ($website->users->isEmpty())
                                                <span class="badge badge-secondary">No users assigned</span>
                                            @else
                                                @foreach ($website->users as $user)
                                                    @php
                                                        $nameParts = explode(' ', $user->name);
                                                        $firstInitial = strtoupper(substr($nameParts[0], 0, 1));
                                                        $lastInitial = isset($nameParts[1])
                                                            ? strtoupper(substr($nameParts[1], 0, 1))
                                                            : '';
                                                    @endphp
                                                    <span class="team-member" data-toggle="tooltip" title="{{ $user->name }}">
                                                        <span class="initials"
                                                            style="display:inline-block; width: 30px; height: 30px; border-radius: 50%; background-color: #007bff; color: white; text-align: center; line-height: 30px;">
                                                            {{ $firstInitial }}{{ $lastInitial }}
                                                        </span>
                                                    </span>
                                                @endforeach
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            <div class="btn-group btn-group-sm" role="group" aria-label="Actions">
                                                @can('Edit Project')
                                                    <a href="{{ route('admin.websites.edit', $website->id) }}"
                                                        class="mr-2 btn btn-primary rounded-pill">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                @endcan
                                                @can('Delete Project')
                                                    @if ($website->id)
                                                        <form method="POST"
                                                            action="{{ route('admin.websites.delete', $website) }}"
                                                            class="d-inline" id="delete-form-{{ $website->id }}">
                                                            @csrf
                                                            @method('delete')
                                                            <button type="button" class="btn btn-danger rounded-pill"
                                                                onclick="confirmDelete({{ $website->id }})">
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
            </div>
        @endcan
    </div>
@endsection
