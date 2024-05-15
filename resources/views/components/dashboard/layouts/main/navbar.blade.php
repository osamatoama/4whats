<div class="topbar">
    <nav class="navbar-custom" id="navbar-custom">
        <ul class="list-unstyled topbar-nav float-end mb-0">
            <livewire:dashboard.store-switcher/>
            
            <li class="dropdown">
                <a class="nav-link dropdown-toggle nav-user" data-bs-toggle="dropdown" href="#" role="button"
                   aria-haspopup="false" aria-expanded="false">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset(path: 'assets/dashboard/images/users/user-4.jpg') }}" alt="profile-user" class="rounded-circle me-2 thumb-sm"/>
                        <div>
                            <small class="d-none d-md-block font-11">{{ auth()->user()->role->label() }}</small>
                            <span class="d-none d-md-block fw-semibold font-12">{{ auth()->user()->name }} <i class="mdi mdi-chevron-down"></i></span>
                        </div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    {{--<a class="dropdown-item" href="{{ url(path: 'profile') }}">
                        <i class="ti ti-user font-16 me-1 align-text-bottom"></i>
                        @lang('dashboard.navbar.profile')
                    </a>
                    <a class="dropdown-item" href="{{ url(path: 'settings') }}">
                        <i class="ti ti-settings font-16 me-1 align-text-bottom"></i>
                        @lang('dashboard.navbar.settings')
                    </a>
                    <div class="dropdown-divider mb-0"></div>--}}
                    <form action="{{ route(name: 'dashboard.logout') }}" method="POST">
                        @csrf
                        <button class="dropdown-item">
                            <i class="ti ti-power font-16 me-1 align-text-bottom"></i>
                            @lang('dashboard.navbar.logout')
                        </button>
                    </form>
                </div>
            </li>
        </ul>

        <ul class="list-unstyled topbar-nav mb-0">
            <li>
                <button class="nav-link button-menu-mobile nav-icon" id="togglemenu">
                    <i class="ti ti-menu-2"></i>
                </button>
            </li>
        </ul>
    </nav>
</div>
