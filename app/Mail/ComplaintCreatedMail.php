<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ComplaintCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $complaintData;

    /**
     * Create a new message instance.
     *
     * @param array $complaintData
     * @return void
     */
    public function __construct($complaintData)
    {
        $this->complaintData = $complaintData;
    }

    public function build()
    {
        $code = $this->complaintData['COMPLAINT_NO'];

        // Query for MODULE_NAME
        $moduleResult = DB::select("SELECT MODULE_TEXT FROM SAP_MODULE_DTL WHERE DEPT_MODULE='TERMS' AND MODULE_TEXT = ?", [$this->complaintData['MODULE']]);
        $module = !empty($moduleResult) ? $moduleResult[0]->module_text : '-';

        // Query for CUSTOMER_NAME
        $customerResult = DB::select("SELECT CLIENT_NAME FROM CELINT_MASTER WHERE ERP_VERT='TERMS' AND CLIENT_CD = ?", [$this->complaintData['CUST_CD']]);
        $customer_name = !empty($customerResult) ? $customerResult[0]->client_name : '-';

        // Define the lookup arrays
        $COMPL_TYPE = [
            'DB_Object' => 'DB Object',
            'Form' => 'Form',
            'Graph' => 'Graph',
            'Others' => 'Others',
            'Report' => 'Report',
            'Tables' => 'Tables',
            'Views' => 'Views'
        ];
        $ERROR_TYPE = [
            'DP' => 'Database Problem',
            'NR' => 'New Requirement',
            'OT' => 'Others',
            'SP' => 'Software Problem',
            'ST' => 'Support',
            'UP' => 'User Problem'
        ];
        $COMPL_LEVEL = [
            'C' => 'Critical',
            'L' => 'Low',
            'M' => 'Medium'
        ];

        // Assign the filtered values
        $this->complaintData['CUSTOMER_NAME'] = $customer_name;
        $this->complaintData['MODULE_NAME'] = $module;
        $this->complaintData['COMPL_TYPE_NAME'] = $COMPL_TYPE[$this->complaintData['COMPL_TYPE']] ?? null;
        $this->complaintData['ERROR_TYPE_NAME'] = $ERROR_TYPE[$this->complaintData['ERROR_TYPE']] ?? null;
        $this->complaintData['COMPL_LEVEL_NAME'] = $COMPL_LEVEL[$this->complaintData['COMPL_LEVEL']] ?? null;

        // dd($this->complaintData);
        // Build the email
        $email = $this->subject("New Complaint Logged with Ref Id: $code")
            ->view('emails.complaint_created')
            ->with('complaintData', $this->complaintData);

        if (!empty($this->complaintData['FILENAME'])) {
            $email->attach(public_path($this->complaintData['FILENAME']));
        }

        return $email;
    }
}
