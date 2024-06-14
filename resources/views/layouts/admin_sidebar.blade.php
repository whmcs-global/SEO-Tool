<aside id="sidebar-wrapper">
    <div class="sidebar-brand">
        <a href="{{ route('dashboard') }}"> <img alt="image" src="{{ asset('assets/img/logo.png') }}"
                class="header-logo" /> <span class="logo-name">SEO Tool</span>
        </a>
    </div>
    <!-- <ul class="sidebar-menu">
        <li class="dropdown @if (in_array(Request::route()->getName(), ['dashboard'])) active @endif">
            <a href="{{ route('dashboard') }}" class="nav-link"><i class="fa-brands fa-searchengin"></i></i><span>Keyword Tracker</span></a>
        </li>
    </ul> -->
    <ul class="sidebar-menu">
        <li class="dropdown @if (in_array(Request::route()->getName(), ['dashboard'])) active @endif">
                <a href="#" class="menu-toggle nav-link has-dropdown"><i
                class="fa-brands fa-searchengin"></i><span>Keyword</span></a>
                <ul class="dropdown-menu">
                    <li @if (in_array(Request::route()->getName(), ['dashboard'])) class="active" @endif><a id="keyword-tracker"
                            href="{{ route('dashboard') }}">Keyword Tracker</a></li>
                    <li @if (in_array(Route::current()->getName(), ['keywords.create'])) class="active" @endif><a
                            href="{{ route('keywords.create') }}">Add Keyword</a></li>
                </ul>
            </li>
            <li class="dropdown {{ Route::is('backlinks.*') ? 'active' : '' }} ">
                <a href="{{ route('backlinks.index') }}" class="nav-link">
                    <i class="fa-solid fa-link"></i><span>Backlinks</span>
                </a>
            </li>

    </ul>
    <!-- <ul class="sidebar-menu">
        <li class="dropdown @if (in_array(Request::route()->getName(), ['keywords.show'])) active @endif">
            <a href="{{ route('keywords.show') }}" class="nav-link"><i class="fa-brands fa-searchengin"></i></i><span>Keywords</span></a>
        </li>
    </ul> -->
    @can('User list')
    <ul class="sidebar-menu">
        <li class="dropdown @if (in_array(Request::route()->getName(), ['admin.users.index'])) active @endif">
            <a href="{{ route('admin.users.index') }}" class="nav-link"><i
                    class="fa-regular fa-user"></i><span>Users</span></a>
        </li>
    </ul>
    @endcan
        @can('Role list')
        <ul class="sidebar-menu">
            <li class="dropdown @if (in_array(Request::route()->getName(), ['admin.roles.index'])) active @endif">
                <a href="{{ route('admin.roles.index') }}" class="nav-link"><i class="fa-solid fa-people-roof"></i><span>Roles</span></a>
            </li>
        </ul>
        @endcan
        @can('Google API')
        <ul class="sidebar-menu">
            <li class="dropdown @if (in_array(Request::route()->getName(), ['admin.settings'])) active @endif">
                <a href="{{ route('admin.settings') }}" class="nav-link"><i class="fa-solid fa-gear"></i><span>Settings</span></a>
            </li>
        </ul>
        @endcan
</aside>
