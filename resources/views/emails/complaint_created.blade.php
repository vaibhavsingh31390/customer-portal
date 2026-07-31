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
                            {{ $complaintData['customer_name'] }} ({{ $complaintData['client_code'] }})</td>
                    </tr>
                    <tr>
                        <th style="border: 1px solid #000; padding: 3px; text-align: left; background-color: #f2f2f2;">
                            Email Id :</th>
                        <td style="border: 1px solid #000; padding: 3px; text-align: left;">
                            @if ($complaintData['contact_email'] != '')
                                <a href="mailto:{{ $complaintData['contact_email'] }}">
                                    {{ $complaintData['contact_email'] }}
                                </a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th style="border: 1px solid #000; padding: 3px; text-align: left; background-color: #f2f2f2;">
                            Complaint Date :</th>
                        <td style="border: 1px solid #000; padding: 3px; text-align: left;">
                            {{ $complaintData['complaint_date'] }}</td>
                    </tr>
                    <tr>
                        <th style="border: 1px solid #000; padding: 3px; text-align: left; background-color: #f2f2f2;">
                            Module :</th>
                        <td style="border: 1px solid #000; padding: 3px; text-align: left;">
                            {{ $complaintData['module_name'] }}</td>
                    </tr>
                    <tr>
                        <th style="border: 1px solid #000; padding: 3px; text-align: left; background-color: #f2f2f2;">
                            Complaint By :</th>
                        <td style="border: 1px solid #000; padding: 3px; text-align: left;">
                            {{ $complaintData['contact_name'] }}</td>
                    </tr>
                    <tr>
                        <th style="border: 1px solid #000; padding: 3px; text-align: left; background-color: #f2f2f2;">
                            Problem Type :</th>
                        <td style="border: 1px solid #000; padding: 3px; text-align: left;">
                            {{ $complaintData['complaint_type_name'] }}</td>
                    </tr>
                    <tr>
                        <th style="border: 1px solid #000; padding: 3px; text-align: left; background-color: #f2f2f2;">
                            Complaint Type :</th>
                        <td style="border: 1px solid #000; padding: 3px; text-align: left;">
                            {{ $complaintData['error_type_name'] }}</td>
                    </tr>
                    <tr>
                        <th style="border: 1px solid #000; padding: 3px; text-align: left; background-color: #f2f2f2;">
                            Problem Description :</th>
                        <td style="border: 1px solid #000; padding: 3px; text-align: left;">
                            {{ $complaintData['problem_description'] }}</td>
                    </tr>
                    <tr>
                        <th style="border: 1px solid #000; padding: 3px; text-align: left; background-color: #f2f2f2;">
                            Remarks :</th>
                        <td style="border: 1px solid #000; padding: 3px; text-align: left;">
                            {{ $complaintData['internal_remarks'] }}</td>
                    </tr>
                    <tr>
                        <th style="border: 1px solid #000; padding: 3px; text-align: left; background-color: #f2f2f2;">
                            Problem Level :</th>
                        <td style="border: 1px solid #000; padding: 3px; text-align: left;">
                            {{ $complaintData['priority_name'] }}</td>
                    </tr>
                </table>


            </td>
        </tr>
    </table>
    <p>Your complaint has been logged in our system, your complaint number is:
        {{ $complaintData['complaint_number'] }}.
        <br> Our
        consultant will contact you as soon as possible.
    </p>

    <h2 style="font-size: 14px;">Thanks & Regards<br>heyvai.dev Support</h2>
</body>

</html>
