@include('include.topNav')

@php
    use App\Support\ComplaintStatus;
    $listHeaders = [
        'Complaint No.', 'Complaint Dt.', 'Cust Cd.', 'Cust name', 'Status',
        'Module', 'Compl Type', 'Error Type', 'Problem Desc', 'Close Dt', 'Assign To',
    ];
@endphp

<div class="main-content d-flex flex-column portal-shell__main portal-main">
    <form action="{{ route('complaint.export') }}" id="complaintForm" method="post" class="portal-form">
        @csrf

        <x-page-header title="Complaints" :subtitle="$isStaff ? 'Search and manage all client complaints' : 'View and export your complaint records'">
            <x-slot:actions>
                <x-button variant="primary" :href="route('show.create.complaint')" icon="plus">
                    Create Complaint
                </x-button>
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
                        @if ($isStaff)
                            <div class="form-group col-md-4">
                                <label for="client_cd">Customer</label>
                                <div class="portal-form__control">
                                    <select id="client_cd" name="client_cd" class="form-control portal-select2"
                                        data-placeholder="All customers">
                                        <option value=""></option>
                                        @foreach ($clients as $client)
                                            <option value="{{ $client->client_code }}">{{ $client->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @else
                            <input type="hidden" id="client_cd" name="client_cd"
                                value="{{ $customer_name->client_code ?? Session::get('user')->user_code }}">
                        @endif
                        <div class="form-group col-md-4">
                            <label for="status_cd">Status</label>
                            <div class="portal-form__control">
                                <select id="status_cd" name="status_cd" class="form-control portal-select2"
                                    data-placeholder="All statuses">
                                    @foreach (ComplaintStatus::filterOptions() as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="actions">
                        <div class="left portal-actions-left">
                            <button class="btn btn-secondary portal-btn" name="type" type="submit" value="Excel" formtarget="_blank">
                                <x-icon name="file-spreadsheet" :size="18" /><span class="portal-btn__label">Excel</span>
                            </button>
                            <button class="btn btn-secondary portal-btn" name="type" type="submit" value="PDF" formtarget="_blank">
                                <x-icon name="file-text" :size="18" /><span class="portal-btn__label">PDF</span>
                            </button>
                        </div>
                        <div class="right portal-actions-right">
                            <button class="btn btn-primary portal-btn" id="applyComplaintFilters" type="button">
                                <span class="portal-btn__label">Apply Filters</span>
                            </button>
                            <button class="btn btn-secondary portal-btn" id="resetComplaintFilters" type="button">
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
                        'action' => route('complaint.list'),
                        'headers' => $listHeaders,
                        'lengthCol' => 'col-md-2',
                    ])
                </x-card>
            </div>
        </div>
    </form>

    <div class="flex-grow-1"></div>
    @include('include.footer')
</div>
