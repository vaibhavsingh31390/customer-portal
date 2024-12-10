<!-- Top Navbar -->
<nav class="navbar navbar-expand fixed-top top-menu">
    <a class="navbar-brand" href="{{ route('dashboard') }}">
        <!-- Large logo -->
        <img src="{{ asset('assets/img/mawai.png') }}" alt="Logo" class="large-logo">

        <!-- Small logo -->
        <img src="{{ asset('assets/img/mawai.png') }}" alt="Small Logo" class="small-logo">
    </a>

    <!-- Burger menu -->
    <div class="burger-menu toggle-menu">
        <span class="top-bar"></span>
        <span class="middle-bar"></span>
        <span class="bottom-bar"></span>
    </div>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">

        <!-- Search form -->
        <div class="nav-search-form d-none d-lg-block" action="#">
            <img src="{{ asset('assets/img/mawai_support.jpg') }}" alt="Logo" class="large-logo">
        </div>

        <!-- Right nav -->
        <ul class="navbar-nav right-nav ml-auto">
            <!-- Profile dropdown -->
            <li class="nav-item dropdown profile-nav-item">
                <div class="btn">
                    <span class="name font-weight-bold">{{ Session::get('name') }}</span>
                </div>

            </li>
            <li class="nav-item dropdown profile-nav-item">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="dropdown-item" type="submit">
                        <i data-feather="log-out" class="icon"></i>
                        Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>
</nav>
<!-- End Top Navbar -->
