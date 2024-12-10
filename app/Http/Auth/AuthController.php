<?php

namespace App\Http\Auth;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class AuthController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('USER_NAME', $credentials['username'])->where('USER_STATUS', 'Y')->first();

        if (!$user || $credentials['password'] !== $user->user_password) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }
        $token = encrypt($user->user_name);
        $request->session()->put('user_token', $token);
        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('user_token');
        $request->session()->forget('user');
        $request->session()->forget('name');
        return redirect()->route('login.form');
    }


    public function dashboard(Request $request)
    {
        $user =  $request->session()->get('user');
        $customer = DB::select("
        SELECT 
            COUNT(CASE WHEN STATUS = 'PN' THEN 1 END) AS COUNT_PEND,
            COUNT(*) AS TOTAL_COUNT 
        FROM 
            \"CUSTOMER_COMPLAINT\" 
        WHERE 
            \"CUST_CD\" = '$user->eng_cd' 
            AND TRUNC(\"COMPL_DT\", 'MM') = TRUNC(SYSDATE, 'MM')
        ");
        return view('dashboard.dashboard', ['customer' => @$customer[0]]);
    }

    public function getPendingList(Request $request)
    {

        $total_analytics = DB::select("
            SELECT 
                COUNT(*) AS TOTAL,
                COUNT(CASE WHEN CC.STATUS = 'PN' THEN 1 END) AS TOTAL_PEND
            FROM 
                \"CUSTOMER_COMPLAINT\" CC
            LEFT JOIN 
                \"CELINT_MASTER\" CM 
            ON 
                CM.CLIENT_CD = CC.CUST_CD 
            WHERE 
                TRUNC(CC.\"COMPL_DT\", 'MM') = TRUNC(SYSDATE, 'MM')
        ")[0];


        $search = $request->search != '' ? $request->search : "%";

        $All = DB::select("
            SELECT 
                CC.CUST_CD,  
                CM.CLIENT_NAME, 
                COUNT(CASE WHEN CC.STATUS = 'PN' THEN 1 END) AS COUNT_PEND
            FROM 
                \"CUSTOMER_COMPLAINT\" CC 
            LEFT JOIN 
                \"CELINT_MASTER\" CM 
            ON  
                CM.CLIENT_CD = CC.CUST_CD 
            WHERE 
                TRUNC(CC.\"COMPL_DT\", 'MM') = TRUNC(SYSDATE, 'MM') 
                AND LOWER(CM.CLIENT_NAME) LIKE '%' || LOWER(:search) || '%'
            GROUP BY 
                CC.CUST_CD, 
                CM.CLIENT_NAME
             HAVING COUNT(CASE WHEN CC.STATUS = 'PN' THEN 1 END) > 0    
        ", ['search' => mb_strtolower($search, 'UTF-8')]);

        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = !empty($request->per_page) ? $request->per_page : 10;
        $resultsCollection = collect($All);
        $currentPageItems = $resultsCollection->slice(($page - 1) * $perPage, $perPage)->all();
        $btl_datas = new LengthAwarePaginator(
            $currentPageItems,
            count($resultsCollection),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );
        $count = $btl_datas->count();
        $number_of_page = $btl_datas->lastPage();
        $currentPage = $btl_datas->currentPage();
        $total_items = $btl_datas->total();
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
            return response()->json(['status' => 0, 'data' => $btl_datas, 'pagination' => $pagination, 'msg' => 'Data Not Found!', 'total_analytics' => $total_analytics]);
        } else {
            $records = '';
            $total_pend = 0;
            foreach ($btl_datas as $row) {
                $total_pend += (int) $row->count_pend;
                $records .= '<tr class="">
                    <td>' . $row->client_name . '</td>
                    <td>' . $row->count_pend . '</td>
                    </tr>';
            }
        }
        return response()->json(['displayfrom' => $displayfrom, 'displayto' => $displayto, 'total_items' => $total_items, 'status' => 1, 'data' => $records, 'pagination' => $pagination, 'msg' => 'This is matched data!', 'total_analytics' => $total_analytics]);
    }


    public function getPendingListClinet(Request $request)
    {

        $user =  $request->session()->get('user');
        $search = $request->search != '' ? $request->search : "%";
        $All = DB::select("
            SELECT 
                CC.COMPLAINT_NO,  
                CC.STATUS
            FROM
                \"CUSTOMER_COMPLAINT\" CC 
            WHERE 
                TRUNC(CC.\"COMPL_DT\", 'MM') = TRUNC(SYSDATE, 'MM') 
                AND CC.CUST_CD = :eng_cd
                AND LOWER(CC.COMPLAINT_NO) LIKE '%' || LOWER(:search) || '%'
            ORDER BY 
                CC.COMPL_DT desc 
        ", ['eng_cd' => $user->eng_cd, 'search' => mb_strtolower($search, 'UTF-8')]);



        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = !empty($request->per_page) ? $request->per_page : 10;
        $resultsCollection = collect($All);
        $currentPageItems = $resultsCollection->slice(($page - 1) * $perPage, $perPage)->all();
        $btl_datas = new LengthAwarePaginator(
            $currentPageItems,
            count($resultsCollection),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );
        $count = $btl_datas->count();
        $number_of_page = $btl_datas->lastPage();
        $currentPage = $btl_datas->currentPage();
        $total_items = $btl_datas->total();
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
            return response()->json(['status' => 0, 'data' => $btl_datas, 'pagination' => $pagination, 'msg' => 'Data Not Found!']);
        } else {
            $records = '';
            $STATUS = [
                'CL' => 'Cancel',
                'CM' => 'Complete',
                'HL' => 'Hold',
                'PN' => 'Pending',
                'SV' => 'Sent For Customer Verification',
            ];
            foreach ($btl_datas as $row) {
                $link = base64_encode($row->complaint_no);
                $records .= '<tr class="">
                    <td><a href="/complaint/edit/' . $link . '">' . $row->complaint_no . '</a></td>
                    <td>' . @$STATUS[$row->status] . '</td>
                    </tr>';
            }
        }
        return response()->json(['displayfrom' => $displayfrom, 'displayto' => $displayto, 'total_items' => $total_items, 'status' => 1, 'data' => $records, 'pagination' => $pagination, 'msg' => 'This is matched data!']);
    }
}
