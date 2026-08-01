<?php

namespace App\Http\Auth;

use App\Models\User;
use App\Support\ComplaintAnalytics;
use App\Support\ComplaintStatus;
use App\Support\SqlHelper;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class AuthController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function showLoginForm()
    {
        $testMode = config('test.enabled');

        return view('auth.login', [
            'testMode' => $testMode,
            'testAccounts' => $testMode ? $this->testAccountsForPanel() : [],
        ]);
    }

    protected function testAccountsForPanel(): array
    {
        $password = config('test.dummy_password');

        return collect(config('test.accounts', []))
            ->map(fn (array $account) => array_merge($account, ['password' => $password]))
            ->sortBy(fn (array $account) => match ($account['role'] ?? '') {
                'admin' => 0,
                'client' => 1,
                'support' => 2,
                default => 3,
            })
            ->values()
            ->all();
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('username', $credentials['username'])->where('status', 'Y')->first();

        if (! $user || $credentials['password'] !== $user->getAuthPassword()) {
            return back()->withErrors([
                'username' => 'The provided credentials do not match our records.',
            ])->withInput($request->only('username'));
        }

        $token = encrypt($user->user_code);
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
        $user = $request->session()->get('user');
        $clientCounts = ComplaintAnalytics::monthlyStatusCounts($user->user_code);
        $supportCounts = ComplaintAnalytics::monthlyStatusCounts();

        return view('dashboard.dashboard', [
            'customer' => (object) [
                'total_count' => $clientCounts['total'],
                'count_pend' => $clientCounts['pending'],
                'count_open' => $clientCounts['open'],
                'count_closed' => $clientCounts['closed'],
            ],
            'supportSummary' => ComplaintAnalytics::toAnalyticsObject($supportCounts),
            'clientStatusChart' => ComplaintAnalytics::chartPayload($clientCounts),
            'supportStatusChart' => ComplaintAnalytics::chartPayload($supportCounts),
        ]);
    }

    protected function supportDashboardSummary(): object
    {
        return ComplaintAnalytics::toAnalyticsObject(ComplaintAnalytics::monthlyStatusCounts());
    }

    public function getPendingList(Request $request)
    {
        $counts = ComplaintAnalytics::monthlyStatusCounts();
        $total_analytics = ComplaintAnalytics::toAnalyticsObject($counts);
        $total_analytics->chart = ComplaintAnalytics::chartPayload($counts);

        $search = $request->search != '' ? $request->search : '%';

        $All = SqlHelper::select('
            SELECT
                cc.client_code,
                c.name AS client_name,
                COUNT(CASE WHEN cc.status = \'PN\' THEN 1 END) AS count_pend
            FROM
                '.SqlHelper::table(SqlHelper::TABLE_COMPLAINTS).' cc
            LEFT JOIN
                '.SqlHelper::table(SqlHelper::TABLE_CLIENTS).' c
            ON
                c.client_code = cc.client_code
            WHERE
                '.SqlHelper::currentMonthFilter('cc.complaint_date').'
                AND '.SqlHelper::likeContains('c.name', ':search').'
            GROUP BY
                cc.client_code,
                c.name
             HAVING COUNT(CASE WHEN cc.status = \'PN\' THEN 1 END) > 0
        ', ['search' => mb_strtolower($search, 'UTF-8')]);

        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = ! empty($request->per_page) ? $request->per_page : 10;
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
            return response()->json(['status' => 0, 'data' => $btl_datas, 'pagination' => $pagination, 'msg' => 'Data Not Found!', 'total_analytics' => $total_analytics]);
        }

        $records = '';
        foreach ($btl_datas as $row) {
            $records .= '<tr class="">
                <td>' . $row->client_name . '</td>
                <td>' . $row->count_pend . '</td>
                </tr>';
        }

        return response()->json(['displayfrom' => $displayfrom, 'displayto' => $displayto, 'total_items' => $total_items, 'status' => 1, 'data' => $records, 'pagination' => $pagination, 'msg' => 'This is matched data!', 'total_analytics' => $total_analytics]);
    }

    public function getPendingListClinet(Request $request)
    {
        $user = $request->session()->get('user');
        $search = $request->search != '' ? $request->search : '%';
        $All = SqlHelper::select('
            SELECT
                cc.complaint_number,
                cc.status
            FROM
                '.SqlHelper::table(SqlHelper::TABLE_COMPLAINTS).' cc
            WHERE
                '.SqlHelper::currentMonthFilter('cc.complaint_date').'
                AND cc.client_code = :user_code
                AND '.SqlHelper::likeContains('cc.complaint_number', ':search').'
            ORDER BY
                cc.complaint_date desc
        ', ['user_code' => $user->user_code, 'search' => mb_strtolower($search, 'UTF-8')]);

        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = ! empty($request->per_page) ? $request->per_page : 10;
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
            return response()->json(['status' => 0, 'data' => $btl_datas, 'pagination' => $pagination, 'msg' => 'Data Not Found!']);
        }

        $records = '';
        foreach ($btl_datas as $row) {
            $link = base64_encode($row->complaint_number);
            $statusHtml = ComplaintStatus::tableBadgeHtml($row->status);
            $rowClass = ComplaintStatus::rowClass($row->status);
            $records .= '<tr class="'.$rowClass.'">
                <td><a href="/complaint/edit/'.$link.'">'.e($row->complaint_number).'</a></td>
                <td class="portal-table__status">'.$statusHtml.'</td>
                </tr>';
        }

        return response()->json(['displayfrom' => $displayfrom, 'displayto' => $displayto, 'total_items' => $total_items, 'status' => 1, 'data' => $records, 'pagination' => $pagination, 'msg' => 'This is matched data!']);
    }
}
