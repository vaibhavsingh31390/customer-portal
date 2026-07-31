@extends('base.baseAuth')

@section('title', ' — Page Not Found')

@section('content')
    <div class="portal-error">
        <div class="portal-error__card">
            <p class="portal-error__code">404</p>
            <h1 class="portal-error__title">Page not found</h1>
            <p class="portal-error__desc">The page you're looking for doesn't exist or has been moved.</p>
            <x-button variant="primary" :href="route('login.form')" icon="arrow-left">
                Back to Login
            </x-button>
        </div>
    </div>
@endsection
