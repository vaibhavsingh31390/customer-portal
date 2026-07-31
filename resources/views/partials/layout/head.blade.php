<meta charset="UTF-8">
<title>Customer Portal | heyvai.dev @yield('title')</title>
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="color-scheme" content="light dark">
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Theme: apply persisted preference before first paint --}}
<script src="{{ asset('assets/js/theme-init.js') }}"></script>

{{-- Typography --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

{{-- Design tokens (load first) --}}
<link rel="stylesheet" href="{{ asset('assets/css/tokens.css') }}">

{{-- Vendor CSS --}}
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/LineIcons.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/viewer.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/icofont.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/calendar.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/toastify.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/select2-bootstrap.css') }}">

{{-- App overrides (load last among stylesheets) --}}
<link rel="stylesheet" href="{{ asset('assets/css/style_child.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">

{{-- Favicon (generated from heyvai.dev logo) --}}
<link rel="icon" href="{{ asset('favicon.png') }}" type="image/png" sizes="32x32">
<link rel="icon" href="{{ asset('favicon-32x32.png') }}" type="image/png" sizes="32x32">
<link rel="icon" href="{{ asset('favicon-16x16.png') }}" type="image/png" sizes="16x16">
<link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

@yield('styles')
