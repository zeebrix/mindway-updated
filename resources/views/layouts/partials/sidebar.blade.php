<aside class="left-sidebar">
    <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
            <a href="{{ route('home') }}" class="text-nowrap logo-img">
                <img src="{{ asset('/logo/logo.png') }}" width="180" alt="Mindway Logo" />
            </a>
            <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                <i class="ti ti-x fs-8"></i>
            </div>
        </div>

        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
            <ul id="sidebarnav">
                @can('access-admin-panel')
                <li class="nav-small-cap"><span class="hide-menu">Home</span></li>
                <!-- <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.dashboard') }}">
                        <span><i class="ti ti-layout-dashboard"></i></span>
                        <span class="hide-menu">Dashboard</span>
                    </a>
                </li> -->
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.users.index') }}">
                        <span><i class="ti ti-user-plus"></i></span>
                        <span class="hide-menu">Users</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.csm.index') }}">
                        <span><i class="ti ti-user-plus"></i></span>
                        <span class="hide-menu">Customer Success Manager</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{route('admin.programs.index')}}">
                        <span><i class="ti ti-article"></i></span>
                        <span class="hide-menu">Programs</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{route('admin.counselors.index')}}">
                        <span><i class="ti ti-article"></i></span>
                        <span class="hide-menu">Session</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{route('admin.request-sessions.index')}}">
                        <span><i class="ti ti-article"></i></span>
                        <span class="hide-menu">Requested Session</span>
                    </a>
                </li>
                <li class="nav-small-cap"><span class="hide-menu">Course</span></li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{route('admin.courses.index')}}">
                        <span><i class="ti ti-article"></i></span>
                        <span class="hide-menu">All Courses</span>
                    </a>
                </li>
                <!-- <li class="sidebar-item">
                    <a class="sidebar-link" href="{{route('admin.courses-audio.index')}}">
                        <span><i class="ti ti-article"></i></span>
                        <span class="hide-menu">Course Audio</span>
                    </a>
                </li> -->
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{route('admin.sos-audios.index')}}">
                        <span><i class="ti ti-article"></i></span>
                        <span class="hide-menu">Sos Audio</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{route('admin.categories.index')}}">
                        <span><i class="ti ti-article"></i></span>
                        <span class="hide-menu">Course Categories</span>
                    </a>
                </li>
                <li class="nav-small-cap"><span class="hide-menu">Sleep Courses</span></li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{route('admin.category-courses.index')}}">
                        <span><i class="ti ti-article"></i></span>
                        <span class="hide-menu">Sleep Course</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{route('admin.sleep-audios.index')}}">
                        <span><i class="ti ti-article"></i></span>
                        <span class="hide-menu">Sleep Audio</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{route('admin.links.index')}}">
                        <span><i class="ti ti-article"></i></span>
                        <span class="hide-menu">Account Links</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{route('admin.feelings.index')}}">
                        <span><i class="ti ti-article"></i></span>
                        <span class="hide-menu">Emojis</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{route('admin.music.index')}}">
                        <span><i class="ti ti-article"></i></span>
                        <span class="hide-menu">Music</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{route('admin.sleep-screens.index')}}">
                        <span><i class="ti ti-article"></i></span>
                        <span class="hide-menu">Sleep Screen</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{route('admin.home-emojis.index')}}">
                        <span><i class="ti ti-article"></i></span>
                        <span class="hide-menu">Home Emojis</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{route('admin.single-courses.index')}}">
                        <span><i class="ti ti-article"></i></span>
                        <span class="hide-menu">Single Course</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{route('admin.quotes.index')}}">
                        <span><i class="ti ti-article"></i></span>
                        <span class="hide-menu">Quote</span>
                    </a>
                </li>
                @endcan


                @can('access-counsellor-panel')
                <li class="nav-small-cap"><span class="hide-menu">Counsellor Menu</span></li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs('counsellor.dashboard') ? 'active' : '' }}" href="{{ route('counsellor.dashboard') }}">
                        <span><i class="ti ti-layout-dashboard"></i></span>
                        <span class="hide-menu">Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs('counsellor.clients.*') ? 'active' : '' }}" href="{{ route('counsellor.clients.index') }}">
                        <span><i class="ti ti-calendar-event"></i></span>
                        <span class="hide-menu">Clients</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs('counsellor.sessions.*') ? 'active' : '' }}" href="{{ route('counsellor.sessions.history') }}">
                        <span><i class="ti ti-calendar-event"></i></span>
                        <span class="hide-menu">History</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs('counsellor.availability.*') ? 'active' : '' }}" href="{{ route('counsellor.availability.index') }}">
                        <span><i class="ti ti-calendar-event"></i></span>
                        <span class="hide-menu">Availability</span>
                    </a>
                </li>
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">Other<svg width="112" height="2" viewBox="0 0 112 2" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0.5 1.5L111.5 0.5" stroke="#D7D7D7" stroke-linecap="round" />
                        </svg>
                    </span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" target="_blank" href="https://mindwayeap.notion.site" aria-expanded="false">
                        <span>
                            <i class="ti ti-settings"></i>
                        </span>
                        <span class="hide-menu">Policies & Procedures </span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs('counsellor.profile.*') ? 'active' : '' }}" href="{{ route('counsellor.profile') }}">
                        <span><i class="ti ti-calendar-event"></i></span>
                        <span class="hide-menu">Profile</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs('counsellor.settings.*') ? 'active' : '' }}" href="{{route('counsellor.settings.index')}}">
                        <span><i class="ti ti-users"></i></span>
                        <span class="hide-menu">Settings</span>
                    </a>
                </li>
                @endcan


                @can('access-program-panel')
                <li class="nav-small-cap"><span class="hide-menu">Manage</span></li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs('program.dashboard') ? 'active' : '' }}" href="{{ route('program.dashboard') }}">
                        <span><i class="ti ti-layout-dashboard"></i></span>
                        <span class="hide-menu">Home</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs('program.analytics.*') ? 'active' : '' }}" href="{{route('program.analytics.index')}}">
                        <span><i class="ti ti-article"></i></span>
                        <span class="hide-menu">Analytics</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs('program.employees.*') ? 'active' : '' }}" href="{{route('program.employees.index')}}">
                        <span><i class="ti ti-article"></i></span>
                        <span class="hide-menu">Employees</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs('program.requests.*') ? 'active' : '' }}" href="{{route('program.requests.index')}}">
                        <span><i class="ti ti-article"></i></span>
                        <span class="hide-menu">Requests</span>
                    </a>
                </li>
                <li class="nav-small-cap"><span class="hide-menu">Others</span></li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs('program.app_setup.*') ? 'active' : '' }}" href="https://mindwayeap.com.au/booking" target="_blank">
                        <span><i class="ti ti-users"></i></span>
                        <span class="hide-menu">App Setup Guide</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs('program.settings.*') ? 'active' : '' }}" href="{{route('program.settings.index')}}">
                        <span><i class="ti ti-users"></i></span>
                        <span class="hide-menu">Settings</span>
                    </a>
                </li>

                @endcan
            </ul>
        </nav>
    </div>
</aside>