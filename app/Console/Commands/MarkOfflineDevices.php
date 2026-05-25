<?php

namespace App\Console\Commands;

use App\Services\DeviceService;
use Illuminate\Console\Command;

class MarkOfflineDevices extends Command
{
    protected $signature   = 'devices:mark-offline';
    protected $description = 'Mark devices offline if they have not sent a heartbeat recently';

    public function handle(DeviceService $service): void
    {
        $count = $service->markOfflineDevices();
        $this->info("Marked {$count} device(s) as offline.");
    }
}
