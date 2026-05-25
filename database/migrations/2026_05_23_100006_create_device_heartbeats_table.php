<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_heartbeats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('battery_level')->nullable();
            $table->unsignedTinyInteger('signal_strength')->nullable();
            $table->string('sim_operator')->nullable();
            $table->boolean('gateway_enabled')->default(true);
            $table->string('app_version')->nullable();
            $table->string('android_version')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['device_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_heartbeats');
    }
};
