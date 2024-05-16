@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    @if(session('message'))
    <div class="alert alert-success">
        <strong>Success alert!</strong> {{ session('message') }}
    </div>
    @endif
    <div class="py-12">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="clearfix"></div>
            <div class="overflow-hidden bg-info">
                <div class="table-responsive">
                    <table class="table mb-0 table-striped table-hover">
                        <thead class="text-white bg-primary">
                            <tr>
                                <th scope="col" class="px-4 py-3">Name</th>
                                <th scope="col" class="px-4 py-3">Email</th>
                                <th scope="col" class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td class="px-4 py-3">{{$user->name}}</td>
                                <td class="px-4 py-3">{{$user->email}}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{route('admin.users.show',$user)}}" class="mr-2 btn btn-primary btn-sm">Roles & Permission</a>
                                    <form method="POST" action="{{route('admin.users.destroy',$user)}}" style="display:inline;">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
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
