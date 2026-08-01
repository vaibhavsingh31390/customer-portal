<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerComplaint extends Model
{
    protected $table = 'customer_complaints';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'complaint_number',
        'complaint_date',
        'client_code',
        'module',
        'complaint_type',
        'error_type',
        'problem_description',
        'priority',
        'target_date',
        'status',
        'internal_remarks',
        'reason',
        'action_taken',
        'file_name',
        'change_type',
        'update_standard',
        'changed_by',
        'closed_date',
        'contact_name',
        'verified_by',
        'verified_at',
        'change_verified_by',
        'time_taken',
        'verification_sent_at',
        'assigned_to',
        'attachment_name',
        'rating',
        'estimated_hours',
        'contact_email',
        'email_send_status',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(ComplaintMessage::class, 'complaint_number', 'complaint_number')
            ->orderBy('created_at');
    }
}
