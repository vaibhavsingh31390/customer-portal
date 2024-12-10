@extends('base.base')

@section('title', 'Dashboard')

@section('styles')
@endsection

@section('content')
    <!-- Page Wrapper -->
    @php
        $client = false;
    @endphp
    <div id="wrapper">
        @if (Session::get('user')->eng_cd && preg_match('/^[S]/', Session::get('user')->eng_cd))
            @include('include.sidebarSupport')
            @include('complaint.support')
        @else
            @php
                $client = true;
            @endphp
            @include('include.sidebar')
            @include('complaint.client')
        @endif


        <!-- Scroll to Top Button-->
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>

        <!-- Logout Modal-->


    </div>
    <!-- End of Page Wrapper -->

@endsection

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


        // Get table data
        const getTableData = () => {
            const client = @json($client);
            const search = $('#dataTable_filter input').val();;
            const per_page = $('select[name="dataTable_length"]').val();;
            const primaryKey = 'COMPLAINT_NO';
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajax({
                type: 'POST',
                url: $('#listForm').attr('action'),
                data: {
                    'client': client,
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
                    } else if (res.status == 0) {
                        var message =
                            '<tr><td colspan="12"><center>Data Not Found!</center></td></tr>';
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
            const client = @json($client);
            const search = $('#dataTable_filter input').val();
            const per_page = $('select[name="dataTable_length"]').val();;
            const primaryKey = 'COMPLAINT_NO';
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajax({
                type: 'POST',
                url: $('#listForm').attr('action'),
                data: {
                    'client': client,
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
                    } else if (res.status == 0) {
                        var message =
                            '<tr><td colspan="12"><center>Data Not Found!</center></td></tr>';
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
    </script>
@endsection
