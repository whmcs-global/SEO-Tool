<!-- resources/views/websites/create.blade.php -->
@extends('layouts.admin')

@section('title', 'Add New Website')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Add New Website</div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.websites.store') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">Name</label>
                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autofocus>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="url" class="col-md-4 col-form-label text-md-right">URL</label>
                            <div class="col-md-6">
                                <input id="url" type="url" class="form-control @error('url') is-invalid @enderror" name="url" value="{{ old('url') }}" required>
                                @error('url')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="GOOGLE_ANALYTICS_CLIENT_ID" class="col-md-4 col-form-label text-md-right">Google Analytics Client ID</label>
                            <div class="col-md-6">
                                <input id="GOOGLE_ANALYTICS_CLIENT_ID" type="text" class="form-control @error('GOOGLE_ANALYTICS_CLIENT_ID') is-invalid @enderror" name="GOOGLE_ANALYTICS_CLIENT_ID" value="{{ old('GOOGLE_ANALYTICS_CLIENT_ID') }}" required>
                                @error('GOOGLE_ANALYTICS_CLIENT_ID')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="GOOGLE_ANALYTICS_CLIENT_SECRET" class="col-md-4 col-form-label text-md-right">Google Analytics Client Secret</label>
                            <div class="col-md-6">
                                <input id="GOOGLE_ANALYTICS_CLIENT_SECRET" type="text" class="form-control @error('GOOGLE_ANALYTICS_CLIENT_SECRET') is-invalid @enderror" name="GOOGLE_ANALYTICS_CLIENT_SECRET" value="{{ old('GOOGLE_ANALYTICS_CLIENT_SECRET') }}" required>
                                @error('GOOGLE_ANALYTICS_CLIENT_SECRET')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="GOOGLE_ANALYTICS_REDIRECT_URI" class="col-md-4 col-form-label text-md-right">Google Analytics Redirect URI</label>
                            <div class="col-md-6">
                                <input id="GOOGLE_ANALYTICS_REDIRECT_URI" type="url" class="form-control @error('GOOGLE_ANALYTICS_REDIRECT_URI') is-invalid @enderror" name="GOOGLE_ANALYTICS_REDIRECT_URI" value="{{ old('GOOGLE_ANALYTICS_REDIRECT_URI') }}" required>
                                @error('GOOGLE_ANALYTICS_REDIRECT_URI')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="API_KEY" class="col-md-4 col-form-label text-md-right">API Key</label>
                            <div class="col-md-6">
                                <input id="API_KEY" type="text" class="form-control @error('API_KEY') is-invalid @enderror" name="API_KEY" value="{{ old('API_KEY') }}" required>
                                @error('API_KEY')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Add Website
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
