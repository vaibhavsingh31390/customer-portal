@include('include.topNav')

<div class="main-content d-flex flex-column portal-shell__main portal-main">

    <form action="{{ route('save.edit.complaint', ['id' => base64_encode($data->complaint_number)]) }}" id="complaintForm"
        method="post" enctype="multipart/form-data" class="portal-form">
        @csrf
        @include('include.formHeader', ['title' => 'Complaint Edit', 'subtitle' => 'Support — update complaint details'])

        <div class="row">
            <div class="col-xl-12">
                <div class="card mb-30 portal-card">
                    <div class="card-body">
                        <div class="portal-form-section">
                            <h2 class="portal-form-section__title">Customer &amp; complaint</h2>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="P8_CUST_CD">Select Customer</label>
                                    <div class="portal-form__control">
                                        <select id="P8_CUST_CD" name="P8_CUST_CD" class="form-control portal-select2" data-placeholder="Select customer…">
                                            <option value=""></option>
                                            @foreach ($customer as $cust)
                                                <option value="{{ $cust->client_code }}" data-src="{{ $cust->email }}"
                                                    @if ($cust->client_code === $data->client_code) selected @endif>
                                                    {{ $cust->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="P8_COMPLAINT_NO">Complaint No.</label>
                                    <input type="text" id="P8_COMPLAINT_NO" name="P8_COMPLAINT_NO"
                                        class="form-control disabled" value="{{ $data->complaint_number }}" readonly>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="P3_COMPL_DT"><span class="required text-danger">*</span> Complaint Date</label>
                                    <input type="date" id="P3_COMPL_DT" name="P3_COMPL_DT"
                                        class="form-control disabled"
                                        value="{{ !empty($data->complaint_date) ? date('Y-m-d', strtotime($data->complaint_date)) : '' }}" readonly>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="P3_CONTACT_MAIL_ID">Customer Email</label>
                                    <input type="email" id="P3_CONTACT_MAIL_ID" name="P3_CONTACT_MAIL_ID"
                                        class="form-control" value="{{ $data->contact_email ?? '' }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="P3_USER_NAME">Complaint By</label>
                                    <input type="text" id="P3_USER_NAME" name="P3_USER_NAME" class="form-control"
                                        value="{{ $data->contact_name ?? '' }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="P8_CUST_NAME">Customer Code</label>
                                    <input type="text" id="P8_CUST_NAME" name="P8_CUST_NAME"
                                        class="form-control disabled" value="{{ $data->client_code }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="portal-form-section">
                            <h2 class="portal-form-section__title">Classification</h2>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="P3_MODULE">Module</label>
                                    <div class="portal-form__control">
                                        <select id="P3_MODULE" name="P3_MODULE" class="form-control portal-select2" data-placeholder="Select module…">
                                            <option value=""></option>
                                            @foreach ($module as $mod)
                                                <option value="{{ $mod->name }}" @if ($mod->name === $data->module) selected @endif>
                                                    {{ $mod->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="P3_COMPL_TYPE">Problem Type</label>
                                    <select id="P3_COMPL_TYPE" name="P3_COMPL_TYPE" class="form-control">
                                        <option value=""></option>
                                        @foreach (['DB_Object', 'Form', 'Graph', 'Others', 'Report', 'Tables', 'Views'] as $type)
                                            <option value="{{ $type }}" @if ($data->complaint_type === $type) selected @endif>{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="P3_ERROR_TYPE" id="P3_ERROR_TYPE_LABEL">Complaint Type</label>
                                    <select id="P3_ERROR_TYPE" name="P3_ERROR_TYPE" class="form-control">
                                        <option value=""></option>
                                        <option value="DP" @if ($data->error_type === 'DP') selected @endif>Database Problem</option>
                                        <option value="NR" @if ($data->error_type === 'NR') selected @endif>New Requirement</option>
                                        <option value="OT" @if ($data->error_type === 'OT') selected @endif>Others</option>
                                        <option value="SP" @if ($data->error_type === 'SP') selected @endif>Software Problem</option>
                                        <option value="ST" @if ($data->error_type === 'ST') selected @endif>Support</option>
                                        <option value="UP" @if ($data->error_type === 'UP') selected @endif>User Problem</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="P3_COMPL_LEVEL">Complaint Level</label>
                                    <select id="P3_COMPL_LEVEL" name="P3_COMPL_LEVEL" class="form-control">
                                        <option value=""></option>
                                        <option value="C" @if ($data->priority === 'C') selected @endif>Critical</option>
                                        <option value="L" @if ($data->priority === 'L') selected @endif>Low</option>
                                        <option value="M" @if ($data->priority === 'M') selected @endif>Medium</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="P3_STATUS_TYPE">Status</label>
                                    <select id="P3_STATUS_TYPE" name="P3_STATUS_TYPE" class="form-control">
                                        <option value=""></option>
                                        <option value="CL" @if ($data->status === 'CL') selected @endif>Cancel</option>
                                        <option value="CM" @if ($data->status === 'CM') selected @endif>Complete</option>
                                        <option value="HL" @if ($data->status === 'HL') selected @endif>Hold</option>
                                        <option value="PN" @if ($data->status === 'PN') selected @endif>Pending</option>
                                        <option value="SV" @if ($data->status === 'SV') selected @endif>Sent For Customer Verification</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="P8_TIME_TAKEN">Time Taken (hrs)</label>
                                    <input type="number" id="P8_TIME_TAKEN" name="P8_TIME_TAKEN" class="form-control"
                                        value="{{ $data->time_taken }}">
                                </div>
                            </div>
                        </div>

                        <div class="portal-form-section">
                            <h2 class="portal-form-section__title">Assignment &amp; dates</h2>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="P8_ASSIGN_TO">Assign To</label>
                                    <div class="portal-form__control">
                                        <select id="P8_ASSIGN_TO" name="P8_ASSIGN_TO" class="form-control portal-select2" data-placeholder="Select engineer…">
                                            <option value=""></option>
                                            @foreach ($assignto as $ass)
                                                <option value="{{ $ass->engineer_code }}" @if ($ass->engineer_code === $data->assigned_to) selected @endif>
                                                    {{ $ass->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="P3_CLOSE_DT_TYPE">Close Date</label>
                                    <input type="date" id="P3_CLOSE_DT_TYPE" name="P3_CLOSE_DT_TYPE" class="form-control"
                                        value="{{ !empty($data->closed_date) ? date('Y-m-d', strtotime($data->closed_date)) : '' }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="P8_CHANGE_DONE_BY">Change Done By</label>
                                    <div class="portal-form__control">
                                        <select id="P8_CHANGE_DONE_BY" name="P8_CHANGE_DONE_BY" class="form-control portal-select2" data-placeholder="Select engineer…">
                                            <option value=""></option>
                                            @foreach ($assignto as $ass)
                                                <option value="{{ $ass->engineer_code }}" @if ($ass->engineer_code === $data->changed_by) selected @endif>
                                                    {{ $ass->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="portal-form-section">
                            <h2 class="portal-form-section__title">Details</h2>
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label for="P3_PROBLEM_DESC">Problem Description</label>
                                    <textarea id="P3_PROBLEM_DESC" name="P3_PROBLEM_DESC" class="form-control" maxlength="500">{{ $data->problem_description }}</textarea>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="P8_REASON">Reason</label>
                                    <textarea id="P8_REASON" name="P8_REASON" class="form-control" maxlength="500">{{ $data->reason }}</textarea>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="P8_ACTION">Action</label>
                                    <textarea id="P8_ACTION" name="P8_ACTION" class="form-control" maxlength="500">{{ $data->action_taken ?? '' }}</textarea>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="P3_MAWAI_REMARKS">Remarks For Customer</label>
                                    <textarea id="P3_MAWAI_REMARKS" name="P3_MAWAI_REMARKS" class="form-control" maxlength="500">{{ $data->internal_remarks }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
