<?php

namespace App\Http\Complaint;

use App\Exports\Reports\ComplaintRegisterExcel;
use App\Mail\ComplaintCreatedMail;
use App\Models\ComplaintMessage;
use App\Models\CustomerComplaint;
use App\Support\ComplaintAccess;
use App\Support\ComplaintStatus;
use App\Support\SqlHelper;
use App\Support\UserRole;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ComplaintController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function showComplaints(Request $request)
    {
        $user = $request->session()->get('user');
        $userCode = $user->user_code ?? '';
        $isStaff = UserRole::isStaff($userCode);
        $clients = [];
        $customer_name = null;

        if ($isStaff) {
            $clients = SqlHelper::select(
                'SELECT name, client_code FROM '.SqlHelper::table(SqlHelper::TABLE_CLIENTS)." WHERE erp_vertical = 'TERMS' ORDER BY 1"
            );
        } else {
            $customer_name = SqlHelper::selectOne(
                'SELECT name, client_code FROM '.SqlHelper::table(SqlHelper::TABLE_CLIENTS)." WHERE erp_vertical = 'TERMS' AND client_code = ? ORDER BY 1",
                [$userCode]
            );
        }

        return view('dashboard.complaints', compact('clients', 'customer_name', 'isStaff'));
    }

    public function getTableData(Request $request)
    {
        $search = $request->input('search');
        $query = $this->complaintQueryForUser($request);

        if ($request->date_from != '' && $request->date_to != '') {
            $query->whereBetween('complaint_date', [$request->date_from, $request->date_to]);
        }

        $query = ComplaintStatus::applyFilter($query, $request->input('status_cd'));

        $columns = Schema::getColumnListing((new CustomerComplaint)->getTable());
        if ($search) {
            $query->where(function (Builder $q) use ($columns, $search) {
                $lowerSearch = mb_strtolower($search, 'UTF-8');
                foreach ($columns as $column) {
                    $q->orWhereRaw('LOWER('.$column.') like ?', ["%{$lowerSearch}%"]);
                }
            });
        }

        $db_data = $query->orderBy('complaint_date', 'DESC')
            ->orderBy($this->resolveSortColumn($request->input('sorting')), $this->resolveSortOrder($request->input('order')))
            ->paginate($request->input('per_page', 10));

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
                'SELECT name, engineer_code FROM '.SqlHelper::table(SqlHelper::TABLE_ENGINEERS)." WHERE working_status = 'WK' AND engineer_code = ? ORDER BY 1",
                [$assignedTo]
            );
            $assignedLabel = $assignedTo ? " ($assignedTo)" : '';
            $statusHtml = ComplaintStatus::tableBadgeHtml($row['status']);
            $rowClass = ComplaintStatus::rowClass($row['status']);
            $complaintNumber = e($row['complaint_number']);
            $records .= '<tr class="'.$rowClass.'">
                <td><a href="/complaint/edit/'.$link.'" class="portal-table__complaint-link">'.$complaintNumber.'</a></td>
                <td>'.(! empty($row['complaint_date']) ? date('d-m-Y', strtotime($row['complaint_date'])) : '-').'</td>
                <td>'.e($clientCode).'</td>
                <td>'.e(@$customer_name[0]->name).'</td>
                <td class="portal-table__status">'.$statusHtml.'</td>
                <td>'.e($row['module']).'</td>
                <td>'.e($row['complaint_type']).'</td>
                <td>'.e($row['error_type']).'</td>
                <td'.$style.'>'.e($row['problem_description']).'</td>
                <td>'.(! empty($row['closed_date']) ? date('d-m-Y', strtotime($row['closed_date'])) : '-').'</td>
                <td>'.e(@$assignto[0]->name).$assignedLabel.'</td>
                </tr>';
        }

        return response()->json(['displayfrom' => $displayfrom, 'displayto' => $displayto, 'total_items' => $total_items, 'status' => 1, 'data' => $records, 'pagination' => $pagination, 'msg' => 'This is matched data!']);
    }

    public function exportComplaints(Request $request)
    {
        $search = $request->input('search');

        if ($request->type == 'Excel') {
            ob_end_clean();
            ob_start();

            return Excel::download(new ComplaintRegisterExcel(), 'ComplaintRegister.xls');
        }

        if ($request->type == 'PDF') {
            $date_from = $request->date_from;
            $date_to = $request->date_to;
            $query = $this->complaintQueryForUser($request);

            if ($request->date_from != '' && $request->date_to != '') {
                $query->whereBetween('complaint_date', [$request->date_from, $request->date_to]);
            }
            $query = ComplaintStatus::applyFilter($query, $request->input('status_cd'));
            if ($search) {
                $query->where('complaint_number', 'like', "%{$search}%");
            }
            $datas = $query->orderBy('complaint_date', 'DESC')->get();
            $pdf = Pdf::loadView('register.report', compact('datas', 'date_from', 'date_to'));

            return $pdf->stream('RegisterReport.pdf');
        }

        abort(400, 'Invalid export type.');
    }

    protected function complaintQueryForUser(Request $request): Builder
    {
        $user = $request->session()->get('user');
        $userCode = $user->user_code ?? '';
        $clientCode = $request->input('client_cd');

        if (UserRole::isClient($userCode)) {
            return CustomerComplaint::where('client_code', $userCode);
        }

        if ($clientCode !== '' && $clientCode !== null) {
            return CustomerComplaint::where('client_code', $clientCode);
        }

        return CustomerComplaint::query();
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
        $user = $request->session()->get('user');
        $userCode = $user->user_code ?? '';
        $isStaff = UserRole::isStaff($userCode);

        $rules = [
            'P3_COMPL_DT' => 'required|date',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['type' => 0, 'message' => $validator->errors()->all()]);
        }

        $clientCode = $this->resolveClientCodeFromRequest($request, $userCode);

        if ($clientCode === null || $clientCode === '') {
            $message = $isStaff ? 'Please select a customer.' : 'Unable to identify your client account.';

            return response()->json(['type' => 0, 'message' => [$message]]);
        }

        $clientValidator = Validator::make(
            ['client_code' => $clientCode],
            ['client_code' => 'required|exists:clients,client_code']
        );

        if ($clientValidator->fails()) {
            return response()->json(['type' => 0, 'message' => $clientValidator->errors()->all()]);
        }

        DB::beginTransaction();

        try {
            $complaintNumber = $this->nextComplaintNumber();
            $data = $this->buildCreateComplaintPayload($request, $userCode, $clientCode, $complaintNumber);

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

            $this->storeMessage(
                $complaintNumber,
                $user->user_code,
                $request->session()->get('name'),
                ComplaintAccess::authorRole($user->user_code),
                $request->input('P3_PROBLEM_DESC') ?: 'Complaint opened.',
                'comment'
            );

            DB::commit();

            $mailTo = trim((string) $request->input('P3_CONTACT_MAIL_ID'));
            $cc = null;

            if ($isStaff) {
                $cc = env('MAIL_CC') ?: null;
            } elseif (env('MAIL_CC')) {
                $mailTo = env('MAIL_CC');
                $cc = $request->input('P3_CONTACT_MAIL_ID') ?: null;
            }

            if ($mailTo !== '') {
                try {
                    $email = Mail::to($mailTo);
                    if (! empty($cc)) {
                        $email->cc($cc);
                    }
                    $email->send(new ComplaintCreatedMail($data));
                } catch (\Throwable $e) {
                    report($e);
                }
            }

            return response()->json(['type' => 1, 'message' => 'Complaint created successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['type' => 0, 'message' => $e->getMessage()]);
        }
    }

    public function showEditComplaints(Request $request, $id)
    {
        $complaint = $this->findComplaintOrFail($request, $id);
        $data = (object) $complaint->toArray();

        $this->ensureThreadSeeded($complaint);

        $clientCode = $complaint->client_code;
        $module = SqlHelper::select('SELECT name FROM '.SqlHelper::table(SqlHelper::TABLE_SAP_MODULES)." WHERE department_module = 'TERMS' ORDER BY 1");
        $customer = SqlHelper::select('SELECT name, client_code, email FROM '.SqlHelper::table(SqlHelper::TABLE_CLIENTS)." WHERE erp_vertical = 'TERMS' ORDER BY 1");
        $customer_name = SqlHelper::selectOne(
            'SELECT name FROM '.SqlHelper::table(SqlHelper::TABLE_CLIENTS)." WHERE erp_vertical = 'TERMS' AND client_code = ? ORDER BY 1",
            [$clientCode]
        );
        $assignto = SqlHelper::select('SELECT name, engineer_code FROM '.SqlHelper::table(SqlHelper::TABLE_ENGINEERS)." WHERE working_status = 'WK' AND department = 'SWE' ORDER BY 1");

        $userCode = $request->session()->get('user')->user_code ?? '';
        $isStaff = UserRole::isStaff($userCode);
        $messages = $complaint->messages()
            ->when(! $isStaff, fn ($q) => $q->where('is_internal', false))
            ->get();

        return view('dashboard.complaintEdit', [
            'module' => $module,
            'customer' => $customer,
            'assignto' => $assignto,
            'data' => $data,
            'customer_name' => $customer_name,
            'messages' => $messages,
            'isStaff' => $isStaff,
            'canClose' => ComplaintAccess::canClose($request, $complaint),
            'canReply' => ComplaintAccess::canPostMessage($request, $complaint),
            'isClosed' => ComplaintAccess::isClosed($complaint),
            'isAdmin' => UserRole::isAdmin($userCode),
        ]);
    }

    public function postComplaintMessage(Request $request, $id)
    {
        $complaint = $this->findComplaintOrFail($request, $id);

        if (! ComplaintAccess::canPostMessage($request, $complaint)) {
            return response()->json(['type' => 0, 'message' => 'You cannot reply on this complaint.']);
        }

        $user = $request->session()->get('user');
        $isStaff = UserRole::isStaff($user->user_code);

        $validator = Validator::make($request->all(), [
            'body' => 'required|string|max:2000',
            'message_type' => 'nullable|in:comment,remark,action',
            'is_internal' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['type' => 0, 'message' => $validator->errors()->first()]);
        }

        $isInternal = $isStaff && $request->boolean('is_internal');
        $messageType = $request->input('message_type', 'comment');
        if (! $isStaff) {
            $messageType = 'comment';
        }

        $wasAwaitingVerification = ! $isStaff && $complaint->status === 'SV';

        DB::beginTransaction();

        try {
            $createdMessages = [];

            $createdMessages[] = $this->storeMessage(
                $complaint->complaint_number,
                $user->user_code,
                $request->session()->get('name'),
                ComplaintAccess::authorRole($user->user_code),
                $request->input('body'),
                $messageType,
                $isInternal
            );

            if ($isStaff && $messageType === 'remark') {
                $complaint->update(['internal_remarks' => $request->input('body')]);
            }

            if ($isStaff && $messageType === 'action') {
                $complaint->update(['action_taken' => $request->input('body')]);
            }

            if ($wasAwaitingVerification) {
                $complaint->update(['status' => 'PN']);
                $createdMessages[] = $this->storeMessage(
                    $complaint->complaint_number,
                    $user->user_code,
                    'System',
                    'system',
                    'Customer replied — ticket reopened to Pending.',
                    'status',
                    false
                );
            }

            DB::commit();

            $complaint->refresh();

            return $this->threadJsonResponse($request, $complaint, 'Reply posted successfully.', $createdMessages);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['type' => 0, 'message' => $e->getMessage()]);
        }
    }

    public function closeComplaint(Request $request, $id)
    {
        $complaint = $this->findComplaintOrFail($request, $id);

        if (! ComplaintAccess::canClose($request, $complaint)) {
            return response()->json(['type' => 0, 'message' => 'Only the client or an admin can close this ticket.']);
        }

        $user = $request->session()->get('user');
        $isClient = UserRole::isClient($user->user_code);

        $status = $request->input('status');
        $ratingRules = ($isClient && $status === 'CM')
            ? 'required|integer|min:1|max:5'
            : 'nullable|integer|min:1|max:5';

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:CM,CL',
            'rating' => $ratingRules,
            'body' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json(['type' => 0, 'message' => $validator->errors()->first()]);
        }

        $status = $request->input('status');
        $rating = $request->input('rating');
        $body = $request->input('body') ?: ($status === 'CM' ? 'Ticket closed as complete.' : 'Ticket cancelled.');

        DB::beginTransaction();

        try {
            $complaint->update([
                'status' => $status,
                'closed_date' => now()->toDateString(),
                'rating' => $rating,
            ]);

            $createdMessages = [];

            $createdMessages[] = $this->storeMessage(
                $complaint->complaint_number,
                $user->user_code,
                $request->session()->get('name'),
                ComplaintAccess::authorRole($user->user_code),
                $body,
                'close',
                false,
                $rating ? (int) $rating : null
            );

            DB::commit();

            $complaint->refresh();

            return $this->threadJsonResponse($request, $complaint, 'Ticket closed successfully.', $createdMessages);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['type' => 0, 'message' => $e->getMessage()]);
        }
    }

    public function saveEditComplaints(Request $request, $id)
    {
        $complaint = $this->findComplaintOrFail($request, $id);

        $user = $request->session()->get('user');
        $userCode = $user->user_code ?? '';

        if (UserRole::isClient($userCode)) {
            return response()->json(['type' => 0, 'message' => 'Use the communication thread to reply or close this ticket.']);
        }

        $validator = Validator::make($request->all(), [
            'P3_COMPL_DT' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['type' => 0, 'message' => $validator->errors()->all()]);
        }

        $newStatus = $request->input('P3_STATUS_TYPE') ?: $complaint->status;

        if (in_array($newStatus, ['CM', 'CL'], true) && ! UserRole::isAdmin($userCode)) {
            return response()->json([
                'type' => 0,
                'message' => 'Only a client (via Close Ticket) or an admin can mark a ticket complete or cancelled.',
            ]);
        }

        $clientCode = $this->resolveClientCodeFromRequest($request, $userCode, $complaint->client_code);

        if ($clientCode === null || $clientCode === '') {
            return response()->json(['type' => 0, 'message' => ['Please select a customer.']]);
        }

        $clientValidator = Validator::make(
            ['client_code' => $clientCode],
            ['client_code' => 'required|exists:clients,client_code']
        );

        if ($clientValidator->fails()) {
            return response()->json(['type' => 0, 'message' => $clientValidator->errors()->all()]);
        }

        DB::beginTransaction();

        try {
            $complaint->update($this->buildUpdateComplaintPayload($request, $userCode, $clientCode, $newStatus));

            DB::commit();

            return response()->json(['type' => 1, 'message' => 'Complaint updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['type' => 0, 'message' => $e->getMessage()]);
        }
    }

    protected function findComplaintOrFail(Request $request, string $encodedId): CustomerComplaint
    {
        $complaintNumber = base64_decode($encodedId);
        $complaint = CustomerComplaint::where('complaint_number', $complaintNumber)->first();

        if (! $complaint || ! ComplaintAccess::canView($request, $complaint)) {
            abort(404);
        }

        return $complaint;
    }

    protected function ensureThreadSeeded(CustomerComplaint $complaint): void
    {
        if ($complaint->messages()->exists()) {
            return;
        }

        if ($complaint->problem_description) {
            $this->storeMessage(
                $complaint->complaint_number,
                $complaint->client_code,
                $complaint->contact_name ?: 'Client',
                'client',
                $complaint->problem_description,
                'comment'
            );
        }

        if ($complaint->internal_remarks) {
            $this->storeMessage(
                $complaint->complaint_number,
                $complaint->assigned_to ?: 'S000',
                'Support',
                'support',
                $complaint->internal_remarks,
                'remark'
            );
        }

        if ($complaint->action_taken) {
            $this->storeMessage(
                $complaint->complaint_number,
                $complaint->assigned_to ?: 'S000',
                'Support',
                'support',
                $complaint->action_taken,
                'action'
            );
        }
    }

    protected function resolveClientCodeFromRequest(Request $request, ?string $userCode = null, ?string $fallback = null): ?string
    {
        if ($userCode !== null && UserRole::isClient($userCode)) {
            return $userCode;
        }

        return $request->input('P8_CUST_CD')
            ?: $request->input('P8_CUST_NAME')
            ?: $request->input('P3_CUST_CD')
            ?: $fallback;
    }

    protected function resolveSortColumn(?string $column): string
    {
        $allowed = [
            'complaint_number',
            'complaint_date',
            'client_code',
            'status',
            'module',
            'complaint_type',
            'error_type',
            'priority',
            'closed_date',
            'assigned_to',
        ];

        return in_array($column, $allowed, true) ? $column : 'complaint_number';
    }

    protected function resolveSortOrder(?string $order): string
    {
        return strtoupper((string) $order) === 'ASC' ? 'ASC' : 'DESC';
    }

    protected function nextComplaintNumber(): string
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            $complaintNumber = SqlHelper::selectOne(SqlHelper::genComplaintNoQuery())?->data;
            if ($complaintNumber) {
                return $complaintNumber;
            }
        }

        $prefix = 'CP'.date('Ym');
        $seq = CustomerComplaint::query()
            ->where('complaint_number', 'like', $prefix.'%')
            ->count() + 1;

        return $prefix.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * @return array<string, mixed>
     */
    protected function buildCreateComplaintPayload(
        Request $request,
        string $userCode,
        string $clientCode,
        string $complaintNumber,
    ): array {
        $isStaff = UserRole::isStaff($userCode);

        $data = [
            'complaint_number' => $complaintNumber,
            'complaint_date' => $request->input('P3_COMPL_DT'),
            'client_code' => $clientCode,
            'module' => $request->input('P3_MODULE'),
            'complaint_type' => $request->input('P3_COMPL_TYPE'),
            'error_type' => $request->input('P3_ERROR_TYPE'),
            'problem_description' => $request->input('P3_PROBLEM_DESC'),
            'priority' => $request->input('P3_COMPL_LEVEL'),
            'status' => $request->input('P3_STATUS_TYPE') ?: 'PN',
            'contact_name' => $request->input('P3_USER_NAME'),
            'contact_email' => $request->input('P3_CONTACT_MAIL_ID'),
        ];

        if ($isStaff) {
            $data['internal_remarks'] = $request->input('P3_MAWAI_REMARKS');
            $data['time_taken'] = $request->input('P8_TIME_TAKEN');
            $data['closed_date'] = $request->input('P3_CLOSE_DT_TYPE');
            $data['assigned_to'] = $request->input('P8_ASSIGN_TO') ?: null;
            $data['changed_by'] = $request->input('P8_CHANGE_DONE_BY');
            $data['reason'] = $request->input('P8_REASON');
            $data['action_taken'] = $request->input('P8_ACTION');
        } else {
            $data['internal_remarks'] = null;
            $data['time_taken'] = null;
            $data['closed_date'] = null;
            $data['assigned_to'] = null;
            $data['changed_by'] = null;
            $data['reason'] = null;
            $data['action_taken'] = null;
            $data['status'] = 'PN';
        }

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    protected function buildUpdateComplaintPayload(
        Request $request,
        string $userCode,
        string $clientCode,
        ?string $newStatus,
    ): array {
        $updates = [
            'complaint_date' => $request->input('P3_COMPL_DT'),
            'client_code' => $clientCode,
            'module' => $request->input('P3_MODULE'),
            'complaint_type' => $request->input('P3_COMPL_TYPE'),
            'error_type' => $request->input('P3_ERROR_TYPE'),
            'problem_description' => $request->input('P3_PROBLEM_DESC'),
            'priority' => $request->input('P3_COMPL_LEVEL'),
            'status' => $newStatus,
            'contact_name' => $request->input('P3_USER_NAME'),
            'contact_email' => $request->input('P3_CONTACT_MAIL_ID'),
            'time_taken' => $request->input('P8_TIME_TAKEN') ?: null,
            'assigned_to' => $request->input('P8_ASSIGN_TO') ?: null,
            'changed_by' => $request->input('P8_CHANGE_DONE_BY') ?: null,
        ];

        if (UserRole::isAdmin($userCode) && in_array($newStatus, ['CM', 'CL'], true)) {
            $updates['closed_date'] = $request->input('P3_CLOSE_DT_TYPE') ?: now()->toDateString();
        } elseif (! in_array($newStatus, ['CM', 'CL'], true)) {
            $updates['internal_remarks'] = $request->input('P3_MAWAI_REMARKS');
            $updates['reason'] = $request->input('P8_REASON');
            $updates['action_taken'] = $request->input('P8_ACTION');
            $updates['closed_date'] = $request->input('P3_CLOSE_DT_TYPE') ?: null;
        }

        return $updates;
    }

    protected function storeMessage(
        string $complaintNumber,
        string $authorCode,
        ?string $authorName,
        string $authorRole,
        string $body,
        string $messageType = 'comment',
        bool $isInternal = false,
        ?int $rating = null,
    ): ComplaintMessage {
        return ComplaintMessage::create([
            'complaint_number' => $complaintNumber,
            'author_user_code' => $authorCode,
            'author_name' => $authorName,
            'author_role' => $authorRole,
            'body' => $body,
            'is_internal' => $isInternal,
            'message_type' => $messageType,
            'rating' => $rating,
            'created_at' => now(),
        ]);
    }

    protected function renderThreadMessages(array $messages): string
    {
        if ($messages === []) {
            return '';
        }

        return view('complaint._threadMessages', [
            'messages' => collect($messages),
        ])->render();
    }

    protected function threadState(Request $request, CustomerComplaint $complaint): array
    {
        return [
            'canReply' => ComplaintAccess::canPostMessage($request, $complaint),
            'canClose' => ComplaintAccess::canClose($request, $complaint),
            'isClosed' => ComplaintAccess::isClosed($complaint),
            'status' => $complaint->status,
            'rating' => $complaint->rating,
            'ratingHtml' => $complaint->rating
                ? view('complaint._threadRating', ['rating' => $complaint->rating])->render()
                : null,
        ];
    }

    protected function threadJsonResponse(
        Request $request,
        CustomerComplaint $complaint,
        string $message,
        array $createdMessages,
    ) {
        return response()->json([
            'type' => 1,
            'message' => $message,
            'html' => $this->renderThreadMessages($createdMessages),
            'state' => $this->threadState($request, $complaint),
        ]);
    }

    public function downloadComplaintFile(Request $request, $filename)
    {
        $filename = basename((string) $filename);
        $filePath = public_path('uploads/'.$filename);

        if (! file_exists($filePath)) {
            abort(404, 'File not found.');
        }

        $complaint = CustomerComplaint::query()
            ->where('attachment_name', 'like', '%/'.$filename)
            ->first();

        if ($complaint && ! ComplaintAccess::canView($request, $complaint)) {
            abort(403, 'You are not allowed to download this file.');
        }

        return response()->download($filePath);
    }
}
