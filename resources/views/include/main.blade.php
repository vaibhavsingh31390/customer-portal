@include('include.topNav')

<!-- Main Content Wrapper -->
<div class="main-content d-flex flex-column hide-sidemenu">
    <!-- Main Content Header -->
    <div class="main-content-header d-sm-flex align-items-center justify-content-between mb-4">
        <h1>Dashboard</h1>
        <a href="{{ route('show.create.complaint') }}"
            class="d-flex justify-content-between align-items-center btn btn btn-primary" style="max-width: 190px">
            <i class="lni lni-plus mr-2"></i>
            Create Complaint</a>
    </div>
    <!-- End Main Content Header -->

    <!-- Stats Card -->
    <div class="row">
        <div class="col-lg-6 col-sm-6">
            <div class="stats-card-two mb-30" style="background-color: rgba(0, 143, 251, 1);">
                <div class="media align-items-center justify-content-between">
                    <div class="ml-0">
                        <p class="mb-10 line-height-1">Total Complaint</p>
                        <h3 class="mb-0 fs-25"> {{ $customer->total_count }}</h3>
                    </div>
                    <div class="avatar avatar-blue">
                        {{-- <i data-feather="dollar-sign"></i> --}}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-sm-6">
            <div class="stats-card-two mb-30" style="background-color: rgba(0, 227, 150, 1);">
                <div class="media align-items-center justify-content-between">
                    <div class="ml-0">
                        <p class="mb-10 line-height-1">Pending Complaint</p>
                        <h3 class="mb-0 fs-25"> {{ $customer->count_pend }}</h3>
                    </div>
                    <div class="avatar avatar-cyan">
                        {{-- <i data-feather="briefcase"></i> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Stats Card -->

    <!-- Total & Pending -->
    <div class="row">
        <div class="col-lg-6 col-xl-6" id="left_parent">
            <div class="card mb-30">
                <div class="card-body">
                    <div class="card-header">
                        <h5 class="card-title">Pending Complaint</h5>
                    </div>
                    <form action="{{ route('dashboard.pending.list.client') }}" id="listForm" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-sm-12 col-md-3">
                                <div class="dataTables_length" id="dataTable_length"><label>Show entries</label><select
                                        name="dataTable_length" aria-controls="dataTable"
                                        class="custom-select custom-select-sm form-control form-control-sm">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select> </div>
                            </div>
                            <div class="col-sm-12 col-md-6  mb-4">
                                <div id="dataTable_filter" class="dataTables_filter"><label>Search:</label><input
                                        type="search" class="form-control form-control-sm" placeholder=""
                                        aria-controls="dataTable"></div>
                            </div>
                        </div>
                        <div class="loader-wrapper">
                            <div class="loader-table"></div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover text-vertical-middle mb-0">
                                <thead class="bort-none borpt-0">
                                    <tr>
                                        <th>
                                            Complaint No.
                                        </th>
                                        <th>
                                            Status
                                        </th>
                                    </tr>
                                </thead>

                                <tbody id="form_detail">

                                </tbody>
                            </table>

                        </div>
                        <div class="mt-2" id="pagination">

                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-xl-6" id="right_parent">
            <div class="card mb-30">
                <div class="card-body">
                    <div class="card-header">
                        <h5 class="card-title">Total Vs Pending</h5>
                    </div>
                    <div id="total-pending-chart"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="flex-grow-1"></div>
    @include('include.footer')
</div>
<!-- End Main Content Wrapper -->

@section('scripts')
    <script>
        const debounce = (callback, delay) => {
            let timeoutId;
            return function(...args) {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => {
                    callback.apply(this, args);
                }, delay);
            };
        };

        $(document).ajaxStart(function() {
            $('.loader-wrapper').css('display', 'flex');
            $('.table-responsive').css('display', 'none');
        }).ajaxStop(function() {
            $('.loader-wrapper').css('display', 'none');
            $('.table-responsive').css('display', 'block');
        });


        const primaryKey = 'COUNT_PEND';
        // Get table data
        const getTableData = () => {
            const search = $('#dataTable_filter input').val();;
            const per_page = $('select[name="dataTable_length"]').val();;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajax({
                type: 'POST',
                url: $('#listForm').attr('action'),
                data: {
                    'search': search,
                    'per_page': per_page,
                    'sorting': primaryKey,
                    'order': 'DESC'
                },
                success: function(res) {
                    console.log(res);
                    if (res.status == 1) {
                        console.log(res);
                        jQuery("#form_detail").html(res.data);
                        jQuery("#pagination").html(res.pagination);
                        resize();
                    } else if (res.status == 0) {
                        var message =
                            '<tr><td colspan="2"><center>Data Not Found!</center></td></tr>';
                        jQuery("#form_detail").html(message);
                    }
                }
            });
        }

        $('select[name="dataTable_length"]').on('change', function() {
            const page = $('#current_page').text();
            getTableDataByPage(page);
        });

        function getTableDataByPage(PAGENO) {
            const search = $('#dataTable_filter input').val();
            const per_page = $('select[name="dataTable_length"]').val();;
            const primaryKey = 'COUNT_PEND';
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajax({
                type: 'POST',
                url: $('#listForm').attr('action'),
                data: {
                    'page': PAGENO,
                    'search': search,
                    'per_page': per_page,
                    'sorting': primaryKey,
                    'order': 'DESC'
                },
                success: function(res) {
                    console.log(res);
                    if (res.status == 1) {
                        console.log(res);
                        jQuery("#form_detail").html(res.data);
                        jQuery("#pagination").html(res.pagination);
                        resize();
                    } else if (res.status == 0) {
                        var message =
                            '<tr><td colspan="2"><center>Data Not Found!</center></td></tr>';
                        jQuery("#form_detail").html(message);
                    }
                }
            });
        }

        const getTableDataBySearch = function() {
            const page = $('#current_page').text();
            getTableDataByPage(1);
        };

        $(document).ready(() => {
            $('#dataTable_filter input').on('keyup', debounce(function() {
                getTableDataBySearch.call(this);
            }, 500));
        });
        getTableData();

        let TOT_ANA_TOT = {{ $customer->total_count }};
        let TOT_ANA_PEND = {{ $customer->count_pend }};

        var options = {
            chart: {
                height: 530,
                type: 'bar',
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    dataLabels: {
                        position: 'top',
                    },
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return val;
                },
                offsetY: -40,
                style: {
                    fontSize: '12px',
                    colors: ["#304758"]
                }
            },
            series: [{
                name: 'Total',
                data: [TOT_ANA_TOT],
                colors: ['#008ffb']
            }, {
                name: 'Pending',
                data: [TOT_ANA_PEND],
                colors: ['#00e396']
            }],
            xaxis: {
                categories: ["Complaints"],
                position: 'top',
                labels: {
                    offsetY: -18,
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            yaxis: {
                labels: {
                    show: true,
                    formatter: function(val) {
                        return val + "%";
                    }
                }
            },
            fill: {
                gradient: {
                    shade: 'light',
                    type: "horizontal",
                    shadeIntensity: 0.25,
                    gradientToColors: undefined,
                    inverseColors: true,
                    opacityFrom: 1,
                    opacityTo: 1,
                    stops: [50, 0, 100, 100]
                },
            },
            stroke: {
                width: 1,
                colors: ["#fff"]
            },
            tooltip: {
                enabled: false
            },
            grid: {
                show: false,
            },
            legend: {
                show: true,
                offsetY: -10,
            }
        };

        var chart = new ApexCharts(
            document.querySelector("#total-pending-chart"),
            options
        );

        chart.render();

        const resize = () => {
            var leftParentHeight = $('#left_parent').height();
            $('#right_parent').height(leftParentHeight);
        }
    </script>
@endsection
