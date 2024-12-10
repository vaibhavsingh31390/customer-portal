<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="UTF-8">
    <title>MAWAI TERMS | CUSTOMER PORTAL @yield('title')</title>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/LineIcons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/viewer.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/icofont.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/calendar.css') }}">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/toastify.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/select2-bootstrap.css') }}">
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('Favicon_2.png') }}">
    @yield('styles')
</head>

<body>
    <!-- Preloader -->
    <div class="preloader">
        <div class="d-table">
            <div class="d-tablecell">
                <span class="loader">
                    <span class="loader-inner"></span>
                </span>
            </div>
        </div>
    </div>
    <!-- End Preloader -->
    <!-- Page Wrapper -->
    <div id="wrapper">
        <div class="container-fluid p-0">
            @yield('content')
        </div>
    </div>



    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <!-- Feather Icon JS -->
    <script src="{{ asset('assets/js/feather.min.js') }}"></script>
    <!-- Gallery viewer JS -->
    <script src="{{ asset('assets/js/viewer.min.js') }}"></script>
    <!-- ApexCharts JS -->
    <script src="{{ asset('assets/js/apex-charts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/js/apex-charts/apexcharts-stock-prices.js') }}"></script>
    <script src="{{ asset('assets/js/apex-charts/apex-line-charts.js') }}"></script>
    <script src="{{ asset('assets/js/apex-charts/apex-area-charts.js') }}"></script>
    <script src="{{ asset('assets/js/apex-charts/apex-bar-charts.js') }}"></script>
    <script src="{{ asset('assets/js/apex-charts/apex-mixed-charts.js') }}"></script>
    <script src="{{ asset('assets/js/apex-charts/apex-pie-donuts-charts.js') }}"></script>
    <script src="{{ asset('assets/js/apex-charts/sales-by-countries.js') }}"></script>
    <script src="{{ asset('assets/js/apex-charts/month-sales-statistics.js') }}"></script>
    <script src="{{ asset('assets/js/apex-charts/order-summary.js') }}"></script>
    <script src="{{ asset('assets/js/apex-charts/visitors-overview.js') }}"></script>
    <script src="{{ asset('assets/js/apex-charts/leads-stats.js') }}"></script>
    <script src="{{ asset('assets/js/apex-charts/apex-column-charts.js') }}"></script>
    <!-- Custom chart JS -->
    {{-- <script src="{{ asset('assets/js/custom-chart.js') }}"></script> --}}
    <!-- Custom JS -->
    <script src="{{ asset('assets/js/custom.js') }}"></script>

    <script src="{{ asset('assets/js/select2.min.js') }}"></script>

    <!-- Page level plugins -->
    <script src="{{ asset('assets/js/toastify-js.js') }}"></script>

    <!-- Page level custom scripts -->
    <script type="text/javascript">
        const showToast = (message, type = true, duration = 5000, position = "right") => {

            const bgColor = type ? "#28a745" : '#dc3545';
            Toastify({
                text: message,
                duration: duration,
                close: true,
                gravity: "top",
                stopOnFocus: true,
                position: position,
                style: {
                    background: bgColor,
                    color: "#FFFFFF"
                },
            }).showToast();
        }

        $(document).ready(function() {
            if ($(window).width() >= 1024) {
                setTimeout(() => {
                    $('.burger-menu').click();
                }, 100);
            }

            $(window).resize(function() {
                if ($(window).width() < 768) {
                    $('.burger-menu').click();
                }
            });
        })
    </script>
    @yield('scripts')
</body>

</html>
