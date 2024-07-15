@extends('layouts.admin')

@section('title', 'Add New Project')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('admin.websites.store') }}">
                        @csrf
                        <fieldset class="fieldset-gray">
                            <legend>Add New Project</legend>
                            <x-form-field label="Project Name" name="name" value="{{ old('name') }}" required="true" />
                            <x-form-field label="Select property type" name="property_type" id="property_type" :options="['domain' => 'Domain', 'url_prefix' => 'URL prefix']" required="true" />
                            <div class="form-group row">
                                <label for="url" class="col-md-4 col-form-label text-md-right" id="url_label">Domain</label>
                                <div class="col-md-6">
                                    <input id="url" type="text" class="form-control @error('url') is-invalid @enderror" name="url" value="{{ old('url') }}" required>
                                    @error('url')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="help-dropdown">
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="helpDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Help
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="helpDropdown">
                                        <a class="dropdown-item" target="blank" href="https://support.google.com/webmasters/answer/34592?hl=en">What is Property type ?</a>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="fieldset-gray">
                            <legend>Google Analytics Settings</legend>
                            <x-form-field label="Google Client ID" name="GOOGLE_ANALYTICS_CLIENT_ID" value="{{ old('GOOGLE_ANALYTICS_CLIENT_ID') }}" />
                            <x-form-field label="Google Client Secret" name="GOOGLE_ANALYTICS_CLIENT_SECRET" value="{{ old('GOOGLE_ANALYTICS_CLIENT_SECRET') }}" />
                            <x-form-field label="Google Redirect URI" name="GOOGLE_ANALYTICS_REDIRECT_URI" type="text" value="{{ old('GOOGLE_ANALYTICS_REDIRECT_URI') }}" />
                            <x-form-field label="API Key" name="API_KEY" value="{{ old('API_KEY') }}" />
                            <div class="help-dropdown">
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="helpDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Help
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="helpDropdown">
                                        <a class="dropdown-item" target="blank" href="https://developers.google.com/workspace/marketplace/configure-oauth-consent-screen" target="_blank">Configure OAuth Consent</a>
                                        <a class="dropdown-item" target="blank" href="https://support.google.com/googleapi/answer/6158862?hl=en">Get API keys</a>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="fieldset-gray">
                            <legend>Google Ads Settings</legend>
                            <x-form-field label="Google Ads Developer Token" name="GOOGLE_ADS_DEVELOPER_TOKEN" value="{{ old('GOOGLE_ADS_DEVELOPER_TOKEN') }}" />
                            <x-form-field label="Google Client ID" name="GOOGLE_ADS_CLIENT_ID" value="{{ old('GOOGLE_ADS_CLIENT_ID') }}" />
                            <x-form-field label="Google Client Secret" name="GOOGLE_ADS_CLIENT_SECRET" value="{{ old('GOOGLE_ADS_CLIENT_SECRET') }}" />
                            <x-form-field label="Google Redirect URI" name="GOOGLE_ADS_REDIRECT_URI" type="text" value="{{ old('GOOGLE_ADS_REDIRECT_URI') }}" />
                            <x-form-field label="Google Key" name="GOOGLE_ADS_KEY" value="{{ old('GOOGLE_ADS_KEY') }}" />
                            <x-form-field label="Google Login Customer ID" name="GOOGLE_ADS_LOGIN_CUSTOMER_ID" value="{{ old('GOOGLE_ADS_LOGIN_CUSTOMER_ID') }}" />
                            <div class="help-dropdown">
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="helpDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Help
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="helpDropdown">
                                        <a class="dropdown-item" target="blank" href="https://developers.google.com/workspace/marketplace/configure-oauth-consent-screen" target="_blank">Configure OAuth Consent</a>
                                        <a class="dropdown-item" target="blank" href="https://developers.google.com/google-ads/api/docs/first-call/dev-token" target="_blank">Google Ads Developer Token</a>
                                        <a class="dropdown-item" target="blank" href="https://support.google.com/google-ads/answer/1704344?hl=en">Get Customer ID</a>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Add Project
                                </button>
                                <a href="{{ url()->previous() }}" class="btn btn-black">
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const propertyType = document.getElementById('property_type');
        const urlLabel = document.getElementById('url_label');

        propertyType.addEventListener('change', function () {
            if (this.value === 'domain') {
                urlLabel.textContent = 'Domain';
            } else if (this.value === 'url_prefix') {
                urlLabel.textContent = 'URL';
            }
        });
    });
</script>
@endpush
@push('styles')
<style>
    .fieldset-gray {
        background-color: #f7f7f7;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 15px;
        position: relative;
    }
    .fieldset-gray legend {
        font-weight: bold;
    }
    .help-dropdown {
        position: relative;
        margin-left: 85%;
    }
</style>
@endpush
