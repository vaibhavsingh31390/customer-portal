<?php

namespace App\Http\Register;

use App\Exports\Reports\ComplaintRegisterExcel;
use App\Models\CustomerComplaint;
use App\Support\SqlHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class RegisterController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function showComplaintsRegister(Request $request)
    {
        $user = $request->session()->get('user');
        $clients = SqlHelper::select('SELECT name, client_code FROM '.SqlHelper::table(SqlHelper::TABLE_CLIENTS)." WHERE erp_vertical = 'TERMS' ORDER BY 1");
        $customer_name = SqlHelper::selectOne(
            'SELECT name, client_code FROM '.SqlHelper::table(SqlHelper::TABLE_CLIENTS)." WHERE erp_vertical = 'TERMS' AND client_code = ? ORDER BY 1",
            [$user->user_code]
        );

        return view('dashboard.complaintRegister', [
            'clients' => $clients,
            'customer_name' => $customer_name,
        ]);
    }

    public function showComplaintsRegisterReport(Request $request)
    {
        $validator = Validator::make($request->all(), []);

        if ($validator->fails()) {
            return response()->json(['type' => 0, 'message' => $validator->errors()->all()]);
        }

        $search = $request->input('search');
        if ($request->client_cd != '') {
            $query = CustomerComplaint::where('client_code', $request->client_cd);
        } else {
            $query = CustomerComplaint::query();
        }

        if ($request->date_from != '' && $request->date_to != '') {
            $query->whereBetween('complaint_date', [$request->date_from, $request->date_to]);
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
            $assignto = SqlHelper::select(
                'SELECT name, engineer_code FROM '.SqlHelper::table(SqlHelper::TABLE_ENGINEERS)." WHERE working_status = 'WK' AND department = 'SWE' AND engineer_code = ? ORDER BY 1",
                [$assignedTo]
            );
            $style = strlen($row['problem_description']) > 100 ? ' style="min-width: 500px;"' : ' style=""';
            $assignedLabel = $assignedTo ? " ($assignedTo)" : '';
            $records .= '<tr class="">
                <td><a href="/complaint/edit/' . $link . '">' . $row['complaint_number'] . '</a></td>
                <td>' . (! empty($row['complaint_date']) ? date('d-m-Y', strtotime($row['complaint_date'])) : '-') . '</td>
                <td>' . $clientCode . '</td>
                <td style="min-width: 200px;">' . @$customer_name[0]->name . '</td>
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

    public function showComplaintsRegisterReportExport(Request $request)
    {
        $validator = Validator::make($request->all(), []);

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
                $query = CustomerComplaint::where('client_code', $request->client_cd);
            } else {
                $query = CustomerComplaint::query();
            }
            if ($request->date_from != '' && $request->date_to != '') {
                $query->whereBetween('complaint_date', [$request->date_from, $request->date_to]);
            }
            if ($search) {
                $query->where('complaint_number', 'like', "%{$search}%");
            }
            $datas = $query->orderBy('complaint_date', 'DESC')->get();
            $pdf = Pdf::loadView('register.report', compact('datas', 'date_from', 'date_to'));

            return $pdf->stream('RegisterReport.pdf');
        }

        dd('INVALID TYPE');
    }
}
