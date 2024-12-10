@include('include.topNav')

<div class="main-content d-flex flex-column hide-sidemenu">
    <!-- Main Content Header -->

    <form action="{{ route('save.edit.complaint', ['id' => base64_encode($data->complaint_no)]) }}" id="complaintForm"
        method="post" enctype="multipart/form-data">
        @csrf
        <div class="main-content-header">
            <h1>Complaint Edit</h1>

            <div class="text-left text-md-right text-lg-right w-70 mt-2 mt-sm-0 action-btn">
                <a href="{{ route('dashboard') }}" class="btn mr-3 p-0">
                    <i data-feather="home" class="icon"></i>
                </a>
                <button class="btn btn-secondary mr-2" id="backButton" type="button">Cancel</button>
                <button class="btn btn-primary" id="submitComplaint" type="submit" data-dismiss="modal">
                    Save
                    <span class="loader-btn"></span>
                </button>
            </div>
        </div>
        <!-- End Main Content Header -->

        <div class="row">
            <div class="col-xl-12">
                <div class="card mb-30">
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="P3_MODULE">Module</label>
                                <select id="P3_MODULE" name="P3_MODULE" class="form-control">
                                    <option value=""></option>
                                    @foreach ($module as $mod)
                                        <option value="{{ $mod->m1 }}"
                                            @if ($mod->m1 === $data->module) selected @endif>
                                            {{ $mod->module_text }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="P3_COMPL_DT"><span class="required text-danger">*</span>Complaint
                                    Date</label>
                                <input type="date" id="P3_COMPL_DT" name="P3_COMPL_DT" class="form-control  disabled"
                                    value="{{ !empty($data->compl_dt) ? date('Y-m-d', strtotime($data->compl_dt)) : '' }}">
                            </div>

                            <div class="form-group col-md-4">
                                <label for="P3_CONTACT_MAIL_ID">Customer Email</label>
                                <input type="email" id="P3_CONTACT_MAIL_ID" name="P3_CONTACT_MAIL_ID"
                                    class="form-control" value="{{ $data->contact_mail_id }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="P3_USER_NAME">Complaint By</label>
                                <input type="text" id="P3_USER_NAME" name="P3_USER_NAME" class="form-control "
                                    value="{{ $data->user_name }}">
                            </div>

                            <div class="form-group col-md-4">
                                <label for="P3_COMPL_TYPE">Type</label>
                                <select id="P3_COMPL_TYPE" name="P3_COMPL_TYPE" class="form-control">
                                    <option value=""></option>
                                    <option value="DB_Object" @if ($data->compl_type === 'DB_Object') selected @endif>
                                        DB_Object</option>
                                    <option value="Form" @if ($data->compl_type === 'Form') selected @endif>Form
                                    </option>
                                    <option value="Graph" @if ($data->compl_type === 'Graph') selected @endif>
                                        Graph</option>
                                    <option value="Others" @if ($data->compl_type === 'Others') selected @endif>
                                        Others</option>
                                    <option value="Report" @if ($data->compl_type === 'Report') selected @endif>
                                        Report</option>
                                    <option value="Tables" @if ($data->compl_type === 'Tables') selected @endif>
                                        Tables</option>
                                    <option value="Views" @if ($data->compl_type === 'Views') selected @endif>
                                        Views</option>
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="P3_ERROR_TYPE" id="P3_ERROR_TYPE_LABEL">Complaint Type
                                </label>
                                <select id="P3_ERROR_TYPE" name="P3_ERROR_TYPE" class="form-control form-control-user">
                                    <option value=""></option>
                                    <option value="DP" @if ($data->error_type === 'DP') selected @endif>
                                        Database Problem</option>
                                    <option value="NR" @if ($data->error_type === 'NR') selected @endif>New
                                        Requirement</option>
                                    <option value="OT" @if ($data->error_type === 'OT') selected @endif>
                                        Others</option>
                                    <option value="SP" @if ($data->error_type === 'SP') selected @endif>
                                        Software Problem</option>
                                    <option value="ST" @if ($data->error_type === 'ST') selected @endif>
                                        Support</option>
                                    <option value="UP" @if ($data->error_type === 'UP') selected @endif>
                                        User Problem
                                    </option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="P3_PROBLEM_DESC">Problem</label>
                                <textarea id="P3_PROBLEM_DESC" name="P3_PROBLEM_DESC" class="form-control" maxlength="500">{{ $data->problem_desc }}</textarea>
                            </div>
                            {{-- <div class="form-group col-md-4">
                                <label for="P3_MAWAI_REMARKS">Remarks</label>
                                <textarea id="P3_MAWAI_REMARKS" name="P3_MAWAI_REMARKS" class="form-control" maxlength="500">{{ $data->mawai_remarks }}</textarea>
                            </div> --}}

                            <div class="form-group col-md-4">
                                <label for="P3_COMPL_LEVEL">Complaint L.</label>
                                <select id="P3_COMPL_LEVEL" name="P3_COMPL_LEVEL" class="form-control">
                                    <option value="">
                                    </option>
                                    <option value="C" @if ($data->compl_level === 'C') selected @endif>
                                        Critical</option>
                                    <option value="L" @if ($data->compl_level === 'L') selected @endif>Low
                                    </option>
                                    <option value="M" @if ($data->compl_level === 'M') selected @endif>
                                        Medium</option>
                                </select>
                            </div>

                            {{-- <div class="form-group col-md-4">
                                <label for="P3_CUST_CD">Customer</label>
                                <input type="text" id="P3_CUST_CD" name="P3_CUST_CD"
                                    class="form-control  disabled" value="{{ $data->cust_cd }}">
                            </div>

                            <div class="form-group col-md-4">
                                <label for="P3_COMPL_ID">Complaint ID</label>
                                <input type="text" id="P3_COMPL_ID" name="P3_COMPL_ID"
                                    class="form-control  disabled" value="{{ $data->complaint_no }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="P3_UPLOADID">Module</label>
                                <input type="text" id="P3_UPLOADID" name="P3_UPLOADID" class="form-control">
                            </div> --}}

                            <div class="form-group col-md-4">
                                <label for="P3_UPLOAD">Upload</label>
                                <input type="file" id="P3_UPLOAD" name="P3_UPLOAD" class="form-control">
                                <span>
                                    @if ($data->filename)
                                        <a class="mt-2"
                                            href="{{ route('download.complaint.file', ['filename' => basename($data->filename)]) }}">Download
                                            File</a>
                                    @else
                                        {{-- <p class="mt-2">No file uploaded for this complaint.</p> --}}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="card mb-30">
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="P3_MAWAI_REMARKS">Remarks For Customer</label>
                                <textarea id="P3_MAWAI_REMARKS" name="P3_MAWAI_REMARKS" class="form-control disabled" maxlength="500">{{ $data->mawai_remarks }}</textarea>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="P3_STATUS_TYPE">Status</label>
                                <select id="P3_STATUS_TYPE" name="P3_STATUS_TYPE"
                                    class="form-control form-control-user disabled">
                                    <option value=""></option>
                                    <option value="CL" @if ($data->status === 'CL') selected @endif>
                                        Cancel</option>
                                    <option value="CM" @if ($data->status === 'CM') selected @endif>
                                        Complete</option>
                                    <option value="HL" @if ($data->status === 'HL') selected @endif>
                                        Hold</option>
                                    <option value="PN" @if ($data->status === 'PN') selected @endif>
                                        Pending
                                    </option>
                                    <option value="SV" @if ($data->status === 'SV') selected @endif>
                                        Sent For Customer Verification
                                    </option>
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="P3_CLOSE_DT_TYPE">Close Dt</label>
                                <input type="date" id="P3_CLOSE_DT_TYPE" name="P3_CLOSE_DT_TYPE"
                                    class="form-control disabled"
                                    value="{{ !empty($data->close_dt) ? date('Y-m-d', strtotime($data->close_dt)) : '' }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<!-- End of Content Wrapper -->
