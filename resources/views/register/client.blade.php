@include('include.topNav')

<div class="main-content d-flex flex-column hide-sidemenu">
    <!-- Main Content Header -->

    <form action="{{ route('register.complaint.export') }}" action2="{{ route('register.complaint.report') }}"
        id="complaintForm" method="post">
        @csrf
        <div class="main-content-header">
            <h1>Complaint Register</h1>

            <div class="text-left text-md-right text-lg-right w-70 mt-2 mt-sm-0 action-btn">
                <a href="{{ route('dashboard') }}" class="btn mr-3 p-0">
                    <i data-feather="home" class="icon"></i>
                </a>
            </div>
        </div>
        <!-- End Main Content Header -->

        <div class="row">
            <div class="col-xl-12">
                <div class="card mb-30">
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="date_from">Date From</label>
                                <input type="date" id="date_from" name="date_from" class="form-control">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="P3_COMPL_DT">Date To</label>
                                <input type="date" id="date_to" name="date_to"
                                    class="form-control form-control-user">
                            </div>

                            <div class="form-group col-md-4">
                                <label for="P3_CONTACT_MAIL_ID">Customer</label>
                                <input type="text" id="CLIENT_NAME" name="CLIENT_NAME" class="form-control disabled"
                                    value="{{ $customer_name->client_name }}">

                                <input type="hidden" id="client_cd" name="client_cd" class="form-control disabled"
                                    value="{{ $customer_name->client_cd }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-md-12">
                                <div class="actions  text-right">
                                    <div class="left">
                                        <button class="btn btn-primary" name="type"
                                            id="executeComplaintRegisterExport" type="submit"
                                            style="margin-left: auto;" value="Excel" formtarget="_blank">
                                            <i class="icofont-file-excel"></i>
                                        </button>
                                        <button class="btn btn-primary" name="type"
                                            id="executeComplaintRegisterExport" type="submit"
                                            style="margin-left: auto;" value="PDF" formtarget="_blank">
                                            <i class="icofont-file-pdf"></i>
                                        </button>
                                    </div>

                                    <div class="right">
                                        <button class="btn btn-primary" id="executeComplaintRegister" type="submit"
                                            style="margin-left: auto;">Search
                                            <span class="loader-btn"></span>
                                        </button>
                                        <button class="btn btn-primary" id="resetFieldsRegister" type="button"
                                            style="margin-left: auto;"
                                            onclick="document.getElementById('date_from').value=''; document.getElementById('date_to').value='';">
                                            Reset
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 col-xl-12" id="left_parent">
                <div class="card mb-30">
                    <div class="card-body">
                        <div class="card-header">
                            <h5 class="card-title">Complaint List</h5>
                        </div>
                        <form action="{{ route('getTableData.support') }}" id="listForm" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-sm-12 col-md-2">
                                    <div class="dataTables_length" id="dataTable_length"><label>Show
                                            entries</label><select name="dataTable_length" aria-controls="dataTable"
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
                                                Complaint Dt.
                                            </th>
                                            <th>
                                                Cust Cd.
                                            </th>
                                            <th>
                                                Cust name
                                            </th>
                                            <th>
                                                Status
                                            </th>
                                            <th>
                                                Module
                                            </th>
                                            <th>
                                                Compl Type
                                            </th>
                                            <th>
                                                Error Type
                                            </th>
                                            <th>
                                                Problem Desc
                                            </th>
                                            <th>
                                                Close Dt
                                            </th>
                                            <th>
                                                Assign To
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
        </div>
    </form>
</div>
<!-- End of Content Wrapper -->
