<?php

namespace App\Http\Complaint;

use App\Mail\ComplaintCreatedMail;
use App\Models\CustomerComplaint;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class ComplaintController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function showComplaints()
    {
        return view('dashboard.complaintList');
    }

    public function getTableData(Request $request)
    {
        $search = $request->input('search');

        if ($request->client != "false") {
            $user =  $request->session()->get('user');
            $query = CustomerComplaint::where('CUST_CD', $user->eng_cd);
        } else {
            $query = CustomerComplaint::query();
        }

        $columns = Schema::getColumnListing((new CustomerComplaint)->getTable());
        if ($search) {
            $query->where(function (Builder $q) use ($columns, $search) {
                $lowerSearch = mb_strtolower($search, 'UTF-8');
                foreach ($columns as $column) {
                    $lowerColumn = mb_strtolower($column, 'UTF-8');
                    $q->orWhereRaw('LOWER(' . $lowerColumn . ') like ?', ["%{$lowerSearch}%"]);
                }
            });
        }
        $db_data = $query->where('STATUS', 'PN')->orderBy('COMPL_DT', 'DESC')
            ->orderBy($request->sorting, $request->order)
            ->paginate($request->per_page);


        if ($request->order == 'ASC') {
            $sorting = 'DESC';
            $sorting_class = 'sorting_desc';
        } else {
            $sorting = 'ASC';
            $sorting_class = 'sorting_asc';
        }
        $count = $db_data->count();
        $number_of_page = $db_data->lastPage();
        $currentPage = $db_data->currentPage();
        $total_items = $db_data->total();
        $displayfrom = ($currentPage - 1) * $request->per_page + 1;
        $displayto = ($currentPage - 1) * $request->per_page + $count;
        $pagination = '';
        //display the pagination
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
                        Showing ' . (int)$displayfrom  . ' of ' . ceil($total_items) . ' of  entries
                        </li>
                    </ul>
            </div>';
        }

        if ($count == 0) {
            return response()->json(['status' => 0, 'data' => $db_data, 'pagination' => $pagination, 'msg' => 'Data Not Found!']);
        } else {
            $records = '';
            foreach ($db_data as $row) {
                $link = base64_encode($row['complaint_no']);
                $cust_cd = $row['cust_cd'];
                $eng_cd =  $row['assign_to'];

                $customer_name =  DB::select("SELECT CLIENT_NAME FROM CELINT_MASTER WHERE ERP_VERT='TERMS' AND CLIENT_CD = '$cust_cd' ORDER BY 1");
                $style = strlen($row['problem_desc']) > 100 ? ' style="min-width: 500px;"' : ' style=""';
                $assignto =  DB::select("SELECT ENG_NAME,ENG_CD FROM ENG_MASTER WHERE WORKING_ST='WK' AND DEPARTMENT='SWE' AND ENG_CD = '$eng_cd' ORDER BY 1");
                $eng_cd = $eng_cd ? " ($eng_cd)" : "";
                $records .= '<tr class="">
                    <td><a href="/complaint/edit/' . $link . '">' . $row['complaint_no'] . '</a></td>
                    <td>' .  (!empty($row['compl_dt']) ? date('d-m-Y', strtotime($row['compl_dt'])) : '-') . '</td>
                    <td>' . $cust_cd . '</td>
                    <td>' . @$customer_name[0]->client_name . '</td>
                    <td>' . $row['status'] . '</td>
                    <td>' . $row['module'] . '</td>
                    <td>' . $row['compl_type'] . '</td>
                    <td>' . $row['error_type'] . '</td>
                    <td' . $style . '>' . $row['problem_desc'] . '</td>
                    <td>' . (!empty($row['close_dt']) ? date('d-m-Y', strtotime($row['close_dt'])) : '-')  . '</td>
                    <td>' . @$assignto[0]->eng_name  . $eng_cd . '</td>
                    </tr>';
            }
        }
        return response()->json(['displayfrom' => $displayfrom, 'displayto' => $displayto, 'total_items' => $total_items, 'status' => 1, 'data' => $records, 'pagination' => $pagination, 'msg' => 'This is matched data!']);
    }


    public function showCreateComplaints()
    {
        $module =  DB::select("SELECT MODULE_TEXT,MODULE_TEXT M1 FROM SAP_MODULE_DTL WHERE DEPT_MODULE='TERMS' ORDER BY 1");
        $customer =  DB::select("SELECT CLIENT_NAME,CLIENT_CD,CLIENT_MAIL_ID FROM CELINT_MASTER WHERE ERP_VERT='TERMS' ORDER BY 1");
        $assignto =  DB::select("SELECT ENG_NAME,ENG_CD FROM ENG_MASTER WHERE WORKING_ST='WK' AND DEPARTMENT='SWE' ORDER BY 1");
        return view('dashboard.complaintCreate', ['module' => $module, 'customer' => $customer, 'assignto' => $assignto]);
    }

    public function saveCreateComplaints(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'P3_MODULE' => 'required',
            'P3_COMPL_DT' => 'required',
            // 'P3_CONTACT_MAIL_ID' => 'required|email',
            // 'P3_USER_NAME' => 'required',
            // 'P3_COMPL_TYPE' => 'required',
            // 'P3_ERROR_TYPE' => 'required',
            // 'P3_PROBLEM_DESC' => 'required',
            // 'P3_MAWAI_REMARKS' => 'nullable',
            // 'P3_COMPL_LEVEL' => 'required',
            // 'P3_STATUS_TYPE' => 'required',
            // 'P3_CUST_CD' => 'required',
            // 'P3_UPLOAD' => 'nullable|file|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['type' => 0, 'message' => $validator->errors()->all()]);
        }

        DB::beginTransaction();

        try {
            $COMPLAINT_NO = DB::select("SELECT GEN_COMPL_NO as data FROM DUAL")[0]->data;

            // Prepare data for create method
            $data = [
                'COMPLAINT_NO' => $COMPLAINT_NO,
                'COMPL_DT' => $request->input('P3_COMPL_DT'),
                'CUST_CD' => $request->input('P3_CUST_CD') ?? $request->input('P8_CUST_NAME'),
                'MODULE' => $request->input('P3_MODULE'),
                'COMPL_TYPE' => $request->input('P3_COMPL_TYPE'),
                'ERROR_TYPE' => $request->input('P3_ERROR_TYPE'),
                'PROBLEM_DESC' => $request->input('P3_PROBLEM_DESC'),
                'COMPL_LEVEL' => $request->input('P3_COMPL_LEVEL'),
                'STATUS' => $request->input('P3_STATUS_TYPE'),
                'MAWAI_REMARKS' => $request->input('P3_MAWAI_REMARKS'),
                'USER_NAME' => $request->input('P3_USER_NAME'),
                'CONTACT_MAIL_ID' => $request->input('P3_CONTACT_MAIL_ID'),
                'TIME_TAKEN' => $request->input('P8_TIME_TAKEN'),
                'CLOSE_DT' => $request->input('P3_CLOSE_DT_TYPE'),
                'ASSIGN_TO' => $request->input('P8_ASSIGN_TO'),
                'CHANGE_DONE_BY' => $request->input('P8_CHANGE_DONE_BY'),
                'REASON' => $request->input('P8_REASON'),
                'ACTION' => $request->input('P8_ACTION'),
            ];

            if ($request->hasFile('P3_UPLOAD')) {
                $file = $request->file('P3_UPLOAD');
                $filename = time() . '_' . $file->getClientOriginalName();

                $uploadPath = public_path('uploads');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $file->move($uploadPath, $filename);

                $data['FILENAME'] = '/uploads/' . $filename;
            }
            CustomerComplaint::create($data);
            DB::commit();

            $mailTo = $request->input('P3_CONTACT_MAIL_ID');
            // $mailTo = 'vaibhav.singh@mawaimail.com';
            $cc = env('MAIL_CC');
            // $cc = 'vaibhav.singh@mawaimail.com';
            if ($mailTo != "") {
                $user =  $request->session()->get('user');
                if (!($user->eng_cd && preg_match('/^S/', $user->eng_cd))) {
                    $mailTo = env('MAIL_CC');
                    $cc = env('P3_CONTACT_MAIL_ID');
                }
                $email = Mail::to($mailTo);
                if (!empty($cc)) {
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
        $data = DB::select("SELECT * FROM MAWAI.CUSTOMER_COMPLAINT WHERE COMPLAINT_NO  = '$id'");

        $custcd = $data[0]->cust_cd;
        $module =  DB::select("SELECT MODULE_TEXT,MODULE_TEXT M1 FROM SAP_MODULE_DTL WHERE DEPT_MODULE='TERMS' ORDER BY 1");
        $customer =  DB::select("SELECT CLIENT_NAME,CLIENT_CD,CLIENT_MAIL_ID FROM CELINT_MASTER WHERE ERP_VERT='TERMS' ORDER BY 1");
        $customer_name =  DB::select("SELECT CLIENT_NAME FROM CELINT_MASTER WHERE ERP_VERT='TERMS' AND CLIENT_CD = '$custcd' ORDER BY 1");
        $assignto =  DB::select("SELECT ENG_NAME,ENG_CD FROM ENG_MASTER WHERE WORKING_ST='WK' AND DEPARTMENT='SWE' ORDER BY 1");
        return view('dashboard.complaintEdit', ['module' => $module, 'customer' => $customer, 'assignto' => $assignto, 'data' => $data[0], 'customer_name' => $customer_name[0]]);
    }


    public function saveEditComplaints(Request $request, $id)
    {

        $id = base64_decode($id);
        $validator = Validator::make($request->all(), [
            // 'P3_MODULE' => 'required',
            'P3_COMPL_DT' => 'required',
            // 'P3_CONTACT_MAIL_ID' => 'required|email',
            // 'P3_USER_NAME' => 'required',
            // 'P3_COMPL_TYPE' => 'required',
            // 'P3_ERROR_TYPE' => 'required',
            // 'P3_PROBLEM_DESC' => 'required',
            // 'P3_MAWAI_REMARKS' => 'nullable',
            // 'P3_COMPL_LEVEL' => 'required',
            // 'P3_STATUS_TYPE' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['type' => 0, 'message' => $validator->errors()->all()]);
        }

        DB::beginTransaction();

        try {
            $complaint = CustomerComplaint::where('COMPLAINT_NO', $id)->update([
                'COMPL_DT' => $request->input('P3_COMPL_DT'),
                'CUST_CD' => $request->input('P3_CUST_CD') ?? $request->input('P8_CUST_NAME'),
                'MODULE' => $request->input('P3_MODULE'),
                'COMPL_TYPE' => $request->input('P3_COMPL_TYPE'),
                'ERROR_TYPE' => $request->input('P3_ERROR_TYPE'),
                'PROBLEM_DESC' => $request->input('P3_PROBLEM_DESC'),
                'COMPL_LEVEL' => $request->input('P3_COMPL_LEVEL'),
                'STATUS' => $request->input('P3_STATUS_TYPE'),
                'MAWAI_REMARKS' => $request->input('P3_MAWAI_REMARKS'),
                'USER_NAME' => $request->input('P3_USER_NAME'),
                'CONTACT_MAIL_ID' => $request->input('P3_CONTACT_MAIL_ID'),
                'TIME_TAKEN' => $request->input('P8_TIME_TAKEN'),
                'CLOSE_DT' => $request->input('P3_CLOSE_DT_TYPE'),
                'ASSIGN_TO' => $request->input('P8_ASSIGN_TO'),
                'CHANGE_DONE_BY' => $request->input('P8_CHANGE_DONE_BY'),
                'REASON' => $request->input('P8_REASON'),
                'ACTION' => $request->input('P8_ACTION'),
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
        } else {
            abort(404, 'File not found.');
        }
    }
}
