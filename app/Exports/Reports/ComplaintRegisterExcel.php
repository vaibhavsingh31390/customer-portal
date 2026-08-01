<?php

namespace App\Exports\Reports;

use App\Models\CustomerComplaint;
use App\Support\SqlHelper;
use App\Support\UserRole;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ComplaintRegisterExcel implements FromCollection, WithHeadings, WithCustomStartCell
{
    public function collection()
    {
        $user = request()->session()->get('user');
        $userCode = $user->user_code ?? '';
        $clientCode = request()->client_cd;

        if (UserRole::isClient($userCode)) {
            $query = CustomerComplaint::where('client_code', $userCode);
        } elseif ($clientCode != '') {
            $query = CustomerComplaint::where('client_code', $clientCode);
        } else {
            $query = CustomerComplaint::query();
        }
        if (request()->date_from != '' && request()->date_to != '') {
            $query->whereBetween('complaint_date', [request()->date_from, request()->date_to]);
        }

        $query = \App\Support\ComplaintStatus::applyFilter($query, request()->input('status_cd'));
        $data = $query->orderBy('complaint_date', 'DESC')->get();
        $datas = [];

        $complaintTypes = [
            'DB_Object' => 'DB Object',
            'Form' => 'Form',
            'Graph' => 'Graph',
            'Others' => 'Others',
            'Report' => 'Report',
            'Tables' => 'Tables',
            'Views' => 'Views',
        ];
        $errorTypes = [
            'DP' => 'Database Problem',
            'NR' => 'New Requirement',
            'OT' => 'Others',
            'SP' => 'Software Problem',
            'ST' => 'Support',
            'UP' => 'User Problem',
        ];
        $statuses = [
            'CL' => 'Cancel',
            'CM' => 'Complete',
            'HL' => 'Hold',
            'PN' => 'Pending',
            'SV' => 'Sent For Customer Verification',
        ];

        foreach ($data as $dt) {
            $x = new \stdClass();
            $moduleRow = SqlHelper::selectOne(
                'SELECT name FROM '.SqlHelper::table(SqlHelper::TABLE_SAP_MODULES)." WHERE department_module = 'TERMS' AND name = ?",
                [$dt['module']],
            );
            $module = $moduleRow->name ?? '-';

            $clientCode = $dt['client_code'];
            $customerRow = SqlHelper::selectOne(
                'SELECT name FROM '.SqlHelper::table(SqlHelper::TABLE_CLIENTS)." WHERE erp_vertical = 'TERMS' AND client_code = ? ORDER BY 1",
                [$clientCode]
            );
            $x->complaint_number = $dt['complaint_number'];
            $x->client_name = $customerRow->name ?? '-';
            $x->status = $statuses[$dt['status']];
            $x->complaint_date = ! empty($dt['complaint_date']) ? date('d-m-Y', strtotime($dt['complaint_date'])) : '-';
            $x->module = $module;
            $x->complaint_type = $complaintTypes[$dt['complaint_type']];
            $x->error_type = $errorTypes[$dt['error_type']];
            $x->problem_description = $dt['problem_description'];
            $x->reason = $dt['reason'];
            $x->action_taken = $dt['action_taken'];
            $x->closed_date = ! empty($dt['closed_date']) ? date('d-m-Y', strtotime($dt['closed_date'])) : '-';
            $x->contact_name = $dt['contact_name'];
            $datas[] = $x;
        }

        return Collection::make($datas);
    }

    public function headings(): array
    {
        return [
            ['Customer', request()->client_cd],
            ['From', request()->date_from],
            ['To', request()->date_to],
            ['Status', request()->status_cd ? (\App\Support\ComplaintStatus::filterOptions()[request()->status_cd] ?? request()->status_cd) : 'All'],
            [''],
            [
                'Complaint No', 'Complaint Date', 'Client Name', 'Status', 'Module', 'Complaint Type', 'Error Type',
                'Problem Description', 'Reason', 'Action Taken', 'Closed Date', 'Contact Name',
            ],
        ];
    }

    public function startCell(): string
    {
        return 'A2';
    }
}
