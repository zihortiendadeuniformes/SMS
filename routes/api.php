<?php

use App\Http\Controllers\Api\Device\CommandsController;
use App\Http\Controllers\Api\Device\ConfigController;
use App\Http\Controllers\Api\Device\HeartbeatController;
use App\Http\Controllers\Api\Device\LogsController;
use App\Http\Controllers\Api\Device\MessagesController;
use App\Http\Controllers\Api\Device\RegisterController;
use App\Http\Controllers\Api\V1\BalanceController;
use App\Http\Controllers\Api\V1\DevicesController;
use App\Http\Controllers\Api\V1\SmsController;
use Illuminate\Support\Facades\Route;

// ── Device API (Android) ─────────────────────────────────────────────────────
Route::prefix('device')->name('api.device.')->group(function () {

    Route::post('register', RegisterController::class)->name('register');

    Route::middleware('auth.device')->group(function () {
        Route::post('heartbeat', HeartbeatController::class)->name('heartbeat');
        Route::get('config', ConfigController::class)->name('config');

        Route::prefix('messages')->name('messages.')->group(function () {
            Route::get('pending', [MessagesController::class, 'pending'])->name('pending');
            Route::post('{smsMessage}/reserve', [MessagesController::class, 'reserve'])->name('reserve');
            Route::post('{smsMessage}/sent',    [MessagesController::class, 'markSent'])->name('sent');
            Route::post('{smsMessage}/failed',  [MessagesController::class, 'markFailed'])->name('failed');
        });

        Route::prefix('commands')->name('commands.')->group(function () {
            Route::get('/',              [CommandsController::class, 'index'])->name('index');
            Route::post('{command}/ack',    [CommandsController::class, 'ack'])->name('ack');
            Route::post('{command}/result', [CommandsController::class, 'result'])->name('result');
        });

        Route::post('logs', LogsController::class)->name('logs');
    });
});

// ── Public Client API (v1) ───────────────────────────────────────────────────
Route::prefix('v1')->name('api.v1.')->middleware(['auth.apikey', 'throttle:60,1'])->group(function () {
    Route::post('sms/send',       [SmsController::class, 'send'])->name('sms.send');
    Route::get('sms/{smsMessage}', [SmsController::class, 'show'])->name('sms.show');
    Route::get('sms',              [SmsController::class, 'index'])->name('sms.index');
    Route::get('devices',          [DevicesController::class, 'index'])->name('devices');
    Route::get('balance',          BalanceController::class)->name('balance');
});
