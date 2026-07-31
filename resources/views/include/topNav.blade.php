<!-- Top Navbar -->
<nav class="navbar navbar-expand fixed-top top-menu portal-topnav">
    <div class="portal-topnav__start">
        <button type="button" id="sidebar-toggle" class="portal-topnav__icon-btn portal-topnav__burger"
            aria-label="Toggle navigation menu" aria-expanded="false" aria-controls="portal-sidebar">
            <i data-lucide="menu" class="portal-topnav__burger-icon" aria-hidden="true"></i>
        </button>

        <a class="navbar-brand portal-topnav__brand" href="{{ route('dashboard') }}">
            <x-brand-logo class="large-logo portal-topnav__logo" />
            <x-brand-logo class="small-logo portal-topnav__logo portal-topnav__logo--sm" />
        </a>

        <span class="portal-topnav__portal-name d-none d-lg-inline">Customer Portal</span>
    </div>

    <div class="collapse navbar-collapse portal-topnav__collapse" id="navbarSupportedContent">
        <ul class="navbar-nav right-nav ml-auto portal-topnav__actions">
            <li class="nav-item">
                <button type="button" id="theme-toggle" class="portal-topnav__icon-btn" aria-label="Toggle theme">
                    <i data-lucide="moon" class="portal-topnav__theme-icon" aria-hidden="true"></i>
                </button>
            </li>
            <li class="nav-item portal-topnav__user">
                <span class="portal-topnav__avatar" aria-hidden="true">{{ strtoupper(substr(Session::get('name', 'U'), 0, 1)) }}</span>
                <span class="name portal-topnav__username">{{ Session::get('name') }}</span>
            </li>
            <li class="nav-item">
                <form action="{{ route('logout') }}" method="POST" class="portal-topnav__logout-form">
                    @csrf
                    <button class="portal-topnav__logout-btn" type="submit" aria-label="Logout">
                        <x-icon name="log-out" :size="16" />
                        <span class="portal-topnav__logout-label">Logout</span>
                    </button>
                </form>
            </li>
        </ul>
    </div>
</nav>
