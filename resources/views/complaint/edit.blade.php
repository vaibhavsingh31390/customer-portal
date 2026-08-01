@include('include.topNav')

<div class="main-content d-flex flex-column portal-shell__main portal-main">

    <form action="{{ route('save.edit.complaint', ['id' => base64_encode($data->complaint_number)]) }}" id="complaintForm"
        method="post" enctype="multipart/form-data" class="portal-form">
        @csrf
        @include('include.formHeader', ['title' => 'Complaint Edit', 'subtitle' => 'View details and use the thread to reply or close', 'showSave' => false])

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
                                            <option value="{{ $mod->name }}" @if ($mod->name === $data->module) selected @endif>
                                                {{ $mod->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="P3_COMPL_DT"><span class="required text-danger">*</span> Complaint Date</label>
                                    <input type="date" id="P3_COMPL_DT" name="P3_COMPL_DT" class="form-control disabled"
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
                                    <label for="P3_COMPL_TYPE">Type</label>
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
                                    <label for="P3_UPLOAD">Upload</label>
                                    <input type="file" id="P3_UPLOAD" name="P3_UPLOAD" class="form-control">
                                    @php $attachment = $data->attachment_name ?? $data->file_name ?? null; @endphp
                                    @if ($attachment)
                                        <a class="d-inline-block mt-2"
                                            href="{{ route('download.complaint.file', ['filename' => basename($attachment)]) }}">Download file</a>
                                    @endif
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="P3_PROBLEM_DESC">Problem Description</label>
                                    <textarea id="P3_PROBLEM_DESC" name="P3_PROBLEM_DESC" class="form-control" maxlength="500">{{ $data->problem_description }}</textarea>
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
                                        <option value="CL" @if ($data->status === 'CL') selected @endif>Cancel</option>
                                        <option value="CM" @if ($data->status === 'CM') selected @endif>Complete</option>
                                        <option value="HL" @if ($data->status === 'HL') selected @endif>Hold</option>
                                        <option value="PN" @if ($data->status === 'PN') selected @endif>Pending</option>
                                        <option value="SV" @if ($data->status === 'SV') selected @endif>Sent For Customer Verification</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="P3_CLOSE_DT_TYPE">Close Date</label>
                                    <input type="date" id="P3_CLOSE_DT_TYPE" name="P3_CLOSE_DT_TYPE"
                                        class="form-control disabled"
                                        value="{{ !empty($data->closed_date) ? date('Y-m-d', strtotime($data->closed_date)) : '' }}" readonly>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="P3_MAWAI_REMARKS">Remarks For Customer</label>
                                    <textarea id="P3_MAWAI_REMARKS" name="P3_MAWAI_REMARKS" class="form-control disabled" maxlength="500" readonly>{{ $data->internal_remarks }}</textarea>
                                    <small class="form-text text-muted">Post customer-facing remarks in the communication thread below.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @include('complaint._thread')
</div>
