<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SmsMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id', 'device_id', 'api_key_id', 'to_number', 'from_device_number',
        'message_body', 'status', 'priority', 'attempts', 'max_attempts',
        'error_message', 'provider_response', 'reserved_at', 'sent_at',
        'failed_at', 'cancelled_at',
    ];

    protected $casts = [
        'client_id' => 'integer',
        'device_id' => 'integer',
        'api_key_id' => 'integer',
        'priority' => 'integer',
        'attempts' => 'integer',
        'max_attempts' => 'integer',
        'provider_response' => 'array',
        'reserved_at' => 'datetime',
        'sent_at' => 'datetime',
        'failed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function apiKey(): BelongsTo
    {
        return $this->belongsTo(ApiKey::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(SmsLog::class);
    }

    public function canRetry(): bool
    {
        return $this->attempts < $this->max_attempts;
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }
}
