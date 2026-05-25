<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id', 'name', 'phone_number', 'device_uuid', 'device_token',
        'pairing_code', 'server_url', 'status', 'gateway_enabled',
        'battery_level', 'signal_strength', 'sim_operator', 'android_version',
        'app_version', 'last_heartbeat_at', 'last_seen_at',
        'heartbeat_interval_seconds', 'pull_interval_seconds',
    ];

    protected $casts = [
        'gateway_enabled' => 'boolean',
        'battery_level' => 'integer',
        'signal_strength' => 'integer',
        'last_heartbeat_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'heartbeat_interval_seconds' => 'integer',
        'pull_interval_seconds' => 'integer',
    ];

    protected $hidden = ['device_token'];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function smsMessages(): HasMany
    {
        return $this->hasMany(SmsMessage::class);
    }

    public function heartbeats(): HasMany
    {
        return $this->hasMany(DeviceHeartbeat::class);
    }

    public function commands(): HasMany
    {
        return $this->hasMany(DeviceCommand::class);
    }

    public function smsLogs(): HasMany
    {
        return $this->hasMany(SmsLog::class);
    }

    public function isOnline(): bool
    {
        return $this->status === 'online';
    }

    public function isDisabled(): bool
    {
        return $this->status === 'disabled';
    }

    public function isGatewayActive(): bool
    {
        return $this->gateway_enabled && !$this->isDisabled();
    }

    public static function generatePairingCode(): string
    {
        return strtoupper(Str::random(8));
    }

    public static function generateToken(): string
    {
        return Str::random(64);
    }
}
