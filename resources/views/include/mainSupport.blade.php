@include('include.topNav')

<div class="main-content d-flex flex-column portal-shell__main portal-main">
    <x-page-header title="Dashboard" subtitle="Support overview across all clients">
        <x-slot:actions>
            <x-button variant="primary" :href="route('show.create.complaint')" icon="plus">
                Create Complaint
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="row">
        <div class="col-lg-6 col-sm-6">
            <x-stat-card label="Total Complaints" :value="$supportSummary->total_count ?? 0" variant="primary" icon="file-text" value-id="total_complaint" />
        </div>
        <div class="col-lg-6 col-sm-6">
            <x-stat-card label="Pending Complaints" :value="$supportSummary->count_pend ?? 0" variant="success" icon="clock" value-id="pending_complaint" />
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 col-xl-6" id="left_parent">
            <x-card title="Pending Complaints">
                @include('include.dataTable', [
                    'action' => route('dashboard.pending.list'),
                    'headers' => ['Client Name', 'Pending Complaints'],
                    'lengthCol' => 'col-md-3',
                    'numericLastColumn' => true,
                ])
            </x-card>
        </div>
        <div class="col-lg-6 col-xl-6" id="right_parent">
            <x-card title="Total Vs Pending">
                <div id="total-pending-chart"></div>
            </x-card>
        </div>
    </div>

    <div class="flex-grow-1"></div>
    @include('include.footer')
</div>

@section('scripts')
    <script>
        let TOT_ANA_TOT = {{ $supportSummary->total_count ?? 0 }};
        let TOT_ANA_PEND = {{ $supportSummary->count_pend ?? 0 }};

        function updateChartData() {
            $('#total_complaint').text(TOT_ANA_TOT);
            $('#pending_complaint').text(TOT_ANA_PEND);
            chart.updateSeries([
                { name: 'Total', data: [TOT_ANA_TOT] },
                { name: 'Pending', data: [TOT_ANA_PEND] },
            ]);
        }

        const debounce = (callback, delay) => {
            let timeoutId;
            return function(...args) {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => callback.apply(this, args), delay);
            };
        };

        const primaryKey = 'COUNT_PEND';
        const emptyColspan = 2;

        const getTableData = () => getTableDataByPage(1);

        function getTableDataByPage(PAGENO) {
            const search = $('#dataTable_filter input').val();
            const per_page = $('select[name="dataTable_length"]').val();
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
            jQuery.ajax({
                type: 'POST',
                url: $('#listForm').attr('action'),
                data: { page: PAGENO, search, per_page, sorting: primaryKey, order: 'DESC' },
                success: function(res) {
                    if (res.status == 1) {
                        jQuery("#form_detail").html(res.data);
                        jQuery("#pagination").html(res.pagination);
                        TOT_ANA_TOT = res.total_analytics.total;
                        TOT_ANA_PEND = res.total_analytics.total_pend;
                        updateChartData();
                        resize();
                    } else if (res.status == 0) {
                        jQuery("#form_detail").html('<tr><td colspan="' + emptyColspan + '" class="portal-table-empty"><div class="portal-empty-state"><p class="portal-empty-state__title">No data found</p></div></td></tr>');
                    }
                }
            });
        }

        $('select[name="dataTable_length"]').on('change', function() {
            getTableDataByPage($('#current_page').text() || 1);
        });

        $(document).ready(() => {
            $('#dataTable_filter input').on('keyup', debounce(() => getTableDataByPage(1), 500));
        });
        getTableData();

        var chart = new ApexCharts(
            document.querySelector('#total-pending-chart'),
            window.PortalUI.complaintChartOptions([
                { name: 'Total', data: [TOT_ANA_TOT] },
                { name: 'Pending', data: [TOT_ANA_PEND] },
            ])
        );
        chart.render();
        window.PortalUI.registerChart(chart, function () {
            return window.PortalUI.complaintChartOptions([
                { name: 'Total', data: [TOT_ANA_TOT] },
                { name: 'Pending', data: [TOT_ANA_PEND] },
            ]);
        });

        const resize = () => { $('#right_parent').height($('#left_parent').height()); };
    </script>
@endsection
