<?php

namespace App\Http\Complaint;

use App\Mail\ComplaintCreatedMail;
use App\Models\CustomerComplaint;
use App\Support\SqlHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class ComplaintController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function showComplaints()
    {
        return view('dashboard.complaintList');
    }

    public function getTableData(Request $request)
    {
        $search = $request->input('search');

        if ($request->client != 'false') {
            $user = $request->session()->get('user');
            $query = CustomerComplaint::where('client_code', $user->user_code);
        } else {
            $query = CustomerComplaint::query();
        }

        $columns = Schema::getColumnListing((new CustomerComplaint)->getTable());
        if ($search) {
            $query->where(function (Builder $q) use ($columns, $search) {
                $lowerSearch = mb_strtolower($search, 'UTF-8');
                foreach ($columns as $column) {
                    $q->orWhereRaw('LOWER('.$column.') like ?', ["%{$lowerSearch}%"]);
                }
            });
        }
        $db_data = $query->where('status', 'PN')->orderBy('complaint_date', 'DESC')
            ->orderBy($request->sorting, $request->order)
            ->paginate($request->per_page);

        $count = $db_data->count();
        $number_of_page = $db_data->lastPage();
        $currentPage = $db_data->currentPage();
        $total_items = $db_data->total();
        $displayfrom = ($currentPage - 1) * $request->per_page + 1;
        $displayto = ($currentPage - 1) * $request->per_page + $count;
        $pagination = '';

        if ($number_of_page > 1) {
            $prv = $currentPage - 1;
            $next = $currentPage + 1;
            if ($next > $number_of_page) {
                $next = $number_of_page;
            }
            $pagination .= '
            <div class="col-sm-12 col-md-6">
                <div class="dataTables_paginate paging_simple_numbers" id="dataTable_paginate">
                    <ul class="pagination">
                     <li class="paginate_button page-item previous page-link"
                            id="dataTable_previous" onclick="return getTableDataByPage(1);">
                               <i class="lni lni-angle-double-left"></i></a>
                        </li>
                        <li class="paginate_button page-item previous page-link"
                            id="dataTable_previous" onclick="return getTableDataByPage(' . $prv . ');">
                            <i class="lni lni-chevron-left"></i></a>
                        </li>
                        <li class="paginate_button page-item active page-link disabled" id="current_page">' . $currentPage . '</li>
                         <li class="paginate_button page-item next page-link"
                            id="dataTable_previous" onclick="return getTableDataByPage(' . $next . ');">
                            <i class="lni lni-chevron-right"></i>
                        </li>
                        <li class="paginate_button page-item last page-link" id="dataTable_next"
                        onclick="return getTableDataByPage(' . $number_of_page . ');">
                        <i class="lni lni-angle-double-right"></i></li>
                    </ul>
                    </div>
            </div>
            <div class="col-sm-12 col-md-6">
                    <ul class="pagination">
                        <li class="paginate_button page-item last page-link">
                        Showing ' . (int) $displayfrom . ' of ' . ceil($total_items) . ' of  entries
                        </li>
                    </ul>
            </div>';
        }

        if ($count == 0) {
            return response()->json(['status' => 0, 'data' => $db_data, 'pagination' => $pagination, 'msg' => 'Data Not Found!']);
        }

        $records = '';
        foreach ($db_data as $row) {
            $link = base64_encode($row['complaint_number']);
            $clientCode = $row['client_code'];
            $assignedTo = $row['assigned_to'];

            $customer_name = SqlHelper::select(
                'SELECT name FROM '.SqlHelper::table(SqlHelper::TABLE_CLIENTS)." WHERE erp_vertical = 'TERMS' AND client_code = ? ORDER BY 1",
                [$clientCode]
            );
            $style = strlen($row['problem_description']) > 100 ? ' style="min-width: 500px;"' : ' style=""';
            $assignto = SqlHelper::select(
                'SELECT name, engineer_code FROM '.SqlHelper::table(SqlHelper::TABLE_ENGINEERS)." WHERE working_status = 'WK' AND department = 'SWE' AND engineer_code = ? ORDER BY 1",
                [$assignedTo]
            );
            $assignedLabel = $assignedTo ? " ($assignedTo)" : '';
            $records .= '<tr class="">
                <td><a href="/complaint/edit/' . $link . '">' . $row['complaint_number'] . '</a></td>
                <td>' . (! empty($row['complaint_date']) ? date('d-m-Y', strtotime($row['complaint_date'])) : '-') . '</td>
                <td>' . $clientCode . '</td>
                <td>' . @$customer_name[0]->name . '</td>
                <td>' . $row['status'] . '</td>
                <td>' . $row['module'] . '</td>
                <td>' . $row['complaint_type'] . '</td>
                <td>' . $row['error_type'] . '</td>
                <td' . $style . '>' . $row['problem_description'] . '</td>
                <td>' . (! empty($row['closed_date']) ? date('d-m-Y', strtotime($row['closed_date'])) : '-') . '</td>
                <td>' . @$assignto[0]->name . $assignedLabel . '</td>
                </tr>';
        }

        return response()->json(['displayfrom' => $displayfrom, 'displayto' => $displayto, 'total_items' => $total_items, 'status' => 1, 'data' => $records, 'pagination' => $pagination, 'msg' => 'This is matched data!']);
    }

    public function showCreateComplaints()
    {
        $module = SqlHelper::select('SELECT name FROM '.SqlHelper::table(SqlHelper::TABLE_SAP_MODULES)." WHERE department_module = 'TERMS' ORDER BY 1");
        $customer = SqlHelper::select('SELECT name, client_code, email FROM '.SqlHelper::table(SqlHelper::TABLE_CLIENTS)." WHERE erp_vertical = 'TERMS' ORDER BY 1");
        $assignto = SqlHelper::select('SELECT name, engineer_code FROM '.SqlHelper::table(SqlHelper::TABLE_ENGINEERS)." WHERE working_status = 'WK' AND department = 'SWE' ORDER BY 1");

        return view('dashboard.complaintCreate', ['module' => $module, 'customer' => $customer, 'assignto' => $assignto]);
    }

    public function saveCreateComplaints(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'P3_COMPL_DT' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['type' => 0, 'message' => $validator->errors()->all()]);
        }

        DB::beginTransaction();

        try {
            $complaintNumber = SqlHelper::selectOne(SqlHelper::genComplaintNoQuery())?->data;

            $data = [
                'complaint_number' => $complaintNumber,
                'complaint_date' => $request->input('P3_COMPL_DT'),
                'client_code' => $request->input('P3_CUST_CD') ?? $request->input('P8_CUST_NAME'),
                'module' => $request->input('P3_MODULE'),
                'complaint_type' => $request->input('P3_COMPL_TYPE'),
                'error_type' => $request->input('P3_ERROR_TYPE'),
                'problem_description' => $request->input('P3_PROBLEM_DESC'),
                'priority' => $request->input('P3_COMPL_LEVEL'),
                'status' => $request->input('P3_STATUS_TYPE'),
                'internal_remarks' => $request->input('P3_MAWAI_REMARKS'),
                'contact_name' => $request->input('P3_USER_NAME'),
                'contact_email' => $request->input('P3_CONTACT_MAIL_ID'),
                'time_taken' => $request->input('P8_TIME_TAKEN'),
                'closed_date' => $request->input('P3_CLOSE_DT_TYPE'),
                'assigned_to' => $request->input('P8_ASSIGN_TO'),
                'changed_by' => $request->input('P8_CHANGE_DONE_BY'),
                'reason' => $request->input('P8_REASON'),
                'action_taken' => $request->input('P8_ACTION'),
            ];

            if ($request->hasFile('P3_UPLOAD')) {
                $file = $request->file('P3_UPLOAD');
                $filename = time() . '_' . $file->getClientOriginalName();

                $uploadPath = public_path('uploads');
                if (! file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $file->move($uploadPath, $filename);

                $data['attachment_name'] = '/uploads/' . $filename;
            }

            CustomerComplaint::create(array_merge($data, [
                'id' => 'COMP-' . str_replace(['CP', '-'], '', $complaintNumber),
            ]));
            DB::commit();

            $mailTo = $request->input('P3_CONTACT_MAIL_ID');
            $cc = env('MAIL_CC');
            if ($mailTo != '') {
                $user = $request->session()->get('user');
                if (! ($user->user_code && preg_match('/^S/', $user->user_code))) {
                    $mailTo = env('MAIL_CC');
                    $cc = env('P3_CONTACT_MAIL_ID');
                }
                $email = Mail::to($mailTo);
                if (! empty($cc)) {
                    $email->cc($cc);
                }
                $email->send(new ComplaintCreatedMail($data));
            }

            return response()->json(['type' => 1, 'message' => 'Complaint created successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['type' => 0, 'message' => $e->getMessage()]);
        }
    }

    public function showEditComplaints($id)
    {
        $id = base64_decode($id);
        $data = SqlHelper::selectOne(
            'SELECT * FROM '.SqlHelper::table(SqlHelper::TABLE_COMPLAINTS).' WHERE complaint_number = ?',
            [$id]
        );

        if (! $data) {
            abort(404);
        }

        $clientCode = $data->client_code;
        $module = SqlHelper::select('SELECT name FROM '.SqlHelper::table(SqlHelper::TABLE_SAP_MODULES)." WHERE department_module = 'TERMS' ORDER BY 1");
        $customer = SqlHelper::select('SELECT name, client_code, email FROM '.SqlHelper::table(SqlHelper::TABLE_CLIENTS)." WHERE erp_vertical = 'TERMS' ORDER BY 1");
        $customer_name = SqlHelper::selectOne(
            'SELECT name FROM '.SqlHelper::table(SqlHelper::TABLE_CLIENTS)." WHERE erp_vertical = 'TERMS' AND client_code = ? ORDER BY 1",
            [$clientCode]
        );
        $assignto = SqlHelper::select('SELECT name, engineer_code FROM '.SqlHelper::table(SqlHelper::TABLE_ENGINEERS)." WHERE working_status = 'WK' AND department = 'SWE' ORDER BY 1");

        return view('dashboard.complaintEdit', [
            'module' => $module,
            'customer' => $customer,
            'assignto' => $assignto,
            'data' => $data,
            'customer_name' => $customer_name,
        ]);
    }

    public function saveEditComplaints(Request $request, $id)
    {
        $id = base64_decode($id);
        $validator = Validator::make($request->all(), [
            'P3_COMPL_DT' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['type' => 0, 'message' => $validator->errors()->all()]);
        }

        DB::beginTransaction();

        try {
            CustomerComplaint::where('complaint_number', $id)->update([
                'complaint_date' => $request->input('P3_COMPL_DT'),
                'client_code' => $request->input('P3_CUST_CD') ?? $request->input('P8_CUST_NAME'),
                'module' => $request->input('P3_MODULE'),
                'complaint_type' => $request->input('P3_COMPL_TYPE'),
                'error_type' => $request->input('P3_ERROR_TYPE'),
                'problem_description' => $request->input('P3_PROBLEM_DESC'),
                'priority' => $request->input('P3_COMPL_LEVEL'),
                'status' => $request->input('P3_STATUS_TYPE'),
                'internal_remarks' => $request->input('P3_MAWAI_REMARKS'),
                'contact_name' => $request->input('P3_USER_NAME'),
                'contact_email' => $request->input('P3_CONTACT_MAIL_ID'),
                'time_taken' => $request->input('P8_TIME_TAKEN'),
                'closed_date' => $request->input('P3_CLOSE_DT_TYPE'),
                'assigned_to' => $request->input('P8_ASSIGN_TO'),
                'changed_by' => $request->input('P8_CHANGE_DONE_BY'),
                'reason' => $request->input('P8_REASON'),
                'action_taken' => $request->input('P8_ACTION'),
            ]);

            DB::commit();

            return response()->json(['type' => 1, 'message' => 'Complaint updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['type' => 0, 'message' => $e->getMessage()]);
        }
    }

    public function downloadComplaintFile($filename)
    {
        $filePath = public_path('uploads/' . $filename);
        if (file_exists($filePath)) {
            return response()->download($filePath);
        }

        abort(404, 'File not found.');
    }
}
