@include('include.topNav')

@php
    $listHeaders = [
        'Complaint No.', 'Complaint Dt.', 'Cust Cd.', 'Cust name', 'Status',
        'Module', 'Compl Type', 'Error Type', 'Problem Desc', 'Close Dt', 'Assign To',
    ];
@endphp

<div class="main-content d-flex flex-column portal-shell__main portal-main">
    <form action="{{ route('register.complaint.export') }}" action2="{{ route('register.complaint.report') }}"
        id="complaintForm" method="post" class="portal-form">
        @csrf

        <x-page-header title="Complaint Register" subtitle="Search and export across all clients">
            <x-slot:actions>
                <x-button variant="ghost" :href="route('dashboard')" icon="home" class="portal-btn--icon-only" aria-label="Dashboard" />
            </x-slot:actions>
        </x-page-header>

        <div class="row">
            <div class="col-xl-12">
                <x-card title="Filters">
                    <div class="form-row portal-form-section">
                        <div class="form-group col-md-4">
                            <label for="date_from">Date From</label>
                            <input type="date" id="date_from" name="date_from" class="form-control">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="date_to">Date To</label>
                            <input type="date" id="date_to" name="date_to" class="form-control">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="client_cd">Customer</label>
                            <div class="portal-form__control">
                                <select id="client_cd" name="client_cd" class="form-control portal-select2"
                                    data-placeholder="Select customer…">
                                    <option value=""></option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->client_code }}">{{ $client->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="actions">
                        <div class="left portal-actions-left">
                            <button class="btn btn-secondary portal-btn" name="type" id="executeComplaintRegisterExport" type="submit" value="Excel" formtarget="_blank">
                                <x-icon name="file-spreadsheet" :size="18" /><span class="portal-btn__label">Excel</span>
                            </button>
                            <button class="btn btn-secondary portal-btn" name="type" id="executeComplaintRegisterExport" type="submit" value="PDF" formtarget="_blank">
                                <x-icon name="file-text" :size="18" /><span class="portal-btn__label">PDF</span>
                            </button>
                        </div>
                        <div class="right portal-actions-right">
                            <button class="btn btn-primary portal-btn" id="executeComplaintRegister" type="submit">
                                <span class="portal-btn__label">Search</span>
                                <span class="loader-btn portal-btn__spinner" aria-hidden="true"></span>
                            </button>
                            <button class="btn btn-secondary portal-btn" id="resetFieldsRegister" type="button"
                                onclick="document.getElementById('date_from').value=''; document.getElementById('date_to').value='';$('#client_cd').val(null).trigger('change');">
                                <span class="portal-btn__label">Reset</span>
                            </button>
                        </div>
                    </div>
                </x-card>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 col-xl-12" id="left_parent">
                <x-card title="Results">
                    @include('include.dataTable', [
                        'action' => route('getTableData.support'),
                        'headers' => $listHeaders,
                        'lengthCol' => 'col-md-2',
                    ])
                </x-card>
            </div>
        </div>
    </form>
</div>
