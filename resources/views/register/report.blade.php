<!DOCTYPE html>
<html>

<head>
    <style>
        /* General styling */
        body {
            font-family: Arial, sans-serif;
            color: #000;
            margin: 0;
            padding: 0;
        }

        /* Table styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            font-size: 11px;
            padding: 4px;
            min-width: 50px;
        }

        @page {
            margin: 50px 10px 15px;
            size: A4 landscape;
        }

        /* Header styling */
        header {
            position: fixed;
            text-align: center;
            top: -25px;
            left: 0;
            transform: translateX(-50%);
            text-align: center;
            margin-left: 50%;
        }

        main {
            margin-top: 0px;
            padding: 0 50px;
        }
    </style>
</head>

<body>
    <header>
        Complaint Register Report
        @if (!empty($date_from) && !empty($date_to))
            ({{ $date_from }} - {{ $date_to }})
        @endif
    </header>
    <main>
        <table>
            <thead>
                <tr>
                    <th>Complaint No</th>
                    <th>Compl Dt</th>
                    <th>Cust name</th>
                    <th>Status</th>
                    <th>Module</th>
                    <th>Compl Type</th>
                    <th>Error Type</th>
                    <th>Problem Desc</th>
                    <th>Reason</th>
                    <th>Action</th>
                    <th>Close Dt</th>
                    <th>User Name</th>
                </tr>
            </thead>
            <tbody>
                <!-- Replace with your Blade template foreach loop -->
                @foreach ($datas as $data)
                    <tr>
                        @php
                            $customer_name = DB::select(
                                "SELECT CLIENT_NAME FROM CELINT_MASTER WHERE ERP_VERT='TERMS' AND CLIENT_CD = '$data->cust_cd' ORDER BY 1",
                            );
                            // Query for MODULE_NAME
                            $moduleResult = DB::select(
                                "SELECT MODULE_TEXT FROM SAP_MODULE_DTL WHERE DEPT_MODULE='TERMS' AND MODULE_TEXT = ?",
                                [$data->module],
                            );
                            $module = !empty($moduleResult) ? $moduleResult[0]->module_text : '-';

                            // Define the lookup arrays
                            $COMPL_TYPE = [
                                'DB_Object' => 'DB Object',
                                'Form' => 'Form',
                                'Graph' => 'Graph',
                                'Others' => 'Others',
                                'Report' => 'Report',
                                'Tables' => 'Tables',
                                'Views' => 'Views',
                            ];
                            $ERROR_TYPE = [
                                'DP' => 'Database Problem',
                                'NR' => 'New Requirement',
                                'OT' => 'Others',
                                'SP' => 'Software Problem',
                                'ST' => 'Support',
                                'UP' => 'User Problem',
                            ];
                            $COMPL_LEVEL = [
                                'C' => 'Critical',
                                'L' => 'Low',
                                'M' => 'Medium',
                            ];
                            $STATUS = [
                                'CL' => 'Cancel',
                                'CM' => 'Complete',
                                'HL' => 'Hold',
                                'PN' => 'Pending',
                                'SV' => 'Sent For Customer Verification',
                            ];
                        @endphp
                        <td>{{ $data->complaint_no }}</td>
                        <td>{{ !empty($data->compl_dt) ? date('d-m-Y', strtotime($data->compl_dt)) : '-' }}</td>
                        <td>{{ @$customer_name[0]->client_name }}</td>
                        <td>{{ $STATUS[$data->status] }}</td>
                        <td>{{ $module }}</td>
                        <td>{{ $COMPL_TYPE[$data->compl_type] }}</td>
                        <td>{{ $ERROR_TYPE[$data->error_type] }}</td>
                        <td>{{ $data->problem_desc }}</td>
                        <td>{{ $data->reason }}</td>
                        <td>{{ $data->action }}</td>
                        <td>{{ !empty($data->close_dt) ? date('d-m-Y', strtotime($data->close_dt)) : '-' }}</td>
                        <td>{{ $data->user_name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </main>
    <footer>
        {{-- Page {PAGENO} --}}
    </footer>
</body>

</html>
