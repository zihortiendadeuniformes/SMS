<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'company_name', 'email', 'phone', 'status',
        'daily_sms_limit', 'monthly_sms_limit', 'used_sms_today',
        'used_sms_month', 'notes',
    ];

    protected $casts = [
        'daily_sms_limit' => 'integer',
        'monthly_sms_limit' => 'integer',
        'used_sms_today' => 'integer',
        'used_sms_month' => 'integer',
    ];

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function apiKeys(): HasMany
    {
        return $this->hasMany(ApiKey::class);
    }

    public function smsMessages(): HasMany
    {
        return $this->hasMany(SmsMessage::class);
    }

    public function smsLogs(): HasMany
    {
        return $this->hasMany(SmsLog::class);
    }

    public function blockedNumbers(): HasMany
    {
        return $this->hasMany(BlockedNumber::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function hasReachedDailyLimit(): bool
    {
        return $this->used_sms_today >= $this->daily_sms_limit;
    }

    public function hasReachedMonthlyLimit(): bool
    {
        return $this->used_sms_month >= $this->monthly_sms_limit;
    }
}
