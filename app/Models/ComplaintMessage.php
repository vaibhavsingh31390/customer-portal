<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplaintMessage extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'complaint_number',
        'author_user_code',
        'author_name',
        'author_role',
        'body',
        'is_internal',
        'message_type',
        'rating',
        'attachment_name',
        'created_at',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(CustomerComplaint::class, 'complaint_number', 'complaint_number');
    }
}
