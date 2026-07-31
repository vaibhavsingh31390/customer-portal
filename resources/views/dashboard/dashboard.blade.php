@extends('base.base')

@section('title', 'Dashboard')

@section('content')
    @if (Session::get('user')->user_code && preg_match('/^[S]/', Session::get('user')->user_code))
        @include('include.sidebarSupport')
        @include('include.mainSupport')
    @else
        @include('include.sidebar')
        @include('include.main')
    @endif
@endsection
