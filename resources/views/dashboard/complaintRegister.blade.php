@extends('base.base')

@section('title', 'Dashboard')

@section('styles')
@endsection

@section('content')
    @php $client = false; @endphp
    @if (Session::get('user')->user_code && preg_match('/^[S]/', Session::get('user')->user_code))
        @include('include.sidebarSupport')
        @include('register.support')
    @else
        @php $client = true; @endphp
        @include('include.sidebar')
        @include('register.client')
    @endif
@endsection

@section('scripts')
    @if (Session::get('user')->user_code && preg_match('/^[S]/', Session::get('user')->user_code))
        <script>
            $(document).ready(function() {
                if (window.PortalUI && window.PortalUI.initSelect2) {
                    window.PortalUI.initSelect2('#client_cd');
                }
            });
        </script>
    @endif
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
            const DATE_FROM = $('#date_from').val();
            const DATE_TO = $('#date_to').val();
            const CLIENT_CD = $('#client_cd').val();
            const client = @json($client);
            const search = $('#dataTable_filter input').val();;
            const per_page = $('select[name="dataTable_length"]').val();;
            const primaryKey = 'complaint_number';
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajax({
                type: 'POST',
                url: $('#listForm').attr('action2'),
                data: {
                    'client_cd': CLIENT_CD,
                    'date_from': DATE_FROM,
                    'date_to': DATE_TO,
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
                        // jQuery("#total_items").text(res.total_items);
                        // jQuery("#displayto").text(res.displayto);
                        // jQuery("#displayfrom").text(res.displayfrom);
                    } else if (res.status == 0) {
                        // jQuery(".po_search_loader").hide();
                        var
                            message =
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
            const DATE_FROM = $('#date_from').val();
            const DATE_TO = $('#date_to').val();
            const CLIENT_CD = $('#client_cd').val();
            const client = @json($client);
            const search = $('#dataTable_filter input').val();
            const per_page = $('select[name="dataTable_length"]').val();;
            const primaryKey = 'complaint_number';
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajax({
                type: 'POST',
                url: $('#listForm').attr('action'),
                data: {
                    'client_cd': CLIENT_CD,
                    'date_from': DATE_FROM,
                    'date_to': DATE_TO,
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
                        var
                            message =
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


        $('#executeComplaintRegister').on('click', function(e) {
            e.preventDefault();
            if ($('#CLIENT').val() === '') {
                return showToast('Customer is required.', false);
            }
            getTableData();
        });
    </script>
@endsection
