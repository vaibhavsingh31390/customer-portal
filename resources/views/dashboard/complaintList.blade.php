@extends('base.base')

@section('title', 'Complaint List')

@section('content')
    @php $client = false; @endphp
    @if (Session::get('user')->user_code && preg_match('/^[S]/', Session::get('user')->user_code))
        @include('include.sidebarSupport')
        @include('complaint.support')
    @else
        @php $client = true; @endphp
        @include('include.sidebar')
        @include('complaint.client')
    @endif
@endsection

@section('scripts')
    <script>
        const debounce = (callback, delay) => {
            let timeoutId;
            return function(...args) {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => callback.apply(this, args), delay);
            };
        };

        const primaryKey = 'complaint_number';
        const emptyColspan = 12;

        function getTableDataByPage(PAGENO) {
            const client = @json($client);
            const search = $('#dataTable_filter input').val();
            const per_page = $('select[name="dataTable_length"]').val();
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
            jQuery.ajax({
                type: 'POST',
                url: $('#listForm').attr('action'),
                data: { client, page: PAGENO, search, per_page, sorting: primaryKey, order: 'DESC' },
                success: function(res) {
                    if (res.status == 1) {
                        jQuery('#form_detail').html(res.data);
                        jQuery('#pagination').html(res.pagination);
                    } else if (res.status == 0) {
                        jQuery('#form_detail').html('<tr><td colspan="' + emptyColspan + '" class="portal-table-empty"><div class="portal-empty-state"><p class="portal-empty-state__title">No complaints found</p></div></td></tr>');
                    }
                }
            });
        }

        const getTableData = () => getTableDataByPage(1);

        $('select[name="dataTable_length"]').on('change', function() {
            getTableDataByPage($('#current_page').text() || 1);
        });

        $(document).ready(() => {
            $('#dataTable_filter input').on('keyup', debounce(() => getTableDataByPage(1), 500));
        });
        getTableData();
    </script>
@endsection
