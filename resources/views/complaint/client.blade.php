@include('include.topNav')

@php
    $listHeaders = [
        'Complaint No.', 'Complaint Dt.', 'Cust Cd.', 'Cust name', 'Status',
        'Module', 'Compl Type', 'Error Type', 'Problem Desc', 'Close Dt', 'Assign To',
    ];
@endphp

<div class="main-content d-flex flex-column portal-shell__main portal-main">
    <x-page-header title="Complaint List" subtitle="View and manage all complaints">
        <x-slot:actions>
            <x-button variant="primary" :href="route('show.create.complaint')" icon="plus">
                Create Complaint
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="row">
        <div class="col-lg-12 col-xl-12" id="left_parent">
            <x-card title="Complaint List">
                @include('include.dataTable', [
                    'action' => route('getTableData.support'),
                    'headers' => $listHeaders,
                    'lengthCol' => 'col-md-2',
                ])
            </x-card>
        </div>
    </div>

    <div class="flex-grow-1"></div>
    @include('include.footer')
</div>
