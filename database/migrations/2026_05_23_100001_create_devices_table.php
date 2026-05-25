<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('phone_number')->nullable();
            $table->string('device_uuid')->unique()->nullable();
            $table->string('device_token')->unique()->nullable();
            $table->string('pairing_code')->unique()->nullable();
            $table->string('server_url')->nullable();
            $table->enum('status', ['online', 'offline', 'disabled'])->default('offline');
            $table->boolean('gateway_enabled')->default(true);
            $table->unsignedTinyInteger('battery_level')->nullable();
            $table->unsignedTinyInteger('signal_strength')->nullable();
            $table->string('sim_operator')->nullable();
            $table->string('android_version')->nullable();
            $table->string('app_version')->nullable();
            $table->timestamp('last_heartbeat_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->unsignedInteger('heartbeat_interval_seconds')->default(30);
            $table->unsignedInteger('pull_interval_seconds')->default(5);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
