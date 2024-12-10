<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerComplaint extends Model
{
    // Define the table associated with the model
    protected $table = 'CUSTOMER_COMPLAINT';

    // Define the primary key (if it's not 'id')
    protected $primaryKey = 'COMPL_ID';

    // Specify that the primary key is not auto-incrementing
    public $incrementing = false;

    // Define the type of the primary key
    protected $keyType = 'string';

    // Disable timestamps if not using created_at and updated_at
    public $timestamps = false;

    // Define the fillable attributes
    protected $fillable = [
        'COMPLAINT_NO',
        'COMPL_DT',
        'CUST_CD',
        'MODULE',
        'COMPL_TYPE',
        'ERROR_TYPE',
        'PROBLEM_DESC',
        'COMPL_LEVEL',
        'TARGET_DT',
        'STATUS',
        'MAWAI_REMARKS',
        'REASON',
        'ACTION',
        'FILE_NAME',
        'CHANGE_TYPE',
        'UPDATE_STD',
        'CHANGE_DONE_BY',
        'CLOSE_DT',
        'USER_NAME',
        'CLEINT_VERIFY_BY',
        'CLIENT_VERIFY_DT',
        'CHANGE_VERIFY_BY',
        'TIME_TAKEN',
        'VER_SENT_DT',
        'ASSIGN_TO',
        'FILENAME',
        'RATE',
        'EST_HR',
        'CONTACT_MAIL_ID',
        'EMAIL_SEND_STATUS',
        'COMPL_ID'
    ];
}
