@include('include.topNav')

<div class="main-content d-flex flex-column hide-sidemenu">
    <!-- Main Content Header -->
    <div class="main-content-header d-sm-flex align-items-center justify-content-between mb-4">
        <h1>Complaint List(Support)</h1>
        <a href="{{ route('show.create.complaint') }}"
            class="d-flex justify-content-between align-items-center btn btn btn-primary" style="max-width: 190px">
            <i class="lni lni-plus mr-2"></i>
            Create Complaint</a>
    </div>
    <!-- End Main Content Header -->

    <!-- Stats Card -->
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
                                <div class="dataTables_length" id="dataTable_length"><label>Show entries</label><select
                                        name="dataTable_length" aria-controls="dataTable"
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
    <!-- End Stats Card -->

    <!-- Footer -->
    <div class="flex-grow-1"></div>
    @include('include.footer')
</div>
