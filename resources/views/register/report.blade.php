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
                            use App\Support\SqlHelper;

                            $customerRow = SqlHelper::selectOne(
                                'SELECT name FROM '.SqlHelper::table(SqlHelper::TABLE_CLIENTS)." WHERE erp_vertical = 'TERMS' AND client_code = ? ORDER BY 1",
                                [$data->client_code]
                            );
                            $moduleRow = SqlHelper::selectOne(
                                'SELECT name FROM '.SqlHelper::table(SqlHelper::TABLE_SAP_MODULES)." WHERE department_module = 'TERMS' AND name = ?",
                                [$data->module],
                            );
                            $module = $moduleRow->name ?? '-';

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
                        <td>{{ $data->complaint_number }}</td>
                        <td>{{ !empty($data->complaint_date) ? date('d-m-Y', strtotime($data->complaint_date)) : '-' }}</td>
                        <td>{{ $customerRow->name ?? '-' }}</td>
                        <td>{{ $STATUS[$data->status] }}</td>
                        <td>{{ $module }}</td>
                        <td>{{ $COMPL_TYPE[$data->complaint_type] }}</td>
                        <td>{{ $ERROR_TYPE[$data->error_type] }}</td>
                        <td>{{ $data->problem_description }}</td>
                        <td>{{ $data->reason }}</td>
                        <td>{{ $data->action_taken }}</td>
                        <td>{{ !empty($data->closed_date) ? date('d-m-Y', strtotime($data->closed_date)) : '-' }}</td>
                        <td>{{ $data->contact_name }}</td>
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
