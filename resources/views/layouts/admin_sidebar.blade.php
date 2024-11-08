<aside id="sidebar-wrapper">
    <div class="sidebar-brand">
        <a href="{{ route('home') }}">
            <img alt="SEO Tool Logo" src="{{ asset('assets/img/logo.png') }}" class="header-logo" />
            <span class="logo-name">SEO Tool</span>
        </a>
    </div>

    <ul class="sidebar-menu">
        <li class="{{ Request::route()->getName() == 'new.dashboard' ? 'active' : '' }}">
            <a href="{{ route('new.dashboard') }}" class="nav-link toggled"><svg xmlns="http://www.w3.org/2000/svg"
                    width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-monitor">
                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                    <line x1="8" y1="21" x2="16" y2="21"></line>
                    <line x1="12" y1="17" x2="12" y2="21"></line>
                </svg><span>Dashboard</span></a>
        </li>
        <li
            class="dropdown {{ in_array(Request::route()->getName(), ['dashboard', 'keywords.create', 'keywords.details']) ? 'active' : '' }}">
            <a href="#" class="menu-toggle nav-link has-dropdown">
                <i class="fa-brands fa-searchengin"></i><span>Keyword</span>
            </a>
            <ul class="dropdown-menu">
                <li class="{{ Request::route()->getName() == 'keywords.details' ? 'active' : '' }}">
                    <a href="{{ route('keywords.details') }}">Keyword Tracker</a>
                </li>
                <li class="{{ Request::route()->getName() == 'dashboard' ? 'active' : '' }}">
                    <a id="keyword-tracker" href="{{ route('dashboard') }}">Keyword Management</a>
                </li>
                <li class="{{ Request::route()->getName() == 'keywords.create' ? 'active' : '' }}">
                    <a href="{{ route('keywords.create') }}">Add Keyword</a>
                </li>
            </ul>
        </li>
        <li class="dropdown {{ Route::is('backlinks.*') ? 'active' : '' }} ">
            <a href="{{ route('backlinks.index') }}" class="nav-link">
                <i class="fa-solid fa-link"></i><span>Backlinks</span>
            </a>
        </li>
        <li class="dropdown {{ Route::is('page.*') ? 'active' : '' }} ">
            <a href="{{ route('page.list') }}" class="nav-link">
                <i class="fa fa-file"></i><span>Track Pages</span>
            </a>
        </li>
        @can('User list')
            <li class="dropdown {{ Request::route()->getName() == 'admin.users.index' ? 'active' : '' }}">
                <a href="{{ route('admin.users.index') }}" class="nav-link">
                    <i class="fa-regular fa-user"></i><span>Users</span>
                </a>
            </li>
        @endcan

        @can('Role list')
            <li class="dropdown {{ Request::route()->getName() == 'admin.roles.index' ? 'active' : '' }}">
                <a href="{{ route('admin.roles.index') }}" class="nav-link">
                    <i class="fa-solid fa-people-roof"></i><span>Roles</span>
                </a>
            </li>
        @endcan

        @canany(['Google API'])
            <li
                class="dropdown {{ in_array(Request::route()->getName(), ['admin.settings', 'admin.cron.status']) ? 'active' : '' }}">
                <a href="#" class="menu-toggle nav-link has-dropdown">
                    <i class="fa-solid fa-gear"></i><span>Settings</span>
                </a>
                <ul class="dropdown-menu">
                    @can('Google API')
                        <li class="{{ Request::route()->getName() == 'admin.settings' ? 'active' : '' }}">
                            <a href="{{ route('admin.settings') }}">Google API</a>
                        </li>
                    @endcan
                    <li class="{{ Request::route()->getName() == 'admin.cron.status' ? 'active' : '' }}">
                        <a href="{{ route('admin.cron.status') }}">Cron Status</a>
                    </li>
                </ul>
            </li>
        @endcanany
        @canany(['Project list', 'Add New Project'])
            <li
                class="dropdown {{ in_array(Request::route()->getName(), ['admin.projects', 'admin.websites.create']) ? 'active' : '' }}">
                <a href="#" class="menu-toggle nav-link has-dropdown">
                    <i class="fa-solid fa-globe"></i><span>Manage Projects</span>
                </a>
                <ul class="dropdown-menu">
                    @can('Project list')
                        <li class="{{ Request::route()->getName() == 'admin.projects' ? 'active' : '' }}">
                            <a href="{{ route('admin.projects') }}">Manage Project</a>
                        </li>
                    @endcan
                    @can('Add New Project')
                        <li class="{{ Request::route()->getName() == 'admin.websites.create' ? 'active' : '' }}">
                            <a href="{{ route('admin.websites.create') }}">Create New Project</a>
                        </li>
                    @endcan
                </ul>
            </li>

        @endcanany
    </ul>
</aside>
