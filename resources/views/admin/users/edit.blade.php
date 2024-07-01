@extends('layouts.admin')

@section('title')
    @isset($user)
        Edit User
    @else
        Create User
    @endisset
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        @isset($user)
                            {{ __('Edit User') }}
                        @else
                            {{ __('Create User') }}
                        @endisset
                    </div>
                    <div class="card-body">
                        <form method="POST"
                            action="{{ isset($user) ? route('admin.user.update', $user) : route('admin.user.store') }}"
                            id="userForm">
                            @csrf
                            @isset($user)
                                @method('PUT')
                            @endisset
                            <div class="mb-3 row">
                                <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Name') }}</label>
                                <div class="col-md-6">
                                    <input id="name" type="text"
                                        class="form-control @error('name') is-invalid @enderror" name="name"
                                        value="{{ old('name', $user->name ?? '') }}" required autocomplete="name" autofocus>
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="password"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>
                                <div class="col-md-6">
                                    <input id="password" type="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                         autocomplete="new-password">
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="password-confirm"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>
                                <div class="col-md-6">
                                    <input id="password-confirm" type="password" class="form-control"
                                        name="password_confirmation" autocomplete="new-password">
                                    <span id="confirmPasswordError" class="text-danger" style="display: none;">Please confirm your password.</span>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="role"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Role') }}</label>
                                <div class="col-md-6">
                                    <select id="role" class="form-control @error('role') is-invalid @enderror"
                                        name="role" required>
                                        <option value="">Select Role</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}"
                                                {{ old('role', $user->roles->first()->name ?? '') == $role->name ? 'selected' : '' }}>
                                                {{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('role')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="field2" class="col-md-4 col-form-label text-md-end">{{ __('Assign Projects') }}</label>
                                <div class="col-md-6">
                                    <select id="field2" class="form-control" name="projects[]" multiple multiselect-search="true" multiselect-max-items="3">
                                        @foreach($websites as $website)
                                            <option value="{{ $website->id }}" {{ in_array($website->id, $selected_project) ? 'selected' : '' }}>{{ $website->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mb-0 row">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        @isset($user)
                                            {{ __('Update') }}
                                        @else
                                            {{ __('Save') }}
                                        @endisset
                                    </button>
                                    <a href="{{ url()->previous() }}" class="btn btn-black" >
                                        {{ __('Back') }}
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/custom/multiselect-dropdown.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#userForm').on('submit', function(e) {
                var password = $('#password').val();
                var confirmPassword = $('#password-confirm').val();

                if (password !== '' && confirmPassword === '') {
                    e.preventDefault();
                    $('#confirmPasswordError').text('Please confirm your password.').show();
                } else if (password !== '' && password !== confirmPassword) {
                    e.preventDefault();
                    $('#confirmPasswordError').text('Passwords do not match.').show();
                } else {
                    $('#confirmPasswordError').hide();
                }
            });
        });
    </script>
@endpush