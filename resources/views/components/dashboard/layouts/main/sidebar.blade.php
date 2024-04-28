<div class="leftbar-tab-menu">
    <div class="main-icon-menu">
        <a href="{{ route(name: 'dashboard.home') }}" class="logo logo-metrica d-block text-center">
            <span>
                <img src="{{ asset(path: 'assets/dashboard/images/logo-sm.png') }}" alt="logo-small" class="logo-sm">
            </span>
        </a>
        <div class="main-icon-menu-body">
            <div class="position-reletive h-100" data-simplebar style="overflow-x: hidden;">
                <ul class="nav nav-tabs" role="tablist" id="tab-menu">
                    <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" title="{{ __(key: 'dashboard.common.dashboard') }}" data-bs-trigger="hover">
                        <a href="#dashboard" id="dashboard-tab" class="nav-link active">
                            <i class="ti ti-smart-home menu-icon"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="main-menu-inner">
        <div class="topbar-left">
            <a href="{{ route(name: 'dashboard.home') }}" class="logo">
            <span>
                <img src="{{ asset(path: 'assets/dashboard/images/logo-dark.png') }}" alt="logo-large" class="logo-lg logo-dark">
                <img src="{{ asset(path: 'assets/dashboard/images/logo.png') }}" alt="logo-large" class="logo-lg logo-light">
            </span>
            </a>
        </div>
        <div class="menu-body navbar-vertical tab-content" data-simplebar>
            <div id="dashboard" class="main-icon-menu-pane tab-pane active show" role="tabpanel" aria-labelledby="dasboard-tab">
                <div class="title-box">
                    <h6 class="menu-title">@lang('dashboard.common.dashboard')</h6>
                </div>
                <ul class="nav flex-column">
                    <x-dashboard.layouts.main.navigation/>
                </ul>
            </div>
        </div>
    </div>
</div>
