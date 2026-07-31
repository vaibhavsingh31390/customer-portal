<?php

namespace App\Mail;

use App\Support\SqlHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ComplaintCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $complaintData;

    public function __construct($complaintData)
    {
        $this->complaintData = $complaintData;
    }

    public function build()
    {
        $code = $this->complaintData['complaint_number'];

        $moduleRow = SqlHelper::selectOne(
            'SELECT name FROM '.SqlHelper::table(SqlHelper::TABLE_SAP_MODULES)." WHERE department_module = 'TERMS' AND name = ?",
            [$this->complaintData['module']]
        );
        $module = $moduleRow->name ?? '-';

        $customerRow = SqlHelper::selectOne(
            'SELECT name FROM '.SqlHelper::table(SqlHelper::TABLE_CLIENTS)." WHERE erp_vertical = 'TERMS' AND client_code = ?",
            [$this->complaintData['client_code']]
        );
        $customer_name = $customerRow->name ?? '-';

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
        $priorities = [
            'C' => 'Critical',
            'L' => 'Low',
            'M' => 'Medium',
        ];

        $this->complaintData['customer_name'] = $customer_name;
        $this->complaintData['module_name'] = $module;
        $this->complaintData['complaint_type_name'] = $complaintTypes[$this->complaintData['complaint_type']] ?? null;
        $this->complaintData['error_type_name'] = $errorTypes[$this->complaintData['error_type']] ?? null;
        $this->complaintData['priority_name'] = $priorities[$this->complaintData['priority']] ?? null;

        $email = $this->subject("New Complaint Logged with Ref Id: $code")
            ->view('emails.complaint_created')
            ->with('complaintData', $this->complaintData);

        if (! empty($this->complaintData['attachment_name'])) {
            $email->attach(public_path($this->complaintData['attachment_name']));
        }

        return $email;
    }
}
