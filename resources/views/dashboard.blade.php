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
        @can('add keyword')
        <div class="mb-3 row justify-content-end">
            <div class="col-auto">
                <a href="{{ route('keywords.create') }}" class="btn btn-primary rounded-pill">Add Keyword</a>
            </div>
        </div>
        @endcan
        <div class="bg-blue-200 card">
            <div class="p-0 card-body">
            <table class="table table-hover">
                <thead class="thead-dark bg-primary">
                    <tr>
                        <th scope="col" style="width: 30%;">Keyword</th>
                        @role('admin')
                        <th scope="col">Created By</th>
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
                        @role('admin')
                        <td>{{ $keyword->user->name }}</td>
                        @endrole
                        <td>{{ $keyword->created_at->format('M d, Y H:i A') }}</td>
                        <td>{{ $keyword->updated_at->format('M d, Y H:i A') }}</td>
                        <td class="text-right">
                            <div class="btn-group btn-group-sm" role="group" aria-label="Actions">
                                <a href="{{ route('keywords.analytics', $keyword) }}" class="btn btn-primary rounded-pill mr-2">
                                    <i class="fas fa-chart-line"></i> Analytics
                                </a>
                                @can('edit keyword')
                                <a href="{{ route('keywords.edit', $keyword) }}" class="btn btn-secondary rounded-pill mr-2">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                @endcan
                                @can('delete keyword')
                                <form method="POST" action="{{ route('keywords.destroy', $keyword) }}" class="d-inline mr-2">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" class="btn btn-danger rounded-pill" onclick="return confirm('Are you sure you want to delete this keyword?')">
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
            </div>
        </div>
    </div>
@endsection
