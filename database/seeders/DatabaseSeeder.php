<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Device;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedRolesAndPermissions();
        $this->seedAdminUser();
        $this->seedSettings();
        $this->seedDemoClient();
    }

    private function seedRolesAndPermissions(): void
    {
        $permissions = [
            'view dashboard',
            'manage clients', 'view clients', 'create clients', 'edit clients', 'delete clients',
            'manage devices', 'view devices', 'create devices', 'edit devices', 'delete devices',
            'manage api_keys', 'view api_keys', 'create api_keys', 'edit api_keys', 'delete api_keys',
            'manage sms', 'view sms', 'send sms', 'cancel sms',
            'manage commands', 'view commands', 'send commands',
            'view logs', 'manage logs',
            'manage settings',
            'manage blocked_numbers',
            'manage users',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $operator = Role::firstOrCreate(['name' => 'operator', 'guard_name' => 'web']);

        $admin->syncPermissions(Permission::all());
        $operator->syncPermissions([
            'view dashboard', 'view clients', 'view devices', 'view api_keys',
            'view sms', 'send sms', 'view logs', 'view commands', 'send commands',
        ]);
    }

    private function seedAdminUser(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@sendbridge.local'],
            [
                'name' => 'Admin',
                'password' => Hash::make('Admin@2024!'),
                'email_verified_at' => now(),
            ]
        );
        $user->assignRole('super-admin');
    }

    private function seedSettings(): void
    {
        $defaults = [
            ['key' => 'default_heartbeat_interval_seconds', 'value' => '30', 'type' => 'integer', 'description' => 'Default heartbeat interval in seconds'],
            ['key' => 'default_pull_interval_seconds', 'value' => '5', 'type' => 'integer', 'description' => 'Default SMS pull interval in seconds'],
            ['key' => 'offline_timeout_minutes', 'value' => '2', 'type' => 'integer', 'description' => 'Minutes without heartbeat before marking offline'],
            ['key' => 'max_sms_per_minute', 'value' => '10', 'type' => 'integer', 'description' => 'Max SMS per minute per device'],
            ['key' => 'max_sms_per_day', 'value' => '500', 'type' => 'integer', 'description' => 'Default max SMS per day'],
            ['key' => 'max_attempts', 'value' => '3', 'type' => 'integer', 'description' => 'Max retry attempts for failed SMS'],
            ['key' => 'allow_remote_disable', 'value' => 'true', 'type' => 'boolean', 'description' => 'Allow remote gateway disable'],
            ['key' => 'allow_remote_server_change', 'value' => 'true', 'type' => 'boolean', 'description' => 'Allow remote server URL change'],
            ['key' => 'default_country_code', 'value' => '+1', 'type' => 'string', 'description' => 'Default country code'],
            ['key' => 'admin_notification_email', 'value' => 'admin@sendbridge.local', 'type' => 'string', 'description' => 'Admin notification email'],
        ];

        foreach ($defaults as $setting) {
            Setting::firstOrCreate(['key' => $setting['key']], $setting);
        }
    }

    private function seedDemoClient(): void
    {
        $client = Client::firstOrCreate(
            ['email' => 'demo@sendbridge.local'],
            [
                'name' => 'Demo Client',
                'company_name' => 'Demo Corp',
                'phone' => '+18005551234',
                'status' => 'active',
                'daily_sms_limit' => 500,
                'monthly_sms_limit' => 5000,
            ]
        );

        Device::firstOrCreate(
            ['device_uuid' => 'demo-device-001'],
            [
                'client_id' => $client->id,
                'name' => 'Demo Device',
                'phone_number' => '+18005550001',
                'pairing_code' => 'DEMO1234',
                'status' => 'offline',
                'gateway_enabled' => true,
            ]
        );
    }
}
