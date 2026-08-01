@extends('base.base')

@section('title', 'Dashboard')

@section('content')
    @php use App\Support\UserRole; @endphp
    @if (UserRole::isAdmin(Session::get('user')->user_code))
        @include('include.sidebarAdmin')
        @include('include.mainSupport')
    @elseif (UserRole::isSupport(Session::get('user')->user_code))
        @include('include.sidebarSupport')
        @include('include.mainSupport')
    @else
        @include('include.sidebar')
        @include('include.main')
    @endif
@endsection
