<!doctype html>
<html class="no-js" lang="en">

<head>
    @include('partials.layout.head')
</head>

<body>
    <div class="preloader">
        <div class="d-table">
            <div class="d-tablecell">
                <span class="loader">
                    <span class="loader-inner"></span>
                </span>
            </div>
        </div>
    </div>

    <div id="wrapper">
        <div class="container-fluid p-0">
            @yield('content')
        </div>
    </div>

    @php $loadApexCharts = true; @endphp
    @include('partials.layout.scripts')
</body>

</html>
