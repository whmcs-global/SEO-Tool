@extends('layouts.guest')

@section('content')
<div class="container">
    <div class="alert alert-danger">
        {{ session('message') }}
    </div>
</div>
@endsection
