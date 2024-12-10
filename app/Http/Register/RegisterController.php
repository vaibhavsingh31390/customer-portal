<?php


namespace App\Http\Register;

use App\Exports\Reports\ComplaintRegisterExcel;
use App\Models\CustomerComplaint;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class RegisterController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function showComplaintsRegister(Request $request)
    {
        $user =  $request->session()->get('user');
        $clients = DB::select("SELECT CLIENT_NAME,CLIENT_CD FROM CELINT_MASTER WHERE ERP_VERT='TERMS' ORDER BY 1");
        $customer_name =  DB::select("SELECT CLIENT_NAME, CLIENT_CD FROM CELINT_MASTER WHERE ERP_VERT='TERMS' AND CLIENT_CD = '$user->eng_cd' ORDER BY 1");
        return view('dashboard.complaintRegister', ['clients' => $clients, 'customer_name' => @$customer_name[0]]);
    }

    public function showComplaintsRegisterReport(Request $request)
    {

        $validator = Validator::make($request->all(), [
            // 'client_cd' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['type' => 0, 'message' => $validator->errors()->all()]);
        }

        $search = $request->input('search');
        if ($request->client_cd != '') {
            $query = CustomerComplaint::where('CUST_CD', $request->client_cd);
        } else {
            $query = CustomerComplaint::query();
        }

        if ($request->date_from != '' && $request->date_to != '') {
            $query->whereBetween('COMPL_DT', [$request->date_from, $request->date_to]);
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
        // dd($db_data);
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
                $assignto =  DB::select("SELECT ENG_NAME,ENG_CD FROM ENG_MASTER WHERE WORKING_ST='WK' AND DEPARTMENT='SWE' AND ENG_CD = '$eng_cd' ORDER BY 1");
                $style = strlen($row['problem_desc']) > 100 ? ' style="min-width: 500px;"' : ' style=""';
                $eng_cd = $eng_cd ? " ($eng_cd)" : "";
                $records .= '<tr class="">
                    <td><a href="/complaint/edit/' . $link . '">' . $row['complaint_no'] . '</a></td>
                    <td>' .  (!empty($row['compl_dt']) ? date('d-m-Y', strtotime($row['compl_dt'])) : '-') . '</td>
                    <td>' . $cust_cd . '</td>
                    <td style="min-width: 200px;">' . @$customer_name[0]->client_name . '</td>
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


    public function showComplaintsRegisterReportExport(Request $request)
    {

        $validator = Validator::make($request->all(), [
            // 'client_cd' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['type' => 0, 'message' => $validator->errors()->all()]);
        }

        $search = $request->input('search');


        if ($request->type == 'Excel') {
            ob_end_clean();
            ob_start();
            return Excel::download(new ComplaintRegisterExcel(), 'ComplaintRegister.xls');
        } elseif ($request->type == 'PDF') {
            $date_from = $request->date_from;
            $date_to = $request->date_to;
            if ($request->client_cd != '') {
                $query = CustomerComplaint::where('CUST_CD', $request->client_cd);
            } else {
                $query = CustomerComplaint::query();
            }
            if ($request->date_from != '' && $request->date_to != '') {
                $query->whereBetween('COMPL_DT', [$request->date_from, $request->date_to]);
            }
            if ($search) {
                $query->where('COMPLAINT_NO', 'like', "%{$search}%");
            }
            $datas = $query->orderBy('COMPL_DT', 'DESC')->get();
            $pdf = Pdf::loadView('register.report', compact('datas', 'date_from', 'date_to'));
            return $pdf->stream('RegisterReport.pdf');
        } else {
            dd('INVALID TYPE');
        }
    }
}
