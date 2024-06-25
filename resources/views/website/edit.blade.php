@extends('layouts.admin')

@section('title', 'Edit Project')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Edit Project</div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.websites.update', $website->id) }}">
                        @csrf
                        @method('PUT')

                        <x-form-field label="Name" name="name" value="{{ $website->name }}" required="true" />
                        <x-form-field label="URL" name="url" type="text" value="{{ $website->url }}" required="true" />
                        <x-form-field label="Google Analytics Client ID" name="GOOGLE_ANALYTICS_CLIENT_ID" value="{{ $website->GOOGLE_ANALYTICS_CLIENT_ID }}" />
                        <x-form-field label="Google Analytics Client Secret" name="GOOGLE_ANALYTICS_CLIENT_SECRET" value="{{ $website->GOOGLE_ANALYTICS_CLIENT_SECRET }}" />
                        <x-form-field label="Google Analytics Redirect URI" name="GOOGLE_ANALYTICS_REDIRECT_URI" type="text" value="{{ $website->GOOGLE_ANALYTICS_REDIRECT_URI }}" />
                        <x-form-field label="API Key" name="API_KEY" value="{{ $website->API_KEY }}" />
                        <x-form-field label="Google Ads Developer Token" name="GOOGLE_ADS_DEVELOPER_TOKEN" value="{{ $website->GOOGLE_ADS_DEVELOPER_TOKEN }}" />
                        <x-form-field label="Google Ads Client ID" name="GOOGLE_ADS_CLIENT_ID" value="{{ $website->GOOGLE_ADS_CLIENT_ID }}" />
                        <x-form-field label="Google Ads Client Secret" name="GOOGLE_ADS_CLIENT_SECRET" value="{{ $website->GOOGLE_ADS_CLIENT_SECRET }}" />
                        <x-form-field label="Google Ads Redirect URI" name="GOOGLE_ADS_REDIRECT_URI" type="text" value="{{ $website->GOOGLE_ADS_REDIRECT_URI }}" />
                        <x-form-field label="Google Ads Key" name="GOOGLE_ADS_KEY" value="{{ $website->GOOGLE_ADS_KEY }}" />
                        <x-form-field label="Google Ads Login Customer ID" name="GOOGLE_ADS_LOGIN_CUSTOMER_ID" value="{{ $website->GOOGLE_ADS_LOGIN_CUSTOMER_ID }}" />

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Update Project
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
