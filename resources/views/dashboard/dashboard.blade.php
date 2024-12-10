@extends('base.base')

@section('title', 'Dashboard')

@section('styles')
@endsection

@section('content')
    <!-- Page Wrapper -->
    <div id="wrapper">
        @if (Session::get('user')->eng_cd && preg_match('/^[S]/', Session::get('user')->eng_cd))
            @include('include.sidebarSupport')
            @include('include.mainSupport')
        @else
            @include('include.sidebar')
            @include('include.main')
        @endif


        <!-- Scroll to Top Button-->
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>

        <!-- Logout Modal-->


    </div>
    <!-- End of Page Wrapper -->

@endsection
