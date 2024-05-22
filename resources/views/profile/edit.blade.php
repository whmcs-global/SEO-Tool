@extends('layouts.admin')
@section('title')
Profile
@endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="p-4 mb-4 bg-white rounded-lg shadow-sm p-sm-8">
                {{-- <h2 class="mb-4 text-xl font-semibold text-dark">{{ __('Profile') }}</h2> --}}
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 mb-4 bg-white rounded-lg shadow-sm p-sm-8">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 mb-4 bg-white rounded-lg shadow-sm p-sm-8">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
