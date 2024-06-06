<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') | {{ config('app.name', 'App') }}</title>
    <link rel="stylesheet" href="{{ asset('assets/css/app.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/superadmin.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.ico') }}" type="image/x-icon" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js" defer></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @stack('styles')
    <style>
    .aws-region-select {
        margin-right: 20px; /* Adjust the value as necessary */
        height: 38px; /* Ensure it matches the height of other navbar elements */
    }
    </style>
</head>

<body>
    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            <div class="navbar-bg"></div>
            <nav class="sticky navbar navbar-expand-lg main-navbar">
                <div class="mr-auto form-inline">
                    <ul class="mr-3 navbar-nav">
                        <li style="margin-right: 10px;">
                            <a href="#" data-toggle="sidebar" class="nav-link nav-link-lg collapse-btn">
                                <i data-feather="align-justify"></i>
                            </a>
                        </li>
                    </ul>
                </div>
                <ul class="navbar-nav navbar-right">
                    <li style="margin-right: 10px; margin-top: 10px;">
                        <label for="">Project</label>
                    </li>
                        <li class="nav-item dropdown" style="margin-right: 10px;">
                            <select class="form-control aws-region-select" id="aws-region-select">
                                <option data-href="{{ route('websites.default')}}">HostingSeekers</option>
                                @foreach ($websites as $website)
                                    <option data-href="{{ route('websites.set', $website) }}" @if(auth()->user()->website_id == $website->id) selected @endif>
                                        {{ $website->name }}
                                    </option>
                                @endforeach
                                <!-- <option data-href="{{ route('admin.websites.create') }}">Add New Project</option> -->
                            </select>
                        </li>
                        <li style="margin-right: 10px;">
                            <a href="{{ route('admin.websites.create') }}" class="btn btn-primary">Add New Project</a>
                        </li>
                        <li class="dropdown" style="margin-right: 10px;">
                            <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                                <img alt="image" src="{{ isset(auth()->user()->userDetails->profile_pic_path) ? asset('storage/'.auth()->user()->userDetails->profile_pic_path) : asset('assets/img/user.png') }}" class="user-img-radious-style">
                                <span class="d-sm-none d-lg-inline-block">{{ auth()->user()->name }}</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right pullDown">
                                <div class="dropdown-title">{{ auth()->user()->name }}</div>
                                <a href="{{ route('profile.edit') }}" class="dropdown-item has-icon"> <i class="far fa-user"></i> Profile</a>
                                <div class="dropdown-divider"></div>
                                <form id="frm-logout" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                                <a href="{{ route('logout') }}" class="dropdown-item has-icon text-danger" onclick="event.preventDefault();document.getElementById('frm-logout').submit();"> <i class="fas fa-sign-out-alt"></i> Logout</a>
                            </div>
                        </li>
                    </ul>
            </nav>
            <div class="main-sidebar sidebar-style-2">
                @include('layouts.admin_sidebar')
            </div>
            <div class="main-content">
                @yield('content')
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/js/app.min.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.js') }}"></script>
    <script src="{{ asset('assets/js/iziToast.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-validation.min.js') }}"></script>
    <script src="{{ asset('assets/js/sweetalert.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
    @stack('scripts')
    <script>
        document.getElementById('aws-region-select').addEventListener('change', function() {
            var selectedRegion = this.options[this.selectedIndex].getAttribute('data-href');
            window.location = selectedRegion;
        });
    </script>
</body>
</html>
