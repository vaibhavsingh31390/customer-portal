@include('include.topNav')

@php
    $clientChart = $clientStatusChart ?? ['labels' => [], 'data' => [], 'colors' => []];
@endphp

<div class="main-content d-flex flex-column portal-shell__main portal-main">
    <x-page-header title="Dashboard" subtitle="Overview of your complaints and activity">
        <x-slot:actions>
            <x-button variant="primary" :href="route('show.create.complaint')" icon="plus">
                Create Complaint
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="row">
        <div class="col-lg-3 col-sm-6">
            <x-stat-card label="Total (This Month)" :value="$customer->total_count" variant="primary" icon="file-text" />
        </div>
        <div class="col-lg-3 col-sm-6">
            <x-stat-card label="Open" :value="$customer->count_open" variant="success" icon="folder-open" />
        </div>
        <div class="col-lg-3 col-sm-6">
            <x-stat-card label="Pending" :value="$customer->count_pend" variant="warning" icon="clock" />
        </div>
        <div class="col-lg-3 col-sm-6">
            <x-stat-card label="Closed" :value="$customer->count_closed" variant="default" icon="check-circle" />
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 col-xl-6" id="left_parent">
            <x-card title="Recent Complaints">
                @include('include.dataTable', [
                    'action' => route('dashboard.pending.list.client'),
                    'headers' => ['Complaint No.', 'Status'],
                    'lengthCol' => 'col-md-3',
                ])
            </x-card>
        </div>
        <div class="col-lg-6 col-xl-6" id="right_parent">
            <x-card title="Complaints by Status (This Month)">
                <div id="status-breakdown-chart"></div>
            </x-card>
        </div>
    </div>

    <div class="flex-grow-1"></div>
    @include('include.footer')
</div>

@section('scripts')
    <script>
        const debounce = (callback, delay) => {
            let timeoutId;
            return function(...args) {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => callback.apply(this, args), delay);
            };
        };

        const primaryKey = 'COUNT_PEND';
        const emptyColspan = 2;
        const statusChartData = @json($clientChart);

        const getTableData = () => getTableDataByPage(1);

        function getTableDataByPage(PAGENO) {
            const search = $('#dataTable_filter input').val();
            const per_page = $('select[name="dataTable_length"]').val();
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });
            jQuery.ajax({
                type: 'POST',
                url: $('#listForm').attr('action'),
                data: { page: PAGENO, search, per_page, sorting: primaryKey, order: 'DESC' },
                success: function(res) {
                    if (res.status == 1) {
                        jQuery("#form_detail").html(res.data);
                        jQuery("#pagination").html(res.pagination);
                        resize();
                    } else if (res.status == 0) {
                        jQuery("#form_detail").html('<tr><td colspan="' + emptyColspan + '" class="portal-table-empty"><div class="portal-empty-state"><p class="portal-empty-state__title">No complaints found</p></div></td></tr>');
                    }
                }
            });
        }

        const getTableDataBySearch = () => getTableDataByPage(1);

        $('select[name="dataTable_length"]').on('change', function() {
            getTableDataByPage($('#current_page').text() || 1);
        });

        $(document).ready(() => {
            $('#dataTable_filter input').on('keyup', debounce(getTableDataBySearch, 500));
        });
        getTableData();

        var chart = new ApexCharts(
            document.querySelector('#status-breakdown-chart'),
            window.PortalUI.statusChartOptions(statusChartData.data, statusChartData.labels, statusChartData.colors)
        );
        chart.render();
        window.PortalUI.registerChart(chart, function () {
            return window.PortalUI.statusChartOptions(statusChartData.data, statusChartData.labels, statusChartData.colors);
        });

        const resize = () => {
            $('#right_parent').height($('#left_parent').height());
        };
    </script>
@endsection
