@include('include.topNav')
@php
    $clientCdToFilter = Session::get('user')->user_code;
    $filteredCustomers = array_filter($customer, function($client) use ($clientCdToFilter) {
        return $client->client_code === $clientCdToFilter && $client->email !== null;
    });
    $clientMailId = empty($filteredCustomers) ? '' : reset($filteredCustomers)->email;
@endphp

<div class="main-content d-flex flex-column portal-shell__main portal-main">

    <form action="{{ route('save.create.complaint') }}" id="complaintForm" method="post" enctype="multipart/form-data" class="portal-form">
        @csrf
        <input type="hidden" name="P3_CUST_CD" value="{{ Session::get('user')->user_code }}">
        @include('include.formHeader', ['title' => 'Complaint Create', 'subtitle' => 'Submit a new support request'])

        <div class="row">
            <div class="col-xl-12">
                <div class="card mb-30 portal-card">
                    <div class="card-body">
                        <div class="portal-form-section">
                            <h2 class="portal-form-section__title">Complaint details</h2>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="P3_MODULE">Module</label>
                                    <select id="P3_MODULE" name="P3_MODULE" class="form-control">
                                        <option value=""></option>
                                        @foreach ($module as $mod)
                                            <option value="{{ $mod->name }}">{{ $mod->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="P3_COMPL_DT"><span class="required text-danger">*</span> Complaint Date</label>
                                    <input type="date" id="P3_COMPL_DT" name="P3_COMPL_DT" class="form-control disabled"
                                        value="{{ date('Y-m-d') }}" readonly>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="P3_CONTACT_MAIL_ID">Customer Email</label>
                                    <input type="email" id="P3_CONTACT_MAIL_ID" name="P3_CONTACT_MAIL_ID"
                                        class="form-control" value="{{ $clientMailId }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="P3_USER_NAME">Complaint By</label>
                                    <input type="text" id="P3_USER_NAME" name="P3_USER_NAME" class="form-control">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="P3_COMPL_TYPE">Type</label>
                                    <select id="P3_COMPL_TYPE" name="P3_COMPL_TYPE" class="form-control">
                                        <option value=""></option>
                                        <option value="DB_Object">DB Object</option>
                                        <option value="Form" selected>Form</option>
                                        <option value="Graph">Graph</option>
                                        <option value="Others">Others</option>
                                        <option value="Report">Report</option>
                                        <option value="Tables">Tables</option>
                                        <option value="Views">Views</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="P3_ERROR_TYPE" id="P3_ERROR_TYPE_LABEL">Complaint Type</label>
                                    <select id="P3_ERROR_TYPE" name="P3_ERROR_TYPE" class="form-control">
                                        <option value=""></option>
                                        <option value="DP">Database Problem</option>
                                        <option value="NR">New Requirement</option>
                                        <option value="OT">Others</option>
                                        <option value="SP">Software Problem</option>
                                        <option value="ST">Support</option>
                                        <option value="UP" selected>User Problem</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="P3_COMPL_LEVEL">Complaint Level</label>
                                    <select id="P3_COMPL_LEVEL" name="P3_COMPL_LEVEL" class="form-control">
                                        <option value=""></option>
                                        <option value="C">Critical</option>
                                        <option value="L" selected>Low</option>
                                        <option value="M">Medium</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="P3_UPLOAD">Upload</label>
                                    <input type="file" id="P3_UPLOAD" name="P3_UPLOAD" class="form-control">
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="P3_PROBLEM_DESC">Problem Description</label>
                                    <textarea id="P3_PROBLEM_DESC" name="P3_PROBLEM_DESC" class="form-control" maxlength="500"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="portal-form-section">
                            <h2 class="portal-form-section__title">Status</h2>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="P3_STATUS_TYPE">Status</label>
                                    <select id="P3_STATUS_TYPE" name="P3_STATUS_TYPE" class="form-control disabled">
                                        <option value=""></option>
                                        <option value="CL">Cancel</option>
                                        <option value="CM">Complete</option>
                                        <option value="HL">Hold</option>
                                        <option value="PN" selected>Pending</option>
                                        <option value="SV">Sent For Customer Verification</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="P3_CLOSE_DT_TYPE">Close Date</label>
                                    <input type="date" id="P3_CLOSE_DT_TYPE" name="P3_CLOSE_DT_TYPE"
                                        class="form-control disabled" readonly>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="P3_MAWAI_REMARKS">Remarks For Customer</label>
                                    <textarea id="P3_MAWAI_REMARKS" name="P3_MAWAI_REMARKS" class="form-control disabled" maxlength="500"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
