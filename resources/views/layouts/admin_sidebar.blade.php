<aside id="sidebar-wrapper">
    <div class="sidebar-brand">
        <a href="{{ route('home') }}">
            <img alt="image" src="{{ asset('assets/img/logo.png') }}" class="header-logo" />
            <span class="logo-name">SEO Tool</span>
        </a>
    </div>

    <ul class="sidebar-menu">
        <li class="dropdown @if (in_array(Request::route()->getName(), ['dashboard', 'keywords.create', 'keywords.details'])) active @endif">
            <a href="#" class="menu-toggle nav-link has-dropdown">
                <i class="fa-brands fa-searchengin"></i><span>Keyword</span>
            </a>
            <ul class="dropdown-menu">
                <li @if (in_array(Request::route()->getName(), ['keywords.details'])) class="active" @endif><a href="{{ route('keywords.details') }}">Keyword Tracker</a></li>
                <li @if (in_array(Request::route()->getName(), ['dashboard'])) class="active" @endif><a id="keyword-tracker" href="{{ route('dashboard') }}">Keyword Management</a></li>
                <li @if (in_array(Route::current()->getName(), ['keywords.create'])) class="active" @endif><a href="{{ route('keywords.create') }}">Add Keyword</a></li>
            </ul>
        </li>
        {{-- <li class="dropdown {{ Route::is('backlinks.*') ? 'active' : '' }}">
            <a href="{{ route('backlinks.index') }}" class="nav-link">
                <i class="fa-solid fa-link"></i><span>Backlinks</span>
            </a>
        </li> --}}
        <li class="dropdown @if (in_array(Request::route()->getName(), ['backlinks.index','backlinks.status.list'])) active @endif">
            <a href="#" class="menu-toggle nav-link has-dropdown">
                <i class="fa-brands fa-searchengin"></i><span>Backlinks</span>
            </a>
            <ul class="dropdown-menu">
                <li @if (in_array(Request::route()->getName(), ['backlinks.index'])) class="active" @endif><a href="{{ route('backlinks.index') }}">Active Backlinks</a></li>
                @role('Admin|Super Admin')
                <li @if (in_array(Request::route()->getName(), ['backlinks.status.list'])) class="active" @endif><a href="{{ route('backlinks.status.list', ['approve_status' => 'pending']) }}">Pending Backlinks Approvals</a></li>
                @endrole
            </ul>
        </li>
    </ul>

    @can('User list')
    <ul class="sidebar-menu">
        <li class="dropdown @if (in_array(Request::route()->getName(), ['admin.users.index'])) active @endif">
            <a href="{{ route('admin.users.index') }}" class="nav-link">
                <i class="fa-regular fa-user"></i><span>Users</span>
            </a>
        </li>
    </ul>
    @endcan

    @can('Role list')
    <ul class="sidebar-menu">
        <li class="dropdown @if (in_array(Request::route()->getName(), ['admin.roles.index'])) active @endif">
            <a href="{{ route('admin.roles.index') }}" class="nav-link">
                <i class="fa-solid fa-people-roof"></i><span>Roles</span>
            </a>
        </li>
    </ul>
    @endcan

    <ul class="sidebar-menu">
    @canany(['Google API', 'Project list'])
        <li class="dropdown @if (in_array(Request::route()->getName(), ['admin.settings', 'admin.projects'])) active @endif">
            <a href="#" class="menu-toggle nav-link has-dropdown">
                <i class="fa-solid fa-gear"></i><span>Settings</span>
            </a>
            <ul class="dropdown-menu">
                @can('Google API')
                    <li @if (in_array(Request::route()->getName(), ['admin.settings'])) class="active" @endif>
                        <a href="{{ route('admin.settings') }}">Google API</a>
                    </li>
                @endcan
                @can('Project list')
                    <li @if (in_array(Request::route()->getName(), ['admin.projects'])) class="active" @endif>
                        <a href="{{ route('admin.projects') }}">Manage Project</a>
                    </li>
                @endcan
            </ul>
        </li>
    @endcanany
</ul>

</aside>
