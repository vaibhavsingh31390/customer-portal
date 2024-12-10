@extends('base.baseAuth')

@section('title', 'Login')

@section('styles')
    <style>
        .auth-bg {
            background-image: url({{ asset('assets/img/bg.png') }});
        }

        .auth-bg {
            background-size: cover;
            background-position: center center;
            background-origin: border-box;
            min-height: 100svh;
            min-width: 100svw;
            box-shadow: rgba(17, 17, 26, 0.1) 0px 8px 24px, rgba(17, 17, 26, 0.1) 0px 16px 56px, rgba(17, 17, 26, 0.1) 0px 24px 80px;
        }

        .auth-box {
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
        }

        .small-logo {
            /* box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px; */
            padding: 10px;
            border-radius: 5px;
        }

        .auth-main-content {
            /* height: auto !important; */
            /* min-height: 100svh !important; */
        }




        @media (max-width: 768px) {
            .auth-main-content {
                display: flex;
                justify-content: center;
                align-items: center;
            }
        }
    </style>
@endsection

@section('content')
    <div class="auth-main-content auth-bg">
        <div class="d-table">
            <div class="d-tablecell">
                <div class="auth-box">
                    <div class="row align-items-center">
                        <div class="col-md-6 d-none d-md-block">
                            <div class="form-left-content">
                                <div class="auth-logo">
                                    <img src="{{ asset('assets/img/mawai.png') }}" alt="Mawai Logo" class="small-logo mb-3"
                                        style="max-width: 60%;">
                                </div>

                                <div class="auth-logo">
                                    <img src="{{ asset('assets/img/Support-Center.svg') }}" alt="Logo">
                                </div>
                                <p class="mt-3 text-center" style="font-size: 11px;">Copyright © {{ date('Y') }} Mawai
                                    Infotech
                                    Ltd. All rights reserved
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-content">
                                <div class="auth-logo text-center d-block d-md-none">
                                    <img src="{{ asset('assets/img/mawai.png') }}" alt="Mawai Logo" class="mb-3"
                                        style="max-width: 60%;">
                                </div>
                                <h1 class="heading">Log In</h1>
                                <form action="{{ route('login') }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label class="form-label">Username</label>
                                        <input type="text" class="form-control" id="username" name="username" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Password</label>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary">Log In</button>
                                        {{-- <a class="fp-link" href="{{ route('password.request') }}">Forgot Password?</a> --}}
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- If you need to show errors --}}
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <script>
                showToast('{{ $error }}', false);
            </script>
        @endforeach
    @endif
@endsection
