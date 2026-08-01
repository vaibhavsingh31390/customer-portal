@extends('base.base')

@section('title', 'Dashboard')

@section('styles')
@endsection

@section('content')
    @php use App\Support\UserRole; @endphp
    @if (UserRole::isStaff(Session::get('user')->user_code))
        @include(UserRole::isAdmin(Session::get('user')->user_code) ? 'include.sidebarAdmin' : 'include.sidebarSupport')
        @include('complaint.editSupport')
    @else
        @include('include.sidebar')
        @include('complaint.edit')
    @endif
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            if (window.PortalUI && window.PortalUI.initSelect2) {
                window.PortalUI.initSelect2('.portal-select2');
            }

            $('#resetFieldsRegister').click(function(e) {
                console.log('yes');
                $('#date_from').val('');
                $('#date_to').val('');
            });

            $('#backButton').click(function() {
                history.back();
            });


            $('#P8_CUST_CD').on('change', function() {
                var selectedOption = $(this).val();
                $('#P8_CUST_NAME').val(selectedOption);
                $('#P3_CONTACT_MAIL_ID').val($(this).find("option:selected").attr('data-src'));
            });

            function validateFields(fields) {
                var isValid = true;
                fields.forEach(function(field) {
                    var element = $('#' + field);

                    // Check if element exists in DOM
                    if (element.length > 0) {
                        if (element.is('textarea')) {
                            if (!element.val()) {
                                element.addClass('is-invalid');
                                isValid = false;
                            } else {
                                element.removeClass('is-invalid');
                            }
                        } else {
                            if (!element.val().trim()) {
                                element.addClass('is-invalid');
                                isValid = false;
                            } else {
                                element.removeClass('is-invalid');
                            }
                        }
                    }
                });
                return isValid;
            }


            $('#submitComplaint').on('click', function(e) {
                e.preventDefault();

                let fieldsToValidate = [
                    // 'P3_MODULE',
                    'P3_COMPL_DT',
                    // 'P3_CONTACT_MAIL_ID',
                    // 'P3_USER_NAME',
                    // 'P3_COMPL_TYPE',
                    // 'P3_ERROR_TYPE',
                    // 'P3_PROBLEM_DESC',
                    // 'P3_MAWAI_REMARKS',
                    // 'P3_COMPL_LEVEL',
                    // 'P3_STATUS_TYPE',
                    // 'P3_CLOSE_DT_TYPE',
                    // 'P3_CUST_CD',
                    // 'P3_COMPL_ID',
                    // 'P3_UPLOADID',
                    // 'P3_UPLOAD'

                    // 'P8_CUST_CD',
                    // 'P8_CUST_NAME',
                    // 'P8_TIME_TAKEN',
                    // 'P8_ASSIGN_TO',
                    // 'P8_CHANGE_DONE_BY',
                    // 'P8_REASON',
                    // 'P8_ACTION',
                ];

                // Validate the fields
                if (validateFields(fieldsToValidate)) {
                    let formData = new FormData($('#complaintForm')[0]);
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: $('#complaintForm').attr('action'),
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.type) {
                                navigateBackWithToast(response.message, true, @json(route('complaint')));
                            } else {
                                showToast(response.message, false);
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.log('Error submitting complaint: ' + errorThrown);
                        }
                    });
                } else {
                    showToast('Please fill all the required fields.', false);
                }
            });
        });
    </script>

    <script src="{{ asset('assets/js/complaint-thread.js') }}"></script>

@endsection
