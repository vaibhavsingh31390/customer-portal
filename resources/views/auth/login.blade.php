@extends('base.baseAuth')

@section('title', 'Login')

@section('content')
    <button type="button" id="theme-toggle" class="portal-auth__theme-btn portal-topnav__icon-btn" aria-label="Toggle theme">
        <i data-lucide="moon" aria-hidden="true"></i>
    </button>

    <div class="portal-auth">
        <div class="portal-auth__card">
            <div class="portal-auth__grid">
                <div class="portal-auth__brand">
                    <x-brand-logo class="portal-auth__logo" />
                    <p class="portal-auth__brand-tagline">Built for modern customer support</p>
                    @include('include.brandCopyright')
                </div>

                <div class="portal-auth__form-wrap">
                    <x-brand-logo class="portal-auth__logo d-md-none mb-4" />
                    <h1 class="portal-auth__title">Welcome back</h1>
                    <p class="portal-auth__subtitle">Sign in to your customer portal</p>

                    <form id="login-form" action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="form-label" for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required autocomplete="username">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password">
                        </div>
                        <button type="submit" class="btn btn-primary portal-btn portal-auth__submit w-100">
                            <span class="portal-btn__label">Log In</span>
                            <span class="portal-dots-loader portal-btn__spinner loader-btn" aria-hidden="true"><span></span><span></span><span></span></span>
                        </button>
                    </form>

                    @if ($testMode)
                        <div class="portal-test-panel test-mode-panel">
                            <span class="portal-test-panel__badge test-mode-badge">Test Mode</span>
                            <p class="portal-test-panel__hint">Quick login with seeded test accounts:</p>
                            @foreach ($testAccounts as $account)
                                <button type="button"
                                    class="portal-test-login-btn test-login-btn{{ ($account['role'] ?? '') === 'admin' ? ' portal-test-login-btn--admin' : '' }}"
                                    data-username="{{ $account['username'] }}"
                                    data-password="{{ $account['password'] }}">
                                    <strong>{{ $account['label'] }}</strong>
                                    @if (($account['role'] ?? '') === 'admin')
                                        <span class="portal-test-login-btn__role">Admin</span>
                                    @endif
                                    <span>{{ $account['username'] }} / {{ $account['password'] }} — {{ $account['description'] }}</span>
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @if ($testMode ?? false)
        <script>
            document.querySelectorAll('.test-login-btn').forEach(function (button) {
                button.addEventListener('click', function () {
                    document.getElementById('username').value = this.dataset.username;
                    document.getElementById('password').value = this.dataset.password;
                    document.getElementById('login-form').submit();
                });
            });
        </script>
    @endif

    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <script>showToast(@json($error), false);</script>
        @endforeach
    @endif
@endsection
