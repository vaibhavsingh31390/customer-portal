@extends('base.baseAuth')

@section('title', ' — Server Error')

@section('content')
    <div class="portal-error">
        <div class="portal-error__card">
            <p class="portal-error__code">500</p>
            <h1 class="portal-error__title">Something went wrong</h1>
            <p class="portal-error__desc">We're working on it. Please try again in a few moments.</p>
            <x-button variant="primary" :href="route('login.form')" icon="refresh-cw">
                Try Again
            </x-button>
        </div>
    </div>
@endsection
