@extends('base.base')

@php use App\Support\UserRole; @endphp

@section('title', 'Complaints')

@section('content')
    @if (UserRole::isAdmin(Session::get('user')->user_code))
        @include('include.sidebarAdmin')
    @elseif (UserRole::isSupport(Session::get('user')->user_code))
        @include('include.sidebarSupport')
    @else
        @include('include.sidebar')
    @endif

    @include('complaint.list')
@endsection

@section('scripts')
    @if ($isStaff)
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
                timeoutId = setTimeout(() => callback.apply(this, args), delay);
            };
        };

        const listUrl = @json(route('complaint.list'));
        const emptyColspan = 11;

        $(document).ajaxStart(function() {
            $('.loader-wrapper').css('display', 'flex');
            $('.table-responsive').css('display', 'none');
        }).ajaxStop(function() {
            $('.loader-wrapper').css('display', 'none');
            $('.table-responsive').css('display', 'block');
        });

        function fetchComplaints(page) {
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            jQuery.ajax({
                type: 'POST',
                url: listUrl,
                data: {
                    client_cd: $('#client_cd').val() || '',
                    date_from: $('#date_from').val(),
                    date_to: $('#date_to').val(),
                    page: page,
                    search: $('#dataTable_filter input').val(),
                    per_page: $('select[name="dataTable_length"]').val(),
                    sorting: 'complaint_number',
                    order: 'DESC'
                },
                success: function(res) {
                    if (res.status == 1) {
                        jQuery('#form_detail').html(res.data);
                        jQuery('#pagination').html(res.pagination);
                    } else if (res.status == 0) {
                        jQuery('#form_detail').html(
                            '<tr><td colspan="' + emptyColspan + '" class="portal-table-empty"><div class="portal-empty-state"><p class="portal-empty-state__title">No complaints found</p></div></td></tr>'
                        );
                        jQuery('#pagination').html('');
                    }
                }
            });
        }

        const getTableData = () => fetchComplaints(1);
        const getTableDataByPage = (page) => fetchComplaints(page);

        $('select[name="dataTable_length"]').on('change', function() {
            getTableDataByPage($('#current_page').text() || 1);
        });

        $(document).ready(() => {
            $('#dataTable_filter input').on('keyup', debounce(() => getTableDataByPage(1), 500));
            getTableData();
        });

        $('#applyComplaintFilters').on('click', function(e) {
            e.preventDefault();
            getTableData();
        });

        $('#resetComplaintFilters').on('click', function() {
            $('#date_from').val('');
            $('#date_to').val('');
            @if ($isStaff)
                $('#client_cd').val(null).trigger('change');
            @endif
            getTableData();
        });
    </script>
@endsection
