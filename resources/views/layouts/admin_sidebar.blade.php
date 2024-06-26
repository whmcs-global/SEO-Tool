<aside id="sidebar-wrapper">
    <div class="sidebar-brand">
        <a href="{{ route('dashboard') }}"> <img alt="image" src="{{ asset('assets/img/logo.png') }}"
                class="header-logo" /> <span class="logo-name">SEO Tool</span>
        </a>
    </div>
    <ul class="sidebar-menu">
        <li class="dropdown @if (in_array(Request::route()->getName(), ['dashboard'])) active @endif">
            <a href="{{ route('dashboard') }}" class="nav-link"><i class="fa-brands fa-searchengin"></i></i><span>Keywords</span></a>
        </li>
    </ul>
    @role('admin')
        <ul class="sidebar-menu">
            <li class="dropdown @if (in_array(Request::route()->getName(), ['admin.roles.index'])) active @endif">
                <a href="{{ route('admin.roles.index') }}" class="nav-link"><i class="fa-solid fa-people-roof"></i><span>Roles</span></a>
            </li>
        </ul>
        <ul class="sidebar-menu">
            <li class="dropdown @if (in_array(Request::route()->getName(), ['admin.permissions.index'])) active @endif">
                <a href="{{ route('admin.permissions.index') }}" class="nav-link"><i
                        class="fa-solid fa-check"></i><span>Permissions</span></a>
            </li>
        </ul>
        <ul class="sidebar-menu">
            <li class="dropdown @if (in_array(Request::route()->getName(), ['admin.users.index'])) active @endif">
                <a href="{{ route('admin.users.index') }}" class="nav-link"><i
                        class="fa-regular fa-user"></i><span>Users</span></a>
            </li>
        </ul>
        <ul class="sidebar-menu">
            <li class="dropdown @if (in_array(Request::route()->getName(), ['admin.settings'])) active @endif">
                <a href="{{ route('admin.settings') }}" class="nav-link"><i class="fa-solid fa-gear"></i><span>Settings</span></a>
            </li>
        </ul>
    @endrole
</aside>
