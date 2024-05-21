@extends('layouts.admin')

@section('content')
<div class="container">
    <section class="section">
        <div class="flash-message">
            @if(isset($errorMessage))
                <div class="alert alert-danger alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    {{ $errorMessage }}
                </div>
            @endif
        </div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Admin Settings</div>

                <div class="card-body">

                        <div class="form-group row">
                            <label for="client_id" class="col-md-4 col-form-label text-md-right">Client ID</label>

                            <div class="col-md-6">
                                <input id="client_id" type="text" class="form-control" name="client_id" value="{{ old('client_id', $adminSetting->client_id ?? '') }}" required autocomplete="client_id" autofocus disabled>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="client_secret_id" class="col-md-4 col-form-label text-md-right">Client Secret ID</label>

                            <div class="col-md-6">
                                <input id="client_secret_id" type="text" class="form-control" name="client_secret_id" value="{{ old('client_secret_id', $adminSetting->client_secret_id ?? '') }}" required autocomplete="client_secret_id" disabled>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="redirect_url" class="col-md-4 col-form-label text-md-right">Redirect URL</label>

                            <div class="col-md-6">
                                <input id="redirect_url" type="text" class="form-control" name="redirect_url" value="{{ old('redirect_url', $adminSetting->redirect_url ?? '') }}" required autocomplete="redirect_url" disabled>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="refresh_token" class="col-md-4 col-form-label text-md-right">Refresh Token</label>

                            <div class="col-md-6">
                                <input id="refresh_token" type="text" class="form-control" name="refresh_token" value="{{ old('refresh_token', $adminSetting->refresh_token ?? '') }}" required autocomplete="refresh_token" disabled>
                            </div>
                        </div>

                        <div class="mb-0 form-group row">
                            <div class="col-md-6 offset-md-4">
                                @if ($adminSetting)
                                <button id="disconnect_google" class="btn btn-danger">
                                    Disconnect
                                </button>
                                @else
                                <a href="{{ route('googleConnect')}}" class="btn btn-primary" >Link Google Account</a>
                                @endif
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$("#disconnect_google").click(function () {
    swal({
        title: 'Are you sure?',
        text: 'once you disconnect, certain features will no longer be available for use. Proceed with caution.',
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    })
    .then((willDelete) => {
        if (willDelete) {
            @if ($adminSetting)
            window.location.href = "{{ route('googleStatus', [ 'adminSetting'  => $adminSetting]) }}";
            @endif
        }
    });
});
</script>
@endpush
