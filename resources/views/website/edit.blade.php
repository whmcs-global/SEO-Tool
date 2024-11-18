@extends('layouts.admin')

@section('title', 'Edit Project')
@section('content')
<style>
    .fieldset-gray {
        background-color: #f7f7f7;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 15px;
    }
    .fieldset-gray legend {
        font-weight: bold;
    }
</style>
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

                    <form method="POST" action="{{ route('admin.websites.update', $website->id) }}">
                        @csrf
                        @method('PUT')
                        <fieldset class="fieldset-gray">
                            <legend>Update Project</legend>
                        <x-form-field label="Name" name="name" value="{{ $website->name }}" required="true" />
                        <x-form-field label="Select property type" name="property_type" id="property_type" :options="['domain' => 'Domain', 'url_prefix' => 'URL prefix']" value="{{ $website->property_type }}" required="true" />
                        <div class="form-group row">
                            <label for="url" class="col-md-4 col-form-label text-md-right" id="url_label">Domain or URL</label>
                            <div class="col-md-6">
                                <input id="url" type="text" class="form-control @error('url') is-invalid @enderror" name="url" value="{{ $website->url }}" required>
                                @error('url')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        </fieldset>
                        <fieldset class="fieldset-gray">
                            <legend>Google Search Console Settings</legend>
                        <x-form-field label="Google Client ID" name="GOOGLE_ANALYTICS_CLIENT_ID" value="{{ $website->GOOGLE_ANALYTICS_CLIENT_ID }}" />
                        <x-form-field label="Google Client Secret" name="GOOGLE_ANALYTICS_CLIENT_SECRET" value="{{ $website->GOOGLE_ANALYTICS_CLIENT_SECRET }}" />
                        <x-form-field label="Google Redirect URI" name="GOOGLE_ANALYTICS_REDIRECT_URI" type="text" value="{{ $website->GOOGLE_ANALYTICS_REDIRECT_URI }}" />
                        <x-form-field label="API Key" name="API_KEY" value="{{ $website->API_KEY }}" />
                        </fieldset>
                        <fieldset class="fieldset-gray">
                            <legend>Google Ads Settings</legend>
                        <x-form-field label="Google Ads Developer Token" name="GOOGLE_ADS_DEVELOPER_TOKEN" value="{{ $website->GOOGLE_ADS_DEVELOPER_TOKEN }}" />
                        <x-form-field label="Google Client ID" name="GOOGLE_ADS_CLIENT_ID" value="{{ $website->GOOGLE_ADS_CLIENT_ID }}" />
                        <x-form-field label="Google Client Secret" name="GOOGLE_ADS_CLIENT_SECRET" value="{{ $website->GOOGLE_ADS_CLIENT_SECRET }}" />
                        <x-form-field label="Google Redirect URI" name="GOOGLE_ADS_REDIRECT_URI" type="text" value="{{ $website->GOOGLE_ADS_REDIRECT_URI }}" />
                        <x-form-field label="Google Key" name="GOOGLE_ADS_KEY" value="{{ $website->GOOGLE_ADS_KEY }}" />
                        <x-form-field label="Google Login Customer ID" name="GOOGLE_ADS_LOGIN_CUSTOMER_ID" value="{{ $website->GOOGLE_ADS_LOGIN_CUSTOMER_ID }}" />
                        </fieldset>
                        <fieldset class="fieldset-gray">
                            <legend>Google Analytics</legend>

                            <div class="form-group">
                                <x-form-field label="Property ID" name="property_id" value="{{ $website->property_id }}" />
                            </div>

                            <h6 class="info-text">
                                Add this Email into your Google Analytics in Account access management with Viewer Permission:
                                <a href="#">seotool@dev-hosting-seekers.iam.gserviceaccount.com</a>
                            </h3>
                        </fieldset>
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Update Project
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const propertyType = document.getElementById('property_type');
    const urlLabel = document.getElementById('url_label');

    function updateUrlLabel() {
        if (propertyType.value === 'domain') {
            urlLabel.textContent = 'Domain';
        } else if (propertyType.value === 'url_prefix') {
            urlLabel.textContent = 'URL';
        }
    }
    updateUrlLabel();
    propertyType.addEventListener('change', updateUrlLabel);
});
</script>
@endsection
