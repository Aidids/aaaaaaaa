<div class="d-print-none">
    <nav id="sidebar" class="sidebar sidebar-offcanvas d-print-none">

        <ul class="nav nav-scrolling">

            <li class="{{ Request::is('dashboard') ? 'nav-item active' : 'nav-item' }}">
                <a href="{{ route('dashboard.index') }}" class="nav-link">
                <span class="icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-pie-chart menu-icon">
                        <path d="M21.21 15.89A10 10 0 1 1 8 2.83">
                        </path>
                        <path d="M22 12A10 10 0 0 0 12 2v10z">
                        </path>
                    </svg>
                </span>
                    <span class="menu-title">Dashboard</span>
                </a>
            </li>
            <li class="{{ Request::is('profile') ? 'nav-item active' : 'nav-item' }}">
                <a href="{{ route('profile.index', ['user' => Auth::user()->id]) }}" class="nav-link">
                <span class="icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user menu-icon">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2">
                        </path>
                        <circle cx="12" cy="7" r="4">
                        </circle>
                    </svg>
                </span>
                    <span class="menu-title">Job Profile</span>
                </a>
            </li>
            @can('isAdmin', Auth::user())
                <li class="{{ Request::is('employee') ? 'nav-item active' : 'nav-item' }}" onclick="showemployee()">
                    <a href="javascript:void(0);" data-toggle="collapse" aria-expanded="false" aria-controls="employee" class="nav-link">
                <span class="icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users menu-icon">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2">
                        </path>
                        <circle cx="9" cy="7" r="4">
                        </circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87">
                        </path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75">
                        </path>
                    </svg>
                </span>
                        <span class="menu-title">Employee</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div id="employee" class="d-none collapse show">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item">
                                <a href="{{ route('employee.index') }}" class="nav-link">All Employees</a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endcan
            <li class="nav-item" onclick="showleave()">
                <a href="javascript:void(0);" data-toggle="collapse" aria-expanded="false" aria-controls="leave" class="nav-link">
                <span class="icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clock menu-icon">
                        <circle cx="12" cy="12" r="10">
                        </circle>
                        <polyline points="12 6 12 12 16 14">
                        </polyline>
                    </svg>
                </span>
                    <span class="menu-title">Leave</span>
                    <i class="menu-arrow"></i>
                </a>
                <div id="leave" class="d-none collapse show">
                    <ul class="nav flex-column sub-menu">
                        @can('isApprover', Auth::user())
                        <li class="nav-item">
                            <a href="{{ route('leave.approve', ['user' => Auth::user()->id] ) }}" class="nav-link">Approve Leave</a>
                        </li>
                            <li class="nav-item">
                            <a href="{{ route('leave.offshore-approve', ['user' => Auth::user()->id] ) }}" class="nav-link">Approve Offshore Leave</a>
                        </li>
                        @endcan
                        <li class="nav-item">
                            <a href="{{ route('leave.request', ['user' => Auth::user()->id]) }}" class="nav-link">Leave Request</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('leave.offshore-index', ['user' => Auth::user()->id]) }}" class="nav-link">Offshore leave request</a>
                        </li>
                        @can('isAdmin', Auth::user())
                            <li class="nav-item">
                                <a href="{{ route('leave.summary', ['user' => Auth::user()->id]) }}" class="nav-link">Leave Summary</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('leave.offshore-summary', ['user' => Auth::user()->id]) }}" class="nav-link">Offshore Request Summary</a>
                            </li>
                        @endcan
                        <li class="nav-item">
                            <a href="{{ route('administration.leave-type') }}" class="nav-link">Leave Description</a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item" onclick="showadministration()">
                <a href="javascript:void(0);" data-toggle="collapse" aria-expanded="false" aria-controls="administration" class="nav-link">
                <span class="icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-briefcase menu-icon">
                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2">
                        </rect>
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16">
                        </path>
                    </svg>
                </span>
                    <span class="menu-title">Administration</span>
                    <i class="menu-arrow"></i>
                </a>
                <div id="administration" class="d-none collapse show">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a href="{{ route('administration.approvers') }}" class="nav-link">Approvers</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('administration.updateAnnualLeave') }}" class="nav-link">Update Annual Leave</a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="{{ Request::is('profile') ? 'nav-item active' : 'nav-item' }}">
                <a href="{{ route('error.coming-soon') }}" class="nav-link">
                <span class="icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-text" viewBox="0 0 16 16">
                        <path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5z"/>
                        <path d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5L9.5 0zm0 1v2A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
                    </svg>
                </span>
                    <span class="menu-title">E-Form</span>
                </a>
            </li>
        </ul>
    </nav>
    <nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
        <div class="navbar-menu-wrapper d-flex align-items-stretch">
            <div class="d-flex justify-content-between w-100">
                <div class="d-flex justify-content-start">
                    <button type="button" id="navbarToggler" class="navbar-toggler navbarToggler align-self-center d-lg-none pl-0" onclick="toggleSidebar()">
                        <i class="bi bi-text-indent-right fa-lg"></i>
                        <span title="Collapse sidebar">
                        </span>
                    </button>
                    <button type="button" id="navbarTogglers" class="navbar-toggler navbarTogglers align-self-center d-lg-none pl-0" style="visibility: hidden;" onclick="toggleSidebar()">
                        <i class="bi bi-text-indent-left fa-lg"></i>
                        <span title="Floating sidebar">
                        </span>
                    </button>
                </div>
                <div>
                    <ul class="navbar-nav navbar-nav-right ml-auto">
                        <li class="nav-item">
                        </li>

                    {{-- Profile Navbar --}}

                    <li class="nav-item nav-profile dropdown">
                        <a href="javascript: void(0);" data-toggle="dropdown" button type="button" onclick="showprofileDropdown()" aria-expanded="true" class="d-flex align-items-center nav-link dropdown-toggle mr-0">
                            <div class="nav-profile-text">
                                <p class="mb-0 text-black text-right">
                                    {{ Auth::user()->name }}
                                <br>
                                <span class="text-secondary font-size-90 d-block mt-1">
                                     {{ Auth::user()->department->name ?? '-' }} | {{ Auth::user()->title }}
                                </span>
                                    <span class="text-secondary font-size-90 d-block mt-1">{{ date('l j F Y') }}</span>
                                </p>
                            </div>
                            <div class="avatars-w-40">
                                <img src="{{ asset('img/icons/profile.png') }}" alt="image" class="rounded-circle">
                            </div>
                        </a>
                        <div aria-labelledby="profileDropdown" class="d-none dropdown-menu dropdown-menu-right navbar-dropdown show" id="profileDropdown"  button type="button">
                            <div class="dropdown-item profile">
                                <div class="avatars-w-50">
                                    <img src="{{ asset('img/icons/profile.png') }}" alt="image" class="rounded-circle">
                                </div>
                                <div class="nav-profile-text font-size-default ml-2">
                                    <p class="my-0 text-black">{{ Auth::user()->name }}</p>
                                    <p class="text-truncate text-secondary font-size-90">
                                        {{ Auth::user()->department->name }} | {{ Auth::user()->title }}
                                    </p>
                                </div>
                            </div>
                            <div class="dropdown-divider">
                            </div>
                                <a href="/hq/login" class="dropdown-item">
                                    <i class="bi bi-door-open fa-lg mr-3"></i>
                                    Log out
                                </a>
                        </div>
                    </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
</div>



