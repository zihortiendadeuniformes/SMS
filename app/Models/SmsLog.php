<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'client_id', 'device_id', 'sms_message_id', 'type', 'level',
        'message', 'context', 'ip_address', 'user_agent', 'created_at',
    ];

    protected $casts = [
        'context' => 'array',
        'created_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function smsMessage(): BelongsTo
    {
        return $this->belongsTo(SmsMessage::class);
    }
}
