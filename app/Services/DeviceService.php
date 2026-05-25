<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Device;
use App\Models\DeviceHeartbeat;
use App\Models\SmsLog;
use App\Models\Setting;
use Illuminate\Support\Str;

class DeviceService
{
    public function registerDevice(array $data, string $pairingCode): array
    {
        // Check if using auto-registration (device UUID as pairing code for new devices)
        $device = Device::where('pairing_code', $pairingCode)->first();

        if (!$device) {
            // Auto-create device if pairing code starts with AUTO- or device_uuid provided
            if (str_starts_with($pairingCode, 'AUTO-') || !empty($data['device_uuid'])) {
                return $this->autoCreateAndRegisterDevice($data, $pairingCode);
            }
            return ['success' => false, 'error' => 'Invalid pairing code'];
        }

        if ($device->device_token) {
            return ['success' => false, 'error' => 'Device already registered. Use regenerate token.'];
        }

        return $this->activateDevice($device, $data);
    }

    private function autoCreateAndRegisterDevice(array $data, string $pairingCode): array
    {
        // Find or create a default client for auto-registered devices
        $client = Client::firstOrCreate(
            ['email' => 'auto@devices.sendbridge.local'],
            [
                'name' => 'Auto-Registered Devices',
                'company_name' => 'Auto Devices',
                'status' => 'active',
                'daily_sms_limit' => 1000,
                'monthly_sms_limit' => 10000,
            ]
        );

        $token = Device::generateToken();
        $newPairingCode = Device::generatePairingCode();

        $device = Device::create([
            'client_id'                  => $client->id,
            'name'                       => $data['name'] ?? 'Device ' . substr($data['device_uuid'] ?? $newPairingCode, 0, 8),
            'phone_number'               => $data['phone_number'] ?? null,
            'device_uuid'                => $data['device_uuid'] ?? Str::uuid(),
            'device_token'               => $token,
            'pairing_code'               => $newPairingCode,
            'server_url'                 => config('app.url'),
            'status'                     => 'online',
            'gateway_enabled'            => true,
            'android_version'            => $data['android_version'] ?? null,
            'app_version'                => $data['app_version'] ?? null,
            'heartbeat_interval_seconds' => 60,
            'pull_interval_seconds'      => 5,
            'last_seen_at'               => now(),
            'last_heartbeat_at'          => now(),
        ]);

        SmsLog::create([
            'client_id' => $device->client_id,
            'device_id' => $device->id,
            'type'      => 'device_registered',
            'level'     => 'info',
            'message'   => "Device {$device->name} auto-registered successfully",
        ]);

        return [
            'success'      => true,
            'device_token' => $token,
            'device_id'    => $device->id,
            'pairing_code' => $newPairingCode,
            'config'       => $this->getDeviceConfig($device),
            'auto_created' => true,
        ];
    }

    private function activateDevice(Device $device, array $data): array
    {
        $token = Device::generateToken();

        $device->update([
            'device_uuid'    => $data['device_uuid'] ?? Str::uuid(),
            'device_token'   => $token,
            'name'           => $data['name'] ?? $device->name,
            'phone_number'   => $data['phone_number'] ?? $device->phone_number,
            'android_version' => $data['android_version'] ?? null,
            'app_version'    => $data['app_version'] ?? null,
            'status'         => 'online',
            'last_seen_at'   => now(),
        ]);

        SmsLog::create([
            'client_id' => $device->client_id,
            'device_id' => $device->id,
            'type'      => 'device_registered',
            'level'     => 'info',
            'message'   => "Device {$device->name} registered successfully",
        ]);

        return [
            'success'      => true,
            'device_token' => $token,
            'device_id'    => $device->id,
            'pairing_code' => $device->pairing_code,
            'config'       => $this->getDeviceConfig($device),
        ];
    }

    public function processHeartbeat(Device $device, array $data): array
    {
        $heartbeat = DeviceHeartbeat::create([
            'device_id'       => $device->id,
            'battery_level'   => $data['battery_level'] ?? null,
            'signal_strength' => $data['signal_strength'] ?? null,
            'sim_operator'    => $data['sim_operator'] ?? null,
            'gateway_enabled' => $data['gateway_enabled'] ?? true,
            'app_version'     => $data['app_version'] ?? null,
            'android_version' => $data['android_version'] ?? null,
            'ip_address'      => request()->ip(),
        ]);

        $device->update([
            'status'              => 'online',
            'last_heartbeat_at'   => now(),
            'last_seen_at'        => now(),
            'battery_level'       => $data['battery_level'] ?? $device->battery_level,
            'signal_strength'     => $data['signal_strength'] ?? $device->signal_strength,
            'sim_operator'        => $data['sim_operator'] ?? $device->sim_operator,
            'app_version'         => $data['app_version'] ?? $device->app_version,
            'android_version'     => $data['android_version'] ?? $device->android_version,
        ]);

        return ['success' => true, 'config' => $this->getDeviceConfig($device)];
    }

    public function getDeviceConfig(Device $device): array
    {
        return [
            'heartbeat_interval_seconds' => $device->heartbeat_interval_seconds,
            'pull_interval_seconds'      => $device->pull_interval_seconds,
            'gateway_enabled'            => $device->gateway_enabled,
            'max_sms_per_minute'         => (int) Setting::get('max_sms_per_minute', 10),
            'max_attempts'               => (int) Setting::get('max_attempts', 3),
        ];
    }

    public function markOfflineDevices(): int
    {
        $timeoutMinutes = (int) Setting::get('offline_timeout_minutes', 2);

        $count = Device::where('status', 'online')
            ->where('last_heartbeat_at', '<', now()->subMinutes($timeoutMinutes))
            ->update(['status' => 'offline']);

        return $count;
    }
}
