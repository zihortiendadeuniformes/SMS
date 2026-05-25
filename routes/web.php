<?php

use App\Http\Controllers\Admin\ApiKeyController;
use App\Http\Controllers\Admin\BlockedNumberController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeviceController;
use App\Http\Controllers\Admin\IntegrationController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SmsController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('admin.dashboard'));

// Fix orphaned pending messages - reassign to matching device client
Route::get('/fix-messages/{token}', function (string $token) {
    if ($token !== 'SB-SETUP-2026-XK9') abort(403);
    try {
        $device   = \App\Models\Device::first();
        $messages = \App\Models\SmsMessage::where('status', 'pending')->get();
        $fixed = 0;
        foreach ($messages as $msg) {
            if ($msg->client_id !== $device->client_id) {
                $msg->update(['client_id' => $device->client_id]);
                $fixed++;
            }
        }
        return response()->json([
            'device_client_id' => $device->client_id,
            'messages_fixed'   => $fixed,
            'messages'         => $messages->map(fn($m) => ['id'=>$m->id,'client_id'=>$m->client_id,'status'=>$m->status]),
        ]);
    } catch (\Throwable $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// Temporary setup route - DELETE after first use
Route::get('/setup-init/{token}', function (string $token) {
    if ($token !== 'SB-SETUP-2026-XK9') {
        abort(403);
    }
    try {
        $user = \App\Models\User::updateOrCreate(
            ['email' => 'carlosjuanhtc@gmail.com'],
            [
                'name'              => 'Carlos',
                'password'          => \Illuminate\Support\Facades\Hash::make('htc1234567'),
                'email_verified_at' => now(),
            ]
        );
        if (!$user->hasRole('super-admin')) {
            $user->assignRole('super-admin');
        }
        return response()->json(['ok' => true, 'user' => $user->email, 'role' => 'super-admin']);
    } catch (\Throwable $e) {
        return response()->json(['ok' => false, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()], 500);
    }
});

// ── Auth ─────────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('login',  [\App\Http\Controllers\Auth\LoginController::class, 'showForm'])->name('login');
    Route::post('login', [\App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login.post');
});
Route::post('logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])
    ->middleware('auth')->name('logout');

// ── Admin panel ───────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {

    Route::get('dashboard', DashboardController::class)->name('dashboard');

    // Clients
    Route::resource('clients', ClientController::class);
    Route::post('clients/{client}/reset-daily', [ClientController::class, 'resetDailyUsage'])->name('clients.reset-daily');

    // Devices
    Route::resource('devices', DeviceController::class);
    Route::post('devices/{device}/toggle-gateway',  [DeviceController::class, 'toggleGateway'])->name('devices.toggle-gateway');
    Route::post('devices/{device}/toggle-status',   [DeviceController::class, 'toggleStatus'])->name('devices.toggle-status');
    Route::post('devices/{device}/regenerate-token', [DeviceController::class, 'regenerateToken'])->name('devices.regenerate-token');
    Route::post('devices/{device}/regenerate-pairing', [DeviceController::class, 'regeneratePairingCode'])->name('devices.regenerate-pairing');
    Route::post('devices/{device}/send-command',    [DeviceController::class, 'sendCommand'])->name('devices.send-command');

    // API Keys  (resource name uses underscore to match views)
    Route::resource('api-keys', ApiKeyController::class)->names([
        'index'   => 'api_keys.index',
        'create'  => 'api_keys.create',
        'store'   => 'api_keys.store',
        'show'    => 'api_keys.show',
        'edit'    => 'api_keys.edit',
        'update'  => 'api_keys.update',
        'destroy' => 'api_keys.destroy',
    ]);
    Route::post('api-keys/{apiKey}/regenerate', [ApiKeyController::class, 'regenerate'])->name('api_keys.regenerate');

    // SMS
    Route::get('sms',          [SmsController::class, 'index'])->name('sms.index');
    Route::get('sms/compose',  [SmsController::class, 'compose'])->name('sms.compose');
    Route::post('sms/send',    [SmsController::class, 'send'])->name('sms.send');
    Route::get('sms/{smsMessage}', [SmsController::class, 'show'])->name('sms.show');
    Route::post('sms/{smsMessage}/cancel', [SmsController::class, 'cancel'])->name('sms.cancel');
    Route::post('sms/{smsMessage}/retry',  [SmsController::class, 'retry'])->name('sms.retry');

    // Logs
    Route::get('logs', [LogController::class, 'index'])->name('logs.index');

    // Settings
    Route::get('settings',    [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings',   [SettingController::class, 'update'])->name('settings.update');

    // Blocked numbers
    Route::get('blocked-numbers',              [BlockedNumberController::class, 'index'])->name('blocked_numbers.index');
    Route::post('blocked-numbers',             [BlockedNumberController::class, 'store'])->name('blocked_numbers.store');
    Route::delete('blocked-numbers/{blockedNumber}', [BlockedNumberController::class, 'destroy'])->name('blocked_numbers.destroy');

    // Integrations
    Route::get('integrations', [IntegrationController::class, 'index'])->name('integrations.index');
    Route::post('integrations/webhook/test', [IntegrationController::class, 'testWebhook'])->name('integrations.webhook.test');

    // Users
    Route::resource('users', UserController::class)->except('show');
});
