<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blocked_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->string('phone_number');
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->unique(['client_id', 'phone_number']);
            $table->index('phone_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blocked_numbers');
    }
};
