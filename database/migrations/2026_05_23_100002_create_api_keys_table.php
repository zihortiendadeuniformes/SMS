<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('api_key')->unique();
            $table->string('api_secret')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('allowed_ips')->nullable();
            $table->unsignedInteger('daily_limit')->default(1000);
            $table->unsignedInteger('monthly_limit')->default(10000);
            $table->unsignedInteger('used_today')->default(0);
            $table->unsignedInteger('used_month')->default(0);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_keys');
    }
};
