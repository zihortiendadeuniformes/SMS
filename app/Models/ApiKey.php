<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id', 'name', 'api_key', 'api_secret', 'status',
        'allowed_ips', 'daily_limit', 'monthly_limit',
        'used_today', 'used_month', 'last_used_at',
    ];

    protected $hidden = ['api_key', 'api_secret'];

    protected $casts = [
        'daily_limit' => 'integer',
        'monthly_limit' => 'integer',
        'used_today' => 'integer',
        'used_month' => 'integer',
        'last_used_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function smsMessages(): HasMany
    {
        return $this->hasMany(SmsMessage::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function hasReachedDailyLimit(): bool
    {
        return $this->used_today >= $this->daily_limit;
    }

    public function hasReachedMonthlyLimit(): bool
    {
        return $this->used_month >= $this->monthly_limit;
    }

    public function isAllowedFromIp(string $ip): bool
    {
        if (empty($this->allowed_ips)) {
            return true;
        }
        $allowed = array_map('trim', explode(',', $this->allowed_ips));
        return in_array($ip, $allowed);
    }

    public static function generateKey(): string
    {
        return 'sk_' . Str::random(40);
    }

    public static function generateSecret(): string
    {
        return Str::random(64);
    }
}
