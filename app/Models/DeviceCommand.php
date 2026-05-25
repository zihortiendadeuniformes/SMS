<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceCommand extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'device_id', 'command', 'payload', 'status', 'response',
        'error_message', 'created_at', 'received_at', 'executed_at', 'failed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'response' => 'array',
        'created_at' => 'datetime',
        'received_at' => 'datetime',
        'executed_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public const AVAILABLE_COMMANDS = [
        'enable_gateway',
        'disable_gateway',
        'update_config',
        'sync_now',
        'clear_local_queue',
        'restart_service',
        'update_server_url',
        'refresh_permissions',
        'ping',
        'logout_device',
    ];
}
