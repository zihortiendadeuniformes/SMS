<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceHeartbeat extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'device_id', 'battery_level', 'signal_strength', 'sim_operator',
        'gateway_enabled', 'app_version', 'android_version', 'ip_address', 'created_at',
    ];

    protected $casts = [
        'battery_level' => 'integer',
        'signal_strength' => 'integer',
        'gateway_enabled' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
