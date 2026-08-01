<!doctype html>
<html class="no-js" lang="en">

<head>
    @include('partials.layout.head')
</head>

<body>
    <x-loader variant="page" />

    <div id="wrapper">
        <div class="container-fluid p-0">
            @yield('content')
        </div>
    </div>

    @php $loadApexCharts = true; @endphp
    @include('partials.layout.scripts')
</body>

</html>
