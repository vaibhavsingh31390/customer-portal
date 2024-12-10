<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Complaint Created</title>
</head>

<body style="font-family: Arial, sans-serif; font-size: 14px; margin: 0; padding: 0;">
    <h2 style="font-size: 14px;">Dear Sir,</h2>
    <table align="center" width="600" style="border-collapse: collapse; margin: 20px auto; width: 600px;">
        <tr>
            <td>

                <table width="100%" style="border-collapse: collapse; border: 1px solid #000; font-size: 12px;">
                    <tr>
                        <th style="border: 1px solid #000; padding: 3px; text-align: left; background-color: #f2f2f2;">
                            Customer Code and Name :</th>
                        <td style="border: 1px solid #000; padding: 3px; text-align: left;">
                            {{ $complaintData['CUSTOMER_NAME'] }} ({{ $complaintData['CUST_CD'] }})</td>
                    </tr>
                    <tr>
                        <th style="border: 1px solid #000; padding: 3px; text-align: left; background-color: #f2f2f2;">
                            Email Id :</th>
                        <td style="border: 1px solid #000; padding: 3px; text-align: left;">
                            @if ($complaintData['CONTACT_MAIL_ID'] != '')
                                <a href="mailto:{{ $complaintData['CONTACT_MAIL_ID'] }}">
                                    {{ $complaintData['CONTACT_MAIL_ID'] }}
                                </a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th style="border: 1px solid #000; padding: 3px; text-align: left; background-color: #f2f2f2;">
                            Complaint Date :</th>
                        <td style="border: 1px solid #000; padding: 3px; text-align: left;">
                            {{ $complaintData['COMPL_DT'] }}</td>
                    </tr>
                    <tr>
                        <th style="border: 1px solid #000; padding: 3px; text-align: left; background-color: #f2f2f2;">
                            Module :</th>
                        <td style="border: 1px solid #000; padding: 3px; text-align: left;">
                            {{ $complaintData['MODULE_NAME'] }}</td>
                    </tr>
                    <tr>
                        <th style="border: 1px solid #000; padding: 3px; text-align: left; background-color: #f2f2f2;">
                            Complaint By :</th>
                        <td style="border: 1px solid #000; padding: 3px; text-align: left;">
                            {{ $complaintData['USER_NAME'] }}</td>
                    </tr>
                    <tr>
                        <th style="border: 1px solid #000; padding: 3px; text-align: left; background-color: #f2f2f2;">
                            Problem Type :</th>
                        <td style="border: 1px solid #000; padding: 3px; text-align: left;">
                            {{ $complaintData['COMPL_TYPE_NAME'] }}</td>
                    </tr>
                    <tr>
                        <th style="border: 1px solid #000; padding: 3px; text-align: left; background-color: #f2f2f2;">
                            Complaint Type :</th>
                        <td style="border: 1px solid #000; padding: 3px; text-align: left;">
                            {{ $complaintData['ERROR_TYPE_NAME'] }}</td>
                    </tr>
                    <tr>
                        <th style="border: 1px solid #000; padding: 3px; text-align: left; background-color: #f2f2f2;">
                            Problem Description :</th>
                        <td style="border: 1px solid #000; padding: 3px; text-align: left;">
                            {{ $complaintData['PROBLEM_DESC'] }}</td>
                    </tr>
                    <tr>
                        <th style="border: 1px solid #000; padding: 3px; text-align: left; background-color: #f2f2f2;">
                            Remarks :</th>
                        <td style="border: 1px solid #000; padding: 3px; text-align: left;">
                            {{ $complaintData['PROBLEM_DESC'] }}</td>
                    </tr>
                    <tr>
                        <th style="border: 1px solid #000; padding: 3px; text-align: left; background-color: #f2f2f2;">
                            Problem Level :</th>
                        <td style="border: 1px solid #000; padding: 3px; text-align: left;">
                            {{ $complaintData['COMPL_LEVEL_NAME'] }}</td>
                    </tr>
                </table>


            </td>
        </tr>
    </table>
    <p>Your complaint has been logged in our system, your complaint number is:
        {{ $complaintData['CUST_CD'] }}.
        <br> Our
        consultant will contact you as soon as possible.
    </p>

    <h2 style="font-size: 14px;">Thanks & Regards<br>Mawai Support</h2>
</body>

</html>
