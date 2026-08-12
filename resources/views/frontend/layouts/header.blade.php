<!-- Header -->
<div class="header header-one">
    <a href="{{ route('index') }}"
        class="d-inline-flex d-sm-inline-flex align-items-center d-md-inline-flex d-lg-none align-items-center device-logo">
        <img style="width:70px" src="{{ asset('assets') }}/img/logo.jpg" class="img-fluid logo2" alt="Logo">
    </a>
    <div class="main-logo d-inline float-start d-lg-flex align-items-center d-none d-sm-none d-md-none">
        <div class="logo-white">
            <a href="{{ route('index') }}">
                <img style="width:70px" src="{{ asset('assets') }}/img/logo.jpg" class="img-fluid logo-blue"
                    alt="Logo">
            </a>
            <a href="{{ route('index') }}">
                <img style="width:70px" src="{{ asset('assets') }}/img/logo.jpg" class="img-fluid logo-small"
                    alt="Logo">
            </a>
        </div>
        <div class="logo-color">
            <a href="{{ route('index') }}">
                <img style="width:70px" src="{{ asset('assets') }}/img/logo.jpg" class="img-fluid logo-blue"
                    alt="Logo">
            </a>
            <a href="{{ route('index') }}">
                <img style="width:70px" src="{{ asset('assets') }}/img/logo.jpg" class="img-fluid logo-small"
                    alt="Logo">
            </a>
        </div>
    </div>
    <!-- Sidebar Toggle -->
    <a href="javascript:void(0);" id="toggle_btn">
        <span class="toggle-bars">
            <span class="bar-icons"></span>
            <span class="bar-icons"></span>
            <span class="bar-icons"></span>
            <span class="bar-icons"></span>
        </span>
    </a>
    <!-- /Sidebar Toggle -->

    <!-- Mobile Menu Toggle -->
    <a class="mobile_btn" id="mobile_btn">
        <i class="fas fa-bars"></i>
    </a>
    <!-- /Mobile Menu Toggle -->

    <!-- Search -->
    <div class="top-nav-search d-none">
        <form>
            <input type="text" class="form-control" placeholder="Search here">
            <button class="btn" type="submit"><img src="{{ asset('assets') }}/img/icons/search.svg"
                    alt="img"></button>
        </form>
    </div>
    <!-- /Search -->

    <!-- Header Menu -->
    <ul class="nav nav-tabs user-menu">
        <li class="nav-item has-arrow dropdown-heads">
            <a href="javascript:void(0);" class="win-maximize">
                <i class="fe fe-maximize"></i>
            </a>
        </li>
        <!-- User Menu -->
        <li class="nav-item dropdown">
            <a href="javascript:void(0)" class="user-link nav-link" data-bs-toggle="dropdown">
                <span class="user-img">
                    <img src="{{ auth()->check() && auth()->user()->images ? asset('frontend/users/' . auth()->user()->images) : asset('assets/img/favicon.png') }}"
                        onerror="this.src='{{ asset('assets/img/favicon.png') }}'" alt=""
                        class="profilesidebar">
                    <span class="animate-circle"></span>
                </span>
                <span class="user-content">
                    <span class="user-name">{{ auth()->check() ? auth()->user()->name : 'Guest' }}</span>
                </span>
            </a>
            <div class="dropdown-menu menu-drop-user">
                <div class="profilemenu">
                    <div class="subscription-logout">
                        <ul>
                            <li class="pb-0">
                                @auth
                                    <a onclick="document.getElementById('logoutForm').submit()" class="dropdown-item"
                                        href="javascript:void(0)">Log Out</a>
                                    <form id="logoutForm" action="{{ route('logout') }}" method="post">
                                        @csrf
                                    </form>
                                @else
                                    <a class="dropdown-item" href="{{ route('login') }}">Log In</a>
                                @endauth
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </li>
        <!-- /User Menu -->
    </ul>
    <!-- /Header Menu -->
</div>
<!-- /Header -->
