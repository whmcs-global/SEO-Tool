<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') | {{ config('app.name', 'App') }}</title>

    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('assets/css/app.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/superadmin.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.ico') }}" type="image/x-icon">

    <!-- Stack styles -->
    @stack('styles')

    <style>
    .aws-region-select {
        margin-right: 20px;
        height: 30px;
    }
    .small-text {
        font-size: 0.8em;
        display: block;
        margin-top: 2px;
    }
    </style>
</head>
<body>
    <div class="loader"></div>
    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg main-navbar sticky-top">
                <a href="#" data-toggle="sidebar" class="nav-link nav-link-lg collapse-btn">
                    <i class="fas fa-align-justify"></i>
                </a>
                <div class="navbar-nav ml-auto">
                    <li class="nav-item" style="margin-right: 10px;">
                        <button id="refreshButton" data-href="{{ route('keywords.refresh') }}" class="btn btn-primary">
                            Fetch Remote Data
                        </button>
                        <span class="small-text font-weight-bold">Last Updated At: {{ $lastUpdated ?? 'N/A' }}</span>
                    </li>
                    <li class="nav-item">
                        <label for="aws-region-select" style="margin-top: 10px; margin-right: 10px;">Project</label>
                    </li>
                    <li class="nav-item" style="margin-right: 10px;">
                        <select class="form-control aws-region-select" id="aws-region-select">
                            <option data-href="{{ route('websites.default') }}">HostingSeekers</option>
                            @foreach ($websites as $website)
                                <option data-href="{{ route('websites.set', $website) }}" @if(auth()->user()->website_id == $website->id) selected @endif>
                                    {{ $website->name }}
                                </option>
                            @endforeach
                        </select>
                    </li>
                    @role('Super-admin|Admin')
                    <li class="nav-item">
                        <a href="{{ route('admin.websites.create') }}" class="btn btn-primary">Add New Project</a>
                    </li>
                    @endrole
                    <li class="nav-item dropdown">
                        <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                            <img alt="image" src="{{ isset(auth()->user()->userDetails->profile_pic_path) ? asset('storage/'.auth()->user()->userDetails->profile_pic_path) : asset('assets/img/user.png') }}" class="user-img-radious-style">
                            <span class="d-sm-none d-lg-inline-block">{{ auth()->user()->name }}</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="dropdown-title">{{ auth()->user()->name }}</div>
                            <a href="{{ route('profile.edit') }}" class="dropdown-item has-icon"><i class="far fa-user"></i> Profile</a>
                            <div class="dropdown-divider"></div>
                            <form id="frm-logout" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                            <a href="{{ route('logout') }}" class="dropdown-item has-icon text-danger" onclick="event.preventDefault();document.getElementById('frm-logout').submit();"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    </li>
                </div>
            </nav>
            <!-- Sidebar -->
            <div class="main-sidebar sidebar-style-2">
                @include('layouts.admin_sidebar')
            </div>
            <!-- Main Content -->
            <div class="main-content">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- JS Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js" defer></script>
    <script src="{{ asset('assets/js/app.min.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.js') }}"></script>
    <script src="{{ asset('assets/js/iziToast.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-validation.min.js') }}"></script>
    <script src="{{ asset('assets/js/sweetalert.min.js') }}"></script>
    <script src="{{ asset('assets/js/common.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>

    <!-- Stack scripts -->
    @stack('scripts')

    <script>
        $(document).ready(function() {
            $('#keyword-tracker').on('click', function() {
                $('.loader').show();
            });
            $(window).on("load", function () {
                $(".loader").fadeOut("slow");
            });

            $('#refreshButton').on('click', function() {
                var url = $(this).data('href');
                var button = $(this);

                $.ajax({
                    url: url,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    beforeSend: function() {
                        button.prop('disabled', true);
                        button.append(' <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
                    },
                    success: function(response) {
                        if (response.success) {
                            button.text('Data Refreshed'); 
                            button.css('background-color', 'green');
                            button.attr('title', 'Please reload the page to see the changes');
                            button.tooltip();
                        } else {
                            button.prop('disabled', false);
                            button.text('Refresh Data');
                            button.css('background-color', '');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        button.prop('disabled', false); 
                        button.text('Refresh Data');
                        button.css('background-color', 'red');
                    },
                    complete: function() {
                        button.find('.spinner-border').remove();
                    }
                });
            });

            document.getElementById('aws-region-select').addEventListener('change', function() {
                var selectedRegion = this.options[this.selectedIndex].getAttribute('data-href');
                window.location = selectedRegion;
            });
        });
    </script>
</body>
</html>
